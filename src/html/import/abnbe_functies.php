<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/10 07:50:00 $
 		File Versie					: $Revision: 1.42 $

*/

function addMeldarray($controleBedrag, $regelNr, $rekening, $notabedrag)
{ 
  global $meldArray;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag )
    $meldArray[] = "regel ".$regelNr.": ".$rekening." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$regelNr.": ".$rekening." --> notabedrag sluit aan ";
}

function textPart($str, $start, $stop)
{
  $len = $stop - $start + 1;
  return trim(substr($str, $start-1,$len));
}

function ontnullen($in)
{
  while (substr($in,0,1) == "0")
  {
    $in = substr($in,1);
  }
  return $in;
}

function maakBedrag($bedrag)
{
  return str_replace(",",".",$bedrag);
}

function convertFixedLine($rawData,$debug=false)
{
  global $row;
  $data[1] = textPart($rawData,1,15);
  $data[3]  = textPart($rawData,21,70);

  if ($data[1] == "SECURITYTRANS")
  {
    $data[4]  = textPart($rawData,71,121); // call 3700
    $data[6]  = textPart($rawData,131,165);
    $data[8]  = textPart($rawData,170,173);
    $data[11] = textPart($rawData,195,211);
    $data[12] = textPart($rawData,212,228);
    $data[13] = textPart($rawData,229,232); // storno aanduiding 0002
    $data[14] = maakBedrag(textPart($rawData,233,250));
    $data[15] = maakBedrag(textPart($rawData,251,268));
    $data[16] = textPart($rawData,269,271);
    $data[17] = maakBedrag(textPart($rawData,272,289));
    $data[18] = textPart($rawData,290,292);
    $data[19] = maakBedrag(ontnullen(textPart($rawData,293,312)));
    $data[21] = maakBedrag(textPart($rawData,331,348));  // == controlebedrag dep/lichtingen in do_L en do_D
    $data[22] = textPart($rawData,349,351);
    $data[23] = maakBedrag(textPart($rawData,352,369));  // == controlebedrag
    $data[24] = textPart($rawData,370,372);
    $data[28] = maakBedrag(textPart($rawData,398,415));
    $data[29] = textPart($rawData,416,418);
    $data[30] = maakBedrag(textPart($rawData,419,436));
    $data[31] = maakBedrag(textPart($rawData,437,454));
    $data[32] = textPart($rawData,455,457);
    $data[33] = strtoupper(textPart($rawData,513,523));
    $data[34] = textPart($rawData,523,536);
    //$data[35] wordt in de importfile gekoppeld !!
    // valutakoers aanpassen 1/koers
    // $data[14] = 1/$data[14];
  }
  else
  {
    $data[39]  = textPart($rawData,21,70);
    $data[40] = textPart($rawData,71,105);
    $data[41] = textPart($rawData,176,179);
    $data[42] = maakBedrag(textPart($rawData,222,239));
    $data[43] = textPart($rawData,240,242);
    $data[44] = maakBedrag(textPart($rawData,243,260));  // == controlebedrag
    $data[45] = textPart($rawData,261,263);              // valuta controle bedrag
    $data[46] = textPart($rawData,184,200);  // aangepast jan 2011 op verzoek tnt
    $data[47] = textPart($rawData,343,391);
    $data[48] = maakBedrag(textPart($rawData,291,299));
    $data[49] = textPart($rawData,300,307);

    $data[55] = textPart($rawData,106,109);

    if ( (float) str_replace(",", "", trim($data[47]))  == $data[44] )
    {
      $data[47] = "";
    }
  }
//  debug($data, $row);
  return $data;
}


function _debetbedrag()
{
	global $data, $mr, $valutaLookup;
	
	if ($valutaLookup == true)
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _debetbedragMR2()
{
  global $data, $mr2, $valutaLookup;

  if ($valutaLookup == true)
    return -1 * $mr2["Debet"];
  else
    return -1 * ($mr2["Debet"] * $mr2["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[24];
	if ($valutaLookup == true)
	  return $mr[Credit];
	else
	  return $mr[Credit]  * $mr[Valutakoers];
}


function _valutakoersCash()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
  $valuta = $data[43];
  If ($valuta == "EUR") return 1;
  
  if ($data[48] <> 0 AND $data[48] != 1)
  {
    return 1/$data[48];
  }
  else
  {
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($query);
    $laatsteKoers = $DB->lookupRecord();
    return $laatsteKoers[Koers];
  }  
   
   
}


function do_valutaKoersControleUSD($soort="default")
{
  global $fonds, $data, $mr, $valutaLookup;
  $db = new DB();
  switch (strtolower($soort))
  {
    case "tax":
      $mr["Valuta"] = $data[32];
      break;
    default :
      $mr["Valuta"] = $data[29];
  }
  
  if ($mr["Valuta"] == "EUR")
  {
    $mr["Valutakoers"] = 1;
    return;
  }
  
  if ( strtoupper($mr["Valuta"]) <> "EUR" AND ( $data[14] == 1 or $data[14] == "") )
  {
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$data[29]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";

    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers["Koers"];
    $valutaLookup = true;
  }
  else
  {
    $mr["Valutakoers"] = $data[14];
  }
}

function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup;
  $db = new DB();
  $valuta = $data[24];

	$valutaLookup = false;
	if ($valuta <> "EUR" AND $mr["Valuta"] == $valuta)
	{
		 $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC";
     $laatsteKoers = $db->lookupRecordByQuery($query);
     $valutaLookup = true;
     return $laatsteKoers["Koers"];
	}
	else
	  return $data[14];
}



function getRekeningNr($port,$valuta)
{

  $DB = new DB();
  $port = trim($port);
  $query = "SELECT Portefeuille FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$port.$valuta."' ";
  $DB->SQL($query);
  if ($record = $DB->lookupRecord())
  {
    $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND  Portefeuille = '".$record["Portefeuille"]."' AND Valuta = '".$valuta."' AND Deposito = 1  ";
    $DB->SQL($query);
    if ($record = $DB->lookupRecord())
      return $record["Rekening"];
  }
  return $port.$valuta;
}


function getRekeningNrMEM($rekening)
{
  global  $data, $mr;
  $DB = new DB();
  $port = trim($rekening);
  $query = "SELECT Portefeuille FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$port."MEM' ";
  $DB->SQL($query);
  if ($record = $DB->lookupRecord())
  {
    $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$record["Portefeuille"]."' AND Memoriaal = 1  AND Inactief = 0 ";
    $DB->SQL($query);
    if ($record = $DB->lookupRecord())
      $mr["Rekening"] = $record["Rekening"];
    else
      $mr["Rekening"] = "geen MEM $port";
  }

  return;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file, $valutaLookup, $type;

  $dateVeld = ($data[11] <> "")?$data[11]:$data[12];
	$datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);
	$mr["Boekdatum"]         = $datum;
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = ($type=="s")?$row:10000+$row;
  $mr["Rekening"]          = $data[35];
  $mr["bankTransactieId"]  = $data[3];

	$valutaAanduidingen = array(11,13,16,20,24,26);
	$valutaVertalingen = array('DKK'=>'DKR');
  $valutaLookup = false;
	foreach ($valutaAanduidingen as $id)
	{
	  if (array_key_exists($data[$id],$valutaVertalingen))
	    $data[$id] = $valutaVertalingen[$data[$id]];
	}



}

function try_TOB()
{
  global $mr, $data, $valutaLookup, $output;
  $mr["Grootboekrekening"] = "TOB";
  $mr["Valuta"]            = $data[32];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[31]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)  $output[] = $mr;
  return $mr["Bedrag"];
  
}

function try_ROER()
{
  global $mr, $data, $valutaLookup, $output;
  //debug($data[4]);
  $raw = trim($data[4]);
  if (substr($raw,0,8) == "Belg.RV:")
  {
    $split = explode(" ",$raw);
    $bedrag = $split[1];
    $valuta = $split[2];
  }
  else
  {
    return 0;
  }

  if ($data[31]-$bedrag >= 0)
  {
    $data["roer"] = $bedrag;
    $val = ($data[32] == $valuta)?$data[32]:" XXX";
    $mr["Grootboekrekening"] = "ROER";
    $mr["Valuta"]            = $val;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)  $output[] = $mr;
    return $mr["Bedrag"];
  }
  else
  {
    return 0;
  }



}

function try_KOBU()
{
  global $mr, $data, $valutaLookup, $output, $afw;
  $mr["Grootboekrekening"] = "KOBU";
	$mr["Valuta"]            = $data[16];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[15]);
	$mr["Bedrag"]            = _debetbedrag();
	$mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
	if ($mr["Bedrag"] <> 0)  $output[] = $mr;
  return $mr["Bedrag"];
}

function try_KOST()
{
  global $mr, $data, $valutaLookup, $output, $afw;
  $mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $data[18];
  if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[17]);
	$mr["Bedrag"]            = _debetbedrag();
	$mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
	if ($mr["Bedrag"] <> 0)  $output[] = $mr;
  return $mr["Bedrag"];
}

function try_RENOB()
{
  global $mr, $data, $valutaLookup, $output;
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Valuta"]            = $data[24];
    if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
	  $mr["Aantal"]            = 0;
  	$mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = abs($data[23]);
	  $mr["Debet"]             = 0;
	  $mr["Bedrag"]            = _creditbedrag();
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)  $output[] = $mr;
    return $mr["Bedrag"];
}

function try_RENME()
{
  global $mr, $data, $valutaLookup, $output;
  $mr["Grootboekrekening"] = "RENME";
  $mr["Valuta"]            = $data[24];
  if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data[23]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)  $output[] = $mr;
  return $mr["Bedrag"];
}



function checkstorno()
{
  global $fonds, $data, $meldArray, $mr, $row;
  if ($data[13] == "0002")
  {
    $meldArray[] = "regel {$row}: overgeslagen storno: rekening {$mr["Rekening"]}, datum {$mr["Boekdatum"]}, {$mr["Omschrijving"]}, bedrag={$mr["Bedrag"]}";
    return true;
  }

  return false;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output;

	$mr = array();
	$mr[aktie]             = "A";
  $controleBedrag = 0;
  
	do_algemeen();
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
  do_valutaKoersControleUSD();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($data[28] * $data[30] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  
  $controleBedrag += $mr[Bedrag];
  if (!checkstorno())
  {
    $output[] = $mr;

    $controleBedrag += try_RENME();

    $controleBedrag += try_KOST();

    $controleBedrag += try_KOBU();

    $controleBedrag += try_TOB();

    addMeldarray($data[44], $mr[regelnr], $mr[Rekening], $controleBedrag);
  }


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A_O()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output;

	$mr = array();
	$mr[aktie]             = "A/O";
	do_algemeen();
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
  do_valutaKoersControleUSD();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($data[28] * $data[30] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  try_RENME();

	try_KOST();

	try_KOBU();

  try_TOB();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A_S()  // Aankoop sluiten
{

  global $fonds, $data, $mr, $output;

	$mr = array();
	$mr[aktie]             = "AS";
	do_algemeen();
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
  do_valutaKoersControleUSD();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($data[28] * $data[30] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  try_RENME();

	try_KOST();

	try_KOBU();

  try_TOB();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output;
	$mr = array();
  $controleBedrag = 0;
  
	$mr[aktie]              = "V";
	do_algemeen();
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	do_valutaKoersControleUSD();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  
  $controleBedrag += $mr[Bedrag];

  if (!checkstorno())
  {
	  $output[] = $mr;

    $controleBedrag += try_RENOB();

    $controleBedrag += try_KOST();

    $controleBedrag += try_KOBU();

    $controleBedrag += try_TOB();

    addMeldarray($data[44], $mr[regelnr], $mr[Rekening], $controleBedrag);
}
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_RO()
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "RO";
	do_algemeen();
	$mr[Omschrijving]      = "Coupon ".$fonds[Omschrijving];
	$mr[Verwerkt]          = 0;
 	$mr[Valuta]            = $data[24];
  $mr[Fonds]             = $fonds[Fonds];
	//$mr[Valutakoers]       = _valutakoers();
  do_valutaKoersControleUSD(); // dividendprobleem sbcall 3105
	$mr[Memoriaalboeking]  = 0;
 	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  if ($data[23] < 0)
  {
    $mr[Debet]             = abs($data[23]) + abs($data[31]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = _debetbedrag();
  }
  else
  {
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[23]) + abs($data[31]);
	  $mr[Bedrag]            = _creditbedrag();
  }

	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  $mr["Grootboekrekening"] = "RENOB";

  $output[] = $mr;

	$mr[Grootboekrekening] = "ROER";
	$mr[Valuta]            = $data[32];
	//$mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V_S()
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "V/S";
	do_algemeen();
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
  do_valutaKoersControleUSD();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  try_RENOB();

	try_KOST();

	try_KOBU();

  try_TOB();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V_O() // Verkoop openen
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "VO";
	do_algemeen();
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
  do_valutaKoersControleUSD();
  $mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	try_RENOB();

	try_KOST();

  try_TOB();
}

function do_STUKMUT()
{
  global $data;
//  debug($data);
  if ($data[28] > 0)
  {
    do_D();
  }
  else
  {
    do_L();
  }


}

function do_L()  //Lichting
{
  global $fonds, $data, $mr, $output;

	$mr = array();
	$mr["aktie"]              = "L";
	do_algemeen();
  getRekeningNrMEM($data[6]);
	$mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	do_valutaKoersControleUSD();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[28];
	$mr["Fondskoers"]        = $data[30];
  $mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
	$mr["Transactietype"]    = "L";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;
  
  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fonds"]             = "";
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * -1;
    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }
	
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_LS()  //Lossing  05-01-2015
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "L";
	do_algemeen();
  getRekeningNrMEM($data[6]);
	$mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	do_valutaKoersControleUSD();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[28];
	$mr[Fondskoers]        = $data[17];
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "L";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[18]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;
	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[23]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_DM()  //Deponering met mutatie
{
  global $fonds, $data, $mr, $output,$afw;
	$mr = array();
	$mr[aktie]              = "DM";

  
  do_algemeen();
  if ($mr[Rekening] == "")
    getRekeningNrMEM($data[6]);
  $mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  do_valutaKoersControleUSD();
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[28];
  $mr[Fondskoers]        = $data[fondskoers];
  $mr[Debet]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
  $mr[Credit]            = 0;
  $mr[Bedrag]            = _debetbedrag();
  $mr[Transactietype]    = "D";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 0;
  $output[] = $mr;

  if  ($mr[Bedrag] <> 0)
  {
    $mr[Grootboekrekening] = "STORT";
    $mr[Fonds]             = "";
    $mr[Valuta]            = "EUR";
    $mr[Valutakoers]       = 1;
    $mr[Aantal]            = 0;
    $mr[Fonds]             = "";
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($mr[Bedrag]);
    $mr[Bedrag]            = $mr[Credit];
    $mr[Transactietype]    = "";
    $output[] = $mr;
  }

  /// mutatie boeking

	$mr = array();
	$mr[aktie]              = "DM";
	do_algemeen();
  $dateVeld = $data[46];
	$datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);
	$mr[Boekdatum]         = $datum;
	$mr[Rekening]          = trim($data[40]);
	if (trim($data[47]) == "") 
    $mr[Omschrijving]      = "geen omschrijving bij Deponering  ".$fonds[Omschrijving];
  else
    $mr[Omschrijving]      = $data[47];
  
  
	$mr[Grootboekrekening] = "MUT";
	$mr[Valuta]            = $data[43];
	$mr[Valutakoers]       = _valutakoersCash();
	$mr[Fonds]             = "";
	$mr[Aantal]            = '';
	$mr[Fondskoers]        = '';

	$mr[Debet]             = 0;
	$mr[Credit]            = 0;
	$mr[Bedrag]            = $data[42];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;


  if($data[42] > 0)
  {
    $mr[Grootboekrekening] 	= "STORT";
    $mr[Debet]        			=	0;
    $mr[Credit]       			= abs($data[42]);
    $mr[Bedrag]       			= $mr[Credit];
  }
  else
  {
    $mr[Grootboekrekening] 	= "ONTTR";
    $mr[Debet]			        = abs($data[42]);
    $mr[Credit]       			= 0;
    $mr[Bedrag]       			= _debetbedrag();
  }
  $mr = $afw->reWrite("MUT",$mr);

  $output[] = $mr;


}
  ////////////////////////////


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
///
function splitTaxForDV()  // Call 7806
{
  global $fonds, $data, $mr, $mr2, $output, $meldArray, $controleBedrag;
  $uitzonderingenArray = array (
    "GB00B03MLX29" => "NL",
  );

  $isinPrefix = substr($fonds["ISINCode"],0,2);

  foreach ($uitzonderingenArray as $isin => $prefix)
  {
    if ($fonds["ISINCode"] == $isin)
    {
      $isinPrefix = $prefix;
    }

  }
  $brutoDiv = abs($data[23]) + abs($data[31]);
  $factor =  ABS( $data[31]/$brutoDiv ) * 100;
  $mr2 = $mr;

  $meldTel = 0;
  switch ($isinPrefix)
  {
    case "NL":
      if ($factor >= 14.95 AND $factor <= 15.05)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 40.45 AND $factor <= 40.55)
      {
        $mr["Debet"]              = $brutoDiv * 0.15;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;
    case "DE":
      if ($factor >= 26.335 AND $factor <= 26.405)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 48.4225 AND $factor <= 48.4925)
      {
        $mr["Debet"]              = $brutoDiv * 0.26375;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;
    case "CH":
      if ($factor >= 34.95 AND $factor <= 35.05)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 54.45 AND $factor <= 54.55)
      {
        $mr["Debet"]              = $brutoDiv * 0.35;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;
    case "FI":
      if ($factor >= 34.95 AND $factor <= 35.05)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 54.45 AND $factor <= 54.55)
      {
        $mr["Debet"]              = $brutoDiv * 0.35;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;
    case "DK":
      if ($factor >= 26.95 AND $factor <= 27.05)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 48.85 AND $factor <= 48.95)
      {
        $mr["Debet"]              = $brutoDiv * 0.27;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }

      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;
    case "FR":
      if ($factor >= 27.95 AND $factor <= 28.05)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 49.55 AND $factor <= 49.65)
      {
        $mr["Debet"]              = $brutoDiv * 0.28;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;
    case "BE":
    case "GB":
      if ($factor >= 0 AND $factor <= 0.03)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;
    case "JP":
      if ($factor >= 15.293 AND $factor <= 15.337)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 40.68 AND $factor <= 40.76)
      {
        $mr["Debet"]              = $brutoDiv * 0.15315;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;

    case "NO":
      if ($factor >= 24.95 AND $factor <= 25.05)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 47.45 AND $factor <= 47.55)
      {
        $mr["Debet"]              = $brutoDiv * 0.25;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";

      }
      break;

    case "US":
      if ($factor >= 14.95 AND $factor <= 15.05)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen DIVBE ";
        $meldTel++;
      }

      if ($factor >= 29.95 AND $factor <= 30.05)
      {
        $mr["Grootboekrekening"] = "ROER";
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Alleen ROER ";
        $meldTel++;
      }

      if ($factor >= 40.45 AND $factor <= 40.55)
      {
        $mr["Debet"]              = $brutoDiv * 0.15;
        $mr["Bedrag"]             = _debetbedrag();

        $mr2["Debet"]             = abs($data[31]) - $mr["Debet"];
        $mr2["Grootboekrekening"] = "ROER";
        //$mr2["Bedrag"]            = -1 * ($mr2["Debet"] * $mr2["Valutakoers"]);
        $mr2["Bedrag"]            = _debetbedragMR2();
        $output[] = $mr2;

        $controleBedrag += $mr2["Bedrag"];

        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - opgesplitst ";
        $meldTel++;
      }
      if ($meldTel == 0)
      {
        $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." -->geboekt als  DIVBE - mogelijk nog uitsplitsten ";
      }
      break;

    default;
      $meldArray[] = "regel ".$mr["regelnr"].": ". $mr["Rekening"]." --> DIVBE - Zelf verdelen indien van toepassing ";
  }


}
///

function do_STUKDIV()
{
  global $fonds, $data, $mr, $output, $afw, $controleBedrag, $row;
  $mr = array();
  if ($data[28] < 0)
  {

    $mr["aktie"]              = "L";
    do_algemeen();
    getRekeningNrMEM($data[6]);
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    do_valutaKoersControleUSD();
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data[28];
    $mr["Fondskoers"]        = $data[30];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
    $mr["Transactietype"]    = "L";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    $output[] = $mr;

    if  ($mr["Bedrag"] <> 0)
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Fonds"]             = "";
      $mr["Valuta"]            = "EUR";
      $mr["Valutakoers"]       = 1;
      $mr["Aantal"]            = 0;
      $mr["Fonds"]             = "";
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = abs($mr["Bedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = $mr["Debet"] * -1;
      $mr["Transactietype"]    = "";
      $output[] = $mr;
    }

    $mr["Rekening"]          = $data[35];
    getRekeningNr($mr["Rekening"], "");
    $mr["Omschrijving"]      = "Roerheffing ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "ROER";
    $mr["Valuta"]            = $data[32];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[31]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
  else
  {

    $mr["aktie"]              = "D";
//  debug($data);
    do_algemeen();
    getRekeningNrMEM($data[6]);
    $mr["Omschrijving"]      = "Deponering  ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    do_valutaKoersControleUSD();
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data[28];
    $mr["Fondskoers"]        = $data[30];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
    $mr["Transactietype"]    = "D";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $output[] = $mr;

    if  ($mr["Bedrag"] <> 0)
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Fonds"]             = "";
      $mr["Valuta"]            = "EUR";
      $mr["Valutakoers"]       = 1;
      $mr["Aantal"]            = 0;
      $mr["Fonds"]             = "";
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
      $mr["Transactietype"]    = "";
      $output[] = $mr;
    }
  }
//  debug($data);
}


function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output, $afw, $controleBedrag, $row;

  // call 9050
  if (abs($data[23])  == 0 )
  {
    do_STUKDIV();
    return;
  }

	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "DV";
	do_algemeen();

	$mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "DIV";

	$mr["Valuta"]            = $data[24];
	//$mr[Valutakoers]       = _valutakoers();
  do_valutaKoersControleUSD(); // dividendprobleem sbcall 3105
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  if ($data[23] < 0)
  {
    $mr["Debet"]             = abs($data[23]) + abs($data[31]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[23]) + abs($data[31]);
	  $mr["Bedrag"]            = _creditbedrag();
  }

	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $controleBedrag = $mr["Bedrag"];
	$output[] = $mr;
  
  $controleBedrag += try_ROER();

	$mr["Grootboekrekening"] = "DIVBE";

	$mr["Valuta"]            = $data[32];
 
	//$mr[Valutakoers]       = _valutakoers();
   do_valutaKoersControleUSD("tax"); // dividendprobleem sbcall 3105
  $mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs($data[31]-$data["roer"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $mr = $afw->reWrite("DIVBE",$mr);


  if ((float)$data["roer"] == 0 AND $mr["Bedrag"] != 0)
  {
    splitTaxForDV();
  }


	if ($mr["Bedrag"] <> 0)
  {
		$output[] = $mr;
    $controleBedrag += $mr["Bedrag"];
  }


  addMeldarray($data[44], $mr["regelnr"], $mr["Rekening"], $controleBedrag);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KAP()  //kapitaals vermindering
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "DV";
	do_algemeen();
	$mr[Omschrijving]      = "Kapitaals vermindering ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "DIV";
	$mr[Valuta]            = $data[24];
	//$mr[Valutakoers]       = _valutakoers();
  do_valutaKoersControleUSD(); // dividendprobleem sbcall 3105
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  if ($data[23] < 0)
  {
    $mr[Debet]             = abs($data[21]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = _debetbedrag();
  }
  else
  {
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[21]);
	  $mr[Bedrag]            = _creditbedrag();
  }

	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;


}

function do_D($record)  // Deponering van stukken
{

  global $data,$fonds,$mr,$output;
  $mr = array();
  $mr[aktie]              = "D";
//  debug($data);
  do_algemeen();
  getRekeningNrMEM($data[6]);
  $mr["Omschrijving"]      = "Deponering  ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  do_valutaKoersControleUSD();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[28];
  $mr["Fondskoers"]        = $data[30];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;

  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fonds"]             = "";
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }

}

function do_Mutatie()
{

	global $data,$mr,$output, $afw;
//	debug($data);
	$mr = array();
	$mr["aktie"]              = "Mut.";
	do_algemeen();
  $dateVeld = $data[49];
	$datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);

	$mr["Boekdatum"]         = $datum;
	$mr["Rekening"]          = trim($data[40]);


	$mr["Omschrijving"]      = $data[47];
	$mr["Grootboekrekening"] = "MUT";
	$mr["Valuta"]            = $data[43];
	$mr["Valutakoers"]       = _valutakoersCash();
	$mr["Fonds"]             = "";
	$mr["Aantal"]            = '';
	$mr["Fondskoers"]        = '';

	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = $data[42];
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

  $transactieCode = $data[41];


  if($data[42] > 0)
  {
    $mr["Grootboekrekening"] 	= ($transactieCode == "A200")?"KRUIS":"STORT";
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($data[42]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $mr = $afw->reWrite("GLDSTORT",$mr);
  }
  else
  {
    $mr["Grootboekrekening"] 	= ($transactieCode == "A200")?"KRUIS":"ONTTR";
    $mr["Debet"]			        = abs($data[42]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= -1 * $mr["Debet"];
    $mr = $afw->reWrite("GLDONTTR",$mr);
  }


  if (stristr($mr["Omschrijving"], "FRACTIONS SEC TRANSIT ACC"))
  {
    $splt = explode("TRANSIT ACC", $mr["Omschrijving"]);
    $mr["Omschrijving"] = trim($splt[1]);
    $mr["Grootboekrekening"] 	= "VKSTO";
  }

  if ($transactieCode == "A200")
  {
    $mr["Omschrijving"] = "Valuta overboeking";
  }


  if ($transactieCode == "A181")
  {
    $btw = abs($data[44]) - abs($data[42]);
    $mr["Omschrijving"] = "Bewaarloon incl. BTW: {$btw}";
    $mr["Grootboekrekening"] 	= "BEW";
    if($data[42] > 0)
    {
      $mr["Debet"]        			=	0;
      $mr["Credit"]       			= abs($data[44]);
      $mr["Bedrag"]       			= $mr["Credit"];
    }
    else
    {
      $mr["Debet"]			        = abs($data[44]);
      $mr["Credit"]       			= 0;
      $mr["Bedrag"]       			= -1 * $mr["Debet"];
    }
  }

  $output[] = $mr;

}

function do_BEH()
{

  global $data,$mr,$output, $afw;
//	debug($data);
  $mr = array();
  $mr["aktie"]              = "BEH";
  do_algemeen();
  $dateVeld = $data[49];
  $datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);

  $mr["Boekdatum"]         = $datum;
  $mr["Rekening"]          = trim($data[40]);


  $mr["Omschrijving"]      = $data[47];
  $mr["Grootboekrekening"] = "BEH";
  $mr["Valuta"]            = $data[43];
  $mr["Valutakoers"]       = _valutakoersCash();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';

  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $data[42];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $transactieCode = $data[41];


  if( $data[42] > 0 )
  {
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($data[42]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $mr = $afw->reWrite("BEH",$mr);
  }
  else
  {
    $mr["Debet"]			        = abs($data[42]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= -1 * $mr["Debet"];
    $mr = $afw->reWrite("BEH",$mr);
  }

  $output[] = $mr;

}

function do_BEW()
{

  global $data,$mr,$output, $afw;
//	debug($data);
  $mr = array();
  $mr["aktie"]              = "BEW";
  do_algemeen();
  $dateVeld = $data[49];
  $datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);

  $mr["Boekdatum"]         = $datum;
  $mr["Rekening"]          = trim($data[40]);


  $mr["Omschrijving"]      = $data[47];
  $mr["Grootboekrekening"] = "BEW";
  $mr["Valuta"]            = $data[43];
  $mr["Valutakoers"]       = _valutakoersCash();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';

  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $data[42];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $transactieCode = $data[41];


  if( $data[42] > 0 )
  {
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($data[42]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $mr = $afw->reWrite("BEW",$mr);
  }
  else
  {
    $mr["Debet"]			        = abs($data[42]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= -1 * $mr["Debet"];
    $mr = $afw->reWrite("BEW",$mr);
  }

  $output[] = $mr;

}

function do_RENTE()
{

  global $data,$mr,$output, $afw;
//	debug($data);
  $mr = array();
  $mr["aktie"]              = "RENTE";
  do_algemeen();
  $dateVeld = $data[49];
  $datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);

  $mr["Boekdatum"]         = $datum;
  $mr["Rekening"]          = trim($data[40]);


  $mr["Omschrijving"]      = $data[47];
  $mr["Grootboekrekening"] = "RENTE";
  $mr["Valuta"]            = $data[43];
  $mr["Valutakoers"]       = _valutakoersCash();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';

  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $data[42];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $transactieCode = $data[41];


  if( $data[42] > 0 )
  {
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($data[42]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $mr = $afw->reWrite("RENTE",$mr);
  }
  else
  {
    $mr["Debet"]			        = abs($data[42]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= -1 * $mr["Debet"];
    $mr = $afw->reWrite("RENTE",$mr);
  }

  $output[] = $mr;

}


function do_TB()
{
  global $data,$mr,$output, $afw, $meldarray;
  do_algemeen();
  $meldarray[] = "regel {$mr["regelnr"]}: transactiecode 999 overgeslagen ";
//  debug($meldarray);
}

function do_NVT()
{
  global $data,$mr,$output, $afw, $meldArray, $row;

  $meldArray[] = "regel {$row}: <span style='color:red' >transactiecode <b>999</b> overgeslagen</span> ";

//  debug($meldarray);
}

function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}

?>