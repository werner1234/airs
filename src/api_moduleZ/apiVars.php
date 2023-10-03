<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/22 13:57:42 $
    File Versie         : $Revision: 1.3 $

    $Log: apiVars.php,v $
    Revision 1.3  2019/02/22 13:57:42  cvs
    call 7488

    Revision 1.2  2019/02/06 08:16:23  cvs
    call 7488

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/

$__debug = true;
$__develop = true;
$error   = array();   // error condition, empty is no errors;
$result  = array();   // result array for logging
$__glob  = array();

/////////////////////////////////////////////////////////////////
/// Per host specific
/////////////////////////////////////////////////////////////////
$_DB_resources[1]['server'] = "localhost";
$_DB_resources[1]['user']   = "airsCvs";
$_DB_resources[1]['passwd'] = "aeict2009";
//$_DB_resources[1]['db']     = "airs_apitest";
$_DB_resources[1]['db']     = "airs_vry";

$__glob["wildcardsAllowed"] = false;                            // mag * als veld gebruikt worden
$__glob["trustedIP"]        = array();                          // vanaf welke IP adressen toegestaan

$__glob["apiKey"]           = "aeict1234567890";                // APIkey = aanroep wachtwoord
$__glob["queriesPerHour"]   = 250;                              // max aantal verzoeken per uur   (brute force protectie)
$__glob["VB"] = "VRY";
$__glob["urlBase"] = "https://apitest.airshost.nl/content/";    // basisurl voor download links
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


