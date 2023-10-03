<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/07 07:57:03 $
 		File Versie					: $Revision: 1.3 $

 		$Log: airsTempl_functies.php,v $
 		Revision 1.3  2020/07/07 07:57:03  cvs
 		call 8728
 		
 		Revision 1.2  2020/07/01 15:13:12  cvs
 		call 8728
 		
 		Revision 1.1  2020/06/29 13:55:59  cvs
 		call 8728
 		


*/

function cnvNumber($in)
{
  return str_replace(",",".", $in);
}

function _debetbedrag()
{
  global $data, $mr;
  $rekeningValuta = substr($data["rekening"], -3);
  $fondsValuta    = $mr["Valuta"];
  return ($rekeningValuta == $fondsValuta )?-1 * $mr["Debet"]:-1 * ($mr["Debet"] * $mr["Valutakoers"]);

}

function _creditbedrag()
{
  global $data, $mr;
  $rekeningValuta = substr($data["rekening"], -3);
  $fondsValuta    = $mr["Valuta"];
  return ($rekeningValuta == $fondsValuta )?$mr["Credit"]:($mr["Credit"] * $mr["Valutakoers"]);
}


function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;

	$rekeningValuta = substr($data["rekening"], -3);
	$fondsValuta    = $mr["Valuta"];

  if ($rekeningValuta == "EUR" AND $fondsValuta == "EUR")
  {
    return 1;
  }

  if ($rekeningValuta == "EUR" AND $fondsValuta != "EUR" AND (float)$data["wisselkoers"] != 0)
  {
    return $data["wisselkoers"];
  }
  else
  {
    if ($data["wisselkoers"] == 1 OR (float)$data["wisselkoers"] == 0)
    {
      $db = new DB();
      $query = "
      SELECT 
        * 
      FROM 
        Valutakoersen 
      WHERE 
        Valuta='{$fondsValuta}' AND 
        Datum <= '{$mr["Boekdatum"]}' 
      ORDER BY 
        Datum DESC";
      $laatsteKoers = $db->lookupRecordByQuery($query);
      return $laatsteKoers["Koers"];
    }
    else
    {
      return $data["wisselkoers"];
    }

  }
}

function getFonds()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();

  $fonds = array();
  if (trim($data["fonds"]) != "")
  {

    $query = "SELECT * FROM Fondsen WHERE Fonds = '".trim($data["fonds"])."' ";

    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return $fonds;
    }
    else
    {
      $error[] = "$row: airsfonds ".$data["fonds"]."  niet gevonden ";
      return false;
    }
  }

  $ISIN = trim($data["isin"]);

  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$ISIN}' AND Valuta ='{$data["valuta"]}' ";
    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return $fonds;
    }
    else
    {
      $error[] = "$row: fonds $ISIN/".$data["valuta"]." niet gevonden ";
      return false;
    }
  }

}


function getRekening()
{
  global $data;

  $db = new DB();

  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `Rekening` = '{$data["rekening"]}'  ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    return false;
  }

}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;


  $datum = explode("-",$data["datum"]);
	$mr["Boekdatum"]         = $datum[2]."-".$datum[1]."-".$datum[0];
  $mr["settlementDatum"]   = $datum[2]."-".$datum[1]."-".$datum[0];

  $mr["bankTransactieId"]  = $datum[2].$datum[1].$datum[0].$data["rekening"].$row;
}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_Aankoop()  // Aankoop van stukken
{
  global $data, $mr, $output , $fonds, $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "A";
  do_algemeen();

  $mr["Rekening"]       = getRekening();
  $fonds = getFonds();

  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];

  $mr["Credit"]          = 0;
  $mr["Debet"]           = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]          = _debetbedrag();

  $controleBedrag += $mr["Bedrag"];
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Transactietype"]    = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KOST";

  if ($data["kostenFV"] < 0)
  {
    $mr["Credit"]            = abs($data["kostenFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["kostenFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  $mr["Grootboekrekening"] = "KOBU";

  if ($data["belastingFV"] < 0)
  {
    $mr["Credit"]            = abs($data["belastingFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENME";

  if ($data["opgelopenRenteFV"] > 0)
  {
    $mr["Credit"]            = abs($data["opgelopenRenteFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["opgelopenRenteFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  //rekval

  $mr["Valuta"]              = substr($mr["Rekening"],-3);
  $mr["Grootboekrekening"]   = "KOST";
  $mr["Valutakoers"]         = _valutakoers();

  if ($data["kostenRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["kostenRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["kostenRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  $mr["Grootboekrekening"] = "KOBU";

  if ($data["belastingRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["belastingRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENME";

  if ($data["opgelopenRenteRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["opgelopenRenteRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["opgelopenRenteRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag, $data["nettoRekVal"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_Verkoop()  // Verkoop van stukken
{
  global $data, $mr, $output , $fonds, $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "V";
  do_algemeen();

  $mr["Rekening"]       = getRekening();
  $fonds = getFonds();

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];

  $mr["Debet"]           = 0;
  $mr["Credit"]          = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]          = _creditbedrag();

  $controleBedrag += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Transactietype"]    = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KOST";

  if ($data["kostenFV"] < 0)
  {
    $mr["Credit"]            = abs($data["kostenFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["kostenFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  $mr["Grootboekrekening"] = "KOBU";

  if ($data["belastingFV"] < 0)
  {
    $mr["Credit"]            = abs($data["belastingFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENOB";

  if ($data["opgelopenRenteFV"] > 0)
  {
    $mr["Credit"]            = abs($data["opgelopenRenteFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["opgelopenRenteFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  //rekval

  $mr["Valuta"]              = substr($mr["Rekening"],-3);
  $mr["Grootboekrekening"]   = "KOST";
  $mr["Valutakoers"]         = _valutakoers();

  if ($data["kostenRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["kostenRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["kostenRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  $mr["Grootboekrekening"] = "KOBU";

  if ($data["belastingRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["belastingRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENME";

  if ($data["opgelopenRenteRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["opgelopenRenteRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["opgelopenRenteRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag, $data["nettoRekVal"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_Coupon()  //Rente of couponrente
{
  global $data, $mr, $output , $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "COUP";
  do_algemeen();

  $mr["Rekening"]       = getRekening();
  $fonds = getFonds();

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $data["valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["brutoFV"] < 0)
  {
    $mr["Credit"]          = 0;
    $mr["Debet"]           = abs($data["brutoFV"]);
    $mr["Bedrag"]          = _debetbedrag();
  }
  else
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data["brutoFV"]);
    $mr["Bedrag"]          = _creditbedrag();
  }
  $controleBedrag += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";

  if ($data["belastingFV"] < 0)
  {
    $mr["Credit"]            = abs($data["belastingFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // belasting op rentevaluta
  $mr["Valuta"]              = substr($mr["Rekening"],-3);
  $mr["Valutakoers"]         = _valutakoers();
  if ($data["belastingRekVal"] < 0 )

  {
    $mr["Credit"]            = abs($data["belastingRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoRekVal"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_Dividend()  //Contant dividend
{
  global $data, $mr, $output , $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DIV";
  do_algemeen();

  $mr["Rekening"]       = getRekening();
  $fonds = getFonds();

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $data["valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["brutoFV"] < 0)
  {
    $mr["Credit"]          = 0;
    $mr["Debet"]           = abs($data["brutoFV"]);
    $mr["Bedrag"]          = _debetbedrag();
  }
  else
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data["brutoFV"]);
    $mr["Bedrag"]          = _creditbedrag();
  }
  $controleBedrag += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "KNBA";

  if ($data["kostenFV"] < 0)
  {
    $mr["Credit"]            = abs($data["kostenFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["kostenFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // belasting op rentevaluta
//  $mr["Valuta"]              = substr($mr["Rekening"],-3);
//  $mr["Valutakoers"]         = _valutakoers();
  if ($data["kostenRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["kostenRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["kostenRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "DIVBE";

  if ($data["belastingFV"] < 0)
  {
    $mr["Credit"]            = abs($data["belastingFV"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingFV"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // belasting op rentevaluta
  $mr["Valuta"]              = substr($mr["Rekening"],-3);
  $mr["Valutakoers"]         = _valutakoers();
  if ($data["belastingRekVal"] < 0)
  {
    $mr["Credit"]            = abs($data["belastingRekVal"] );
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["belastingRekVal"] );
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = _debetbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoRekVal"]);
}


function do_Bankkosten()  {   do_mut("KNBA");   }
function do_Beheerloon()  {   do_mut("BEH");    }
function do_Bewaarloon()  {   do_mut("BEW");    }
function do_Kruispost()   {   do_mut("KRUIS");  }
function do_Onttrekking() {   do_mut("ONTTR");  }
function do_Storting()    {   do_mut("STORT");  }
function do_Rente()       {   do_mut("RENTE");  }

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_mut($grootboek)
{
  global $data, $mr, $output , $meldArray;
	$mr = array();

	$mr["aktie"]              = "MUT";
	do_algemeen();

	$mr["Rekening"]       = getRekening();

	$mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = $grootboek;
  $mr["Valuta"]            = $data["valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["brutoFV"] < 0)
  {
    $mr["Credit"]          = 0;
    $mr["Debet"]           = abs($data["brutoFV"]);
    $mr["Bedrag"]          = _debetbedrag();
  }
  else
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data["brutoFV"]);
    $mr["Bedrag"]          = _creditbedrag();
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  checkControleBedrag($mr["Bedrag"], $data["nettoRekVal"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>