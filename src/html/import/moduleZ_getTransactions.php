<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/12 10:49:38 $
    File Versie         : $Revision: 1.2 $

    $Log: moduleZ_getTransactions.php,v $
    Revision 1.2  2018/10/12 10:49:38  cvs
    call 7175

    Revision 1.1  2018/06/18 06:56:32  cvs
    update naar VRY omgeving



*/
include_once("wwwvars.php");

if (!function_exists("json_decode"))
{
  include_once "../../config/JSON.php";
}

include "../moduleZ_functions.php";

$url = $moduleZ_urls["transactions"];

$result = file_get_contents($url);

$log = array(
  "ipaddress" => $_SERVER["REMOTE_ADDR"],
  "referer"   => $url,
  "data"      => '{ "aktie": "ophalen transakties" }'
);
$mzError = "";

mz_logApiCall($log);
$pos = strpos($result, '{');   // zoek eerste { strip wat ervoor staat (BOM)

$result = substr($result,$pos);
$data = json_decode($result,true);

$dataRows = array();

// van de datum velden alleen de eerste 10 tekens gebruiken
// onderstaande array bevat de datumvelden
$datesArray = array("booking_date","transaction_date","settlement_date");

// welke velden (keys) komen we tegen in de json data, dit worden later kolommen in de datafile
$keyArray = array("account_number");

// maak dataregels
foreach ($data['accounts'] as $account)
{
  $rekeningnr = $account["account_number"];
  foreach ($account["transactions"] as $trans)
  {
    $row = array("account_number" => $rekeningnr);
    foreach ($trans as $k=>$v)
    {
      if (!in_array($k, $keyArray))
      {
        $keyArray[] = $k;
      }
      if (in_array($k, $datesArray))
      {
        $row["$k"] = substr($v,0,10);
      }
      else
      {
        $row["$k"] = $v;
      }

    }
    $dataRows[] = $row;
  }
}

$output = array();
$r=0;
foreach($dataRows as $data)
{
  $r++;
  $row = array();
  if ($r == 1)  // eerste regel is de header
  {
    foreach ($keyArray as $k)
    {
      $row[] = $k;
    }
    $output[] = $row;
    $r++;
    $row = array();
  }

  foreach ($keyArray as $k)
  {
    $row[] = (String)$data[$k];
  }
  $output[] = $row;
}

$file = "../../temp/moduleZtrans_".$USR."_".date("YmdHis").".csv";
$fp = fopen($file, 'w');  // maak een tijdelijk bestand aan in de temp dir
foreach ($output as $fields)
{
  fputcsv($fp, $fields);
}
fclose($fp);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($file));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo file_get_contents($file);

unlink($file);
