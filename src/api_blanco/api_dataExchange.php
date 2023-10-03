<?php
/*
    AE-ICT sourcemodule created 17 sep 2018
    Author              : Chris van Santen
    Filename            : api_dataExchange.php

    $Log: api_dataExchange.php,v $
    Revision 1.5  2020/05/25 08:46:04  cvs
    call 8380

*/

$error = array();
$data = $__ses["data"]["registration"];
$airsRec = (array)$data["airsRecord"];
$resultData = array();
$correlationId = $data["correlationId"];
unset($data["CDD"]);
unset($data["contracts"]);
unset($data["documents"]);

$ndx = -1;

foreach ($data["parties"] as $party)
{
  $np = $party["naturalPerson"];
  if ((string)$np["accountHolderName"] != "")
  {
    $ndx++;
    $resultRec[$ndx]["blancoId"] = $data["_id"];
    $resultRec[$ndx]["createdAt"] = str_replace("T"," ",substr($data["createdAt"],0,19));
    $resultRec[$ndx]["naam"] = $np["accountHolderName"];
    $resultRec[$ndx]["plaats"] = $np["address"]["city"];
    $resultRec[$ndx]["land"] = $np["address"]["country"];
    $resultRec[$ndx]["adres"] = $np["address"]["line1"];
    if ( (string)$np["address"]["line2"] != "")
    {
      $resultRec[$ndx]["adres"] .= $np["address"]["line2"];
    }
    if (strtoupper($np["address"]["country"]) == "NL" AND strlen($np["address"]["postalCode"]) == 6)
    {
      // spatie in NL postcode
      $resultRec[$ndx]["pc"] = substr($np["address"]["postalCode"],0,4)." ".substr($np["address"]["postalCode"],-2);
    }
    else
    {
      $resultRec[$ndx]["pc"] = $np["address"]["postalCode"];
    }


    $resultRec[$ndx]["verzendAdres"] = $resultRec[$ndx]["adres"];
    $resultRec[$ndx]["verzendPc"]    = $resultRec[$ndx]["pc"];

    $resultRec[$ndx]["IBAN"] = $np["bankAccount"];
    $resultRec[$ndx]["geboortedatum"] = $np["dateOfBirth"];
    $resultRec[$ndx]["email"] = $np["email"];
    $resultRec[$ndx]["voornamen"] = $np["givenNames"];
    $resultRec[$ndx]["voorletters"] = $np["initials"];
    $resultRec[$ndx]["voornamen"] = $np["givenNames"];
    $resultRec[$ndx]["achternaam"] = $np["lastName"];
    $resultRec[$ndx]["nationaliteit"] = $np["nationality"];
    $resultRec[$ndx]["BSN"] = $np["personalNumber"];
    $resultRec[$ndx]["tel1"] = $np["phone"];
    $resultRec[$ndx]["roepnaam"] = $np["preferredName"];
    $resultRec[$ndx]["geslacht"] = ($np["sex"] == "F")?"vrouw":"man";
    $resultRec[$ndx]["zoekveld"] = $resultRec[$ndx]["achternaam"].", ".$resultRec[$ndx]["voorletters"];  
  }
  else
  {
    if($resultRec[$ndx]["part_achternaam"] != "")
    {
      continue; // alleen pers 2 gebruiken voor partner
    }
    $resultRec[$ndx]["part_geboortedatum"] = $np["dateOfBirth"];
    $resultRec[$ndx]["emailPartner"] = $np["email"];
    $resultRec[$ndx]["part_voornamen"] = $np["givenNames"];
    $resultRec[$ndx]["part_voorletters"] = $np["initials"];
    $resultRec[$ndx]["part_voornamen"] = $np["givenNames"];
    $resultRec[$ndx]["part_achternaam"] = $np["lastName"];
    $resultRec[$ndx]["part_nationaliteit"] = $np["nationality"];
    $resultRec[$ndx]["part_BSN"] = $np["personalNumber"];
    $resultRec[$ndx]["tel2"] = $np["phone"];
    $resultRec[$ndx]["part_roepnaam"] = $np["preferredName"];
    $resultRec[$ndx]["part_geslacht"] = ($np["sex"] == "F")?"vrouw":"man";
  }
  


}

$db = new DB();

foreach ($resultRec as $item)
{
  $blancoId =  $item["blancoId"];
  $add_date  =  $item["createdAt"];
  $blob = json_encode($item);
  $query = "
    INSERT INTO `CRM_blanco_mutatieQueue` SET 
        `add_date`     = '$add_date',
        `add_user`     = 'blanco',
        `change_date`  = NOW(),
        `change_user`  = 'blanco',
        `jsonData`     = '{$blob}',
        `blancoId`     = '{$blancoId}',
        `verwerkt`     = 0,
        `afgewerkt`    = 0
  ";

  if (!$db->executeQuery($query))
  {
    $error[] = "insert failed for id {$blancoId}";
  }

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

  $query = "SELECT * FROM CRM_naw WHERE portefeuille = '".trim($portefeuille)."' ";

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
