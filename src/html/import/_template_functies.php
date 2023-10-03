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

  $data["rekening"]         = $data[1];
  $data["afrekenValuta"]    = $data[9];
  $data["portefeuille"]     = $data[18];

//  $data["isin"]             = $data[22];
//  $data["bankCode"]         = $data[34];

  if (strtolower(substr($data[3],0,4)) == "isin")
  {
    $data["isin"]         = substr($data[3],5);
  }

  if (strtolower(substr($data[3],0,8)) == "telekurs")
  {
    $data["bankCode"] = substr($data[3], 9);
  }


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



}

function _cnvNumber($in)
{
  global $set;
  $in = str_replace($set["thousandSign"], "", $in);
  return str_replace($set["decimalSign"], ".", $in);
}

function _cnvDate($in)
{
  $parts = explode(".", $in);
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

function checkControleBedrag($controleBedrag,$notabedrag)
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

function kostenPosten($gbMap = "")
{
  global $meldArray, $mr, $data, $controleBedrag, $output;
  if ($gbMap == "")
  {
    $gbMap = array(
      "COM" => "KOST",

      "FOR" => "KOBU",
      "TAX" => "KOBU",
      "STM" => "KOBU",
      "STX" => "KOBU",

      "BKF" => "KNBA",
      "CHG" => "KNBA",
      "UNE" => "KNBA",
      "OTF" => "KNBA",

      "MTF" => "BEH",

      "WTH" => "DIVBE",

      "INE" => "RENME",
      "CPN" => "RENME",

      "BIR" => "RENOB",
    );
  }

  $tel = 0;
  foreach ($data["kostenPosten"] as $kp)
  {

    $tel++;
    if ($kp["categorie"] == "")
    {
      continue;
    }

    $gb = $gbMap[$kp["categorie"]];

    if ($kp["categorie"] == "CPN" AND $kp["bedrag"] >= 0)
    {
      $gb = "RENOB";
    }

    if ($gb == "")
    {
      $gb = "KNBA";
      $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$mr["regelnr"]}: Geen grootboek mapping voor kostencategorie {$kp["categorie"]}</span>";
    }

    $mr["Valuta"]             = $kp["valuta"];
    $mr["Valutakoers"]        = _valutakoers($kp["valuta"]);
    $mr["Grootboekrekening"]  = $gb;
    $mr["Aantal"]             = 0;
    $mr["Fondskoers"]         = 0;
    if ($kp["bedrag"] < 0)
    {
      $mr["Credit"]           = 0;
      $mr["Debet"]            = abs($kp["bedrag"]);
      $mr["Bedrag"]           = _debetbedrag();
    }
    else
    {
      $mr["Debet"]            = 0;
      $mr["Credit"]           = abs($kp["bedrag"]);
      $mr["Bedrag"]           = _creditbedrag();
    }

    $controleBedrag       += $mr["Bedrag"];

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

  kostenPosten();

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

  kostenPosten();

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

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
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
  checkVoorDubbelInRM($mr);

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

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{
  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();


  $mr["aktie"]              = ($grootboek == "")?"geldmut":$grootboek;
  do_algemeen();

//  debug($data);
  $valuta1  = $data[20];
  $valuta2  = $data[41];
  $rp = explode("/", $data[19]);
  $rek1 = $rp[0];
  $rp = explode("/", $data[40]);
  $rek2 = $rp[0];
  if ($valuta1 != "EUR" AND $valuta2 != "EUR")
  {
    $meldArray[] = "regel ".$data["row"].": Forex in VV {$valuta1}/{$valuta2}, deze handmatig boeken";
    return;
  }

  if ($valuta1 == "EUR")
  {
    $pootEUR = array(
      "rekening" => $rek1,
      "valuta"  => $data[20],
      "bedrag" => $data[32],
    );
    $pootVV = array(
      "rekening" => $rek2,
      "valuta"  => $data[41],
      "bedrag" => $data[43],
    );
  }
  else
  {
    $pootEUR = array(
      "rekening" => $rek2,
      "valuta"  => $data[41],
      "bedrag" => $data[43],
    );
    $pootVV = array(
      "rekening" => $rek1,
      "valuta"  => $data[20],
      "bedrag" => $data[32]
    );
  }
  $wKoers = abs($pootEUR["bedrag"]/$pootVV["bedrag"]);
//  debug($pootEUR,"pootEUR");
//  debug($pootVV,"pootVV");
// poot 1 boeken

  $mr["Rekening"]          = $pootEUR["rekening"].$pootEUR["valuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $pootVV["valuta"];
  $mr["Valutakoers"]       = $wKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"]." {$data[20]}/{$data[41]}";
  $mr["Grootboekrekening"] = "KRUIS";

  if ($pootEUR["bedrag"] < 0)
  {
    $mr["Debet"]             = abs($pootVV["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = (-1 * $mr["Debet"]) * $mr["Valutakoers"];
  }
  else
  {
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

  $mr["Rekening"]          = $pootVV["rekening"].$pootVV["valuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valuta"]            = $pootVV["valuta"];
  $mr["Valutakoers"]       = $wKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";

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


  checkControleBedrag($controleBedrag,abs($data[32])+abs($data[43]));

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
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = abs($data["bedragCashMut"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedragCashMut"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoCash"]);

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

  kostenPosten();

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

  kostenPosten();

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
	global $transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


