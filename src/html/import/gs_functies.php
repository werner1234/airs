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

  $data["rekening"]             = trim($data[1]);

  $data["afrekenValutaCash"]    = trim($data[2]);
  $data["afrekenValuta"]        = ($data["afrekenValutaCash"] != "")?$data["afrekenValutaCash"]:trim($data[15]);
  $data["omschrijving"]         = $data[20];
  $data["boekdatum"]            = _cnvDate($data[11]);
  $data["settledatum"]          = _cnvDate($data[12]);
  $data["isin"]                 = $data[5];
  $data["bankCode"]             = trim($data[3]);
  $data["bankCodeDiv"]          = trim($data[19]);
  $data["fondsValuta"]          = $data[15];
  $data["aantal"]               = $data[13] * 1;
  $data["nettoBedrag"]          = $data[14] * 1;
//  $data["valutakoersRekFonds"]  = $data[41];
//  $data["valutakoersFondsEur"]  = ($data[46] != 0)?1/$data[46]:1;
  $data["transactieId"]         = $data[23];
  $data["transactieCode"]       = $data[9]."-".$data[10];
  $data["koers"]                = $data[18];
  $data["opgelopenRente"]       = $data[17];
  $data["provisie"]             = $data[16];
//  $data["brokerKosten"]         = $data[34];
//  $data["taxes"]                = $data[35];
//  $data["OverigeKosten"]        = $data[36];
//  $data["FTT"]                  = $data[37];
  $data["storno"]               = $data[21];
  $data["gestorneerdId"]        = $data[22];

  $data["kost"]                 = $data[16];
  $data["opgelopenRente"]       = $data[17];


  $data["valuta1"] = trim($data[2]);
  $data["valuta2"] = trim($data[15]);
  $data["bedrag1"] = $data[13] * 1;
  $data["bedrag2"] = $data[14] * 1;
  $data["fxOmschrijving"]   = $data[7] .' - '. $data[2] .' - '. $data[15];

}

function _cnvNumber($in)
{
  global $set;
  $in = str_replace($set["thousandSign"], "", $in);
  return str_replace($set["decimalSign"], ".", $in);
}

function _cnvDate($in)
{
   return substr($in,4,4)."-".substr($in,0,2)."-".substr($in,2,2);
}

function getTransactieMapping()
{
  global $set, $transactieCodes, $transactieMapping;
  $db = new DB();
  $query = "SELECT bankCode, doActie FROM {$set["transactieCodes"]}";

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

  $divArray = array('DEP-DIVR', 'WDRL-DIVR', 'DEP-INTR', 'WDRL-INTC', 'WDRL-FEES', 'REC-WIRI');
  $exludeCashISINArray = array(  // ISIN code found in CASH rows
    "EUD018980004",
    "USD018400003",
    "JPD013920004",
  );


  $fonds = array();

  // checken of het een cash boeking is zonder Fonds info
  if (  in_array($data["isin"], $exludeCashISINArray)
        AND
        (
          $data["bankCodeDiv"] == $data["bankCode"] OR
          $data["bankCodeDiv"] == ""
        )
     )
  {
    return true;
  }

  $bankCode = $data["bankCode"];

  if(in_array($data['transactieCode'], $divArray))
  {
    $bankCode = $data['bankCodeDiv'];
  }

  if ($data["bankCodeDiv"] == $data["bankCode"] AND in_array($data['transactieCode'], $divArray))
  {
    return true;  // bij CASH is er geen fonds dus exit
  }

  if ($bankCode)
  {
    $query = "SELECT * FROM Fondsen WHERE {$set["bankCode"]} = '".$bankCode."' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($data["isin"]);

  if($ISIN != "" AND !in_array($data['transactieCode'], $divArray))
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$ISIN}' AND Valuta ='{$data["fondsValuta"]}' ";

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
    $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds bankcode {$bankCode} (zonder ISIN) niet gevonden </span>";
    $error[]      = "$row: fonds bankcode ". $bankCode ." (zonder ISIN) niet gevonden ";
    return false;
  }

}

function getRekening($rekeningNr="")
{
  global $data, $error, $row, $depotBank, $meldArray;

  $db = new DB();

  $valuta = ($data["afrekenValutaCash"] != '')?$data["afrekenValutaCash"]:$data["afrekenValuta"];

  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]). $valuta;
  }

  $query = "
  SELECT 
    * 
  FROM 
    Rekeningen 
  WHERE 
    `consolidatie` = 0 AND 
    `RekeningDepotbank` = '{$rekeningNr}' AND 
    `Depotbank` = '".$depotBank."' ";

  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return $rekeningNr;
  }

  $query = "
  SELECT 
    * 
  FROM 
    Rekeningen 
  WHERE 
    `consolidatie` = 0 AND 
    `Rekening` = '{$rekeningNr}' AND 
    `Depotbank` = '{$depotBank}' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rekeningNr;
  }

  $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: rekening {$rekeningNr} niet gevonden </span>";
  $error[]     = "{$row}: rekening {$rekeningNr} niet gevonden ";
  return false;

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

function _valutakoersAIRS()
{
  global $mr;

  $db = new DB();
  $query = "
    SELECT 
      * 
    FROM 
      Valutakoersen 
    WHERE 
      Valuta='{$mr["Valuta"]}' AND 
      Datum <= '{$mr["Boekdatum"]}' 
    ORDER BY 
      Datum DESC";

  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
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
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='{$mr["Valuta"]}' AND Datum <= '{$mr["Boekdatum"]}' ORDER BY Datum DESC";
      $laatsteKoers = $db->lookupRecordByQuery($query);
      return $laatsteKoers["Koers"];
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

//function checkControleBedrag($controleBedrag,$notabedrag)
//{
//  global $meldArray, $data, $mr;
//
//  $controleBedrag = round($controleBedrag,2);
//  $notabedrag     = round($notabedrag,2);
//  $prefix = "regel {$mr["regelnr"]}: {$mr["Rekening"]} --> bedrag";
//  if ( $controleBedrag <> $notabedrag )
//  {
//    $meldArray[] = "{$prefix} sluit niet aan bank= {$notabedrag} / AIRS = {$controleBedrag} / verschil = ".round($notabedrag - $controleBedrag, 3);
//  }
//  else
//  {
//    $meldArray[] = "{$prefix} sluit aan ";
//  }
//
//}

function kostenPosten($gbMap = "")
{
  global $meldArray, $mr, $data, $controleBedrag, $output;

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
//  debug($data);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutaKoersAIRS();
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

  $mr["Grootboekrekening"]  = "KOST";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;



  if ($data["kost"] != 0)
  {
    $kosten = $data["kost"];
  }
  else
  {
    $kosten  = 0;
    $parts   = array();
    if (stristr($data["omschrijving"], "Total Charges "))
    {
      $parts = explode("Total Charges ", $data["omschrijving"]);
    }
    else
    {
      $parts = explode("Commission ", $data["omschrijving"]);
    }
    if (count($parts) == 2)
    {
      $kostenParts = explode(" ", $parts[1]);
      $kosten      = $kostenParts[0];
    }

  }

  $mr["Credit"]           = 0;
  $mr["Debet"]            = abs($kosten);
  $mr["Bedrag"]           = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"]  = "RENME";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Credit"]             = 0;
  $mr["Debet"]              = abs($data["opgelopenRente"]);
  $mr["Bedrag"]             = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,-1 * $data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_CORP()  // Vernkoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "CORP";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  if ($fonds["fondssoort"] == "OBL")
  {
    $mr["Omschrijving"]      = "Lossing ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = 1/$fonds["Fondseenheid"];
  }
  else
  {
    $mr["Omschrijving"]      = "Distributie ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = $data["koers"];
  }
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];

  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"]  = "KOST";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  if ($data["kost"] != 0)
  {
    $kosten = $data["kost"];
  }
  else
  {
    $kosten  = 0;
    $parts   = array();

    if (stristr($data["omschrijving"], "Total Charges "))
    {
      $parts = explode("Total Charges ", $data["omschrijving"]);
    }
    else
    {
      $parts = explode("Commission ", $data["omschrijving"]);
    }
    if (count($parts) == 2)
    {
      $kostenParts = explode(" ", $parts[1]);
      $kosten      = $kostenParts[0];
    }
  }



  $mr["Credit"]             = 0;
  $mr["Debet"]              = abs($kosten);
  $mr["Bedrag"]             = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
//  debug($mr);
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"]  = "RENOB";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Credit"]             = 0;
  $mr["Debet"]              = abs($data["opgelopenRente"]);
  $mr["Bedrag"]             = _debetbedrag();

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
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"]  = "KOST";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  if ($data["kost"] != 0)
  {
    $kosten = $data["kost"];
  }
  else
  {
    $kosten  = 0;
    $parts   = array();

    if (stristr($data["omschrijving"], "Total Charges "))
    {
      $parts = explode("Total Charges ", $data["omschrijving"]);
    }
    else
    {
      $parts = explode("Commission ", $data["omschrijving"]);
    }
    if (count($parts) == 2)
    {
      $kostenParts = explode(" ", $parts[1]);
      $kosten      = $kostenParts[0];
    }
  }



  $mr["Credit"]             = 0;
  $mr["Debet"]              = abs($kosten);
  $mr["Bedrag"]             = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
//  debug($mr);
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"]  = "RENOB";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  $mr["Credit"]             = abs($data["opgelopenRente"]);
  $mr["Debet"]              = 0;
  $mr["Bedrag"]             = _creditbedrag();

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

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DIV";
  do_algemeen();
  checkVoorDubbelInRM($mr);

//debug($data);
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];

  $mr["Valuta"]            = $fonds["Valuta"];;

  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $brutoBedrag = $data["aantal"];

  switch($data['transactieCode'])
  {
    case "DEP-DIVR":
      $mr["Grootboekrekening"] = "DIV";
      break;

    case "WDRL-DIVR":
      $mr["Grootboekrekening"] = "DIVBE";
      $brutoBedrag = -1 * $brutoBedrag;
      if (stristr($data['omschrijving'], 'RECLAIMABLE'))
      {
        $mr["Omschrijving"] = "Dividend RECLAIMABLE {$fonds["Omschrijving"]}";
      }
      break;
    default:
      break;
  }

  if ($brutoBedrag > 0)
  {
    $mr["Credit"]            = abs($brutoBedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($brutoBedrag);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  kostenPosten();

  checkControleBedrag($controleBedrag, $brutoBedrag);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENOB()  //Coupon
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  if($data['bankCode'] == $data['bankCodeDiv'])
  {
    do_R();
    return;
  }

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENOB";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $brutoBedrag = $data['aantal']; // bruto bedrag zet in aantal column

  if ($brutoBedrag > 0)
  {
    $mr["Credit"]            = abs($brutoBedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($brutoBedrag);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$brutoBedrag);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{
  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "FX";
  do_algemeen();

  checkVoorDubbelInRM($mr);

  if ($data["valuta1"] != "EUR" AND $data["valuta2"] != "EUR")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": FX boeking zonder EUR handmatig boeken";
    return false;
  }

  $rekRec1 = getRekening($data["rekening"].$data["valuta1"]);
  $rekRec2 = getRekening($data["rekening"].$data["valuta2"]);

  if ($data["valuta1"] == "EUR" AND $data["valuta2"] != "EUR" && $rekRec1)
  {
    $mr["Rekening"] = $rekRec1;
    $mr["Valuta"]            = $data["valuta1"];
    $mr["Valutakoers"]       = 1;
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";

    if ($data["transactieCode"] == 'BUY-CURP'){
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag1"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
    }

    if ($data["transactieCode"] == 'SALE-CURS'){
      $mr["Debet"]             = abs($data["bedrag1"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }

    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

  }

  if ($data["valuta1"] == "EUR" AND $data["valuta2"] != "EUR" && $rekRec2)
  {
    $mr["Rekening"] = $rekRec2;
    $mr["Valuta"]            = $data["valuta2"];
    $mr["Valutakoers"]       = abs($data["bedrag1"]/$data["bedrag2"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";

    if ($data["transactieCode"] == 'BUY-CURP'){
      $mr["Debet"]             = abs($data["bedrag2"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }

    if ($data["transactieCode"] == 'SALE-CURS'){
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag2"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
    }

    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"] ;
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

  }

  if ($data["valuta1"] != "EUR" AND $data["valuta2"] == "EUR" && $rekRec1)
  {
    $mr["Rekening"] = $rekRec1;
    $mr["Valuta"]            = $data["valuta1"];
    $mr["Valutakoers"]       = abs($data["bedrag2"]/$data["bedrag1"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";

    if ($data["transactieCode"] == 'BUY-CURP'){
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag1"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
    }

    if ($data["transactieCode"] == 'SALE-CURS'){
      $mr["Debet"]             = abs($data["bedrag1"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }

    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }

  if ($data["valuta1"] != "EUR" AND $data["valuta2"] == "EUR" && $rekRec2)
  {
    $mr["Rekening"] = $rekRec2;
    $mr["Valuta"]            = $data["valuta2"];
    $mr["Valutakoers"]       = 1;
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";

    if ($data["transactieCode"] == 'BUY-CURP'){
      $mr["Debet"]             = abs($data["bedrag2"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }

    if ($data["transactieCode"] == 'SALE-CURS'){
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag2"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
    }

    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"] ;
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }

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
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $brutoBedrag = $data['aantal']; // bruto bedrag zet in aantal column

  $mr["Grootboekrekening"] = "RENTE";

  if (substr($data["transactieCode"],0,3) == "DEP")
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($brutoBedrag);
    $mr["Bedrag"]            = $mr["Credit"] ;
  }

  if (substr($data["transactieCode"],0,4) == "WDRL")
  {
    $mr["Debet"]             = abs($brutoBedrag);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag, $brutoBedrag);

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
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $brutoBedrag = $data['aantal']; // bruto bedrag zet in aantal column

  $mr["Grootboekrekening"] = "BEH";

  if ($data["transactieCode"] == 'DEP-FEES'){
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($brutoBedrag);
    $mr["Bedrag"]            = $mr["Credit"] ;
  }

  if ($data["transactieCode"] == 'WDRL-FEES'){
    $mr["Debet"]             = abs($brutoBedrag);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag, $brutoBedrag);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_GELDMUT()  //mutatie geld
{
  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  if(!empty($data[4]))
  {
    return;
  }
  if (stristr($data["omschrijving"], "within account"))
  {
    return; // skip interne boeking zijn per saldo altijd 0
  }

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValutaCash"];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $brutoBedrag = $data['aantal']; // bruto bedrag zet in aantal column

  $mr["Grootboekrekening"] = "BEH";


  $stortArray = array('DEP-CASD', 'DEP-OTHR', 'REC-WIRI');
  $onttrArray = array('WDRL-CASW', 'WDRL-OTHR', 'DEL-WIRO');

  if (in_array($data["transactieCode"], $stortArray))
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($brutoBedrag);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  if (in_array($data["transactieCode"], $onttrArray))
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($brutoBedrag);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag, $brutoBedrag);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////1
function do_L()
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "L";
  do_algemeen();
  if ($rekRec  = getRekening($data["rekening"]."MEM") )
  {
    $mr["Rekening"] = $rekRec;
  }
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"] * -1;
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
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
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Bedrag"]);
  $mr["Bedrag"]            = _creditbedrag($mr["Credit"]);
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $controleBedrag         += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,0);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_D()
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "D";
  do_algemeen($data[19]."MEM");
  if ( checkVoorDubbelInRM($mr))
  {
    return;
  }
  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"] * -1;
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
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
  $controleBedrag         += abs($mr["Bedrag"]);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



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


