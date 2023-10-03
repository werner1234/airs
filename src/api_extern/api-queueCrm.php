<?php
/*
    AE-ICT sourcemodule created 12 dec 2018
    Author              : Chris van Santen
    Filename            : api-queueCrm.php

    $Log: api-queueCrm.php,v $
    Revision 1.3  2019/03/01 15:15:04  cvs
    call 7364

    Revision 1.2  2018/12/14 15:58:56  cvs
    call 7364

    Revision 1.1  2018/12/12 15:04:21  cvs
    call 7364

*/

$db = new DB();

$dataFields     = array();
$invalidFields  = array();
$crmValidFields = array(
  "voorletters",
  "tussenvoegsel",
  "achternaam",
  "verzendAanhef",
  "email",
  "tel1",
  "adres",
  "pc",
  "plaats",
  "land",
  "memo"
);



foreach($__ses["data"] as $field=>$value)
{
  if ( in_array($field, $crmValidFields) )
  {
    $dataFields[$field] = $value;
  }
  else
  {
    if (substr($field, 0,3) == "cf_")
    {
      $dataFields[$field] = $value;
    }
    else
    {
      $invalidFields[] = $field;
    }

  }
}

if (count($dataFields) > 0)
{

//  $datafieldsOrg = $dataFields;



  $dataFields["voorletters"]    = strtoupper(trim($dataFields["voorletters"]));
  $dataFields["achternaam"]     = ucwords(trim($dataFields["achternaam"]));
  $dataFields["tussenvoegsel"]  = trim($dataFields["tussenvoegsel"]);
  $dataFields["adres"]          = ucwords($dataFields["adres"]);
  $dataFields["plaats"]         = ucwords($dataFields["plaats"]);
  $dataFields["land"]           = ucwords($dataFields["land"]);
  $dataFields["pc"]             = strtoupper($dataFields["pc"]);

  $achternaam     = $dataFields["achternaam"];
  $tusselvoegsel  = $dataFields["tussenvoegsel"];
  $voorletters    = $dataFields["voorletters"];

  $dataFields["zoekveld"] = $achternaam.", ".$voorletters.rtrim(" ".strtolower($tussenvoegsel));
  $dataFields["naam"]     =  $voorletters.rtrim(" ".strtolower($tusselvoegsel))." ".$achternaam;

  $incFile = "api-queueCrm_1_".$__glob["VB"].".php";


  if (file_exists($incFile))
  {

    include $incFile;
  }


  $data = toJson($dataFields);
  $query = "
  INSERT INTO 
    `apiQueueExtern` 
  SET  
    `add_user` = 'apiExt', 
    `add_date` = NOW(), 
    `change_user` = 'apiExt', 
    `change_date` = NOW(), 
    `action` = '{$__ses["action"]}', 
    `eventCode` = '{$__ses["eventCode"]}', 
    `submitterIp` = '{$__ses["submitterIp"]}', 
    `dataFields` = '{$data}'
  ";
  $db->executeQuery($query);
  $output["result"] = "ok";
}
else
{
  $error[] = "No valid data";
}

if (count($invalidFields) > 0)
{
  $error[] = "invalid datafields detected: ".implode(", ", $invalidFields);
}


