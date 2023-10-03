<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/12/21 10:51:41 $
 		File Versie					: $Revision: 1.1 $

 		$Log: AIRS_cls_airshost.php,v $
 		Revision 1.1  2018/12/21 10:51:41  cvs
 		call 7442
 		

*/

class AIRS_cls_airshost
{
  var $db;
  var $bedrijf;
  var $infoArray;

  function AIRS_cls_airshost($bedrijf = null)
  {
    global $USR, $__appvar;
    $this->db = new DB(2);

    if ($bedrijf != null)
    {
      if (is_array($bedrijf))
      {
        $this->bedrijf = $bedrijf;
      }
      else
      {
        $this->bedrijf[] = $bedrijf;
      }
    }
    $this->getInfoArray();
  }

  function getUpdateSchema()
  {
    $updateSchema = array();
    $query = "SELECT `bedrijf`, `updateUren`, `updateMinuten` FROM `bedrijven` WHERE `bedrijf` IN ('".implode("','",$this->bedrijf)."')";
    $this->db->executeQuery($query);
    while($rec = $this->db->nextRecord())
    {
      if ($rec["updateUren"] != "")
      {
        $updateSchema[$rec["bedrijf"]] = $rec["updateUren"]."u / ".$rec["updateMinuten"]."m ";
      }
      else
      {
        $updateSchema[$rec["bedrijf"]] = "onbekend";
      }

    }
    return $updateSchema;
  }

  function getInfoArray()
  {
    $this->infoArray = array();
    $query = "SELECT `bedrijf`, `appVersie`, `versie`, `airshost` FROM `bedrijven` WHERE `bedrijf` IN ('".implode("','",$this->bedrijf)."')";
    $this->db->executeQuery($query);
    while($rec = $this->db->nextRecord())
    {
      $this->infoArray[$rec["bedrijf"]] = array(
        "airshost"       => $rec["airshost"],
        "softwareVersie" => $rec["versie"],
        "platformVersie" => $rec["appVersie"],
      );
    }
  }

}

