<?
/*
    AE-ICT sourcemodule created 17 feb. 2022
    Author              : Chris van Santen
    Filename            : socgen_functies.php


*/


function mapDataFields()
{
  global $data;

  $data["rekening"]         = $data[1];
  $data["afrekenValuta"]    = $data[30];
  $data["portefeuille"]     = $data[2];
  $data["isin"]             = $data[12];
  $data["bankCode"]         = $data[13];
  $data["omschrijving"]     = $data[11];
  $data["boekdatum"]        = _cnvDate($data[4]);
  $data["settledatum"]      = _cnvDate($data[3]);
  $data["aantal"]           = _cnvNumber($data[14]);
  $data["nettoBedrag"]      = _cnvNumber($data[22]);
  $data["brutoPFValuta"]    = _cnvNumber($data[25]);
  $data["nettoPFValuta"]    = _cnvNumber($data[29]);

//  $data["valutakoers"]      = 999;
  $data["valutakoers"]      = _cnvNumber($data[31]);
  $data["transactieId"]     = $data[6];
  $data["storno"]           = $data[7];
  $data["transactieCode"]   = $data[8];
  $data["transactieType"]   = $data[9];
  $data["fondsValuta"]      = $data[16];
  $data["fxValuta"]         = $data[23];
  $data["koers"]            = _cnvNumber($data[15]);
  $data["brutoBedrag"]      = $data[17];
  $data["opgelopenRente"]   = _cnvNumber($data[18]);  
  $data["costs"]            = _cnvNumber($data[19]);
  $data["taxes"]            = _cnvNumber($data[20]);


//debug($data);

  /*
  $data["kostenPosten"][0]  = array(
    "categorie"     => $data[50],
    "valuta"        => $data[51],
    "bedrag"        => $data[52],
    "omschrijving"  => $data[53] );
  $data["kostenPosten"][1]  = array(
    "categorie"     => $data[55],
    "valuta"        => $data[56],
    "bedrag"        => $data[57],
    "omschrijving"  => $data[58] );
  $data["kostenPosten"][2]  = array(
    "categorie"     => $data[60],
    "valuta"        => $data[61],
    "bedrag"        => $data[62],
    "omschrijving"  => $data[63] );
  $data["kostenPosten"][3]  = array(
    "categorie"     => $data[65],
    "valuta"        => $data[66],
    "bedrag"        => $data[67],
    "omschrijving"  => $data[68] );
  $data["kostenPosten"][4]  = array(
    "categorie"     => $data[70],
    "valuta"        => $data[71],
    "bedrag"        => $data[72],
    "omschrijving"  => $data[73] );


*/
}

function _cnvNumber($in)
{
  global $set;
  $in = str_replace($set["thousandSign"], "", $in);
  return str_replace($set["decimalSign"], ".", $in);
}

function _cnvDate($in)
{
  $parts = explode("/", $in);
  return $parts[2]."-".$parts[1]."-".$parts[0];
}

function getTransactieMapping()
{
  global $set, $transactieCodes, $transactieMapping;
  $db = new DB();
  $query = "SELECT bankCode,doActie FROM {$set["transactieCodes"]}";

  $db->executeQuery($query);
  while ($row = $db->nextRecord())
  {
    $transactieCodes[]                   = $row["bankCode"];
    $transactieMapping[$row["bankCode"]] = $row["doActie"];
  }
  $transactieCodes = array_unique($transactieCodes);
  sort($transactieCodes);
}

function getFonds()
{
  global $set, $data, $error, $row, $fonds,$meldArray;
  $db = new DB();
  $fonds = array();

  $bankCode = trim($data["bankCode"]);

  if ($bankCode != "" AND $bankCode != "NULL")
  {
  
    $query = "SELECT * FROM Fondsen 
              WHERE {$set["bankCode"]} = '".trim($bankCode)."' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($data["isin"]);

  if($ISIN != "" AND $ISIN != "NULL")
  {
    $query = "SELECT * FROM Fondsen WHERE 
              ISINCode = '{$ISIN}' AND Valuta ='{$data["fondsValuta"]}' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds {$ISIN}/{$data["fondsValuta"]} niet gevonden </span>";
      $error[]     = "$row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
      return false;
    }
  }
  else
  {
    $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds bankcode {$data["bankCode"]} (zonder ISIN) niet gevonden </span>";
    $error[]      = "$row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden ";
    return false;
  }

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
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: rekening {$rekeningNr} niet gevonden </span>";
      $error[]     = "{$row}: rekening {$rekeningNr} niet gevonden ";
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
  global $data, $mr;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if (  ($rekValuta == "EUR" AND $mr["Valuta"] != "EUR") OR
        ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta) )
  {
    if ($data["valutakoers"] == 999)
    {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='{$mr["Valuta"]}' AND Datum <= '{$mr["Boekdatum"]}' ORDER BY Datum DESC";
      $laatsteKoers = $db->lookupRecordByQuery($query);
      return $laatsteKoers["Koers"];
    }
    else
    {
      return $data["valutakoers"];
    }

  }

//  if ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta)
//  {
//      return 1/$data["valutakoers"];
//  }
//  else
//  {
//    return 9999;
//  }

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

function checkControleBedragOud($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $prefix = "regel {$mr["regelnr"]}: {$mr["Rekening"]} --> bedrag";
  if ( $controleBedrag <> $notabedrag )
  {
    $meldArray[] = "{$prefix} sluit niet aan bank= {$notabedrag} / AIRS = {$controleBedrag} / verschil = ".($notabedrag - $controleBedrag);
  }
  else
  {
    $meldArray[] = "{$prefix} sluit aan ";
  }

}

function kostenPosten()
{
  global $meldArray, $mr, $data, $controleBedrag, $output, $fonds;

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "KOST";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["costs"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
   
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "KOBU";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["taxes"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  switch(trim(strtoupper($data["transactieType"])))
  {
    case "CASH ACCOUNT":
      do_FX();
      return;
      break;
    case "FORWARD":
      do_forward();
      return;
      break;
  }

    $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
  if(checkForDoubleImport($mr)) { return; }


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

  verwerkRente("A");

  kostenPosten();

  checkControleBedrag($controleBedrag, -1 * abs($data["nettoPFValuta"]));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Verkoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  switch(trim(strtoupper($data["transactieType"])))
  {
    case "CASH ACCOUNT":
      do_FX();
      return;
      break;
    case "FORWARD":
      do_forward();
      return;
      break;
  }

  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  if(checkForDoubleImport($mr)) { return; }

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

  verwerkRente("V");

  kostenPosten();

  checkControleBedrag($controleBedrag, $data["nettoPFValuta"]);
}


function verwerkRente($actie)
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);

  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;


  switch($actie)
  {
    case "A":
      $mr["Grootboekrekening"]  = "RENME";
      $mr["Debet"]            = abs((float)$data["opgelopenRente"] );
      $mr["Credit"]           = 0;
      $mr["Bedrag"]           = _debetbedrag();
      break;
    case "V":
      $mr["Grootboekrekening"]  = "RENOB";
      $mr["Debet"]            = 0;
      $mr["Credit"]           = abs((float)$data["opgelopenRente"] );
      $mr["Bedrag"]           = _creditbedrag();
      break;
  }

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

     
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "KOBU";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["taxes"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  // uitzonderingen afhandelen
  switch(trim(strtoupper($data["transactieType"])))
  {
    case "CASH ACCOUNT":
      do_GELDMUT(); 
      return;
    case "FUTURE":
    case "MONEY MARKET":
      do_NVT(); 
      return;
    case "CONVERTIBLE BOND":
    case "FIXED INCOME":
      do_RENOB(); 
      return;
  }

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DIV";
  do_algemeen();
  if(checkForDoubleImport($mr)){ return; }

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($data["brutoBedrag"] > 0)
  {
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["brutoBedrag"]);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;
  
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "KOBU";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["costs"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
   
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "DIVBE";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["taxes"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENOB()  //Coupon
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENOB";
  do_algemeen();
  if(checkForDoubleImport($mr)){ return; }

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($data["brutoBedrag"] > 0)
  {
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["brutoBedrag"]);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;
  
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "KOST";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["costs"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
   
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "KNBA";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["taxes"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{

	global $meldArray, $error, $row;

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();


  $mr["aktie"]              = "FX";
  do_algemeen();

//  debug($data);
  $valuta1  = $data["fondsValuta"];
  $valuta2  = $data["fxValuta"];
  $bedrag1  = $data["aantal"];
  $bedrag2  = $data["brutoBedrag"];

  if ($valuta1 != "EUR" AND $valuta2 != "EUR")
  {
    $meldArray[] = "regel ".$row.": Forex in VV {$valuta2}/{$valuta1}, deze handmatig boeken";
    return;
  }

  if ($valuta1 == "EUR")
  {
    $pootEUR = array(
      "rekening" => $data["rekening"],
      "valuta"  => $valuta1,
      "bedrag"  => $bedrag1,
      "isDebit" => $data["nettoBedrag"] > 0,
    );
    $pootVV = array(
      "rekening" => $data["rekening"],
      "valuta"  => $valuta2,
      "bedrag" => $bedrag2,
      "isDebit" => !$pootEUR["isDebit"]
    );
  }
  else
  {
    $pootEUR = array(
      "rekening" => $data["rekening"],
      "valuta"  => $valuta2,
      "bedrag" => $bedrag2,
      "isDebit" => $data["nettoBedrag"] < 0,
    );
    $pootVV = array(
      "rekening" => $data["rekening"],
      "valuta"  => $valuta1,
      "bedrag" => $bedrag1,
      "isDebit" => !$pootEUR["isDebit"]
    );
  }
  $wKoers = abs($pootEUR["bedrag"]/$pootVV["bedrag"]);

// poot 1 boeken

  $mr["Rekening"]          = $pootEUR["rekening"].$pootEUR["valuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkForDoubleImport($mr) ) { return; }

  $mr["Valuta"]            = $pootVV["valuta"];
  $mr["Valutakoers"]       = $wKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $mr["Grootboekrekening"] = "KRUIS";

  if ($pootEUR["isDebit"])
  {
    $mr["Omschrijving"]      = "FX {$pootEUR["valuta"]}/{$pootVV["valuta"]}";
    $mr["Debet"]             = abs($pootVV["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"];
  }
  else
  {
    $mr["Omschrijving"]      = "FX {$pootVV["valuta"]}/{$pootEUR["valuta"]}";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($pootVV["bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // poot 2 VW boeken

  $mr["Rekening"]          = $pootVV["rekening"].$pootVV["valuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valuta"]            = $pootVV["valuta"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";

  if ($pootVV["isDebit"])
  {
    $mr["Debet"]             = abs($pootVV["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 *abs($pootVV["bedrag"]);
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($pootVV["bedrag"]);
    $mr["Bedrag"]            = abs($pootVV["bedrag"]);
  }
  $mr["Valutakoers"]       = $wKoers;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // KOSTEN
  $mr["Rekening"]          = $data["rekening"].$data["fxValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valuta"]            = $data["fxValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KOST";

  $mr["Debet"]             = abs($data["costs"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Valutakoers"]* $mr["Debet"];

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, abs($data[32])+abs($data[43]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_R()  //rente
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "RENTE";
  do_algemeen();
  if(checkForDoubleImport($mr)){ return; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["brutoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = abs($data["brutoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_BEH()  //beheerfee
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "BEH";
  do_algemeen();
  if( checkForDoubleImport($mr)){ return; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["brutoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = abs($data["brutoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  if(trim(strtoupper($data["transactieType"])) != "CASH ACCOUNT")
  {
    do_STUKMUT();
    return;
  }

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  if( checkForDoubleImport($mr)) { return; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["fondsValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["brutoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Bedrag"]            = _creditbedrag();
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  $mr["Valuta"]            = $data["fondsValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"]  = "KNBA";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Debet"]            = abs($data["costs"]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


function do_STUKMUT()  //mutatie gelstukkend
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  if( checkForDoubleImport($mr)) { return; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = 0;

  if ($data["aantal"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

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

function do_forward()
{
  global $row, $meldArray, $error;
  $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: Let op forward. Handmatig boeken </span>";
  $error[]     = "regel {$row}: Let op forward. Handmatig boeken ";
  return false;
}