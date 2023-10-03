<?php
include_once("../config/btr_config.php");
include_once("wwwvars.php");

function getUserData()
{
  ob_start();
  $_GET['action'] = 'edit';
  $_GET['id'] = $_SESSION["usersession"]["gebruiker"]["id"];
  $skipGebruikersbeheer = true;
  include_once('gebruikerEdit.php');
  ob_end_clean();
  $userData = array();
  $ignoreFields = array(
    "wachtwoord",
    "id",
    "paspoortnummer"
  );

  foreach ($editObject->object->data["fields"] as $key => $record) {
    if ((in_array(strtolower($key), $ignoreFields))) {
      continue;
    }

    if ($key === "CRM_relatieSoorten") {
      $value = array();
      foreach ($opties as $veld => $omschrijving) {
        if (in_array($veld, $huidigeOpties))
          $value[] = array("label" => $veld, "value" => $omschrijving);
      }
      $userData[$key] = $value;
      continue;
    }

    $value = $record["value"];
    if (isSerialized($value)) {
      $value = unserialize($value);
      foreach ($value as $listKey => $listValue) {
        if ($listValue === 0 || $listValue === 1)
          $value[$listKey] = $listValue === 1;
      }
    }

    if ( isset ($record["form_options"][$value]) ) {
      $value = $record["form_options"][$value];
    }

    if ($record["form_type"] === "checkbox") {
      $value = $value == 1;
    }

    if ($record['form_visible'] || $record['list_visible']) {
      $userData[$key] = $value;
    }

    $userData[$key] =  utf8_encode($value);
  }
  return $userData;
}
function encodeJsonDataToNFW(&$item)
{
  $item = mb_convert_encoding($item, 'UTF-8', "windows-1252");
}
$tmpAppvar = $__appvar;
unset($tmpAppvar['superuser']);
unset($tmpAppvar['superuserLogin']);
unset($tmpAppvar['superuserWachtwoord']);
unset($tmpAppvar['SMSapiKey']);
unset($tmpAppvar['classdir']);
unset($tmpAppvar['databaseObjects']);
unset($tmpAppvar['basedir']);
unset($tmpAppvar['htmldir']);
unset($tmpAppvar['importdata']);
unset($tmpAppvar['rapportdir']);
unset($tmpAppvar['recordsdir']);
array_walk_recursive($tmpAppvar, 'encodeJsonDataToNFW');

$tmpFix = isset($__FIX) ? $__FIX : null;

if ($tmpFix) {
    unset($tmpAppvar['wachtwoord']);
}

$status = array(
  'nawOnly'       => (GetModuleAccess('alleenNAW') == 1),
  'userdata'      => getUserData(),
  'fix'           => $tmpFix,
  'appvar'        => $tmpAppvar
);

header("Content-Type:application/json");
echo json_encode($status);