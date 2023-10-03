<?php
/*
    AE-ICT sourcemodule created 09 jul. 2021
    Author              : Chris van Santen
    Filename            : api_realtimeATT.php


*/
global $USR;
$USR = "rtatt".rand(11111,99999);
$error = array();
$portefeuille = $__ses["data"]["portefeuille"];

include_once("../../classes/HTML_rapportList.php");

$_POST = array(
  "counter" => "0",
  "posted" => "true",
  "save" => "0",
  "rapport_types" => "|ATT",
  "extra" => "",
  "actief" => "actief",
  "portefeuilleIntern" => "10",
  "metConsolidatie" => "0",
  "datum_van" => "01-01-".date("Y"),
  "datum_tot" => substr(getLaatsteValutadatum(),0,10),
  "layout" => "",
  "Portefeuille" => "{$portefeuille}",
  "modelcontrole_level" => "fonds",
  "username" => "{$USR}",
  "switchmenu" => "",
  "APIcall" => true
);
include_once "../rapportFrontofficeClientAfdrukkenHtml.php";

$list = new rapportList("htmlATT", $portefeuille);
$list->postData = $data;
$list->idField = "id";
$list->perPage = 100;

$list->addColumn("htmlATT","datum",array());
$list->addColumn("htmlATT","waardeBegin",array());
$list->addColumn("htmlATT","stortingen",array());
$list->addColumn("htmlATT","onttrekkingen",array());
$list->addColumn("htmlATT","ongerealiseerd",array());
$list->addColumn("htmlATT","opbrengsten",array());
$list->addColumn("htmlATT","kosten",array());
$list->addColumn("htmlATT","rente",array());
$list->addColumn("htmlATT","resultaatVerslagperiode",array());
$list->addColumn("htmlATT","waardeHuidige",array());
$list->addColumn("htmlATT","performance",array());
$list->addColumn("htmlATT","perfCumulatief",array());
$list->addColumn("htmlATT","specifiekeIndexPerformance",array("hideColumn"=>true));
$list->addColumn("htmlATT","specifiekeIndexVorige",array("hideColumn"=>true));
$list->addColumn("htmlATT","specifiekeIndex",array("hideColumn"=>true));
$list->addColumn("htmlATT","gerealiseerd",array("hideColumn"=>true));

$list->setupFilter('attHtmlRapport', array(
//  'groupings' => array ('hoofdcategorie', 'beleggingscategorie'),
  'sortFields' => array('datum'),
  'sortOrder' => array('ASC'),
  'hideOrderBreak' => false
));

$list->setWhere("soort = 'maand'");

$list->setSearch( null);
$list->selectPage(null);
//ad($list->getSQL());
$list->postData['allowExport'] = true;
$list->setRapportData();
$firstRow = null;
$rowArray = array();
while($row = $list->getRow())
{
  $rowArray = array();
  foreach ($row as $k=>$v)
  {
    $rowArray[$k] = $v["value"];
  }
  $resArray[] = $rowArray;
  if ( empty ($firstRow) ) {$firstRow = $rowArray;}
  $lastRow = $rowArray;
}
$started   = false;
$tot = array();
foreach($resArray as $row)
{
  $tot["result"]          = $row["ongerealiseerd"] + $row["gerealiseerd"];
  $tot["stortingen"]      += $row["stortingen"];
  $tot["onttrekkingen"]   += $row["onttrekkingen"];
}
//ad ($resArray);
$db = new DB();
$query = "DELETE FROM _htmlRapport_ATT WHERE add_user = '{$USR}'";
$db->executeQuery($query);
$output = array(
  "user"            => $USR,
  "portefeuille"    => $portefeuille,
  "beginWaarde"     => $firstRow["waardeBegin"],
  "laatsteWaarde"   => $lastRow["waardeHuidige"],
  "Stortingen"      => $tot["stortingen"],
  "Onttrekkingen"   => $tot["onttrekkingen"],
  "rapportageDatum" => $_POST["datum_tot"],
  "rendement"       => $lastRow["perfCumulatief"],
);
