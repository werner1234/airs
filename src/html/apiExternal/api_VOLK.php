<?php
/*
    AE-ICT sourcemodule created 05 mrt. 2021
    Author              : Chris van Santen
    Filename            : api_VOLK.php


*/

global $__dbDebug, $__ses;

$portefeuille = $__ses["data"]["portefeuille"];

if ($__ses["data"]["rapportDatum"])
{
  $data["datum_tot"] = sanatizeInput($__ses["data"]["rapportDatum"]);
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

$where = "";
$limit = "";
$output = array();
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
    $prts[] = sanatizeInput(trim($item));
  }
  $where = " Portefeuilles.Portefeuille IN ('".implode("','", $prts)."') AND ";
}

//ad($__ses["data"]);
if ($__ses["data"]["accman"] != "")
{
  $where .= " `Portefeuilles`.`Accountmanager` = '{$__ses["data"]["accman"]}' AND ";
}

// check begin datum rapportage!
$query = "
  SELECT 
    Portefeuilles.Portefeuille,     
    Portefeuilles.Client,     
    Portefeuilles.SpecifiekeIndex, 
    Portefeuilles.Startdatum, 
    Portefeuilles.Einddatum,
    Portefeuilles.RapportageValuta, 
    Vermogensbeheerders.layout, 
    Vermogensbeheerders.Vermogensbeheerder	
  FROM 
    (Portefeuilles, Vermogensbeheerders) 
  WHERE 
    {$where}
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder 
    {$limit}";

//ad($query);
// asort
$db = new DB();
$db->debug = $__dbDebug;
$db2 = new DB();
$db2->debug = $__dbDebug;

$valutaDatum = getLaatsteValutadatum();
$rapJul      = form2jul($data['datum_tot']);
$valutaJul   = db2jul($valutaDatum);

$db->executeQuery($query);
while ($pdata = $db->nextRecord())
{
  $portefeuille    = $pdata["Portefeuille"];
  $client          = $pdata["Client"];
  $specifiekeIndex = $pdata["SpecifiekeIndex"];
  verwijderTijdelijkeTabel($portefeuille);
  $rapportageDatum["a"] = jul2sql(form2jul($data['datum_van']));
  $rapportValues["rapportageDatum"]["a"]  = $rapportageDatum[a];
  $rapportValues["rapJul"]                = $rapJul;
  $rapportValues["valutaDatum"]           = $valutaDatum;
  $rapportValues["valutaJul"]             = $valutaJul;

  if($rapJul > $valutaJul + 86400)
  {
    $error[] =  "Fout: {$portefeuille} Er is geen of onvoldoende data om een rapportage te kunnen maken.";
    continue;
  }
  $rapportageDatum['b'] = jul2sql($rapJul);

  if(db2jul($rapportageDatum["b"]) < db2jul($pdata['Startdatum']))
  {
    $rapportageDatum["b"] = $pdata["Startdatum"];
    $rapportDatum         = ($pdata["Startdatum"]);
  }

  if(db2jul($rapportageDatum["b"]) > db2jul($pdata["Einddatum"]))
  {
    $error[] = "Fout: {$portefeuille} Deze portefeuille heeft een einddatum  (".date("d-m-Y",db2jul($pdata["Einddatum"])).")";
    continue;
  }

// controlleer of datum a niet groter is dan datum b!
  if(db2jul($rapportageDatum["a"]) > db2jul($rapportageDatum["b"]))
  {
    $error[] = "Fout: {$portefeuille} Van datum kan niet groter zijn dan  T/m datum!";
    continue;
  }
  $julrapport   = db2jul($rapportageDatum["a"]);
  $rapportMaand = date("m",$julrapport);
  $rapportDag   = date("d",$julrapport);
  $rapportJaar  = date("Y",$julrapport);

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
    $db->debug = $__dbDebug;
    $query = "SELECT * FROM Fondsen WHERE Fonds = '".$rec["fonds"]."'";
    $fondsRec = $dbl->lookupRecordByQuery($query);
    $rec["ISINCode"] = $fondsRec["ISINCode"];
    $rec["rating"] = $fondsRec["rating"];

    $volk->addRecord($rec);
  }

  $statics = array(
    "portefeuille" => $portefeuille,
    "client" => $client,
    "specifiekeIndex" => $specifiekeIndex,
    "start" => $rapportageDatum["a"],
    "stop" => $rapportageDatum["b"],

  );



//$query = "SELECT * FROM `_htmlRapport_VOLK` WHERE portefeuille='$portefeuille' AND add_user='$USR' ORDER BY beleggingscategorieVolgorde, beleggingscategorieOmschrijving, fonds";
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
    regioVolgorde,
    beleggingscategorieOmschrijving, 
    fondsOmschrijving";

  $dataSet = array();
  $db2->executeQuery($query);
//$output["sql"]=$query;
  while($rec = $db2->nextRecord())
  {

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
      "categorie"                     => utf8_encode($rec["beleggingscategorieOmschrijving"]),
      "regio"                         => utf8_encode($rec["Regio"]),
      "regioOmschrijving"             => utf8_encode($rec["regioOmschrijving"]),
      "regioVolgorde"                 => $rec["regioVolgorde"],
      "beleggingssector"              => utf8_encode($rec["beleggingssector"]),
      "beleggingssectorOmschrijving"  => utf8_encode($rec["beleggingssectorOmschrijving"]),
      "hoofdsector"                   => utf8_encode($rec["hoofdsector"]),
      "hoofdsectorOmschrijving"       => utf8_encode($rec["hoofdsectorOmschrijving"]),
      "hoofdsectorVolgorde"           => $rec["hoofdsectorVolgorde"],
      "beleggingssectorVolgorde"      => $rec["beleggingssectorVolgorde"],
      //"ISINCode"          => ($rec["ISINCode"]),
    );
  }

  $query = "DELETE FROM `_htmlRapport_VOLK` WHERE portefeuille='$portefeuille' AND add_user='$USR'";
  $db2->executeQuery($query);

  $results[] = array(
    "portefeuille" => $portefeuille,
    "statics"      => $statics,
    "data" => $dataSet
  );
}

if (count($error) > 0)
{
  $output["errors"] = $error;
}

//ksort($dataSet);
$output["results"] = $results;


echo json_encode($output);

exit();



