<?php
/*
    AE-ICT sourcemodule created 17 sep 2018
    Author              : Chris van Santen
    Filename            : api_dataExchange.php

*/


/// GROENSYS            => AIRS
$mapping = array(
  "naam"                            => "naam",
  "aanhef"                          => "verzendAanhef",
  "email"                           => "email",
  "postAdres_straatEnHuisNummer"    => "adres",
  "postAdres_postcode"              => "pc",
  "postAdres_plaats"                => "plaats",
  "postAdres_land"                  => "land",
  "verzendAdres_straatEnHuisNummer" => "verzendAdres",
  "verzendAdres_postcode"           => "verzendPc",
  "verzendAdres_plaats"             => "verzendPlaats",
  "verzendAdres_land"               => "verzendLand",
);

$data = $__ses["body"];
$airsRec = (array)$data["airsRecord"];
$resultData = array();
$correlationId = $data["correlationId"];
//debug($data);
//debug($airsRec);


$groensysArray = $airsRec;
unset($groensysArray["adressen"]);
$db = new DB();

foreach ($airsRec["adressen"] as $item)
{
  $item = (array)$item;
  if ($item["adresType"] == "postAdres")
  {
    $groensysArray["postAdres_straatEnHuisNummer"]    = (string) $item["straatEnHuisNummer"];
    $groensysArray["postAdres_postcode"]              = (string) $item["postcode"];
    $groensysArray["postAdres_plaats"]                = (string) $item["plaats"];
    $groensysArray["postAdres_land"]                  = (string) $item["land"];

  }
  else
  {
    $groensysArray["verzendAdres_straatEnHuisNummer"] = (string) $item["straatEnHuisNummer"];
    $groensysArray["verzendAdres_postcode"]           = (string) $item["postcode"];
    $groensysArray["verzendAdres_plaats"]             = (string) $item["plaats"];
    $groensysArray["verzendAdres_land"]               = (string) $item["land"];
  }
}

if ( $groensysArray["postAdres_straatEnHuisNummer"]   != ""  AND
     $groensysArray["verzendAdres_straatEnHuisNummer"] == "" )
{
  $groensysArray["verzendAdres_straatEnHuisNummer"] = (string) $item["straatEnHuisNummer"];
  $groensysArray["verzendAdres_postcode"]           = (string) $item["postcode"];
  $groensysArray["verzendAdres_plaats"]             = (string) $item["plaats"];
  $groensysArray["verzendAdres_land"]               = (string) $item["land"];
}


switch (strtolower($data["operation"]))
{
  case "insert":
    $crmRec = lookupCMR($airsRec["portefeuilleNummer"],$correlationId);
    if ($crmRec["result"] == 0)
    {
      $resultData["resultCode"] = "-10";
      $resultData["result"] = "record bestaat al";
    }
    else
    {
      $queryData = buildQuery($groensysArray,"add");
      $queryDataOuput = implode(" , \n",$queryData[1]);
      if ($queryData[0])
      {
        $query = "
           INSERT INTO CRM_naw SET
           $queryDataOuput
           , add_date = NOW()
           , add_user = 'groensys'
           , change_date = NOW()
           , change_user = 'groensys'
           , zoekveld = '{$airsRec["naam"]}'
           , memo='".date("j-n-Y G:i")." aangemaakt vanuit Groensys\n'
           , externID = '$correlationId'
           , portefeuille = '".$airsRec["portefeuilleNummer"]."'
           , aktief = 1
           , debiteur = 1 ";
//        debug($query);
        if ($db->executeQuery($query))
        {
          $resultData["resultCode"] = "0";
          $resultData["result"] = "OK - record toegevoegd";
          updateTrackAndTrace($db->last_id(), "naam","added",$airsRec["naam"]);
        }
        else
        {
          $resultData["resultCode"] = "-100";
          $resultData["result"] = "FOUT - ".mysql_error();
        }
      }
    }
    break;
  case "update":
    $crmRec = lookupCMR($airsRec["portefeuilleNummer"],$correlationId);
    if ($crmRec["result"] == 0)
    {
      $queryData = buildQuery($groensysArray);
      $queryDataOuput = implode(" , \n",$queryData[1]);

      $query = "
        UPDATE CRM_naw SET
          $queryDataOuput
          , change_date = NOW()
          , change_user = 'groensys'
          , memo='".date("j-n-Y G:i")." bijgewerkt vanuit Groensys\n".$crmRec["record"]["memo"]."'
          WHERE externID = '{$correlationId}'";
//      debug($query);
      if ($db->executeQuery($query))
      {
        for ($y=0; $y < count($queryData[2]); $y++)
        {
          $t = $queryData[2][$y][0];
          $v = $queryData[2][$y][1];
          updateTrackAndTrace($crmRec["record"]["id"], $t, $crmRec["record"][$t],$v);
        }
        $resultData["resultCode"] = "0";
        $resultData["result"] = "OK - record bijgewerkt";
      }
      else
      {
        $resultData["resultCode"] = "-100";
        $resultData["result"] = "FOUT - ".mysql_error();
      }
    }
    else
    {
      $resultData["resultCode"] = "-10";
      $resultData["result"] = "geen passend record voor update";
    }
    break;
  case "delete":
    $crmRec = lookupCMR($airsRec["portefeuilleNummer"],$correlationId);
    if ($crmRec["result"] == 0)
    {
      $query = "
        UPDATE CRM_naw SET
          aktief = 0,
          memo='".date("j-n-Y G:i")." verwijdert in Groensys\n".$crmRec["record"]["memo"]."'
        WHERE externID = '{$correlationId}'";
//      debug($query);
      if ($db->executeQuery($query))
      {
        $resultData["resultCode"] = "0";
        $resultData["result"] = "OK - record op inaktief gezet";
        updateTrackAndTrace($crmRec["record"]["id"], "aktief", $crmRec["record"]["aktief"], 0);
      }
      else
      {
        $resultData["resultCode"] = "-100";
        $resultData["result"] = "FOUT - ".mysql_error();
      }
    }
    else
    {
      $resultData["resultCode"] = "-10";
      $resultData["result"] = "geen record voor delete";
    }
    break;
  default:
    $error[] = "invalid operation: ".$data["operation"];
    $resultData["resultCode"] = "-999";
    $resultData["result"] = "invalid operation: ".$data["operation"];
}


function buildQuery($airsRec,$action="u")
{
  global $mapping;
  $db = new DB();
  $error  = array();
  $ttData = array();
  $data   = array();
  $query  = "SHOW COLUMNS FROM CRM_naw;";
  $db->executeQuery($query);
  while ($fldRec = $db->nextRecord())
  {
    $AIRSfieldNames[] = $fldRec["Field"];
  }

  foreach ($airsRec as $key => $value)
  {
    if ($mapping[$key] <> "")
    {
      if ($mapping[$key] == "naam")
      {
        $parts = explode("\n",str_replace("\r","",$value));
        $data[] = " `naam` = '".mysql_escape_string( trim($parts[0]) )."'";
        $data[] = " `naam1` = '".mysql_escape_string( trim($parts[1]) )."'";
      }
      else
      {
        $data[] = " `".$mapping[$key]."` = '".mysql_escape_string($value)."'";
      }

      $ttData[] = array($key, $value);
    }
    else
    {
      $error[] = $key;
    }

  }

  if (count($data) < 1 )
    return array(false,$error);
  else
    return array(true, $data, $ttData);
}


function updateTrackAndTrace($id, $field, $old, $new)
{
  $db = new DB();
  $query  = "INSERT INTO trackAndTrace SET ";
  $query .= " `tabel` = 'CRM_naw', ";
  $query .= " `recordId` = '$id', ";
  $query .= " `veld` = '$field', ";
  $query .= " `oudeWaarde` = '$old', ";
  $query .= " `nieuweWaarde` = '$new', ";
  $query .= " `add_date` = NOW(), ";
  $query .= " `add_user` = 'groensys' ";
  return $db->executeQuery($query);
}

function lookupCMR($portefeuille, $externID)
{
  $db = new DB();
  if (trim($externID) == "" OR trim($portefeuille) == "" )
  {
    $out["result"] = -1;
    $out["msg"]    = "Onvoldoende gegevens";
    return $out;
  }

  $query = "SELECT * FROM CRM_naw WHERE portefeuille = '".trim($portefeuille)."' AND externID = '".trim($externID)."' ";

  if ($CRMrec = $db->lookupRecordByQuery($query))
  {
    $out["result"] = 0;
    $out["msg"]    = "portefeuille and externID gevonden";
    $out["record"] = $CRMrec;
  }
  else
  {
    $query = "SELECT * FROM CRM_naw WHERE portefeuille = '".trim($portefeuille)."' ";
    $db->SQL($query);
    if ($CRMrec = $db->lookupRecord())
    {
      $out["result"] = -1;
      $out["msg"]    = "portefeuille gevonden, geen geldig externID";
      $out["record"] = $CRMrec;
    }
    else
    {
      $out["result"] = -1;
      $out["msg"]    = "geen geldige portefeuille voor match gevonden";
    }
  }
  return $out;
}
