<?
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Chris van Santen
    Filename            : _template_functies.php


*/

///////////////////////////////////////////////////////////////////////////////
///
/// TEMPLATE file voor bankimport, dit bestand niet aanpassen
/// maar opslaan als html/import/{fileprefix}_functies.php
///
///////////////////////////////////////////////////////////////////////////////

function mapDataFields()
{
  global $data;

  //account,fonds,ISIN,Aantal,Datum,koers (EUR),waarde (EUR),categorie
  $data["rekening"]            = $data[1]; // account
  $data["afrekenValuta"]       = "EUR";
  $data["fondsomschrijving"]   = $data[2]; // fonds
  $data["isin"]                = $data[3]; // ISIN
  $data["aantal"]              = (float)$data[4]; // Aantal
  $data["boekdatum"]           = $data[5]; // Datum
  $data["nettoBedrag"]         = (float)$data[7] * -1; // waarde (EUR)
  $data["koers"]               = round(abs($data["nettoBedrag"]) / abs($data["aantal"]), 6); // waarde (EUR)
  $data["transactieCode"]      = trim($data[8]); // categorie
  // not used $data[6]; // koers (EUR)

  /*
  $data["omschrijving"]     = trim("");
  $data["boekdatum"]        = _cnvDate($data[15]);
  $data["settledatum"]      = _cnvDate($data[16]);
  $data["aantal"]           = _cnvNumber($data[5]);
  $data["nettoBedrag"]      = _cnvNumber($data[14]);
  $data["nettoCash"]        = _cnvNumber($data[14]);
//  $data["valutakoers"]      = 999;
  $data["valutakoers"]      = 1/_cnvNumber($data[10]);
  $data["transactieId"]     = $data["rekening"].$data["boekdatum"].$data["nettoBedrag"];
  $data["transactieCode"]   = $data[4];
  //$data["storno"]           = $data[8];
  //$data["stornoId"]         = $data[9];
  $data["fondsValuta"]      = $data[64];
  $data["koers"]            = _cnvNumber($data[8]);
  $data["brutoBedrag"]      = $data[31];
  $data["bedragCashMut"]    = $data[32];
*/


}

function _cnvNumber($in)
{
  global $set;
  $in = str_replace($set["thousandSign"], "", $in);
  return str_replace($set["decimalSign"], ".", $in);
}


function getFonds()
{
  global $set, $data, $error, $row, $fonds,$meldArray;
  $db = new DB();

  $fonds = array();

  $ISIN = trim($data["isin"]);

  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE 
              ISINCode = '{$ISIN}' AND Valuta ='{$data["afrekenValuta"]}' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds {$ISIN}/{$data["afrekenValuta"]} niet gevonden </span>";
      $error[]     = "$row: fonds $ISIN/".$data["afrekenValuta"]." niet gevonden ";
      return false;
    }
  }

  $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds bankcode {$data["bankCode"]} (zonder ISIN) niet gevonden </span>";
  $error[]      = "$row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden ";
  return false;


}

function getRekening($rekeningNr="")
{
  global $data, $error, $row, $depotBank, $meldArray;

  $db = new DB();
  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);
  }

  $query = "SELECT * FROM Rekeningen 
            WHERE consolidatie = 0 AND 
                  `RekeningDepotbank` = '{$rekeningNr}' AND 
                  `Depotbank` = '".$depotBank."' ";


  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return $rekeningNr;
//    return array("rekening" => $rec["Rekening"],
//                 "valuta"   => $rec["Valuta"]);
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE 
              consolidatie = 0 AND 
              `Rekening` = '{$rekeningNr}' AND 
              `Depotbank` = '{$depotBank}' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: rekening {$rekeningNr} icm depotbank niet gevonden </span>";
      $error[]     = "{$row}: rekening {$rekeningNr} icm depotbank niet gevonden ";
      return false;
    }
  }
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray, $depotBank;
  
  $value = "{$depotBank}|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr;

	if ($data["afrekenValuta"] == $mr["Valuta"] )
  {
    return -1 * $mr["Debet"];
  }
	else
  {
    return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
}

function _creditbedrag()
{
	global $data, $mr;

  if ($data["afrekenValuta"] == $mr["Valuta"] )
  {
    return $mr["Credit"];
  }
	else
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }

}

function checkVoorDubbelInRM($mr)
{
  global $meldArray;
  $db = new DB();
  $query = "
  SELECT 
    id 
  FROM 
    Rekeningmutaties 
  WHERE 
    bankTransactieId = '{$mr["bankTransactieId"]}' AND 
    Rekening         = '{$mr["Rekening"]}' AND
    Boekdatum        = '{$mr["Boekdatum"]}'
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel {$mr["regelnr"]}: rekenmutatie is al aanwezig (oa.RMid {$rec["id"]})";
    return true;
  }
  return false;
}

function do_algemeen()
{
	global $mr, $row, $data, $_file;
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data["transactieId"];
	$mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["settledatum"];

	if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec;
  }


}  
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
  if(checkVoorDubbelInRM($mr)) { return; }
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = 1;
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = abs($data["aantal"]);
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = $mr["Debet"] * -1;
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

  if($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag, abs($data["nettoBedrag"]) * -1);

  switch($data["transactieCode"])
  {
    case "Bedongen Fondsenkorting":
      // Bedongen Fondsenkorting		Aankoop	per regel een tegenregel maken. KNBA met omschrijving "Bedongen fonsdenkorting". Ook het FONDS koppelen
      $mr["Omschrijving"]      = "Bedongen fondsenkorting ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "KNBA";
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = 1;
      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
      $controleBedrag       += $mr["Bedrag"];
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      if($mr["Bedrag"] <> 0)
      {
        $output[] = $mr;
      }
      break;

    case "Dividend Herinvesteren":
      // Dividend Herinvesteren		Aankoop	per regel een tegenregel maken. DIV met omschrijving "Dividend <fonds>". Ook het FONDS koppelen
      $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "DIV";
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = 1;
      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
      $controleBedrag       += $mr["Bedrag"];
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      if($mr["Bedrag"] <> 0)
      {
        $output[] = $mr;
      }
      break;
  }
  
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Verkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  if(checkVoorDubbelInRM($mr)) { return; }
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = 1;
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = abs($data["aantal"]) * -1;
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = $mr["Credit"];
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, abs($data["nettoBedrag"]) );
}


function do_K()  // Kosten
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  if(checkVoorDubbelInRM($mr)) { return; }
  $mr["Omschrijving"]      = "Kosten ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "KNBA";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = 1;
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = abs($data["aantal"]);
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = 0; 
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = $mr["Credit"] * +1;
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, abs($data["nettoBedrag"]) * +1);
}

function do_dagtotalen($regels)
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $row;
  $controleBedrag = 0;

 debug( $regels);
  foreach($regels as $sleutel => $totaal)
  {
    $data = array();
    $data["afrekenValuta"] = "EUR";
    list($data["transactieCode"], $data["rekening"], $data["boekdatum"]) = explode("/", $sleutel);

    debug($data);

    $row++;
    $mr = array();



    do_algemeen();

    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;

    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;



    switch(strtolower($data["transactieCode"]))
    {
      case "kosten index people":
        $gb           = "KNBA";
        $omschrijving = "Kosten IndexPeople";
        $aktie        = "TB-KOST";
        break;
     case "verkopen":
//     case "Onttrekking":
       $gb           = "ONTTR";
       $omschrijving = "Ontrekking";
       $aktie        = "TB-V";
       break;
     case "kopen":
//     case "Storting":
        $gb           = "STORT";
        $omschrijving = "Storting";
        $aktie        = "TB-A";
        break;
    }

    $mr["aktie"]             = $aktie;
    $mr["Omschrijving"]      = $omschrijving;
    $mr["Grootboekrekening"] = $gb;

    if($totaal > 0)
    {
      $mr["Credit"]  = abs($totaal);
      $mr["Debet"]   = 0;
    }
    else
    {
      $mr["Credit"] = 0;
      $mr["Debet"]  = abs($totaal);
    }
    $mr["Bedrag"]   = $totaal;


    if($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NVT()
{
  return true;
}

function do_error()
{
	global $transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


