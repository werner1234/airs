<?php

/*
  AE-ICT source module
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2017/06/14 14:37:57 $
  File Versie					: $Revision: 1.2 $

  $Log: fondsReconClass.php,v $
  Revision 1.2  2017/06/14 14:37:57  cvs
  no message

  Revision 1.1  2017/03/31 12:39:44  cvs
  eerste commit



 */

class fondsReconClass
{
  var $user;
  var $server = 1;
  var $batch = "";
  var $tableName = "tijdelijkeFondsRecon";
  var $resultset = "";
  var $bankField = "";
  var $stats     = array();
  var $bcFound   = array();
  var $bankrow   = array();
  var $airsrowISIN = array();
  var $airsrowBC = array();
  var $matchArray = array();
  var $veldArray = array(
    "TGB"    => "stroeveCode",
    "ISIN"   => "ISINCode",

  );


  function fondsReconClass($depotBank)
  {
    global $USR, $__appvar;
    
    //include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
    $depotBank = ($depotBank == "SNS")?"NIBC":$depotBank;
    $this->user = $USR;
    $this->depotbank = $depotBank;
    $this->batch = date("YmdHi");
    $this->bankField = $this->veldArray[$depotBank];


  }

  function addBank($bankCode, $ISIN, $valuta, $beurs)
  {
    $this->bankrow[] = array($bankCode, $ISIN, $valuta, $beurs);

  }

  function addAirs($bankCode, $ISIN, $valuta, $beurs)
  {
    $key = $ISIN.$valuta;
    $this->airsrowISIN[$key][] = array($bankCode, $ISIN, $valuta, $beurs);
    $this->airsrowBC[$bankCode][] = array($bankCode, $ISIN, $valuta, $beurs);
  }

  function matchRows()
  {

    $db = new DB();
    $this->bcFound = array();
    debug(count($this->bankrow),"bankregels");
    foreach ($this->bankrow as $bank)
    {
//      debug($bank,"BANK");
      $isin = $bank[1].$bank[2];
//      debug($isin);
      $count = 0;
      foreach ($this->airsrowISIN[$isin] as $isinItem)
      {
//        debug($isinItem, "AIRS");
        $count++;
        if ($isinItem[1] != "")
        {
          $this->bcFound[] = $bank[0];
          if ((int)$isinItem[0] == (int)$bank[0])
          {
            $this->matchArray[] = array($bank[0],$bank[1],$bank[2],$bank[3],$isinItem[0],$isinItem[1],$isinItem[2],$isinItem[3],"match" => "fullmatch");
            $this->stats["fullmatch"]++;
          }
          else
          {
            //$this->matchArray[] = array((int)$bank[0],$bank[1],$bank[2],$isinItem[0],$isinItem[1],$isinItem[2],"match" => "Bankcode <> BC Airs");
            //$this->stats["Bankcode <> BC Airs"]++;
            if ($this->airsrowBC[$bank[0]][0] != "" )
            {
              $this->matchArray[] = array((int)$bank[0],$bank[1],$bank[2],$isinItem[0],$isinItem[1],$isinItem[2],"match" => "ISIN match / BC <>");
              $this->stats["ISIN match / BC <>"]++;
            }
          }
        }
      }
      if ($count == 0)
      {
        if ($this->airsrowBC[$bank[0]][0] != "")
        {
          $airs = $this->airsrowBC[$bank[0]][0];
          $this->matchArray[] = array((int)$bank[0],$bank[1],$bank[2],$airs[0],$airs[1],$airs[2],"match" => "BC match / ISIN <>");
          $this->stats["BC match / ISIN <>"]++;
        }
        else
        {
          $this->matchArray[] = array((int)$bank[0],$bank[1],$bank[2],"match" => "missing in AIRS");
          $this->stats["missing in AIRS"]++;
        }
      }
    }

    $query = '
  SELECT 
    ISINCode,
    Valuta,
    stroeveCode,
    Fonds,
    beurs
  FROM
    Fondsen  
  WHERE
    stroeveCode <> "" AND
    stroeveCode NOT IN ("'.implode('","',$this->bcFound).'")
  ';

   $db->executeQuery($query);
   while ($rec = $db->nextRecord())
   {
     $this->matchArray[] = array("","","",(int)$rec["stroeveCode"],$rec["ISINCode"],$rec["Valuta"],"match" => "AIRS BC missing in Bank");
     $this->stats["AIRS BC missing in Bank"]++;
   }

  }

  function addBankRow()
  {

    $db = new DB();
    $queryBase = "
    INSERT INTO `".$this->tableName."` 
       ( `source`,
         matchcode,
         add_user,
         add_date,
         change_user, 
         change_date, 
         batch,
         depot,
         bankCode, 
         beurs,
         ISIN,
         valuta,
         isinVal_bank,
         isinVal_airs,
         bankcode_bank,
         bankcode_airs
          )
    VALUES
         ";
    $query = $queryBase;
    $interval = 200;
    $i = $interval;
    foreach ($this->bankrow as $item)
    {
      $i--;
      $query .= "\n(
      'bank',
      'bestand', 
      '".$this->user."',
      NOW(),
      '".$this->user."',
      NOW(),'".$this->batch."', 
      '".$this->depotbank."', 
      '".$item[0]."', 
      '".$item[3]."', 
      '".$item[1]."', 
      '".$item[2]."',
      1,
      0,
      1,
      0     
      ),";
      if ($i == 0)
      {
        $query = substr($query,0,-1);
        echo "*";
        $db->executeQuery($query);
        $i = $interval;
        $query = $queryBase;
      }
    }
    $query = substr($query,0,-1);
    echo "*";
    $db->executeQuery($query);
    $this->bankrow = array();   // destroy array
  }

  function getStats()
  {
    $db = new DB();
    $query = "
    SELECT 
      count(id) as totaal
    ";
    foreach($this->veldArray as $item)
    {
      $query .= ", SUM(".$item." <> '') AS aantal_".$item."\n";
    }
    $query .= "FROM Fondsen";

    return $db->lookupRecordByQuery($query);
  }



  function addAirsRecords()
  {
    $db = new DB();
    $query = "SELECT `".$this->bankField."` as bankcode, ISINCode, Valuta, beurs FROM Fondsen WHERE `".$this->bankField."` <> '' ";
    debug($query);
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $out[] = $rec;
      $bcArray[] = $rec["bankcode"];
    }
debug($bcArray);
    $db = new DB();
    $queryBase = "
    INSERT INTO `".$this->tableName."` 
       ( `source`,
         matchcode,
         add_user,
         add_date,
         change_user, 
         change_date, 
         batch,
         depot,
         bankCode, 
         beurs,
         ISIN,
         valuta,
         isinVal_bank,
         isinVal_airs,
         bankcode_bank,
         bankcode_airs)
    VALUES
         ";
    $query = $queryBase;
    $interval = 200;
    $i = $interval;
    foreach ($out as $item)
    {
      $i--;
      $query .= "\n(
      'airs',
      'Airs bankcode', 
      '".$this->user."',
      NOW(),
      '".$this->user."',
      NOW(),
      '".$this->batch."', 
      '".$this->depotbank."', 
      '".$item["bankcode"]."', 
      '".$item["beurs"]."', 
      '".$item["ISINCode"]."', 
      '".$item["Valuta"]."',
      0,
      1,
      0,
      1
      ),";
      if ($i == 0)
      {
        $query = substr($query,0,-1);
        echo "*";
        $db->executeQuery($query);
        debug($query);
        $i = $interval;
        $query = $queryBase;
      }
    }
    $query = substr($query,0,-1);
    echo "*";
    $db->executeQuery($query);
    $out = array(); // destroy array

    $query = "
    INSERT INTO tijdelijkeFondsRecon
    (
      ISIN, 
      valuta, 
      beurs, 
      source, 
      depot, 
      matchcode, 
      add_user, 
      add_date, 
      change_user, 
      change_date,
      isinVal_bank, 
      isinVal_airs, 
      bankcode_bank,
      bankcode_airs
    )
    (
    SELECT
      tijdelijkeFondsRecon.ISIN,
      tijdelijkeFondsRecon.valuta,
      tijdelijkeFondsRecon.beurs,
      'airs',
      '".$this->depotbank."',
      'zonder bankcode',
      tijdelijkeFondsRecon.add_user,
      tijdelijkeFondsRecon.add_date,
      tijdelijkeFondsRecon.change_user,
      tijdelijkeFondsRecon.change_date,
      0,
      1,
      0,
      0
    FROM
      tijdelijkeFondsRecon
    INNER JOIN Fondsen ON 
      tijdelijkeFondsRecon.ISIN = Fondsen.ISINCode AND 
      tijdelijkeFondsRecon.valuta = Fondsen.Valuta
    WHERE 
      Fondsen.stroeveCode = '' and source = 'bank'
    )
    ";
    debug($query);
    echo "<br/> query voor AIRS fondsen zonder bankcode";
    flush();
    flush();
    $db->executeQuery($query);

    echo "<br/> tellen ivm dubbele ISIN/val bank";
    flush();
    flush();
    $query = "
    SELECT
      count(id) as aantal , 
      ISIN,
      valuta
    FROM
      tijdelijkeFondsRecon
    WHERE
      source = 'bank' 
    GROUP BY
      ISIN, valuta
    HAVING 
      aantal <> 1
    ";
    $db2 = new DB();
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $query = "
      UPDATE 
        tijdelijkeFondsRecon 
      SET
        isinVal_bank = ".$rec["aantal"]."
      WHERE 
        ISIN = '".$rec["ISIN"]."' AND 
        valuta = '".$rec["valuta"]."' AND
        source = 'bank'
      ";
      $db2->executeQuery($query);
      echo "*";
    }

    echo "<br/> tellen ivm dubbele ISIN/val AIRS";
    flush();
    flush();
    $query = "
    SELECT
      count(id) as aantal , 
      ISIN,
      valuta
    FROM
      tijdelijkeFondsRecon
    WHERE
      source = 'airs' 
    GROUP BY
      ISIN, valuta
    HAVING 
      aantal <> 1
    ";
    $db2 = new DB();
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $query = "
      UPDATE 
        tijdelijkeFondsRecon 
      SET
        isinVal_bank = ".$rec["aantal"]."
      WHERE 
        ISIN = '".$rec["ISIN"]."' AND 
        valuta = '".$rec["valuta"]."' AND
        source = 'airs'
      ";
      $db2->executeQuery($query);
      echo "*";
    }

    echo "<br/> tellen ivm dubbele bankCode bank";
    flush();
    flush();
    $query = "
    SELECT
      count(id) as aantal , 
      bankCode
    FROM
      tijdelijkeFondsRecon
    WHERE
      source = 'bank' 
    GROUP BY
      bankCode
    HAVING 
      aantal <> 1
    ";
    $db2 = new DB();
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $query = "
      UPDATE 
        tijdelijkeFondsRecon 
      SET
        bankcode_bank = ".$rec["aantal"]."
      WHERE 
        bankCode = '".$rec["bankCode"]."',
        source = 'bank'
      ";
      $db2->executeQuery($query);
      echo "*";
    }

    echo "<br/> tellen ivm dubbele bankCode AIRS";
    flush();
    flush();
    $query = "
    SELECT
      count(id) as aantal , 
      bankCode
    FROM
      tijdelijkeFondsRecon
    WHERE
      source = 'airs' 
    GROUP BY
      bankCode
    HAVING 
      aantal <> 1
    ";
    $db2 = new DB();
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $query = "
      UPDATE 
        tijdelijkeFondsRecon 
      SET
        bankcode_bank = ".$rec["aantal"]."
      WHERE 
        bankCode = '".$rec["bankCode"]."',
        source = 'airs'
      ";
      $db2->executeQuery($query);
      echo "*";
    }
    //echo "<br/> tellen ivm dubbele ISIN/val bank";
    flush();
    flush();

//
//    $query = "
//      SELECT
//        bankCode,
//        ISIN,
//        valuta,
//        beurs
//      FROM
//         tijdelijkeFondsRecon
//      WHERE
//        `source` = 'bank'
//        AND
//        `".$this->bankField."` NOT IN ('','".implode("',\n'",$bcArray)."') ";
//    debug($query);

  }


  function matchAirs ($bankCode, $ISIN, $valuta)
  {
    $db = new DB();
    $query = "
    SELECT 
      * 
    FROM 
      `Fondsen` 
    WHERE 
      `".$this->bankField."` = '".$bankCode."' ";

    $match1 = $db->lookupRecordByQuery($query);

    $query = "
    SELECT 
      * 
    FROM 
      `Fondsen` 
    WHERE 
      `ISINCode` = '".$ISIN."'  AND 
      `Valuta` = '".$valuta."'";
    $match2 = $db->lookupRecordByQuery($query);

    $query = "
    SELECT 
      * 
    FROM 
      `Fondsen` 
    WHERE 
      `ISINCode` = '".$ISIN."'  AND 
      `Valuta` = '".$valuta."'  AND 
      `".$this->bankField."` = '".$bankCode."' ";
    $match3 = $db->lookupRecordByQuery($query);

    if ($match3)
    {
      return array("result" => "full", "ISIN"     => $match3["ISINCode"],
                                       "valuta"   => $match3["Valuta"],
                                       "bankcode" => $match3[$this->bankField],
                                       "beurs"    => $match3["beurs"]);
    }



    if ($match1)
    {
      return array("result" => "bank", "ISIN"     => $match1["ISINCode"],
                                       "valuta"   => $match1["Valuta"],
                                       "bankcode" => $match1[$this->bankField],
                                       "beurs"    => $match1["beurs"]  );
    }

    if ($match2)
    {
      return array("result" => "ISIN", "ISIN"     => $match2["ISINCode"],
                                       "valuta"   => $match2["Valuta"],
                                       "bankcode" => $match2[$this->bankField],
                                       "beurs"    => $match2["beurs"] );
    }

    return array("result" => "none");

  }
  

  function getFonds($fonds, $part = "all")
  {
    $db = new DB($this->server);
    $q = "SELECT * FROM Fondsen WHERE Fonds='$fonds' ";
    if ($rec = $db->lookupRecordByQuery($q))
    {
      if ($part == "all")
        return $rec;
      else
        return $rec[$part];
    }
    else
      return false;
  }


  function initModule()
  {
    include_once("AE_cls_SQLman.php");

    $tst = new SQLman();
    $tst->tableExist($this->tableName,true);

    $tst->changeField($this->tableName,"datum",array("Type"=>"date","Null"=>false));
    $tst->changeField($this->tableName,'depot',array("Type"=>"varchar(15)","Null"=>false));
    $tst->changeField($this->tableName,'bank_bankCode',array("Type"=>"varchar(28)","Null"=>false));
    $tst->changeField($this->tableName,'bank_ISIN',array("Type"=>"varchar(28)","Null"=>false));
    $tst->changeField($this->tableName,'bank_valuta',array("Type"=>"varchar(4)","Null"=>false));
    $tst->changeField($this->tableName,'bank_beurs',array("Type"=>"varchar(48)","Null"=>false));
    $tst->changeField($this->tableName,'airs_bankCode',array("Type"=>"varchar(28)","Null"=>false));
    $tst->changeField($this->tableName,'airs_ISIN',array("Type"=>"varchar(28)","Null"=>false));
    $tst->changeField($this->tableName,'airs_valuta',array("Type"=>"varchar(4)","Null"=>false));
    $tst->changeField($this->tableName,'airs_beurs',array("Type"=>"varchar(48)","Null"=>false));
    $tst->changeField($this->tableName,'matchcode',array("Type"=>"varchar(50)","Null"=>false));
    $tst->changeField($this->tableName,'batch',array("Type"=>"varchar(20)","Null"=>false));

  }

  function clearTable()
  {
    $db = new DB();
    $extrawhere = " AND depot = '".$this->depotbank."' ";
    $query = "DELETE FROM `".$this->tableName."` WHERE add_user = '".$this->user."' ".$extrawhere;
    debug($query);
    $db->executeQuery($query);
  }

  


}
?>