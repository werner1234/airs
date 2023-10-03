<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/22 14:20:49 $
    File Versie         : $Revision: 1.4 $

    $Log: apiVars.php,v $
    Revision 1.4  2018/10/22 14:20:49  cvs
    call 7228

    Revision 1.3  2018/09/26 09:30:07  cvs
    update naar DEMO

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


include_once "../../config/local_vars.php";
error_reporting(E_ERROR);
/////////////////////////////////////////////////////////////////
/// Per host specific
/////////////////////////////////////////////////////////////////
$_DB_resources[1]['server'] = "localhost";
$_DB_resources[1]['user']   = "airsCvs";
$_DB_resources[1]['passwd'] = "aeict2009";
$_DB_resources[1]['db']     = "airs_productie";
//$_DB_resources[1]['db']     = "airs_ano";

$__glob["wildcardsAllowed"] = false;                            // mag * als veld gebruikt worden
$__glob["trustedIP"]        = array();
$__glob["apiKey"]           = "AE123456789";                // APIkey = aanroep wachtwoord
$__glob["queriesPerHour"]   = 800;                             // max aantal verzoeken per uur   (brute force protectie)

/////////////////////////////////////////////////////////////////
// __ses varaibles (session globals)
/////////////////////////////////////////////////////////////////
$__ses = array();
$__ses["ipChecked"] = false;
$__ses["loggedIn"]  = false;
$__ses["method"]    = $_SERVER["REQUEST_METHOD"];
$__ses["ipaddress"] = $_SERVER["REMOTE_ADDR"];
$__ses["referer"]   = (isset($_SERVER["HTTP_REFERER"]))?$_SERVER["HTTP_REFERER"]:"onbekend";
$__ses["data"]      = $_REQUEST;
$__ses["action"]    = strip($__ses["data"]["action"]);
$__ses["id"]        = (isset($__ses["data"]["actionId"]))?(int)$__ses["data"]["actionId"]:0;
