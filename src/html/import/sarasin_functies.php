<?
/*
    AE-ICT sourcemodule created 12 feb. 2021
    Author              : Chris van Santen
    Filename            : sarasin_functies.php


*/



function mapDataFields()
{
  global $data;

  $rek2Parts = explode("/",$data[40]);
  $rek1Parts = explode("/",$data[19]);


  $data["isin"]               = $data[22];
  $data["bankCode"]           = $data[34];
  $data["afrekenValuta"]      = $rek2Parts[1];
  if($rek2Parts[1] == "")
  {
    $data["cashAfrekenValuta"]  = $rek1Parts[1];
    $data["cashRekening"]       = $rek1Parts[0];
    $data["nettoBedrag"]        = $data[33];
  }
  else
  {
    $data["cashAfrekenValuta"]  = $rek2Parts[1];
    $data["cashRekening"]       = $rek2Parts[0];
    $data["nettoBedrag"]        = $data[44];
  }

  $data["portefeuille"]       = $data[18];
  $data["rekening"]           = $rek2Parts[0];

  $data["omschrijving"]       = trim($data[3]);
  $data["boekValuta"]         = $data[20];
  $data["boekdatum"]          = $data[26];
  $data["settledatum"]        = $data[27];
  $data["aantal"]             = $data[28];

  $data["nettoCash"]          = $data[33];
  if ($data[33] != 0 AND $data[44] != 0 AND $data[41] == "EUR")
  {
    $data["valutakoers"]      = abs($data[44]/$data[33]);
  }
  else
  {
    $data["valutakoers"]      = 999;
  }



  $data["transactieId"]     = $data[5];
  $data["transactieCode"]   = $data[2];
  $data["storno"]           = $data[8];
  $data["stornoId"]         = $data[9];
  $data["fondsValuta"]      = ($data[29] == "")?$data[20]:$data[29];
  $data["koers"]            = $data[30];
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

function getTransactieMapping()
{
  global $set, $transactieCodes, $transactieMapping;
  $db = new DB();
  $query = "SELECT bankCode,doActie FROM sarTransactieCodes";

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
  global $data, $error, $row, $fonds,$meldArray;
  $DB = new DB();

  $fonds = array();
  if (trim($data["bankCode"]) != "")
  {

    $query = "SELECT * FROM Fondsen WHERE Sarasincode = '".trim($data["bankCode"])."' ";
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
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel $row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden </span>";
      $error[] = "$row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
    }
  }
  else
  {
    $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel $row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden </span>";
    $error[] = "$row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden ";
  }

}

function getRekening($rekeningNr="")
{
  global $data, $error, $row, $meldArray;

  $db = new DB();
  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);
  }


  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` IN ('SAR','SARCH') ";

  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return array("rekening" => $rec["Rekening"],
                 "valuta"   => $rec["Valuta"]);
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` IN ('SAR','SARCH') ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel $row: rekening ".$rekeningNr." niet gevonden </span>";
      //$error[] = "$row: rekening {$rekeningNr} niet gevonden ";
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

function _debetbedragCash()
{
  global $data, $mr;

  if ($data["cashAfrekenValuta"] == $mr["Valuta"] )
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

function _creditbedragCash()
{
	global $data, $mr;

  if ($data["cashAfrekenValuta"] == $mr["Valuta"] )
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

function do_algemeen($altRekening = "")
{
	global $mr, $row, $volgnr, $data, $_file;
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data["transactieId"];
	$mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["settledatum"];

  $chkRek = "";
  if ($altRekening != "")
  {
    $chkRek = $altRekening;
  }

	if ($rekRec  = getRekening($chkRek) )
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

function kostenPosten($gbMap = "")
{
  global $meldArray, $mr, $data, $controleBedrag, $output;
  if ($gbMap == "")
  {
    $gbMap = array(
      "COM" => "KOST",

      "AGT" => "KOBU",
      "EXE" => "KOBU",
      "FOR" => "KOBU",
      "OTT" => "KOBU",
      "TAX" => "KOBU",
      "STM" => "KOBU",
      "STX" => "KOBU",

      "ADM" => "KNBA",
      "BKF" => "KNBA",
      "CHG" => "KNBA",
      "UNE" => "KNBA",
      "OTF" => "KNBA",

      "MTF" => "BEH",

      "WTH" => "DIVBE",

      "INE" => "RENME",
      "CIP" => "RENME",
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
      $gb = "KOBU";
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

function do_STUKMUT()  // Deponering/Lichting van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "STUKMUT";
  do_algemeen($data[19]."MEM");

  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }

  if ($data["aantal"] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers($fonds["Valuta"]);
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data["aantal"];
    $mr["Fondskoers"]        = $data["koers"];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag          += ($mr["Debet"] * -1);
    $mr["Transactietype"]    = "D";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    $output[] = $mr;



    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Grootboekrekening"] = "STORT";


    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers($fonds["Valuta"]);
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data["aantal"] ;
    $mr["Fondskoers"]        = $data["koers"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag          += $mr["Credit"];
    $mr["Transactietype"]    = "L";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    $output[] = $mr;



    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Grootboekrekening"] = "ONTTR";


    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * -1;


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

  }
//  $controleBedrag         += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,$data["nettoBedrag"] * -1);
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
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }
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
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }
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
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }

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

function do_KAPUITK()  //kapitaal uitkering
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "KAPUITK";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }

  $mr["Omschrijving"]      = $data[12]." ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $bBedrag = $data["nettoBedrag"];
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
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }

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
  if ($rekRec  = getRekening($data["cashRekening"].$data["cashAfrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }



  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["boekValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["cashAfrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoCash"] < 0)
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = abs($data["nettoCash"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedragCash();
  }
  else
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoCash"]);
    $mr["Bedrag"]            = _creditbedragCash();
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

//  kostenPosten();

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
  if ($rekRec  = getRekening($data["cashRekening"].$data["cashAfrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["boekValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["cashAfrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoCash"] < 0)
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = abs($data["nettoCash"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedragCash();
  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoCash"]);
    $mr["Bedrag"]            = _creditbedragCash();
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

//  kostenPosten();


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
  if ($rekRec  = getRekening($data["cashRekening"].$data["cashAfrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["boekValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["cashAfrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["bedragCashMut"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["bedragCashMut"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedragCash();
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedragCashMut"]);
    $mr["Bedrag"]            = _creditbedragCash();
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
  global $meldArray, $data, $row;

  $meldArray[] = "regel ".$row.":<b> overgeslagen ({$data["omschrijving"]} - {$data["transactieCode"]})</b>";
  return true;
}

function do_error()
{
	global $do_func,$transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


