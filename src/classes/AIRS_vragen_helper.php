<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/04/01 13:05:15 $
    File Versie         : $Revision: 1.6 $

    $Log: AIRS_vragen_helper.php,v $
    Revision 1.6  2019/04/01 13:05:15  cvs
    call 7662

*/

class AIRS_vragen_helper
{
  var $crmRefId = -1;
  var $user;
  var $ingevuldArray = array();
  var $VragenIngevuldRec = array();
  var $crmRefRec = array();

  function AIRS_vragen_helper($crmRefId=-1, $options = array())
  {
    global $USR;

    $this->user = $USR;
    $this->crmRefId = $crmRefId;
    $db = new DB();

    if ($this->crmRefId == -1 )  // geen crmrefId bekend ophalen uit de tabel
    {
       if (
           (int)$options["vragenlijstId"] != 0 AND
           (int)$options["relatieId"] != 0 AND
           $options["add_date"] != ""
          )
       {
         $query = "SELECT * FROM `VragenLijstenPerRelatie` WHERE datum = '".substr($options["add_date"],0,10)."' AND nawId = ".(int)$options["relatieId"]." AND vragenLijstId = ".(int)$options["vragenlijstId"];
         $this->crmRefRec = $db->lookupRecordByQuery($query);
       }
       $this->crmRefId = (int)$this->crmRefRec["id"];
    }

    if ($this->crmRefId > 0 )
    {
      $query = "SELECT * FROM `VragenLijstenPerRelatie` WHERE id = {$this->crmRefId}";
      $this->crmRefRec = $db->lookupRecordByQuery($query);

      $rec = array();
      $query = "SELECT * FROM `VragenIngevuld` WHERE crmRef_id = {$this->crmRefId}";
      $db->executeQuery($query);

      while ($this->VragenIngevuldRec = $db->nextRecord())
      {
        $rec[] = $this->VragenIngevuldRec;

        if ($this->VragenIngevuldRec["antwoordId"] < 1)
        {
          $this->ingevuldArray[$this->VragenIngevuldRec["vraagId"]] = array( "antwoord" => $this->VragenIngevuldRec["antwoordOpen"], "action" => "read", "openvraag" => 1);
        }
        else
        {
          $this->ingevuldArray[$this->VragenIngevuldRec["vraagId"]] = array( "antwoord" => $this->VragenIngevuldRec["antwoordId"], "action" => "read");
        }
      }
    }





  }

  function getCrmRefId()
  {
    return $this->crmRefId;
  }

  function updateIngevuld($vraagId, $antwoordId)
  {

    $openVraag = (substr($vraagId,-1) == "O");
    $vraagId = substr($vraagId,0,-1);
    if ($this->ingevuldArray[$vraagId]["antwoord"] != $antwoordId)  // is antwoord gewijzigd?
    {
      $this->ingevuldArray[$vraagId]["antwoord"] = $antwoordId;
      $this->ingevuldArray[$vraagId]["openVraag"] = $openVraag;
      if ($this->ingevuldArray[$vraagId]["action"] != "read")
      {
        $this->ingevuldArray[$vraagId]["action"] = "insert";
      }
      else
      {
        $this->ingevuldArray[$vraagId]["action"] = "update";
      }
    }
  }

  function showIngevuld($kop="antwoordMatrix" )
  {
    debug($this->ingevuldArray,$kop);
  }

  function saveIngevuld()
  {
    $db = new DB();
//    debug($this->ingevuldArray);
    foreach ($this->ingevuldArray as $vId => $antwoord)
    {
      if ($antwoord["openVraag"])
      {
        $aId   = 0;
        $aOpen = mysql_real_escape_string($antwoord["antwoord"]);
      }
      else
      {
        $aId   = $antwoord["antwoord"];
        $aOpen = "";
      }


      //debug($antwoord, $vId);
      switch ($antwoord["action"])
      {
        case "update":  // bestaande updaten
          $query = "
            UPDATE 
              `VragenIngevuld` 
            SET
              change_date  = NOW(),
              change_user  = '".$this->user."', 
              antwoordId   = '".$aId."',
              antwoordOpen = '".$aOpen."'
            WHERE
              crmRef_id = '".$this->crmRefId."' AND
              vraagId = '".$vId."'";
//          debug($query);
          $db->executeQuery($query);
          break;
        case "insert": // nieuw antwoord toevoegen
          $query = "
            INSERT INTO 
              `VragenIngevuld` 
            SET
              add_date       = '".$this->crmRefRec["datum"]."',
              add_user       = '".$this->user."', 
              change_date    = NOW(),
              change_user    = '".$this->user."', 
              antwoordId     = '".$aId."',
              antwoordOpen   = '".$aOpen."',
              crmRef_id      = '".$this->crmRefId."',
              vraagId        = '".$vId."',
              relatieId      = '".$this->crmRefRec["nawId"]."',
              vragenlijstId  = '".$this->crmRefRec["vragenLijstId"]."'
              ";
          $db->executeQuery($query);
          break;
        default: // ongewijzigd
      }
    }
  }

  function getIngevuld($vraagId)
  {
    return $this->ingevuldArray[$vraagId]["antwoord"];
  }
}