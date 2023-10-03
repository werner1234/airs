<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/10/03 15:30:35 $
 		File Versie					: $Revision: 1.15 $

 		$Log: degiro_functies.php,v $
 		Revision 1.15  2018/10/03 15:30:35  cvs
 		no message
 		
 		Revision 1.14  2018/10/03 12:48:21  cvs
 		call 7189
 		
 		Revision 1.13  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/06/15 12:21:46  cvs
 		bankcode kolom gewijzigd
 		
 		Revision 1.11  2017/10/16 12:26:39  cvs
 		call 6170
 		
 		Revision 1.10  2017/09/22 14:32:46  cvs
 		call 6205
 		
 		Revision 1.9  2016/12/13 12:19:05  cvs
 		aanpassing import bestandsindeling
 		
 		Revision 1.8  2016/07/18 12:47:17  cvs
 		update 20160718
 		
 		Revision 1.7  2016/03/29 09:13:17  cvs
 		call 4419
 		
 		Revision 1.6  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/07/01 14:07:15  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/06/22 09:05:46  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/06/11 16:10:12  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/06/11 15:58:12  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/03 13:25:48  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***
 		




*/
function giroGetFondskoers($fondscode, $datum="now")
{
  $sqlDatum = ($datum = "now")?" NOW() ":" '".$datum."' ";
  $query = "
    SELECT
      Fondskoersen.Fonds,
      Fondskoersen.Datum,
      Fondskoersen.Koers
    FROM
      `Fondskoersen`
    WHERE
      Fonds = '".$fondscode."'
    AND 
      Datum <= NOW()
    ORDER BY
      Datum DESC
";
  $db = new DB();
  if (!$rec = $db->lookupRecordByQuery($query))
  {
    return false;
  }
  else
  {
    return $rec["Koers"];
  }
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray,$portefeuilleAddArray;

  $portefeuilleAddArray[$portefeuille][] = $valuta;
//debug($portefeuilleAddArray, $portefeuille."/".$valuta);
  if (
    ($valuta == "MEM" AND in_array("EUR", $portefeuilleAddArray[$portefeuille]))  OR
    ($valuta == "EUR" AND in_array("MEM", $portefeuilleAddArray[$portefeuille]))
  )
  {
    return; // per portefeuille alleen MEM of EUR toevoegen tweede valuta wordt automatisch erbij aangemaakt
  }
  $value = "GIRO|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }

}

function giroCheckFonds($giroCode="",$ISIN="",$valuta="")
{
  
  //// LET OP FVLCODE vervangen 
  global $error;
  $db = new DB();
  $fonds = array();
   if ($giroCode <> "")
   {
     $query = "SELECT * FROM Fondsen WHERE giroCode = '".$giroCode."' ";
     if ($rec = $db->lookupRecordByQuery($query))
     {
       return $rec;
     }
   }  

   if ($ISIN == ""  OR $valuta == "")
   {
     return false;
   }

   $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$ISIN."' AND Valuta = '$valuta' ";
   
   if ($rec = $db->lookupRecordByQuery($query))
   {
     return $rec;
   }

   return false;  
     
}

function getRekening($rekeningNr="-1", $depot="GIRO")
{
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`= 0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"]; 
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`= 0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
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

//function addToRekeningAdd($portefeuille,$valuta)
//{
//  global $rekeningAddArray;
//
//  $value = "GIRO|".$portefeuille."|".$valuta;
//  if (!in_array($value,$rekeningAddArray))
//  {
//    $rekeningAddArray[] = $value;
//  }
//}

function _debetbedrag()
{
	global $mr, $valutaLookup;

	if ($valutaLookup == true)
	  return -1 * $mr["Debet"]  * $mr["Valutakoers"];
	else
	  return -1 * $mr["Debet"];
}

function _creditbedrag()
{
	global $mr, $valutaLookup;
	
	if ($valutaLookup == true)
    return $mr["Credit"] * $mr["Valutakoers"];
  else
	  return $mr["Credit"];
	  
}


function _valutakoers($valuta)
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
  
  $valutaLookup = true;


    if ($valuta <> "EUR" AND $mr["Valuta"] == $valuta)
    {
      $valutaLookup = false;
    }
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    
    return $laatsteKoers["Koers"];
  
}



function do_algemeenCash()
{
	global $mr, $row, $volgnr, $data, $_file, $fonds;


	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
	$mr[bankTransactieId]  = $data[1];

  $datum = explode("-",$data[8]);
	$mr[Boekdatum]         = $datum[0]."-".$datum[1]."-".$datum[2];

  $datum = explode(".",$data[8]);
  $mr[settlementDatum]   = $datum[0]."-".$datum[1]."-".$datum[2];
  //$mr[FX_koppelid]       = $data[16];
  $fonds = giroCheckFonds($data[11],$data[14],$data[15]);
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file, $fonds;


	$mr[bestand]           = $_file;
	$mr[regelnr]           = $row;
	$mr[bankTransactieId]  = $data[1];

  $datum = explode("-",$data[9]);
	$mr[Boekdatum]         = $datum[0]."-".$datum[1]."-".$datum[2];

  $datum = explode(".",$data[9]);
  $mr[settlementDatum]   = $datum[0]."-".$datum[1]."-".$datum[2];
  
  $fonds = giroCheckFonds($data[6],$data[10],$data[12]);
}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $ISINskipLichtingDeponering;
  $controleBedrag = 0;
  
	$mr = array();
  $mr[Rekening]          = trim($data[3]).trim($data[12]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  do_algemeen();
  $mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers($data[12]);
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[14];
	
  
  if ($data[15] == 0)
  {
    $mr[aktie]             = "D";
    $mr[Transactietype]    = "D";
    $mr[Rekening]          = substr($mr[Rekening],0,-3)."MEM";
    $mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
    if (in_array($data[10], $ISINskipLichtingDeponering))
    {
      $mr["Fondskoers"]        = 0;
    }
    else
    {
      $mr["Fondskoers"]        = giroGetFondskoers($fonds["Fonds"],$mr["Boekdatum"]);
    }
    $mr[Debet]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
    $mr[Credit]            = 0;
    $mr[Bedrag]            = -1 * $mr["Debet"]  * $mr["Valutakoers"];
  }
  else
  {
    $mr[aktie]             = "A";
    $mr[Transactietype]    = "A";
    $mr[Rekening]          = trim($data[3]).trim($data[12]);
    $mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
    $mr[Fondskoers]        = $data[15];
    $mr[Debet]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
    $mr[Credit]            = 0;
    $mr[Bedrag]            = _debetbedrag();
  }
	
	

  $controleBedrag       += $mr[Bedrag];
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;
  
  if ($mr[aktie] == "D")
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
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";
    if ($mr["Bedrag"] != 0)
    {
      $output[] = $mr;
    }

  }
  
  if ($data[15] <> 0)
  {
    checkControleBedrag($controleBedrag*-1,$data[16]);
  }  
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $ISINskipLichtingDeponering;
  $controleBedrag = 0;
  
	$mr = array();
  $mr[Rekening]          = trim($data[3]).trim($data[12]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  do_algemeen();
  $mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers($data[12]);
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[14];
  
	
  $controleBedrag       += $mr[Bedrag];
  if ($data[15] == 0)
  {
    $mr[aktie]             = "L";
    $mr[Transactietype]    = "L";
    $mr[Rekening]          = substr($mr[Rekening],0,-3)."MEM";
    $mr[Omschrijving]      = "Lichting ".$fonds[Omschrijving];
//    debug($ISINskipLichtingDeponering);
//    debug($data);
    if (in_array($data[10], $ISINskipLichtingDeponering))
    {
      $mr["Fondskoers"]        = 0;
    }
    else
    {
      $mr["Fondskoers"]        = giroGetFondskoers($fonds["Fonds"],$mr["Boekdatum"]);
    }
    $mr[Credit]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
    $mr[Debet]            = 0;
    $mr[Bedrag]            = $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    $mr[aktie]             = "V";
    $mr[Transactietype]    = "V";
    $mr[Rekening]          = trim($data[3]).trim($data[12]);
    $mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving]; 
    $mr[Fondskoers]        = $data[15];
    $mr[Credit]             = abs($mr[Aantal] * $mr[Fondskoers] * $fonds[Fondseenheid]);
    $mr[Debet]            = 0;
    $mr[Bedrag]            = _creditbedrag();
  }


  $controleBedrag       += $mr[Bedrag];
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;
  
  if ($mr[aktie] == "L")
  {
    $mr[Grootboekrekening] = "ONTTR";
    $mr[Fonds]             = "";
    $mr[Valuta]            = "EUR";
    $mr[Valutakoers]       = 1;
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Credit]            = 0;
    $mr[Debet]             = abs($mr[Bedrag]);
    $mr[Bedrag]            = $mr[Debet];
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";
    if ($mr["Bedrag"] != 0)
    {
      $output[] = $mr;
    }
  }
  
  if ($data[15] <> 0)
  {
    checkControleBedrag($controleBedrag*-1,$data[16]);
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_STORT()  // Storting van geld of stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output,$meldArray,$actieOmschrijving, $afw;
;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "STORT";
	do_algemeenCash();
  $mr[Rekening]          = trim($data[3]).trim($data[6]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  $mr[Omschrijving]      = $actieOmschrijving;
  $mr[Grootboekrekening] = "STORT";
  $mr[Valuta]            = $data[6];
  $mr[Valutakoers]       = _valutakoers($data[6]);
  $mr[Fonds]             = "";
  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
  $mr[Credit]            = abs($data[7]);
  $mr[Bedrag]            = _creditbedrag();
  $mr[Transactietype]    = "";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 0;
  $mr = $afw->reWrite("GLDSTORT",$mr);
  $output[] = $mr;
  $controleBedrag       += $mr[Bedrag];

  
  
  checkControleBedrag($controleBedrag,$data[7]);
	
}

///////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_ONTTR()  // Opname van geld of stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output,$meldArray,$actieOmschrijving, $afw;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "ONTTR";
	do_algemeenCash();
  $mr[Rekening]          = trim($data[3]).trim($data[6]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  $mr[Omschrijving]      = $actieOmschrijving;
  $mr[Grootboekrekening] = "ONTTR";
  $mr[Valuta]            = $data[6];
  $mr[Valutakoers]       = _valutakoers($data[6]);
  $mr[Fonds]             = "";
  $mr[Aantal]            = 0;
  $mr[Fondskoers]        = 0;
  $mr[Credit]             = 0;
  $mr[Debet]            = abs($data[7]);
  $mr[Bedrag]            = _debetbedrag();
  $mr[Transactietype]    = "";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 0;
  $mr = $afw->reWrite("GLDONTTR",$mr);
  $output[] = $mr;
  $controleBedrag       += $mr[Bedrag];

  checkControleBedrag($controleBedrag,$data[7]);
	
}

///////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_CashAlgemeen($grootboek, $omschrijving="", $fondsOnderdrukken=false)  
{
  global $fonds, $data, $mr, $output,$meldArray,$actieOmschrijving;

	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]             = $grootboek;
	do_algemeenCash();
	$mr[Rekening]          = trim($data[3]).trim($data[6]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ($omschrijving == "")
  {
    $omschrijving = $actieOmschrijving;
  }
  if ($fondsOnderdrukken)
  {
    $mr[Omschrijving]      = $omschrijving;
    $mr[Fonds]             =  "";
  }
  else
  {
    $mr[Omschrijving]      = $omschrijving." ".$fonds[Omschrijving];  
    $mr[Fonds]             =  $fonds[Fonds];
  }
	
	$mr[Grootboekrekening] = $grootboek;
	$mr[Valuta]            = $data[6];
  $mr[Valutakoers]       = _valutakoers($data[6]);
	
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	if ($data[7] < 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr[Debet]             = abs($data[7]);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
	}
	else
	{
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($data[7]);
	  $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[7]);

}

//call 4419  start

function do_RENTE_KV()
{
  global $data;

  if ($data[7] > 0)
  {
    do_CashAlgemeen("RENOB", "Verkoop");
  }
  else
  {
    do_CashAlgemeen("RENME", "Aankoop");
  }
}
//call 4419  stop


function do_DIV()   {  do_CashAlgemeen( "DIV",   "Dividend"         );  }
function do_DIVBE() {  do_CashAlgemeen( "DIVBE", "Dividend"         );  }
function do_KNBA()  {  do_CashAlgemeen( "KNBA"                      );  }
function do_BEH()   {  do_CashAlgemeen( "BEH", "", true             );  }
function do_RENOB() {  do_CashAlgemeen( "RENOB", "Coupon"           );  }

function do_RENTE() {  do_CashAlgemeen( "RENTE", "", true           );  }
function do_VKSTO() {  do_CashAlgemeen( "VKSTO", ""                 );  }
function do_KOST()  {  do_CashAlgemeen( "KOST",  ""                 );  }
function do_KOBU()  {  do_CashAlgemeen( "KOBU",  ""                 );  }
function do_BEW()   {  do_CashAlgemeen( "BEW",   "", true           );  }




function do_FX() 
{  
  global $data;
  
  do_CashAlgemeen( "FX", $data[17], true );
  
}

function do_KRUIS($legA, $legB)
{
  global $mr, $output, $meldArray, $data;
  $data = $legA;
  do_algemeenCash();
  $mr[Fonds]               = "";
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "KRUIS";
  if ($legA[5] == "3040")
  {
    $DebRec = $legA;
    $CredRec = $legB;
  }
  else
  {
    $DebRec = $legB;
    $CredRec = $legA;
  }

  if ($DebRec[6] == "EUR" AND $CredRec[6] <> "EUR" )
  {
   
    $mr["Valuta"]            = $CredRec[6];
    $mr["Valutakoers"]       = abs($DebRec[7] / $CredRec[7]);
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[1];
    $mr["Rekening"]          = $DebRec[3]."EUR";
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($CredRec[7]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($CredRec[7] * $mr["Valutakoers"]) * -1;

    $output[] = $mr;
   
    $controleBedrag += $mr["Debet"];
    $mr["bankTransactieId"]  = $CredRec[1];
    $mr["Rekening"]          = $CredRec[3].$CredRec[6];
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[7]);
    $mr["Bedrag"]            = $mr["Credit"];
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];
    $output[] = $mr;
    $controleBedrag -= $mr["Credit"];

  }
  elseif ($DebRec[6] <> "EUR" AND $CredRec[6] == "EUR" )
  {
   
    $mr["Valuta"]            = $DebRec[6];
    $mr["Valutakoers"]       = abs($CredRec[7] / $DebRec[7]);
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[1];
    $mr["Rekening"]          = $DebRec[3].$DebRec[6];
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[7]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[7]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;
    $mr["bankTransactieId"]  = $CredRec[1];
    $mr["Rekening"]          = $CredRec[3]."EUR";
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($DebRec[7]);
    $mr["Bedrag"]            = abs($DebRec[7]* $mr["Valutakoers"]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;

  }
  elseif ($DebRec[6] <> $CredRec[6] )
  {
   
    $mr["Valuta"]            = $DebRec[6];
    
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[1];
    $mr["Rekening"]          = $DebRec[3].$DebRec[6];
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[7]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[7]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Valuta"]            = $CredRec[6];
    $mr["bankTransactieId"]  = $CredRec[1];
    $mr["Rekening"]          = $CredRec[3].$CredRec[6];
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[7]);
    $mr["Bedrag"]            = abs($CredRec[7]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;
  }
  else
  {
   
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Valuta"]            = $DebRec[6];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = $DebRec[4];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[1];
    $mr["Rekening"]          = $DebRec[3].$DebRec[6];
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[7]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[7]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Grootboekrekening"] = "STORT";
    $mr["Omschrijving"]      = $CredRec[4];
    $mr["Valuta"]            = $CredRec[6];
    $mr["bankTransactieId"]  = $CredRec[1];
    $mr["Rekening"]          = $CredRec[3].$CredRec[6];
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[7]);
    $mr["Bedrag"]            = abs($CredRec[7]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;
  }

  //addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], 0);
    
}


function do_NVT()
{
  // dummy
}


///////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error($transactie="")
{
	global $do_func;
  global $missingTransacties;
  if ($transactie <> "")
  {
    if (in_array($transactie, $missingTransacties))
    {
      return;
    }
    
    $missingTransacties[] = $transactie;
    echo "<BR>FOUT geen transactie mapping voor :".$transactie;
  }
  else
  {
    echo "<BR>FOUT functie $do_func bestaat niet!";
  }
    
	
}


?>