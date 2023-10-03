<?php
/*
    AE-ICT sourcemodule created 04 jun. 2021
    Author              : Chris van Santen
    Filename            : api_documentlinks.php


*/



$portefeuille = sanatizePortefeuille($__ses["data"]["portefeuille"]);
$rel_id       = (int)$__ses["data"]["rel_id"];

$whereArray = array();
if ($__ses["data"]["categorie"] != "")
{
  $cat = sanatizeCategorie($__ses["data"]["categorie"]);
  $whereArray[] = "`categorie` = '{$cat}'";
}


//$_DB_resources[1]['server'] = "update.airs.nl";
//$_DB_resources[1]['user']   = "chris123";
//$_DB_resources[1]['passwd'] = "4191vj(U4)";
//$_DB_resources[1]['db']     = "airs_ano";

$db = new DB();

$query = "
SELECT 
  *
FROM
   `CRM_naw`
WHERE
  `portefeuille` = '{$portefeuille}' AND 
  `id` = {$rel_id}
";

if (!$prtRec = $db->lookupRecordByQuery($query))
{
  $error[] = "Portefeuille combinatie onbekend";
//  $error[] = $query;
}
else
{
  if (count($whereArray) > 0)
  {
    $extraWhere = " AND ".implode(" AND ", $whereArray);
  }
  $query = "
  SELECT 
    * 
  FROM
    `dd_reference`
  WHERE
   `module` = 'CRM_naw' AND 
   `module_id` = '{$prtRec["id"]}'
  {$extraWhere}
  ORDER BY
    categorie, add_date DESC
  ";
//  ad($query);
  $julOffset = 1577833200;
  $listing = array();
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $listing[] = array(
      "pull_id"     => $rec["id"],
      "category"    => $rec["categorie"],
      "description" => $rec["description"],
      "filename"    => $rec["filename"],
      "add_date"    => $rec["add_date"],
      "doctok"      => encodeJul()

    );
  }
}

$output[] = array(
  "portefeuille"  => $portefeuille,
  "rel_id"        => $rel_id,
  "items"         => count($listing),
  "listing"       => $listing
);




