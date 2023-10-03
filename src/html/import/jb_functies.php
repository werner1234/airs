<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/13 14:22:29 $
 		File Versie					: $Revision: 1.14 $

 		$Log: jb_functies.php,v $
 		Revision 1.14  2020/07/13 14:22:29  cvs
 		call 8680
 		
 		Revision 1.13  2020/06/17 10:08:19  cvs
 		call 8680
 		
 		Revision 1.12  2020/06/12 06:55:02  cvs
 		call 8680
 		
 		Revision 1.10  2019/04/15 14:30:17  cvs
 		call 7717
 		
 		Revision 1.9  2018/10/17 11:13:23  cvs
 		call 7230
 		
 		Revision 1.8  2018/10/17 10:46:14  cvs
 		call 7230
 		
 		Revision 1.7  2018/09/11 14:58:09  cvs
 		call 7130
 		
 		Revision 1.6  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/07/20 07:28:45  cvs
 		call 7054
 		
 		Revision 1.4  2018/06/15 07:28:15  cvs
 		call 5912
 		
 		Revision 1.3  2018/05/23 13:11:12  cvs
 		call 5912
 		
 		Revision 1.2  2018/05/01 06:13:10  cvs
 		call 5913
 		
 		Revision 1.1  2017/09/20 06:16:38  cvs
 		megaupdate
 		


*/

function trimRecord($data)
{
  foreach ($data as $key => $value)
  {
    if (trim($value) == ".00")
      $dataOut[] = 0;
    else
      $dataOut[] = trim($value);
  }
  return $dataOut;
}

function ontnullen($t)
{

}

function getRekening($rekeningNr="-1", $depot="Jul Baer")
{
  global $mr, $meldArray, $data, $row;
  $kbew = $data["KBEW"];

//  debug($rekeningNr, "voor ".$kbew["row"]);
  $rk = $rekeningNr;
  if (strlen($rekeningNr) < 12 AND substr($rekeningNr, -3) != "MEM")    // als IBAN niet gevuld
  {
    $rekeningNr =trim($kbew[3]).substr($rekeningNr,-3);
  }
//  if ($rk != $rekeningNr)
//    debug($rekeningNr, "na ".$kbew["row"]);
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
  if ($rec = $db->lookupRecordByQuery($query))
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
//      debug("error verwacht");
      logError($mr["regelnr"], "Rekening niet gevonden ($rekeningNr)");
      return false;
    }
    
  }
}

function RekeningNrFromIBAN($data)
{
  $data = str_replace(" ","", $data);
  return substr($data,-12);
}


function makeDate($datum)
{
  return substr($datum,0,4)."-".substr($datum,4,2)."-".substr($datum,6,2);
}


function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "JB|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $mr, $valutaLookup;

	if ($valutaLookup == true)
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
	global $mr, $valutaLookup;
  if ($valutaLookup == true)
  {
    return $mr["Credit"];
  }
  else
  {
    return  ($mr["Credit"] * $mr["Valutakoers"]);
  }
}


function _valutakoersAV($rekVal, $rowValue=0)
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$fndVal = $mr["Valuta"];
	$valutaLookup = false;
	if ( $rekVal == "EUR" AND $fndVal == $rekVal)  { return 1;   }

	if (
	     ($rekVal <> "EUR" AND $fndVal == $rekVal) OR
        $rowValue == "MEM"                                 )
	{
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$fndVal."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $DB->lookupRecordByQuery($query);
    $valutaLookup = true;
    return $laatsteKoers["Koers"];
	}
	else if ($rekVal == "EUR" AND $fndVal != "EUR")
  {
    return $rowValue;
  }
	else
	{
    return 0;
  }

}

function _valutakoers($rekVal, $rowValue=0)
{

  //$kbew[19]/$kbew[31]

	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $rekVal;
	$valutaLookup = false;
	if ($valuta == "EUR" AND $mr["Valuta"] == $valuta)
  {
    return 1;
  }

	if ( ($valuta <> "EUR" AND $mr["Valuta"] == $valuta) OR
        $rowValue == "MEM"                                 )
	{
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $DB->lookupRecordByQuery($query);
    $valutaLookup = true;
    return $laatsteKoers["Koers"];
	}
	return $rowValue;
}

function logError($txt, $regel=0)
{
  global $meldArray;
  $meldArray[] = "regel {$regel}: {$txt}";
}

function JB_getfonds($BankFondscode="",$isin="", $valuta="" )
{
  global $fonds, $fondsLookupResults, $mr;
  $db = new DB();
  $fondsLookupResults = array();

  // bankfonds code
  $fondsNotFound = true;

  if ($BankFondscode <> "")
  {
    $fondsLookupResults["bankcode"] = ontnullen($BankFondscode);
    $query = "SELECT * FROM Fondsen WHERE JBcode = '" . trim($BankFondscode) . "' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      $fondsNotFound = false;
    }
  }

  if ($fondsNotFound)        // isin/val
  {
    if ($isin <> "" AND $valuta <> "")
    {
      $fondsLookupResults["fonds"] = $isin;
      $fondsLookupResults["valutra"] = $valuta;
      $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$isin."' AND Valuta = '".$valuta."'  ";

      if (!$fonds = $db->lookupRecordByQuery($query))
      {
        logError("Fonds niet gevonden ".$isin."/".$valuta, $mr["regelnr"]);
        $fondsLookupResults["notFound"] = true;
      }
      else
      {
        $fondsNotFound = false;
      }
    }
    else
    {
      logError("Fonds niet gevonden via bankcode:  ".$BankFondscode, $mr["regelnr"]);
    }
  }
  return !$fondsNotFound;

}

function tryRente(){
  global $mr, $controleBedrag, $output, $kbew, $fonds;

  // only proceed in case of obligation
  if(strtoupper($fonds["fondssoort"])!="OBL"){ return; }

  // verwerk de rente
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;

  $renteBedrag = (float)$kbew[32];
  if ($renteBedrag < 0)
	{
	  $mr["Grootboekrekening"] = "RENME";
    $mr["Credit"]            = 0;
	  $mr["Debet"]             = abs($renteBedrag);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
	  $mr["Grootboekrekening"] = "RENOB";
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($renteBedrag);
    $mr["Bedrag"]            = _creditbedrag();
  }
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


}

function tryFees()
{
  global $mr, $row, $volgnr, $data, $feeMapping, $output, $controleBedrag;
  $gbData = array();

  foreach ($data["KBEW"]["kosten"] as $code=>$bedrag)
  {
    if (!array_key_exists($code, $feeMapping))
    {
      logError("FEE mapping niet gevonden voor code: ".$code , $mr["regelnr"]);
      continue;
    }
    else
    {
      $gbData[$feeMapping[$code]] += $bedrag;
    }
  }
  // verdicht per grootboek boeken
  if (count($gbData) > 0)
  {
    foreach ($gbData as $gb => $bedrag)
    {
      $mr["Grootboekrekening"] = $gb;
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($bedrag);
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag         += $mr["Bedrag"];
      $mr["Transactietype"]    = "";
//    debug($mr);
      if ($mr["Bedrag"] <> 0)
      {
        $output[] = $mr;
      }
    }
  }

}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;



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

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $_file, $controleBedrag, $dbew, $kbew;
  $controleBedrag = 0;

  if (count($data) != 2)
  {
    $d = array_merge($data["DBEW"], $data["KBEW"]);
    logError("do_a: zonder cash mutaties != 2 legs", $d["row"]);
    return false;
  }
//debug($data);
//  debug($fonds);

  $dbew = $data["DBEW"];
  $kbew = $data["KBEW"];

	$mr = array();
	$mr["aktie"]             = "A";
//	do_algemeen();

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $dbew["row"];
  $mr["bankTransactieId"]  = $dbew[37];

  $mr["Boekdatum"]         = makeDate($dbew[26]);
  $mr["settlementDatum"]   = makeDate($dbew[27]);

	//$mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
	$mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
	$mr["Rekening"]          = getRekening($mr["Rekening"]);
  JB_getfonds($dbew[9],$dbew[11],$dbew[25]);
  
	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAV($kbew[8],$kbew[19]/$kbew[31]);


	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $dbew[19];
	$mr["Fondskoers"]        = $dbew[22];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

  tryRente();
  tryFees();


//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[34]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[35]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  $mr["Grootboekrekening"] = "KOBU";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[33]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  $mr["Grootboekrekening"] = "KOBU";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[36]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }

  if ($kbew[32] <> 0)  // aankoop obligatie
	{
	  $mr["Grootboekrekening"] = "RENME";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = 0;
	  $mr["Debet"]             = abs($kbew[32]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
    {
	    $output[] = $mr;
    }

	}

  checkControleBedrag($controleBedrag,$kbew[19]*(($kbew[20] == "S")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Verkoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $_file, $controleBedrag, $dbew, $kbew;
  $controleBedrag = 0;

  if (count($data) != 2)
  {
    $d = array_merge($data["DBEW"], $data["KBEW"]);
    logError("do_v: zonder cash mutaties != 2 legs", $d["row"]);
    return false;
  }

  $dbew = $data["DBEW"];
  $kbew = $data["KBEW"];

  $mr = array();
  $mr["aktie"]             = "V";
//	do_algemeen();

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $dbew["row"];
  $mr["bankTransactieId"]  = $dbew[37];

  $mr["Boekdatum"]         = makeDate($dbew[26]);
  $mr["settlementDatum"]   = makeDate($dbew[27]);

//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
//  debug($kbew, "rekening= ".$mr["Rekening"] );
  JB_getfonds($dbew[9],$dbew[11],$dbew[25]);

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAV($kbew[8],$kbew[19]/$kbew[31]);


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $dbew[19];
  $mr["Fondskoers"]        = $dbew[22];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[34]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[35]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }


  tryRente();
  tryFees();
//
//  $mr["Grootboekrekening"] = "KOBU";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[33]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  $mr["Grootboekrekening"] = "KOBU";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]             = 0;
//  $mr["Debet"]             = abs($kbew[36]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//
//  if ($kbew[32] != 0)  // aankoop obligatie
//	{
//    $mr["Grootboekrekening"] = "RENOB";
//	  $mr["Aantal"]            = 0;
//	  $mr["Fondskoers"]        = 0;
//	  $mr["Credit"]            = abs($kbew[32]);
//	  $mr["Debet"]             = 0;
//	  $mr["Bedrag"]            = _creditbedrag();
//    $controleBedrag       += $mr["Bedrag"];
//	  $mr["Transactietype"]    = "";
//	  if ($mr["Bedrag"] <> 0)
//    {
//      $output[] = $mr;
//    }
//
//
//	}


  checkControleBedrag($controleBedrag,$kbew[19]*(($kbew[20] == "S")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////




/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_E()  // Emissie
{
  global $fonds, $data, $mr, $output,$meldArray, $_file;
  $controleBedrag = 0;

  if (count($data) != 2)
  {
    $d = array_merge($data["DBEW"], $data["KBEW"]);
    logError("do_a: zonder cash mutaties != 2 legs", $d["row"]);
    return false;
  }

  $dbew = $data["DBEW"];
  $kbew = $data["KBEW"];

  $mr = array();
  $mr["aktie"]             = "E";
//	do_algemeen();

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $dbew["row"];
  $mr["bankTransactieId"]  = $dbew[37];

  $mr["Boekdatum"]         = makeDate($dbew[26]);
  $mr["settlementDatum"]   = makeDate($dbew[27]);

//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  JB_getfonds($dbew[9],$dbew[11],$dbew[25]);

  $mr["Omschrijving"]      = "Emissie ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($kbew[8]);


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $dbew[19];
  $mr["Fondskoers"]        = $dbew[22];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "E";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[34]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[35]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[33]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[36]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  if ($kbew[32] <> 0)  // aankoop obligatie
  {
    $mr["Grootboekrekening"] = "RENME";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($kbew[32]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

  }

  checkControleBedrag($controleBedrag,$kbew[19]*(($kbew[20] == "S")?-1:1));
}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_D()  // Deponering van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $_file;
  $controleBedrag = 0;

  $dbew = $data["DBEW"];

  $mr = array();
  $mr["aktie"]             = "D";
//	do_algemeen();

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $dbew["row"];
  $mr["bankTransactieId"]  = $dbew[37];

  $mr["Boekdatum"]         = makeDate($dbew[26]);
  $mr["settlementDatum"]   = makeDate($dbew[27]);

  $mr["Rekening"]          = trim($dbew[3])."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  JB_getfonds($dbew[9],$dbew[11],$dbew[25]);

  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers("EUR","MEM");


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $dbew[19];
  $mr["Fondskoers"]        = $dbew[22];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"];
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  if ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Fondskoers"] *$mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;

    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }

  checkControleBedrag(0,0);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()  // Lichting van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $_file;
  $controleBedrag = 0;

  $dbew = $data["DBEW"];

  $mr = array();
  $mr["aktie"]             = "L";
//	do_algemeen();

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $dbew["row"];
  $mr["bankTransactieId"]  = $dbew[37];

  $mr["Boekdatum"]         = makeDate($dbew[26]);
  $mr["settlementDatum"]   = makeDate($dbew[27]);

  $mr["Rekening"]          = trim($dbew[3])."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  JB_getfonds($dbew[9],$dbew[11],$dbew[25]);

  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers("EUR","MEM");


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $dbew[19];
  $mr["Fondskoers"]        = $dbew[22];
  $mr["Credit"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]            = 0;
  $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  if ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";

    $mr["Credit"]             = 0;
    $mr["Debet"]            = abs($mr["Fondskoers"] *$mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"];;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;

    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }


  checkControleBedrag(0,0);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_LOS()  // Lossing
{
  global $fonds, $data, $mr, $output,$meldArray, $_file;
  $controleBedrag = 0;

  if (count($data) != 2)
  {
    $d = array_merge($data["DBEW"], $data["KBEW"]);
    logError("do_v: zonder cash mutaties != 2 legs", $d["row"]);
    return false;
  }

  $dbew = $data["DBEW"];
  $kbew = $data["KBEW"];

  $mr = array();
  $mr["aktie"]             = "L";
//	do_algemeen();

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $dbew["row"];
  $mr["bankTransactieId"]  = $dbew[37];

  $mr["Boekdatum"]         = makeDate($dbew[26]);
  $mr["settlementDatum"]   = makeDate($dbew[27]);

//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

//  debug($kbew, "rekening= ".$mr["Rekening"] );
  JB_getfonds($dbew[9],$dbew[11],$dbew[25]);

  $mr["Omschrijving"]      = "Lossing ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]    = _valutakoers($kbew[8]);


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $dbew[19];
  $mr["Fondskoers"]        = $dbew[22];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[34]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[35]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[33]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($kbew[36]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  if ($kbew[32] != 0)  // aankoop obligatie
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = abs($kbew[32]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }


  }


  checkControleBedrag($controleBedrag,$kbew[19]*(($kbew[20] == "S")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_CPDV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "CPDV";
  $kbew = $data["KBEW"];

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $kbew["row"];
  $mr["bankTransactieId"]  = $kbew[37];

  $mr["Boekdatum"]         = makeDate($kbew[26]);
  $mr["settlementDatum"]   = makeDate($kbew[27]);

//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  JB_getfonds($kbew[9],$kbew[11],$kbew[25]);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($kbew[8], $kbew[19]/$kbew[31]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ( ($fonds["fondssoort"] == "OBL") OR
       ($fonds["fondssoort"] == "OVERIG" AND $fonds["Fondseenheid"] = 0.01) )
  {
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "RENOB";
  }
  else
  {
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "DIV";
  }

  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($kbew[38] * $kbew[22] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];



	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "DIVBE";
	$mr["Debet"]             = abs($kbew[36]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$kbew[19]*(($kbew[20] == "S")?-1:1));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEH()  //Kosten
{
  global $fonds, $data, $mr, $output, $afw;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "BEH";
  $kbew = $data["KBEW"];
  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $kbew["row"];
  $mr["bankTransactieId"]  = $kbew[37];

  $mr["Boekdatum"]         = makeDate($kbew[26]);
  $mr["settlementDatum"]   = makeDate($kbew[27]);
  $mr["Valuta"]            = $kbew[8];
//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valutakoers"]       = _valutakoers($kbew[8]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = ucfirst(strtolower($kbew[28]));
  $mr["Grootboekrekening"] = "BEH";

  if ($kbew[20] == "S")
  {
    $mr["Debet"]             = abs($kbew[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            =  abs($kbew[19]);
    $mr["Bedrag"]            = _creditbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("BEH",$mr);
  $output[] = $mr;
  checkControleBedrag($controleBedrag,($kbew[82]/$kbew[15]) * (($kbew[20] == "S")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENTE()  //Kosten
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENTE";
  $kbew = $data["KBEW"];
  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $kbew["row"];
  $mr["bankTransactieId"]  = $kbew[37];

  $mr["Boekdatum"]         = makeDate($kbew[26]);
  $mr["settlementDatum"]   = makeDate($kbew[27]);
  $mr["Valuta"]            = $kbew[8];
//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valutakoers"]       = _valutakoers($kbew[8]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = ucfirst(strtolower(trim( trim($kbew[28])." ".trim($kbew[29]) )));
  $mr["Grootboekrekening"] = "RENTE";

  if ($kbew[20] == "S")
  {
    $mr["Debet"]             = abs($kbew[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            =  abs($kbew[19]);
    $mr["Bedrag"]            = _creditbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  checkControleBedrag($controleBedrag,($kbew[82]/$kbew[15]) * (($kbew[20] == "S")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KNBA()  //bank kosten
{
  global $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "KNBA";
  $kbew = $data["KBEW"];
//  debug($data);
  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $kbew["row"];
  $mr["bankTransactieId"]  = $kbew[37];

  $mr["Boekdatum"]         = makeDate($kbew[26]);
  $mr["settlementDatum"]   = makeDate($kbew[27]);
  $mr["Valuta"]            = $kbew[8];
//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valutakoers"]       = _valutakoers($kbew[8]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = ucfirst(strtolower($kbew[28]));
  $mr["Grootboekrekening"] = "KNBA";

  if ($kbew[20] == "S")
  {
    $mr["Debet"]             = abs($kbew[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            =  abs($kbew[19]);
    $mr["Bedrag"]            = _creditbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  checkControleBedrag($controleBedrag,($kbew[82]/$kbew[15]) * (($kbew[20] == "S")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_MUT()  //geldmutaties
{
  global $fonds, $data, $mr, $output, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "MUT";
  $kbew = $data["KBEW"];
  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $kbew["row"];
  $mr["bankTransactieId"]  = $kbew[37];

  $mr["Boekdatum"]         = makeDate($kbew[26]);
  $mr["settlementDatum"]   = makeDate($kbew[27]);
  $mr["Valuta"]            = $kbew[8];
//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valutakoers"]       = _valutakoers($kbew[8]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = ucfirst(strtolower(trim( trim($kbew[28])." ".trim($kbew[29]) )));


  if ($kbew[20] != "S")  // groter dan 0
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            =  abs($kbew[19]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr = $afw->reWrite("GLDSTORT",$mr);
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($kbew[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $mr = $afw->reWrite("GLDONTTR",$mr);
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  checkControleBedrag($controleBedrag,($kbew[82]/$kbew[15]) * (($kbew[20] == "S")?-1:1));
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KRUIS()  //geldmutaties
{
  global $fonds, $data, $mr, $output, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "MUT";
  $kbew = $data["KBEW"];
  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $kbew["row"];
  $mr["bankTransactieId"]  = $kbew[37];

  $mr["Boekdatum"]         = makeDate($kbew[26]);
  $mr["settlementDatum"]   = makeDate($kbew[27]);
  $mr["Valuta"]            = $kbew[8];
//  $mr["Rekening"]          = trim($kbew[3]).trim($kbew[8]);
  $mr["Rekening"]          = trim(RekeningNrFromIBAN($kbew[96])).trim($kbew[8]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ($mr["Valuta"] != "EUR")
  {

    $valKrs = (float)trim(substr($kbew[29],5));
    $mr["Valutakoers"]       = 1/$valKrs;
  }
  else
  {
    $mr["Valutakoers"]       = 1;
  }


  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = ucfirst(strtolower(trim( trim($kbew[28])." ".trim($kbew[29]) )));

  $mr["Grootboekrekening"] = "KRUIS";
  if ($kbew[20] != "S")  // groter dan 0
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            =  abs($kbew[19]);
    $mr["Bedrag"]            =  $mr["Credit"];

  }
  else
  {
    $mr["Debet"]             = abs($kbew[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  checkControleBedrag($controleBedrag,($kbew[82]/$kbew[15]) * (($kbew[20] == "S")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NVT()
{
  return true;
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>