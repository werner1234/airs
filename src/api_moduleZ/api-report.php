<?php

/*
    AE-ICT sourcemodule created 15 aug 2018
    Author              : Chris van Santen
    Filename            : api-report.php

    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/06 08:14:15 $
    File Versie         : $Revision: 1.2 $

    $Log: api-report.php,v $
    Revision 1.2  2019/02/06 08:14:15  cvs
    call 7488

    Revision 1.1  2018/09/14 09:26:36  cvs
    update 14-9-2018



    fields
      - portfolio(m),
      - reportType(m),
      - startDate(c),
      - endDate(c)
*/

$db = new DB();

/* purge bestanden ouder dan $purgeAfterDays dagen */
$dlp         = getcwd();
$contentPath = $dlp."/content/";
$files       = scandir($contentPath);
$now         = floor(time()/86400);
foreach ($files as $item)
{
  if (substr($item, -4) != ".pdf")
  {
    continue;
  }

  $fileTime = floor(filectime($contentPath."/".$item)/86400);

  if ($now - $fileTime > $purgeAfterDays)
  {
    unlink($contentPath."/".$item);
  }

}
/* einde purge routine */


$validReportTypes = array("VOLK","PERF");

if ( $arg["portfolio"] == "" OR $arg["reportType"] == "" )
{
  $error[] = "empty mandatory fields";
}

$reportType = strtoupper(trim($arg["reportType"]));
if (!in_array($reportType, $validReportTypes))
{
  $error[] = "unknown reportType: $reportType";
}

$portefeuille = trim($arg["portfolio"]);
$query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '".mysql_real_escape_string($portefeuille)."'";
if (!$portRec = $db->lookupRecordByQuery($query))
{
  $error[] = "portfolio not found: $portefeuille";
}

if ($arg["startDate"] != "")
{
  if (!$startDate = jsonToDb($arg["startDate"]))
  {
    $error[] = "startDate invalid format";
  }
}
else
{
  $startDate = date("Y")."-01-01";
}

if ($arg["stopDate"] != "")
{
  if (!$endDate = jsonToDb($arg["stopDate"]))
  {
    $error[] = "endDate invalid format";
  }
}
else
{
  $endDate = date("Y-m-d");
}

if (noErrors())
{

  unset($_SESSION["mdzPOSTdata"]);

  switch ($reportType)
  {
    case "VOLK":
      $_SESSION["mdzPOSTdata"] = array(
        "posted"              => 1,
        "save"                => 1,
        "downloadPath"        => $contentPath,
        "rapport_types"       => "|VOLK",
        "datum_van"           => dateFlipFormat($startDate),
        "datum_tot"           => dateFlipFormat($endDate),
        "layout"              => "82",
        "Portefeuille"        => $portefeuille,
        "modelcontrole_level" => "fonds",
      );

      include_once("../html/rapportFrontofficeClientAfdrukken.php");

      //$url = "https://apitest.airshost.nl/content/volktest.pdf";
//      debug($savedFilename);
      $url = $savedFilename;
      break;
    case "PERF":
      $_SESSION["mdzPOSTdata"] = array(
        "posted"              => 1,
        "save"                => 1,
        "downloadPath"        => $contentPath,
        "rapport_types"       => "|PERF",
        "datum_van"           => dateFlipFormat($startDate),
        "datum_tot"           => dateFlipFormat($endDate),
        "layout"              => "82",
        "Portefeuille"        => $portefeuille,
        "modelcontrole_level" => "fonds",
      );

      include_once("../html/rapportFrontofficeClientAfdrukken.php");
      $url = $savedFilename;
      break;
    default:
  }

    $output =array(
      "AccountNumber" => $portefeuille,
      "FileReference" => $__glob["urlBase"].$url,
      "Subject"       => $reportType." rapport",
      "Content"       => $reportType." rapport for account $portefeuille over the period ".dbToJson($startDate)." till ".dbToJson($endDate),
    );

}
