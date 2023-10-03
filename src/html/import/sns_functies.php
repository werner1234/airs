<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/06/28 09:17:37 $
 		File Versie					: $Revision: 1.11 $

 		$Log: sns_functies.php,v $
 		Revision 1.11  2011/06/28 09:17:37  cvs
 		fondscode aanpassingen
 		
 		Revision 1.10  2009/03/31 08:45:19  cvs
 		*** empty log message ***
 		
 		Revision 1.9  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.8  2008/07/23 10:24:37  cvs
 		*** empty log message ***

 		Revision 1.7  2008/06/24 10:35:09  cvs
 		*** empty log message ***

 		Revision 1.6  2008/06/18 07:22:25  cvs
 		*** empty log message ***

 		Revision 1.5  2008/06/16 15:03:22  cvs
 		$valutaLookup in do_algemeen() resetten

 		Revision 1.4  2008/06/09 12:11:11  cvs
 		_valutaKoers_aanEnVerkoop() $data global gemaakt

 		Revision 1.3  2008/06/09 10:02:01  cvs
 		do_mutatie valuta forceren naar EURO

 		Revision 1.2  2008/05/29 15:31:19  cvs
 		diverse tweaks op aanwijzing van Theo

 		Revision 1.1  2008/05/27 15:19:15  cvs
 		- SNS import do_V en do_DV
 		- StroeveVT import datum selecteerbaar
*/

function cleanRow($data)
{
	foreach ($data as $value)
	{
	  $value=trim($value);
	  $value = str_replace(',','.',$value);
	  if(substr($value,-1,1) == '-')
	  {
	    $valueZonderMin = str_replace('-','',$value);
	    if(isNumeric($valueZonderMin))
	     $value = $valueZonderMin * -1;
	  }
	  $tmp[]=$value;
	}
	return $tmp;
}


function _debetbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[16];
	if ($valutaLookup == true)
	  return -1 * $mr[Debet];
	else
	  return -1 * ($mr[Debet] * $mr[Valutakoers]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[16];
	if ($valutaLookup == true)
	  return $mr[Credit];
	else
	  return $mr[Credit]  * $mr[Valutakoers];
}

function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[16];

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
	  return 1/$data[7];
}

function _valutaKoers_aanEnVerkoop()
{
   global $data;
   $valutaKoers = trim($data[7]);
   return 1/$valutaKoers;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file, $valutaLookup;

	$datum = substr($data[5],0,4).'-'.substr($data[5],4,2).'-'.substr($data[5],6,2);
	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
	$mr[Boekdatum]         = $datum;

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
	$mr[Rekening]          = $data[3].$data[11];
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutaKoers_aanEnVerkoop();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[15];
	$mr[Fondskoers]        = $data[17];
  $mr[Debet]             = abs($data[15] * $data[17] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  $mr[Grootboekrekening] = "RENME";
  $mr[Valuta]            = $data[13];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = abs($data[12]);
  $mr[Bedrag]            = _debetbedrag();
  $mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
    $output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[11];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[8]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
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

function do_A_S()  // Aankoop sluiten
{

  global $fonds, $data, $mr, $output;

	$mr = array();
	$mr[aktie]             = "AS";
	do_algemeen();
	$mr[Rekening]          = $data[3].$data[11];
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutaKoers_aanEnVerkoop();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[15];
	$mr[Fondskoers]        = $data[17];
  $mr[Debet]             = abs($data[15] * $data[17] * $fonds[Fondseenheid]); //abs($data[19]/$mr[Valutakoers]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  $mr[Grootboekrekening] = "RENME";
  $mr[Valuta]            = $data[13];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = abs($data[12]);
  $mr[Bedrag]            = _debetbedrag();
  $mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
    $output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[11];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[8]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
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

function do_V()
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "V";
	do_algemeen();
	$mr[Rekening]          = $data[3].$data[11];
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutaKoers_aanEnVerkoop();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[15];
	$mr[Fondskoers]        = $data[17];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	if ($data[7] <> 0 ) // verkoop van obligaties
	{
	  $mr[Grootboekrekening] = "RENOB";
    $mr[Valuta]            = $data[13];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = abs($data[12]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
		  $output[] = $mr;
	}

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[11];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[8]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
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

function do_V_O() // Verkoop openen
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "VO";
	do_algemeen();
	$mr[Rekening]          = $data[3].$data[11];
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutaKoers_aanEnVerkoop();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[15];
	$mr[Fondskoers]        = $data[17];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);  //abs($data[19]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	if ($data[7] <> 0 ) // verkoop van obligaties
	{
	  $mr[Grootboekrekening] = "RENOB";
    $mr[Valuta]            = $data[13];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = abs($data[12]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
		  $output[] = $mr;
	}

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[11];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[8]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[23]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	  $output[] = $mr;
}

function do_L()  //Lossing van obligaties
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "L";
	do_algemeen();
	$mr[Rekening]          = $data[3].$data[11];
	$mr[Omschrijving]      = "Lossing ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutaKoers_aanEnVerkoop();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[15];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "L";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;
	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[12]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "DV";
	do_algemeen();
	$mr[Rekening]          = $data[3].$data[11];
	$mr[Omschrijving]      = "Dividend ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "DIV";
	$mr[Valuta]            = "EUR";
	$mr[Valutakoers]       = 1;
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($data[10]) + abs($data[25]);
	$mr[Bedrag]            = $mr[Credit];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "DIVBE";
	$mr[Valuta]            = "EUR";
	$mr[Valutakoers]       = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[25]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KNBA";
	$mr[Valuta]            = $data[11];
	if ($data[9] <> "EUR")
	  $mr[Valutakoers]       = _valutakoers();
	else
	  $mr[Valutakoers]       = 1;
	//$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[8]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
  if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[24];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[23]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

}


function do_DV_R()
{
 	global $data,$mr,$output;
  if($data[14] == 1)
   do_DV();
  else
   do_R();
}

function do_D($record)  // Deponering van stukken
{

  global $data,$fonds,$mr,$output;
  $mr[aktie]              = "D";
  do_algemeen();
  $mr[Rekening]          = $data[3].$data[11];
  $mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $fonds[Valuta];
  $mr[Valutakoers]       = _valutaKoers_aanEnVerkoop();
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[15];
  $mr[Fondskoers]        = $data[fondskoers];
  $mr[Debet]             = abs($data[15] * $data[fondskoers] * $fonds[Fondseenheid]);
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

}

function do_Mutatie()
{

	global $data,$mr,$output;
	$mr = array();
	$mr[aktie]              = "Mutatie";
	do_algemeen();
	$mr[Rekening]          = intval($data[2]).$data[7];
	$mr[Omschrijving]      = "";
	$mr[Grootboekrekening] = "MUT";
//	$mr[Valuta]            = $data[7];
//	$mr[Valutakoers]       = _valutakoers();
	$mr[Valuta]            = "EUR";
	$mr[Valutakoers]       = 1;
	$mr[Fonds]             = "";
	$mr[Aantal]            = '';
	$mr[Fondskoers]        = '';

	$mr[Debet]             = 0;
	$mr[Credit]            = 0;
	$mr[Bedrag]            = $data[6];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$search = array('DIVIDENDNOTA','EFFECTENNOTA','COUPONNOTA');
	foreach ($search as $string)
	{
	  if(strstr($data[8],$string))
	    $overslaan = true;
	}


	if(strstr($data[8],'RENTE'))
	{
	  $mr[Grootboekrekening] 	= "RENTE";
	  if($data[6] > 0)
	  {
	    $mr[Omschrijving] = "Creditrente";
      $mr[Debet]        = 0;
      $mr[Credit]       = $data[6];
      $mr[Bedrag]       = $mr[Credit];
	  }
	  else
    {
      $mr[Omschrijving] = "Debetrente";
      $mr[Debet]        = abs($data[6]);
      $mr[Credit]       = 0;
      $mr[Bedrag]       = _debetbedrag();
    }
  }
  elseif (strstr($data[8],"BEWAARLOON"))
  {
    $mr[Grootboekrekening] 	= "BEW";
    $mr[Omschrijving]				= "Bewaarloon";
    $mr[Debet]        			= abs($data[6]);
    $mr[Credit]       			= 0;
    $mr[Bedrag]       			= _debetbedrag();
  }
  else
  {
    $mr[Omschrijving] 			= $data[8];
    if($data[6] > 0)
    {
      $mr[Grootboekrekening] 	= "STORT";
      $mr[Debet]        			=	0;
      $mr[Credit]       			= abs($data[6]);
      $mr[Bedrag]       			= $mr[Credit];
    }
    else
    {
      $mr[Grootboekrekening] 	= "ONTTR";
      $mr[Debet]			        = abs($data[6]);
      $mr[Credit]       			= 0;
      $mr[Bedrag]       			= _debetbedrag();
    }
  }

  if($overslaan != true)
	  $output[] = $mr;

}


function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}

?>