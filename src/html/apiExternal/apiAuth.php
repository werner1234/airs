<?php
/*
    AE-ICT sourcemodule created 11 jan. 2021
    Author              : Chris van Santen
    Filename            : apiAuth.php


*/

header('X-Powered-By: AE-ICT api engine');   // obsure PHP version
header('Content-Type: application/json');   // force to JSON


if (isset($__ses["data"]["portefeuille"]) )
{
  $port = urldecode($__ses["data"]["portefeuille"]);
  if (!strstr($port, ","))
  {
    $__ses["data"]["portefeuille"] = sanatizePortefeuille($port);
  }

  if (!$__ses["data"]["portefeuille"])
  {
    $error[] = "invalid portfolio";
  }

}
//if (isset($__ses["data"]["aedebug"]))
//{
//  global $__debug;
//  $__debug = $__ses["data"]["aedebug"];
//}


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

$headers = getallheaders();

$__ses["data"]["accman"] = "";

$keyValid = false;

if (is_array($__glob["apiKey"]))
{
  foreach ($__glob["apiKey"] as $accman=>$apikey)
  {

    $keyValid = ($apikey == $headers["apiKey"]);
    if ($keyValid)
    {

      $__ses["data"]["accman"] = $accman;
      break;
    }
  }
}
else  // apikey as string
{
  $keyValid = ($__glob["apiKey"] == $headers["apiKey"]);
}


if ($keyValid AND $__ses["ipChecked"])
{
  $__ses["loggedIn"] = true; // validation true
}
else
{
  $error[] = "invalid apikey ";
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

if ($requestsPerHour > $__glob["queriesPerHour"]+10)
{
  header("HTTP/1.0 404 Not Found");
  exit;
}

/////////////////////////////////////////////////////////////////
/// add logging record
/////////////////////////////////////////////////////////////////
logApiCall($__ses);

if (count($error) > 0)
{
  $output = array("errors" => $error);
  UpdateLogApiCall();
  echo toJson($output);
  exit;
}

