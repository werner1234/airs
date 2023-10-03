<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/13 12:37:00 $
    File Versie         : $Revision: 1.3 $

    $Log: api_ATT.php,v $
    Revision 1.3  2019/02/13 12:37:00  cvs
    call 7567

    Revision 1.2  2018/09/26 09:30:07  cvs
    update naar DEMO

    Revision 1.1  2018/02/01 12:55:28  cvs
    update naar airsV2



*/

global $__dbDebug;
global $__debug;

$portefeuille = $__ses["data"]["portefeuille"];
$USR = "api_".rand(111111,999999); // param portaal
$sessionId = rand(15,100);   // AIRS gebruikers hebben 0-10  // param portaal
$__appvar['TijdelijkeRapportageMaakUniek'] = " AND TijdelijkeRapportage.add_user = '".$USR."' AND TijdelijkeRapportage.sessionId = '".$sessionId."' ";
//////////


unset($_SESSION["htmlATT"]);
include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

$db = new DB();
$query = "SELECT * FROM Portefeuilles WHERE `Portefeuille` = '".trim(substr($portefeuille,0,24))."'";
$portRec = $db->lookupRecordByQuery($query);

$index = new indexHerberekening();

if (strtolower($__ses["data"]["datum"]) == "portstart")
{
  $rapportStart = substr($portRec["Startdatum"],0,10);
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
  $rapportStart = substr($portRec["Startdatum"],0,10);
//    $rapportStart = (date("Y"))."-01-01";
}
//Startdatum

$rapportDatum = substr(getLaatsteValutadatum(),0,10);

$db = new DB();
$db->debug = $__dbDebug;
$db2 = new DB();
$db2->debug = $__dbDebug;
$where = "";
$output = array();
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
  $where = "WHERE Portefeuilles.Portefeuille IN ('".implode("','", $prts)."')";
}

if ($__ses["data"]["accman"] != "")
{
  if (strlen($where) > 0)
  {
    $where .= "AND ";
  }
  $where .= " `Accountmanager` = '{$__ses["data"]["accman"]}' ";
}

$query = "
  SELECT 
    Portefeuilles.*,  
    Vermogensbeheerders.PerformanceBerekening	
  FROM 
    Portefeuilles 
  JOIN Vermogensbeheerders ON 
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  {$where}
";

//ad($query);
include_once($__appvar["basedir"]."/classes/AE_cls_htmlColomns.php");
include_once($__appvar["basedir"]."/classes/htmlReports/htmlATT.php");

$db->executeQuery($query);

while ($portRec = $db->nextRecord())
{
  $portefeuille = $portRec["Portefeuille"];
  verwijderTijdelijkeTabel($portefeuille);

  $indexData = $index->getWaarden( $rapportStart,$rapportDatum ,$portefeuille,$portRec["SpecifiekeIndex"]);
  $cumPerfArray = array();
  $qPerfArray = array();
  $yPerfArray = array();


  $att = new htmlATT($portefeuille);
  $att->initModule();
  $att->clearTable();
  $specifiekeIndexVorige = 0;
  $started = false;
  $kTel = 0;
  $vorigeMaand = 100;
  foreach($indexData as $row)
  {
//    debug($row);
    $row["soort"] = "maand";
    $row["portefeuille"] = $portefeuille;


    if ( $portRec['PerformanceBerekening']==2 )   // Modified Dietz
    {
      $row["perfCumulatief"] = $index->periodePerformance($portefeuille,$rapportStart,$row['datum'],substr($portRec["Startdatum"],0,10));
      $row["index"]=$row["perfCumulatief"]+100;
      $maandPerf=(($row["index"]/100)/($vorigeMaand/100)-1)*100;
      $vorigeMaand=$row["index"];
//    $row["performanceOrg"] = $row["performance"];
      $row["performance"] =$maandPerf;
    }
    else // standaard Perf
    {
      $row["perfCumulatief"] = $row["index"] - 100;
    }

    if ($row["perfCumulatief"] <> 0 OR $started)
    {
      $started = true;
      $row["specifiekeIndexVorige"] = $row["specifiekeIndexPerformance"]; //- $specifiekeIndexVorige;
      $specifiekeIndexVorige = $row["specifiekeIndexPerformance"];
    }
    else
    {
      $row["specifiekeIndexVorige"] = 0;
    }

    $cumPerfArray[$row["datum"]] = $row["perfCumulatief"];

    $cumPerfArray[$row["datum"]] = $row["perfCumulatief"];
//  debug($row);
    $att->addRecord($row);

  }
  $query = "SELECT * FROM `_htmlRapport_ATT` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
  $data = array();
  $db2->executeQuery($query);
  while($rec = $db2->nextRecord())
  {
    $data[$rec["datum"]] = array(
      "datum"            => $rec["datum"],
      "waardeBegin"      => $rec["waardeBegin"],
      "stort_onttr"      => ($rec["stortingen"] - $rec["onttrekkingen"]),
      "resultaat"        => ($rec["resultaatVerslagperiode"] ),
      "waardeEind"       => ($rec["waardeHuidige"]),
      "performance"      => ($rec["performance"]),
      "perfCumulatief"   => ($rec["perfCumulatief"]),
    );

//  $data[$rec["datum"]] = $rec;
  }
  ksort($data);
  $output[] = array(
    "portefeuille" => $portefeuille,
    "statics" => array(
      "client" => $portRec["Client"],
      "specifiekeIndex" => $portRec["SpecifiekeIndex"],
      "start" => $rapportStart,
      "stop" => $rapportDatum,
      "altFonds" => $_POST["altFonds"],
      "user" => $USR),
    "data" => $data
  );
  $query = "DELETE FROM `_htmlRapport_ATT` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
  $db2->executeQuery($query);

}










echo json_encode($output);
exit;