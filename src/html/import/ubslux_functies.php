<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/21 09:58:35 $
 		File Versie					: $Revision: 1.7 $

 		$Log: ubslux_functies.php,v $
 		Revision 1.7  2020/07/21 09:58:35  cvs
 		call 7606
naar rvv 20201113

*/


function trimFields($in)
{
  $out = array();
  foreach ($in as $item)
  {
    $out[] = trim($item);
  }
  return $out;
}

function ubsCheckIsDate($in)
{
  $s = explode(".",$in);

  if (count($s) != 3) return false;

  $d = (int)$s[0];
  $m = (int)$s[1];
  $j = (int)$s[2];
  return checkdate($m, $d, $j);
}

function l($v1, $v2="", $v3="")
{
  global $ubsLuxDebug;
  if ($ubsLuxDebug)
  {
    echo "<br/>$v1 --> $v2 --> $v3";
  }

}

function checkRowType ($data, $row)
{
  global $fileTypeArray, $ftRowsSkipped;
  $cols = count($data)-1;
//debug($data);
  $fileType = "";
  l("<hr>regel","row: ".$row, "cols=$cols");

  foreach ($fileTypeArray as $type => $checks)
  {
//    debug($checks, $type);
    l("--cols check", $type, $checks["columns"]);
    l("--skip check", (int)$checks["skipped"]);
    if ($cols != $checks["columns"] AND $cols != ($checks["columns"]-1))
    {
      continue;
    }
    $fileType  = $type;
    $dateCheck = true;

    foreach ($checks["dates"] as $d)
    {
      l("$type",$d,$data[$d]);
      if (!ubsCheckIsDate($data[$d]))
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

function ubsluxDatum  ($date)
{
  $s = explode(".",$date);
  return $s[2]."-".$s[1]."-".$s[0];
}



function ubsluxNumber($in)
{
  $in = str_replace("+", "", $in);
  return $in * 1;  // verwijder voorloop nullen
}



function checkControleBedrag($controleBedrag, $nettoBedrag)
{
  global $meldArray, $data, $mr;
//debug(array($controleBedrag,$nettoBedrag,$data),"in controle");
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($nettoBedrag,2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan BANK = ".$notabedrag." / AIRS = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
}



function _debetbedrag()
{
  global $data, $mr;

  if ($data["rekValuta"] == $mr["Valuta"] )
    return -1 * $mr["Debet"];
else
  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
  global $data, $mr;

  if ($data["rekValuta"] == $mr["Valuta"] )
    return $mr["Credit"];
  else
    return $mr["Credit"] * $mr["Valutakoers"];
}

function _valutakoersAIRS($fondsValuta)
{
  global $fonds, $data, $mr, $valutaLookup, $DB, $kostenFactor;

  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$fondsValuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  $kostenFactor = 1;
  return $laatsteKoers["Koers"];
}


function _valutakoers($rekValuta, $fondsValuta)
{
  global $fonds, $data, $mr, $valutaLookup, $DB, $kostenFactor;


  if ($rekValuta == "EUR" AND $fondsValuta == "EUR")
  {
    $kostenFactor = 1;
    return 1;
  }

  if ($rekValuta == "EUR" AND $fondsValuta <> $rekValuta)
  {
    $kostenFactor = $data["wisselkoers"];
    return $data["wisselkoers"];
  }
  elseif ( $rekValuta != "EUR" AND  $fondsValuta == $rekValuta )
  {
//    return $fData["wisselkoers"];
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$fondsValuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $DB->lookupRecordByQuery($query);
    $kostenFactor = 1;
    return $laatsteKoers["Koers"];
  }


  return 999;
}

function ubslCheckRekening($rekening, $valuta, $depot="UBSL")
{
  global $row;
  $db = new DB();

  $rekeningNr = $rekening.$valuta;

  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["Rekening"];
    }
    else
    {
      return false;
    }
  }
}

function getFondsPos($data)
{
  global $fonds;
  $isin   = trim($data["isin"]);
  $portefeuille = trim($data["rekening"]);
  $fonds  = false;
  $db = new DB();

  if ($isin <> "")
  {
    // eerst zoeken naar fondsen in positie tbv DIV
    $query = "
     SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds AS Fonds,
        Left(Fondsen.ISINcode,12) AS ISIN,
        SUM(Rekeningmutaties.Aantal) AS aantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = '0'
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      JOIN Fondsen ON 
        Rekeningmutaties.Fonds = Fondsen.Fonds            
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) >= '".(date("Y")-1)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= NOW() AND
        Rekeningen.Portefeuille = '{$portefeuille}' AND
        Left(Fondsen.ISINcode,12) = '{$isin}'
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds,
        Left(Fondsen.ISINcode,12)
      ORDER BY   
        SUM(Rekeningmutaties.Aantal) DESC
      
    ";

    if (!$posRec = $db->lookupRecordByQuery($query))  // als geen posities gevonden
    {
      // zoeken fonds op ISIN

      $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$isin}' ";
      $fonds = $db->lookupRecordByQuery($query);

    }
    else
    {

      $query = "SELECT * FROM Fondsen WHERE Fonds = '{$posRec["Fonds"]}' ";

      $fonds = $db->lookupRecordByQuery($query);

    }

    return $fonds;


  }
  return false;

}

/////////////////////

function getFonds()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();

  $fonds = array();
  if (trim($data[3]) <> 0 AND $data[6] != "CASH")
  {
    $bankcode = trim($data[3]);
    $query = "SELECT * FROM Fondsen WHERE  UBSLcode = '".trim($bankcode)."' ";
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
      return $fonds;
    }
    else
    {
      $error[] = "{$data["row"]}: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
    }
  }
  else
  {
    $error[] = "{$data["row"]}: fonds UBSLcode {$bankcode} (zonder ISIN) niet gevonden ";
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
	global $mr, $data, $controleBedrag;

  $mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["valutadatum"];
  $mr["Rekening"]          = ubslCheckRekening($data["rekening"].$data["rekValuta"]);
	$mr["bestand"]           = $data["file"];
	$mr["regelnr"]           = $data["row"];
//  $mr["aktie"]             = $data["transactiecode"];
  $mr["bankTransactieId"]  = $data["transactieId"];

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output, $kostenFactor;
//  debug($data);
//  debug($fonds);
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]             = "A";
	do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"] );

	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data["aantal"];
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "RENME";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data["ownFees"]/$kostenFactor);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$kosten = abs($data["brokerFees"]) + abs($data["otherFees"]) + abs($data["deliveryFees"]) +
            abs($data["handlingFees"]) + abs($data["exchangeFees"]);

	$mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = $kosten/$kostenFactor;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
    
  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data["tax"]/$kostenFactor);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
    
  checkControleBedrag($controleBedrag, $data["afrekenBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_D()  // Deponering van stukken
{
  global $fonds, $data, $mr, $output, $kostenFactor, $meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "D";
  do_algemeen();

  $data["rekValuta"]       = "EUR";

  $mr["Rekening"]          = ubslCheckRekening($data["rekening"]."MEM");
  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAirs($mr["Valuta"]);

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Debet"];
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


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

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $kosten = abs($data["brokerFees"]) + abs($data["otherFees"]) + abs($data["deliveryFees"]) +
    abs($data["handlingFees"]) + abs($data["exchangeFees"]);
  $mr["Rekening"]          = ubslCheckRekening($data["rekening"]."EUR");
  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = $kosten/$kostenFactor;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag, $data["afrekenBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()  // Lichting van stukken
{
  global $fonds, $data, $mr, $output, $kostenFactor, $meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "L";
  do_algemeen();

  $data["rekValuta"]       = "EUR";

  $mr["Rekening"]          = ubslCheckRekening($data["rekening"]."MEM");
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAirs($mr["Valuta"]);

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Credit"];
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fonds"]             = "";
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($mr["Bedrag"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"];

  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $kosten = abs($data["brokerFees"]) + abs($data["otherFees"]) + abs($data["deliveryFees"]) +
    abs($data["handlingFees"]) + abs($data["exchangeFees"]);
  $mr["Rekening"]          = ubslCheckRekening($data["rekening"]."EUR");
  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = $kosten/$kostenFactor;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag, $data["afrekenBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Verkoop van stukken
{

  global $fonds, $data, $mr, $output, $kostenFactor;
//  debug($data);
//  debug($fonds);
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "A";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"] );

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]            = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "RENOB";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["ownFees"]/$kostenFactor);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $kosten = abs($data["brokerFees"]) + abs($data["otherFees"]) + abs($data["deliveryFees"]) +
    abs($data["handlingFees"]) + abs($data["exchangeFees"]);

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = $kosten/$kostenFactor;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["tax"]/$kostenFactor);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["afrekenBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray;
  $mr = array();


  do_algemeen();

  getFondsPos($data);
  $controleBedrag = 0;
  //$mr["Rekening"]        = ubslCheckRekening($data["rekening"].$data["valuta"]);
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr["Grootboekrekening"] = "DIV";

  if ($data["afrekenBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["afrekenBedrag"]) + abs($data["tax"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["afrekenBedrag"]) + abs($data["tax"]);
    $mr["Bedrag"]            = _creditbedrag();

  }
  $controleBedrag += $mr["Bedrag"];

  $output[] = $mr;


  // uitgezet om te wachten op eerste DIVBE boekiing 3-2-2021

  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["tax"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }




  checkControleBedrag($controleBedrag, $data["afrekenBedrag"]);



}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()
{

	global $data,$mr,$output, $transactieCodes, $afw, $meldArray;
//debug($data);
	$mr = array();
	$mr["aktie"]              = "Mut.";
	do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  if (strtolower($data["omschrijving"]) == "dividend")
  {
    do_DIV();
    return;
  }
  if (strtolower($data["omschrijving"]) == "coupon credit" AND $data["isin"] != "")
  {
    $meldArray[] = "{$data["row"]}: Coupen credit icm Fonds gevonden ";
    return;
  }



  $controleBedrag = 0;
	//$mr["Rekening"]        = ubslCheckRekening($data["rekening"].$data["valuta"]);
	$mr["Omschrijving"]      = $data["omschrijving"];
	$mr["Valuta"]            = $data["rekValuta"];    // dbs2778
  $mr["Valutakoers"]       = $data["wisselkoers"];

	$mr["Fonds"]             = "";
	$mr["Aantal"]            = '';
	$mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	if ($data["afrekenBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";

    $mr["Debet"]             = abs($data["afrekenBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $mr = $afw->reWrite("ONTTR", $mr);

  }
	else
  {
    $mr["Grootboekrekening"] = "STORT";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["afrekenBedrag"]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr = $afw->reWrite("STORT", $mr);

  }

	if (
	  trim($data["omschrijving"]) == "Custody fee" OR
	  trim($data["omschrijving"]) == "All inclusive fee"
  )
  {

    $mr["Grootboekrekening"] = "BEW";
    $mr = $afw->reWrite("BEW", $mr);
  }

  if (trim($data["omschrijving"]) == "External Asset Management Fee" )
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr = $afw->reWrite("BEH", $mr);
  }

  if (substr(strtolower($data["omschrijving"]),0,8) == "interest")
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr = $afw->reWrite("RENTE", $mr);
  }

  if (strtolower($data["omschrijving"]) == "coupon credit")
  {
    $mr["Grootboekrekening"] = "VKSTO";
    $mr = $afw->reWrite("VKSTO", $mr);
  }

  if (strtolower($data["omschrijving"]) == "forex trade spot")
  {
    $mr["Grootboekrekening"] = "KRUIS";
  }




	$controleBedrag += $mr["Bedrag"];

  $output[] = $mr;


	checkControleBedrag($controleBedrag, $data["afrekenBedrag"]);


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_FX($fxData)
{


  global $data,$mr,$output, $transactieCodes, $afw, $meldarray;


  if (count($fxData) != 2)
  {
    $meldarray[] = "koppelId {$fxData[0]["koppelId"]} komt meer dan 2x voor";
    return;
  }
  if ($fxData[0]["rekValuta"] != "EUR" AND $fxData[1]["rekValuta"] != "EUR")
  {
    $meldarray[] = "koppelId {$fxData[0]["koppelId"]} geen EUR poot gevonden, handmatig boeken";
    return;
  }

  $mr = array();
  $mr["aktie"]              = "KRUIS";

  if ($fxData[0]["rekValuta"] == "EUR")
  {
    $eurPoot = $fxData[0];
    $vvPoot  = $fxData[1];
  }
  else
  {
    $eurPoot = $fxData[1];
    $vvPoot  = $fxData[0];
  }
  debug($eurPoot,"EUR");
  debug($vvPoot,"VV");
  $data = $eurPoot;
  do_algemeen();

  $controleBedrag = 0;

  $mr["Omschrijving"]      = "Valuta transactie ".$data["koppelId"]. "  ";
  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = 1;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  if ($data["afrekenBedrag"] < 0) // wordt contra geboekt
  {
    $mr["Debet"]             = abs($data["afrekenBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["afrekenBedrag"]);
    $mr["Bedrag"]            = _creditbedrag();
  }
  $controleBedrag = $mr["Bedrag"];


  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $output[] = $mr;

  $data = $vvPoot;
  do_algemeen();

  $mr["Omschrijving"]      = "Valuta transactie ".$data["koppelId"]. "  ";
  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = abs($eurPoot["afrekenBedrag"]/$vvPoot["afrekenBedrag"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  if ($data["afrekenBedrag"] < 0) // wordt contra geboekt
  {
    $mr["Debet"]             = abs($data["afrekenBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["afrekenBedrag"]);
    $mr["Bedrag"]            = _creditbedrag();
  }

  $controleBedrag += ($mr["Bedrag"] * $mr["Valutakoers"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $output[] = $mr;
  checkControleBedrag($controleBedrag, 0);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////




function do_NVT()
{
  global $meldArray, $data;
//  $meldArray[] = "regel ".$data["regelnr"].":<b> met aktie NVT overgeslagen</b>";
}

function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}

?>