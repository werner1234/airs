<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/10 06:47:45 $
    File Versie         : $Revision: 1.2 $

    $Log: apiAuth.php,v $
    Revision 1.2  2018/07/10 06:47:45  cvs
    ApiKey van naar header verhuisd

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



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

//$keyValid = ($__glob["apiKey"] == $__ses["data"]["apiKey"]);

// API key doorgeven via de headers
$headers = getallheaders();

$keyValid = ($__glob["apiKey"] == $headers["apiKey"]);

if ($keyValid AND $__ses["ipChecked"])
{
  $__ses["loggedIn"] = true; // validation true
}
else
{
  $error[] = "invalid apikey ";
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
