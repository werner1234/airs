<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/04/24 14:13:04 $
    File Versie         : $Revision: 1.3 $

    $Log: apiAuth.php,v $
    Revision 1.3  2019/04/24 14:13:04  cvs
    call 7630

    Revision 1.2  2019/04/17 10:31:39  cvs
    call 7719

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/

// portefeuille url encodeing verwijderen call 7719
if (isset($__ses["data"]["portefeuille"]) )
{
  $__ses["data"]["portefeuille"] = urldecode($__ses["data"]["portefeuille"]);
}


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

$keyValid = ($__glob["apiKey"] == $__ses["data"]["apiKey"]);

/////////////////////////////////////////////////////////////////
/// APIkey checking
/////////////////////////////////////////////////////////////////
if ($keyValid AND $__ses["ipChecked"])
{
  $__ses["loggedIn"] = true; // validation true
}
else
{
  $error[] = "invalid creds";
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
/// add logging record
/////////////////////////////////////////////////////////////////
logApiCall($__ses);
