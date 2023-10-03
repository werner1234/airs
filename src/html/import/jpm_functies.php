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
  /*
  From Date;To Date;Account Number;Full Account Number;IBAN;
  Account Name;Type Of Transaction;Instrument Type Code;Instrument Type Description;Transaction Number;Olympic Transaction Code;Transaction Description;Instrument Short Name;Instrument Long Name;Valoren;Valoren Suffix;ISIN;CUSIP;SEDOL;Ticker;Pricing Currency;Base Currency;Transaction Currency;Quantity;Trade Date;Value Date;System Date;Transaction Price;Transaction To Base FX Rate;Price in Base Currency;Net Transaction Amount;Net Transaction Amount in Base;Accrued Interest;Accrued Interest in Base;Interest Rate;Gross Transaction Amount;Brokerage Fees;Counterparty Fees;JPM Brokerage Fees;JPM Fees;Swiss Tax;Withholding Tax;Cash Currency;Cash Amount;Counterparty Name;Reversal Flag;Reversal Reference;Marketplace;Asset Classification 1;Asset Classification 2;Asset Classification 3;Current Face;Factor;Maturity Date;Ex Date;Payable Date;Dividend/Coupon Rate
  */


   

  $data["portefeuille"]         = trim($data[3]);
  $data["rekening"]             = trim($data[3]);

  $data["transactieId"]         = $data[6];
  $data["transactieCode"]       = $data[7];
  $data["omschrijving"]         = $data[8];

  $data["isin"]                 = $data[10];
  $data["bankCode"]             = trim($data[32]);

  $data["fondsValuta"]          = $data[34];
  $data["afrekenValuta"]        = trim($data[44]);
  $data["fxValuta"]             = trim($data[11]);
  $data["aantal"]               = (float)$data[12];
  $data["settledatum"]          = _cnvDate($data[14]);
  $data["boekdatum"]            = _cnvDate($data[13]);

  $data["koers"]                = (float)$data[15];
  $data["valutakoers"]          = (float)$data[16];

  $data["nettoBedrag"]          = (float)$data[17];

  $data["opgelopenRente"]       = (float)$data[19];

  $data["brutoBedrag"]          = (float)$data[21];
  $data["brokerKosten"]         = (float)$data[38];
  $data["overigeKosten"]        = (float)$data[39];
  $data["brokerKostenJPM"]      = (float)$data[40];
  $data["overigeKostenJPM"]     = (float)$data[41];
  $data["taxesSwiss"]           = (float)$data[42];
  $data["taxes"]                = (float)$data[43];
  
  $data["storno"]               = $data[47];
  $data["gestorneerdId"]        = $data[48];
 debug($data);
}

function _cnvNumber($in)
{
  global $set;
  $in = str_replace($set["thousandSign"], "", $in);
  return str_replace($set["decimalSign"], ".", $in);
}

function _cnvDate($in)
{
   return str_replace(".", "-", $in);
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

  if($ISIN != "" )
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

  $valuta = $data["afrekenValuta"] != '' ? $data["afrekenValuta"] : $data["afrekenValutaCash"];

  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]). $valuta;
  }

  $query = "SELECT * FROM Rekeningen 
            WHERE consolidatie = 0 AND 
                  `RekeningDepotbank` = '{$rekeningNr}' AND 
                  `Depotbank` = '".$depotBank."' ";

  if ($rec = $db->lookupRecordByQuery($query) )
  {

    return $rekeningNr;
  }

  $query = "SELECT * FROM Rekeningen WHERE 
            consolidatie = 0 AND 
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
	$mr["bestand"]            = $_file;
	$mr["regelnr"]            = $row;
	$mr["bankTransactieId"]   = $data["transactieId"];
	$mr["bankTransactieCode"] = $data["transactieCode"];
	$mr["Boekdatum"]          = $data["boekdatum"];
  $mr["settlementDatum"]    = $data["settledatum"];

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
    $meldArray[] = "{$prefix} sluit niet aan bank= {$notabedrag} / AIRS = {$controleBedrag} / verschil = ".round($notabedrag - $controleBedrag, 3);
  }
  else
  {
    $meldArray[] = "{$prefix} sluit aan ";
  }

}

function kostenPosten()
{
  global $data;

  if ($data["brokerKosten"] != 0)     { boekKosten("KOBU", $data["brokerKosten"]);  }
  if ($data["overigeKosten"] != 0)    { boekKosten("KOBU", $data["overigeKosten"]);  }
  if ($data["brokerKostenJPM"] != 0)  { boekKosten("KOST", $data["brokerKostenJPM"]);  }
  if ($data["overigeKostenJPM"] != 0) { boekKosten("KOST", $data["overigeKostenJPM"]);  }
  if ($data["taxesSwiss"] != 0)       { boekKosten("KOBU", $data["taxesSwiss"]);  }
  if ($data["taxes"] != 0)            { boekKosten("KOBU", $data["taxes"]);  }

}

function boekKosten($grootboek, $bedrag)
{
  global $mr;
  $mr["Grootboekrekening"]  = $grootboek;
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;

  if ($bedrag < 0)
  {
    $mr["Credit"]           = 0;
    $mr["Debet"]            = abs($bedrag);
    $mr["Bedrag"]           = _debetbedrag();
  }
  else
  {
    $mr["Debet"]            = 0;
    $mr["Credit"]           = abs($bedrag);
    $mr["Bedrag"]           = _creditbedrag();
  }

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

  checkControleBedrag($controleBedrag,$data["nettoBedrag"] * -1);
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
//  debug($data);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
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

  checkControleBedrag($controleBedrag,-1 * $data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
//debug($data);
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

  $bBedrag = $data["nettoBedrag"] ;
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

  boekKosten("DIVBE", $data["taxes"]);

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


//function do_FX()  //FX mutaties
//{
//
//  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
//
//  $mr = array();
//  $mr["aktie"]              = "FX";
//  do_algemeen();
//  checkVoorDubbelInRM($mr);
//
//  $controleBedrag = 0;
//  $mr["Omschrijving"]    = $data["omschrijving"];
//  if ($rekRec  = getRekening(trim($data["rekening"]). $data["fxValuta"]) )
//  {
//    $mr["Rekening"] = $rekRec;
//  }
//
//  $mr["Valuta"]            = $data["fxValuta"];
//  $mr["Valutakoers"]       = _valutakoers($data["fxValuta"]);
//  $mr["Fonds"]             = "";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Grootboekrekening"] = "FX";
//  if ($data["nettoBedrag"] < 0)
//  {
//    $mr["Debet"]             = abs($data["nettoBedrag"]);
//    $mr["Credit"]            = 0;
//    $mr["Bedrag"]            = -1 * $mr["Debet"];
//  }
//  else
//  {
//    $mr["Debet"]             = 0;
//    $mr["Credit"]            = abs($data["nettoBedrag"]);
//    $mr["Bedrag"]            = $mr["Credit"];
//  }
//
//
//  $mr["Transactietype"]    = "";
//  $mr["Verwerkt"]          = 0;
//  $mr["Memoriaalboeking"]  = 0;
//  $controleBedrag         += $mr["Bedrag"];
//
//  $output[] = $mr;
//
//  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
//
//}
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
  $mr["Valutakoers"]       = $data["valutakoers"];
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
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
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


function do_BEW()  //bewaarloon
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "BEW";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "BEW";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "BEW";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
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


function do_RENTE()  //rente
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
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
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


function do_KNBA()  //bankkosten
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "KNBA";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
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



function do_NVT()
{
  return true;
}

function do_error()
{
	global $transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


