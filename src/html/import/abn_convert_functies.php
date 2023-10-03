<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
File Versie					: $Revision: 1.3 $

$Log: abn_convert_functies.php,v $
Revision 1.3  2018/09/23 17:14:23  cvs
call 7175

Revision 1.2  2018/08/11 09:00:02  rvv
*** empty log message ***

Revision 1.1  2011/07/16 09:52:45  cvs
*** empty log message ***

Revision 1.24  2011/06/28 09:17:37  cvs
fondscode aanpassingen

Revision 1.23  2008/10/01 07:48:06  cvs
nieuwe commit 1-10-2008

Revision 1.22  2006/11/13 11:17:47  cvs
*** empty log message ***

Revision 1.21  2006/08/18 08:26:18  cvs
extra functie do_d_nul en do_l_nul

Revision 1.20  2006/05/03 07:15:03  cvs
*** empty log message ***

Revision 1.19  2006/03/06 11:46:19  cvs
do_D: als bedrag = 0 toch eerste transactie verwerken

Revision 1.18  2006/01/03 07:43:56  cvs
todo 243




*/



function convertRecord($record)  //554 records
{
	global $data;
	$_data = explode(chr(10),$record[txt]);
	$wr = array();
	for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
	{
		$_r = explode("&&",$_data[$subLoop]);
		$_tempRec[$_r[0]] = $_r[1];
		switch ($_r[0])
		{
			case "23":
			   $wr[transactienr] = $_r[1];
			   $wr[boekdatum] = "20".substr($_r[1],0,2)."-".substr($_r[1],2,2)."-".substr($_r[1],4,2);
			   break;
			case "53a":
			   $wr[rekeningnr] = intval($_r[1]);
			   break;
			case "83a":
			   $wr[depotnr] = intval($_r[1]);
		     break;
			case "72":
			   if (Trim($_r[1]) <> "")  // niet overschrijven als leeg
			     $wr[transaktietype] = $_r[1];
			   break;
			case "35A":
			   for($xx=0;$xx < strlen($_r[1]);$xx++)
			   {
				    $_l = 	substr($_r[1],$xx,1);
				    if ($_l >= "0" AND $_l <= "9")
				      $wr[aantal] .= $_l;
				    elseif ($_l == ",")
				      $wr[aantal] .= ".";
			   }
			   break;
			case "35B":
			   $_val = explode(" ",$_r[1]);
			   $xx=1;
			   while($xx < count($_val))
			   {
				    if ($_val[$xx] <> "")
				    {
					     $wr[aabcode] = $_val[$xx];
					     break;
				    }
				    $xx++;
			   }
			   break;
			case "35U":
			   if (Trim($_r[1]) <> "")  // niet overschrijven als leeg
			   {
				    $wr[valutacode] = cnvBedrag(substr($_r[1],0,3));
				    $wr[fondskoers] = cnvBedrag(substr($_r[1],3));
		  	 }
			   break;
			case "36":
			   $wr[wisselkoers] = cnvBedrag($_r[1]);
			   break;
			case "34A":
			   if (Trim($_r[1]) <> "") // niet overschrijven als leeg
			   {

				    if (substr($_r[1],8,1) == "N")
				    {
					     $wr[valuta]    = substr($_r[1],9,3);
					     $wr[bedrag]    = cnvBedrag(substr($_r[1],11))*-1;
				    }
				    else
				    {
					     $wr[valuta]    = substr($_r[1],8,3);
					     $wr[bedrag]    = cnvBedrag(substr($_r[1],11));
				    }
			   }
			   break;
			case "32G":
			   $wr[kosten] = cnvBedrag($_r[1]);
			   break;
			case "71C":
			   $_l = trim($_r[1]);

			   $_bedr = substr($wr[kosten],4);
			   if (substr($wr[kosten],0,1) == "N")
			   {
			     $_bedr = substr($wr[kosten],4);
			     $wr["valuta_".$_l] = substr($wr[kosten],1,3);
			     $wr["kosten_".$_l] = $_bedr * -1;
			   }
			   else
			   {
			     $wr["valuta_".$_l] = substr($wr[kosten],0,3);
			     $_bedr = substr($wr[kosten],3);
			     $wr["kosten_".$_l] = $_bedr;
			   }
			   if ($wr["valuta_".$_l] <> "EUR")
			   {
			     $wr["kosten_".$_l] = $wr["kosten_".$_l] / $wr["wisselkoers"];   // EUR kosten terug naar eigen valuta
			   }
				 break;
		}

	}
	$DB = New DB;
	$query = "SELECT * FROM Valutas WHERE AABgrens > 0 AND Valuta = '".$wr[valutacode]."'";
	$DB->SQL($query);
	if ($aab = $DB->lookupRecord())
	{
		if ($wr[wisselkoers] > $aab[AABgrens])
		{
			$wr[wisselkoers] = $wr[wisselkoers]/$aab[AABcorrectie];
		}
	}
  debug($data);
	$data = $wr;

	return;

}

function getValutaKoers($valuta,$datum)
{
  global $DB;
  $query = "SELECT * FROM Valutakoersen WHERE Valuta = '".$valuta."' AND datum <= '".$datum."'";
  $DB->SQL($query);
	$valutaKoers = $DB->lookupRecord();
  return $valutaKoers['Koers'];

}

function _debetbedrag()
{
	global $mr;
	if ( stristr($mr[Rekening],$mr[Valuta]) )
		return -1 * $mr[Debet];
	else
		return -1 * ($mr[Debet] * $mr[Valutakoers]);
}

function _creditbedrag()
{
	global $mr;
	if ( stristr($mr[Rekening],$mr[Valuta]) )
		return $mr[Credit];
	else
		return $mr[Credit] * $mr[Valutakoers];
}

function do_algemeen()
{
	global $data,$fonds,$mr,$output,$DB;
	$mr = array();

	$mr[Boekdatum]      = $data[boekdatum];
	if ($data[aabcode] <> "")
	{
		$query = "SELECT * FROM Fondsen WHERE AABCode = '".$data[aabcode]."' ";
		$DB->SQL($query);
		if (!$fonds = $DB->lookupRecord())
		$fonds[Omschrijving] = "koers niet Fondslijst";
	}
	else
	$fonds[Omschrijving] = "Fout bij lezen AABcode";
	$mr[Rekening]       = $data[rekeningnr].$data[valuta];
	return;
}


function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}

function convertMt940($record)
{
  $data = array();
  $dnx = 0;
  $_data = explode(chr(10),$record[txt]);
  $wr = array();
  $subRecord = 0;
  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);
    $_tempRec[$_r[0]] = $_r[1];
    switch ($_r[0])
    {
      case "20":
        $wr[transactienr] = $_r[1];
        break;
      case "25":
        $wr[rekeningnr] = intval($_r[1]);
        break;
      case "28":
      case "28C":
        $wr[afshriftnr] = $_r[1];
        break;
      case "60F":
        $wr[a_60F] = $_r[1];
        $wr[valuta]     = substr($_r[1],7,3);
        break;
      case "61":
        if ($subRecord > 0)
        {
          $data[$dnx] = $wr;                                   // sla vorige subrecord op
          $dnx++;
          $wr = array();                                 // laadt defaults opnieuw
          $wr[transactienr]  = $data[$dnx-1][transactienr];
          $wr[rekeningnr]    = $data[$dnx-1][rekeningnr];
          $wr[afshriftnr]    = $data[$dnx-1][afshriftnr];
          $wr[valuta]        = $data[$dnx-1][valuta];
          $wr[a_60F]         = $data[$dnx-1][a_60F];
        }
        $subRecord = 1;
        $wr[debcre]     = substr($_r[1],10,1);
        //$wr[valutadatum] = "20".substr($_r[1],0,2)."-".substr($_r[1],2,2)."-".substr($_r[1],4,2);
        $_tmp = explode("N",substr($_r[1],11));
        $wr[bedrag]      = cnvBedrag($_tmp[0]);
        break;
      case "86":
        $wr[omschrijving] = str_replace(chr(13)," ",$_r[1]);

        break;
      case "62F":
        $wr[boekdatum] = "20".substr($_r[1],1,2)."-".substr($_r[1],3,2)."-".substr($_r[1],5,2);
        $wr[valuta]     = substr($_r[1],7,3);
        break;
    }
  }
  $data[$dnx] = $wr;
  for ($xx=0;$xx <= $dnx;$xx++)
  {
    $data[$xx][boekdatum] = $wr[boekdatum];
  }
  //listarray($data);
  return $data;  // geeft arrayset met deelrecords terug

}


function do_mt940($mt940,$mt940Tel)
{
  Global $data,$output,$mr;
  $_result = convertMt940($mt940);
  for ($x =0;$x < count($_result);$x++)
  {
    $d = $_result[$x];  //$d = shortcut voor te bewerken record
    $mr = array();
    $mr[aktie]             = $mt940Tel."/".$x;
    $mr[Rekening]          = $d[rekeningnr].$d[valuta];
    $mr[Boekdatum]         = $d[boekdatum];
    $mr[Valuta]            = $d[valuta];
    $mr[Valutakoers]       = 1;
    $mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Transactietype]    = "";
    if (substr($d,0,5) == "RENTE")
    {
      $mr[Grootboekrekening] = "RENTE";
      if ($d[debcre] == "C")
      {
        $mr[Omschrijving] = "Creditrente";
        $mr[Debet]        = 0;
        $mr[Credit]       = abs($d[bedrag]);
        $mr[Bedrag]       = $mr[Credit];
      }
      else
      {
        $mr[Omschrijving] = "Debetrente";
        $mr[Debet]        = abs($d[bedrag]);
        $mr[Credit]       = 0;
        $mr[Bedrag]       = _debetbedrag($mr[Debet]);
      }
    }
    elseif (substr($d,0,10) == "BEWAARLOON")
    {
      $mr[Grootboekrekening] 	= "BEW";
      $mr[Omschrijving]				= "Bewaarloon";
      $mr[Debet]        			= abs($d[bedrag]);
      $mr[Credit]       			= 0;
      $mr[Bedrag]       			= _debetbedrag($mr[Debet]);
    }
    else
    {
      $mr[Omschrijving] 			= $d[omschrijving];
      if ($d[debcre] == "C")
      {
        $mr[Grootboekrekening] 	= "STORT";
        $mr[Debet]        			=	0;
        $mr[Credit]       			= abs($d[bedrag]);
        $mr[Bedrag]       			= $mr[Credit];
      }
      else
      {
        $mr[Grootboekrekening] 	= "ONTTR";
        $mr[Debet]			        = abs($d[bedrag]);
        $mr[Credit]       			= 0;
        $mr[Bedrag]       			= _debetbedrag($mr[Debet]);
      }
    }
    addAndCheck("940");

  }


}


function addAndCheck($soort="554")
{
	global $mr,$error, $output,$DB,$data;

	// check bestaat rekeningnummer
	//
	$fout = false;
	$_rekNr = $mr[Rekening];


  if (!$fout)
  {
  	if ($soort == "554")
  	{
  	  if (isNumeric($_rekNr) )
	    {
        $error[] = "<font color=maroon>$_rekNr :$soort Rekeningnummer niet compleet omdat fonds onbekend is AAB=".$data[aabcode]." :: ".$mr[Omschrijving];
	  	  $fout = true;
	    }

  		// check bestaat portefeuille voor dit rekeningnummer
  		//
  		$_code = intval($data[depotnr]);
  		$query = "SELECT * FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$_code."' ";
  		$DB->SQL($query);
  		if (!$_bla = $DB->lookupRecord())
  		{
  			$error[] = "$_rekNr :Portefeuille komt niet voor ($_code) ";
  			$fout = true;
  		}

  		// check bestaat rekeningnummer
  		//
  		$query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
  		$DB->SQL($query);
  		if (!$_bla = $DB->lookupRecord())
  		{
  			$error[] = "<font color=navy>$_rekNr :Rekeningnummer komt niet voor </font> ";
  			$fout = true;
  		}
  		//
			// check of AAB code voorkomt in fondsen tabel
			//
			$query = "SELECT * FROM Fondsen WHERE AABCode = '".$data[aabcode]."' ";
			$DB->SQL($query);
			if (!$fonds = $DB->lookupRecord())
			{
				$error[] = "$_rekNr :AAB code komt niet voor fonds tabel ($data[aabcode])";
				$fout = true;
			}
  	}
  	else // bij MT940 zelf portefeuille opzoeken
  	{
  		//echo "---in 940----";
  		$query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
	    $DB->SQL($query);
	    if (!$rekening = $DB->lookupRecord())
	    {
  		  $error[] = "$_rekNr :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr) AAB=".$data[aabcode]." :: ".$mr[Omschrijving];
	  	  $fout = true;
	    }
  		$_code = $rekening[Portefeuille];
			$query = "SELECT * FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$_code."' ";
			$DB->SQL($query);
			if (!$_bla = $DB->lookupRecord())
			{
				$error[] = "$_rekNr :Portefeuille komt niet voor ($_code) ";
				$fout = true;
			}
  	}
	}



	if (!$fout)
	{
		$output[] = $mr;
		return true;
	}
	else
		return false;

	}


	/////////////////////////////////////////////////////////////////////////////////
	//
	/////////////////////////////////////////////////////////////////////////////////

	function do_A($record)  // aankoop van stukken
	{
		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "A";
		do_algemeen();
		$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = $data[aantal];
		$mr[Fondskoers]        = $data[fondskoers];
		$mr[Debet]             = abs($data[aantal] * $data[fondskoers] * $fonds[Fondseenheid]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "A";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		if (addAndCheck() )
		{
			if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;

			//$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Debet]             = 0;
  		$mr[Credit]            = 0;
			$mr[Transactietype]    = "";
			$mr[Grootboekrekening] = "KOST";

			if ($data[kosten_16] )
			{
				$mr[Debet]             = abs($data[kosten_16]);
				$mr[Valuta]            = $data[valuta_16];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_17] )
			{
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_15])
			{
				$mr[Grootboekrekening] = "RENME";
				$mr[Debet]             = abs($data[kosten_15]);
				$mr[Valuta]            = $data[valuta_15];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
		}

	}


	function do_V($record)  // verkoop van stukken
	{
		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "V";
		do_algemeen();
		$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = -1 * $data[aantal];
		$mr[Fondskoers]        = $data[fondskoers];
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[aantal] * $data[fondskoers] * $fonds[Fondseenheid]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "V";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		if (addAndCheck())
		{
			if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;


			//$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Credit]            = 0;
			$mr[Debet]             = 0;
			$mr[Transactietype]    = "";
			$mr[Grootboekrekening] = "KOST";

			if ($data[kosten_16] )
			{
				$mr[Debet]             = abs($data[kosten_16]);
				$mr[Valuta]            = $data[valuta_16];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_17] )
			{
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_15])
			{
				$mr[Grootboekrekening] = "RENOB";
				$mr[Credit]            = abs($data[kosten_15]);
				$mr[Debet]             = 0;
				$mr[Valuta]            = $data[valuta_15];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _creditbedrag($mr[Credit]);
				addAndCheck();
			}
		}

	}

	function do_AO($record)  // aankoop van opties
	{
		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "AO";
		do_algemeen();
		$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = $data[aantal];
		$mr[Fondskoers]        = $data[fondskoers];
		$mr[Debet]             = abs($data[aantal] * $data[fondskoers] * $fonds[Fondseenheid]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "A/O";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		if (addAndCheck())
		{
			if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;

			$mr[Credit]            = 0;
			//$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Bedrag]            = $mr[Debet];
			$mr[Transactietype]    = "";
			$mr[Grootboekrekening] = "KOST";

			if ($data[kosten_16] )
			{
				$mr[Debet]             = abs($data[kosten_16]);
				$mr[Valuta]            = $data[valuta_16];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();

			}

			if ($data[kosten_17] )
			{
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
		}
	}

	function do_VO($record)  // verkoop van opties
	{
		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "VO";
		do_algemeen();
		$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = -1 * $data[aantal];
		$mr[Fondskoers]        = $data[fondskoers];
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[aantal] * $data[fondskoers] * $fonds[Fondseenheid]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "V/S";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		if(addAndCheck())
		{
			$mr[Debet]      = 0;
			$mr[Credit]			= 0;
			if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;

			//$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;
			$mr[Bedrag]            = $mr[Debet];
			$mr[Transactietype]    = "";
			$mr[Grootboekrekening] = "KOST";

			if ($data[kosten_16] )
			{
				$mr[Debet]             = abs($data[kosten_16]);
				$mr[Valuta]            = $data[valuta_16];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_17] )
			{
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
        if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_15])
			{
				$mr[Grootboekrekening] = "RENOB";
				$mr[Debet]             = abs($data[kosten_15]);
				$mr[Valuta]            = $data[valuta_15];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
		}
	}

	function do_CD($record)  // contant dividend
	{
		global $data,$fonds,$mr,$output;
		do_algemeen();
		$mr[aktie]              = "CD";
		$mr[Omschrijving]      = "Dividend ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "DIV";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[aantal] * $data[fondskoers]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		if (addAndCheck())
		{
			if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;

			$mr[Credit]            = 0;
			//$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Fondskoers]        = 0;

			$mr[Transactietype]    = "";
			if ($data[kosten_17])
			{
				$mr[Grootboekrekening] = "DIVBE";
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			$mr[Grootboekrekening] = "KNBA";
			if ($data[kosten_16] )
			{
				$mr[Debet]             = abs($data[kosten_16]);
				$mr[Valuta]            = $data[valuta_16];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
				addAndCheck();
			}

		}
	}

	function do_CR($record) // Coupon Rente
	{
		global $data,$fonds,$mr,$output;
		do_algemeen();
		$mr[aktie]              = "CR";
		$mr[Omschrijving]      = "Rente ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "RENOB";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs(($data[aantal] * $data[fondskoers])/100);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		if (addAndCheck())
		{
			if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;

			//$mr[Fonds]             = "";
			$mr[Aantal]            = 0;
			$mr[Credit] 					 = 0;
			$mr[Fondskoers]        = 0;
			$mr[Transactietype]    = "";
			$mr[Grootboekrekening] = "KOST";

			if ($data[kosten_16] )
			{
			  $mr[Grootboekrekening] = "KNBA";
				$mr[Debet]             = abs($data[kosten_16]);
				$mr[Valuta]            = $data[valuta_16];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag();
				addAndCheck();
				$mr[Grootboekrekening] = "KOST"; // terugzetten naar default;
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag();
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag();
				addAndCheck();
			}
		}
	}
	/////////////////////////////////////////////////////////////////////////////////
	//
	/////////////////////////////////////////////////////////////////////////////////
	function do_D($record)  // Deponering van stukken
	{

		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "D";
		do_algemeen();
		$mr[Rekening]          = $data[depotnr]."MEM";
		$mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
//		$mr[Valutakoers]       = $data[wisselkoers];
    $mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = $data[aantal];
		$mr[Fondskoers]        = $data[fondskoers];
		$mr[Debet]             = abs($data[aantal] * $data[fondskoers] * $fonds[Fondseenheid]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "D";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;
		addAndCheck();
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
      addAndCheck();
    }

	}
/////////////////////////////////////////////////////////////////////////////////
	//
	/////////////////////////////////////////////////////////////////////////////////
	function do_D_nul($record)  // Deponering van stukken van met nul waarde
	{

		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "D";
		do_algemeen();
		$mr[Rekening]          = $data[depotnr]."MEM";
		$mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
//		$mr[Valutakoers]       = $data[wisselkoers];
    $mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = $data[aantal];
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "D";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;
		addAndCheck();
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
      addAndCheck();
    }

	}

	/////////////////////////////////////////////////////////////////////////////////
	//
	/////////////////////////////////////////////////////////////////////////////////
	function do_L($record)  // Lichting van stukken
	{
		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "L";
		do_algemeen();
		$mr[Rekening]          = $data[depotnr]."MEM";
		$mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
//		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = -1 * $data[aantal];
		$mr[Fondskoers]        = $data[fondskoers];
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[aantal] * $data[fondskoers] * $fonds[Fondseenheid]);
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "L";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;
		addAndCheck();
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
        $mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $mr[Transactietype]    = "";
        addAndCheck();
    }
	}
  /////////////////////////////////////////////////////////////////////////////////
	//
	/////////////////////////////////////////////////////////////////////////////////
	function do_L_nul($record)  // Lichting van stukken met waarde 0
	{
		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "L";
		do_algemeen();
		$mr[Rekening]          = $data[depotnr]."MEM";
		$mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
//		$mr[Valutakoers]       = $data[wisselkoers];
		$mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = -1 * $data[aantal];
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _creditbedrag();
		$mr[Transactietype]    = "L";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;
		addAndCheck();
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
        $mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $mr[Transactietype]    = "";
        addAndCheck();
    }
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