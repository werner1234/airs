<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2009/07/06 13:10:20 $
 		File Versie					: $Revision: 1.6 $

 		$Log: atn_functies.php,v $
 		Revision 1.6  2009/07/06 13:10:20  cvs
 		datum formatering boekdatum
 		
 		Revision 1.5  2009/07/06 13:01:09  cvs
 		bedrag uit veld 10 ipv 8

 		Revision 1.4  2009/06/02 12:02:28  cvs
 		*** empty log message ***

 		Revision 1.3  2009/05/19 06:54:40  cvs
 		*** empty log message ***

 		Revision 1.2  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.1  2008/07/24 06:22:57  cvs
 		ATN import toevoegen



*/

function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}

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
	return 1;
	/*
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
  */
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
	$mr[Boekdatum]         = substr($data[3],0,10);
	$mr[Aantal]            = cnvBedrag($data[4]);
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
	$mr[Rekening]          = trim($data[20])."MEM";
	$mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];

	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Fondskoers]        = cnvBedrag($data[10]);
	$mr[Debet]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
	$mr[Transactietype]    = "D";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 1;

	$output[] = $mr;
  if ($mr[Bedrag] <> 0 AND $data[1] <> 5)
  {
	  $mr[Grootboekrekening] = "STORT";
	  $mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = $mr[Debet];
	  $mr[Debet]             = 0;
  	$mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]);  // 2008-04-17 cvs valutacorrectie
 		$mr[Transactietype]    = "";
    $output[] = $mr;
  }

  $mr[Grootboekrekening] = "KOST";

  $mr[Fonds]             = "";
	$mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = abs(cnvBedrag($data[16]));
  $mr[Credit]            = 0;
  $mr[Bedrag]            = -1 * ($mr[Debet]);
  $mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
		  $output[] = $mr;


	if ($mr[Bedrag] <> 0 AND $data[1] <> 5)
  {
	  $mr[Grootboekrekening] = "STORT";
	  $mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = $mr[Debet];
	  $mr[Debet]             = 0;
  	$mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]);  // 2008-04-17 cvs valutacorrectie
 		$mr[Transactietype]    = "";
    $output[] = $mr;
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
		$mr[Rekening]          = trim($data[20])."MEM";
		$mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = -1 * $mr[Aantal];
		$mr[Fondskoers]        = cnvBedrag($data[10]);
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
		$mr[Bedrag]            =  $mr[Credit] * $mr[Valutakoers];
		$mr[Transactietype]    = "L";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 1;

		$output[] = $mr;
    if ($mr[Bedrag] <> 0 AND $data[1] <> 6)
    {
      if ($data[1] == 20)
		    $mr[Grootboekrekening] = "BEH";
		  else
		    $mr[Grootboekrekening] = "ONTTR";
		  $mr[Fonds]             = "";
		  $mr[Aantal]            = 0;
		  $mr[Fondskoers]        = 0;
		  $mr[Debet]             = $mr[Credit];
		  $mr[Credit]            = 0;
		  $mr[Bedrag]            = -1 * ($mr["Debet"] *  $mr["Valutakoers"]);  // 2008-04-17 cvs valutacorrectie
		  $mr[Transactietype]    = "";

		  $output[] = $mr;
    }


    $mr[Grootboekrekening] = "KOST";

    $mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs(cnvBedrag($data[16]));
    $mr[Credit]            = 0;
    $mr[Bedrag]            = -1 * ($mr[Debet]);
    $mr[Transactietype]    = "";
    if ($mr[Bedrag] <> 0)
		  $output[] = $mr;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NY()  //nog niet aanwezig
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