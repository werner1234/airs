<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/14 15:58:56 $
    File Versie         : $Revision: 1.1 $

    $Log: index.php,v $
    Revision 1.1  2018/12/14 15:58:56  cvs
    call 7364


*/

include_once "wwwvars.php";

$p = explode("html/", getcwd());
//debug($p);
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
   // $output["sess"] = $__ses;
    $result = $output;
    break;

  case "queuecrm":
    include_once 'api-queueCrm.php';
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
  $result = "";
}
UpdateLogApiCall();
echo toJson($output, $addslashes);
