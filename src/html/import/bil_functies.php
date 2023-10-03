<?
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.2 $

 		$Log: bil_functies.php,v $
 		Revision 1.2  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/09/20 06:15:59  cvs
 		megaupdate
 		
 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***
 		




*/

function getRekening($rekeningNr="-1", $depot="BIL")
{
  $db = new DB();
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

function reformat($data)
{
  foreach ($data as $item)
  {
    $n = str_replace(",", ".", $item);
    $out[] = trim($n);
  }
  return $out;
}
function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "BIL|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function KostenSamenstellen()
{
  global $data;
  $kostenArray = array();
  // 15 sets van 4 kolommen
  for($x=0; $x<15; $x++ )
  {
    $ind = 24+ ($x*4);
    if (trim($data[$ind]) == "")
    {
      continue;
    }
    $key = $data[$ind+3];
    $val = $data[$ind+2];
    $kostenArray[$key] += $val;
  }
  return $kostenArray;
}


function _debetbedrag()
{
	global $data, $mr, $valutaLookup;

	if ($valutaLookup == true)
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr[Valutakoers]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[9];
	if ($valutaLookup == true)
	  return $mr["Credit"];
	else
	  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$afrekeningValuta = $data[14];
	$valutaLookup = false;
	if ($afrekeningValuta <> "EUR" AND $mr["Valuta"] == $afrekeningValuta)
	{
    $mr["Valuta"] = $afrekeningValuta;
	   if ($data[21] > 0)
     {
       $valutaLookup = true;
       
       return $data[21];
     }
     else
     {
		   $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$afrekeningValuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC LIMIT 1";
       $DB->SQL($query);
       $laatsteKoers = $DB->lookupRecord();
       $valutaLookup = true;
       return $laatsteKoers["Koers"];
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
		   $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC LIMIT 1";
       $DB->SQL($query);
       $laatsteKoers = $DB->lookupRecord();
       $valutaLookup = true;
       return $laatsteKoers["Koers"];
     }
	}
	else
	  return $data[10];
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data[8];

  $datum = explode("/",$data[15]);
	$mr["Boekdatum"]         = $datum[2]."-".$datum[1]."-".$datum[0];

  $datum = explode("/",$data[16]);
  $mr["settlementDatum"]   = $datum[2]."-".$datum[1]."-".$datum[0];
}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  debug($data, "aankoop");
  $mr = array();
  $mr["aktie"] = "A";
  do_algemeen();
  $mr["Rekening"] = trim($data[13]) . trim($data[14]);
  $mr["Rekening"] = getRekening($mr["Rekening"]);

  $mr["Omschrijving"] = "Aankoop " . $fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"] = $fonds["Valuta"];
//	$mr["Valutakoers"]       = _valutakoers();
  $mr["Valutakoers"] = $data[87];
  $mr["Fonds"] = $fonds["Fonds"];
  $mr["Aantal"] = $data[5];
  $mr["Fondskoers"] = $data[11];
  $mr["Debet"] = abs($data[5] * $data[11] * $fonds["Fondseenheid"]);
  $mr["Credit"] = 0;
  $mr["Bedrag"] = _debetbedrag();
  $controleBedrag += $mr["Bedrag"];
  $mr["Transactietype"] = "A";
  $mr["Verwerkt"] = 0;
  $mr["Memoriaalboeking"] = 0;

  $output[] = $mr;

  $kost = KostenSamenstellen();

  foreach ($kost as $_val => $_bedrag)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Valuta"] = $_val;
    $mr["Aantal"] = 0;
    $mr["Fondskoers"] = 0;
    $mr["Debet"] = abs($_bedrag);
    $mr["Bedrag"] = _debetbedrag();
    $controleBedrag += $mr["Bedrag"];
    $mr["Transactietype"] = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }

//
//
//	$mr["Grootboekrekening"] = "KOBU";
//	$mr["Valuta"]            = $data[31];
//  if ($mr[Valuta] == "EUR") $mr["Valutakoers"]  = 1;
//  //$mr[Fonds]             = "";
//	$mr["Aantal"]            = 0;
//	$mr["Fondskoers"]        = 0;
//	$mr["Credit"]            = 0;
//	$mr["Debet"]             = abs($data[30]);
//	$mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag       += $mr["Bedrag"];
//	$mr["Transactietype"]    = "";
//	if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }





//  $mr[Grootboekrekening] = "KOBU";
//	$mr[Valuta]            = $data[9];
//  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
//  //$mr[Fonds]             = "";
//	$mr[Aantal]            = 0;
//	$mr[Fondskoers]        = 0;
//	$mr[Credit]            = 0;
//	$mr[Debet]             = abs($data[13]);
//	$mr[Bedrag]            = _debetbedrag();
//  $controleBedrag       += $mr[Bedrag];
//	$mr[Transactietype]    = "";
//	if ($mr[Bedrag] <> 0)
//	  $output[] = $mr;

  
  
	if ($data[103] <> 0)  // aankoop obligatie
	{
	  $mr["Grootboekrekening"] = "RENME";
	  $mr["Valuta"]            = $fonds["Valuta"];

	  //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  $mr["Debet"]             = abs($data[103]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }


	}
  checkControleBedrag($controleBedrag,$data[6]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output,$meldArray;
  debug($data,"verkoop");
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "V";
	do_algemeen();
  $mr["Rekening"]          = trim($data[13]).trim($data[14]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
	$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
//	$mr["Valutakoers"]       = _valutakoers();
  $mr["Valutakoers"]       = $data[87];
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = -1 * $data[5];
	$mr["Fondskoers"]        = $data[11];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($data[5] * $data[11] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;


  $kost = KostenSamenstellen();

  foreach ($kost as $_val => $_bedrag)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Valuta"] = $_val;
    $mr["Aantal"] = 0;
    $mr["Fondskoers"] = 0;
    $mr["Debet"] = abs($_bedrag);
    $mr["Bedrag"] = _debetbedrag();
    $controleBedrag += $mr["Bedrag"];
    $mr["Transactietype"] = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
  
	if ($data[103] <> 0 )
	{
	  $mr["Grootboekrekening"] = "RENOB";
    $mr["Valuta"]            = $fonds["Valuta"];

    //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
  	$mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = abs($data[103]);
	  $mr["Debet"]             = 0;
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
		  $output[] = $mr;
	}
  checkControleBedrag($controleBedrag,$data[6]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_R()  //Rente of couponrente
{
  return true; // nog niet klaar
  global $fonds, $data, $mr, $output,$meldArray;
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

		  $mr[Transactietype]    = "";
		  if ($mr[Bedrag] <> 0)
			  $output[] = $mr;
	  }
	  else
  	{
		  $mr[Rekening]          = trim($data[1]).trim($data[9]);
      $mr[Rekening]          = getRekening($mr["Rekening"]);

		  if ($data[3])
		    $mr[Omschrijving]      = "Rente ".$fonds[Omschrijving];
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
        $mr[Omschrijving]      = "Debetrente";
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
  return true; // nog niet klaar
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "L";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
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

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "RENOB";  //obligatie rente
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[7]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DV()  //Contant dividend
{
  return true; // nog niet klaar
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "DV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
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
  return true; // nog niet klaar
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
	$mr[aktie]              = "DO";
	do_algemeen();
	$mr[Rekening]          = trim($data[1])."MEM";
  $mr[Rekening]          = getRekening($mr["Rekening"]);
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

function do_KO()  //Kosten algemeen
{
  return true; // nog niet klaar
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "`KO";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = $data[22];
	$mr[Grootboekrekening] = "KNBA";
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
  if ($mr[Bedrag] <> 0)
	  $output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KD()  //Kosten depot
{
  return true; // nog niet klaar
	global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "KD";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
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
  $mr[Debet]             = abs($data[14]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  if ($mr[Bedrag] <> 0)
	  $output[] = $mr;
  return;
}

///////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_ST()  // Storting van geld of stukken
{
  return true; // nog niet klaar
  global $fonds;
	global $data;
	global $mr;
	global $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "ST";
	do_algemeen();
	if ($data[3])  // ISINcode gevuld
	{

    $mr[Valuta]            = $fonds[Valuta];
		$mr[Valutakoers]       = _valutakoers();
		$mr[Fonds]             = $fonds[Fonds];
		$mr[Aantal]            = $data[5];
		$mr[Fondskoers]        = $data[8];

    if ($data[5] == 0)  // aantal = leeg
    {
      $mr[Rekening]          = trim($data[1]).trim($data[9]);
      $mr[Rekening]          = getRekening($mr["Rekening"]);
      $mr[Omschrijving]      = "Fractieverrekening  ".$fonds[Omschrijving];
      $mr[Grootboekrekening] = "VKSTO";
		  $mr[Fonds]             = "";
		  $mr[Aantal]            = 0;
	    $mr[Fondskoers]        = 0;
  	  $mr[Debet]             = 0;
		  $mr[Credit]            = abs($data[14]);
	    $mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]); // 2008-04-17 cvs valutacorrectie
      $controleBedrag       += $mr[Bedrag];

  	  $mr[Transactietype]    = "";

  	  $output[] = $mr;
    }
    else
    {
		  $mr[Rekening]          = trim($data[1])."MEM";
      $mr[Rekening]          = getRekening($mr["Rekening"]);

      $mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
	   	$mr[Grootboekrekening] = "FONDS";

		  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
		  $mr[Credit]            = 0;
		  $mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
      $controleBedrag       += $mr[Bedrag];

		  $mr[Transactietype]    = "D";
		  $mr[Verwerkt]          = 0;
		  $mr[Memoriaalboeking]  = 1;

		  $output[] = $mr;
      if ($mr[Bedrag] <> 0)
      {
		    $mr[Grootboekrekening] = "STORT";
		    $mr[Fonds]             = "";
		    $mr[Aantal]            = 0;
	  	  $mr[Fondskoers]        = 0;
  		  $mr[Debet]             = 0;
		    $mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	  	  $mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]);  // 2008-04-17 cvs valutacorrectie
      //  $controleBedrag       += $mr[Bedrag];

  		  $mr[Transactietype]    = "";
        $output[] = $mr;
      }

      if ($data[7] > 0)  // toegevoegd 20-6-2007 meenemen opgelopen rente
      {

	    	$mr[Grootboekrekening] = "RENME";
	   	  $mr[Valuta]            = $fonds[Valuta];
	 	    $mr[Valutakoers]       = _valutakoers();
		    $mr[Fonds]             = $fonds[Fonds];
		    $mr[Aantal]            = 0;
		    $mr[Fondskoers]        = 0;
		    $mr[Debet]             = abs($data[7]);
		    $mr[Credit]            = 0;
		    $mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
        $controleBedrag       += $mr[Bedrag];

		    $mr[Transactietype]    = "";
		    $mr[Verwerkt]          = 0;
		    $mr[Memoriaalboeking]  = 1;

		    $output[] = $mr;

        $mr[Grootboekrekening] = "STORT";
		    $mr[Fonds]             = "";
		    $mr[Aantal]            = 0;
	      $mr[Fondskoers]        = 0;
  	    $mr[Debet]             = 0;
		    $mr[Credit]            = abs($data[7]);
	      $mr[Bedrag]            = ($mr["Credit"] *  $mr["Valutakoers"]); // 2008-04-17 cvs valutacorrectie
        //$controleBedrag       += $mr[Bedrag];

  	    $mr[Transactietype]    = "";

  	    $output[] = $mr;

        checkControleBedrag($controleBedrag,$data[14]*-1);
        
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
			$mr[Rekening]          = trim($data[1]).trim($data[9]);
      $mr[Rekening]          = getRekening($mr["Rekening"]);
			if ($data[3])
		  	$mr[Omschrijving]      = "Storting van geld";
			else
		  	$mr[Omschrijving]      = $data[22];
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
  return true; // nog niet klaar
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "OP";
	do_algemeen();
	if ($data[3])
	{
    if ($data[5] == 0 AND $data[8] == 0)
    {
      $mr[Rekening]          = trim($data[1]).trim($data[9]);
      $mr[Rekening]          = getRekening($mr["Rekening"]);
      $mr[Omschrijving]      = "Overige kosten:  ".$fonds[Omschrijving];  //dbs 2853
      $mr[Grootboekrekening] = "KNBA";
      $mr[Valuta]            = $data[9];
      $mr[Valutakoers]       = _valutakoers();
      $mr[Fonds]             = $fonds[Fonds];
      $mr[Aantal]            = 0;
      $mr[Fondskoers]        = 0;
      $mr[Debet]             = abs($data[14]);
      $mr[Credit]            = 0;
      $mr[Bedrag]            = _debetbedrag(); 
      $mr[Transactietype]    = "";
      $mr[Verwerkt]          = 0;
      $mr[Memoriaalboeking]  = 0;
      $controleBedrag       -= $mr[Bedrag];
      if ($mr[Bedrag] <> 0)
      {  
        $output[] = $mr;
      }  
    } 
    else
    {
      $mr[Rekening]          = trim($data[1])."MEM";
      $mr[Rekening]          = getRekening($mr["Rekening"]);
      $mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
      $mr[Grootboekrekening] = "FONDS";
      $mr[Valuta]            = $fonds[Valuta];
      $mr[Valutakoers]       = _valutakoers();
      $mr[Fonds]             = $fonds[Fonds];
      $mr[Aantal]            = -1 * $data[5];
      $mr[Fondskoers]        = $data[8];
      $mr[Debet]             = 0;
      $mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
      $mr[Bedrag]            =  $mr[Credit] * $mr[Valutakoers];
      $mr[Transactietype]    = "L";
      $mr[Verwerkt]          = 0;
      $mr[Memoriaalboeking]  = 1;
      
      $controleBedrag       += $mr[Bedrag];
      $output[] = $mr;
      if ($mr[Bedrag] <> 0)
      {
        $mr[Grootboekrekening] = "ONTTR";
        $mr[Rekening]          = getRekening($mr["Rekening"]);
        if ($mr[Valuta] == "EUR")      $mr[Valutakoers]  = 1;
        $mr[Fonds]             = "";
        $mr[Aantal]            = 0;
        $mr[Fondskoers]        = 0;
        $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
        $mr[Credit]            = 0;
        $mr[Bedrag]            = -1 * ($mr["Debet"] *  $mr["Valutakoers"]);  // 2008-04-17 cvs valutacorrectie
        $mr[Transactietype]    = "";

        $output[] = $mr;
      }


      if ($data[7] > 0)  // toegevoegd 20-6-2007 meenemen opgelopen rente
      {
        $mr[Rekening]          = trim($data[1])."MEM";
        $mr[Rekening]          = getRekening($mr["Rekening"]);
        $mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
        $mr[Grootboekrekening] = "RENOB";
        $mr[Valuta]            = $fonds[Valuta];
        $mr[Valutakoers]       = _valutakoers();
        $mr[Fonds]             = $fonds[Fonds];
        $mr[Aantal]            = 0;
        $mr[Fondskoers]        = 0;
        $mr[Debet]             = 0;
        $mr[Credit]            = abs($data[7]);
        $mr[Bedrag]            =  $mr[Credit] * $mr[Valutakoers];
        $mr[Transactietype]    = "";
        $mr[Verwerkt]          = 0;
        $mr[Memoriaalboeking]  = 1;
        $controleBedrag       += $mr[Bedrag];

        $output[] = $mr;

        $mr[Grootboekrekening] = "ONTTR";
        if ($mr[Valuta] == "EUR")      $mr[Valutakoers]  = 1;
        $mr[Fonds]             = "";
        $mr[Aantal]            = 0;
        $mr[Fondskoers]        = 0;
        $mr[Debet]             = abs($data[7]);
        $mr[Credit]            = 0;
        $mr[Bedrag]            = -1 * ($mr["Debet"] *  $mr["Valutakoers"]);  // 2008-04-17 cvs valutacorrectie
        $mr[Transactietype]    = "";

        $output[] = $mr;
      }
    }
		
    
    // einde aanpassing 20-6-2007
    checkControleBedrag($controleBedrag,$data[14]); 
	}
	else
	{
		if (substr($data[22],0,2) == "34" OR
		    substr($data[22],0,2) == "VT")  // Geen ISIN en veld 22 begint met "34"
		{
			$_srt = substr($data[22],0,2) == "VT";

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
			$mr[Debet]             = abs($data[14]);
			$mr[Credit]            = 0;
			$mr[Bedrag]            = _debetbedrag();  // 2008-04-17 cvs valutacorrectie
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
      $mr[Rekening]          = getRekening($mr["Rekening"]);
			if ($data[3])
		  	$mr[Omschrijving]      = "Opname van geld";
			else
		  	$mr[Omschrijving]      = $data[22];

		  if (substr($data[22],0,2) == "57")
			  $mr[Grootboekrekening] = "BEH";
			else
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


function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>