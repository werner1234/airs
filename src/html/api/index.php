<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/08 11:20:08 $
    File Versie         : $Revision: 1.12 $

    $Log: index.php,v $
20210315 naar RVV call 9309



*/


include_once "wwwvars.php";
include_once "../../config/debugSpecial.php";
include_once "../../classes/AE_cls_ApplicatieVertaling.php";
include_once "../../config/applicatieVertaling.php";
include_once "../../classes/AIRS_vragen_helper.php";
include_once("../../classes/AE_cls_Morningstar.php");


$ms = new AE_cls_Morningstar();

error_reporting(0);

$startTime = microtime();

$p = explode("html/", getcwd());

$__appvar["basedir"] = $p[0];

$id = $__ses["id"];  // (int) van tag actionId

/**********************************************************************
 * tags
 *
 * tag                              functie
 * ---------------------------------------------------------------------
 * crmnawmut                        plaatst mutaties in de tabel CRM_mutatieQueue
 * crminfobyportefeuille            zoekt a.d.h. van portefeuille het CRM_id een geeft deze door aan tag "crminfobyid"
 * crminfobyid                      geeft opgegeven velden terug behorende bij een CRM_id
 * credits                          geeft IP, connecties v/h laatste uur en aantal toegestaan
 * vragenlijstselectie              geeft vragenlijst vragen terug incl id
 * vragengetvragen                  geeft vragen behorende bij een vragenlijst_id
 * vragenpostantwoorden             plaatst de gegeven antwoorden in de tabel
 * vragengetingevuldbyportefeuille  geeft reeds ingevulde id's terug
 * htmlrapasset                     geeft dataset voor HTMLrapport ASSET
 * htmlrapvermogen                  geeft dataset voor HTMLrapport verloop vermogen
 * htmlrapportefeuillewaarde        geeft dataset van laatste portefeuillewaarde
 * htmlraprendement                 geeft dataset van ATT rapport YTD
 * htmlverdelingportefeuille        geeft dataset van VOLK
 * htmlinfotransacties              geeft dataset van transacties voor portefeuille/fonds
 * htmlinforekening                 geeft dataset van rekeningmutaties
 * htmlinfodivcoup
 * htmlinfofondskoers
 * htmlinfodoorkijk
 * htmlportefeuilletransacties
 * htmlportefeuillemutaties
 * portefeuillestatics              geeft het portefeuilleRecord terug
 * vragenlijstoverzichtbyportefeuille
 * htmlinfofondsdetails
 * cmsstatics
 */

switch ($__ses["action"])
{

  case "crmnawmut":

    $d = $__ses["data"];
    $crmId = findCRMidByPortefeuille($d["portefeuille"]);
    $query = "
      INSERT INTO CRM_mutatieQueue SET 
          `add_date`     = NOW()
        , `add_user`     = 'apiEng'
        , `change_date`  = NOW()
        , `change_user`  = 'apiEng'
        , `portefeuille` = '".$d["portefeuille"]."'
        , `type`         = '".$d["type"]."'
        , `veld`         = '".$d["field"]."'
        , `wasWaarde`    = '".$d["org"]."'
        , `wordtWaarde`  = '".$d["new"]."'
        , `ip`           = '".$d["ip"]."'
        , `CRM_id`       = '".$crmId."'
      ";
    $db = new DB();
    if ($db->executeQuery($query))
    {
      $output = array();
      $output["result"] = "queued";
    }
    else
    {
      $error[] = "add to CRM queue failed";
    }
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
            $pFlds[] = substr(trim($item),2);
          }
          else
          {
            $nFlds[] = trim($item);
          }

        }
      }

      $n = apiGetCRMById($id,$nFlds);
//      print_r($__ses["data"]["portefeuille"]);
      if (count($pFlds) > 0)
      {
        $p = apiGetPortefeuilleByPortnr($__ses["data"]["portefeuille"], $pFlds);
//        print_r($p);
        $n["portFields"] = $p;
      }

      $output = $n;
    }
    else
    {
      $output = apiGetCRMById($id);
    }

    break;
  case "credits":
    $output = array();
    $output["yourIP"]        = $__ses["ipaddress"];
    $output["lastHour"]      = checkQueriesPerHour($__ses["ipaddress"]);
    $output["allowPerHour"]  = $__glob["queriesPerHour"];

    $result = $output;
    break;
  case "vragenlijstoverzichtbyportefeuille":
    if (!$id = findCRMidByPortefeuille($__ses["data"]["portefeuille"]))
    {
      $error[] = "portefeuille to id failed";
    }
    $output = array();
    $query = "
    SELECT
      id,
      omschrijving,
      portaalStatus
    FROM
      VragenLijstenPerRelatie
    WHERE 
      zichtbaarInPortaal = 1 AND
      nawId = $id
    ORDER BY 
      add_date";
    $db = new DB();
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $vragen[] = $rec;
    }
    $output["vragenlijst"] = $vragen;
    $output["results"]     = count($output["vragenlijst"]);
    $result = $output;
    break;
  case "vragenlijstselectie":
    $output = array();
    $query = "SELECT * FROM `VragenVragenlijsten`";
    $db = new DB();
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $output["vragen"][$rec[id]] = $rec["omschrijving"];
    }
    $output["results"] = count($output["vragen"]);
    $result = $output;
    break;
  case "vragengetvragen":
    $output = array();
    $db     = new DB();
    $db2    = new DB();
    $query = "SELECT * FROM `VragenLijstenPerRelatie` WHERE id = $id";
    $vragenlijst = $db->lookupRecordByQuery($query);
    $query  = "SELECT * FROM `VragenVragen` WHERE vragenlijstId = ".$vragenlijst["vragenLijstId"]." AND offline = 0 ORDER BY volgorde";

    $db->executeQuery($query);
    $output["vragenLijstId"] = $id;
    $output["vragenLijst"]   = $vragenlijst["omschrijving"];
    while ($rec = $db->nextRecord())
    {
      $antwoordArray = array();
      $query = "SELECT * FROM `VragenAntwoorden` WHERE vraagId = ".$rec["id"];
      $db2->executeQuery($query);
      while ($aRec = $db2->nextRecord())
      {
        $antwoordArray[] = array(

          "id"       => $aRec["id"],
          "antwoord" => $aRec["omschrijving"],
          "punten"   => $aRec["punten"]
        );
      }

      $query = "
      SELECT 
        id,
        antwoordId,
        antwoordOpen
      FROM 
        VragenIngevuld 
      WHERE 
        vraagId        ='".$rec['id']."' AND 
        crmRef_id      ='".$id."'";

      $ingevuldRec = $db2->lookupRecordByQuery($query);
//debug($ingevuldRec);
      if ( trim($ingevuldRec["antwoordOpen"]) == "")
      {
        $ingevuld = (int)$ingevuldRec["antwoordId"];
      }
      else
      {
        $ingevuld = $ingevuldRec["antwoordOpen"];
      }

      $output["vragen"][$rec["vraagNummer"]] = array(
        "vraagId"      => $rec["id"],
        "omschrijving" => $rec["omschrijving"],
        "vraag"        => $rec["vraag"],
        "factor"       => $rec["factor"],
        "antwoorden"   => $antwoordArray,
        "ingevuld"     => $ingevuld
      );
    }

    $result = $output;

    break;
  case "vragenpostantwoorden":
    $db = new DB();
    if (!$id = findCRMidByPortefeuille($__ses["data"]["portefeuille"]))
    {
      $error[] = "portefeuille to id failed";
    }
    $d = $__ses["data"];

    if (!$VragenLijstRec = getVragenLijstPerRelatieById($__ses["data"]["vragenlijstId"]))
    {
      $error[] = "VragenLijst not found";
    }
    $vrgHelper = new AIRS_vragen_helper($__ses["data"]["vragenlijstId"]);


    $raw = explode("|",$__ses["data"]["antwoorden"]);

    foreach ($raw as $item)
    {
      $antw = explode(":=",str_replace("',", "",substr($item,6)));  // bij laatste record ', verwijderen
      $vrgHelper->updateIngevuld($antw[0], $antw[1]);
    }
    $vrgHelper->saveIngevuld();
    if (count($error) == 0)
    {
      $output['result'] = 'queued';
      $query = "
      UPDATE 
        VragenLijstenPerRelatie 
      SET 
        portaalDatumIngevuld = NOW(), 
        portaalStatus = 'ingevuld',
        log = concat('".date("d-m-Y H:i")." ingevuld via portaal','\n',log) 
      WHERE 
        id = ".$__ses["data"]["vragenlijstId"];
      $db->executeQuery($query);
    }

    $result = $output;
    break;
  case "vragengetingevuldbyportefeuille":
    $db = new DB();
    if (!$id = findCRMidByPortefeuille($__ses["data"]["portefeuille"]))
    {
      $error[] = "portefeuille to id failed";
    }
    $query = "SELECT DISTINCT vragenlijstId FROM VragenIngevuld WHERE  `relatieId`     = '".$id."'";
    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {
      $ingevuld[] = $rec["vragenlijstId"];
    }
    $output["vragenlijstId"] = $ingevuld;
    $result = $output;
    break;
  case "htmlrapasset":
    $disable_auth = true;
    include_once "../HTMLrapport/dashboard_assetVerdeling_functies.php";
    $portefeuille = $__ses["data"]["portefeuille"];
    $output = getASSETvalues(true);

    $output = str_replace("&#9729;","<i class='fa fa-chart-pie'></i>",$output);

    $db = new DB();
    $query = "SELECT * FROM laatstePortefeuilleWaarde WHERE portefeuille = '".$__ses["data"]["portefeuille"]."'";
    $rec = $db->lookupRecordByQuery($query);
    $output["rapportageDatum"] = $rec["rapportageDatum"];


    $query = "SELECT Portefeuilles.Risicoklasse FROM Portefeuilles WHERE portefeuille = '".$__ses["data"]["portefeuille"]."'";
    $rec = $db->lookupRecordByQuery($query);
    $output["risicoProfiel"] = $rec["Risicoklasse"];

    $result = $output;
    break;
  case "htmlrapassethc":
    $disable_auth = true;
    include_once "../HTMLrapport/dashboard_assetVerdeling_functies.php";
    $portefeuille = $__ses["data"]["portefeuille"];
    $output = getASSETHCvalues(true);

    $output = str_replace("&#9729;","<i class='fa fa-chart-pie'></i>",$output);

    $db = new DB();
    $query = "SELECT * FROM laatstePortefeuilleWaarde WHERE portefeuille = '".$__ses["data"]["portefeuille"]."'";
    $rec = $db->lookupRecordByQuery($query);
    $output["rapportageDatum"] = $rec["rapportageDatum"];

    $query = "SELECT Portefeuilles.Risicoklasse FROM Portefeuilles WHERE portefeuille = '".$__ses["data"]["portefeuille"]."'";
    $rec = $db->lookupRecordByQuery($query);
    $output["risicoProfiel"] = $rec["Risicoklasse"];

    $result = $output;
    break;
  case "htmlrapvermogen":
    $disable_auth = true;

    include_once "../HTMLrapport/dashboard_verloopVermogen_functies.php";
    include_once("../../classes/htmlReports/htmlDashboardHelper.php");
    include_once "../rapport/rapportRekenClass.php";

    $portefeuille = $__ses["data"]["portefeuille"];
    $db = new DB();
    $portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");


    $dash = new htmlDashboardHelper($portefeuille);
    if ($__ses["data"]["datum"] == "portStart")
    {
      $rapportStart = $portRec["Startdatum"];
    }


    if ($__ses["data"]["datum"] != "")
    {
      $rapportStart = $__ses["data"]["datum"];
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
      $rapportStop = $__ses["data"]["rapportDatum"];
    }
    else
    {
      $rapportStop = date("Y-m-d");
    }



    $dash->user = "apiEng";
    $dash->getData(false,$rapportStart,$rapportStop);

    $dataset = $dash->getRecords("maand",$rapportStart,$rapportStop);

    $out = getVerloopVermogen($dataset, true);

    $output = $out;

    $result = $output;

    break;
  case "htmlraprendement":
    $disable_auth = true;
    include_once "api_ATT.php";
    $error = array();
    break;
  case "htmlrendementfull":
    $disable_auth = true;
    $__ses["data"]["datum"] = "portStart";
    include_once "api_ATT.php";


    $error = array();
    break;
  case "htmlverdelingportefeuille":
    $disable_auth = true;
    $error = array();
    include_once "api_VOLK.php";
    $result = $output;
    break;
  case "htmlportefeuilletransacties":
    $disable_auth = true;
    $error = array();
    include_once "api_TRANS.php";
    $result = $output;
    break;

   case "htmlportefeuillemutaties":
    $disable_auth = true;
    $error = array();
    include_once "api_MUT.php";
    $result = $output;
    break;

  case "htmlinfofondsdetails":
    $disable_auth = true;

    include_once "api_info_fondsDetails.php";
    $error = array();
    $result = $output;
    break;

  case "htmlinforekening":
    $disable_auth = true;
    include_once "api_info_rekeningmutaties.php";
    $error = array();
    $result = $output;
    break;
  case "htmlinfotransacties":
    $disable_auth = true;
    include_once "api_info_transacties.php";
    $error = array();
    $result = $output;
    break;
  case "htmlinfodivcoup":
    $disable_auth = true;
    include_once "api_info_divCoup.php";
    $error = array();
    break;
  case "htmlinfofondskoers":
    $disable_auth = true;
    include_once "api_info_fondsKoers.php";
    $error = array();
    break;
  case "htmlinfodoorkijk":
    $disable_auth = true;

    include_once "api_info_doorkijk.php";
    $error = array();
    $result = $output;
    break;
  case "htmlrapportefeuillewaarde":
    $db     = new DB();
    $disable_auth = true;

    if ($__ses["data"]["rt"] == "1")
    {
      include_once "api_realtimeATT.php";
    }
    else
    {
      include_once("../../classes/htmlReports/htmlDashboardHelper.php");
      include_once "../rapport/rapportRekenClass.php";
      $query = "SELECT * FROM laatstePortefeuilleWaarde WHERE portefeuille = '".$__ses["data"]["portefeuille"]."'";
      $output = $db->lookupRecordByQuery($query);
    }

    $result = $output;
    break;

  case "portefeuillestatics":
    $disable_auth = true;
    include_once "api_portStatics.php";
    $result = $output;
    break;

  case "cmsstatics":
    $disable_auth = true;
    include_once "api_cmsStatics.php";
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
  case "pdfrecent":
    $disable_auth = true;

    include_once "api_pdfRecent.php";

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
