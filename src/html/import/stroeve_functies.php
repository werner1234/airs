<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/13 14:32:56 $
 		File Versie					: $Revision: 1.56 $

 		$Log: stroeve_functies.php,v $
 		Revision 1.56  2020/07/13 14:32:56  cvs
 		negatieve creditrente hernoemen naar debetrente
 		
 		Revision 1.55  2020/06/29 10:15:54  cvs
 		call 8727
 		
 		Revision 1.54  2019/09/13 09:42:26  cvs
 		call 8045
 		
 		Revision 1.53  2019/09/04 08:30:52  cvs
 		call 8045
 		
 		Revision 1.52  2019/08/19 14:20:16  cvs
 		call 8002
 		
 		Revision 1.51  2019/03/19 09:38:46  cvs
 		call 7636
 		
 		Revision 1.50  2018/11/23 13:53:16  cvs
 		call 7348
 		
 		Revision 1.49  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2018/07/17 12:27:17  cvs
 		call 6734
 		
 		Revision 1.47  2018/06/15 08:51:07  cvs
 		call 6358
 		
 		Revision 1.46  2018/02/02 12:22:01  cvs
 		call 6410
 		
 		Revision 1.45  2017/09/20 06:17:33  cvs
 		megaupdate 2722
 		
 		Revision 1.44  2016/07/18 12:48:48  cvs
 		update 20160718
 		
 		Revision 1.43  2016/05/25 06:52:52  cvs
 		terugdraaien negatieve boekingen en reset creditbedrag in bepaalde functies
 		
 		Revision 1.42  2016/05/11 12:46:01  cvs
 		call 4918
 		
 		Revision 1.41  2015/11/20 10:29:29  cvs
 		call 4352
 		
 		Revision 1.40  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.39  2014/11/05 12:51:05  cvs
 		dbs 3177
 		
 		Revision 1.38  2014/09/24 13:24:52  cvs
 		dbs2853
 		
 		Revision 1.37  2014/09/17 15:05:29  cvs
 		dbs 2797
 		
 		Revision 1.36  2014/09/15 08:16:45  cvs
 		dbs 2838
 		
 		Revision 1.35  2014/08/29 08:51:41  cvs
 		dbs 2742
 		
 		Revision 1.34  2014/07/08 12:43:24  cvs
 		*** empty log message ***
 		
 		Revision 1.33  2014/04/02 13:54:51  cvs
 		*** empty log message ***
 		
 		Revision 1.32  2014/03/12 10:02:40  cvs
 		*** empty log message ***
 		
 		Revision 1.31  2013/12/16 08:21:00  cvs
 		*** empty log message ***

 		Revision 1.30  2013/05/06 14:26:13  cvs
 		*** empty log message ***

 		Revision 1.29  2012/11/07 10:38:17  cvs
 		*** empty log message ***

 		Revision 1.28  2012/05/15 15:02:19  cvs
 		controlebedrag

 		Revision 1.27  2012/05/08 15:27:04  cvs
 		nota controle

 		Revision 1.26  2011/06/28 11:12:53  cvs
 		*** empty log message ***

 		Revision 1.25  2011/06/28 09:17:37  cvs
 		fondscode aanpassingen



*/

function getRekening($rekeningNr="-1", $depot="TGB")
{
  global $__appvar;
  $db = new DB();
  
  if($__appvar["bedrijf"] == "HOME")
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec["Rekening"]; 
    }
    else
    {
      $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        return $rekeningNr;
      }
      else
      {
        return false; 
      }

    }
  } 
  else  // zonder depotbank controle
  {
      $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."'  ";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        return $rekeningNr;
      }
      else
      {
        return false; 
      }

  }
  
  
  
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray,$portefeuilleAddArray;

  $portefeuilleAddArray[$portefeuille][] = $valuta;

  if (
       ($valuta == "MEM" AND in_array("EUR", $portefeuilleAddArray[$portefeuille]))  OR
       ($valuta == "EUR" AND in_array("MEM", $portefeuilleAddArray[$portefeuille]))
     )
  {
    return; // per portefeuille alleen MEM of EUR toevoegen tweede valuta wordt automatisch erbij aangemaakt
  }
  $value = "TGB|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }

}

function _debetbedrag()
{
	global $data, $mr, $valutaLookup;

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
    $mr[Valuta] = $valuta;
	   if ($data[23] > 0)
     {
       $valutaLookup = true;

       return $data[23];
     }
     else
     {
		   $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC LIMIT 1";
       $DB->SQL($query);
       $laatsteKoers = $DB->lookupRecord();
       $valutaLookup = true;
       return $laatsteKoers[Koers];
     }
	}
	else
	  return $data[10];
}

function _valutakoersDIV()  // tbv dbs2742
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[9];
	$valutaLookup = false;
  if ($data[10] == 1)
  {
    $mr[Valuta]  = $valuta;
  }
  
	if ($valuta <> "EUR" )
	{
    
	   if ($data[23] > 0)
     {
       $valutaLookup = true;
       
       return $data[23];
     }
     else
     {
		   $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC LIMIT 1";
       $DB->SQL($query);
       $laatsteKoers = $DB->lookupRecord();
       $valutaLookup = true;
       return $laatsteKoers[Koers];
     }
	}
	else
	  return $data[10];
}

function checkVoorDubbelInRM($mr)
{
  global $meldArray;
  $db = new DB();
  $query = "
  SELECT 
    id 
  FROM 
    Rekeningmutaties 
  WHERE 
    bankTransactieId = '".$mr["bankTransactieId"]."' AND 
    Rekening         = '".$mr["Rekening"]."' AND
    Boekdatum        = '".$mr["Boekdatum"]."'
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"].")";
    return true;
  }
  return false;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;


  $datum = explode(".",$data[15]);
	$mr["Boekdatum"]         = $datum[2]."-".$datum[1]."-".$datum[0];
  $mr["bankTransactieId"]  = $data[18]."_".trim($data[1])."_".$datum[2].$datum[1].$datum[0];

  $datum = explode(".",$data[16]);
  $mr["settlementDatum"]   = $datum[2]."-".$datum[1]."-".$datum[0];
}

// function checkControleBedrag verhuisd naar html/import/algemeneImportFuncties.php call 6734


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
  $controleBedrag = 0;
  
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
	$mr["Rekening"]          = trim($data[1]).trim($data[9]);
	$mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
	$mr["Omschrijving"]      = "Aankoop ".$fonds[Omschrijving];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[5];
	$mr["Fondskoers"]        = $data[8];
  $mr["Debet"]             = abs($data[5] * $data[8] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $data[9];
  if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs($data[11]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Valuta"]            = $data[9];
  if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
//	if ($data[12] > 0)
//	{
//		$mr["Credit"]            = abs($data[12]);
//		$mr["Debet"]             = 0;
//		$mr["Bedrag"]            = _creditbedrag();
//	}
//	else
//	{
		$mr["Credit"]            = 0;
		$mr["Debet"]             = abs($data[12]);
		$mr["Bedrag"]            = _debetbedrag();
//	}

  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU", $mr);
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

  if ($data[12] + $data[13] <> 0)  // call 4206 fout TGB in mabeltrans
  {

    $mr["Grootboekrekening"] = "KOBU";
    $mr["Valuta"]            = $data[9];
    if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
    //$mr[Fonds]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;

    $mr["Debet"]             = abs($data[13]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("KOBU2", $mr);
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
      $output[] = $mr;
  }
  
  
	if ($data[7] <> 0)  // aankoop obligatie
	{
	  $mr["Grootboekrekening"] = "RENME";
	  $mr["Valuta"]            = $fonds["Valuta"];
	  $mr["Valutakoers"]       = _valutakoers();
	  //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
		$mr["Credit"]            = 0;
	  $mr["Debet"]             = abs($data[7]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
	    $output[] = $mr;

	}
  checkControleBedrag($controleBedrag,$data[14]*-1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "V";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
	// call 4857 aanpassing start
	$mr = $afw->reWrite("FONDS",$mr);
	// call 4857 aanpassing stop
	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	{
		// call 4857 aanpassing start
		$mr = $afw->reWrite("KOST", $mr);
		// call 4857 aanpassing stop

		$output[] = $mr;
	}

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
//	if ($data[12] > 0)
//	{
//		$mr[Credit]            = abs($data[12]);
//		$mr[Debet]             = 0;
//		$mr[Bedrag]            = _creditbedrag();
//	}
//	else
//	{
		$mr[Credit]            = 0;
		$mr[Debet]             = abs($data[12]);
		$mr[Bedrag]            = _debetbedrag();
//	}
	$controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
	{

		// call 4857 aanpassing start
		$mr = $afw->reWrite("KOBU", $mr);
		// call 4857 aanpassing stop
		$output[] = $mr;
	}
  
  if ($data[12] + $data[13] <> 0)  // call 4206 fout TGB in mabeltrans
  {
    $mr[Grootboekrekening] = "KOBU";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Credit]            = 0;
    $mr[Debet]             = abs($data[13]);
    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";
    if ($mr[Bedrag] <> 0)
		{
			// call 4857 aanpassing start
			$mr = $afw->reWrite("KOBU2",$mr);
			// call 4857 aanpassing stop
			$output[] = $mr;
		}
   }  
	if ($data[7] <> 0 )
	{
	  $mr[Grootboekrekening] = "RENOB";
    $mr[Valuta]            = $fonds[Valuta];
    $mr[Valutakoers]       = _valutakoers();
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
	  $mr[Credit]            = abs($data[7]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";
	  if ($mr[Bedrag] <> 0)
		{
			// call 4857 aanpassing start
			$mr = $afw->reWrite("RENOB", $mr);
			// call 4857 aanpassing stop
			$output[] = $mr;
		}
	}
  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OA()  //Aankoop openen bij opties en futures
{

  global $fonds, $data, $mr, $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]             = "OA";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
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
  $mr = $afw->reWrite("KOBU",$mr);
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]*-1);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OV()  //Verkoop openen bij opties en futures
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "OV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

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
  $controleBedrag       += $mr[Bedrag];
  $mr = $afw->reWrite("KOBU",$mr);
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SA()  //Aankoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]             = "SA";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

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
  $controleBedrag       += $mr[Bedrag];
  $mr = $afw->reWrite("KOBU",$mr);

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]*-1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SV()  //Verkoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output,$meldArray,$afw;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "SV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

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
  $controleBedrag       += $mr[Bedrag];
  $mr = $afw->reWrite("KOBU", $mr);

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]);
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
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
	$mr["aktie"]             = "E";
	do_algemeen();
	$mr["Rekening"]          = trim($data[1]).trim($data[9]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();

  if ($data[8] == 0)
  {
  	$mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Transactietype"]    = "D";
    if ($fonds["fondssoort"] == "STOCKDIV")
    {
      $mr["Rekening"]          = trim($data[1])."MEM";
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      $dbl = new DB();

      $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
      $laatsteKoers = $dbl->lookupRecordByQuery($query);

      $mr["Valutakoers"] =  $laatsteKoers["Koers"];
    }
  }
  else
  {
	  $mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
    $mr[Transactietype]    = "A";
  }
	$mr[Grootboekrekening] = "FONDS";


	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();


	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_R()  //Rente of couponrente
{
  global $fonds, $data, $mr, $output,$meldArray,$afw;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "R";
	do_algemeen();

	if ($data[3])
	{
	  if ($data[14] < 0)  // als veld negatief betreft correctie rente
	  {

		  $mr[Rekening]          = trim($data[1]).trim($data[9]);
      $mr[Rekening]          = getRekening($mr["Rekening"]);
      if ( checkVoorDubbelInRM($mr) )
      {
        return true;
      }
		  if ($data[3])
		    $mr[Omschrijving]      = "Coupon ".$fonds[Omschrijving];
		  else
		    $mr[Omschrijving]      = $data[22];

		  $mr[Grootboekrekening] = "RENOB";
		  $mr[Valuta]            = $fonds[Valuta];
		  $mr[Valutakoers]       = _valutakoers();
		  $mr[Fonds]             = "";
		  $mr[Aantal]            = 0;
		  $mr[Fondskoers]        = 0;
		  $mr[Debet]             = abs(($data[5] * $data[8]) * $fonds[Fondseenheid]);
		  $mr[Credit]            = 0;
		  $mr[Bedrag]            = _debetbedrag();
      $controleBedrag       += $mr[Bedrag];

		  $mr[Transactietype]    = "";
		  $mr[Verwerkt]          = 0;
		  $mr[Memoriaalboeking]  = 0;

		  $output[] = $mr;

      // 2008-04-17 cvs start toegvoeging
		  $mr[Grootboekrekening] = "DIVBE";
	    $mr[Valuta]            = $data[9];
	    if ($data[9] <> "EUR")
	      $mr[Valutakoers]       = _valutakoers();
	    else
	      $mr[Valutakoers]       = 1;
	    $mr[Fonds]             = "";
	    $mr[Aantal]            = 0;
	    $mr[Fondskoers]        = 0;
	    $mr[Debet]             = 0;
	    $mr[Credit]            = abs($data[13]);
	    $mr[Bedrag]            = $mr[Credit];
      $controleBedrag       += $mr[Bedrag];

	    if ($mr[Bedrag] <> 0)
		    $output[] = $mr;
		  // 2008-04-17 cvs einde toegvoeging

		  $mr[Grootboekrekening] = "KNBA";
		  $mr[Valuta]            = $data[9];
		  if ($data[9] <> "EUR")
		    $mr[Valutakoers]       = _valutakoers();
		  else
		    $mr[Valutakoers]       = 1;
		  $mr[Fonds]             = "";
		  $mr[Aantal]            = 0;
		  $mr[Fondskoers]        = 0;
		  $mr[Debet]             = 0;
		  $mr[Credit]            = abs($data[11]);
		  $mr[Bedrag]            = $mr[Credit];
      $controleBedrag       += $mr[Bedrag];

      if ($mr[Bedrag] <> 0)
		    $output[] = $mr;

		  $mr[Grootboekrekening] = "KOBU";
		  $mr[Valuta]            = $data[9];
  	  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  	  $mr[Fonds]             = "";
      $mr[Aantal]            = 0;
		  $mr[Fondskoers]        = 0;
		  $mr[Debet]             = 0;
		  $mr[Credit]            = abs($data[12]);
		  $mr[Bedrag]            = _creditbedrag();
      $controleBedrag       += $mr[Bedrag];
      $mr = $afw->reWrite("KOBU", $mr);
		  $mr[Transactietype]    = "";
		  if ($mr[Bedrag] <> 0)
			  $output[] = $mr;
	  }
	  else
  	{
		  $mr[Rekening]          = trim($data[1]).trim($data[9]);
      $mr[Rekening]          = getRekening($mr["Rekening"]);

		  if ($data[3])
		    $mr[Omschrijving]      = "Coupon ".$fonds[Omschrijving];
		  else
		    $mr[Omschrijving]      = $data[22];

		  $mr[Grootboekrekening] = "RENOB";
		  $mr[Valuta]            = $fonds[Valuta];
		  $mr[Valutakoers]       = _valutakoers();
		  $mr[Fonds]             =  $fonds[Fonds];
		  $mr[Aantal]            = 0;
		  $mr[Fondskoers]        = 0;
		  $mr[Debet]             = 0;
		  $mr[Credit]            = abs(($data[5] * $data[8]) * $fonds[Fondseenheid]);
		  $mr[Bedrag]            = _creditbedrag();
      $controleBedrag       += $mr[Bedrag];

		  $mr[Transactietype]    = "";
		  $mr[Verwerkt]          = 0;
		  $mr[Memoriaalboeking]  = 0;

		  $output[] = $mr;

      // 2008-04-17 cvs start toegvoeging
		  $mr[Grootboekrekening] = "DIVBE";
	    $mr[Valuta]            = $data[9];
	    if ($data[9] <> "EUR")
	      $mr[Valutakoers]       = _valutakoers();
	    else
	      $mr[Valutakoers]       = 1;
	    //$mr[Fonds]             = "";
	    $mr[Aantal]            = 0;
	    $mr[Fondskoers]        = 0;
	    $mr[Debet]             = abs($data[13]);
	    $mr[Credit]            = 0;
	    $mr[Bedrag]            = -1 * $mr[Debet];
      $controleBedrag       += $mr[Bedrag];

	    if ($mr[Bedrag] <> 0)
		    $output[] = $mr;
		  // 2008-04-17 cvs einde toegvoeging

		  $mr[Grootboekrekening] = "KNBA";
		  $mr[Valuta]            = $data[9];
		  if ($data[9] <> "EUR")
		    $mr[Valutakoers]       = _valutakoers();
		  else
		    $mr[Valutakoers]       = 1;
		  //$mr[Fonds]             = "";
		  $mr[Aantal]            = 0;
		  $mr[Fondskoers]        = 0;
		  $mr[Debet]             = abs($data[11]);
		  $mr[Credit]            = 0;
		  $mr[Bedrag]            = -1 * $mr[Debet];
      $controleBedrag       += $mr[Bedrag];

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
      $controleBedrag       += $mr[Bedrag];
      $mr = $afw->reWrite("KOBU", $mr);
		  $mr[Transactietype]    = "";
		  if ($mr[Bedrag] <> 0)
			  $output[] = $mr;
  	}
	}
	else
	{
		$mr[Rekening]          = trim($data[1]).trim($data[9]);
    $mr[Rekening]          = getRekening($mr["Rekening"]);
		$mr[Omschrijving]      = "Creditrente";
		$mr[Grootboekrekening] = "RENTE";
		$mr[Valuta]            = $data[9];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[14]); // deze tijdelijk vullen tbv de _creditbedrag() berekening
		$mr[Bedrag]            = _creditbedrag();
    $controleBedrag        = $mr[Bedrag];

		if ($data[14] > 0)
		{
		  $mr[Debet]             = 0;
		  $mr[Credit]            = abs($data[14]);
		}
		else
		{
      if (stristr( $data[22],"hyp") OR 
          stristr( $data[22],"len")OR
          stristr( $data[22],"contr")  )
      {
        $mr[Grootboekrekening] = "ONTTR";
        $mr["Omschrijving"] = $data[22];
      }
      else
      {

        if ( substr($data[22],0,8) == "CI RENTE")
        {
          $mr["Omschrijving"]      = "Creditrente";
        }
        else
        {
          $mr["Omschrijving"]      = "Debetrente";
        }

      }
			
			$mr[Debet]             = abs($data[14]);
			$mr[Credit]            = 0;
			$mr[Bedrag]            = _debetbedrag();
      $controleBedrag        = $mr[Bedrag];

		}
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;
		$output[] = $mr;
	}

  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()  //Lossing van obligaties
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "L";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "V";
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
  $controleBedrag       += $mr[Bedrag];

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
  $controleBedrag       += $mr[Bedrag];
  $mr = $afw->reWrite("KOBU", $mr);
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;



	if ($data[7] <> 0 )
	{
		$mr[Grootboekrekening] = "RENOB";
		$mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		//$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Credit]            = abs($data[7]);
		$mr[Debet]             = 0;
		$mr[Bedrag]            = _creditbedrag();
		$controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "";
		if ($mr[Bedrag] <> 0)
			$output[] = $mr;
	}


  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "DV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
	$mr[Omschrijving]      = "Dividend ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "DIV";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoersDIV();   //dbs 2742
	$mr[Fonds]             =  $fonds[Fonds];
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($data[14] < 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr[Debet]             = abs($data[5] * $data[8]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
	else
	{
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[5] * $data[8]);
	  $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
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
	//$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($data[14] < 0)  // als veld negatief betreft een correctie Dividend
	{
	  $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[13]);
	  $mr[Bedrag]            = $mr[Credit];
    $controleBedrag       += $mr[Bedrag];

	}
	else
	{
	  $mr[Debet]             = abs($data[13]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = -1 * $mr[Debet];
    $controleBedrag       += $mr[Bedrag];

	}
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KNBA";
	$mr[Valuta]            = $data[9];
	if ($data[9] <> "EUR")
	  $mr[Valutakoers]       = _valutakoers();
	else
	  $mr[Valutakoers]       = 1;
	//$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($data[14] < 0)  // als veld negatief betreft een correctie Dividend
	{
	  $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[11]);
	  $mr[Bedrag]            = $mr[Credit];
    $controleBedrag       += $mr[Bedrag];

	}
	else
	{
	  $mr[Debet]             = abs($data[11]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = -1 * $mr[Debet];
    $controleBedrag       += $mr[Bedrag];

	}
  if ($mr[Bedrag] <> 0)
	  $output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($data[14] < 0)  // als veld negatief betreft een correctie Dividend
	{
	  $mr[Credit]            = abs($data[12]);
	  $mr[Debet]             = 0;
	  $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
	else
	{
	  $mr[Credit]            = 0;
	  $mr[Debet]             = abs($data[12]);
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
  $mr = $afw->reWrite("KOBU", $mr);
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]);

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
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
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
	$mr[Bedrag]            = ($mr[Credit] * $mr[Valutakoers]);  //2008-04-17 cvs correctie valutafout
	$mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
    $output[] = $mr;

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
	// wordt niet ingelezen
  return;
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
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
	$mr[Omschrijving]      = $data[22];
	$mr[Grootboekrekening] = "KNBA";
	$mr[Valuta]            = $data[9];
	if ($data[9] == "EUR")
	{
		$mr[Valutakoers]       = 1;
	}
	else
	{
		$mr[Valutakoers]       = _valutakoers();
	}

	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = abs($data[14]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  if ($mr[Bedrag] <> 0)
	  $output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KD()  //Kosten depot
{
	global $fonds, $data, $mr, $output, $afw;
	$mr = array();
	$mr["aktie"]              = "KD";
	do_algemeen();
	$mr["Rekening"]          = trim($data[1]).trim($data[9]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
	$mr["Omschrijving"]      = $data[22];
	switch (substr($data[22],0,2))
	{
		case "57":
			$mr["Grootboekrekening"] = "BEH";
			break;
		case "19":
			$mr["Grootboekrekening"] = "BEW";
			break;
		case "22":
		case "99":
			$mr["Grootboekrekening"] = "VKSTO";
			break;
		default:
			$mr["Grootboekrekening"] = "KNBA";
			break;
	}
	$mr["Valuta"]            = $data[9];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data[14]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = -1 * $mr[Debet];
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("KD", $mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
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
  global $__appvar;
	global $data;
	global $mr;
	global $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;

	$mr["aktie"]              = "ST";
	do_algemeen();
	if ($data[3])  // ISINcode gevuld
	{

    $mr["Valuta"]            = $fonds["Valuta"];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Fonds"]             = $fonds["Fonds"];
		$mr["Aantal"]            = $data[5];
		$mr["Fondskoers"]        = $data[8];

    if ($data[5] == 0)  // aantal = leeg
    {
      $mr["Rekening"]          = trim($data[1]).trim($data[9]);
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      if ( checkVoorDubbelInRM($mr) )
      {
        return true;
      }
      $mr["Omschrijving"]      = "Fractieverrekening  ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "VKSTO";
      $mr["Valuta"]            = $data[9];
      $mr["Valutakoers"]       = ($mr["Valuta"] == "EUR")?1:_valutakoers();
		  $mr["Aantal"]            = 0;
	    $mr["Fondskoers"]        = 0;
  	  $mr["Debet"]             = 0;
		  $mr["Credit"]            = abs($data[14]);
	    $mr["Bedrag"]            = ($mr["Credit"] *  $mr["Valutakoers"]); // 2008-04-17 cvs valutacorrectie
      $controleBedrag       += $mr["Bedrag"];

  	  $mr[Transactietype]    = "";
			// call 4857 aanpassing start
			$mr = $afw->reWrite("VKSTO", $mr);
			// call 4857 aanpassing stop
  	  $output[] = $mr;
    }
    else
    {
		  $mr["Rekening"]          = trim($data[1])."MEM";
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      if ( checkVoorDubbelInRM($mr) )
      {
        return true;
      }

      $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
	   	$mr["Grootboekrekening"] = "FONDS";

		  $mr["Debet"]             = abs($data[5] * $data[8] * $fonds["Fondseenheid"]);
		  $mr["Credit"]            = 0;
		  $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "D";
		  $mr["Verwerkt"]          = 0;
		  $mr["Memoriaalboeking"]  = 1;



		  $output[] = $mr;
      if ($mr["Bedrag"] <> 0)
      {
        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
		    $mr["Grootboekrekening"] = "STORT";
		    $mr["Fonds"]             = "";
		    $mr["Aantal"]            = 0;
	  	  $mr["Fondskoers"]        = 0;
  		  $mr["Debet"]             = 0;
		    $mr["Credit"]            = abs($mr["Bedrag"]); // bedrag FONDS boeking;
        $mr["Bedrag"]            = $mr["Credit"];
      //  $controleBedrag       += $mr[Bedrag];

  		  $mr["Transactietype"]    = "";
	      $output[] = $mr;
      }

      if ($data[7] > 0)  // toegevoegd 20-6-2007 meenemen opgelopen rente
      {

	    	$mr["Grootboekrekening"] = "RENME";
	   	  $mr["Valuta"]            = $fonds["Valuta"];
	 	    $mr["Valutakoers"]       = _valutakoers();
		    $mr["Fonds"]             = $fonds["Fonds"];
		    $mr["Aantal"]            = 0;
		    $mr["Fondskoers"]        = 0;
		    $mr["Debet"]             = abs($data[7]);
		    $mr["Credit"]            = 0;
		    $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
        $controleBedrag       += $mr["Bedrag"]; // 8804 renme hoort te worden opgeteld voor het controlebedrag echter TGB doet het foutief
		    $mr["Transactietype"]    = "";
		    $mr["Verwerkt"]          = 0;
		    $mr["Memoriaalboeking"]  = 1;
		    $output[] = $mr;

        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
        $mr["Grootboekrekening"] = "STORT";
		    $mr["Fonds"]             = "";
		    $mr["Aantal"]            = 0;
	      $mr["Fondskoers"]        = 0;
  	    $mr["Debet"]             = 0;
		    $mr["Credit"]            = abs($mr["Bedrag"]);
	      $mr["Bedrag"]            = $mr["Credit"];
        //$controleBedrag       += $mr[Bedrag];
  	    $mr["Transactietype"]     = "";
  	    $output[] = $mr;

        checkControleBedrag($controleBedrag,$data[14]*$mr["Valutakoers"]*-1);
        
      }

    // einde aanpassing 20-6-2007

    }
	}
	else
	{
		if (substr($data[22],0,2) == "34" or
		    substr($data[22],0,2) == "VT")  // Geen ISIN en veld 22 begint met "34"
		{
			$_srt = substr($data[22],0,2);
			$mr[Rekening]          = trim($data[1]).trim($data[9]);
      $mr[Rekening]          = getRekening($mr["Rekening"]);
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
			$mr[Bedrag]            = _creditbedrag();
			//$mr[Bedrag]            = $mr[Credit];
			$mr[Transactietype]    = "";
			$mr[Verwerkt]          = 0;
			$mr[Memoriaalboeking]  = 0;

			$output[] = $mr;

			if (substr($data[22],0,2) == "34")
			{
				$mr[Rekening]          = trim($data[1])."DEP";
        $mr[Rekening]          = getRekening($mr["Rekening"]);
				$mr[Grootboekrekening] = "KRUIS";
				$mr[Valuta]            = $data[9];
				$mr[Valutakoers]       = _valutakoers();
				//$mr[Fonds]             = "";
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

      //aetodo BOX 15032019
			$mr["Rekening"]          = trim($data[1]).trim($data[9]);
      $mr["Rekening"]          = getRekening($mr["Rekening"]);

		  $mr["Omschrijving"]      = $data[22];

      while (strstr($mr["Omschrijving"], "  "))
      {
        $mr["Omschrijving"]      = str_replace("  "," ", $mr["Omschrijving"]);
      }

		  if ($__appvar["bedrijf"] == "BOX")  // call 7636
      {
        if (substr($mr["Omschrijving"],0,3) == "BG ")
        {
          $oms = explode(" ", $mr["Omschrijving"]);
          array_shift($oms);
          array_shift($oms);
          $mr["Omschrijving"] = implode(" ",$oms);
        }
      }
			$mr["Grootboekrekening"] = "STORT";

      if (  substr($data[22],0,13) == "TR Taxreclaim" )
      {
        $mr["Grootboekrekening"] = "DIVBE";
      }

			$mr["Valuta"]            = $data[9];
			$mr["Valutakoers"]       = _valutakoers();
			$mr["Fonds"]             = "";
			$mr["Aantal"]            = 0;
			$mr["Fondskoers"]        = 0;
			$mr["Debet"]             = 0;
			$mr["Credit"]            = abs($data[14]);
			$mr["Bedrag"]            = _creditbedrag();
			$mr["Transactietype"]    = "";
			$mr["Verwerkt"]          = 0;
			$mr["Memoriaalboeking"]  = 0;
      // call 4857 aanpassing start
      $mr = $afw->reWrite("STORT", $mr);
      // call 4857 aanpassing stop
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
	global $mr, $__appvar;
	global $output, $afw;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "OP";
	do_algemeen();
	if ($data[3])
	{
    if ($data[5] == 0 AND $data[8] == 0)
    {
      $mr["Rekening"]          = trim($data[1]).trim($data[9]);
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      if ( checkVoorDubbelInRM($mr) )
      {
        return true;
      }
      $mr["Omschrijving"]      = "Overige kosten:  ".$fonds["Omschrijving"];  //dbs 2853
      $mr["Grootboekrekening"] = "KNBA";
      $mr["Valuta"]            = $data[9];
			if ($data[9] == 0)
			{
				$mr["Valutakoers"]       = 1;
			}
			else
			{
				$mr["Valutakoers"]       = _valutakoers();
			}

      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = abs($data[14]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag       -= $mr["Bedrag"];
      if ($mr["Bedrag"] <> 0)
      {
				// call 4857 aanpassing start
				$mr = $afw->reWrite("KNBA", $mr);
				// call 4857 aanpassing stop
        $output[] = $mr;
      }  
    } 
    else
    {
      $mr["Rekening"]          = trim($data[1])."MEM";
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      if ( checkVoorDubbelInRM($mr) )
      {
        return true;
      }
      $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "FONDS";
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = _valutakoers();
      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = -1 * $data[5];
      $mr["Fondskoers"]        = $data[8];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[5] * $data[8] * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            =  $mr["Credit"] * $mr["Valutakoers"];
      $mr["Transactietype"]    = "L";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 1;
      $controleBedrag       += $mr["Bedrag"];
      $output[] = $mr;
      if ($mr["Bedrag"] <> 0)
      {
        $mr["Grootboekrekening"] = "ONTTR";
//        $mr["Rekening"]          = getRekening($mr["Rekening"]);
//        if ($mr["Valuta"] == "EUR")      $mr["Valutakoers"]  = 1;
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
        $mr["Debet"]             = abs($mr["Bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * $mr["Debet"];
        $mr["Transactietype"]    = "";
        $output[] = $mr;
      }


      if ($data[7] > 0)  // toegevoegd 20-6-2007 meenemen opgelopen rente
      {
        $mr["Rekening"]          = trim($data[1])."MEM";
        $mr["Rekening"]          = getRekening($mr["Rekening"]);
        $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
        $mr["Grootboekrekening"] = "RENOB";
        $mr["Valuta"]            = $fonds["Valuta"];
        $mr["Valutakoers"]       = _valutakoers();
        $mr["Fonds"]             = $fonds["Fonds"];
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($data[7]);
        $mr["Bedrag"]            =  $mr["Credit"] * $mr["Valutakoers"];
        $mr["Transactietype"]    = "";
        $mr["Verwerkt"]          = 0;
        $mr["Memoriaalboeking"]  = 1;
//        $controleBedrag       += $mr["Bedrag"]; //
        $output[] = $mr;
    
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Debet"]             = abs($mr["Bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * $mr["Debet"];
        $mr["Transactietype"]    = "";
        if (substr($data[22],0,15) == "VK VALUTAKOSTEN")
        {
          $mr = $afw->reWrite("VALK", $mr);
        }
        $controleBedrag       += $mr["Bedrag"];
        $output[] = $mr;

      }
			$controleBedrag = $controleBedrag * -1;
    }
		
    
    // einde aanpassing 20-6-2007
    checkControleBedrag($controleBedrag,$data[14]*$mr["Valutakoers"]);
	}
	else
	{
		if (substr($data[22],0,2) == "34" OR
		    substr($data[22],0,2) == "VT")  // Geen ISIN en veld 22 begint met "34"
		{
			$_srt = substr($data[22],0,2) == "VT";

			$mr["Rekening"]          = trim($data[1]).trim($data[9]);
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
			if ($_srt == "VT")
			  $mr["Omschrijving"]      = "Valutatransactie";
			else
			  $mr["Omschrijving"]      = "Overboeking deposito";
			$mr["Grootboekrekening"] = "KRUIS";
			$mr["Valuta"]            = $data[9];
			$mr["Valutakoers"]       = _valutakoers();
			$mr["Fonds"]             = "";
			$mr["Aantal"]            = 0;
			$mr["Fondskoers"]        = 0;
			$mr["Debet"]             = abs($data[14]);
			$mr["Credit"]            = 0;
			$mr["Bedrag"]            = _debetbedrag();  // 2008-04-17 cvs valutacorrectie
			$mr["Transactietype"]    = "";
			$mr["Verwerkt"]          = 0;
			$mr["Memoriaalboeking"]  = 0;
			
      $output[] = $mr;
      if (substr($data[22],0,2) == "34")
      {

				$mr["Rekening"]          = trim($data[1])."DEP";
        $mr["Rekening"]          = getRekening($mr["Rekening"]);
				$mr["Grootboekrekening"] = "KRUIS";
				$mr["Valuta"]            = $data[9];
				$mr["Valutakoers"]       = _valutakoers();
				$mr["Fonds"]             = "";
				$mr["Aantal"]            = 0;
				$mr["Fondskoers"]        = 0;
				$mr["Debet"]             = 0;
				$mr["Credit"]            = abs($data[14]);
				$mr["Bedrag"]            = $mr["Credit"];
				$mr["Transactietype"]    = "";
				$mr["Verwerkt"]          = 0;
				$mr["Memoriaalboeking"]  = 0;

				$output[] = $mr;
      }
		}
		else
		{

			$mr["Rekening"]          = trim($data[1]).trim($data[9]);
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      if ( checkVoorDubbelInRM($mr) )
      {
        return true;
      }
    	$mr["Omschrijving"]      = $data[22];

      while (strstr($mr["Omschrijving"], "  "))
      {
        $mr["Omschrijving"]      = str_replace("  "," ", $mr["Omschrijving"]);
      }

      if ($__appvar["bedrijf"] == "BOX")  // call 7636
      {

        if (substr($mr["Omschrijving"],0,3) == "BG ")
        {
          $oms = explode(" ", $mr["Omschrijving"]);
          array_shift($oms);
          array_shift($oms);
          $mr["Omschrijving"] = implode(" ",$oms);
        }
      }




		  if (substr($data[22],0,2) == "57")
      {
			  $mr["Grootboekrekening"] = "BEH";
      }  
			else
      {  
			  $mr["Grootboekrekening"] = "ONTTR";
      }


      if (
        substr($data[22],0,10) == "BF Banking" OR
        substr($data[22],0,9)  == "FN Kosten" OR
        substr($data[22],0,19) == "99 Ext. kst. taxrec" OR
        substr($data[22],0,19) == "22 Ext. kst. taxrec" OR
        substr($data[22],0,17) == "46 Externe kosten" OR
        substr($data[22],0,15) == "VK VALUTAKOSTEN")
      {
        $mr["Grootboekrekening"] = "KNBA";
      }


			$mr["Valuta"]            = $data[9];
			$mr["Valutakoers"]       = _valutakoers();
			$mr["Fonds"]             = "";
			$mr["Aantal"]            = 0;
			$mr["Fondskoers"]        = 0;
			$mr["Debet"]             = abs($data[14]);
			$mr["Credit"]            = 0;
			$mr["Bedrag"]            = _debetbedrag();
			$mr["Transactietype"]    = "";
			$mr["Verwerkt"]          = 0;
			$mr["Memoriaalboeking"]  = 0;

			// call 4857 aanpassing start
      $mr = $afw->reWrite("GLDKNBA", $mr);
      $mr = $afw->reWrite("GLDONTTR", $mr);
      // call 4857 aanpassing stop
      if (substr($data[22],0,15) == "VK VALUTAKOSTEN")
      {
        $mr = $afw->reWrite("VALK", $mr);
      }

      $output[] = $mr;
      
		}

	}
   
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VM()  //Variation margin  toegevoegd d.d. 8-7-2014
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
  
  do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
	$mr[Omschrijving]      = "Variation Margin: ".$data[3];
  $mr[Grootboekrekening] = "VMAR";
	$mr[Valuta]            = $data[9];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  if ($data[14] > 0)
  {
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($data[14]);
    $mr[Bedrag]            = _creditbedrag();
  } 
  else
  {
    $mr[Debet]             = abs($data[14]);
    $mr[Credit]            = 0;
    $mr[Bedrag]            = _debetbedrag();
  }  
	
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
	$output[] = $mr;

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