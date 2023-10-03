<?php
/*
    AE-ICT sourcemodule created 26 feb. 2021
    Author              : Chris van Santen
    Filename            : api_MUT.php
*/


$error = array();
$portefeuille = $__ses["data"]["portefeuille"];


if ($__ses["data"]["rapportDatum"])
{
  $data["datum_tot"] = $__ses["data"]["rapportDatum"];
}
if ($__ses["data"]["startDatum"])
{
  $data["datum_van"] = $__ses["data"]["startDatum"];
}

$USR = "api_".rand(111111,999999); // param portaal
$sessionId = rand(15,100);   // AIRS gebruikers hebben 0-10  // param portaal
$__appvar['TijdelijkeRapportageMaakUniek'] = " AND TijdelijkeRapportage.add_user = '".$USR."' AND TijdelijkeRapportage.sessionId = '".$sessionId."' ";


include_once($__appvar["basedir"]."/classes/AE_cls_htmlColomns.php");

include_once($__appvar["basedir"]."/classes/htmlReports/htmlMUT.php");

initModule();

$db = new DB();
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
  $d = explode("-",substr($data["datum_tot"],0,10));
  print_r($d);
  $data["datum_van"] = "01-01-".$d[2];
}

$rapportStart = $data["datum_van"];
$rapportDatum = $data["datum_tot"];

$mut = new htmlMUT($portefeuille);


//$mut->initModule();

$mut->clearTable();
$portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");


//aetodo: wat is koersquery??

$query = "
  SELECT 
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
    Rekeningen.Valuta as rekValuta
  FROM 
    (Rekeningmutaties, Rekeningen,  Grootboekrekeningen)
	LEFT JOIN Fondsen ON
		Fondsen.Fonds = Rekeningmutaties.Fonds
  WHERE
    Rekeningmutaties.Rekening = Rekeningen.Rekening      AND 
    Rekeningen.Portefeuille = '".$portefeuille."'    AND 
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
      Rekeningmutaties.Boekdatum;
  
  ";
//print_r($query);
$db->executeQuery($query);


while ($mutaties = $db->nextRecord())
{
  if ($mutaties["Grootboekrekening"] == "VERM")
  {
    continue;
  }
  $v = array();
  $v['portefeuille']        = $portefeuille;
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

  $mut->addRecord($v);
}

if($data['consolidatie']==1)
{
  verwijderConsolidatie($portefeuille);
}


$query = "
  SELECT 
    * 
  FROM 
    `_htmlRapport_MUT` 
  WHERE 
    portefeuille='$portefeuille' AND 
    add_user='$USR' 
  ORDER BY 
    id";

$db->executeQuery($query);

$dataSet = array();
$notEncodeArray = array(
  "change_user",
  "change_date",
  "add_user",
  "add_date",
  "id",
);

while($rec = $db->nextRecord())
{
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
//ksort($dataSet);


//$query = "DELETE FROM `_htmlRapport_MUT` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
//$db->executeQuery($query);

$output = array();
$output["statics"] = array(
  "portefeuille" => $portefeuille,
  "client"       => $portRec["Client"],
  "start"        => $rapportStart,
  "stop"         => $rapportDatum,
  "rapportType"  => "TRANS"
);

$output["data"] = $dataSet;


if($data['consolidatie']==1)
{
  verwijderConsolidatie($portefeuille);
}

function _getLaatsteValutadatum()
{
  $q = "SELECT Datum FROM Valutakoersen WHERE Valuta = 'EUR' ORDER BY Datum DESC LIMIT 1;";
  $DB = new DB();
  $DB->SQL($q);
  $DB->Query();
  $data = $DB->NextRecord();

  return $data['Datum'];
}

function initModule()
{
  include_once("../../classes/AE_cls_SQLman.php");
  $tableName = "_htmlRapport_MUT";
  $tst = new SQLman();
  $tst->tableExist($tableName,true);
  $tst->changeField($tableName,"Boekdatum",array("Type"=>"date","Null"=>false));
  $tst->changeField($tableName,"Aantal",array("Type"=>"double","Null"=>false));
  $tst->changeField($tableName,'Omschrijving',array("Type"=>"varchar(80)","Null"=>false));
  $tst->changeField($tableName,'Grootboekrekening',array("Type"=>"varchar(20)","Null"=>false));
  $tst->changeField($tableName,'Afschriftnummer',array("Type"=>"varchar(10)","Null"=>false));
  $tst->changeField($tableName,'gbOmschrijving',array("Type"=>"varchar(40)","Null"=>false));
  $tst->changeField($tableName,'Debet',array("Type"=>"double","Null"=>false));
  $tst->changeField($tableName,'Credit',array("Type"=>"double","Null"=>false));
  $tst->changeField($tableName,'Rekening',array("Type"=>"varchar(28)","Null"=>false));
  $tst->changeField($tableName,'Valutakoers',array("Type"=>"double","Null"=>false));
  $tst->changeField($tableName,'Opbrengst',array("Type"=>"tinyint","Null"=>false));
  $tst->changeField($tableName,'Kosten',array("Type"=>"tinyint","Null"=>false));
  $tst->changeField($tableName,'Afdrukvolgorde',array("Type"=>"int","Null"=>false));
  $tst->changeField($tableName,'portefeuille',array("Type"=>"varchar(24)","Null"=>false));
  $tst->changeField($tableName,'fonds',array("Type"=>"varchar(60)","Null"=>false));

  $tst->changeField($tableName,'fondsOmschrijving',array("Type"=>"varchar(64)","Null"=>false));
  $tst->changeField($tableName,'Valuta',array("Type"=>"varchar(4)","Null"=>false));
  $tst->changeField($tableName,'Bedrag',array("Type"=>"double","Null"=>false));
  $tst->changeField($tableName,'rekValuta',array("Type"=>"varchar(5)","Null"=>false));
  $tst->changeField($tableName,'bedragVV',array("Type"=>"double","Null"=>false));
  $tst->changeField($tableName,'bedragEUR',array("Type"=>"double","Null"=>false));
  $tst->changeField($tableName,'mutationId',array("Type"=>"varchar(60)","Null"=>false));

}