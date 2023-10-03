<?
/*
    AE-ICT sourcemodule created 01 nov. 2019
    Author              : Chris van Santen
    Filename            : caw_functies.php

21-10 naar RVV
*/


function vlchNumber($in)
{
 // $in = str_replace(".", "", $in);
  return str_replace("'", "", $in);
}

function mapVlChData()
{
  global $data;

  // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

  $data["isin"]                 = $data[8];
  $data["bankCode"]             = $data[7];
  $data["rekening"]             = $data[6];
  $data["afrekenValuta"]        = $data[12];
  $data["omschrijving"]         = $data[5];
  $data["boekdatum"]            = $data[3];
  $data["settledatum"]          = $data[4];
  $data["aantal"]               = vlchNumber($data[10]);
  $data["valuta1"]              = $data[12];
  $data["valuta2"]              = $data[13];
  $data["fxBedragVV"]           = vlchNumber($data[10]);
  $data["fxBedragEUR"]          = vlchNumber($data[18]);
  $data["fxKoers"]              = 1/vlchNumber($data[16]);
  $data["fxOmschrijving"]       = $data[26];
  $data["nettoBedrag"]          = vlchNumber($data[15]);
  $data["valutakoers"]          = 1/vlchNumber($data[16]);

  $data["transactieId"]         = $data[1];
  $data["transactieCode"]       = $data[2]."-".$data[5];
  $data["fondsValuta"]          = $data[12];
  $data["koers"]                = vlchNumber($data[11]);
  $data["opgelopenRente"]       = vlchNumber($data[24]);
  $data["kost"]                 = vlchNumber($data[22]);
  $data["otherCost"]            = vlchNumber($data[25]);
  $data["income"]               = vlchNumber($data[20]);
  $data["tax"]                  = vlchNumber($data[23]);


}


function getFonds()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();

  $fonds = array();
//  if (trim($data[3]) <> 0 AND $data[6] != "CASH")
//  {
//    $bankcode = trim($data[3]);
//    $query = "SELECT * FROM Fondsen WHERE CAWcode = '".trim($bankcode)."' ";
//    if ($fonds = $DB->lookupRecordByQuery($query))
//    {
//      return true;
//    }
//  }

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

function getFondsDiv()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();



  $fonds = array();

  $ISIN = trim($data["isin"]);


  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$ISIN'  ";

    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
  }
  $error[] = "$row: DIV fonds $ISIN niet gevonden ";

}

function getRekening($fxValuta="")
{
  global $data, $error, $row;

	$depot = "FVLC";
  $db = new DB();
  if ($fxValuta)
  {
    $rekeningNr = trim($data["rekening"]).$fxValuta;
  }
  else
  {
    $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);
  }



  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      //$error[] = $mr["regelnr"]." Rekening niet gevonden ($rekeningNr)";
      return false;
    }
  }
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;

  $value = "FVLC|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr;

	if ($data["afrekenValuta"] == $mr["Valuta"] )
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr;

  if ($data["afrekenValuta"] == $mr["Valuta"] )
	  return $mr["Credit"];
	else
	  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers($rekValuta)
{
  global $data, $mr, $valutaLookup;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if ($data["valutakoers"] != 0)
  {
    return $data["valutakoers"];
  }

  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];

}

function checkVoorDubbelInRM($mr)
{
  global $meldArray;
  return false;
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
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEW()
{

  global $fonds, $data, $mr, $output, $meldArray, $afw;

  $mr = array();
  $mr["aktie"]              = "BEW";
  $controleBedrag           = 0;
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "BEW";
  $mr["Debet"]             = abs($data["nettoBedrag"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  $mr = $afw->reWrite("BEW",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"] * -1);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEH()
{

  global $fonds, $data, $mr, $output, $meldArray, $afw;

  $mr = array();
  $mr["aktie"]              = "BEH";
  $controleBedrag           = 0;
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "BEH";
  $mr["Debet"]             = abs($data["nettoBedrag"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  $mr = $afw->reWrite("BEW",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"] * -1);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_R()  // Rente
{

  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "RENTE";
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "RENTE";

  if ($data["aantal"] < 0)
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
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),abs($data["nettoBedrag"]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_KNBA()
{

  global $fonds, $data, $mr, $output, $meldArray, $afw;

  $mr = array();
  $mr["aktie"]              = "KNBA";
  $controleBedrag           = 0;
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "KNBA";
  $mr["Debet"]             = abs($data["nettoBedrag"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  $mr = $afw->reWrite("KNBA",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"] * -1);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()  //
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "FX";
  do_algemeen();
//  debug($data);
  checkVoorDubbelInRM($mr);

  if ($data["valuta1"] != "EUR" AND $data["valuta2"] != "EUR")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": <span style='color:red'><B>FX boeking zonder EUR handmatig boeken</B></span>";
    return false;
  }

  if ($data["valuta1"] == "EUR" AND $data["valuta2"] != "EUR")
  {
    if ($rekRec  = getRekening($data["valuta1"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta2"];
    $mr["Valutakoers"]       = $data["fxKoers"];
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] < 0)
    {
      $mr["Debet"]             = abs($data["fxBedragEUR"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"] * $data["fxKoers"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["fxBedragEUR"]);
      $mr["Bedrag"]            = $mr["Credit"] * $data["fxKoers"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

    if ($rekRec  = getRekening($data["valuta2"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta2"];
    $mr["Valutakoers"]       = $data["fxKoers"];
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] > 0)
    {
      $mr["Debet"]             = abs($data["fxBedragEUR"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"] ;
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["fxBedragEUR"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
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

  if ($data["valuta1"] != "EUR" AND $data["valuta2"] == "EUR")
  {
    if ($rekRec  = getRekening($data["valuta1"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta1"];
    $mr["Valutakoers"]       = $data["fxKoers"];
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] < 0)
    {
      $mr["Debet"]             = abs($data["fxBedragVV"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["fxBedragVV"]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

    if ($rekRec  = getRekening("EUR") )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta1"];
    $mr["Valutakoers"]       = $data["fxKoers"];
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] > 0)
    {
      $mr["Debet"]             = abs($data["fxBedragVV"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"] * $data["fxKoers"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["fxBedragVV"]);
      $mr["Bedrag"]            = $mr["Credit"] * $data["fxKoers"];
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





  checkControleBedrag($controleBedrag, $data[10] + $data[18]);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $afw;

  $mr = array();
  $mr["aktie"]              = "MUT";
  $controleBedrag           = 0;
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  if ($data["aantal"] < 0)
  {

    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
    $mr = $afw->reWrite("ONTTR",$mr);
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr = $afw->reWrite("STORT",$mr);
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,-1 * $data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  // Dividend
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "DIV";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0; //2022-03-11 verwijderd $data["koers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["income"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["tax"]) ;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KNBA";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kost"]);
  $mr["Bedrag"]            = _debetbedrag();
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

function do_RENOB()
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "COUP";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["income"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["tax"] + $data["kost"]);
  $mr["Bedrag"]            = _debetbedrag();
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

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray;
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

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kost"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["otherCost"] + $data["tax"]);
  $mr["Bedrag"]            = _debetbedrag();
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

function do_STUKMUT()
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "STUKMUT";
  do_algemeen();
  $mr["Rekening"] = "";
  if ($rekRec  = getRekening("MEM") )
  {
    $mr["Rekening"] = $rekRec;
  }
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];

  if ($data["aantal"] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Transactietype"]    = "D";
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Transactietype"]    = "L";
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }



  $controleBedrag       += $mr["Bedrag"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Valutakoers"]       = 1;
  $mr["Valuta"]            = "EUR";
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["aantal"] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "STORT";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = $mr["Bedrag"];
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = $mr["Bedrag"];
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  }
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec;
  }
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"] = "KNBA";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["kost"] < 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data["kost"]);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Credit"]            = abs($data["kost"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Grootboekrekening"] = "VKSTO";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["otherCost"] < 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs( $data["otherCost"]);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Credit"]            = abs($data["otherCost"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }


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

function do_V()  // Verkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray;
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
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($data["opgelopenRente"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kost"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["otherCost"] + $data["tax"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}


function do_NVT()
{
  return true;
}

function do_error()
{
	global $do_func,$transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


?>