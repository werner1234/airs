<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/07/10 08:52:21 $
    File Versie         : $Revision: 1.10 $

    $Log: moduleZ_getMisc_load.php,v $
    Revision 1.10  2019/07/10 08:52:21  cvs
    no message

    Revision 1.9  2018/12/14 08:32:04  cvs
    call 7410

    Revision 1.8  2018/10/19 07:06:43  cvs
    call 7175

    Revision 1.7  2018/10/19 07:04:18  cvs
    call 7175

    Revision 1.6  2018/10/08 06:23:13  cvs
    call 7175, bevindingen 5-10

    Revision 1.5  2018/09/14 09:38:13  cvs
    Naar VRY omgeving ter TEST

    Revision 1.4  2018/09/07 10:12:34  cvs
    commit voor robert call 6989

    Revision 1.3  2018/07/02 08:08:25  cvs
    call 6709

    Revision 1.2  2018/07/02 07:49:17  cvs
    call 6709

    Revision 1.1  2018/06/18 06:59:57  cvs
    update naar VRY omgeving

    Revision 1.1  2018/05/25 09:34:52  cvs
    25-5-2018


*/

include_once("wwwvars.php");
include_once ("moduleZ_functions.php");

$tmpl = new AE_template();
$cfg = new AE_config();
$db = new DB();
$kpl = new AIRS_koppelingen();

$module = $_GET["action"];

$kpl->setModule($module);

$result =  mzApiGET($module);
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
            waarde='" . mysql_real_escape_string($data["airsDescription"]) . "', 
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

echo "inlezen voltooid";

