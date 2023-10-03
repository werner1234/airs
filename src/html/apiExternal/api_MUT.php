<?php
/*
    AE-ICT sourcemodule created 26 feb. 2021
    Author              : Chris van Santen
    Filename            : api_MUT.php


*/

global $__dbDebug;


$portefeuille = $__ses["data"]["portefeuille"];


if ($__ses["data"]["rapportDatum"])
{
  $data["datum_tot"] = sanatizeInput($__ses["data"]["rapportDatum"]);
}
if ($__ses["data"]["startDatum"])
{
  $data["datum_van"] = sanatizeInput($__ses["data"]["startDatum"]);
}

$USR = "api_".rand(111111,999999); // param portaal
$sessionId = rand(15,100);   // AIRS gebruikers hebben 0-10  // param portaal
$__appvar['TijdelijkeRapportageMaakUniek'] = " AND TijdelijkeRapportage.add_user = '".$USR."' AND TijdelijkeRapportage.sessionId = '".$sessionId."' ";


include_once($__appvar["basedir"]."/classes/AE_cls_htmlColomns.php");

include_once($__appvar["basedir"]."/classes/htmlReports/htmlMUT.php");

$db = new DB();
$db->debug = $__dbDebug;
$portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");

if ($data["datum_van"] == "portStart")
{
  $data["datum_van"] = $portRec["Startdatum"];
}

if(trim($data["datum_tot"]) == "")
{
  $d = explode("-",substr(_getLaatsteValutadatum(),0,10));
//print_r($d);
  $data["datum_tot"] = substr(_getLaatsteValutadatum(),0,10);
}

if (trim($data["datum_van"]) == "" AND trim($data["datum_tot"]) !== "" )
{
  $data["datum_van"] = date("Y")."-01-01";
}

$rapportStart = $data["datum_van"];
$rapportDatum = $data["datum_tot"];

$mut = new htmlMUT($portefeuille);


//$mut->initModule();

$mut->clearTable();
$portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");

$limit = "";
$where = "";
$prts = array();
if ($portefeuille == "all")
{
  if ($__debug)
  {
    $limit = " LIMIT 20 ";
  }
}
else
{
  $portefeuilles = explode(",", $portefeuille);

  $prts = array();
  foreach ($portefeuilles as $item)
  {
    $prts[] = trim($item);
  }
  $where = " Rekeningen.Portefeuille IN ('".implode("','", $prts)."') AND ";
  $where2 = " portefeuille IN ('".implode("','", $prts)."') AND ";
}

//ad($__ses["data"]);
if ($__ses["data"]["accman"] != "")
{
  $where .= " `Portefeuilles`.`Accountmanager` = '{$__ses["data"]["accman"]}' AND ";
}


$query = "
  SELECT 
    Rekeningen.Portefeuille,
    Rekeningmutaties.Boekdatum, 
    Rekeningmutaties.Omschrijving ,
    ABS(Rekeningmutaties.Aantal) AS Aantal, 
    Rekeningmutaties.Debet ".$koersQuery." as Debet, 
    Rekeningmutaties.Credit ".$koersQuery." as Credit, 
    Rekeningmutaties.Valutakoers, 
    Rekeningmutaties.Rekening, 
    Rekeningmutaties.Grootboekrekening, 
    Rekeningmutaties.Afschriftnummer, 
    Rekeningmutaties.Fonds,
    Rekeningmutaties.Bedrag,
    Rekeningmutaties.Valuta,
    Fondsen.Omschrijving as FondsOms,
    Grootboekrekeningen.Omschrijving AS gbOmschrijving, 
    Grootboekrekeningen.Opbrengst, 
    Grootboekrekeningen.Kosten, 
    Grootboekrekeningen.Afdrukvolgorde,
    Rekeningen.Valuta as rekValuta,
    Rekeningmutaties.bankTransactieId as mutationId
  FROM 
    (Rekeningmutaties, Rekeningen,  Grootboekrekeningen)
	LEFT JOIN Fondsen ON
		Fondsen.Fonds = Rekeningmutaties.Fonds
	LEFT JOIN Portefeuilles ON
	  Rekeningen.Portefeuille = Portefeuilles.Portefeuille	
  WHERE
    Rekeningmutaties.Rekening = Rekeningen.Rekening      AND 
    {$where} 
    Rekeningmutaties.Verwerkt = '1'                      AND 
    Rekeningmutaties.Boekdatum > '".$rapportStart."'  AND 
    Rekeningmutaties.Boekdatum <= '".$rapportDatum."' ".$extraquery."  AND 
    Grootboekrekeningen.Afdrukvolgorde IS NOT NULL AND 
    Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND 
    ( 
       Grootboekrekeningen.Kosten = '1'       OR 
       Grootboekrekeningen.Opbrengst = '1'    OR 
       Grootboekrekeningen.Onttrekking = '1'  OR 
       Grootboekrekeningen.Storting = '1'     OR 
       Grootboekrekeningen.Kruispost = '1'
    ) 
    ORDER BY 
      Grootboekrekeningen.Afdrukvolgorde, 
      Rekeningmutaties.Boekdatum
    {$limit}  
  
  ";
//($query);
$db->executeQuery($query);


while ($mutaties = $db->nextRecord())
{
  $v = array();
  $v['portefeuille']        = $mutaties["Portefeuille"];
  $v["Boekdatum"]           = $mutaties["Boekdatum"];
  $v['Omschrijving']        = $mutaties["Omschrijving"];
  $v['Aantal']              = $mutaties["Aantal"];
  $v['Debet']               = $mutaties["Debet"];
  $v['Credit']              = $mutaties["Credit"];
  $v['Valutakoers']         = $mutaties["Valutakoers"];
  $v['Rekening']            = $mutaties["Rekening"];
  $v['Grootboekrekening']   = $mutaties["Grootboekrekening"];
  $v['Afschriftnummer']     = $mutaties["Afschriftnummer"];
  $v['gbOmschrijving']      = $mutaties["gbOmschrijving"];
  $v['Opbrengst']           = $mutaties["Opbrengst"];
  $v['Kosten']              = $mutaties["Kosten"];
  $v['Afdrukvolgorde']      = $mutaties["Afdrukvolgorde"];
  $v['fonds']               = $mutaties["Fonds"];
  $v['fondsOmschrijving']   = $mutaties["FondsOms"];
  $v['Bedrag']              = $mutaties["Bedrag"];
  $v['Valuta']              = $mutaties["Valuta"];
  $v['rekValuta']           = $mutaties["rekValuta"];
  $v['bedragVV']            = $mutaties["Credit"]-$mutaties["Debet"];
  $v['bedragEUR']           = ($mutaties["Credit"]-$mutaties["Debet"]) * $mutaties["Valutakoers"];
  $v['mutationId']          = $mutaties["mutationId"];

  $mut->addRecord($v);
}

if($data['consolidatie']==1)
{
  verwijderConsolidatie($portefeuille);
}
//print_r($prts);

$query = "
  SELECT 
    * 
  FROM 
    `_htmlRapport_MUT` 
  WHERE 
    {$where2}
    add_user='$USR' 
  ORDER BY 
    portefeuille, id";

//print_r($query);
$db->executeQuery($query);

$dataSet = array();
$notEncodeArray = array(
  "change_user",
  "change_date",
  "add_user",
  "add_date",
  "id",
);
$output = array();
$prevPortefeuille = "";
while($rec = $db->nextRecord())
{
  $portefeuille = $rec["portefeuille"];
  if ($prevPortefeuille != $portefeuille)
  {

    $output[$portefeuille]["statics"] = array(
      "portefeuille" => $portefeuille,
      "start"        => $rapportStart,
      "stop"         => $rapportDatum

    );
    if ($prevPortefeuille != "")
    {
      $output[$prevPortefeuille]["data"] = $dataSet;
    }
    $dataSet = array();
    $prevPortefeuille = $portefeuille;
  }

  $data = array();
  foreach($rec as $k=>$v)
  {
    if (in_array($k, $notEncodeArray))
    {
      $data[$k] = $v;
    }
    else
    {
      $data[$k] = utf8_encode($v);
    }

  }

  $dataSet[] = $data;


}
$output[$prevPortefeuille]["data"] = $dataSet;
//ksort($dataSet);


//$query = "DELETE FROM `_htmlRapport_MUT` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
//$db->executeQuery($query);







if($data['consolidatie']==1)
{
  verwijderConsolidatie($portefeuille);
}

function _getLaatsteValutadatum()
{
  global $__dbDebug;
  $q = "SELECT Datum FROM Valutakoersen WHERE Valuta = 'EUR' ORDER BY Datum DESC LIMIT 1;";
  $DB = new DB();
  $DB->debug = $__dbDebug;
  $DB->SQL($q);
  $DB->Query();
  $data = $DB->NextRecord();

  return $data['Datum'];
}