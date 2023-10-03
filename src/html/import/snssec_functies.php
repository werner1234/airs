<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.26 $

 		$Log: snssec_functies.php,v $
 		Revision 1.26  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2017/09/20 06:17:15  cvs
 		megaupdate 2722
 		
 		Revision 1.24  2016/09/21 08:30:05  cvs
 		call 5200
 		
 		Revision 1.23  2016/05/25 09:24:12  cvs
 		call 4868
 		
 		Revision 1.22  2016/05/11 14:13:32  cvs
 		call 4868
 		
 		Revision 1.21  2015/06/11 16:18:16  cvs
 		*** empty log message ***
 		
 		Revision 1.19  2014/09/24 14:55:04  cvs
 		dbs 2615
 		
 		Revision 1.18  2014/09/18 09:01:28  cvs
 		*** empty log message ***
 		
 		Revision 1.17  2014/09/17 12:36:35  cvs
 		dbs 2615
 		
 		Revision 1.16  2014/08/29 07:59:47  cvs
 		dbs2778
 		
 		Revision 1.15  2014/08/22 09:53:06  cvs
 		dbs 2778
 		
 		Revision 1.14  2014/04/02 13:54:51  cvs
 		*** empty log message ***
 		
 		Revision 1.13  2013/01/30 10:17:18  cvs
 		check op depotbank SNS
 		getrekening via port + mem methode
 		
 		Revision 1.12  2013/01/02 15:43:18  cvs
 		do_DV en do_R
 		
 		Revision 1.11  2012/11/07 10:38:17  cvs
 		*** empty log message ***
 		
 		Revision 1.10  2012/05/15 15:02:19  cvs
 		controlebedrag
 		
 		Revision 1.9  2011/06/28 09:17:37  cvs
 		fondscode aanpassingen
 		
 		Revision 1.8  2011/04/18 14:34:17  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2011/03/04 07:15:18  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2010/11/30 12:59:15  cvs
 		*** empty log message ***

 		Revision 1.5  2010/11/03 10:43:23  cvs
 		*** empty log message ***

 		Revision 1.4  2010/08/12 09:40:04  cvs
 		*** empty log message ***

 		Revision 1.3  2010/07/13 09:20:56  cvs
 		*** empty log message ***

 		Revision 1.2  2010/06/17 06:55:36  cvs
 		*** empty log message ***

 		Revision 1.1  2010/06/09 15:20:23  cvs
 		*** empty log message ***


*/



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



function convertFixedLine($rawData,$debug=false)
{
  $data[1] = textPart($rawData,1,15);
  $data[3]  = textPart($rawData,21,70);
  if ($data[1] == "SECURITYTRANS")
  {
    $data[6]  = textPart($rawData,171,205);
    $data[8]  = textPart($rawData,210,213);
    $data[11] = textPart($rawData,235,242);
    $data[12] = textPart($rawData,252,259);
    $data[14] = textPart($rawData,273,290);
    $data[15] = textPart($rawData,291,308);
    $data[16] = textPart($rawData,309,311);
    $data[17] = textPart($rawData,312,329);
    $data[18] = textPart($rawData,330,332);
    $data[19] = ontnullen(textPart($rawData,333,352));
    $data[20] = textPart($rawData,353,370);
    $data[21] = textPart($rawData,371,388);
    $data[22] = textPart($rawData,389,391);  // afrekenvaluta
    $data[23] = textPart($rawData,392,409);  
    $data[24] = textPart($rawData,410,412); // valuta dividend
    $data[28] = textPart($rawData,438,455);
    $data[29] = textPart($rawData,456,458);
    $data[30] = textPart($rawData,459,476);
    $data[31] = textPart($rawData,477,494);
    $data[32] = textPart($rawData,495,497);
    $data[33] = textPart($rawData,1776,1790);
    // valutakoers aanpassen 1/koers
    $data[14] = 1/$data[14];
  }
  else
  {
    $data[40] = textPart($rawData,71,105);
    $data[41] = textPart($rawData,176,179);
    $data[42] = textPart($rawData,222,239);
    $data[43] = textPart($rawData,240,242);
    $data[44] = textPart($rawData,243,260);
    $data[45] = textPart($rawData,261,263);
    $data[46] = textPart($rawData,300,307);
    $data[47] = textPart($rawData,343,384);
  }

  if ($debug)
    listarray($data);
  return $data;
}


function checkControleBedrag($controleBedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($data[21],2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}

function doOutput($mr)
{
  global $output, $data;

  // controle storneringen
  if ($data[33] <> "")
  {
    $tmp            = $mr["Debet"];
    $mr["Debet"]    = $mr["Credit"];
    $mr["Credit"]   = $tmp;
    $mr["Bedrag"]   = $mr["Bedrag"] * -1;
    $mr["Omschrijving"] = "STORNO: ".$mr[Omschrijving];
  }

  $output[] = $mr;

}

function _debetbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[22];
	if ($valutaLookup == true)
	  return -1 * $mr[Debet];
	else
	  return -1 * ($mr[Debet] * $mr[Valutakoers]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[22];
	if ($valutaLookup == true)
	  return $mr[Credit];
	else
	  return $mr[Credit]  * $mr[Valutakoers];
}

function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[22];  //afrekenvaluta

	$valutaLookup = false;
	if ($valuta <> "EUR" AND $mr[Valuta] == $valuta)
	{
		 $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC LIMIT 1";
     $DB->SQL($query);
     $laatsteKoers = $DB->lookupRecord();
     $valutaLookup = true;
     return $laatsteKoers[Koers];
	}
	else
	  return $data[14];
}

function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank IN ('SNS','NIBC') ";
  $DB->SQL($query);
  if ($record = $DB->lookupRecord())
    $output = $record["Rekening"];
  else
  {
    // rekeningnr bijzoeken via portnr+mem methode (tnt 30-1-2013)
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".trim($port)."MEM' ";
    $DB->SQL($query);
    $tempRec = $DB->lookupRecord();

    $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$tempRec["Portefeuille"]."' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank IN ('SNS','NIBC') ";
    $DB->SQL($query);
    $tempRec = $DB->lookupRecord();
    $output = $tempRec["Rekening"];
  }  
  return $output;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file, $valutaLookup, $controleBedrag;

  $dateVeld = ($data[11] <> "")?$data[11]:$data[12];
	$datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);

	$mr[Boekdatum]         = $datum;
	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
  $mr[aktie]             = $data[8];
  $mr[bankTransactieId]  = $data[3];
  $controleBedrag        = 0;

	$valutaAanduidingen = array(11,13,16,20,24,26);
	$valutaVertalingen = array('DKK'=>'DKR');
  $valutaLookup = false;
	foreach ($valutaAanduidingen as $id)
	{
	  if (array_key_exists($data[$id],$valutaVertalingen))
	    $data[$id] = $valutaVertalingen[$data[$id]];
	}

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output;
  
	$mr = array();
	$mr[aktie]             = "A";
	do_algemeen();
	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $data[29];
	$mr[Valutakoers]       = _valutakoers();
//	$mr[Valutakoers]       = $data[14];
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($data[28] * $data[30] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	doOutput($mr);

  $mr[Grootboekrekening] = "RENME";
  $mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = abs($data[23]);
  $mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
    doOutput($mr);

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);
    
  $mr[Grootboekrekening] = "KOBU";       // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  
    
  checkControleBedrag($controleBedrag);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A_O()  // Aankoop van opties
{

  global $fonds, $data, $mr, $output;
  
	$mr = array();
	$mr[aktie]             = "A/O";
	do_algemeen();
	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $data[29];
	$mr[Valutakoers]       = $data[14];
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($data[28] * $data[30] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	doOutput($mr);

  $mr[Grootboekrekening] = "RENME";
  $mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = abs($data[23]);
  $mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
    doOutput($mr);

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);
  
  $mr[Grootboekrekening] = "KOBU";       // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  
  
  checkControleBedrag($controleBedrag);  

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

  $omschrPrefix = "Aankoop";
  if ($data[8] == "s1ap" OR $data[8] == "s1ac")
  {
    $omschrPrefix = "Assignment";
  }

	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = $omschrPrefix." ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $data[29];
	$mr[Valutakoers]       = $data[14];
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($data[28] * $data[30] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	doOutput($mr);

  $mr[Grootboekrekening] = "RENME";
  $mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = abs($data[23]);
  $mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
    doOutput($mr);

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);
  
  $mr[Grootboekrekening] = "KOBU";    // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  
    
  checkControleBedrag($controleBedrag);  

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "V";
	do_algemeen();
	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $data[29];
	//$mr[Valutakoers]       = $data[14];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	doOutput($mr);

	if ($data[14] <> 0 ) // verkoop van obligaties
	{
	  $mr[Grootboekrekening] = "RENOB";
    $mr[Valuta]            = $data[24];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = abs($data[23]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
		  doOutput($mr);
	}

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);

  $mr[Grootboekrekening] = "KOBU";    // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  

    
  checkControleBedrag($controleBedrag);  
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
  $omschrPrefix = "Verkoop";
  if ($data[8] == "s1ep" OR $data[8] == "s1ec")
  {
    $omschrPrefix = "Exercise";
  }

	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = $omschrPrefix." ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $data[29];
	$mr[Valutakoers]       = $data[14];
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	doOutput($mr);

	if ($data[14] <> 0 ) // verkoop van obligaties
	{
	  $mr[Grootboekrekening] = "RENOB";
    $mr[Valuta]            = $data[24];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = abs($data[23]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
		  doOutput($mr);
	}

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);

  $mr[Grootboekrekening] = "KOBU";    // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  

    
  checkControleBedrag($controleBedrag);  
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
	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $data[29];
	$mr[Valutakoers]       = $data[14];
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[28];
	$mr[Fondskoers]        = $data[30];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	doOutput($mr);

	if ($data[14] <> 0 ) // verkoop van obligaties
	{
	  $mr[Grootboekrekening] = "RENOB";
    $mr[Valuta]            = $data[24];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = abs($data[23]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
		  doOutput($mr);
	}

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);

  $mr[Grootboekrekening] = "KOBU";    // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  

  
  checkControleBedrag($controleBedrag);  
}

function do_L()  //Lichting van stukken
{
  global $data,$fonds,$mr,$output;
  $mr[aktie]              = "L";
  do_algemeen();
  $mr[Rekening]          = $data[6]."MEM";
  $mr[Omschrijving]      = "Lichting  ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $data[29];
  $mr[Valutakoers]       = $data[14];
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[28];
  $mr[Fondskoers]        = $data[30];
  $mr[Debet]             = 0;
  $mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
  $mr[Bedrag]            = _creditbedrag();
  $controleBedrag        = $mr[Bedrag];
  $mr[Transactietype]    = "L";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 1;
  doOutput($mr);

  if  ($mr[Bedrag] <> 0)
  {
    $mr[Grootboekrekening] = "ONTTR";
    $mr[Fonds]             = "";
    $mr[Valuta]            = "EUR";
    $mr[Valutakoers]       = 1;
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($mr[Bedrag]);
    $mr[Credit]            = 0;

//TODO:kan debrag via functie?

    $mr[Bedrag]            = $mr[Debet] * -1;
    $mr[Transactietype]    = "";
    doOutput($mr);
  }

  if ($data[23] > 0)
  {

	 	$mr[Grootboekrekening] = "RENOB";
	  $mr[Valuta]            = $fonds[Valuta];
	 	$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
		$mr[Credit]            = abs($data[23]);
    $mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]);
    $controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 1;

		doOutput($mr);

    $mr[Grootboekrekening] = "ONTTR";
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($data[23]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
    $mr[Transactietype]    = "";

  	doOutput($mr);

  }
  
  $data[21] = $data[20];  // voor do_l, do_lo en do_d ander notebedrag gebruiken
  checkControleBedrag($controleBedrag);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_LO()  //Lichting Openen
{
  global $data,$fonds,$mr,$output;
  $mr[aktie]              = "LO";
  do_algemeen();
  $mr[Rekening]          = $data[6]."MEM";
  $mr[Omschrijving]      = "Lichting  ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $data[29];
  $mr[Valutakoers]       = $data[14];
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[28];
  $mr[Fondskoers]        = $data[30];
  $mr[Debet]             = 0;
  $mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
  $mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "V/O";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 1;
  doOutput($mr);

  if  ($mr[Bedrag] <> 0)
  {
    $mr[Grootboekrekening] = "ONTTR";
    $mr[Fonds]             = "";
    $mr[Valuta]            = "EUR";
    $mr[Valutakoers]       = 1;
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($mr[Bedrag]);
    $mr[Credit]            = 0;
//TODO:kan debrag via functie?
    $mr[Bedrag]            = $mr[Debet] * -1;
    $mr[Transactietype]    = "";
    doOutput($mr);
  }

  if ($data[23] > 0)
  {

	 	$mr[Grootboekrekening] = "RENOB";
	  $mr[Valuta]            = $fonds[Valuta];
	 	$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
		$mr[Credit]            = abs($data[23]);
//TODO:kan debrag via functie?
    $mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]);
    $controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 1;

		doOutput($mr);

    $mr[Grootboekrekening] = "ONTTR";
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($data[23]);
		$mr[Credit]            = 0;
//TODO:kan debrag via functie?
		$mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
    $mr[Transactietype]    = "";

  	doOutput($mr);

  }
  
  $data[21] = $data[20];  // voor do_l, do_lo en do_d ander notebedrag gebruiken
  checkControleBedrag($controleBedrag);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DS()  //Deponering sluiten
{

  global $data,$fonds,$mr,$output;
  $mr[aktie]              = "DS";
  do_algemeen();
  $mr[Rekening]          = $data[6]."MEM";
  $mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $data[29];
  $mr[Valutakoers]       = $data[14];
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[28];
  $mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
  $mr[Credit]            = 0;
  $mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "A/S";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 1;
  doOutput($mr);

  if  ($mr[Bedrag] <> 0)
  {
    $mr[Grootboekrekening] = "STORT";
    $mr[Fonds]             = "";
    $mr[Valuta]            = "EUR";
    $mr[Valutakoers]       = 1;
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($mr[Bedrag]);
    $mr[Bedrag]            = $mr[Credit];
    $mr[Transactietype]    = "";
    doOutput($mr);
  }

  if ($data[23] > 0)
  {

	 	$mr[Grootboekrekening] = "RENME";
	  $mr[Valuta]            = $fonds[Valuta];
	  $mr[Valutakoers]       = _valutakoers();
	  $mr[Fonds]             = $fonds[Fonds];
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  $mr[Debet]             = abs($data[23]);
	  $mr[Credit]            = 0;
//TODO:kan debrag via functie?
	  $mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";
	  $mr[Verwerkt]          = 0;
	  $mr[Memoriaalboeking]  = 1;

	  doOutput($mr);

    $mr[Grootboekrekening] = "STORT";
    $mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[23]);
//TODO:kan debrag via functie?
    $mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]);
    $mr[Transactietype]    = "";

    doOutput($mr);

  }
  $data[21] = $data[20];  // voor do_l, do_lo en do_d ander notebedrag gebruiken
  checkControleBedrag($controleBedrag);

}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_R()  //Coupon rente
{
  global $fonds, $data, $mr, $output;


  // nog geen geldige data in CSV dus voor nu overslaan

	$mr = array();
	$mr[aktie]              = "R";
	do_algemeen();
	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = "Coupon ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "RENOB";
  $mr[Valuta]            = $data[24];
	$mr[Valutakoers]       = _valutakoers();	
  $mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($data[23]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  $controleBedrag       += $mr[Bedrag];
  
  doOutput($mr);
  
  
  $mr[Grootboekrekening] = "DIVBE";       // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  
  
  $mr[Grootboekrekening] = "KNBA";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);
  
  checkControleBedrag($controleBedrag);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output, $afw;


  // nog geen geldige data in CSV dus voor nu overslaan

	$mr = array();
	$mr[aktie]              = "DV";
	do_algemeen();
	$mr[Rekening]          = getRekeningNr($data[6],$data[22]);
	$mr[Omschrijving]      = "Dividend ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "DIV";
	$mr[Valuta]            = $data[24];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($data[23]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  $controleBedrag       += $mr[Bedrag];
	
  doOutput($mr);
  

  $mr[Grootboekrekening] = "DIVBE";       // boeking buitenlandse belastingen
	$mr[Valuta]            = $data[32];
  $mr[Valutakoers]       = _valutakoers();
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[31]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);  
  
  $mr[Grootboekrekening] = "KNBA";
	$mr[Valuta]            = $data[18];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[17]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";

  // call 4857 aanpassing start
  $mr = $afw->reWrite("KNBA", $mr);
  // call 4857 aanpassing stop

	if ($mr[Bedrag] <> 0)
		doOutput($mr);

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[16];
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[15]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  doOutput($mr);
  
  checkControleBedrag($controleBedrag);

}




function do_D()  // Deponering van stukken
{
  global $data,$fonds,$mr,$output;
  $mr[aktie]              = "D";
  do_algemeen();
  $mr[Rekening]          = $data[6]."MEM";
  $mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $data[29];
  $mr[Valutakoers]       = $data[14];
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[28];
  $mr[Fondskoers]        = $data[30];
  $mr[Debet]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
  $mr[Credit]            = 0;
  $mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "D";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 1;
  doOutput($mr);

  if  ($mr[Bedrag] <> 0)
  {
    $mr[Grootboekrekening] = "STORT";
    $mr[Fonds]             = "";
    $mr[Valuta]            = "EUR";
    $mr[Valutakoers]       = 1;
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($mr[Bedrag]);
    $mr[Bedrag]            = $mr[Credit];
    $mr[Transactietype]    = "";
    doOutput($mr);
  }

  if ($data[23] > 0)
  {

	 	$mr[Grootboekrekening] = "RENME";
	  $mr[Valuta]            = $fonds[Valuta];
	  $mr[Valutakoers]       = _valutakoers();
	  $mr[Fonds]             = $fonds[Fonds];
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  $mr[Debet]             = abs($data[23]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";
	  $mr[Verwerkt]          = 0;
	  $mr[Memoriaalboeking]  = 1;

	  doOutput($mr);

    $mr[Grootboekrekening] = "STORT";
    $mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[23]);
	  $mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]);
    $mr[Transactietype]    = "";

    doOutput($mr);

  }
  
  $data[21] = $data[20];  // voor do_l, do_lo en do_d ander notebedrag gebruiken
  checkControleBedrag(-1 * $controleBedrag);
}

function do_Mutatie()
{

	global $data,$mr,$output, $transactieCodes;
  $searchArray = array();
  foreach ($transactieCodes as $key=>$value)
  {
    $searchArray[] = substr($key,1);
  }
    

	$mr = array();
	$mr[aktie]              = "Mut.";
	do_algemeen();
  $dateVeld = $data[46];
	$datum = substr($dateVeld,0,4).'-'.substr($dateVeld,4,2).'-'.substr($dateVeld,6,2);
	$mr[Boekdatum]         = $datum;
	$mr[Rekening]          = intval($data[40]).$data[43];
	$mr[Omschrijving]      = "";
	$mr[Grootboekrekening] = "MUT";
	$mr[Valuta]            = $data[43];    // dbs2778
  $mr[Valutakoers]       = 1;
  if ($mr[Valuta] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr[Valuta]."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr[Valutakoers] = $laatsteKoers[Koers];
  }

	$mr[Fonds]             = "";
	$mr[Aantal]            = '';
	$mr[Fondskoers]        = '';

	$mr[Debet]             = 0;
	$mr[Credit]            = 0;
	$mr[Bedrag]            = $data[42];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;


	foreach ($searchArray as $string)
	{
	  if(substr($data[41],1) == $string) $overslaan = true;
	}

  $mr[Omschrijving] 			= $data[47];
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
    $mr[Bedrag]       			= $mr[Debet] * -1;
  }

  switch ($data[41])
  {
    case 'c1it':
      $mr[Grootboekrekening] 	= "RENTE";
      break;
    case 'c1cf':
      $mr[Grootboekrekening] 	= "BEW";
      break;
    case 'c1mf':
    case 'c1ad':
      $mr[Grootboekrekening] 	= "BEH";
      break;
    case 'c1vm':
      $mr[Grootboekrekening] 	= "VMAR";
      break;
    case 'c1fx':
      $mr[Grootboekrekening] 	= "KRUIS";
      break;
    default:
  }

  if($overslaan != true)
	  doOutput($mr);
  
}


function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}

?>