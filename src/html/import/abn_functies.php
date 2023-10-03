<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2020/03/27 07:50:08 $
File Versie					: $Revision: 1.53 $

$Log: abn_functies.php,v $
Revision 1.53  2020/03/27 07:50:08  cvs
call 8509

Revision 1.52  2019/05/20 09:35:35  cvs
call 7816

Revision 1.51  2019/02/27 15:15:31  cvs
call 7352

Revision 1.50  2018/11/30 14:33:58  cvs
call 6313

Revision 1.49  2018/08/31 14:25:39  cvs
call 6550

Revision 1.48  2018/08/11 09:00:02  rvv
*** empty log message ***

Revision 1.47  2018/04/30 13:56:39  cvs
call 6865

Revision 1.46  2018/04/06 11:48:45  cvs
trim(omschrijving)

Revision 1.45  2018/04/06 07:05:59  cvs
caal 6313

Revision 1.44  2018/03/21 15:10:46  cvs
call 6313

Revision 1.43  2018/01/18 15:51:19  cvs
call 6511

Revision 1.42  2017/09/27 12:23:28  cvs
call 6213

Revision 1.41  2017/09/19 10:47:53  cvs
call 6115

Revision 1.40  2016/07/18 12:22:55  cvs
update 20160718

Revision 1.39  2015/12/01 09:01:53  cvs
update 2540, call 4352

Revision 1.38  2015/05/21 12:09:57  cvs
*** empty log message ***

Revision 1.37  2015/05/11 13:36:52  cvs
*** empty log message ***

Revision 1.36  2015/05/08 12:08:58  cvs
*** empty log message ***

Revision 1.35  2014/12/16 07:30:30  cvs
*** empty log message ***

Revision 1.34  2014/10/13 11:40:54  cvs
call 3117

Revision 1.33  2014/10/01 13:36:08  cvs
dbs 2901

Revision 1.32  2014/07/31 12:41:05  cvs
omschrijvingen inkorten sepa/accept/ideal

Revision 1.31  2014/07/10 06:53:14  cvs
*** empty log message ***

Revision 1.30  2014/04/02 13:54:51  cvs
*** empty log message ***

Revision 1.29  2014/03/12 10:02:40  cvs
*** empty log message ***

Revision 1.28  2013/12/16 08:21:00  cvs
*** empty log message ***

Revision 1.27  2013/07/03 06:39:19  cvs
*** empty log message ***

Revision 1.26  2012/11/07 10:38:16  cvs
*** empty log message ***

Revision 1.25  2012/05/15 15:02:19  cvs
controlebedrag

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

function validate554RekeningNrs($dataSet)
{
  global $VB;
  $db = new DB();
  for ($_ndx = 0; $_ndx < count($dataSet);$_ndx++)
  {
    $rec = convertRecord($dataSet[$_ndx]);
    
    $rekeningNr = trim($rec["rekeningnr"]);
    $valuta = trim($rec["valuta"]);
    if ($rekeningNr == 0) continue;
    $query = "
      SELECT 
        Rekeningen.id,
        Portefeuilles.Vermogensbeheerder 
      FROM 
        Rekeningen 
      INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 
      WHERE 
        Rekeningen.Rekening = '".$rekeningNr.$valuta."' AND Rekeningen.consolidatie=0 ";
    
    if (!$rec = $db->lookupRecordByQuery($query))
    {
    
      addToRekeningAdd($rekeningNr,$valuta);
    }        
    else
    {
      $VB =  $rec["Vermogensbeheerder"];
    }
  }
}

function validate940RekeningNrs($dataSet)
{
  global $VB;
  $db = new DB();
  for ($_ndx = 0; $_ndx < count($dataSet);$_ndx++)
  {
    $rec = convertMt940($dataSet[$_ndx]);
    $rekeningNr = trim($rec[0]["rekeningnr"]);
    $valuta = trim($rec[0]["valuta"]);
    if ($rekeningNr == 0) continue;
    $query = "
      SELECT 
        Rekeningen.id,
        Portefeuilles.Vermogensbeheerder 
      FROM 
        Rekeningen 
      INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 
      WHERE 
        Rekeningen.Rekening = '".$rekeningNr.$valuta."' AND Rekeningen.consolidatie=0 ";

    if (!$rec = $db->lookupRecordByQuery($query))
    {
      addToRekeningAdd($rekeningNr,$valuta);
    }        
    else
    {
      $VB =  $rec["Vermogensbeheerder"];
    }

  }
  
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "AAB|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function convertRecord($record)  //554 records
{
	global $data;

	$_data = explode(chr(10),$record[txt]);
	$wr = array();
	$wr["functie"] = (String) $record["functie"];
	$wr["bankTransactieCode"] = (String) $record["bankTransactieCode"];
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
            $wr[settlementDatum] = substr($_r[1],0,4)."-".substr($_r[1],4,2)."-".substr($_r[1],6,2);
            
				    if (substr($_r[1],8,1) == "N") 
				    {
					     $wr[valuta]    = substr($_r[1],9,3);
					     $wr[bedrag]    = cnvBedrag(substr($_r[1],12))*-1;
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
			   if ($wr["valuta_".$_l] <> "EUR" )
			   {
			     if ($wr["valuta_".$_l] == $wr["valuta"])
           {
             $wr["kosten_".$_l] = $wr["kosten_".$_l] ;   // call 8994 kosten in valuta
           }
           else
           {
             $wr["kosten_".$_l] = $wr["kosten_".$_l] / $wr["wisselkoers"];   // EUR kosten terug naar eigen valuta
           }


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

	$data = $wr;

	return $wr;

}

function getValutaKoers($valuta,$datum)
{
  global $DB;
  $query = "SELECT * FROM Valutakoersen WHERE Valuta = '".$valuta."' AND datum <= '".$datum."' ORDER BY datum DESC";
	$valutaKoers = $DB->lookupRecordByQuery($query);
  return $valutaKoers['Koers'];

}

function _debetbedrag()
{
	global $mr;
	if ( stristr($mr["Rekening"],$mr["Valuta"]) )
		return -1 * $mr["Debet"];
	else
		return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $mr;
	if ( stristr($mr["Rekening"],$mr["Valuta"]) )
		return $mr["Credit"];
	else
		return $mr["Credit"] * $mr["Valutakoers"];
}

function checkControleBedrag($controleBedrag)
{
  global $meldArray, $mr, $data;
  $data["bedrag"] = trim($data["bedrag"]);
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($data["bedrag"],2);
  
  if ( round($controleBedrag,2) <> round($data["bedrag"],2) )
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}


function do_algemeen()
{
	global $data,$fonds,$mr,$output,$DB;
	$mr = array();

	$mr[Boekdatum]       = $data[boekdatum];
  $mr["bankTransactieCode"] = $data["bankTransactieCode"];
  $mr[settlementDatum] = $data[settlementDatum];
	if ($data[aabcode] <> "")
	{
		$query = "SELECT * FROM Fondsen WHERE AABCode = '".$data[aabcode]."' OR ABRCode = '".$data[aabcode]."' ";
		$DB->SQL($query);
		if (!$fonds = $DB->lookupRecord())
    {
      $fonds[Omschrijving] = "koers niet Fondslijst";
    }

	}
	else
  	$fonds[Omschrijving] = "Fout bij lezen AABcode";

  $mr[Rekening]       = $data[rekeningnr].$data[valuta];
  $mr[bankTransactieId]  = $data[transactienr];
	return;
}


function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}

function convertMt940($record)
{
  global $__appvar;
  $data = array();
  $dnx = 0;
  //listarray($record[txt]);
  $_data = explode(chr(10),$record[txt]);
  $wr = array();
  $subRecord = 0;
  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);
    $_tempRec[$_r[0]] = $_r[1];
    //listarray($_tempRec);
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
        $wr[settlementDatum] = "20".substr($_r[1],0,2)."-".substr($_r[1],2,2)."-".substr($_r[1],4,2);
        $_tmp = explode("N",substr($_r[1],11));
        $wr[bedrag]      = cnvBedrag($_tmp[0]);

        break;
      case "86":

        $wr["omschrijving"] = $_r[1];
        $wr["bankOmschrijving"] = $_r[1];
        $omschr = $_r[1];
//        debug($_r[1]);
        if (strstr($omschr, "BEA   NR" ))
        {
          $txt = explode(",PAS", $omschr);
          $txt = explode("/", $txt[0]);
          $txt = substr($txt[1],6);
          $omschr = "BEA: ".$txt;
        }

        if (stristr($omschr, "/EREF/"))
        {
          $exp = explode("/EREF/",$omschr);
          $omschr = $exp[0];
        }

        if ($capitalize )
        {
          $omschr = strtoupper(substr($omschr,0,1)).strtolower(substr($omschr,1));
        }

        // navraag rubr (buitenlandse overboeking) call 6313
        if (stristr($omschr,"navraagrubr") )
        {
          if (substr($omschr,0,6) != "KOSTEN")
          {
            $s = explode (",", $omschr);
            $s2 = explode(" NAVRAAGRUBR",trim(substr($s[1],2)));
            $omschr = trim(substr($s2[0],strpos($s2[0]," ")));
          }
          else
          {
            $s = explode("NAVRAAGRUBR",$omschr);
            $omschr = $s[0];
          }
        }

        $wr["omschrijving"] = $omschr;

        if (stristr($omschr,"/TRTP/SEPA")  OR
            stristr($omschr,"/TRTP/ACCEPTGIROBETALING") OR
            stristr($omschr,"/TRTP/IDEAL") )
        {
          $parts = explode("/NAME/",$omschr);
          $wr["omschrijving"] = $parts[1];
        }
        if ($__appvar["bedrijf"] != "RCN")
        {
          $wr["omschrijving"] = ucwords(strtolower($wr["omschrijving"]));
        }

        break;
      case "62F":
        $wr[boekdatum] = "20".substr($_r[1],1,2)."-".substr($_r[1],3,2)."-".substr($_r[1],5,2);
        $wr[valuta]     = substr($_r[1],7,3);
        break;
    }
  }

  if (substr(strtoupper($wr["omschrijving"]),0,27) == "KOOP/VERKOOP VREEMDE VALUTA" )
  {
    $oms = explode("\r", $wr["omschrijving"]);
    $kRaw = $oms[4];
    while (strstr($kRaw, "  "))
    {
      $kRaw = str_replace("  "," ", $kRaw);
    }
    $koers = explode(" ",$kRaw);
    $wr["omsKoers"] = round(1/str_replace(",",".",$koers[2]),8);
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
  Global $data,$output,$mr, $zv, $afw;
  $_result = convertMt940($mt940);
  for ($x =0;$x < count($_result);$x++)
  {
    
    $d = $_result[$x];  //$d = shortcut voor te bewerken record
    //debug($d);
    
    $omschrijving = str_replace("\r"," ",$d["omschrijving"]);

//  call 5486  (ook global $zv toevoegen)

    $omschrijvingOrg = $omschrijving;

    $omschrijving = $zv->reWrite($omschrijving, $d["rekeningnr"].$d["valuta"]);

    //$omschrijving = str_replace(" ","-",$omschrijving);
    
      //      debug($omschrijving);
            
    $mr = array();
    $mr[OmschrijvingOrg]   = $omschrijvingOrg;
    $mr[bankOmschrijving]   = str_replace(array("\r", "\n"), ' ', $d["bankOmschrijving"]);
    $mr[aktie]             = $mt940Tel."/".$x;
    $mr[Rekening]          = $d[rekeningnr].$d[valuta];
    $mr[Boekdatum]         = $d[boekdatum];
    $mr[settlementDatum]   = $d[settlementDatum];
    $mr[bankTransactieId]  = str_replace("/","",$d[transactienr]);
    $mr[Valuta]            = $d[valuta];

    $mr[Valutakoers]       = getValutaKoers($d["valuta"],$d["boekdatum"]);
    $mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Transactietype]    = "";
    $mr[regelnr]           = -99;  //  indicatie MT940 voor coloring in list
    //$mr[Omschrijving]      = $d["omschrijving"];
    $mr[Omschrijving]      = trim($omschrijving);

    if (substr($d["omschrijving"],0,5) == "RENTE" OR substr($d["omschrijving"],0,16) == "AFSLUITING RENTE")
    {
      $mr[Grootboekrekening] = "RENTE";
      if ($d[debcre] == "C")
      {
        //$mr[Omschrijving] = "Creditrente";
        $mr[Debet]        = 0;
        $mr[Credit]       = abs($d[bedrag]);
        $mr[Bedrag]       = $mr[Credit];
      }
      else
      {
       // $mr[Omschrijving] = "Debetrente";
        $mr[Debet]        = abs($d[bedrag]);
        $mr[Credit]       = 0;
        $mr[Bedrag]       = _debetbedrag($mr[Debet]);
      }
    }
    elseif (substr($omschrijving,0,10) == "BEWAARLOON" OR 
            substr($omschrijving,0,25) == "ABNAMRO BELEGGEN  SERVICE")
    {
      
      $mr[Grootboekrekening] 	= "BEW";
      //$mr[Omschrijving]				= "Bewaarloon";
      $mr[Debet]        			= abs($d[bedrag]);
      $mr[Credit]       			= 0;
      $mr[Bedrag]       			= _debetbedrag($mr[Debet]);
      $mr = $afw->reWrite("GLDBEW",$mr);
    }
    elseif ($d["omsKoers"] <> "" AND $mr[Valuta] <> "EUR")  // in koop/verkoop opgeslagen koers gebruiken call 5347
    {
      $mr[Valutakoers]        = $d["omsKoers"];
      $mr[Omschrijving] 			= trim($omschrijving);

      if ($d[debcre] == "C")
      {
        $mr[Grootboekrekening] 	= "STORT";
        $mr[Debet]        			=	0;
        $mr[Credit]       			= abs($d[bedrag]);
        $mr[Bedrag]       			= $mr[Credit];
        $mr = $afw->reWrite("GLDSTORT",$mr);
      }
      else
      {
        $mr[Grootboekrekening] 	= "ONTTR";
        $mr[Debet]			        = abs($d[bedrag]);
        $mr[Credit]       			= 0;
        $mr[Bedrag]       			= _debetbedrag($mr[Debet]);
        $mr = $afw->reWrite("GLDONTTR",$mr);
      }
    }
    else
    {
      $mr[Omschrijving] 			= trim($omschrijving);
      if ($d[debcre] == "C")
      {
        $mr[Grootboekrekening] 	= "STORT";
        $mr[Debet]        			=	0;
        $mr[Credit]       			= abs($d[bedrag]);
        $mr[Bedrag]       			= $mr[Credit];
        $mr = $afw->reWrite("GLDSTORT",$mr);
      }
      else
      {
        $mr[Grootboekrekening] 	= "ONTTR";
        $mr[Debet]			        = abs($d[bedrag]);
        $mr[Credit]       			= 0;
        $mr[Bedrag]       			= _debetbedrag($mr[Debet]);
        $mr = $afw->reWrite("GLDONTTR",$mr);
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

//debug($mr);

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
  		//*
      /*
      uigeschakeld per 5-12-2012 reden = RRP
  		$_code = intval($data[depotnr]);
  		$query = "SELECT * FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$_code."' ";
  		$DB->SQL($query);
  		if (!$_bla = $DB->lookupRecord())
  		{
  			$error[] = "$_rekNr :Portefeuille komt niet voor ($_code) ";
  			$fout = true;
  		}
      */
  		// check bestaat rekeningnummer
  		//
  		$query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
  		$DB->SQL($query);
  		if (!$_bla = $DB->lookupRecord())
  		{
  			$error[] = "<font color=navy>(5xx) $_rekNr :Rekeningnummer komt niet voor (transId: {$mr["bankTransactieId"]} / transCode: {$mr["bankTransactieCode"]}) </font> ";
  			$fout = true;
  		}
  		//
			// check of AAB code voorkomt in fondsen tabel
			//
			$query = "SELECT * FROM Fondsen WHERE AABCode = '".$data[aabcode]."' OR ABRCode = '".$data[aabcode]."'";
			$DB->SQL($query);
			if (!$fonds = $DB->lookupRecord())
			{
				$error[] = "$_rekNr :AAB/ABR code komt niet voor fonds tabel ($data[aabcode])";
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
  		  $error[] = "(940) $_rekNr :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr) AAB=".$data[aabcode]." :: ".$mr[Omschrijving];
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
		global $data,$fonds,$mr,$output,$afw;
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
    $controleBedrag       += $mr[Bedrag];
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
      $mr = $afw->reWrite("KOST",$mr);
			if ($data[kosten_16] )
			{
				$mr[Debet]             = abs($data[kosten_16]);
				$mr[Valuta]            = $data[valuta_16];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();
			}
			if ($data[kosten_17] )
			{
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
        $mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_17",$mr);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
        $mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();
			}
			if ($data[kosten_15])
			{
				$mr[Grootboekrekening] = "RENME";
        $mr = $afw->reWrite("RENME",$mr);
				$mr[Debet]             = abs($data[kosten_15]);
				$mr[Valuta]            = $data[valuta_15];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();
			}
		}
    $valutaCorrectie = ($data[valuta] <> "EUR")?$mr[Valutakoers]:1;
		if ($data["valuta"] == $mr["Valuta"])
    {
      $valutaCorrectie = 1;
    }

    checkControleBedrag($controleBedrag * $valutaCorrectie);



	}


	function do_V($record)  // verkoop van stukken
	{
		global $data,$fonds,$mr,$output,$afw;


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
    $controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "V";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;


		if (addAndCheck())
		{
			if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;


			//$mr[Fonds]             = "";
			$mr["Aantal"]            = 0;
			$mr["Fondskoers"]        = 0;
			$mr["Credit"]            = 0;
			$mr["Debet"]             = 0;
			$mr["Transactietype"]    = "";
			$mr["Grootboekrekening"] = "KOST";
      $mr = $afw->reWrite("KOST",$mr);
			if ($data["kosten_16"] )
			{
				$mr["Debet"]             = abs($data["kosten_16"]);
				$mr["Valuta"]            = $data["valuta_16"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
				addAndCheck();
			}
			if ($data["kosten_17"] )
			{
				$mr["Debet"]             = abs($data["kosten_17"]);
				$mr["Valuta"]            = $data["valuta_17"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_17",$mr);
				addAndCheck();
			}
			if ($data["kosten_18"] )
			{
				$mr["Debet"]             = abs($data["kosten_18"]);
				$mr["Valuta"]            = $data["valuta_18"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
				addAndCheck();
			}
			if ($data["kosten_19"] )
			{
				$mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
				$mr["Debet"]             = abs($data["kosten_19"]);
				$mr["Valuta"]            = $data["valuta_19"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
				addAndCheck();
			}
			if ($data[kosten_15])
			{
				$mr[Grootboekrekening] = "RENOB";
        $mr = $afw->reWrite("RENOB",$mr);
				$mr[Credit]            = abs($data[kosten_15]);
				$mr[Debet]             = 0;
				$mr[Valuta]            = $data[valuta_15];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _creditbedrag($mr[Credit]);
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();
			}
		}
    $valutaCorrectie = ($data[valuta] <> "EUR")?$mr[Valutakoers]:1;
    if ($data["valuta"] == $mr["Valuta"])
    {
      $valutaCorrectie = 1;
    }

    checkControleBedrag($controleBedrag * $valutaCorrectie);
	}

	function do_AO($record)  // aankoop van opties
	{
		global $data,$fonds,$mr,$output, $afw;
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
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "A/O";
    if ($fonds["fondssoort"] == "OPT")
    {
      $pos = getPositionByFonds($mr);
      if ($pos < 0)
      {
        $mr["Transactietype"] = "A/S";
      }
      else
      {
        $mr["Transactietype"] = "A/O";
      }
    }



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
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();

			}

			if ($data[kosten_17] )
			{
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
        $mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_17",$mr);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
        $mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
        $mr = $afw->reWrite("KOSTEN_19",$mr);
				addAndCheck();
			}
		}
    $valutaCorrectie = ($data[valuta] <> "EUR")?$mr[Valutakoers]:1;
    if ($data["valuta"] == $mr["Valuta"])
    {
      $valutaCorrectie = 1;
    }
    checkControleBedrag($controleBedrag * $valutaCorrectie);
	}

	function do_VO($record)  // verkoop van opties
	{
		global $data,$fonds,$mr,$output, $afw;
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
    $controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "V/S";

    if ($fonds["fondssoort"] == "OPT")
    {
      $pos = getPositionByFonds($mr);
      if ($pos > 0)
      {
        $mr["Transactietype"] = "V/S";
      }
      else
      {
        $mr["Transactietype"] = "V/O";
      }
    }


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
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();
			}
			if ($data[kosten_17] )
			{
				$mr[Debet]             = abs($data[kosten_17]);
				$mr[Valuta]            = $data[valuta_17];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
        $mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_17",$mr);
				addAndCheck();
			}
			if ($data[kosten_18] )
			{
				$mr[Debet]             = abs($data[kosten_18]);
				$mr[Valuta]            = $data[valuta_18];
        if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
        $mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
				addAndCheck();
			}
			if ($data[kosten_19] )
			{
				$mr[Grootboekrekening] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
				$mr[Debet]             = abs($data[kosten_19]);
				$mr[Valuta]            = $data[valuta_19];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();
			}
			if ($data[kosten_15])
			{
				$mr[Grootboekrekening] = "RENOB";
				$mr[Debet]             = abs($data[kosten_15]);
				$mr[Valuta]            = $data[valuta_15];
				if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
				$mr[Bedrag]            = _debetbedrag($mr[Debet]);
        $controleBedrag       += $mr[Bedrag];
				addAndCheck();
			}
		}
    $valutaCorrectie = ($data[valuta] <> "EUR")?$mr[Valutakoers]:1;
    if ($data["valuta"] == $mr["Valuta"])
    {
      $valutaCorrectie = 1;
    }
    checkControleBedrag($controleBedrag * $valutaCorrectie);  
	}

	function do_CD($record)  // contant dividend
	{
		global $data,$fonds,$mr,$output, $afw;
//    debug($data);
		do_algemeen();
		$mr["aktie"]              = "CD";
		$mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "DIV";
		$mr["Valuta"]            = $fonds["Valuta"];
		$mr["Valutakoers"]       = $data["wisselkoers"];
    if ($mr["Valutakoers"] <> 1 AND $mr["Valuta"] == "EUR")
      $mr["Valuta"] = "???";
		$mr["Fonds"]             = $fonds["Fonds"];
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data["aantal"] * $data["fondskoers"]);
		$mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("DIV",$mr);
		if (addAndCheck())
		{
			if ($mr["Valuta"] == "EUR")
      {
        $mr["Valutakoers"]  = 1;
      }

			$mr["Credit"]            = 0;
			//$mr[Fonds]             = "";
			$mr["Aantal"]            = 0;
			$mr["Fondskoers"]        = 0;

			$mr["Transactietype"]    = "";

      if ($data["kosten_17"])
			{

				$mr["Grootboekrekening"] = "DIVBE";
        $mr = $afw->reWrite("DIVBE",$mr);
//        if ($data["valuta_17"] == $data["valuta"] AND $data["valuta"] != "EUR")
//        {
//          $mr["Debet"]             = abs($data["kostenVV_17"]);
//        }
//        else
//        {
          $mr["Debet"]             = abs($data["kosten_17"]);
//        }

				$mr["Valuta"]            = $data["valuta_17"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);

        if ($data["valutacode"] <> "EUR" AND $data["valuta_17"] == "EUR")
        {
          $mr["Debet"]           = abs($data["kosten_17"]) / $data["wisselkoers"];
          $mr["Valutakoers"]       = $data["wisselkoers"];
          $mr["Valuta"]            = $data["valutacode"];
          $mr["Bedrag"]            = -1 * $mr["Debet"] * $data["wisselkoers"];

        }

        $controleBedrag       += $mr["Bedrag"];
				addAndCheck();

			}


			if ($data["kosten_16"] )
			{
        $mr["Grootboekrekening"] = "KNBA";
        $mr = $afw->reWrite("KOSTEN_16",$mr);
				$mr["Debet"]             = abs($data["kosten_16"]);
				$mr["Valuta"]            = $data["valuta_16"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
				addAndCheck();
			}
			if ($data["kosten_18"] )
			{

				$mr["Debet"]             = abs($data["kosten_18"]);
				$mr["Valuta"]            = $data["valuta_18"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
				addAndCheck();
			}
			if ($data["kosten_19"] )
			{
				$mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
				$mr["Debet"]             = abs($data["kosten_19"]);
				$mr["Valuta"]            = $data["valuta_19"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
				addAndCheck();
			}

		}
    $valutaCorrectie = ($data[valuta] <> "EUR")?$mr[Valutakoers]:1;
    if ($data["valuta"] == $mr["Valuta"])
    {
      $valutaCorrectie = 1;
    }

    checkControleBedrag($controleBedrag );
	}

	function do_CR($record) // Coupon Rente
	{
		global $data,$fonds,$mr,$output,$afw;
		do_algemeen();
		$mr["aktie"]              = "CR";
		$mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "RENOB";
		$mr["Valuta"]            = $fonds["Valuta"];
		$mr["Valutakoers"]       = $data["wisselkoers"];
		$mr["Fonds"]             = $fonds["Fonds"];
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs(($data["aantal"] * $data["fondskoers"])/100);
		$mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("RENOB",$mr);

		if (addAndCheck())
		{
			if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

			//$mr[Fonds]             = "";
			$mr["Aantal"]            = 0;
			$mr["Credit"] 					 = 0;
			$mr["Fondskoers"]        = 0;
			$mr["Transactietype"]    = "";


      if ($data["kosten_17"])
      {

        $mr["Grootboekrekening"] = "DIVBE";
        $mr = $afw->reWrite("DIVBE",$mr);
//        if ($data["valuta_17"] == $data["valuta"] AND $data["valuta"] != "EUR")
//        {
//          $mr["Debet"]             = abs($data["kostenVV_17"]);
//        }
//        else
//        {
          $mr["Debet"]             = abs($data["kosten_17"]);
//        }

        $mr["Valuta"]            = $data["valuta_17"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);

        if ($data["valutacode"] <> "EUR" AND $data["valuta_17"] == "EUR")
        {
          $mr["Debet"]           = abs($data["kosten_17"]) / $data["wisselkoers"];
          $mr["Valutakoers"]       = $data["wisselkoers"];
          $mr["Valuta"]            = $data["valutacode"];
          $mr["Bedrag"]            = -1 * $mr["Debet"] * $data["wisselkoers"];

        }

        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();

      }



			if ($data["kosten_16"] )
			{
			  $mr["Grootboekrekening"] = "KNBA";
        $mr = $afw->reWrite("KOSTEN_16",$mr);
				$mr["Debet"]             = abs($data["kosten_16"]);
				$mr["Valuta"]            = $data["valuta_16"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];

				addAndCheck();
			}

			if ($data["kosten_18"] )
			{

				$mr["Debet"]             = abs($data["kosten_18"]);
				$mr["Valuta"]            = $data["valuta_18"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
				addAndCheck();
			}

			if ($data["kosten_19"] )
			{
				$mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
				$mr["Debet"]             = abs($data["kosten_19"]);
				$mr["Valuta"]            = $data["valuta_19"];
				if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
				$mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];
				addAndCheck();
			}
		}



    $valutaCorrectie = ($data[valuta] <> "EUR")?$mr[Valutakoers]:1;
    if ($data["valuta"] == $mr["Valuta"])
    {
      $valutaCorrectie = 1;
    }

    checkControleBedrag($controleBedrag * $valutaCorrectie);
	}
	

/////////////////////////////////////////////////////////////////////////////////
	//
	/////////////////////////////////////////////////////////////////////////////////
	function do_RVP($record)  // Recieve versus Payment call 3738
	{

		global $data,$fonds,$mr,$output;
		
		do_algemeen();
    $mr[aktie]              = "RVP";
		$mr[Rekening]          = $data[depotnr]."MEM";
		$mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
//		$mr[Valutakoers]       = $data[wisselkoers];
    $mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr["Boekdatum"]);
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
	function do_D($record)  // Deponering van stukken
	{

		global $data,$fonds,$mr,$output;
		$mr[aktie]              = "D";
		do_algemeen();
		$mr[Rekening]          = $data[depotnr]."MEM";
		$mr[Omschrijving]      = "Deponering  ".$fonds[Omschrijving];
		$mr[Grootboekrekening] = "FONDS";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = $data[wisselkoers];
//    $mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
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
		$mr[Valutakoers]       = $data[wisselkoers];
//    $mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
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
		$mr[Valutakoers]       = $data[wisselkoers];
//		$mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
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
	function do_DVP($record)  // Delivery versus Payment call 3738
	{
		global $data,$fonds,$mr,$output;
		
		do_algemeen();
    $mr[aktie]              = "DVP";
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
		$mr[Valutakoers]       = $data[wisselkoers];
//		$mr[Valutakoers]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
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


function getPositionByFonds($mr)
{
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `Rekening` = '".$mr["Rekening"]."'  ";
  $portRec = $db->lookupRecordByQuery($query);
  $query = "
     SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds,
        SUM(Rekeningmutaties.Aantal) AS aantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = '0'
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".date("Y")."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= NOW() AND
        Rekeningen.Portefeuille = '{$portRec["Portefeuille"]}' AND
        Rekeningmutaties.Fonds = '{$mr["Fonds"]}'
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(aantal,4) <> 0
    ";
  $positie = $db->lookupRecordByQuery($query);
  return (int) $positie["aantal"];


}





	function do_error()
	{
		global $do_func;
		echo "<BR>FOUT functie $do_func bestaat niet!";
	}


?>