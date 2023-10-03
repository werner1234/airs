<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/14 15:58:56 $
    File Versie         : $Revision: 1.1 $

    $Log: apiAuth.php,v $
    Revision 1.1  2018/12/14 15:58:56  cvs
    call 7364


*/

/////////////////////////////////////////////////////////////////
/// Access checking
/////////////////////////////////////////////////////////////////

if (count($__glob["trustedIP"]) > 0)  // alleen via trusted ip's
{
  $__ses["ipChecked"] = in_array($__ses["ipaddress"], $__glob["trustedIP"]);
}
else
{
  $__ses["ipChecked"] = true;
}


/////////////////////////////////////////////////////////////////
/// APIkey checking
/////////////////////////////////////////////////////////////////

// API key doorgeven via de headers
$headers = getallheaders();

$keyValid         = ($__glob["apiKey"] == $headers["apiKey"]);
$__ses["action"]  = $headers["action"];

if ($keyValid AND $__ses["ipChecked"])
{
  $__ses["loggedIn"] = true; // validation true
}
else
{
  $error[] = "invalid autorisation";
}

$submitterIp = trim(preg_replace('/[^0-9.]+/', '', $headers["submitterIp"]));
if ($__glob["submitterIpRequired"] AND $submitterIp == "" )
{
  $error[] = "submitter IP is mandatory";
}
else
{
  $__ses["submitterIp"] = $submitterIp;
}

if ($__glob["eventCodeRequired"] AND trim($headers["eventCode"]) == "")
{
  $error[] = "eventCode is mandatory";
}
else
{
  $__ses["eventCode"] = strip($headers["eventCode"]);
}

/////////////////////////////////////////////////////////////////
/// Connects per hour checking
/////////////////////////////////////////////////////////////////
$requestsPerHour = checkQueriesPerHour($__ses["ipaddress"]);
if ($requestsPerHour > $__glob["queriesPerHour"])
{
  $error[] = "too many requestsPerHour ".$requestsPerHour." ( max: ".$__glob["queriesPerHour"].") ";
}

/////////////////////////////////////////////////////////////////
/// block request after 15 errormsgs
/////////////////////////////////////////////////////////////////
$requestsPerHour = checkQueriesPerHour($__ses["ipaddress"]);
if ($requestsPerHour > $__glob["queriesPerHour"]+15)
{
  header("HTTP/1.0 404 Not Found");
  exit;
}


/////////////////////////////////////////////////////////////////
/// add logging record
/////////////////////////////////////////////////////////////////
logApiCall($__ses);
