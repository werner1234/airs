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

  $data["rekening"]             = trim($data[3]);
  $data["portefeuille"]         = trim($data[2]);
  $data["afrekenValuta"]        = trim($data[25]);
  $data["omschrijving"]         = $data[28];
  $data["boekdatum"]            = _cnvDate($data[9]);
  $data["settledatum"]          = _cnvDate($data[10]);
  $data["isin"]                 = $data[7];
  $data["bankCode"]             = trim($data[6]);
  $data["fondsValuta"]          = $data[8];
  $data["aantal"]               = $data[11];
  $data["brutoBedrag"]          = $data[13];
  $data["nettoBedrag"]          = $data[24];
  $data["transactieId"]         = $data[1];
  $data["transactieCode"]       = $data[5];
  $data["koers"]                = $data[12];
  $data["opgelopenRente"]       = $data[22];
  $data["provisie"]             = $data[18];
  $data["brokerKosten"]         = $data[16];
  $data["taxes"]                = $data[20];
  $data["storno"]               = $data[26];
  $data["gestorneerdId"]        = $data[27];

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
//   list($year, $month, $day) = explode("-", $in);
//   return "{$year}-{$month}-{$day}";
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

  $fonds = array();

  $bankCode = $data["bankCode"];

  if(in_array($data['transactieCode'], array('DEP-DIVR', 'WDRL-DIVR', 'DEP-INTR', 'WDRL-INTC')))
  {
    $bankCode = $data['bankCodeDiv'];
  }

  if ($bankCode)
  {
    $query = "SELECT * FROM Fondsen 
              WHERE {$set["bankCode"]} = '".$bankCode."' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($data["isin"]);

  if($ISIN != "" && !in_array($data['transactieCode'], array('DEP-DIVR', 'WDRL-DIVR', 'DEP-INTR', 'WDRL-INTC')))
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
    $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds bankcode {$bankCode} (zonder ISIN) niet gevonden </span>";
    $error[]      = "$row: fonds bankcode ". $bankCode ." (zonder ISIN) niet gevonden ";
    return false;
  }

}

function getRekening($rekeningNr="")
{
  global $data, $error, $row, $depotBank, $meldArray;

  $db = new DB();

  $valuta = $data["afrekenValuta"] != '' ? $data["afrekenValuta"] : $data["afrekenValutaCash"];

  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]). $valuta;
  }

  $query = "SELECT * FROM Rekeningen 
            WHERE consolidatie = 0 
            AND `Depotbank` = '{$depotBank}'
            AND ( `RekeningDepotbank` = '{$rekeningNr}' OR
              (ifnull(`RekeningDepotbank`,'') = '' AND `Rekening` = '{$rekeningNr}')
              -- kijk eerst naar RekeningDepotbank
              -- leeg? vergelijk dan met het veld Rekening
            )";

  if ($rec = $db->lookupRecordByQuery($query) )
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
  global $meldArray, $__develop;
  if($__develop)
  {
    return false;
  }
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
// 10053 verplaatst naar algemeneFuncties
function checkControleBedragDeprecated($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $prefix = "regel {$mr["regelnr"]}: {$mr["Rekening"]} --> bedrag";
  if ( $controleBedrag <> $notabedrag )
  {
    $meldArray[] = "{$prefix} sluit niet aan bank= {$notabedrag} / AIRS = {$controleBedrag} / verschil = ".round($notabedrag - $controleBedrag, 3);
  }
  else
  {
    $meldArray[] = "{$prefix} sluit aan ";
  }

}

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
	if ( checkVoorDubbelInRM($mr)) { return; }

  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutaKoersAIRS();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data["aantal"];
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($data["brutoBedrag"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $calculatedBedrag        = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]) * -1;

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
    $parts = explode("Total Charges ", $data["omschrijving"]);
    $kosten = 0;
    if (count($parts) == 2)
    {
      $kostenParts = explode(" ", $parts[1]);
      $kosten = $kostenParts[0];
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

  checkControleBedrag($calculatedBedrag, $controleBedrag );
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
  if ( checkVoorDubbelInRM($mr)) { return; }

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($data["brutoBedrag"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $calculatedBedrag        = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);

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
    $parts = explode("Total Charges ", $data["omschrijving"]);
    $kosten = 0;
    if (count($parts) == 2)
    {
      $kostenParts = explode(" ", $parts[1]);
      $kosten = $kostenParts[0];
    }
  }



  $mr["Credit"]             = 0;
  $mr["Debet"]              = abs($kosten);
  $mr["Bedrag"]             = _debetbedrag();

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
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

  checkControleBedrag($calculatedBedrag, $controleBedrag);
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
  if ( checkVoorDubbelInRM($mr)) { return; }

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
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

  if ($mr["Bedrag"] == 0)
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
  if ( checkVoorDubbelInRM($mr)) { return; }

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";

  $mr["Valuta"]            = $data["afrekenValuta"];
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

  if ( checkVoorDubbelInRM($mr)) { return; }

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
  if ( checkVoorDubbelInRM($mr)) { return; }

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
  if ( checkVoorDubbelInRM($mr)) { return; }

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

//  if(!empty($data[4]))
//  {
//    return;
//  }
//  if (stristr($data["omschrijving"], "within account"))
//  {
//    return; // skip interne boeking zijn per saldo altijd 0
//  }




  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr)) { return; }

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $brutoBedrag = $data['aantal']; // bruto bedrag zet in aantal column


  if ($data["nettoBedrag"] > 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);

}


function do_STUKMUT()
{
  global $data;
  if ($data["aantal"] > 0)
  {
    do_L();
  }
  else
  {
    do_D();
  }
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
  if ( checkVoorDubbelInRM($mr)) { return; }

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
  do_algemeen($data["rekening"]."MEM");

  if ( checkVoorDubbelInRM($mr)) { return; }

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


