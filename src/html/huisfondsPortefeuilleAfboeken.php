<?php
/*
    AE-ICT sourcemodule created 14 sep. 2022
    Author              : Chris van Santen
    Filename            : huisfondsPortefeuilleAfboeken.php


*/

include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();
//$content = array();
global $USR;

if ($_SESSION["huisfonds"]["file"] == "")
{
  echo "ongeldige aanroep";
  exit;
}


if ($_REQUEST["afboekdatum"] != "")
{

  $portefeuilles  = explode(",", $_REQUEST["portefeuille"]);
  $afboekdatum    = $_REQUEST["afboekdatum"];
  $depot          = $_REQUEST["depot"];
  $output         = array();
  foreach ($portefeuilles as $portefeuille)
  {
    $rekeningen =array();
    $memRekening = $portefeuille."MEM";  //
    $out = getAirsPortefeuilleWaarde($portefeuille,$afboekdatum,$depot);

    $db = new DB();
    $query = "SELECT Rekening, Memoriaal FROM Rekeningen WHERE Portefeuille = '{$portefeuille}'";

    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {

      $rekeningen[$rec["Rekening"]] = getAIRSvaluta($rec["Rekening"],$afboekdatum);
      if ($rec["Memoriaal"] == 1)
      {
        $memRekening = $rec["Rekening"];
      }

    }

    foreach ($rekeningen as $rekening)
    {

      do_geld($rekening);
      echo "<li> geldrekening: {$rekening["Rekening"]}, saldo: {$rekening["totaal"]}</li>";
    }
    foreach($out as $fondsregel)
    {
      do_stukken($fondsregel);
      echo "<li> portefeuille: {$portefeuille}, fonds: {$fondsregel["fonds"]}, aantal: {$fondsregel["totaalAantal"]}</li>";
    }
  }

  foreach($output as $rec)
  {
    $_query = "INSERT INTO `TijdelijkeRekeningmutaties` SET ";
    $qSet = array(
      "add_date = NOW()",
      "add_user = '{$USR}'",
      "change_date = NOW()",
      "change_user = '{$USR}'",
    );

    foreach ($rec as $key=>$value)
    {
//      if ($afboekdatum AND $key == "Boekdatum")
//      {
//        $value = $afboekdatum;
//      }
      $qSet[] = "`{$key}` = '".mysql_escape_string($value)."'";
    }

    $_query .= implode(" ,\n", $qSet);

    if (!$db->executeQuery($_query))
    {
      echo "<li>fout: ".mysql_error()."</li>";
      exit();
    }
  }



  echo "<li>Klaar</li>";
  echo "<hr/> <a href=\"tijdelijkerekeningmutatiesList.php\" class='button'>Ga naar tijdelijk importbestand</a>";
  exit;
}


echo template($__appvar["templateRefreshFooter"], $content);


function getAirsPortefeuilleWaarde($portefeuille, $datum, $depotbank)
{
  $split = explode("-",$datum);
  $datum = $split[2]."-".$split[1]."-".$split[0];

  $db = new DB();

  switch($depotbank)
  {
//      case "AAB BE":
//
//        break;
    case "BIN";
      $depotSearch = "(Portefeuilles.Depotbank = 'BIN'  OR Portefeuilles.Depotbank = 'BINB') ";
      break;
    case "CS";
      $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
      break;
    case "AAB";
      $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'AABIAM') ";
      break;
    default:
      $depotSearch = "Portefeuilles.Depotbank = '".$depotbank."' ";
  }


  $query = "
      SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds as fonds,
        SUM(Rekeningmutaties.Aantal) AS totaalAantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening  
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      WHERE
        Rekeningen.consolidatie='0' AND
        Portefeuilles.consolidatie = '0' AND 
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum, 0, 4)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= '".$datum."' AND 
        Portefeuilles.Portefeuille='".$portefeuille."' AND
        $depotSearch 
      GROUP BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(totaalAantal,4) <> 0
      ORDER BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds; ";


  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    $out[] = $rec;

  }
  return $out;
}

function getAIRSvaluta($rekeningnr, $datum)
{
  $split = explode("-",$datum);
  $datum = $split[2]."-".$split[1]."-".$split[0];

  $tmpDB = New DB();

  $qExtra = "Rekeningmutaties.boekdatum <= '".$datum."' ";

  $query = "
    SELECT 
      Rekeningen.Valuta, 
      round(SUM(Rekeningmutaties.Bedrag),12) as totaal,
      Rekeningmutaties.Rekening
    FROM 
      Rekeningmutaties, Rekeningen
    WHERE
      Rekeningen.consolidatie='0' AND
    	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
    	Rekeningmutaties.boekdatum >= '".substr($datum, 0, 4)."' AND
      Rekeningmutaties.Rekening = '".$rekeningnr."' AND
      
    	$qExtra
    GROUP BY 
      Rekeningen.Valuta
    ORDER BY 
      Rekeningen.Valuta";
//debug($query);
  if ($data = $tmpDB->lookupRecordByQuery($query))
  {
    return $data;
  }
  else
  {
    return false;
  }
}


function _valutakoers()
{
  global $mr;
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "
      SELECT 
        * 
      FROM 
        Valutakoersen 
      WHERE 
        Valuta='".$mr["Valuta"]."' AND 
        Datum <= '".$mr["Boekdatum"]."' 
      ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
  else
  {
    return 1;
  }


}

function _fondskoers()
{
  global $mr, $fonds;
  $db = new DB();
  $query = "
    SELECT 
      * 
    FROM 
      Fondskoersen
    WHERE 
      Fonds = '".$fonds["Fonds"]."' AND 
      Datum <= '".$mr["Boekdatum"]."' 
    ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
}

function do_stukken($data)
{
  global $fonds, $output, $mr, $memRekening;

  $db = new DB();
  $query = "SELECT * FROM Fondsen WHERE Fonds = '".$data["fonds"]."'";

  $fonds = $db->lookupRecordByQuery($query);

  $split = explode("-",$_REQUEST["afboekdatum"]);
  $datum = $split[2]."-".$split[1]."-".$split[0];

  $mr = array();
  $mr["Boekdatum"]         = $datum;
  $mr["settlementDatum"]   = $datum;

  $mr["Rekening"]          = $memRekening;

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Fondskoers"]        = _fondskoers();
  if ($data["totaalAantal"] > 0)
  {
    $mr["aktie"]             = "L";
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Aantal"]            = -1 * $data["totaalAantal"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
    $mr["Transactietype"]    = "L";
    $grootboek = "ONTTR";
  }
  else
  {
    $mr["aktie"]             = "D";
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Aantal"]            = -1 * $data["totaalAantal"];
    $mr["Credit"]             = 0;
    $mr["Debet"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
    $mr["Transactietype"]    = "D";
    $grootboek = "STORT";
  }

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;
  $output[] = $mr;

  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fonds"]             = "";
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = $grootboek;
  if ($mr["Bedrag"] < 0)
  {
    $mr["Credit"]           = abs($mr["Bedrag"]);
    $mr["Debet"]            = 0;
    $mr["Bedrag"]           = $mr["Credit"];
  }
  else
  {
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $mr["Transactietype"]    = "";

  $output[] = $mr;

}


function do_geld($data)
{
  global $mr, $output;

  if (abs($data["totaal"]) < 0.005)
  {
    return false;
  }

//debug($data);
  $split = explode("-",$_REQUEST["afboekdatum"]);
  $datum = $split[2]."-".$split[1]."-".$split[0];

  $mr = array();
  $mr["Rekening"]          = $data["Rekening"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr["Boekdatum"]         = $datum;
  $mr["settlementDatum"]   = $datum;
  $mr["Valuta"]            = $data["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();

  if ($data["totaal"] < 0)
  {
    $mr["Omschrijving"]      = "Storting";
    $mr["aktie"]              = "S";
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            =abs($data["totaal"] );
    $mr["Bedrag"]            = $mr["Credit"];

  }
  else
  {
    $mr["Omschrijving"]      = "Ontrekking";
    $mr["aktie"]              = "O";
    $mr["Grootboekrekening"]  = "ONTTR";
    $mr["Credit"]             = 0;
    $mr["Debet"]              = abs($data["totaal"] );
    $mr["Bedrag"]             = -1 * $mr["Debet"];
  }
//debug($mr);
  $output[] = $mr;

}

