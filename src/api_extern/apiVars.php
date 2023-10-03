<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/14 15:58:56 $
    File Versie         : $Revision: 1.1 $

    $Log: apiVars.php,v $
    Revision 1.1  2018/12/14 15:58:56  cvs
    call 7364

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/

header('X-Powered-By: AE-ICT AIRS api engine');   // obsure PHP version

$__debug = true;

$output  = array();
$error   = array();   // error condition, empty is no errors;
$result  = array();   // result array for logging
$__glob  = array();

/////////////////////////////////////////////////////////////////
/// Per host specific
/////////////////////////////////////////////////////////////////
$_DB_resources[1]['server'] = "localhost";
$_DB_resources[1]['user']   = "airsCvs";
$_DB_resources[1]['passwd'] = "aeict2009";
$_DB_resources[1]['db']     = "airs_productie";
//$_DB_resources[1]['db']     = "airs_ano";

$__glob["wildcardsAllowed"]     = false;                            // mag * als veld gebruikt worden
$__glob["submitterIpRequired"]  = false;                             // submitter IP verplicht
$__glob["eventCodeRequired"]    = true;                             // eventCode verplicht
$__glob["trustedIP"]            = array();                          // vanaf welke IP adressen toegestaan

$__glob["apiKey"]               = "aeict1234567890";                // APIkey = aanroep wachtwoord
$__glob["queriesPerHour"]       = 75;                              // max aantal verzoeken per uur   (brute force protectie)
$__glob["VB"]                   = "AEI";
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



