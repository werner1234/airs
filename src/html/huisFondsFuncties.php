<?php
/*
    AE-ICT sourcemodule created 12 sep. 2022
    Author              : Chris van Santen
    Filename            : huisFondsFuncties.php


*/

function _debetbedrag()
{
	global $mr;
  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $mr;
  return $mr["Credit"] * $mr["Valutakoers"];
}

function _valutakoers()
{
  global $data, $mr, $valutaLookup;

  $valuta = $data[2];
  $valutaLookup = false;

  if (
        ( $valuta == "MEM" AND $mr["Valuta"] == "EUR" ) OR
        ( $valuta == "EUR" AND $mr["Valuta"] == "EUR" ) )
  {
    return 1;
  }

  if ($data[8] > 0)
  {
    $valutaLookup = true;
    return $data[8];
  }
  else
  {
    $db = new DB();
    $query = "SELECT * FROM `Valutakoersen` WHERE `Valuta`='{$mr["Valuta"]}' AND `Datum` <= '{$mr["Boekdatum"]}' ORDER BY `Datum` DESC";
//    debug($query);
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $valutaLookup = true;
    return $laatsteKoers["Koers"];
  }

}


function _fondskoers()
{
  global $mr, $data;
  if ((float)$data[10] > 0)
  {
    return $data[10];
  }

  $db = new DB();
  $query = "
    SELECT 
      * 
    FROM 
      Fondskoersen
    WHERE 
      Fonds = '{$data[12]}' AND 
      Datum <= '{$mr["Boekdatum"]}' 
    ORDER BY Datum DESC";
//  debug($query);
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
  $dat                     = explode("-", $data[9]);
	$mr["Boekdatum"]         = "{$dat[2]}-{$dat[1]}-{$dat[0]}";
  $mr["settlementDatum"]   = "{$dat[2]}-{$dat[1]}-{$dat[0]}";
  $mr["Rekening"]          = $data[1].$data[2];
}



function do_L()  //Lossing van obligaties
{
	global  $data, $mr, $output;
	$mr = array();
	$mr["aktie"]              = "L";
	do_algemeen();
	$mr["Omschrijving"]      = "Lichting  ".$data[11];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[4];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $data[12];
  $mr["Aantal"]            = abs($data[6]) * -1;
	$mr["Fondskoers"]        = _fondskoers();
	$mr["Credit"]            = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $data[13]);
	$mr["Debet"]             = 0;
	$mr["Bedrag"]            = _creditbedrag();
	$mr["Transactietype"]    = "L";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
//  debug($mr,"L1");
	$output[] = $mr;

	if  ($mr["Bedrag"] <> 0)
	{
		$mr["Grootboekrekening"] = "ONTTR";
		$mr["Fonds"]             = "";
		$mr["Valuta"]            = "EUR";
		$mr["Valutakoers"]       = 1;
		$mr["Aantal"]            = 0;
		$mr["Fonds"]             = "";
		$mr["Fondskoers"]        = 0;
		$mr["Credit"]            = 0;
		$mr["Debet"]             = abs($mr["Bedrag"]);
		$mr["Bedrag"]            = -1 * $mr["Debet"];
		$mr["Transactietype"]    = "";
//    debug($mr,"L2");
		$output[] = $mr;
	}
}

function do_D()
{
	global $data, $mr, $output;
	$mr = array();
	$mr["aktie"]              = "D";
	do_algemeen();

	$mr["Omschrijving"]      = "Deponering  ".$data[11];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[4];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $data[12];
	$mr["Aantal"]            = $data[5];
	$mr["Fondskoers"]        = _fondskoers();
	$mr["Debet"]             = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $data[13]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
	$mr["Transactietype"]    = "D";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
//  debug($mr,"D1");
	$output[] = $mr;

	if  ($mr["Bedrag"] <> 0)
	{
		$mr["Grootboekrekening"] = "STORT";
		$mr["Fonds"]             = "";
		$mr["Valuta"]            = "EUR";
		$mr["Valutakoers"]       = 1;
		$mr["Aantal"]            = 0;
		$mr["Fonds"]             = "";
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($mr["Bedrag"]);
		$mr["Bedrag"]            = $mr["Credit"];
		$mr["Transactietype"]    = "";
//    debug($mr,"D2");
		$output[] = $mr;
	}

}

function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr             = array();
  $controleBedrag = 0;
  $mr["aktie"]    = "MUT";
  do_algemeen();

  $mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[4];
  if ($data["aantal"] < 0)
  {

    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["aantal"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["aantal"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
//  debug($mr,"LIQ");
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

//  checkControleBedrag($controleBedrag,-1 * $data["aantal"]);

}



function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


