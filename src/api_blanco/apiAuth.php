<?php
/*
    AE-ICT sourcemodule created 29 jan. 2020
    Author              : Chris van Santen
    Filename            : apiAuth.php

*/


// API key doorgeven via de headers
$headers = getallheaders();
//print_r(apache_request_headers());
$keyValid         = ($__glob["apiKey"] == $headers["apiKey"]);
$__ses["action"]  = $headers["action"];

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

$__ses["data"] = $__ses["body"];

/////////////////////////////////////////////////////////////////
/// add logging record
/////////////////////////////////////////////////////////////////
logApiCall(array(
             "data"    => $__ses["data"],
             "body"    => $__ses["body"],
             "headers" => $headers

           ));

