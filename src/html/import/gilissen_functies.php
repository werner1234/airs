<?
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.13 $

 		$Log: gilissen_functies.php,v $
 		Revision 1.13  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/11/07 10:38:17  cvs
 		*** empty log message ***
 		
 		Revision 1.11  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.10  2006/02/03 07:15:09  cvs
 		*** empty log message ***

 		Revision 1.9  2005/12/08 11:04:29  cvs
 		todo 232, geen globals in do_RM()

 		Revision 1.8  2005/11/14 08:49:22  cvs
 		Onttr en dep kosten werden verkeerd berekend
 		todo 225

 		Revision 1.7  2005/11/01 11:21:06  cvs
 		*** empty log message ***

 		Revision 1.6  2005/10/19 17:14:42  cvs
 		fout aankoop openen bedrag werd niet berekend bij KOST




*/


function _debetbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[9];
	if ($valutaLookup == true)
	  return -1 * $mr[Debet];
	else
	  return -1 * ($mr[Debet] * $mr[Valutakoers]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[9];
	if ($valutaLookup == true)
	  return $mr[Credit];
	else
	  return $mr[Credit] * $mr[Valutakoers];
}

function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[9];
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
	  return $data[10];
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

	$datum = explode(".",$data[15]);
	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
	$mr[Boekdatum]         = $datum[2]."-".$datum[1]."-".$datum[0];
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
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	if ($data[7] <> 0)  // aankoop obligatie
	{
	  $mr[Grootboekrekening] = "RENME";
	  $mr[Valuta]            = $data[9];
	  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
	  $mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  $mr[Debet]             = abs($data[7]);
	  $mr[Bedrag]            = _debetbedrag();
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
	  $output[] = $mr;

	}

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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

function do_V()
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "V";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	if ($data[7] <> 0 ) // verkoop van obligaties
	{
	  $mr[Grootboekrekening] = "RENOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    $mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = abs($data[7]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
		  $output[] = $mr;
	}

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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

function do_OA()  //Aankoop openen bij opties en futures
{

  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "OA";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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

function do_OV()  //Verkoop openen bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "OV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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

function do_SA()  //Aankoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "SA";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;
	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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

function do_SV()  //Verkoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "SV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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

function do_TS()  //Expiratie Time Short bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "TS";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_TL()  //Expiratie Time Long bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "TL";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_E()  //Emissie van stukken of claims
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "E";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_R()  //Rente of couponrente
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "R";
	do_algemeen();
	if ($data[3])
	{
		$mr[Rekening]          = trim($data[1]).trim($data[9]);

		if ($data[3])
		  $mr[Omschrijving]      = "Rente ".$fonds[Omschrijving];
		else
		  $mr[Omschrijving]      = $data[22];

		  $mr[Grootboekrekening] = "RENOB";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[7]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		$output[] = $mr;

		$mr[Grootboekrekening] = "KNBA";
		$mr[Valuta]            = $data[9];
		if ($data[9] <> "EUR")
		  $mr[Valutakoers]       = _valutakoers();
		else
		  $mr[Valutakoers]       = 1;
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = abs($data[11]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = -1 * $mr[Debet];

		$output[] = $mr;

		$mr[Grootboekrekening] = "KOBU";
		$mr[Valuta]            = $data[9];
  	if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  	$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Credit]            = 0;
		$mr[Debet]             = abs($data[12]);
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "";
		if ($mr[Bedrag] <> 0)
			$output[] = $mr;

	}
	else
	{
		$mr[Rekening]          = trim($data[1]).trim($data[9]);
		$mr[Omschrijving]      = "Creditrente";
		$mr[Grootboekrekening] = "RENTE";
		$mr[Valuta]            = $data[9];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Credit]            = $data[14]; // deze tijdelijk vullen tbv de _creditbedrag() berekening
		$mr[Bedrag]            = _creditbedrag();
		if ($data[14] > 0)
		{
		  $mr[Debet]             = 0;
		  $mr[Credit]            = abs($data[14]);
		}
		else
		{
			$mr[Omschrijving]      = "Debetrente";
			$mr[Debet]             = abs($data[14]);
			$mr[Credit]            = 0;
		}
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;
		$output[] = $mr;
	}


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()  //Lossing van obligaties
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "L";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Lossing ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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
  $mr[Fonds]             = "";
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
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Dividend ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "DIV";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "DIVBE";
	$mr[Valuta]            = $data[9];
	if ($data[9] <> "EUR")
	  $mr[Valutakoers]       = _valutakoers();
	else
	  $mr[Valutakoers]       = 1;
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[13]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KNBA";
	$mr[Valuta]            = $data[9];
	if ($data[9] <> "EUR")
	  $mr[Valutakoers]       = _valutakoers();
	else
	  $mr[Valutakoers]       = 1;
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];

	$output[] = $mr;
	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
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

function do_DO()  //Stock dividend
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
	$mr[aktie]              = "DO";
	do_algemeen();
	$mr[Rekening]          = trim($data[1])."MEM";
	$mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
	$mr[Transactietype]    = "D";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 1;

	$output[] = $mr;

	$mr[Grootboekrekening] = "STORT";
	if ($mr[Valuta] == "EUR")      $mr[Valutakoers]  = 1;
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[14]);
	$mr[Bedrag]            = $mr[Credit];
	$mr[Transactietype]    = "";
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DT()  //Terugvorderbaar dividend
{
	// wordt niet ingelezen
  return;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RM()  //Rekening mutatie
{
  global $fonds, $data, $mr, $output;
  if ($data[14] < 0)
  {
    	$_deb_1   = abs($data[14]);
    	$_cre_1   = 0;
    	$_bed_1   = -1 * $_deb_1;

    	$_deb_2   = 0;
    	$_cre_2   = abs($data[14]);
    	$_bed_2   = $_cre_2;
  }
  else
  {
    $_deb_1   = 0;
    $_cre_1   = abs($data[14]);
    $_bed_1   = $_cre_1;

    $_deb_2   = abs($data[14]);
    $_cre_2   = 0;
    $_bed_2   = -1 * $_deb_2;
  }
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = "Overboeking deposito";
	$mr[Grootboekrekening] = "KRUIS";
	$mr[Valuta]            = $data[9];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = $_deb_1;
	$mr[Credit]            = $_cre_1;
	$mr[Bedrag]            = $_bed_1;
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Rekening]          = trim($data[1])."DEP";
	$_rekNr = $mr[Rekening];
	$query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
	$tempDB = new DB();
	$tempDB->SQL($query);
  if (!$rekening = $tempDB->lookupRecord())
     $error[] = "Tijdens inlezen do_RM():Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";

	$mr[Grootboekrekening] = "KRUIS";
	$mr[Valuta]            = $data[9];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = $_deb_2;
	$mr[Credit]            = $_cre_2;
	$mr[Bedrag]            = $_bed_2;
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;



}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KO()  //Kosten algemeen
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "`KO";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = $data[22];
	$mr[Grootboekrekening] = "KNBA";
	$mr[Valuta]            = $data[9];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = $data[14];
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KD()  //Kosten depot
{
	global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "KD";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
	$mr[Omschrijving]      = $data[22];
	switch (substr($data[22],0,2))
	{
		case "57":
			$mr[Grootboekrekening] = "BEH";
			break;
		case "19":
			$mr[Grootboekrekening] = "BEW";
			break;
		case "22":
		case "99":
			$mr[Grootboekrekening] = "VKSTO";
			break;
		default:
			$mr[Grootboekrekening] = "KNBA";
			break;
	}
	$mr[Valuta]            = $data[9];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = $data[14];
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;
  return;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KU()  //Kosten uitleen
{
  // wordt niet ingelezen
	return;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OU()  //Opbrengst uitleen
{
  // wordt niet ingelezen
	return;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_ST()  // Storting van geld of stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
	$mr[aktie]              = "ST";
	do_algemeen();
	if ($data[3])
	{
		$mr[Rekening]          = trim($data[1])."MEM";

		if ($data[3])
		  $mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
		else
		  $mr[Omschrijving]      = "Storting van geld";

		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = $data[5];
		$mr[Fondskoers]        = $data[8];
		$mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
		$mr[Transactietype]    = "D";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 1;
    $fondsBedrag = $mr[Bedrag];
		$output[] = $mr;

		$mr[Grootboekrekening] = "STORT";
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
//		$mr[Debet]             = 0;
//		$mr[Credit]            = abs($data[14]);
//		$mr[Bedrag]            = $mr[Credit];
		$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
		$mr[Debet]             = 0;
		$mr[Bedrag]            = $mr[Credit] * $mr[Valutakoers];

		$mr[Transactietype]    = "";



		$output[] = $mr;
	}
	else
	{
		if (substr($data[22],0,2) == "34" or
		    substr($data[22],0,2) == "VT")  //
		{
			$_srt = substr($data[22],0,2);
			$mr[Rekening]          = trim($data[1]).trim($data[9]);
			if ($_srt == "VT")
			  $mr[Omschrijving]      = "Valutatransactie";
			else
			  $mr[Omschrijving]      = "Overboeking deposito";
			$mr[Grootboekrekening] = "KRUIS";
			$mr[Valuta]            = $data[9];
			$mr[Valutakoers]       = _valutakoers();
			$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Debet]             = 0;
			$mr[Credit]            = abs($data[14]);
			$mr[Bedrag]            = $mr[Credit];
			$mr[Transactietype]    = "";
			$mr[Verwerkt]          = 0;
			$mr[Memoriaalboeking]  = 0;

			$output[] = $mr;

			if ($_srt <> "VT")
			{

				$mr[Rekening]          = trim($data[1])."DEP";
				$mr[Grootboekrekening] = "KRUIS";
				$mr[Valuta]            = $data[9];
				$mr[Valutakoers]       = _valutakoers();
				$mr[Fonds]             = "";
				$mr[Aantal]            = 0;
				$mr[Fondskoers]        = 0;
				$mr[Debet]             = abs($data[14]);
				$mr[Credit]            = 0;
				$mr[Bedrag]            = -1 * $mr[Debet];
				$mr[Transactietype]    = "";
				$mr[Verwerkt]          = 0;
				$mr[Memoriaalboeking]  = 0;

				$output[] = $mr;
			}
		}
		else
		{
			$mr[Rekening]          = trim($data[1]).trim($data[9]);
	  	$mr[Omschrijving]      = "Storting van geld";
			$mr[Grootboekrekening] = "STORT";
			$mr[Valuta]            = $data[9];
			$mr[Valutakoers]       = _valutakoers();
			$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Debet]             = 0;
			$mr[Credit]            = abs($data[14]);
			$mr[Bedrag]            = _creditbedrag();
			$mr[Transactietype]    = "";
			$mr[Verwerkt]          = 0;
			$mr[Memoriaalboeking]  = 0;

			$output[] = $mr;
		}

	}
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OP()  // Opname van geld of stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
	$mr[aktie]              = "OP";
	do_algemeen();
	if ($data[3])
	{
		$mr[Rekening]          = trim($data[1])."MEM";
		if ($data[3])
		  $mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
		else
		  $mr[Omschrijving]      = $data[22];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		if ($mr[Valuta] <> "EUR")
		{
		  $DB = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $laatsteKoers = $DB->lookupRecord();
      $mr[Valutakoers] = $laatsteKoers[Koers];
		}
		else
		  $mr[Valutakoers]       = 1;

		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = -1 * $data[5];
		$mr[Fondskoers]        = $data[8];
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
		$mr[Bedrag]            = $mr[Credit] * $mr[Valutakoers];
		$mr[Transactietype]    = "L";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 1;

		$output[] = $mr;

		$mr[Grootboekrekening] = "ONTTR";
		if ($mr[Valuta] == "EUR")      $mr[Valutakoers]  = 1;
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "";

		$output[] = $mr;
	}
	else
	{
		if (substr($data[22],0,2) == "34" OR
		    substr($data[22],0,2) == "VT")  // Geen ISIN en veld 22 begint met "34"
		{
			$_srt = substr($data[22],0,2) == "VT";

			$mr[Rekening]          = trim($data[1]).trim($data[9]);
			if ($_srt == "VT")
			  $mr[Omschrijving]      = "Valutatransactie";
			else
			  $mr[Omschrijving]      = "Overboeking deposito";
			$mr[Grootboekrekening] = "KRUIS";
			$mr[Valuta]            = $data[9];
			$mr[Valutakoers]       = _valutakoers();
			$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Debet]             = abs($data[14]);
			$mr[Credit]            = 0;
			$mr[Bedrag]            = -1 * $mr[Debet];
			$mr[Transactietype]    = "";
			$mr[Verwerkt]          = 0;
			$mr[Memoriaalboeking]  = 0;

			$output[] = $mr;
      if ($_srt <> "VT")
      {
				$mr[Rekening]          = trim($data[1])."DEP";
				$mr[Grootboekrekening] = "KRUIS";
				$mr[Valuta]            = $data[9];
				$mr[Valutakoers]       = _valutakoers();
				$mr[Fonds]             = "";
				$mr[Aantal]            = 0;
				$mr[Fondskoers]        = 0;
				$mr[Debet]             = 0;
				$mr[Credit]            = abs($data[14]);
				$mr[Bedrag]            = $mr[Credit];
				$mr[Transactietype]    = "";
				$mr[Verwerkt]          = 0;
				$mr[Memoriaalboeking]  = 0;

				$output[] = $mr;
      }
		}
		else
		{
			$mr[Rekening]          = trim($data[1]).trim($data[9]);
	  	$mr[Omschrijving]      = "Opname van geld";
			$mr[Grootboekrekening] = "ONTTR";
			$mr[Valuta]            = $data[9];
			$mr[Valutakoers]       = _valutakoers();
			$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Debet]             = abs($data[14]);
			$mr[Credit]            = 0;
			$mr[Bedrag]            = _debetbedrag();
			$mr[Transactietype]    = "";
			$mr[Verwerkt]          = 0;
			$mr[Memoriaalboeking]  = 0;

			$output[] = $mr;
		}

	}

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VM()  //Variation margin
{
	// wordt niet ingelezen
  return;
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