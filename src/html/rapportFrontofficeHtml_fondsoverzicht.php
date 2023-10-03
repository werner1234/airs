<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/05/01 13:50:27 $
 		File Versie					: $Revision: 1.9 $

 		$Log: rapportFrontofficeHtml_fondsoverzicht.php,v $
 		Revision 1.9  2020/05/01 13:50:27  rm
 		8603
 		
 		Revision 1.8  2017/01/18 08:19:15  rm
 		Html rapportage
 		
 		Revision 1.7  2017/01/16 15:48:28  cvs
 		call 5583
 		
 		Revision 1.6  2017/01/11 15:21:53  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/01/10 09:12:12  cvs
 		call 4830 eerste commit
 		
 		Revision 1.4  2017/01/10 09:10:01  rm
 		Datum goed zetten
 		
 		Revision 1.3  2017/01/10 08:28:50  cvs
 		call 4830 eerste commit
 		
 		Revision 1.2  2013/05/12 11:17:29  rvv
 		*** empty log message ***

 		Revision 1.1  2012/09/01 14:26:24  rvv
 		*** empty log message ***


 		*/

include_once "wwwvars.php";
include_once "../classes/HTML_fondsOverzichtGenereer.php";

$arg = array_merge($_GET, $_POST);

if (

      !isset($arg["fonds"]) OR
      !isset($arg["datum_tot"])
   )
{
  echo "vb, datum_tot of fonds niet opgegeven";
  exit;
}


/** Tot datum omzetten naar jul datum wanneer deze een form datum is */
$dateTotcheck = explode('-', $arg['datum_tot']);
if ( strlen($dateTotcheck[2]) === 4 ) {
  $datumtot = form2jul($arg['datum_tot']);
} else {
  $datumtot = db2jul($arg["datum_tot"]);
}
$arg["datum_tot"] = $datumtot;
$selectData = array(
  'soort' => 'Fondsoverzicht',
  'datumTm' => $datumtot,
  'berekeningswijze' => 'Totaal vermogen',
  'fonds' => $arg["fonds"],
);

include_once("../classes/AE_cls_htmlColomns.php");
include_once("../classes/htmlReports/htmlFondsOverzicht.php");
$fndO = new htmlFondsOverzicht($selectData["VermogensbeheerderVan"]);
$fndO->initModule();
$fndO->clearTable();


$gen = new HTML_fondsOverzichtGenereer($selectData);

$results = $gen->genereer();

foreach ($results as $item)
{
  $fndO->addRecord($item);
}

$item = array();
$item["Rapport"] = "statics";
$item['fonds'] = $arg["fonds"];
$item["Portefeuille"] = "";
$item["Client"] = "";
$item["Naam"] = "";
$item["Naam1"] = "";
$item["accountmanager"] = "";
$item["risicoklasse"] = "";
$item["soortOvereenkomst"] = "";
$item["AandeelTotaalvermogen"] = 0;
$item["AandeelBeleggingscategorie"] = 0;
$item["AandeelTotaalBelegdvermogen"] = 0;
$item["AantalInPortefeuille"] = 0;
$item["ActueleWaarde"] = 0;

$item["memo"] = serialize($gen->statics);
$_SESSION["htmlFondsRapportVars"] = $arg;

$fndO->addRecord($item);

header("location: HTMLrapport/fondsOverzichtRapport.php");

?>