<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/28 11:33:34 $
    File Versie         : $Revision: 1.1 $

    $Log: index.php,v $
    Revision 1.1  2018/09/28 11:33:34  cvs
    call 7097

    Revision 1.3  2018/02/01 12:55:28  cvs
    update naar airsV2

    Revision 1.2  2018/01/24 14:59:28  cvs
    call 6527

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/


include_once "wwwvars.php";

include_once "../config/debugSpecial.php";

$p = explode("html/", getcwd());

$__appvar["basedir"] = $p[0];

$id = $__ses["id"];  // (int) van tag actionId

/**********************************************************************
 * tags
 *
 * tag                              functie
 * ------------------------------------------------------------------------------
 * credits                          geeft IP, connecties v/h laatste uur en aantal toegestaan
 *
 *
 */

switch ($__ses["action"])
{

  case "credits":
    $output = array();
    $output["yourIP"]        = $__ses["ipaddress"];
    $output["lastHour"]      = checkQueriesPerHour($__ses["ipaddress"]);
    $output["allowPerHour"]  = $__glob["queriesPerHour"];
    $result = $output;
    break;
  case "dataexchange":
    $output = array();
    include_once "api_dataExchange.php";
    $output = $resultData;
    $result = $output;
    break;
  default:
    $error[] = "invalid action: ".$__ses["action"];

    break;
}

if (count($error) > 0)
{
  $output = array("errors" => $error);
}
UpdateLogApiCall();
echo toJson($output);






