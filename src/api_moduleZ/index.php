<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/22 13:57:09 $
    File Versie         : $Revision: 1.4 $

    $Log: index.php,v $
    Revision 1.4  2019/02/22 13:57:09  cvs
    call 7488

    Revision 1.3  2019/02/06 08:14:15  cvs
    call 7488

    Revision 1.2  2018/09/14 09:30:57  cvs
    update 14-9-2018

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710



*/

include_once "wwwvars.php";

if ($__develop)
{
  $p = explode("html/", getcwd());
}
else
{
  $p = explode("api_moduleZ/", getcwd());
}



$__appvar["basedir"] = $p[0];

$id = $__ses["id"];  // (int) van tag actionId

$arg = slashArray($__ses["data"]);
/***************************************************************************************************************
 * API interface
 *
 * dateformat strict YYYY-MM-DD
 * (m) mandatory, (c) conditional
 *
 * tag                              functie                               parameters
 * ------------------------------------------------------------------------------
 * credits                          credits for ip                        none
 * fundPrice                        get fund info and price               ISIN(m), currency(m), date(c), numberOfPrices(c)
 * fundPricesPerAM                  get fundprices for one AssetManager   assMan(m), days(c)
 * modelPortfolios                  get model portfolios                  portfolio(c), date(c)
 * orders
 * report                           get report url for portfolio          portfolio(m), reportType(m), startDate(c), endDate(c)
 * reportGetReports
 * reportDeleteBatch
 *
 *
*/

$addslashes = true;

switch (strtolower($__ses["action"]))
{
  case "credits":
    $output = array();
    $output["yourIP"]        = $__ses["ipaddress"];
    $output["lastHour"]      = checkQueriesPerHour($__ses["ipaddress"]);
    $output["allowPerHour"]  = $__glob["queriesPerHour"];
    $result = $output;
    break;
  case "fundprice":
    include_once 'api-fundPrice.php';
    $result = $output;
    break;
  case "fundpricesperam":
    include_once 'api-fundPricesPerAM.php';
    $result = $output;
    break;
  case "modelportfolios":
    include_once 'api-modelPortfolios.php';
    $result = $output;
    break;
  case "orders":
    include_once 'api-orders.php';
    $result = $output;
    break;
  case "report":
    $purgeAfterDays = 7;  // na $purgeAfterDays dagen pdf's verwijderen
    include_once 'api-report.php';
    $result = $output;
    $addslashes = false;
    break;
  case "addfile":
  case "reportgetreports":
  case "reportgetlastbatch":
  case "reportdeletebatch":
    include_once 'api-file.php';
    $result = $output;
    $addslashes = false;
    break;
  default:
    $error[] = "invalid action: ".$__ses["action"];

    break;
}



if (count($error) > 0)
{

  $output = array("errors" => $error);
  header('HTTP/1.0 400 BAD REQUEST');
  header('X-Powered-By: AE-ICT AIRS api engine');   // obsure PHP version
  UpdateLogApiCall();
  echo toJson($output, $addslashes);
}
else
{
  header('X-Powered-By: AE-ICT AIRS api engine');   // obsure PHP version
  UpdateLogApiCall();
  echo toJson($output, $addslashes);
}
