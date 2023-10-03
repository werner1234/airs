<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/14 15:58:56 $
    File Versie         : $Revision: 1.1 $

    $Log: apiFunctions.php,v $
    Revision 1.1  2018/12/14 15:58:56  cvs
    call 7364



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
  $query = "SELECT count(id) as connects FROM `API_extern_logging` WHERE ip = '$ip' AND add_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
  $rec = $db->lookupRecordByQuery($query);
  return (int) $rec["connects"];
}

function logApiCall($s)  // logApicall to table
{
  global $error, $__ses;
  $db = new DB();
  $query = "
    INSERT INTO
      `API_extern_logging`
    SET 
        `add_user`   = 'apiEngine'
      , `add_date` = NOW()
      , `ip`       = '".$s["ipaddress"]."'
      , `referer`  = '".$s["referer"]."'
      , `request`  = '".toJson($s["data"])."'
      , `errors`   = '".toJson($error)."'
      , `method`   = '{$__ses["method"]}'
      , `action`   = '{$__ses["action"]}'
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
      `API_extern_logging`
    SET 
        `errors`   = '".toJson($error)."'
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