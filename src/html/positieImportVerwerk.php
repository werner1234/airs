<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2009/03/14 11:42:06 $
File Versie					: $Revision: 1.4 $

$Log: positieImportVerwerk.php,v $
Revision 1.4  2009/03/14 11:42:06  rvv
*** empty log message ***

Revision 1.3  2006/05/01 14:31:04  cvs
*** empty log message ***

Revision 1.2  2006/04/28 14:00:25  cvs
*** empty log message ***

Revision 1.1  2006/04/28 09:13:42  cvs
*** empty log message ***



*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
$errors = array();

$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'

$row = 0;
$ndx= 0;
$output = array()	;
$prb->show();
$prb->setLabelValue('txt1','inlezen van CSV bestand ');

if ($_GET["bank"] == "gilis")
  include_once("positieImportVerwerk_gilissen.php");
else
  include_once("positieImportVerwerk_stroeve.php");


function getValuta($valuta)
{
  global $errors,$sqldatum;
  $DB = new DB();

  if ($valuta <> "EUR")
  {
    $valQuery = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$sqldatum."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($valQuery);
    $valutaRec = $DB->lookupRecord();
    $valutaKoers = $valutaRec["Koers"];
  }
  else
  $valutaKoers = 1;
  return $valutaKoers;
}

function getFonds($isinCode="")
{
  global $errors;
  if (trim($isinCode) == "")
  {
    $errors[] = "Geen ISIN code bij mutatie ($isinCode)";
    $fonds["Omschrijving"] = "Geen ISIN code bij mutatie ($isinCode)";

  }
  else
  {
    $DB = new DB();
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$isinCode."' ";
    $DB->SQL($query);
    if (!$fonds = $DB->lookupRecord())
    {
      $errors[] = "ISIN code komt niet voor fonds tabel ($isinCode)";
      $fonds["Omschrijving"] = "FOUT ISIN $isinCode";
    }
  }
  return $fonds;
}

function insertNewRecord($insertRecord)
{
  $DB = new DB();
  global $USR;

  $_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
	$sep = " ";
	while (list($key, $value) = each($insertRecord))
	{
	  if ($manualBoekdatum AND $key == "Boekdatum")
	  {
	    $value = $manualBoekdatum;
	  }

   $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'
";
   $sep = ",";
	}
  $_query .= ", add_date = NOW()";
  $_query .= ", add_user = '".$USR."'";
	$_query .= ", change_date = NOW()";
  $_query .= ", change_user = '".$USR."'";

  $DB->SQL($_query);
	if (!$DB->Query())
	{
	  echo mysql_error();
	  Echo "<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}

}

$valdb = new DB();
/// verwerken van de import
$prb->setLabelValue('txt1','aanmaken mutatieregels ('.$ndx.' records) ');
$pro_step = 0;
$pro_multiplier = 100/$ndx;
for ($x=0;$x < $ndx;$x++)
{
  $pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
  $data = $output[$x];
  $dat = explode("-",$_GET["datum"]);
  $sqldatum = $dat[2]."-".$dat[1]."-".$dat[0]." 00:00:00";
  // zoek valutakoers op
  $valutaKoers = getValuta($data["valuta"]);

  $insertRecord = array();
  if ($data["soort"] == "liq")
  {
    $insertRecord["Grootboekrekening"] = "STORT";
    $insertRecord["Rekening"]          =  $data["portefeuille"];
    $insertRecord["Omschrijving"]      =  "Beginboeking liquiditeiten";
    $insertRecord["Valuta"]            =  $data["valuta"];
    $insertRecord["Boekdatum"]         =  $sqldatum;
    $insertRecord["Valutakoers"]       =  $valutaKoers;
    if ($data["waarde"] < 0)
    {
      $insertRecord["Debet"]  = abs($data["waarde"]);
      $insertRecord["Credit"] = 0;
      $insertRecord["Bedrag"] =  (-1 * abs($data["waarde"]));
    }
    else
    {
      $insertRecord["Debet"]  = 0;
      $insertRecord["Credit"] = abs($data["waarde"]);
      $insertRecord["Bedrag"] = abs($data["waarde"]);
    }
    insertNewRecord($insertRecord);

  }
  else
  {
    $fonds       =  getFonds($data["isin"]);
    $valutaKoers =  getValuta($fonds["Valuta"]);

    $insertRecord["Boekdatum"]         =  $sqldatum;
    $insertRecord["Rekening"]          =  $data["portefeuille"]."MEM";
    $insertRecord["Grootboekrekening"] = "FONDS";
    $insertRecord["Omschrijving"]      =  "Beginboeking ".$fonds["Omschrijving"];
		$insertRecord["Valuta"]            = $fonds["Valuta"];
		$insertRecord["Valutakoers"]       = $valutaKoers;
		$insertRecord["Fonds"]             = $fonds["Fonds"];
		$insertRecord["Aantal"]            = $data["aantal"];
		$insertRecord["Fondskoers"]        = $data["koers"];
		$insertRecord["Debet"]             = round(abs($insertRecord["Aantal"] * $insertRecord["Fondskoers"] * $fonds["Fondseenheid"]),2);
		$insertRecord["Credit"]            = 0;
		$insertRecord["Bedrag"]            = round(-1 * ($insertRecord["Debet"]  * $insertRecord["Valutakoers"]),2);
		$insertRecord["Transactietype"]    = "D";
		$insertRecord["Verwerkt"]          = 0;
		$insertRecord["Memoriaalboeking"]  = 1;
    insertNewRecord($insertRecord);

		$insertRecord["Grootboekrekening"] = "STORT";
    $insertRecord["Credit"]            = round(abs($insertRecord["Aantal"] * $insertRecord["Fondskoers"] * $fonds["Fondseenheid"]),2);
    $insertRecord["Bedrag"]            = round($insertRecord["Credit"] * $insertRecord["Valutakoers"],2);
    $insertRecord["Transactietype"]    = "";
 		$insertRecord["Fonds"]             = "";
		$insertRecord["Aantal"]            = 0;
		$insertRecord["Fondskoers"]        = 0;
		$insertRecord["Debet"]             = 0;
    insertNewRecord($insertRecord);

  }

}

if (count($errors) > 0)
{
  echo "<hr>FOUTmeldingen<hr>";
  for($x=0;$x<count($errors);$x++)
  {
    echo "<br>".$x.": ".$errors[$x];
  }
}

//listarray($output);
$prb->hide();
$tijd = mktime() - $start;


?>