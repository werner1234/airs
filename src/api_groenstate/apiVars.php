<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/28 11:33:34 $
    File Versie         : $Revision: 1.1 $

    $Log: apiVars.php,v $
    Revision 1.1  2018/09/28 11:33:34  cvs
    call 7097

    Revision 1.2  2018/01/24 14:59:28  cvs
    call 6527

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/

header('X-Powered-By: AE-ICT api engine');   // obsure PHP version

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

$__glob["wildcardsAllowed"] = false;                            // mag * als veld gebruikt worden
$__glob["trustedIP"]        = array(

);
$__glob["apiKey"]           = "gro123456789";                // APIkey = aanroep wachtwoord
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
  $__ses["body"] = (array) json_decode(file_get_contents('php://input'));
}
