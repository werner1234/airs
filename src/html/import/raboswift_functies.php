<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
File Versie					: $Revision: 1.3 $

$Log: raboswift_functies.php,v $
Revision 1.3  2018/08/11 09:00:02  rvv
*** empty log message ***

Revision 1.2  2013/12/16 08:21:00  cvs
*** empty log message ***

Revision 1.1  2011/04/27 14:55:23  cvs
*** empty log message ***


*/

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
  //listarray($_result);
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


function addAndCheck($soort="940")
{
	global $mr,$error, $output,$DB,$data;

	// check bestaat rekeningnummer
	//
	$fout = false;
	$_rekNr = $mr[Rekening];
	//listarray($mr);
  if (substr($mr[Omschrijving],0,8) == "Eff.nota") $fout=true;
  //echo "[".substr($mr[Omschrijving],0,8)."]".$mr[Omschrijving]."<hr>";
  if (!$fout)
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

	function do_error()
	{
		global $do_func;
		echo "<BR>FOUT functie $do_func bestaat niet!";
	}


?>