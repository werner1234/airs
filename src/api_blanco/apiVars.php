<?php
/*
    AE-ICT sourcemodule created 29 jan. 2020
    Author              : Chris van Santen
    Filename            : apiVars.php

*/
header('X-Powered-By: AE-ICT api blanco engine');   // obsure PHP version

$__debug = true;

$error   = array();   // error condition, empty is no errors;
$result  = array();   // result array for logging
$__glob  = array();

/////////////////////////////////////////////////////////////////
/// Per host specific
/////////////////////////////////////////////////////////////////
$_DB_resources[1]['server'] = "appie8.airshost.nl";
$_DB_resources[1]['user']   = "api_gro";
$_DB_resources[1]['passwd'] = "gpVy4IOTO&i4";
$_DB_resources[1]['db']     = "airs_gro";
//$_DB_resources[1]['db']     = "airs_ano";

$_DB_resources[1]['server'] = "localhost";
$_DB_resources[1]['user']   = "airsCvs";
$_DB_resources[1]['passwd'] = "aeict2009";
$_DB_resources[1]['db']     = "airs_productie";

$__glob["wildcardsAllowed"] = false;                            // mag * als veld gebruikt worden
$__glob["trustedIP"]        = array(

);
$__glob["apiKey"]           = "blan123456789";                // APIkey = aanroep wachtwoord
$__glob["queriesPerHour"]   = 50;                             // max aantal verzoeken per uur   (brute force protectie)

/////////////////////////////////////////////////////////////////
// __ses varaibles (session globals)
/////////////////////////////////////////////////////////////////
$__ses = array();
$__ses["ipChecked"] = false;
$__ses["loggedIn"]  = false;
$__ses["method"]    = $_SERVER["REQUEST_METHOD"];
$__ses["ipaddress"] = $_SERVER["REMOTE_ADDR"];
$__ses["referer"]   = $_SERVER["HTTP_REFERER"];
$__ses["data"]      = $_REQUEST;
$__ses["action"]    = strip($__ses["data"]["action"]);
$__ses["id"]        = (int)$__ses["data"]["actionId"];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $__ses["body"] = (array) json_decode(file_get_contents('php://input'),true);
}
