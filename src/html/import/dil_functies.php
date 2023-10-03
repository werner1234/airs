<?
/*
    AE-ICT sourcemodule created 12 feb. 2021
    Author              : Chris van Santen
    Filename            : sarasin_functies.php


*/



function mapDataFields()
{
  global $data;

  debug($data,"dataIn");
  $out = array();
  switch($data["rowType"])
  {
    case "buysell":
      $rekParts = explode("/",$data[19]);

      $out["rowType"]          = "AV";
      $tc                      = ($data[9] < 0)?"S":"B";
      $out["transactieCode"]   = "AV-".$tc;
      $out["isin"]             = $data[5];
//      $out["bankCode"]         = $data[];
      $out["afrekenValuta"]    = $data[2];
      $out["portefeuille"]     = $data[1];
      $out["rekening"]         = $data[1];
      $out["omschrijving"]     = trim($data[6]);
      $out["boekdatum"]        = $data[7];
      $out["settledatum"]      = $data[8];
      $out["aantal"]           = $data[9];
      $out["nettoBedrag"]      = $data[14];
      $out["valutakoers"]      = $data[11];
      $out["transactieId"]     = $data[38];

      //$out["storno"]           = $data[8];
      //$out["stornoId"]         = $data[9];
      $out["fondsValuta"]      = $data[2];// moet nog gecontroleerd worden
      $out["koers"]            = $data[10];
      $out["brutoBedrag"]      = $data[13];
      $out["kostenZegel"]      = $data[15];
      $out["kost"]             = $data[17];
      $out["kostenBeurs"]      = $data[18];
      $out["KostenCourtage"]   = $data[19];
      $out["KostenBuitenland"] = $data[20];
      $out["KostenTaks"]       = $data[21];
      $out["KostenAanvTaks"]   = $data[22];
      $out["opgelopenRente"]   = $data[24];


      break;
    case "divcoup":
      $rekParts = explode("/",$data[19]);

      $out["rowType"]          = "CD";
      $out["isin"]             = $data[6];
      $out["bankCode"]         = "";
      $out["afrekenValuta"]    = $data[5];
      $out["portefeuille"]     = $data[4];
      $out["rekening"]         = $data[4];
      $out["omschrijving"]     = trim($data[29]);
      $out["boekdatum"]        = $data[2];
      $out["settledatum"]      = $data[3];
      $out["aantal"]           = $data[8];
      $out["nettoBedrag"]      = $data[35];
      //$out["nettoCash"]        = $data[33];
      $out["valutakoers"]      = $data[13];
      $out["transactieId"]     = $data[1];
      $out["transactieCode"]   = "CD-".$data[30];
      //$out["storno"]           = $data[8];
      //$out["stornoId"]         = $data[9];
      $out["fondsValuta"]      = $data[27];
      $out["koers"]            = $data[9];
      $out["brutoBedrag"]      = $data[34];
      $out["roer"]             = $data[15];
      $out["divbe"]            = $out["aantal"] * $out["koers"] * $data[10];
      break;
    case "cash":
      $rekParts = explode("/",$data[19]);

      $out["rowType"]          = "GM";
      $out["afrekenValuta"]    = $data[3];
      $out["rekening"]         = $data[1];
      $out["omschrijving"]     = trim($data[8]);
      $out["boekdatum"]        = $data[4];
      $out["settledatum"]      = $data[5];
      $out["aantal"]           = 0;
      $out["nettoBedrag"]      = $data[13];
      $out["transactieId"]     = $data[7];
      $out["transactieCode"]   = "GM-".$data[6];
      $out["valutakoers"]      = 999;

      break;
    default:
      $out = $data;
  }


  $data = $out;



}

function getFonds()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();

  $fonds = array();
  if (trim($data["bankCode"]) != "")
  {
    $query = "SELECT * FROM Fondsen WHERE Dierickscode = '".trim($data["bankCode"])."' ";
    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($data["isin"]);

  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$ISIN' AND Valuta ='".$data["fondsValuta"]."' ";

    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $error[] = "$row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
    }
  }
  else
  {
    $error[] = "$row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden ";
  }

}

function getRekening($rekeningNr="")
{
  global $data, $error, $row, $depotBank;

  $db = new DB();
  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);
  }


  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depotBank."' ";

  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return array("rekening" => $rec["Rekening"],
                 "valuta"   => $rec["Valuta"]);
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depotBank."' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
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


function _valutakoers($rekValuta)
{
  global $data, $mr, $valutaLookup;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if (
        ($rekValuta == "EUR" AND $mr["Valuta"] != "EUR") OR
        ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta) )
  {
    if ($data["valutakoers"] == 999)
    {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
      $laatsteKoers = $db->lookupRecordByQuery($query);
      return $laatsteKoers["Koers"];
    }
    else
    {
      return $data["valutakoers"];
    }

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
    bankTransactieId = '".$mr["bankTransactieId"]."' AND 
    Rekening         = '".$mr["Rekening"]."' AND
    Boekdatum        = '".$mr["Boekdatum"]."'
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"].")";
    return true;
  }
  return false;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;
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

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag )
  {
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> bedrag sluit niet aan bank= ".$notabedrag." / AIRS = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  }
  else
  {
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> bedrag sluit aan ";
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
  checkVoorDubbelInRM($mr);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data["aantal"];
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

   

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Vernkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

   

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DIV";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";

  $mr["Valuta"]            = $data["fondsValuta"];
  $mr["Valutakoers"]       = $data['valutakoers'];
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $bBedrag = $data["aantal"] * $data["koers"];
  if ($data["brutoBedrag"] > 0)
  {
    $mr["Credit"]            = abs($bBedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($bBedrag);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Credit"]           = 0;
  $mr["Debet"]            = abs($data["divbe"]);
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "ROER";
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data['valutakoers'];
  if ($data["afrekenValuta"] == "EUR")
  {
    $mr["Valutakoers"] = 1;
  }
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Credit"]           = 0;
  $mr["Debet"]            = abs($data["roer"]);
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_FEES()  //beheerfee
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "BEH";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["bedragCashMut"] < 0)
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = abs($data["bedragCashMut"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedragCashMut"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

   

  checkControleBedrag($controleBedrag,$data["nettoCash"]);

}




/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["bedragCashMut"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["bedragCashMut"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedragCashMut"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

   

  checkControleBedrag($controleBedrag,$data["nettoCash"]);

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
	global $do_func,$transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}

function checkRowType ($data, $row)
{
  global $fileTypeArray, $ftRowsSkipped;

  foreach ($fileTypeArray as $type => $checks)
  {
//    debug($checks, $type);

    l("--skip check", (int)$checks["skipped"]);

    $fileType  = $type;
    $dateCheck = true;

    foreach ($checks as $d)
    {
      l("$type",$d,$data[$d]);
      if (!dilCheckIsDate($data[$d]))
      {
        $dateCheck = false;
      }
    }
    if ($dateCheck) { break; }
  }
  // als skipped dan niet inlezen
  l("check vars--".$fileType, $dateCheck, $checks["skipped"]);
  if ($fileType != "" AND $dateCheck AND $checks["skipped"])
  {
    //$ftRowsSkipped[] = $row." geen transactieregel ($fileType) overgeslagen ";
    return false;
  }

  if ($fileType != "" AND $dateCheck) // geldige regel gevonden
  {
    l("return","true", $fileType);
    return $fileType;
  }
  else // ongeldige regel met vermelding
  {
    l("return","false");
    $ftRowsSkipped[] = $row." overgeslagen onbekende transactieregel";
    return false;
  }
}

function l($v1, $v2="", $v3="")
{
  global $fileRowDebug;
  if ($fileRowDebug)
  {
    echo "<br/>$v1 --> $v2 --> $v3";
  }

}

function dilCheckIsDate($in)
{
  $s = explode("-",substr($in,0,10));
  if (count($s) != 3) return false;

  $d = (int)$s[2];
  $m = (int)$s[1];
  $j = (int)$s[0];

  return checkdate($m, $d, $j);
}