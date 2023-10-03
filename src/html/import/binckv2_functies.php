<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2015/12/01 09:02:28 $
 		File Versie					: $Revision: 1.26 $

 		$Log: binckv2_functies.php,v $
 		Revision 1.26  2015/12/01 09:02:28  cvs
 		*** empty log message ***
 		
 		Revision 1.25  2014/03/12 10:02:40  cvs
 		*** empty log message ***
 		
 		Revision 1.24  2013/12/16 08:21:00  cvs
 		*** empty log message ***

 		Revision 1.22  2012/05/22 15:28:31  cvs
 		*** empty log message ***

 		Revision 1.21  2012/05/08 15:27:04  cvs
 		nota controle

 		Revision 1.20  2012/04/25 09:58:18  cvs
 		*** empty log message ***

 		Revision 1.19  2012/02/08 13:58:56  cvs
 		*** empty log message ***

 		Revision 1.18  2011/06/28 09:17:37  cvs
 		fondscode aanpassingen

 		Revision 1.17  2011/04/18 14:59:37  cvs
 		*** empty log message ***




*/


$_transactiecodes = Array("K","V","OK","OV","SK","SV","EX C","EX P",
	                        "AS C","AS P","EMIS","RTDB","RTCR","AFL",
	                        "UITK","UITK + DIV","O-G1","O-G","D","L","O");

function _debetbedrag()
{
	global $data, $mr;
	if ( ($data[3] == "EUR" AND $data[9] <> "EUR") OR strstr($mr[Rekening],"MEM") )
	  return -1 * ($mr[Debet] * $mr[Valutakoers]);
  else
    return -1 * $mr[Debet];
}

function _creditbedrag()
{
	global $data, $mr;
	if ( ($data[3] == "EUR" AND $data[9] <> "EUR") OR strstr($mr[Rekening],"MEM") )
   return $mr[Credit] * $mr[Valutakoers];
  else
	 return $mr[Credit];
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
	if (strstr($data[4],"-")) // "-" detectie daarna datum anders opbouwen dan standaard 2015-11-16
	{
		$datumParts = explode("-",$data[4]);
		$mr[Boekdatum]         = $datumParts[2]."-".$datumParts[1]."-".$datumParts[0]." 00:00:00";
		$mr[settlementDatum]   = $datumParts[2]."-".$datumParts[1]."-".$datumParts[0]." 00:00:00";
	}
	else
	{
		$mr[Boekdatum]         = substr($datum,4,4)."-".substr($datum,2,2)."-".substr($datum,0,2)." 00:00:00";
		$mr[settlementDatum]   = substr($datum,4,4)."-".substr($datum,2,2)."-".substr($datum,0,2)." 00:00:00";
	}
	
  $tijd  = $data[5];
	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
  //$mr[Boekdatum]         = substr($datum,4,4)."-".substr($datum,2,2)."-".substr($datum,0,2)." ".$tijd;
  
  $mr[Rekening]          = Trim($data[1]).Trim($data[3]);
  $datum = $data[17];
  
  $mr[bankTransactieId]  = Trim($data[18]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_K()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Transactietype]    = "";

  if ($data[15] <> 0)
	{
		$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		if ($mr[Valuta] == "EUR")
		  $mr[Valutakoers] = 1;

		$mr[Debet]             = abs($data[15]);
		$mr[Bedrag]            = -1 * $mr[Debet] * $mr[Valutakoers];
    $controleBedrag  += $mr[Bedrag];
		$output[] = $mr;
	}

  if ($data[13] <> 0)
	{
    $mr[Grootboekrekening] = "KOST";
	  $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
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
    $controleBedrag  += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Debet]             = abs($data[14]);
      $mr[Valutakoers]       = 1;
    }
    else
      $mr[Debet]             = abs($data[14]*$data[8]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }



  if ($data[16] <> 0)
	{
    $mr[Grootboekrekening] = "KOBU";
	  $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  if ($mr[Valuta] == "EUR")
	  {
	    $mr[Debet]             = abs($data[16]);
	    $mr[Valutakoers]       = 1;
	  }
	  else
	    $mr[Debet]             = abs($data[16]*$data[8]);

	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  $mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Transactietype]    = "";
  if ($data[15] <> 0)
	{
		$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		if ($mr[Valuta] == "EUR")
		  $mr[Valutakoers] = 1;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[15]);
		$mr[Bedrag]            = abs($mr[Credit] * $mr[Valutakoers]);
    $controleBedrag       += $mr[Bedrag];
		$output[] = $mr;
	}

  if ($data[13] <> 0)
	{
	  $mr[Grootboekrekening] = "KOST";
	  $mr[Valuta]            = $data[9];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
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
    $controleBedrag  += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Debet]             = abs($data[14]);
      $mr[Valutakoers]       = 1;
    }
    else
      $mr[Debet]             = abs($data[14]*$data[8]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }



  if ($data[16] <> 0)
	{
    $mr[Grootboekrekening] = "KOBU";
	  $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  if ($mr[Valuta] == "EUR")
	  {
	    $mr[Debet]             = abs($data[16]);
	    $mr[Valutakoers]       = 1;
	  }
	  else
	    $mr[Debet]             = abs($data[16]*$data[8]);

	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OK()  //Aankoop openen bij opties en futures
{

  global $fonds, $data, $mr, $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;

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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  if ($data[13] <> 0)
	{
	  $mr[Grootboekrekening] = "KOST";
	  $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";

	  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
  	$mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  if ($mr[Valutakoers] == "EUR")
	    $mr[Debet]             = abs($data[13]);
	  else
	    $mr[Debet]             = abs($data[13]*$data[8]);
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Debet]             = abs($data[14]);
      $mr[Valutakoers]       = 1;
    }
    else
      $mr[Debet]             = abs($data[14]*$data[8]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OV()  //Verkoop openen bij opties en futures
{
  global $fonds, $data, $mr, $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	if ($data[13] <> 0)
	{
	  $mr[Grootboekrekening] = "KOST";
	  $mr[Valuta]            = $data[9];
	  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  $mr[Credit]            = 0;
	  if ($mr[Valutakoers] == "EUR")
  	  $mr[Debet]             = abs($data[13]);
	  else
	    $mr[Debet]             = abs($data[13]*$data[8]);
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];

	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Debet]             = abs($data[14]);
      $mr[Valutakoers]       = 1;
    }
    else
      $mr[Debet]             = abs($data[14]*$data[8]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SK()  //Aankoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;
  if ($data[13] <> 0)
	{
    $mr[Grootboekrekening] = "KOST";
	  $mr[Valuta]            = $data[9];
	  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  if ($mr[Valutakoers] == "EUR")
	    $mr[Debet]             = abs($data[13]);
	  else
	    $mr[Debet]             = abs($data[13]*$data[8]);
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];

	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Debet]             = abs($data[14]);
      $mr[Valutakoers]       = 1;
    }
    else
      $mr[Debet]             = abs($data[14]*$data[8]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SV()  //Verkoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "SV";
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  if ($data[13] <> 0)
	{
  	$mr[Grootboekrekening] = "KOST";
    $mr[Valuta]            = $data[9];
	  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  $mr[Credit]            = 0;
	  if ($mr[Valutakoers] == "EUR")
	    $mr[Debet]             = abs($data[13]);
	  else
	    $mr[Debet]             = abs($data[13]*$data[8]);
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Debet]             = abs($data[14]);
      $mr[Valutakoers]       = 1;
    }
    else
      $mr[Debet]             = abs($data[14]*$data[8]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EX_C1()  //Exercise Call optie
{
  return do_SV();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EX_P1()  //Exercise Call optie
{
  return do_SV();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_C1()  //Exercise Call optie
{
  return do_SK();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_P1()  //Exercise Call optie
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
		$mr[Debet]             = abs($data[12]);
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
		$mr[Credit]            = abs($data[12]);
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
  global $fonds, $data, $mr, $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "AFL";
	do_algemeen();
	$mr[Omschrijving]      = "Lossing ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[10];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;
  if ($data[13] > 0)
  {
	  $mr[Grootboekrekening] = "KOST";
	  $mr[Valuta]            = $data[9];
	  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  if ($mr[Valutakoers] == "EUR")
	    $mr[Debet]             = abs($data[13]);
	  else
	    $mr[Debet]             = abs($data[13]*$data[8]);

	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Debet]             = abs($data[14]);
      $mr[Valutakoers]       = 1;
    }
    else
      $mr[Debet]             = abs($data[14]*$data[8]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

	if( $data[15] <> 0)
	{
		$mr[Grootboekrekening] = "RENME";
		$mr[Valuta]            = $data[9];
		if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  	if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  	//$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[15]);
		$mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "";

		$output[] = $mr;

	}

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_UITK()  //Rente obligaties /contant dividend
{
  global $fonds, $data, $mr, $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;

	$_soort = trim($data[21]);
	$mr[aktie]              = "UITK + ".$_soort;
	do_algemeen();

	if ($_soort == "COUP")
	{
	  $mr[Omschrijving]      = "Rente ".$fonds[Omschrijving];
	  $mr[Grootboekrekening] = "RENOB";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs(($data[10] * $data[11]) * $fonds[Fondseenheid]);
		$mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
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
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs(($data[10] * $data[11]) * $fonds[Fondseenheid]);
		if ($data[9] == "PNC")
		  $mr[Credit] = $mr[Credit]/100;

		$mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		$output[] = $mr;

	}
  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "DIVBE";
	  $mr[Valuta]            = $data[9];
	  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
	  //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  if ($mr[Valuta] == "USD")
	    $mr[Debet]             = abs($data[14] * $data[8]);
	  else
		  $mr[Debet]             = abs($data[14]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $output[] = $mr;
  }

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
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
  $mr[Debet]             = abs($data[12]);
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
	$mr[Valuta]            = $data[3];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
	if ($mr[Valuta] <> "EUR")
	  $mr[Valutakoers]     = _valutakoers();
	else
	  $mr[Valutakoers]     = 1;

	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  $mr[Omschrijving]      = $data[25];
	if ($data[12] < 0)
	{
	  switch ($mr[Omschrijving])
	  {
	    case "Kosten afschriften":
	      $mr[Grootboekrekening] = "KNBA";
	      break;
	    case "naar uw EUR-rekening":
	    case "naar uw USD-rekening":
	      $mr[Grootboekrekening] = "KRUIS";
	      break;
	    default:
	      $mr[Grootboekrekening] = "ONTTR";

	  }
		$mr[Debet]             = ($data[12] * -1);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
	}
	else
	{
	  switch ($mr[Omschrijving])
	  {
	    case "naar uw EUR-rekening":
	    case "naar uw USD-rekening":
	      $mr[Grootboekrekening] = "KRUIS";
	      $mr[Omschrijving]      = $data[25];
	      break;
	    default:
	      $mr[Grootboekrekening] = "STORT";
		    $mr[Omschrijving]      = $data[26];

	  }

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
	global $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
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
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "D";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 1;

	$output[] = $mr;

	$mr[Grootboekrekening] = "STORT";
	$mr[Fonds]             = "";
	$mr[Valuta]            = "EUR";
	$mr[Valutakoers]       = 1;
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Bedrag]);
	$mr[Bedrag]            = $mr[Credit];
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";

  if ($mr[Bedrag] <> 0)
	 $output[] = $mr;

  if ($data[15] <> 0)
	{

		$mr[Omschrijving]      = "Meegekochte rente ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		$mr[Valuta]            = "EUR";
		$mr[Valutakoers]       = 1;
		$mr[Aantal]            = 0;
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = abs($data[15]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = -1 * $mr[Debet];
    $controleBedrag       += $mr[Bedrag];

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
    $controleBedrag       += $mr[Bedrag];

		$output[] = $mr;

	}

  if ( round($controleBedrag,2) <> round($data[12],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$data[12]." / controle = ".$controleBedrag;
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_L()  // Lichting van stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "L";
	do_algemeen();

	$mr[Rekening]          = trim($data[1])."MEM";
	$mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = ($data[10] * -1);
	$mr[Fondskoers]        = $data[11];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
  if ($data[9] == "PNC")
	  $mr[Credit]  = $mr[Credit]/100;
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";

	if ($mr[Bedrag] <> 0)
	  $output[] = $mr;

	if ($data[15] <> 0)
	{

		$mr[Omschrijving]      = "Meeverkochte rente ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENME";
		$mr[Valuta]            = "EUR";
		$mr[Valutakoers]       = 1;
		$mr[Aantal]            = 0;
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[15]);
		$mr[Bedrag]            = $mr[Credit];
    $controleBedrag       += $mr[Bedrag];

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
    $controleBedrag       += $mr[Bedrag];

		$output[] = $mr;

	}


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_O()  // toegevoeg 18 feb 2010
{
  global $data;
  echo "<BR>MELDING: transactietype O gevonden, graag aanpassen!";
  /*
  if ($data[10] > 0)
    do_D();
  else
    do_L();
  */
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