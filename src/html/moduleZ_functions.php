<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/11/19 14:26:51 $
    File Versie         : $Revision: 1.9 $

    $Log: moduleZ_functions.php,v $
    Revision 1.9  2018/11/19 14:26:51  cvs
    update naar VRY omgeving

    Revision 1.8  2018/10/24 06:55:05  cvs
    call 7175

    Revision 1.7  2018/10/19 07:04:18  cvs
    call 7175

    Revision 1.6  2018/10/08 06:23:13  cvs
    call 7175, bevindingen 5-10

    Revision 1.5  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.4  2018/09/14 13:49:14  cvs
    call 6709

    Revision 1.3  2018/09/07 10:11:45  cvs
    commit voor robert call 6989

    Revision 1.2  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018



*/


if ($__appvar["bedrijf"] == "VRY")         // productie
{
  $moduleZ_urls = array(

    "klantAdd"        => "https://avryvrywaex01.azurewebsites.net/api/clients",
    "rekeningAdd"     => "https://avryvrywaex01.azurewebsites.net/api/accounts",
    "trade"           => "https://avryvrywaex01.azurewebsites.net/api/trade",
    "products"        => "https://avryvrywaex01.azurewebsites.net/api/products",
    "riskprofiles"    => "https://avryvrywaex01.azurewebsites.net/api/riskprofiles",
    "advisors"        => "https://avryvrywaex01.azurewebsites.net/api/advisors",
    "intermediaries"  => "https://avryvrywaex01.azurewebsites.net/api/intermediaries",

    "financialinstitutes"   => "https://avryvrywaex01.azurewebsites.net/api/financialinstitutes",
    "insurers"              => "https://avryvrywaex01.azurewebsites.net/api/insurers",
    "transactions"          => "https://avryvrywaex01.azurewebsites.net/api/reports/transactions",
    "positions"             => "https://avryvrywaex01.azurewebsites.net/api/reports/positions",      // Handel - Uitgebreid Positions bericht
    "trade"                 => "https://avryvrywaex01.azurewebsites.net/api/trade",  // Handel - Opvragen accounts tbv rebalancing  Expand source
  );
}
else                                       // ontwikkel/test
{
  $moduleZ_urls = array(
    "klantAdd"        => "https://tlumvrywaex01.azurewebsites.net/api/clients",
    "rekeningAdd"     => "https://tlumvrywaex01.azurewebsites.net/api/accounts",
    "rebalance"       => "https://tlumvrywaex01.azurewebsites.net/api/trade",
    "products"        => "https://tlumvrywaex01.azurewebsites.net/api/products",
    "riskprofiles"    => "https://tlumvrywaex01.azurewebsites.net/api/riskprofiles",
    "advisors"        => "https://tlumvrywaex01.azurewebsites.net/api/advisors",
    "intermediaries"  => "https://tlumvrywaex01.azurewebsites.net/api/intermediaries",

    "financialinstitutes"   => "https://tlumvrywaex01.azurewebsites.net/api/financialinstitutes",
    "insurers"              => "https://tlumvrywaex01.azurewebsites.net/api/insurers",
    "transactions"          => "https://tlumvrywaex01.azurewebsites.net/api/reports/transactions",
    "positions"             => "https://tlumvrywaex01.azurewebsites.net/api/reports/positions",      // Handel - Uitgebreid Positions bericht
    "trade"                 => "https://tlumvrywaex01.azurewebsites.net/api/trade",  // Handel - Opvragen accounts tbv rebalancing  Expand source
  );

}

function mzApiPOST1($urlKey, $data)
{
  global $moduleZ_urls;
  $url = $moduleZ_urls["$urlKey"];

  $options = array(
    'http' => array(
      'header'  => "Content-type: application/json\r\n",
      'method'  => 'POST',
      'content' => $data
    )
  );
  $context = stream_context_create($options);
//  debug($context);
  $result = file_get_contents($url, false, $context);
  if ($result === false)
  {
//    return false;
  }

  return $result;
}

function mzApiGET($urlKey)
{
  global $moduleZ_urls, $mzResult, $mzError, $mzHttpcode;
  $url = $moduleZ_urls["$urlKey"];
  $log = array(
    "ipaddress" => $_SERVER["REMOTE_ADDR"],
    "referer"   => $url,
    "data"      => array("GET request"),
    "method"    => "GET"

  );
  mz_logApiCall($log);
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

  $mzResult = curl_exec($ch);
  $mzHttpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);


  mz_UpdateLogApiCall();
  return $mzResult;
}

function mzApiPOST($urlKey, $data)
{
  global $moduleZ_urls, $mzResult, $mzError, $mzHttpcode;
  $mzError = "";

  $url = $moduleZ_urls["$urlKey"];
  $log = array(
    "ipaddress" => $_SERVER["REMOTE_ADDR"],
    "referer"   => $url,
    "data"      => $data,
    "method"    => "POST"

  );
  mz_logApiCall($log);
  $ch = curl_init($url);

  $jsonDataEncoded = $data;

  curl_setopt($ch, CURLOPT_POST, 1);
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

  $mzResult = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $mzHttpcode = $httpcode;
//  $__debug = true;
//  debug($httpcode);


  if ($httpcode != "200" AND trim($mzResult) == "")
  {
    $mzError["httpCode"] = $httpcode;
  }

  if ($httpcode == "200" AND trim($mzResult) == "")
  {
    $mzResult = '{"result":"ok-200" }';
    $mzError = "";
  }

 // $mzResult = (array)json_decode($mzResult);
  //$mzResult["httpCode"] = $httpcode;
  //$mzResult = json_encode($mzResult);


  curl_close($ch);
  mz_UpdateLogApiCall();
  return $mzResult;
}

function mz_logApiCall($s)  // logApicall to table
{
  global $mzError, $_SESSION;
  $db = new DB();
  $query = "
    INSERT INTO
      `API_moduleZ_logging`
    SET 
        `add_user`   = 'apiEngineHTML'
      , `add_date` = NOW()
      , `ip`       = '".$s["ipaddress"]."'
      , `referer`  = '".$s["referer"]."'
      , `method`   = '".$s["method"]."'
      , `request`  = '".mz_toJson($s["data"])."'
      , `errors`   = '".mz_toJson($mzError)."'
      , `results`  = ''
     ";

  $db->executeQuery($query);
  $_SESSION["moduleZ-logId"] = $db->last_id();
  return true;
}



function mz_UpdateLogApiCall($method="GET")  // update logApiCall for exit
{
  global $mzError, $mzResult, $mzHttpcode, $_SESSION;
  $db = new DB();
  $query = "
    UPDATE
      `API_moduleZ_logging`
    SET 
        `errors`   = '".str_replace("'", "`",mz_toJson($mzError,false))."'
      , `results`  = '".str_replace("'", "`",mz_toJson($mzResult,false))."'
      , `httpcode` = '".$mzHttpcode."' 
    WHERE 
      id = ".$_SESSION["moduleZ-logId"];
  $db->executeQuery($query);
  return true;
}

function mz_toJson($data, $addslashes=true)  // output Json string
{
  include_once "../classes/AE_cls_Json.php";
  $json = new AE_Json();
  return $json->json_encode($data, $addslashes);
}

function mz_showCheck($val)
{
  $out = "<i class='far fa-lg ".($val == 1?"fa-check-square":"fa-square")."' aria-hidden='true'></i>";
  return $out;
}