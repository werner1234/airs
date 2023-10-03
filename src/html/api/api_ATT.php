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

$query = "
  SELECT 
    Vermogensbeheerders.PerformanceBerekening,
    Vermogensbeheerders.Vermogensbeheerder,
    Vermogensbeheerders.Layout
  FROM 
    Portefeuilles 
  JOIN Vermogensbeheerders ON 
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder 
  WHERE 
    Portefeuilles.Portefeuille = '".$portefeuille."'";
$vdata = $db->lookupRecordByQuery($query);

$portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");
verwijderTijdelijkeTabel($portefeuille);


$query = "SELECT * FROM Fondsen WHERE Fonds ='".$portRec["SpecifiekeIndex"]."' ";
$fondsRec = $db->lookupRecordByQuery($query);

$index = new indexHerberekening();

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
//    $rapportStart = (date("Y"))."-01-01";
}
//Startdatum


if ($__ses["data"]["rapportDatum"] != "")
{
  $rapportDatum = $__ses["data"]["rapportDatum"];
}
else
{
  $rapportDatum = substr(getLaatsteValutadatum(),0,10);
}
$indexData = $index->getWaarden( $rapportStart,$rapportDatum ,$portefeuille,$portRec["SpecifiekeIndex"]);
$cumPerfArray = array();
$qPerfArray = array();
$yPerfArray = array();

include_once($__appvar["basedir"]."/classes/AE_cls_htmlColomns.php");
include_once($__appvar["basedir"]."/classes/htmlReports/htmlATT.php");
$att = new htmlATT($portefeuille);
$att->initModule();
$att->clearTable();
$specifiekeIndexVorige = 0;
$started = false;
$kTel = 0;
$vorigeMaand = 100;
if ( $vdata['PerformanceBerekening']==2 OR $vdata['PerformanceBerekening']==3)   // Modified Dietz
{
  $maandenCumulatief=array();
  $class='ATTberekening_L'.$vdata['Layout'];

  if(file_exists('../rapport/include/'.$class.'.php'))
  {
    include_once('../rapport/include/'.$class.'.php');
  }
  elseif(file_exists('../rapport/include/layout_'.$vdata['Layout'].'/'.$class.'.php'))
  {
    include_once('../rapport/include/layout_'.$vdata['Layout'].'/' . $class . '.php');
  }
  if(class_exists($class) && method_exists($class,'getPerfArray'))
  {

    $attBerekening=new $class();
    $maandenCumulatief = $attBerekening->getPerfArray($portefeuille,$rapportStart,$rapportDatum, $valuta);
  }
}

foreach($indexData as $row)
{
//    debug($row);
  $row["soort"] = "maand";
  $row["portefeuille"] = $portefeuille;


  if ( $vdata['PerformanceBerekening']==2 OR $vdata['PerformanceBerekening']==3 OR $vdata['PerformanceBerekening']==7)   // Modified Dietz
  {
    if(isset($maandenCumulatief[$row['datum']]))
    {
      $row["performance"]=$maandenCumulatief[$row['datum']]['performance'];
      $row["perfCumulatief"]=$maandenCumulatief[$row['datum']]['index'];
      $row["index"] = $row["perfCumulatief"] + 100;
    }
    else
    {

      $row["perfCumulatief"] = $index->periodePerformance($portefeuille, $rapportStart, $row['datum'], substr($portRec["Startdatum"], 0, 10));
      $row["index"] = $row["perfCumulatief"] + 100;
      $maandPerf = (($row["index"] / 100) / ($vorigeMaand / 100) - 1) * 100;
      $vorigeMaand = $row["index"];
//    $row["performanceOrg"] = $row["performance"];
      $row["performance"] = $maandPerf;
    }
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

$db = new DB();


//debug($fondsRec);

$_SESSION["htmlRapportVars"] = array(
  "portefeuille"    => $portefeuille,
  "client"          => utf8_encode($portRec["Client"]),
  "specifiekeIndex" => $portRec["SpecifiekeIndex"],
  "indexFonds"      => utf8_encode($fondsRec["Omschrijving"]),
  "start"           => $rapportStart,
  "stop"            => $rapportDatum,
  "altFonds"        => $_POST["altFonds"],
  "user"            => $USR,
);

$output["statics"] = $_SESSION["htmlRapportVars"];


$query = "SELECT * FROM `_htmlRapport_ATT` WHERE portefeuille='$portefeuille' AND add_user='$USR' ORDER BY datum";
$data = array();
$db->executeQuery($query);
$started = false;
$totalPerf = -152;
while($rec = $db->nextRecord())
{

  if (!$started AND
    $rec["perfCumulatief"]["value"] <> 0 AND
    $rec["performance"]["value"] <> 0  )
  {
    $started = true;
    $totalPerf = 0;
    $rec["specifiekeIndexPerformance"] = 0;
//    $grafDate = $listData["datum"]["value"];
  }

  if ($started )
  {

    if ($totalPerf == -152 AND $rec["datum"] != 0)
    {
      $totalPerf = $rec["specifiekeIndexPerformance"];
    }
    else
    {
      $totalPerf =  ( (( 1 + ($totalPerf/100)) * ((1 + ($rec["specifiekeIndexPerformance"]/100)))) -1 ) * 100;
    }
  }
  else
  {
    $totalPerf = 0;
    $rec["specifiekeIndexPerformance"] = 0;
  }



  $data[$rec["datum"]] = array(
     "datum"            => $rec["datum"],
     "waardeBegin"      => $rec["waardeBegin"],
     "stort_onttr"      => ($rec["stortingen"] - $rec["onttrekkingen"]),
     "resultaat"        => ($rec["resultaatVerslagperiode"] ),
     "waardeEind"       => ($rec["waardeHuidige"]),
     "performance"      => ($rec["performance"]),
     "perfCumulatief"   => ($rec["perfCumulatief"]),
     "indexPerformance" => ($rec["specifiekeIndexPerformance"]),
     "indexCumulatief"  => ($totalPerf),
  );

//  $data[$rec["datum"]] = $rec;
}
ksort($data);
$output["data"] = $data;
$query = "DELETE FROM `_htmlRapport_ATT` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
$db->executeQuery($query);
if ($__ses["action"] == "htmlrendementfull")
{


  $last = array_pop($output["data"]);
  echo json_encode(array(
    "portefeuilleStart" => substr($rapportStart,0,10),
    "performance"       => $last["perfCumulatief"]
  ));
  exit;
}

echo json_encode($output);
exit;