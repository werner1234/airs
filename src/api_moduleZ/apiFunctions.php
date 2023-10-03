<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/06 08:14:15 $
    File Versie         : $Revision: 1.3 $

    $Log: apiFunctions.php,v $
    Revision 1.3  2019/02/06 08:14:15  cvs
    call 7488

    Revision 1.2  2018/09/14 09:29:27  cvs
    update 14-9-2018

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710




*/
include_once "../classes/AE_cls_mysql.php";



//////////////////////////////////////////////////////////////////////////////////////
/// standaard functies
//////////////////////////////////////////////////////////////////////////////////////

function slashArray($array)
{
  foreach ($array as $k=>$v)
  {
    $out[$k] = addslashes(trim($v));
  }

  return $out;
}

function noErrors()
{
  global $error;
  return (count($error) == 0);
}

function toJson($data, $addslashes=true)  // output Json string
{
  include_once "../classes/AE_cls_Json.php";
  $json = new AE_Json();
  return $json->json_encode($data, $addslashes);
}

function strip($input)  // normalize and sanatize input
{
  $input = strtolower($input);
  $input = preg_replace('/[^a-z0-9 -]+/', '', $input);
  $input = str_replace(' ', '-', $input);
  return trim($input, '-');
}

function checkQueriesPerHour($ip)  // check connections from IP in the last hour
{
  $db = new DB();
  $query = "SELECT count(id) as connects FROM `API_moduleZ_logging` WHERE ip = '$ip' AND add_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
  $rec = $db->lookupRecordByQuery($query);
  return (int) $rec["connects"];
}

function logApiCall($s)  // logApicall to table
{
  global $error, $__ses;
  $s["data"] = array_map("deSlash", $s["data"]);
  $db = new DB();
  $query = "
    INSERT INTO
      `API_moduleZ_logging`
    SET 
        `add_user`   = 'apiEngine'
      , `add_date` = NOW()
      , `ip`       = '".$s["ipaddress"]."'
      , `referer`  = '".$s["referer"]."'
      , `request`  = '".toJson($s["data"])."'
      , `errors`   = '".toJson($error)."'
      , `results`  = ''
     ";

   $db->executeQuery($query);
   $__ses["logId"] = $db->last_id();
   return true;
}



function UpdateLogApiCall()  // update logApiCall for exit
{
  global $error, $result, $__ses;
  $db = new DB();
  $query = "
    UPDATE
      `API_moduleZ_logging`
    SET 
        `errors`   = '".toJson($error,false)."'
      , `results`  = '".toJson($result)."'
    WHERE 
      id = ".$__ses["logId"]."  
     ";
  $db->executeQuery($query);
  return true;
}

function jsonToDb($jDate)
{
  $date = explode("T",strtoupper($jDate));
  if (strlen($date[0]) == 10 )
  {
    return $date[0];
  }
  else
  {
    return false;
  }

}

function dbToJson($date)
{
  if (strlen($date) == 10)
  {
    return $date."T00:00:00";
  }
  else
  {
    return str_replace(" ", "T",$date);
  }
}

function deSlash ($value)
{
  return str_replace("'", "`", $value);
}

function dateFlipFormat($date)   // maakt van dd-mm-yyyy -> yyyy-mm-dd en viceversa
{
  $p = explode("-",$date);
  return $p[2]."-".$p[1]."-".$p[0];
}

function saveApiGlob($apiGlob)
{
  global $jsonFile;

  file_put_contents($jsonFile, json_encode($apiGlob));
}

function purgeContentFiles($dayOfTheYear)
{
  $files = scandir("content");
//  foreach($files as $file)

}

