<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/28 12:34:34 $
    File Versie         : $Revision: 1.2 $

    $Log: apiAuth.php,v $
    Revision 1.2  2018/09/28 12:34:34  cvs
    call 7097

    Revision 1.1  2018/09/28 11:33:34  cvs
    call 7097


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
logApiCall(array(
             "data"   => $__ses["data"],
             "body"   => $__ses["body"]
           ));

