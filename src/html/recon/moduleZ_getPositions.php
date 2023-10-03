<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/11/28 10:52:11 $
    File Versie         : $Revision: 1.3 $

    $Log: moduleZ_getPositions.php,v $
    Revision 1.3  2018/11/28 10:52:11  cvs
    call 7282

    Revision 1.2  2018/10/12 10:48:57  cvs
    call 7175

    Revision 1.1  2018/06/18 06:58:14  cvs
    update naar VRY omgeving



*/

include_once("wwwvars.php");

if (!function_exists(‘json_decode’))
{
  include_once "../../config/JSON.php";
}

include "../moduleZ_functions.php";

$url = $moduleZ_urls["positions"];

$result = file_get_contents($url);

$log = array(
  "ipaddress" => $_SERVER["REMOTE_ADDR"],
  "referer"   => $url,
  "data"      => '{ "aktie": "ophalen postities" }'
);
$mzError = "";

mz_logApiCall($log);

$pos = strpos($result, '{');   // zoek eerste { strip wat ervoor staat (BOM)

$result = substr($result,$pos);
$data = json_decode($result,true);
//debug($data);

$dataRows = array();

// van de datum velden alleen de eerste 10 tekens gebruiken
// onderstaande array bevat de datumvelden
$datesArray = array("date");

// welke velden (keys) komen we tegen in de json data, dit worden later kolommen in de datafile
$keyArray = array("account_number");



// maak dataregels
foreach ($data['accounts'] as $account)
{
  $rekeningnr = $account["account_number"];
  $row = array("account_number" => $rekeningnr);
  foreach ($account["positions"] as $positions)
  {

    foreach ($positions as $k=> $v)
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
  // cash toevoegen ca;ll 6642
  $k = "isin";
  if (!in_array($k, $keyArray))
  {
    $keyArray[] = $k;
  }
  $row["isin"] = "cash";

  $k = "date";
  if (!isset($row["date"]))
  {
    $row["date"] = date("Y-m-d")."T00:00:00+00:00";
  }

  if (!in_array($k, $keyArray))
  {
    $keyArray[] = $k;
  }

  $k = "instrument_currency";
  if (!in_array($k, $keyArray))
  {
    $keyArray[] = $k;
  }
  $row["instrument_currency"] = "EUR";



  $k = "nr_of_participations";
  if (!in_array($k, $keyArray))
  {
    $keyArray[] = $k;
  }
  $row["nr_of_participations"] = $account["transfer"];
  $dataRows[] = $row;
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

$file = "../../temp/moduleZpostions_".$USR."_".date("YmdHis").".csv";
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
