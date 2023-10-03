<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/22 14:20:49 $
    File Versie         : $Revision: 1.3 $

    $Log: api_VOLK.php,v $
    Revision 1.3  2018/10/22 14:20:49  cvs
    call 7228

    Revision 1.2  2018/09/26 09:30:07  cvs
    update naar DEMO

    Revision 1.1  2018/07/11 10:01:45  cvs
    call 6783

    Revision 1.1  2018/02/01 12:55:28  cvs
    update naar airsV2



*/


$error = array();
$portefeuille = $__ses["data"]["portefeuille"];
$volgorde     = $__ses["data"]["volgorde"];
if ($__ses["data"]["rapportDatum"])
{
  $data["datum_tot"] = $__ses["data"]["rapportDatum"];
}

$USR = "api_".rand(111111,999999); // param portaal
$sessionId = rand(15,100);   // AIRS gebruikers hebben 0-10  // param portaal
$__appvar['TijdelijkeRapportageMaakUniek'] = " AND TijdelijkeRapportage.add_user = '".$USR."' AND TijdelijkeRapportage.sessionId = '".$sessionId."' ";
//////////


include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
if(trim($data["datum_tot"]) == "")
{
  $d = explode("-",substr(getLaatsteValutadatum(),0,10));
  $data["datum_tot"] = $d[2]."-".$d[1]."-".$d[0];
}
if (trim($data["datum_van"]) == "" AND trim($data["datum_tot"]) !== "" )
{
  $d = explode("-",substr($data["datum_tot"],0,10));
  $data["datum_van"] = "01-01-".$d[2];
}


$min1dag = false;
$d = explode("-",substr($data["datum_tot"],0,10));
if ( (int) $d[0] === 1 && (int) $d[1] === 1 )
{
  $min1dag = true;
}

// check begin datum rapportage!
$query = "
  SELECT Portefeuilles.Startdatum, 
    Portefeuilles.Einddatum,
    Portefeuilles.RapportageValuta, 
    Vermogensbeheerders.layout, 
    Vermogensbeheerders.Vermogensbeheerder	
  FROM 
    (Portefeuilles, Vermogensbeheerders) 
  WHERE 
    Portefeuilles.Portefeuille = '{$portefeuille}' AND 
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";

verwijderTijdelijkeTabel($portefeuille);
// asort
$db = new DB();
$dbL = new DB();
$pdata = $db->lookupRecordByQuery($query);

$rapportageDatum["a"] = jul2sql(form2jul($data['datum_van']));
$rapJul      = form2jul($data['datum_tot']);
$valutaDatum = getLaatsteValutadatum();
$valutaJul   = db2jul($valutaDatum);



$rapportValues["rapportageDatum"]["a"]  = $rapportageDatum[a];
$rapportValues["rapJul"]                = $rapJul;
$rapportValues["valutaDatum"]           = $valutaDatum;
$rapportValues["valutaJul"]             = $valutaJul;



if($rapJul > $valutaJul + 86400)
{
  $error[] =  "Fout: Er is geen of onvoldoende data om een rapportage te kunnen maken.";

}
$rapportageDatum['b'] = jul2sql($rapJul);

if(db2jul($rapportageDatum["b"]) < db2jul($pdata['Startdatum']))
{
  $rapportageDatum["b"] = $pdata["Startdatum"];
  $rapportDatum         = ($pdata["Startdatum"]);
}

if(db2jul($rapportageDatum["b"]) > db2jul($pdata["Einddatum"]))
{
  $error[] = "Fout: Deze portefeuille heeft een einddatum  (".date("d-m-Y",db2jul($pdata["Einddatum"])).")";

}

// controlleer of datum a niet groter is dan datum b!
if(db2jul($rapportageDatum["a"]) > db2jul($rapportageDatum["b"]))
{
  $error[] = "Fout: Van datum kan niet groter zijn dan  T/m datum!";
}
$julrapport   = db2jul($rapportageDatum["a"]);
$rapportMaand = date("m",$julrapport);
$rapportDag   = date("d",$julrapport);
$rapportJaar  = date("Y",$julrapport);

if (count($error) > 0)
{
  echo json_encode($error);
  exit;
}

if($rapportMaand == 1 && $rapportDag == 1)
{
  $startjaar = true;
  $extrastart = false;
}
else
{
  $startjaar = false;
  // 1 dag eraf is de startdatum!
  $julrapport = db2jul($rapportageDatum["a"]);
  $rapportageDatum["a"] = jul2sql($julrapport);

  $extrastart = mktime(0,0,0,1,1,$rapportJaar);
  if($extrastart < 	db2jul($pdata['Startdatum']))
  {
    $extrastart = $pdata['Startdatum'];
  }
  else
  {
    $extrastart = date("Y-m-d",$extrastart);
  }

}

$rapportValues["rapportageDatum"]["a"] = $rapportageDatum["a"];
$rapportValues["rapportageDatum"]["b"] = $rapportageDatum["b"];
$rapportValues["julrapport"]           = $julrapport;
$rapportValues["rapportMaand"]         = $rapportMaand;
$rapportValues["rapportDag"]           = $rapportDag;
$rapportValues["rapportJaar"]          = $rapportJaar;
$rapportValues["startjaar"]            = $startjaar;
$rapportValues["extrastart"]           = $extrastart;

$fondswaarden["b"] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum["b"],$min1dag,$pdata['RapportageValuta'],$rapportageDatum["a"]);

// eerste loop bepaald totale portefeuillwaarde in EUR
$porteuilleWaarde = 0;
for ($x=0; $x < count($fondswaarden["b"]); $x++)
{
  $rec = $fondswaarden[b][$x];
  $porteuilleWaarde += $rec["actuelePortefeuilleWaardeEuro"];
}

include_once($__appvar["basedir"]."/classes/AE_cls_htmlColomns.php");
include_once($__appvar["basedir"]."/classes/htmlReports/htmlMODEL.php");
include_once($__appvar["basedir"]."/classes/htmlReports/htmlVOLK.php");
$volk = new htmlVOLK($portefeuille);

$volk->clearTable();

// berekende velden bepalen
for ($x=0; $x < count($fondswaarden["b"]); $x++)
{
//	  debug($fondswaarden[b][$x]);
  $rec = $fondswaarden["b"][$x];
  // aandeel op totalewaarde
  $rec["aandeelOpTotaleWaarde"] = round($rec["actuelePortefeuilleWaardeEuro"]/$porteuilleWaarde * 100,2);
  $rec["portefeuille"] = $portefeuille;
  $rec["rapportDatum"] = $rapportDatum;
  if ($rec["type"] == "fondsen")
  {
    // fondsResultaat
    $fondsResultaatReken = ($rec["actuelePortefeuilleWaardeInValuta"] - $rec["beginPortefeuilleWaardeInValuta"]) * $rec["actueleValuta"];
    $rec["fondsResultaat"] = round($fondsResultaatReken, 0);
    //  valutaResultaat
    $rec["valutaResultaat"] = round($rec["actuelePortefeuilleWaardeEuro"] - $rec["beginPortefeuilleWaardeEuro"] - $fondsResultaatReken, 2);
    // resultaatInProcent
    if ( (int)$rec["beginPortefeuilleWaardeEuro"] === 0 )
    {
      $rec["resultaatInProcent"] = 0;
    }
    else
    {
      $rec["resultaatInProcent"] = round ( ( ($rec["fondsResultaat"]+$rec["valutaResultaat"])/abs($rec["beginPortefeuilleWaardeEuro"])) *100,2);
    }
  }


  // opgelopen rente uitsplitsen
  if ($rec["fonds"] <> "" AND $rec["type"] == "rente")
  {
    $rec["beleggingscategorie"] == "RENTE";
    $rec["beleggingscategorieOmschrijving"] = "Opgelopen Rente";
    $rec["beleggingscategorieVolgorde"] = "87";
  }
  $dbl = new DB();
  $query = "SELECT * FROM Fondsen WHERE Fonds = '".$rec["fonds"]."'";
  $fondsRec = $dbl->lookupRecordByQuery($query);
  $rec["ISINCode"] = $fondsRec["ISINCode"];
  $rec["rating"] = $fondsRec["rating"];

  $volk->addRecord($rec);
}

$htmlRapportVars = array(
  "portefeuille" => $portefeuille,
  "client" => $portRec["Client"],
  "specifiekeIndex" => $portRec["SpecifiekeIndex"],
  "start" => $rapportageDatum["a"],
  "stop" => $rapportageDatum["b"],

);
$output["statics"] = $htmlRapportVars;

$db = new DB();


//$query = "SELECT * FROM `_htmlRapport_VOLK` WHERE portefeuille='$portefeuille' AND add_user='$USR' ORDER BY beleggingscategorieVolgorde, beleggingscategorieOmschrijving, fonds";

if ($volgorde == "AZ")
{
  $whereVolgorde = "";
}
else
{
  $whereVolgorde = "
    regioVolgorde,
    beleggingscategorieOmschrijving, 
  ";
}
$query = "
  SELECT 
    * 
  FROM 
    `_htmlRapport_VOLK` 
  WHERE 
    portefeuille='$portefeuille' AND 
    add_user='$USR' 
  ORDER BY 
    hoofdcategorieVolgorde,
    beleggingscategorieVolgorde, 
    {$whereVolgorde}
    fondsOmschrijving";

$dataSet = array();
$db->executeQuery($query);
$output["sql"]=$query;

$query = "SELECT DISTINCT Fonds FROM doorkijk_categorieWegingenPerFonds";
$dbL->executeQuery($query);
$doorkijkArray = array();
while ($r = $dbL->nextRecord())
{
  $doorkijkArray[] = $r["Fonds"];
}

while($rec = $db->nextRecord())
{
//doorkijk query voor menukeuze..
  $doorkijk = in_array($rec["fonds"], $doorkijkArray);
  if ($rec["fonds"] == '')
  {
    $doorkijk = false;
  }


  $dataSet[] =     array(
    "aantal"                        => $rec["totaalAantal"],
    "fonds"                         => utf8_encode($rec["fondsOmschrijving"]) ,
    "valuta"                        => utf8_encode($rec["valuta"]),
    "fondskoers"                    => ($rec["actueleFonds"]),
    "kostprijs1-1"                  => ($rec["beginwaardeLopendeJaar"]),
    "waardeEUR"                     => ($rec["actuelePortefeuilleWaardeEuro"]),
    "historischeWaarde"             => ($rec["historischeWaarde"]),
    "airsFonds"                     => utf8_encode($rec["fonds"]) ,
    "fonds-valutaResultaat"         => ($rec["fondsResultaat"] + $rec["valutaResultaat"]),
    "YTDReslutaat"                  => ($rec["resultaatInProcent"]),
    "wegening"                      => ($rec["aandeelOpTotaleWaarde"]),
    "rekening"                      => ($rec["rekening"]),
    "hoofdcategorie"                => utf8_encode($rec["hoofdcategorieOmschrijving"]),
    "hoofdcategorieVolgorde"        => $rec["hoofdcategorieVolgorde"],
    "categorie"                     => utf8_encode($rec["beleggingscategorieOmschrijving"]),
    "beleggingscategorieVolgorde"   => $rec["beleggingscategorieVolgorde"],
    "regio"                         => utf8_encode($rec["Regio"]),
    "regioOmschrijving"             => utf8_encode($rec["regioOmschrijving"]),
    "regioVolgorde"                 => $rec["regioVolgorde"],
    "beleggingssector"              => utf8_encode($rec["beleggingssector"]),
    "beleggingssectorOmschrijving"  => utf8_encode($rec["beleggingssectorOmschrijving"]),
    "hoofdsector"                   => utf8_encode($rec["hoofdsector"]),
    "hoofdsectorOmschrijving"       => utf8_encode($rec["hoofdsectorOmschrijving"]),
    "hoofdsectorVolgorde"           => $rec["hoofdsectorVolgorde"],
    "beleggingssectorVolgorde"      => $rec["beleggingssectorVolgorde"],
    "doorkijk"                      => $doorkijk,
    "actuelePortefeuilleWaardeInValuta" => $rec["actuelePortefeuilleWaardeInValuta"],
    //"ISINCode"          => ($rec["ISINCode"]),
  );
}
//ksort($dataSet);
$output["data"] = $dataSet;
$query = "DELETE FROM `_htmlRapport_VOLK` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
//$db->executeQuery($query);
if (count($error) > 0)
{
  $output = array("errors" => $error);
}
$result = $output;
UpdateLogApiCall();
echo json_encode($output);

exit();



