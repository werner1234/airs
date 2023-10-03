<?php
/*
    AE-ICT sourcemodule created 29 jan. 2020
    Author              : Chris van Santen
    Filename            : index.php

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




switch (strtolower($__ses["action"]))
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
    $output = array("result" => "ok") ;
    $result = $output;
    break;
  case "hzresults":
    $output = array();
    include_once "api_hzResults.php";
    $output = array("result" => "ok") ;
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






