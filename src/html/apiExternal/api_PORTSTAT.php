<?php
/*
    AE-ICT sourcemodule created 14 nov. 2022
    Author              : Chris van Santen
    Filename            : api_PORTSTAT.php
*/

global $__dbDebug, $__ses;

$portefeuille = $__ses["data"]["portefeuille"];
if (strstr($portefeuille, ","))
{
  $error = "invalid input multiple portfolios not allowed";
}

$USR       = "api_".rand(111111,999999); // param portaal
$sessionId = rand(15,100);   // AIRS gebruikers hebben 0-10  // param portaal
$statics   = array();

if (!$error)
{
  $db         = new DB();
  $db->debug  = $__dbDebug;

  $query = "
  SELECT
    `Portefeuille`,
    `Vermogensbeheerder`,
    `Accountmanager`,
    `tweedeAanspreekpunt` AS TweedeAanspreekpunt,
    `Client`,
    `Depotbank`,
    `Startdatum`,
    `Einddatum`,
    `ModelPortefeuille`,
    `RapportageValuta`,
    `Risicoklasse` AS Risicoprofiel,
    `SpecifiekeIndex`,
    `SoortOvereenkomst`,
    `Taal`
  FROM 
    `Portefeuilles`
  WHERE
    `Portefeuille` = '{$portefeuille}'
    ";

  $statics = $db->lookupRecordByQuery($query);
}

$results[] = array(
    "portefeuille" => $portefeuille,
    "statics"      => $statics
  );


if (count($error) > 0)
{
  $output["errors"] = $error;
}
else
{
  $output["results"] = $results;
}

echo json_encode($output);

exit();
