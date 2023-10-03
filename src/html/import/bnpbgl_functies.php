<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/07 08:00:28 $
 		File Versie					: $Revision: 1.10 $

 		$Log: bnpbgl_functies.php,v $
 		Revision 1.10  2020/07/07 08:00:28  cvs
 		call 7605
 		
 		Revision 1.9  2020/05/20 13:05:35  cvs
 		call 7605
 		
 		Revision 1.8  2020/05/11 08:22:58  cvs
 		call 7605
 		
 		Revision 1.7  2020/05/08 15:00:46  cvs
 		call 7605
 		
 		Revision 1.6  2020/05/06 08:06:23  cvs
 		call 7605
 		
 		Revision 1.5  2020/05/01 07:48:24  cvs
 		call 7605
 		
 		Revision 1.4  2020/04/29 14:16:26  cvs
 		call 7605
 		
 		Revision 1.3  2020/03/30 06:41:24  cvs
 		call 7605
 		
 		Revision 1.2  2019/10/30 13:12:17  cvs
 		call 7605
 		
 		Revision 1.1  2019/07/18 07:53:10  cvs
 		call 7605
 		


*/

function bnpbglDate($inDate)
{
  $d = explode("/",$inDate);
  //return $d[2]."-".$d[1]."-".$d[0];
  return $inDate;
}

function bnpbglNumber($in)
{
  return str_replace(",",".",$in);
}

function bnpbglCheckFonds($ISIN="",$valuta="")
{
  global $error, $mr;
  $db = new DB();

  if ($ISIN == ""  OR $valuta == "")
  {
    return false;
  }

  $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$ISIN."' AND Valuta = '$valuta' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec;
  }
  $meldArray[] = "regel ".$mr["regelnr"].": Fonds niet gevonden: {$ISIN}/{$valuta} ";
  return false;
}


function getRekening($rekeningNr="-1", $depot="BGL")
{
  global $meldArray, $mr;
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"]; 
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $meldArray[] = "regel ".$mr["regelnr"].": rekening {$rekeningNr} --> niet gevonden voor $depot ";
      return false;
    }
    
  }
  
  
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "BGL|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $fData, $mr;


	if ($fData["rekeningValuta"] == $mr["Valuta"])
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
	global $fData, $mr;

	if ($fData["rekeningValuta"] == $mr["Valuta"])
  {
    return $mr["Credit"];
  }
	else
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }

}


function _valutakoers($rekValuta, $fondsValuta)
{
  global $fonds, $fData, $mr, $valutaLookup, $DB;

  if ($rekValuta == "EUR" AND $fondsValuta == "EUR")
  {
    return 1;
  }

  if ( ($rekValuta != "EUR" AND  $fondsValuta == $rekValuta))
  {
    return $fData["operWisselkoers"];
  }
  elseif ($rekValuta == "EUR" AND $fondsValuta <> $rekValuta)
  {
    return $fData["instAccWisselkoers"];
  }
  elseif ($fondsValuta == "EUR" AND $fondsValuta <> $rekValuta)
  {
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$rekValuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $DB->lookupRecordByQuery($query);
    return 1 / $laatsteKoers["Koers"];
  }

  return 999;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $fData, $_file, $fonds;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $fData["transactieId"];
	$mr["Boekdatum"]         = $fData["boekdatum"];
  $mr["settlementDatum"]   = $fData["settlementdatum"];
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

function buildRekening($rekPart, $valuta)
{
  $split = explode("_",$rekPart);
////  debug(array(
//    $rekPart,
//    $valuta,
//    $split[0].$valuta
//  ));
  $reknr = $split[0].$valuta;

  return $reknr;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{
  global $fData, $mr, $output, $afw;

  $mr = array();
  $mr["aktie"]           = "FX";
  do_algemeen();
  $mr["Rekening"]        = buildRekening($fData["rekening1"], $fData["valuta1"]);
  if (!getRekening($mr["Rekening"]))
  {
    return false;
  }
  debug($fData);
  $fData["rekeningValuta"]    = $fData["valuta1"];
  $mr["Omschrijving"]      = $fData["omschrijving"];
  $mr["Grootboekrekening"] = "KRUIS";
  $mr["Valuta"]            = $fData["fxValuta"];
  $mr["Valutakoers"]       = $fData["fxKoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ($fData["transactiecode"] == "FX-Buy")
  {
    $mr["Valutakoers"]       = 1/$mr["Valutakoers"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($fData["fxBedrag"] );
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Credit"]           = 0;
    $mr["Debet"]            = abs($fData["fxBedrag"] );
    $mr["Bedrag"]           = _debetbedrag();
  }

  $mr["Transactietype"]    = "";
  $controleBedrag         += $mr["Bedrag"];
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Rekening"]        = buildRekening($fData["rekening2"], $fData["valuta2"]);
  if (!getRekening($mr["Rekening"]))
  {
    return false;
  }
  $fData["rekeningValuta"]    = $fData["valuta2"];

  $fxBedrag=0;
  if ($fData["transactiecode"] == "FX-Buy")
  {
    $mr["Credit"]           = 0;
    $mr["Debet"]            = abs($fData["fxBedrag"] );
    $mr["Bedrag"]           = _debetbedrag();
    $fxBedrag               = $mr["Bedrag"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($fData["fxBedrag"] );
    $mr["Bedrag"]            = _creditbedrag();
  }

  $mr["Transactietype"]    = "";
  $controleBedrag         += $mr["Bedrag"];
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  if ($fData["transactiecode"] == "FX-Buy" && $fxBedrag!=0)
  {
    checkControleBedrag($fxBedrag, $fData["nettoBedrag"] *-1);
  }
  else
  {
    checkControleBedrag($controleBedrag,$fData["nettoBedrag"] *-1);
  }

  //checkControleBedrag($controleBedrag,$fData[12941] );

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_A()  // Aankoop van stukken
{
  global $fonds, $fData, $mr, $output,$meldArray;
  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();


	if ($fData[46] == "Forex Spot")
  {
//      do_KRUIS();
  }
	else
  {
    $mr["Rekening"]          = buildRekening($fData[55], $fData["rekeningValuta"]);
    if (!getRekening($mr["Rekening"]))
    {
      return false;
    }
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $fData["fondsValuta"]);
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $fData["aantal"] ;
    $mr["Fondskoers"]        = $fData["koers"];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "A";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $fData["kost_1_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
	$mr["Debet"]             = abs($fData["kost_1_bedrag"]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $fData["kost_2_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
	$mr["Debet"]             = abs($fData["kost_2_bedrag"]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $fData["kost_6_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;

	if ($fData["kost_6_bedrag"] > 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($fData["kost_6_bedrag"]);
    $mr["Bedrag"]            = _debetbedrag();
  }
	else
  {
    $mr["Credit"]            = abs($fData["kost_6_bedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }


  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Valuta"]            = $fData["kost_8_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
	$mr["Debet"]             = abs($fData["kost_8_bedrag"]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "RENME";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
	$mr["Debet"]             = abs($fData["opgelopenRente"]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$fData["nettoBedrag"] *-1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Aankoop van stukken
{
  global $fonds, $fData, $mr, $output,$meldArray;
  $controleBedrag = 0;
//  debug($fData);
//  debug($fonds);
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();

//  debug($fData);

  if ($fData[46] == "Forex Spot")
  {
//      do_KRUIS();
  }
  else
  {
    $mr["Rekening"]          = buildRekening($fData[55], $fData["rekeningValuta"]);
    if (!getRekening($mr["Rekening"]))
    {
      return false;
    }
    $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $fData["fondsValuta"]);
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $fData["aantal"] * -1 ;
    $mr["Fondskoers"]        = $fData["koers"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "V";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $fData["kost_1_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($fData["kost_1_bedrag"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $fData["kost_2_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($fData["kost_2_bedrag"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $fData["kost_6_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($fData["kost_6_bedrag"] > 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($fData["kost_6_bedrag"]);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Credit"]            = abs($fData["kost_6_bedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $fData["kost_8_valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($fData["kost_8_bedrag"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($fData["opgelopenRente"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$fData["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_MUT()  //geld en stukken
{
  global $fonds, $fData, $mr, $output,$meldArray;
  $mr = array();
  $controleBedrag = 0;

  $fData["rekeningValuta"]       = $fData[43];

  if ($fData[14] != "")
  {
    do_STUKMUT();
    return;
  }

  if ($fData["transactiecode"] == "Investment")
  {
    do_STORT();
  }
  else
  {
    do_ONTTR();
  }
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_STUKMUT()
{
  global $fData, $mr, $output, $afw, $fonds;

  $mr = array();
  $mr["aktie"]           = "MUT";
  do_algemeen();
  $mr["Rekening"]          = buildRekening($fData["portefeuille"], "MEM");

  if (!getRekening($mr["Rekening"]))
  {
    return false;
  }

  switch(strtoupper(trim($fData["omschrijving"])))
  {
    case "OPTIONAL DIVIDEND ALLOTMENT OF RIGHTS":
      $mr["Omschrijving"]    = "Deponering " . $fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "STORT";
      $mr = $afw->reWrite("STORT",$mr);

      $mr["Aantal"]            = $fData["aantal"];
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = _valutakoers($fData["stukValuta"], $fData["fondsValuta"]);
      $mr["Fonds"]             = $fonds["Fonds"];

      $mr["Fondskoers"]        = $fData["koers"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            = _creditbedrag();
      $mr["Transactietype"]    = "D";

      $controleBedrag         += $mr["Bedrag"];

      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;

      $output[] = $mr;

      checkControleBedrag($controleBedrag, $fData[141]);

      break;
    default:
      echo "<br/>do_STUKMUT: nog niet ingeregeld {$fData['omschrijving']}";
      return;
      break;
  }


}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FEES()  //Fees
{
  global $fData, $mr, $output, $afw;
	$mr = array();
	$mr["aktie"]           = "FEE";
	do_algemeen();
  $mr["Rekening"]        = buildRekening($fData[55], $fData["rekeningValuta"]);
  if (!getRekening($mr["Rekening"]))
  {
    return false;
  }
	$mr["Omschrijving"]    = $fData["omschrijving"];
	$mr["Grootboekrekening"] = "BEH";
	if (stristr($mr["Omschrijving"], "BEWAAR"))
  {
    $mr["Grootboekrekening"] = "BEW";
  }

	$mr["Valuta"]            = $fData["rekeningValuta"];
	$mr["Valutakoers"]       = $fData["operWisselkoers"];
	$mr["Fonds"]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs($fData["nettoBedrag"] );
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "";
  $controleBedrag         += $mr["Bedrag"];
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("FEES",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$fData["nettoBedrag"] * -1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STORT()  //Storting
{
  global $fData, $mr, $output, $afw;

  $mr = array();
  $mr["aktie"]           = "STORT";
  do_algemeen();
  $mr["Rekening"]        = buildRekening($fData[16], $fData[43]);
  if (!getRekening($mr["Rekening"]))
  {
    return false;
  }
//  debug($fData);
  $mr["Omschrijving"]    = $fData["omschrijving"];
  $mr["Grootboekrekening"] = "STORT";
  $mr["Valuta"]            = $fData["operValuta"];
  $mr["Valutakoers"]       = $fData["operWisselkoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($fData["aantal"] );
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "";
  $controleBedrag         += $mr["Bedrag"];
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("STORT",$mr);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$fData[141] );
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_ONTTR()  //Ontrekking
{
  global $fData, $mr, $output, $afw;

  $mr = array();
  $mr["aktie"]           = "ONTTR";
  do_algemeen();
  $mr["Rekening"]        = buildRekening($fData[16], $fData[43]);
  if (!getRekening($mr["Rekening"]))
  {
    return false;
  }
  $mr["Omschrijving"]    = $fData["omschrijving"];
  $mr["Grootboekrekening"] = "ONTTR";
  if (stristr($mr["Omschrijving"], "MANAGEMENT FEES"))
  {
    $mr["Grootboekrekening"] = "BEH";
  }
  $mr["Valuta"]            = $fData["operValuta"];
  $mr["Valutakoers"]       = $fData["operWisselkoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($fData["aantal"] );
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "";
  $controleBedrag         += $mr["Bedrag"];
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("ONTTR",$mr);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$fData[141] * -1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_DIVCOUP()  // Dividend
{
  global $fonds, $fData, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();

  do_algemeen();


  $mr["aktie"]           = "DIV";
  $mr["Rekening"]          = buildRekening($fData[55], $fData["rekeningValuta"]);
  if (!getRekening($mr["Rekening"]))
  {
    return false;
  }
  $fonds = bnpbglCheckFonds($fData["isin"], $fData["fondsValuta"]);

  if ($fData["renteboeking"])
  {
    $mr["Omschrijving"]      = $fData["omschrijving"];
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Valuta"]            = $fData[127];
    $mr["Valutakoers"]       = $fData[128];
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($fData["fxBedrag"] > 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($fData["fxBedrag"] );
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($fData["fxBedrag"] );
      $mr["Bedrag"]            = _debetbedrag();
    }

    $mr["Transactietype"]    = "";
    $controleBedrag         += $mr["Bedrag"];
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    $output[] = $mr;
  }
  else
  {
    if ($fData[47] == "FIXED_INTEREST")
    {
      $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "RENOB";
    }
    else
    {
      $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "DIV";
    }

    $mr["Valuta"]            = $fData["operValuta"];
    $mr["Valutakoers"]       = $fData["operWisselkoers"];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($fData["operBruto"]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;


    $set = array();
    for ($i=1; $i < 11; $i++)
    {
      if ($fData["kost_".$i."_bedrag"] != 0)
      {
        $gbType = $fData["kost_".$i."_type"];
        $set[] = array(
          "grootboek" => (substr($gbType,0,7) == "IMPOTS_")?"DIVBE": "KNBA",
          "valuta"    => $fData["kost_".$i."_valuta"],
          "bedrag"    => $fData["kost_".$i."_bedrag"]
        );
      }
    }


    foreach ($set as $boeking)
    {
      $mr["Grootboekrekening"] = $boeking["grootboek"];
      $mr["Valuta"]            = $boeking["valuta"];
      $mr["Valutakoers"]       = _valutakoers($fData["rekeningValuta"], $mr["Valuta"] );
      //$mr[Fonds]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($boeking["bedrag"]);
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];
      $mr["Transactietype"]    = "";
      if ($mr["Bedrag"] <> 0)
      {
        $output[] = $mr;
      }
    }
  }




  checkControleBedrag($controleBedrag,$fData["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error($row, $tc)
{
	global $do_func;
	echo "<BR>$row: FOUT geen mapping voor transactiecode '$tc'";
}


