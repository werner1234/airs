<?
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Lennart Poot
    Filename            : quintet_functies.php


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

  $data["rekening"]         = $data[3];
  $data["afrekenValuta"]    = $data[19];
  $data["portefeuille"]     = $data[3];
  $data["omschrijving"]     = trim($data[6]);
  $data["transactieId"]     = $data[2];
  $data["transactieCodeA"]  = $data[4];
  $data["transactieCodeB"]  = $data[5];
  $data["transactieCode"]   = trim("{$data['transactieCodeA']}-{$data['transactieCodeB']}", "-");
  $data["bankCode"]         = $data[16];
  $data["isin"]             = $data[17];
  $data["boekdatum"]        = _cnvDate($data[8]);
  $data["settledatum"]      = _cnvDate($data[9]);
  $data["aantal"]           = _cnvNumber($data[24]);
  $data["valutakoers"]      = _cnvNumber($data[10]);
  $data["storno"]           = $data[29];
  $data["stornoId"]         = $data[2];
  $data["fondsValuta"]      = $data[25];
  $data["koers"]            = _cnvNumber($data[26]);
  $data["brutoBedrag"]      = _cnvNumber($data[20]);
  $data["nettoBedrag"]      = _cnvNumber($data[18]);
  $data["brutoValuta"]      = $data[21];
  $data["extKosten"]        = _cnvNumber($data[12]);
  $data["extKostenValuta"]  = _cnvNumber($data[13]);
  $data["bankKosten"]       = _cnvNumber($data[14]);
  $data["bankKostenValuta"] = _cnvNumber($data[15]);
  $data["tax"]              = _cnvNumber($data[27]);
  $data["taxValuta"]        = _cnvNumber($data[28]);

  /*
// CRY COLUMNS
13-External Cost Currency
15-Internal Cost Ccy
19-Net amount CCY
21-Gross amount ccy
25-Product CCY
// OTH COLUMNS
1-Filename
2-Referencenumber
3-Portfolionumber
4-Transactioncode
5-Order Tp
6-Description
7-Security description
8-Trade Date
9-Settlementdate
10-Exchange rate (Product Ccy vs EUR)
11-Exchange rate (Product Ccy vs Account currency)
12-External Cost
14-Internal Cost
16-Internal product Id
17-ISIN-code
18-Net amount (debit/credit on account)
20-Gross amount
22-Coupon/Dividend amount
23-Coupon/Dividend amount CCY/Dividend amount CCY18
24-Number of shares
26-Price
27-(Div)Taxes
28-(Div)Taxes CCY
29-Revers Indicator



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

function verwerkRegel()
{
  global $row, $data, $skipFoutregels, $set, $pro_multiplier, $pro_step, $transactieMapping;

  $row = $data["regelnr"];

  if ($data["isin"] != "" AND $data["bankCode"] != "")
  {
    getFonds();
  }

  $val        = $transactieMapping[trim($data["transactieCode"])];
  $transcode  = trim($data["transactieCode"]);  // tbv  errormelding
  $do_func    = "do_$val";

  if ( function_exists($do_func) )
  {
    call_user_func($do_func);
  }
  else
  {
    do_error($transcode);
  }
}

function _cnvNumber($in)
{
  global $set;
  $in = str_replace($set["thousandSign"], "", $in);
  return str_replace($set["decimalSign"], ".", $in);
}

function _cnvDate($in)
{
  return $in;
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
  if (trim($data["bankCode"]) != "")
  {

    $query = "SELECT * FROM Fondsen 
              WHERE {$set["bankCode"]} = '".trim($data["bankCode"])."' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($data["isin"]);

  if($ISIN != "" )
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
      // $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: rekening {$rekeningNr} niet gevonden </span>";
      // $error[]     = "{$row}: rekening {$rekeningNr} niet gevonden ";
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

function _valutakoersAIRS($rekValuta)
{
  if($rekValuta=="EUR")
  {
    return 1;
  }
  global $data, $mr;
  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='{$rekValuta}' AND Datum <= '{$mr["Boekdatum"]}' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
}


function _valutakoers($rekValuta)
{
  global $data, $mr;
  $fondsValuta = $mr["Valuta"];

  if ($rekValuta == "EUR" AND $fondsValuta == "EUR")
  {
    return 1;
  }

  if ($rekValuta == "EUR" AND $fondsValuta <> $rekValuta)
  {
    if ((float)$data["valutakoers"] == 0)
    {
      return 999;
    }
    else
    {
      return 1 / $data["valutakoers"];
    }
  }
  elseif ( ($rekValuta != "EUR" AND  $fondsValuta == $rekValuta))
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='{$mr["Valuta"]}' AND Datum <= '{$mr["Boekdatum"]}' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
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

function koersUitOmschrijving($omschrijving, &$valuta)
{
  // pak laatste decimal waarde uit de string
  // de laatste 3 hoofdletters voor deze waarde
  // bevatten de tegen valuta
  if(preg_match('/.*([A-Z]{3}) +.* (\d+.\d+)/i', $omschrijving, $matches) != 1)
  {
    $meldArray[] = "regel ".$data["regelnr"].": Forex heeft geen valutakoers, deze handmatig boeken";
    return;
  }

  $exchangeInfo   = $matches[0];
  $valuta         = $matches[1];
  $valutaKoersCSV = 1;

  if((float)$matches[2]!=0)
  {
    return (float)$matches[2];
  }

  return false;
}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;

  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $verschil       = $notabedrag - $controleBedrag;
  if ($verschil == 0)
  {
    $meldArray["gelijk"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
  }
  else if (abs($verschil) < 0.05 )
  {
    $meldArray["verschil005"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($verschil,2);
  }
  else
  {
    $meldArray["verschil"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($verschil,2);
  }
  }


function kostenPosten()
{
  global $meldArray, $mr, $data, $controleBedrag, $output;

  $kostTypes = array(
    "extKosten"   => "KOBU",
    "bankKosten"  => "KOST",
    "tax"         => "KOBU",
  );

  foreach($kostTypes as $key => $gb)
  {

    $mr["Grootboekrekening"]  = $gb;
    $mr["Aantal"]             = 0;
    $mr["Fondskoers"]         = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]              = abs($data[$key]);
    $mr["Bedrag"]             = _debetbedrag();

    $controleBedrag          += $mr["Bedrag"];

    if ($key == "tax" AND trim(strtoupper($data["transactieCodeB"])) == "CASH DIVIDEND")
    {
      $mr["Grootboekrekening"]  = "DIVBE";
    }

    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $controleBedrag          = 0;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
  if(checkVoorDubbelInRM($mr)){ return false; }

  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = abs($data["aantal"]);
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();

	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
	$output[] = $mr;


  kostenPosten();

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);

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
  if(checkVoorDubbelInRM($mr)){ return false; }

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = -1 * abs($data["aantal"]);
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);
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
  if(checkVoorDubbelInRM($mr)){ return false; }

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = $fonds["Fonds"];
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

  kostenPosten();

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
  if(checkVoorDubbelInRM($mr)){ return false; }

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";

  $mr["Valuta"]            = $data["fondsValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['fondsValuta']);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $bBedrag = $data["aantal"] * $data["koers"];
  if ($bBedrag > 0)
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

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

function do_FX()
{
  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();

  $mr["aktie"] = "geldmut";
  do_algemeen();

  // haal koers en valuta uit het bestand
  $koersCSV = koersUitOmschrijving($data["omschrijving"], $valutaCSV);

  // er worden twee regels geboekt (deze functie wordt 2x aangeroepen)
  $isKruis = isset($data["regelVV"]);
  if($isKruis)
  {
    $valutaKoers             = abs((float)$data["nettoBedrag"] / (float)$data["regelVV"]["nettoBedrag"]);
    $valutaVV                = $data["regelVV"]["afrekenValuta"];
  }
  else
  {
    $valutaVV                = $valutaCSV;
    $valutaKoers             = $koersCSV;
  }

  if ($data["afrekenValuta"] != "EUR" AND $valutaVV != "EUR")
  {
    $meldArray[] = "regel ".$data["regelnr"].": Twee keer een vreemde valuta! {$data['afrekenValuta']} infoveld: {$exchangeInfo} , deze handmatig boeken";
    return;
  }

  if($data["afrekenValuta"] == "EUR")
  {
    $valutaKoers             = 1;
  }

  $mr["Rekening"]          = getRekening($data["rekening"].$data["afrekenValuta"]);
  if(checkVoorDubbelInRM($mr)){ return false; }

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = 1 / $valutaKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["regelnr"]           = $data['regelnr'];

  if ($data["nettoBedrag"] < 0) // er gaat geld weg
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Omschrijving"]      = "FX {$data['afrekenValuta']}/{$valutaVV}";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $data["nettoBedrag"];
  }
  else //er komt geld binnen
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Omschrijving"]      = "FX {$valutaVV}/{$data['afrekenValuta']}";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $data["nettoBedrag"];
  }

  if($isKruis)
  {
    $mr["Grootboekrekening"] = "KRUIS";
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  if($isKruis)
  {
    // debug("Controle bedrag: {$controleBedrag} Nettobedrag VV:{$data["regelVV"]["nettoBedrag"]} Koers CSV:{$koersCSV} ");
    if($data["afrekenValuta"] == "EUR")
    {
      checkControleBedrag($controleBedrag, -1 * $data["regelVV"]["nettoBedrag"] / $koersCSV);
    }
    else
    {
      checkControleBedrag($controleBedrag,-1 * $data["regelVV"]["nettoBedrag"] * $koersCSV);
    }
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FXdeprecated()
{
  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();

  $mr["aktie"] = "geldmut";
  do_algemeen();


  if ($data['poot1']['valuta'] != "EUR" AND $data['poot2']['valuta'] != "EUR")
  {
    $meldArray[] = "regel ".$data["regelnr"].": Forex in VV {$poot1['valuta']}/{$poot2['valuta']}, deze handmatig boeken";
    return;
  }

  if ($data['poot1']['valuta'] == "EUR")
  {
    $pootEUR  = $data['poot1'];
    $pootVV   = $data['poot2'];
  }
  else
  {
    $pootEUR  = $data['poot2'];
    $pootVV   = $data['poot1'];
  }

  $wKoers = abs($pootEUR["bedrag"]/$pootVV["bedrag"]);

// poot 1 boeken

  $mr["Rekening"]          = getRekening($pootEUR["rekening"].$pootEUR["valuta"]);
  if(checkVoorDubbelInRM($mr)){ return false; }

  $mr["Valuta"]            = $pootEUR["valuta"];
  $mr["Valutakoers"]       = 1/$data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  $mr["regelnr"]           = $pootEUR['regelnr'];

  if ($pootEUR["bedrag"] < 0) // er gaan euros weg
  {
    $mr["Omschrijving"]      = "FX {$pootEUR['valuta']}/{$pootVV['valuta']}";
    $mr["Debet"]             = abs($pootVV["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = (-1 * $mr["Debet"]) * $mr["Valutakoers"];
  }
  else // er komen euros bij
  {
    $mr["Omschrijving"]      = "FX {$pootVV['valuta']}/{$pootEUR['valuta']}";
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

  // poot 2 boeken

  $mr["Rekening"]          = getRekening($pootVV["rekening"].$pootVV["valuta"]);
  $mr["Valuta"]            = $pootVV["valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  $mr["regelnr"]           = $pootVV['regelnr'];

  if ($pootEUR["bedrag"] > 0)
  {
    $mr["Debet"]             = abs($pootVV["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($pootVV["bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag, abs($pootEUR['bedrag'])+abs($pootVV['bedrag']));

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
  if(checkVoorDubbelInRM($mr)){ return false; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "RENTE";

  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
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
  if(checkVoorDubbelInRM($mr)){ return false; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
    $mr["Grootboekrekening"] = "BEH";
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
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

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  if(checkVoorDubbelInRM($mr)){ return false; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
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


function do_KNBA()
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  if(checkVoorDubbelInRM($mr)){ return false; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KNBA";

  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
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

function do_NVT()
{
  global $meldArray, $data;
  $meldArray[] = "regel ".$data["regelnr"].": <b>NVT met transactiecode {$data['transactieCode']} overgeslagen</b>";
  return true;
}

function do_error($transcode)
{
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


