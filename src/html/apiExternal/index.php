<?php
/*
    AE-ICT sourcemodule created 11 jan. 2021
    Author              : Chris van Santen
    Filename            : index.php


*/

include_once "wwwvars.php";
include_once "../../config/debugSpecial.php";

global $__dbDebug;

error_reporting(0);

$p = explode("html/", getcwd());

$__appvar["basedir"] = $p[0];

$id = $__ses["id"];  // (int) van tag actionId
//ad("portefeuille: ".$__ses["data"]["portefeuille"]);
/**********************************************************************
 * tags
 *
 * tag                              functie
 * ---------------------------------------------------------------------
 * credits
 * crmalljoininfo
 * crminfobyportefeuille
 * htmlrapvermogen
 * htmlraprendement
 * htmlverdelingportefeuille
 * htmlportefeuilletransacties
 * htmlportefeuillemutaties
 * htmlRapPortefeuilleWaarde
 * portefeuillestatics
 *
 * __POST__
 * clientupdate
 * portefeuilleupdate
 * mutatieupdate
 */

$postEndPoints = array(
  "clientupdate",
  "mutatieupdate",
  "portefeuilleupdate"
);

if ($__ses["disableUpdates"] AND in_array($__ses["action"], $postEndPoints ))
{

  $output = array("errors" => "POST calls are disabled");

  UpdateLogApiCall();
  echo toJson($output);
  exit;

}

switch ($__ses["action"])
{
  case "clientupdate":
    $d = $__ses["data"];
    if (!isset($d["new"]))
    {
      $error[] = "field `new` is mandatory";
    }
    if (!isset($d["clientid"]))
    {
      $error[] = "field `clientid` is mandatory";
    }

    if (count($error) == 0)
    {
      if (addToExternQueue($d))
      {
        $output = array();
        $output["result"] = "queued";
      }
      else
      {
        $error[] = "queuing failed";
      }
    }
    break;


  case "mutatieupdate":
    $d = $__ses["data"];
    if (!isset($d["mutationId"]))
    {
      $error[] = "field `mutationId` is mandatory";
    }
    else if (strlen($d["mutationId"]) > 24)
    {
      $error[] = "field `mutationId` > 24 characters";
    }

    if (!isset($d["date"]))
    {
      $error[] = "field `date` is mandatory";
    }

  case "portefeuilleupdate":
    $d = $__ses["data"];
    if (!isset($d["new"]))
    {
      $error[] = "field `new` is mandatory";
    }

    if (!isset($d["portfolioId"]))
    {
      $error[] = "field `portfolioId` is mandatory";
    }


    if (count($error) == 0)
    {
      if (addToExternQueue($d))
      {
        $output = array();
        $output["result"] = "queued";
      }
      else
      {
        $error[] = "queuing failed";
      }
    }

    break;

  case "crmalljoininfo":
    $output = array();
    $where = array();
    $whereStr = "";

    $db = new DB();
    $db->debug = $__dbDebug;

    if ($__ses["data"]["portefeuilleOnly"] == 1)
    {
      $where[] = "`portefeuille` != ''";
    }

    if ($__ses["data"]["blancoIdOnly"] == 1)
    {
      $where[] = "CHAR_LENGTH(`blancoId`) > 2 ";
    }

    if (count($where) > 0)
    {
      $whereStr = "WHERE ".implode(" AND ", $where);
    }


    $where = ($portefeuilleOnly)?" WHERE `portefeuille` != '' ":"";
    $query = "SELECT `id`, `blancoId`, `portefeuille`, `zoekveld` FROM `CRM_naw` {$whereStr} ORDER BY `id`";

    $db->executeQuery($query);

    while ($rec = $db->nextRecord())
    {

      $out = array(
        "id"            => $rec["id"],
        "blancoId"      => $rec["blancoId"],
        "portefeuille"  => $rec["portefeuille"],
        "zoekveld"      => utf8_encode($rec["zoekveld"]),
      );
      $output[] = $out;
    }
    $result = $output;
    break;

  case "crminfobyportefeuille":
     if (!$id = findCRMidByPortefeuille($__ses["data"]["portefeuille"]))
     {
       $error[] = "portefeuille to id failed";
     }
  case "crminfobyid":
    $nFlds = array();
    $pFlds = array();
    if (isset($__ses["data"]["fields"]) )
    {
      $fData = str_replace("%2C",",",$__ses["data"]["fields"]);

      $flds = explode(",", $fData);

      if (count($flds) > 0)
      {
        foreach ($flds as $item)
        {
          if (substr(trim($item),0,2) == "p.")
          {
            $pFlds[] = sanatizeInput(substr(trim($item),2),100);
          }
          else
          {
            $nFlds[] = sanatizeInput(trim($item),100);
          }

        }
      }
      $n = apiGetCRMById($id,$nFlds);

      if (count($pFlds) > 0)
      {
        $p = apiGetPortefeuilleByPortnr($__ses["data"]["portefeuille"], $pFlds);

        $n["portFields"] = $p;
      }

      $output = array(
        "portefeuille" => $portefeuille,
        "data" => $n);
    }
    else
    {
      $output = array(
        "portefeuille" => $portefeuille,
        "data" => apiGetCRMById($id));

    }

    break;

  case "credits":
    $output = array();
    $output["yourIP"]        = $__ses["ipaddress"];
    $output["lastHour"]      = checkQueriesPerHour($__ses["ipaddress"]);
    $output["allowPerHour"]  = $__glob["queriesPerHour"];
    $output["accMan"]        = $__ses["data"]["accman"];
    $result = $output;
    break;

  case "documentcount":
    $disable_auth = true;

    include_once "api_documentCount.php";

    $result = $output;
    break;
  case "documentlinks":
    $disable_auth = true;

    include_once "api_documentlinks.php";

    $result = $output;
    break;
  case "documentpull":
    $disable_auth = true;

    include_once "api_documentPull.php";

    $result = $output;
    break;

  case "htmlportefeuilletransacties":
    $disable_auth = true;
    include_once "api_TRANS.php";
    $result = $output;
    break;

  case "htmlportefeuillemutaties":
    $disable_auth = true;
    include_once "api_MUT.php";
    $result = $output;
    break;

  case "htmlrapvermogen":
    $disable_auth = true;
    $output = array();
    include_once "../HTMLrapport/dashboard_verloopVermogen_functies.php";
    include_once("../../classes/htmlReports/htmlDashboardHelper.php");
    include_once "../rapport/rapportRekenClass.php";

    $portefeuille = $__ses["data"]["portefeuille"];

    if ($__ses["data"]["datum"] == "portStart")
    {
      $rapportStart = $portRec["Startdatum"];
    }

    if ($__ses["data"]["datum"] != "")
    {
      $rapportStart = sanatizeInput($__ses["data"]["datum"]);
    }
    else
    {
      $rapportStart = (date("Y")-1)."-01-01";
    }
    if (db2jul($rapportStart) < db2jul($portRec["Startdatum"]))
    {
      $rapportStart = $portRec["Startdatum"];
    }

    if ($__ses["data"]["rapportDatum"] != "")
    {
      $rapportStop = sanatizeInput($__ses["data"]["rapportDatum"]);
    }
    else
    {
      $rapportStop = date("Y-m-d");
    }


    $where = "";
    if ($portefeuille == "all")
    {
      if ($__debug)
      {
        $where = " LIMIT 20 ";
      }
    }
    else
    {
      $portefeuilles = explode(",", $portefeuille);

      $prts = array();
      foreach ($portefeuilles as $item)
      {
        $prts[] = sanatizeInput(trim($item));
      }
      $where = "WHERE Portefeuille IN ('".implode("','", $prts)."')";
    }

    if ($__ses["data"]["accman"] != "")
    {
      if (strlen($where) > 0)
      {
        $where .= "AND ";
      }
      else
      {
        $where = "WHERE ";
      }
      $where .= "`Portefeuilles`.`Accountmanager` = '{$__ses["data"]["accman"]}' ";
    }

    $query = "SELECT * FROM `Portefeuilles` {$where}";
    //ad($query);
    $db = new DB();
    $db->debug = $__dbDebug;
    $db->executeQuery($query);

    while ($portRec = $db->nextRecord())
    {

      $portefeuille = $portRec["Portefeuille"];
      $dash = new htmlDashboardHelper($portefeuille);
      $dash->user = "apiEng";
      $dash->getData(false,$rapportStart,$rapportStop);

      $dataset = $dash->getRecords("maand",$rapportStart,$rapportStop);

      $out = array(
        "portefeuille" => $portefeuille,
        "data" => getVerloopVermogenExternalApi($dataset, false)
      );
      $output[] = $out;
    }
    $result = $output;

    break;
  case "htmlraprendement":
    $disable_auth = true;
    include_once "api_ATT.php";

    break;

  case "htmlverdelingportefeuille":

    $disable_auth = true;


    include_once "api_VOLK.php";

    $result = $output;
    break;

  case "htmlrapportefeuillewaarde":
    $db           = new DB();
    $db->debug = $__dbDebug;
    $disable_auth = true;
    $retFields    = array(
      "portefeuille",
      "beginWaarde",
      "laatsteWaarde",
      "rendement",
      "Stortingen",
      "Onttrekkingen",
      "Opbrengsten",
      "Kosten",
      "gerealiseerd",
      "ongerealiseerd",
      "mutatieOpgelopenRente",
      "afmstdev",
      "zorgMeting",
      "saldoGeldrek",
      "rapportageDatum",
      "ptfSignMethode",
    );
    include_once("../../classes/htmlReports/htmlDashboardHelper.php");
    include_once "../rapport/rapportRekenClass.php";


    $where = "";
    if ($portefeuille == "all")
    {
      if ($__debug)
      {
        $where = " LIMIT 20 ";
      }
    }
    else
    {
      $portefeuilles = explode(",", $portefeuille);

      $prts = array();
      foreach ($portefeuilles as $item)
      {
        $prts[] = sanatizeInput(trim($item));
      }
      $where = " `laatstePortefeuilleWaarde`.`Portefeuille` IN ('".implode("','", $prts)."')";
    }

    if ($__ses["data"]["accman"] != "")
    {
      if (strlen($where) > 0)
      {
        $where .= " AND ";
      }
      $where .= " `Portefeuilles`.`Accountmanager` = '{$__ses["data"]["accman"]}' ";
    }

    if ($where != "")
    {
      $where = " WHERE ".$where;
    }

    $query = "
      SELECT 
        * 
      FROM 
        `laatstePortefeuilleWaarde` 
      LEFT JOIN 
          Portefeuilles ON
        `laatstePortefeuilleWaarde`.`Portefeuille` = `Portefeuilles`.`Portefeuille`
      {$where}";
    //ad($query);
    $db->executeQuery($query);

    $output = array();
    while ($portRec = $db->nextRecord())
    {

      $row = array(
        "portefeuille"    => $portRec["portefeuille"],
        "rapportageDatum" => $portRec["rapportageDatum"],
        "beginWaarde"     => $portRec["beginWaarde"],
        "stort-onttr"     => $portRec["Stortingen"] - $portRec["Onttrekkingen"],
        "laatsteWaarde"   => $portRec["laatsteWaarde"],
        "rendement"       => $portRec["rendement"],
      );

      $output[] = $row;
    }

    $result = $output;
    break;
  case "portefeuillestatics":

    $disable_auth = true;
    include_once "api_PORTSTAT.php";
    $result = $output;
    break;

  default:
    $error[] = "invalid action: ".$__ses["action"];

    break;
}

if (count($error) > 0)
{
  $output = array("errors" => $error);
}
UpdateLogApiCall();
header('Content-Type: application/json; charset=utf-8');
echo toJson($output);
