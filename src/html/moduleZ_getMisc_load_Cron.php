<?php
/*
    AE-ICT sourcemodule created 26 okt 2018
    Author              : Chris van Santen
    Filename            : moduleZ_getMisc_load_Cron.php

*/

/*
 *   $("#loadProducts").load("moduleZ_getMisc_load.php?action=products");
      $("#loadRiskprofiles").load("moduleZ_getMisc_load.php?action=riskprofiles");
      $("#loadAdvisors").load("moduleZ_getMisc_load.php?action=advisors");
      $("#loadIntermediaries").load("moduleZ_getMisc_load.php?action=intermediaries");
      $("#loadInsurers").load("moduleZ_getMisc_load.php?action=insurers");
      $("#loadFinancialInstitutes").load("moduleZ_getMisc_load.php?action=financialinstitutes");
 */
mzClog("start import");
if ($_SERVER["SERVER_PROTOCOL"] <> "")
{
  echo "cron taak, via browser niet toegestaan";
  exit;
}
$disable_auth = true;
include_once("wwwvars.php");
mzClog("incl wwwvars");
include_once ("moduleZ_functions.php");
mzClog("incl functions");
$modules = array(
  "products",
  "riskprofiles",
  "advisors",
  "intermediaries",
  "insurers",
  "financialinstitutes",
);

$tmpl = new AE_template();
$cfg = new AE_config();
$db = new DB();

mzClog("init done");
foreach ($modules as $module)
{
  mzClog("module $module");

  $kpl = new AIRS_koppelingen();
  $kpl->setModule($module);

  $result =  mzApiGET($module);
  mzClog("result: ".$result);
  $result = (array) json_decode($result);

  if ($module == "financialinstitutes" OR
    $module == "insurers" )
  {
    $queryTemplate = "
    INSERT INTO 
      `CRM_selectievelden` 
    SET 
      add_user='$USR', 
      add_date=NOW(),
      change_user='$USR', 
      change_date=NOW(), 
      module = '{module}', 
      waarde ='{waarde}', 
      omschrijving='{omschrijving}', 
      extra='{extra}'  ";

    $tmpl->loadTemplateFromString($queryTemplate,"CRM_selectievelden");

    if ($module == "insurers")
    {
      $query = "DELETE FROM  `CRM_selectievelden` WHERE module = 'Insurers' ";
    }
    else
    {
      $query = "DELETE FROM  `CRM_selectievelden` WHERE module = 'FinInstitutes' ";
    }
    $db->executeQuery($query);

    foreach ($result as $item)
    {
      $item = (array)$item;
      if (trim($item["description"]) == "")
      {
        continue; // lege regels overslaan
      }
      $extra = "";

      switch ($module)
      {
        case "insurers":
          $kpl->setAirsTable("CRM_selectievelden");
          $airsDescLength = 50;
          $extra = array(
            "module" => "Insurers",
          );

          break;
        case "financialinstitutes":
          $kpl->setAirsTable("CRM_selectievelden");
          $airsDescLength = 50;
          $extra = array(
            "module" => "FinInstitutes",
          );
          break;
      }

      $data = array(
        "externId"          => $item["id"],
        "externDescription" => mysql_real_escape_string($item["description"]),
        "externExtra"       => serialize($extra),
        "airsDescription"   => substr($item["description"], 0, $airsDescLength),
      );
      $changed = $kpl->addItem($data);

      $query = $tmpl->parseBlock("CRM_selectievelden", array(
        "module" => $extra["module"],
        "waarde" => mysql_real_escape_string($data["airsDescription"]),
        "omschrijving" => mysql_real_escape_string($data["externDescription"]),
        "extra" => $data["externId"],
      ));
      $db->executeQuery($query);
    }
  }
  else
  {
    foreach ($result as $item)
    {
      $item = (array) $item;
      if (trim($item["description"]) == "")
      {
        continue; // lege regels overslaan
      }
      $extra = "";

      switch ($module)
      {
        case "intermediaries":
          $kpl->setAirsTable("Remisiers");
          $airsDescLength = 15;
          $extra = array(
            "Remisier" => substr($item["description"],0,15),
          );
          break;
        case "riskprofiles":
          $kpl->setAirsTable("Risicoklassen");
          $airsDescLength = 50;
          $extra = array();
          break;
        case "advisors":
          $kpl->setAirsTable("Accountmanagers");
          $airsDescLength = 50;
          $parts = explode(" ",$item["description"]);
          $last  = end($parts);
          $initals = strtoupper($parts[0][0].substr($parts[0],-1).$last[0].substr($last,-1));
          $extra = array(
            "naam" => strtoupper($parts[0][0].substr($parts[0],-1).$last[0].substr($last,-1)),
          );
          break;
        case "insurers":
          $kpl->setAirsTable("CRM_selectievelden");
          $airsDescLength = 40;
          $extra = array(
          );
          break;
        case "financialinstitutes":
          $kpl->setAirsTable("CRM_selectievelden");
          $airsDescLength = 40;
          $extra = array(
          );
          break;
        case "products":
          $kpl->setAirsTable("SoortOvereenkomsten");
          $airsDescLength = 30;
          $extra = array(
            "allow_secondary_owner"   => ($item["allow_secondary_owner"] == true)?1:0,
            "allow_own_deposit"       => ($item["allow_own_deposit"] == true)?1:0,
            "allow_periodic_deposit"  => ($item["allow_periodic_deposit"] == true)?1:0,
            "allow_disbursement"      => ($item["allow_disbursement"] == true)?1:0,
            "active"                  => ($item["active"] == true)?1:0,
          );
          break;
        default:
          break;
      }

      $description = ($item["name"] != "")?$item["name"]:$item["description"];
      $data = array(
        "externId"          => $item["id"],
        "externDescription" => $description,
        "externExtra"       => serialize($extra),
        "airsDescription"   => substr($description,0,$airsDescLength),
      );
      $changed = $kpl->addItem($data);

      if ($changed != -1 AND count($changed > 1))
      {
        switch ($module)
        {
          case "products":
            $obj = new SoortOvereenkomsten();
            $edObj = new editObject($obj);
//        $edObj->verzendDebug = true;
            $obj->getByField("SoortOvereenkomst", $changed["airsDescription"]);
            $rawData = $obj->data["fields"];
            $editData["id"] = $rawData["id"]["value"];
            $editData["key_SoortOvereenkomst"] = 1;
            $editData["SoortOvereenkomst"] = $changed["changes"]["airsDescription"]["new"];
            if ($editData["id"] > 0)
            {
              $edObj->controller("update",$editData);
            }
            break;
          case "riskprofiles":
            $obj = new Risicoklassen();
            $edObj = new editObject($obj);
            $obj->getByField("Risicoklasse", $changed["airsDescription"]);
            $rawData = $obj->data["fields"];
            $editData["id"] = $rawData["id"]["value"];
            $editData["key_Risicoklasse"] = 1;
            $editData["Risicoklasse"] = $changed["changes"]["airsDescription"]["new"];
            if ($editData["id"] > 0)
            {
              $edObj->controller("update",$editData);
            }
            break;
          case "intermediaries":
            $obj = new Remisiers();
            $edObj = new editObject($obj);
            $obj->getByField("Remisier", $changed["airsDescription"]);
            $rawData = $obj->data["fields"];
            $editData["id"] = $rawData["id"]["value"];
            $editData["key_Remisier"] = 1;
            $editData["Remisier"] = $changed["changes"]["airsDescription"]["new"];
            if ($editData["id"] > 0)
            {
              $edObj->controller("update",$editData);
            }
            break;
          case "advisors":
            $obj = new Accountmanager();
            $edObj = new editObject($obj);
            $obj->getByField("Accountmanager", $changed["airsDescription"]);
            $rawData = $obj->data["fields"];
            $editData["id"] = $rawData["id"]["value"];
            $editData["key_Accountmanager"] = 1;
            $editData["Accountmanager"] = $changed["changes"]["airsDescription"]["new"];
            if ($editData["id"] > 0)
            {
              $edObj->controller("update",$editData);
            }
            break;
        }
      }
      else if ($changed == -1)  // add new record
      {
        switch ($module)
        {
          case "products":
            if (trim($data["airsDescription"]) == "")
            {
              break;  // lege velden niet opslaan
            }
            $query = "
          INSERT INTO 
            `SoortOvereenkomsten` 
          SET 
            add_user='$USR', 
            add_date=NOW(),
            change_user='$USR', 
            change_date=NOW(), 
            SoortOvereenkomst='" . mysql_real_escape_string($data["airsDescription"]) . "'";
            $db->executeQuery($query);
            $query = "
          INSERT INTO 
            `KeuzePerVermogensbeheerder` 
          SET 
            add_user='$USR', 
            add_date=NOW(),
            change_user='$USR', 
            change_date=NOW(), 
            categorie='SoortOvereenkomsten', 
            waarde='" . $data["airsDescription"] . "', 
            vermogensbeheerder='VRY' ";
            $db->executeQuery($query);
            break;
          case "riskprofiles":
            if (trim($data["airsDescription"]) == "")
            {
              break;  // lege velden niet opslaan
            }
            $query = "
          INSERT INTO 
            `Risicoklassen` 
          SET 
            add_user='$USR', 
            add_date=NOW(),
            change_user='$USR', 
            change_date=NOW(), 
            Vermogensbeheerder ='VRY', 
            Risicoklasse='" . mysql_real_escape_string($data["airsDescription"]) . "'  ";
            $db->executeQuery($query);
            break;
          case "intermediaries":
            if (trim($data["airsDescription"]) == "")
            {
              break;  // lege velden niet opslaan
            }
            $query = "
          INSERT INTO 
            `Remisiers` 
          SET 
            add_user='$USR', 
            add_date=NOW(),
            change_user='$USR', 
            change_date=NOW(), 
            Vermogensbeheerder ='VRY', 
            Remisier='".substr($data["airsDescription"],0,15)."', 
            Naam='".mysql_real_escape_string($data["airsDescription"])."'
        ";
            $db->executeQuery($query);
            break;
          case "advisors":
            if (trim($data["airsDescription"]) == "")
            {
              break;  // lege velden niet opslaan
            }
            $parts = explode(" ",$data["airsDescription"]);
            $last  = end($parts);
            $initals = strtoupper($parts[0][0].substr($parts[0],-1).$last[0].substr($last,-1));

            $query = "
          INSERT INTO 
            `Accountmanagers` 
          SET 
            add_user='$USR', 
            add_date=NOW(),
            change_user='$USR', 
            change_date=NOW(), 
            Vermogensbeheerder = 'VRY', 
            Accountmanager ='".$initals."', 
            Naam='".mysql_real_escape_string($data["airsDescription"])."'";
            $db->executeQuery($query);
            break;
        }
      }
    }
  }
}

function mzClog($txt)
{
   echo date("H:i:s")."=>".$txt."\n";
}


