<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:13 $
    File Versie         : $Revision: 1.2 $

    $Log: AIRS_rekeningAfvinkHelper.php,v $
    Revision 1.2  2018/08/18 12:40:13  rvv
    php 5.6 & consolidatie

    Revision 1.1  2016/12/02 14:02:24  cvs
    call 5086



*/


class AIRS_rekeningAfvinkHelper
{

  var $VB;
  var $user;
  var $gbArray;
  var $gbArrayRev;
  var $currentMatchCode;
  function AIRS_rekeningAfvinkHelper($vb)
  {
    global $_SESSION;

    $this->VB = $vb;
    $this->user = $_SESSION["USR"];
    $db = new DB();
    $query = "SELECT rekeningnummer, grootboekrekening FROM grootboeknummers  ORDER BY grootboekrekening";
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $gb = strtoupper($rec["grootboekrekening"]);
      $rn = strtoupper($rec["rekeningnummer"]);
      $this->gbArray[$gb] = $rn;
      $this->gbArrayRev[$rn] = $gb;
    }

  }

  function genereerAfvinkRecords()
  {
    $db = new DB();
    $tel = 0;
    $query = "
    SELECT
      Portefeuilles.Vermogensbeheerder,
      Rekeningmutaties.*
    FROM
      Rekeningmutaties
    INNER JOIN Rekeningen ON 
      Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningmutaties.Boekdatum > '2016-10-01'
    INNER JOIN Portefeuilles ON 
      Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND 
      Portefeuilles.interndepot = 1 AND 
      Portefeuilles.Vermogensbeheerder = '" . $this->VB . "'
    LEFT OUTER JOIN 
      RekeningmutatiesAfvink ON 
      RekeningmutatiesAfvink.rekmut_id = Rekeningmutaties.id
    WHERE
      Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND
      RekeningmutatiesAfvink.id IS NULL
      ";

    $db->executeQuery($query);
    $db1 = new DB();
    $batch = "B".$this->matchcode();
    while ($rec = $db->nextRecord())
    {
      $tel++;
      if ( array_key_exists( strtoupper($rec["Grootboekrekening"]), $this->gbArray) )
      {
        $gb = $this->gbArray[$rec["Grootboekrekening"]];
      }
      else
      {
        $gb = "";
      }
      $query = "
      INSERT INTO RekeningmutatiesAfvink SET 
        add_date        = NOW(),
        change_date     = NOW(),
        add_user        = '".$this->user."',
        change_user     = '".$this->user."',
        volgnr          = 0,
        status          = 0,
        rekmut_id       = ".$rec["id"].",
        batchCode       = '$batch',
        boekdatumOrg    = '".$rec["Boekdatum"]."', 
        grootboekOrg    = '".$rec["Grootboekrekening"]."',
        bedragOrg       = '".$rec["Bedrag"]."',
        bedrag          = '".$rec["Bedrag"]."',
        grootboek       = '$gb',
        rekening        = '".$rec["Rekening"]."', 
        omschrijving    = '".$rec["Omschrijving"]."'
      ";
      $db1->executeQuery($query);
    }
    return $tel;

  }

  function fondsMatch($data)
  {
    $db = new DB();
    $match = $this->matchcode();


    $rawItems = explode(";", $data["items"]);
    $items = array();
///debug($rawItems);
    foreach ($rawItems as $i)
    {
      if (trim($i) <> "")   { $items[] = trim($i); }
    }

    foreach ($items as $i)
    {
      if ($data["itemId"] == $i)
      {
        $extra =  ", `bedrag`      = ".$data["rekmutBedrag"].", omschrijving = CONCAT('*',omschrijving) ";
      }
      else
      {
        $extra = "";
      }

      $query = "
       UPDATE 
         `RekeningmutatiesAfvink`
       SET
          `change_date` = NOW()
        , `change_user` = '".$this->user."'
        , `status`      = 3
        , `matchCode`   = '".$match."'
        $extra
      WHERE
         `id`    = ".$i."
      ";

      $db->executeQuery($query);
    }



    if ($data["b1"] <> 0 )
    {
      $query =  " 
      INSERT INTO 
        `RekeningmutatiesAfvink` 
      SET
        add_date        = NOW(),
        change_date     = NOW(),
        add_user        = '".$this->user."',
        change_user     = '".$this->user."',
        volgnr          = 1,
        status          = 3,
        rekmut_id       = '".$data["rekmutId"]."',
        matchCode       = '$match',
        bedrag          = '".$data["b1"]."',
        grootboek       = '".$data["gb1"]."',
        omschrijving    = ' extra boeking 1 (".$data["rekmutId"].")'
      ";
      $db->executeQuery($query);
    }
    if ($data["b2"] <> 0 )
    {
      $query =  "
      INSERT INTO 
        `RekeningmutatiesAfvink` 
      SET 
        add_date        = NOW(),
        change_date     = NOW(),
        add_user        = '".$this->user."',
        change_user     = '".$this->user."',
        volgnr          = 2,
        status          = 3,
        rekmut_id       = '".$data["rekmutId"]."',
        matchCode       = '$match',
        bedrag          = '".$data["b2"]."',
        grootboek       = '".$data["gb2"]."',
        omschrijving    = ' extra boeking 2 (".$data["rekmutId"].")'
      ";
      $db->executeQuery($query);
    }


  }
  function match($id)
  {
    $db = new DB();
//    $rm = $afh->getRekeningMutatie($id);
    $query = "
       UPDATE 
         `RekeningmutatiesAfvink`
       SET
          `change_date` = NOW()
        , `change_user` = '".$this->user."'
        , `status`      = 1
        , `matchCode`   = '".$this->currentMatchCode."'
      WHERE
         `id`   = $id
      ";

    return $db->executeQuery($query);


  }

  function updateGoedkeur($i)
  {
    global $USR, $rm;
    $db = new DB();
    $query = "
      UPDATE 
        `RekeningmutatiesAfvink` 
      SET
          `change_date` = NOW()
        , `change_user` = '".$this->user."'
        , `status` = 1
      WHERE 
        id = $i
     ";
debug($query);
    return $db->executeQuery($query);
  }

  function updateGrootboek($i)
  {
    global $USR, $rm;
    $db = new DB();
    $query = "
      UPDATE 
        `RekeningmutatiesAfvink` 
      SET
          `change_date`  = NOW()
        , `change_user`  = '".$this->user."'
        , `omschrijving` = 'grootboek aangepast'
        , `grootboek`    = '".$_POST["grootboek"]."' 
      WHERE 
        id = $i
     ";

    return $db->executeQuery($query);
  }

  function getRekeningMutatie($i)
  {
    $db = new DB();
    return $db->lookupRecordByQuery("SELECT * FROM `Rekeningmutaties` WHERE id = $i");
  }

  function matchcode()
  {
    $code = "M".date("YmdHis")."_".rand(0,9999);
    $this->currentMatchCode = $code;
    return $code;
  }


}