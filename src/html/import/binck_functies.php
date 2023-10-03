<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/03 06:54:42 $
 		File Versie					: $Revision: 1.12 $

 		$Log: binck_functies.php,v $
 		Revision 1.12  2018/07/03 06:54:42  cvs
 		no message
 		
 		Revision 1.11  2008/10/01 07:30:53  cvs
 		in do_AFL()
 		 -1 x bij aantal verwijderd
 		
 		Revision 1.10  2007/12/07 10:14:05  cvs
 		diverse kleine aanpassingen transactie import

 		Revision 1.9  2007/08/16 07:18:52  cvs
 		bij do_OG
 		debet bedrag * -1 bij Onttr

 		Revision 1.8  2007/08/15 07:14:42  cvs
 		omzetten naar nieuwe indeling van CSV bestand

 		Revision 1.7  2005/09/21 07:53:48  cvs
 		nieuwe commit 21-9-2005

 		Revision 1.6  2005/07/12 15:03:37  cvs
 		*** empty log message ***

 		Revision 1.5  2005/07/11 11:31:02  cvs
 		*** empty log message ***

 		Revision 1.4  2005/07/11 10:57:19  cvs
 		na eerste test van Theo gevonden problemen/aanpassingen

 		Revision 1.3  2005/05/21 12:37:08  cvs
 		*** empty log message ***

 		Revision 1.2  2005/05/19 15:17:04  cvs
 		einde dag 19-5

 		Revision 1.1  2005/05/17 13:09:38  cvs
 		*** empty log message ***

 		Revision 1.1  2005/05/17 10:38:01  cvs
 		functies naar apart bestand



*/


$_transactiecodes = Array("K","V","OK","OV","SK","SV","EX C","EX P",
	                        "AS C","AS P","EMIS","RTDB","RTCR","AFL",
	                        "UITK","UITK + DIV","O-G1","O-G","D","L");



function _debetbedrag()
{
	global $data, $mr;
	if ($data[9] == "USD" AND !strstr($mr[Rekening],"MEM") )
/*
	  $valuta = $data[9];
	else
		$valuta = "EUR";
	if ($valuta == $mr[Valuta])
*/
	  return -1 * $mr[Debet];
	else
	  return -1 * ($mr[Debet] * $mr[Valutakoers]);
}

function _creditbedrag()
{
	global $data, $mr;
	if ($data[9] == "USD" AND !strstr($mr[Rekening],"MEM"))
/*
	  $valuta = $data[9];
	else
		$valuta = "EUR";
	if ($valuta == $mr[Valuta])
*/
	 return $mr[Credit];
	else
	  return $mr[Credit] * $mr[Valutakoers];
}

function _valutakoers()
{
	global $data;
	$valuta  = $data[9];
	$_bedrag = $data[8];

	if ($valuta <> "PNC")
		return (1/$_bedrag);
	else
	  return (1/($_bedrag/100));
}

function _fondskoers()
{
	global $data, $fonds;

	$valuta  = $data[9];
	$_bedrag = $data[11];

	if ($valuta <> "PNC")
		return $_bedrag;
	else
	  return $_bedrag/100;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

	$datum = $data[4];
	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
	$mr[Boekdatum]         = substr($datum,0,4)."-".substr($datum,4,2)."-".substr($datum,6,2);
	$_rek = Trim($data[9]);
	if ($_rek <> "EUR" AND $_rek <> "USD")
	  $mr[Rekening] = trim($data[1])."EUR";
	else
	  $mr[Rekening] = trim($data[1]).$_rek;

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_K()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "K";
	do_algemeen();

	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($mr[Valuta] == "EUR")
	{
	  $mr[Debet]             = abs($data[13]);
	  $mr[Valutakoers]       = 1;
	}
	else
	  $mr[Debet]             = abs($data[13]*$data[8]);

	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";

	$output[] = $mr;

	if ($data[15] <> 0)
	{
		$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		if ($mr[Valuta] == "EUR")
		  $mr[Valutakoers] = 1;

		$mr[Debet]             = abs($data[15]);
		$mr[Bedrag]            = _debetbedrag();
		$output[] = $mr;
	}
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
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[10];
	$mr[Fondskoers]        = _fondskoers();
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	if ($mr[Valuta] == "EUR")
	{
	  $mr[Debet]             = abs($data[13]);
	  $mr[Valutakoers]       = 1;
	}
	else
	  $mr[Debet]             = abs($data[13]*$data[8]);

	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";

	$output[] = $mr;

  if ($data[15] <> 0)
	{
		$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		if ($mr[Valuta] == "EUR")
		  $mr[Valutakoers] = 1;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[15]);
		$mr[Bedrag]            = abs(_creditbedrag());
		$output[] = $mr;
	}

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OK()  //Aankoop openen bij opties en futures
{

  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "OK";
	do_algemeen();

	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";

	if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($mr[Valutakoers] == "EUR")
	  $mr[Debet]             = abs($data[13]);
	else
	  $mr[Debet]             = abs($data[13]*$data[8]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";

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
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[10];
  $mr[Fondskoers]        = _fondskoers();
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	if ($mr[Valutakoers] == "EUR")
	  $mr[Debet]             = abs($data[13]);
	else
	  $mr[Debet]             = abs($data[13]*$data[8]);
	$mr[Bedrag]            = _debetbedrag();

	$mr[Transactietype]    = "";

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SK()  //Aankoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "SK";
	do_algemeen();

	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($mr[Valutakoers] == "EUR")
	  $mr[Debet]             = abs($data[13]);
	else
	  $mr[Debet]             = abs($data[13]*$data[8]);
	$mr[Bedrag]            = _debetbedrag();

	$mr[Transactietype]    = "";

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
	$mr["Omschrijving"]      = "Verkoop ".$fonds[Omschrijving];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[10];
	$mr[Fondskoers]        = _fondskoers();
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
  $mr[Valuta]            = $data[9];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	if ($mr[Valutakoers] == "EUR")
	  $mr[Debet]             = abs($data[13]);
	else
	  $mr[Debet]             = abs($data[13]*$data[8]);
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";

	$output[] = $mr;

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EX_C()  //Exercise Call optie
{
  return do_SV();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EX_P()  //Exercise Call optie
{
  return do_SV();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_C()  //Exercise Call optie
{
  return do_SK();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_P()  //Exercise Call optie
{
  return do_SV();
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EMIS()  // Emissie van stukken
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "EMIS";
	do_algemeen();

	$mr[Omschrijving]      = "Emissie ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
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


function do_RTDB()  //Geldrente debetboeking
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "RTDB";
	do_algemeen();
	if ($data[3])
	{
    $mr[Omschrijving]      = "Debetrente";
	  $mr[Grootboekrekening] = "RENTE";
		$mr[Valuta]            = $data[9];
		$mr[Valutakoers]       = (1/$data[8]);
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = abs($data[10]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		$output[] = $mr;
	}

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_RTCR()  //Geldrente debetboeking
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "RTCR";
	do_algemeen();
	if ($data[3])
	{
    $mr[Omschrijving]      = "Creditrente";
	  $mr[Grootboekrekening] = "RENTE";
		$mr[Valuta]            = $data[9];
		$mr[Valutakoers]       = (1/$data[8]);
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[10]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		$output[] = $mr;
	}

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AFL()  //Lossing van obligaties
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "AFL";
	do_algemeen();
	$mr[Omschrijving]      = "Lossing ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($mr[Valutakoers] == "EUR")
	  $mr[Debet]             = abs($data[13]);
	else
	  $mr[Debet]             = abs($data[13]*$data[8]);

	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "";

	$output[] = $mr;

	if( $data[15] <> 0)
	{
		$mr[Grootboekrekening] = "RENME";
		$mr[Valuta]            = $data[9];
		if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  	if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  	$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[15]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "";

		$output[] = $mr;

	}
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_UITK()  //Rente obligaties /contant dividend
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$_soort = trim($data[20]);
	$mr[aktie]              = "UITK + ".$_soort;
	do_algemeen();

	if ($_soort == "COUP")
	{
	  $mr[Omschrijving]      = "Rente ".$fonds[Omschrijving];
	  $mr[Grootboekrekening] = "RENOB";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs(($data[10] * $data[11]) * $fonds[Fondseenheid]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		$output[] = $mr;

	}
	else // CONTANT DIVIDEND
	{

	  $mr[Omschrijving]      = "Dividend ".$fonds[Omschrijving];
	  $mr[Grootboekrekening] = "DIV";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs(($data[10] * $data[11]) * $fonds[Fondseenheid]);
		if ($data[9] == "PNC")
		  $mr[Credit] = $mr[Credit]/100;

		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		$output[] = $mr;

		$mr[Grootboekrekening] = "DIVBE";
		$mr[Valuta]            = $data[9];
		if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  	if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		if ($mr[Valuta] == "USD")
		  $mr[Debet]             = abs($data[14] * $data[8]);
		else
			$mr[Debet]             = abs($data[14]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = -1 * $mr[Debet];

		$output[] = $mr;

	}


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_O_G1()  //Bewaarloon
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "O-GI";
	do_algemeen();
	$mr[Omschrijving]      = "Bewaarloon effecten";
	$mr[Grootboekrekening] = "BEW";
	$mr[Valuta]            = $data[9];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = ($data[12] * -1);
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

function do_O_G()  //Opname van Geld
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "O-G";
	do_algemeen();
	$mr[Valuta]            = $data[9];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  $mr[Omschrijving]      = $data[24];
	if ($data[12] < 0)
	{
	  if ($mr[Omschrijving] == "Kosten afschriften")
	    $mr[Grootboekrekening] = "KNBA";
	  else
		  $mr[Grootboekrekening] = "ONTTR";

		$mr[Debet]             = ($data[12] * -1);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
	}
	else
	{
		$mr[Grootboekrekening] = "STORT";
		$mr[Omschrijving]      = $data[25];
		$mr[Debet]             = 0;
		$mr[Credit]            = $data[12];
		$mr[Bedrag]            = _creditbedrag();
	}
	$output[] = $mr;

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_D()  // Deponering van stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
	$mr[aktie]              = "D";
	do_algemeen();

	$mr[Rekening]          = trim($data[1])."MEM";
	$mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = $data[11];
	$mr[Debet]             = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	if ($data[9] == "PNC")
	  $mr[Debet]  = $mr[Debet]/100;
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();

	$mr[Transactietype]    = "D";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 1;

	$output[] = $mr;

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
	if ($data[15] <> 0)
	{

		$mr[Omschrijving]      = "Meegekochte rente ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		$mr[Valuta]            = "EUR";
		$mr[Valutakoers]       = 1;
		$mr[Aantal]            = 0;
		$mr[Fonds]             = "";
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = abs($data[15]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = -1 * $mr[Debet];

		$output[] = $mr;

		$mr[Omschrijving]      = "Opgelopen rente ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "STORT";
		$mr[Valuta]            = "EUR";
		$mr[Valutakoers]       = 1;
		$mr[Aantal]            = 0;
		$mr[Fonds]             = "";
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($mr[Bedrag]);
		$mr[Bedrag]            = $mr[Credit];

		$output[] = $mr;

	}
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_L()  // Lichting van stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
	$mr[aktie]              = "L";
	do_algemeen();

	$mr[Rekening]          = trim($data[1])."MEM";
	$mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = $data[11];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
  if ($data[9] == "PNC")
	  $mr[Credit]  = $mr[Credit]/100;
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "L";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 1;

	$output[] = $mr;

	$mr[Grootboekrekening] = "ONTTR";
	$mr[Fonds]             = "";
	$mr[Valuta]            = "EUR";
	$mr[Valutakoers]       = 1;
	$mr[Aantal]            = 0;
	$mr[Fonds]             = "";
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($mr[Bedrag]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	$mr[Transactietype]    = "";

	$output[] = $mr;
	if ($data[15] <> 0)
	{

		$mr[Omschrijving]      = "Meeverkochte rente ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		$mr[Valuta]            = "EUR";
		$mr[Valutakoers]       = 1;
		$mr[Aantal]            = 0;
		$mr[Fonds]             = "";
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[15]);
		$mr[Bedrag]            = $mr[Credit];

		$output[] = $mr;

		$mr[Omschrijving]      = "Opgelopen rente ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "ONTTR";
		$mr[Valuta]            = "EUR";
		$mr[Valutakoers]       = 1;
		$mr[Aantal]            = 0;
		$mr[Fonds]             = "";
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = abs($mr[Bedrag]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = -1 * $mr[Debet];

		$output[] = $mr;

	}
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