<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/05 14:54:56 $
 		File Versie					: $Revision: 1.15 $

 		$Log: degirov2_functies.php,v $
 		Revision 1.15  2020/02/05 14:54:56  cvs
 		call 8397
 		
 		Revision 1.14  2020/02/03 14:18:19  cvs
 		zonder call
 		
 		Revision 1.13  2020/01/14 14:00:35  cvs
 		call 8342
 		
 		Revision 1.12  2019/09/18 10:31:09  cvs
 		call 8103
 		
 		Revision 1.11  2019/09/18 09:39:31  cvs
 		call 8103
 		
 		Revision 1.10  2019/07/08 10:32:10  cvs
 		call 7910
 		
 		Revision 1.9  2019/03/19 09:17:57  cvs
 		call 7642
 		
 		Revision 1.8  2019/03/06 14:46:15  cvs
 		call 6851
 		
 		Revision 1.7  2019/03/04 15:46:29  cvs
 		call 7243
 		
 		Revision 1.6  2019/03/04 13:14:02  cvs
 		call 7243
 		
 		Revision 1.5  2019/02/14 11:07:20  cvs
 		call 7243
 		
 		Revision 1.4  2019/02/13 10:49:04  cvs
 		call 7243
 		
 		Revision 1.3  2018/10/17 15:38:19  cvs
 		call 7243
 		
 		Revision 1.2  2018/10/17 09:58:08  cvs
 		call 7243
 		
 		Revision 1.1  2018/10/15 15:11:01  cvs
 		call 7243
 		


*/

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

function giroCheckRekening($rekeningNr)
{
  global $error,$row;
  $db = new DB();
  $query = "SELECT id FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$rekeningNr."' AND DepotBank = 'GIRO' ";

  return ($rekening = $db->lookupRecordByQuery($query));

}

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

  global $error;

  if (trim($giroCode) == "15694501")
  {
    $giroCode = "15694498";  // call 7642   Fractiefondscode omzetten naar hoofdfonds
  }

  $fndVal = ($valuta == "GBX")?"GBP":$valuta;

  $db = new DB();
  $fonds = array();

//   call 8053 10-12-2021 ingeschakeld
   if ($giroCode == "705366")
   {
     return true;
   }

   if ($giroCode <> "")
   {
     $query = "SELECT * FROM Fondsen WHERE giroCode = '".$giroCode."' ";
     if ($rec = $db->lookupRecordByQuery($query))
     {
       $rec["pence"] = ($valuta == "GBX")?100:1;
       return $rec;
     }
   }  

   if ($ISIN == ""  OR $fndVal == "")
   {
     return false;
   }

   $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$ISIN."' AND Valuta = '$fndVal' ";
   
   if ($rec = $db->lookupRecordByQuery($query))
   {
     $rec["pence"] = ($valuta == "GBX")?100:1;
     return $rec;
   }

   return false;  
     
}

function getRekening($rekeningNr="-1", $depot="GIRO")
{
  $db = new DB();

  $query = "SELECT * FROM Rekeningen WHERE `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"]; 
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
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
	global $mr, $valutaLookup;
  
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

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file, $fonds;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data[2];
	$mr["Boekdatum"]         = $data[7];
  $mr["settlementDatum"]   = $data[7];
  
  $fonds = giroCheckFonds($data[4],$data[22],$data[30]);

  if ($data[4] != "" AND $data[12] == 0 AND substr($data[9],0,2) != "CA")
  {
    $mr["Rekening"] = substr($data[1]."MEM",2);
  }
  else
  {
    $mr["Rekening"] = substr($data[1] . $data[14],2);
  }
}




/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray, $ISINskipLichtingDeponering, $afw;
  $controleBedrag = 0;

	$mr = array();
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data[14]);
	$mr["Fonds"]             = $fonds["Fonds"];

  $isOptie = false;

	switch($data[19])
  {
    case "BOND":
      $mr["Aantal"]            = $data[11];
      break;
    case "OPT":
      $mr["Aantal"]            = $data[11];
      $isOptie = true;
      break;
    default:
      $mr["Aantal"]            = $data[11]*$data[21];
  }





  if ($data[12] == 0)
  {
    $mr["aktie"]             = "D";
    $mr["Transactietype"]    = "D";
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    if (in_array($data[22], $ISINskipLichtingDeponering))
    {
      $mr["Fondskoers"]        = 0;
    }
    else
    {
      $mr["Fondskoers"]        = giroGetFondskoers($fonds["Fonds"],$mr["Boekdatum"]);
    }

    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"]  * $mr["Valutakoers"];
  }
  else
  {
    $mr["aktie"]             = "A";
    $mr["Transactietype"]    = "A";
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = $data[12] / $fonds["pence"];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
	
	

  $controleBedrag       += $mr["Bedrag"];
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;



  if ($isOptie)
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


	$output[] = $mr;

  if ($mr["aktie"] == "D")
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] > 0)
    {
      $output[] = $mr;
    }
  }


  
  if ($data[12] != 0)
  {
    checkControleBedrag($controleBedrag,$data[13]);
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
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data[14]);
	$mr["Fonds"]             = $fonds["Fonds"];
  $isOptie = false;
  switch($data[19])
  {
    case "OPT":
      $isOptie = true;
      $mr["Aantal"]            = $data[11];
      break;
    case "BOND":
      $mr["Aantal"]            = $data[11];
      break;
    default:
      $mr["Aantal"]            = $data[11]*$data[21];
  }

  if ($data[12] == 0)
  {
    $mr["aktie"]             = "L";
    $mr["Transactietype"]    = "L";
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    if (in_array($data[22], $ISINskipLichtingDeponering))
    {
      $mr["Fondskoers"]        = 0;
    }
    else
    {
      $mr["Fondskoers"]        = giroGetFondskoers($fonds["Fonds"],$mr["Boekdatum"]);
    }

    $mr["Credit"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Debet"]            = 0;
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    $mr["aktie"]             = "V";
    $mr["Transactietype"]    = "V";
    $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = $data[12] / $fonds["pence"];
    $mr["Credit"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Debet"]            = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }


  $controleBedrag       += $mr["Bedrag"];
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;


  if ($isOptie)
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

	$output[] = $mr;
  
  if ($mr["aktie"] == "L")
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Debet"] * -1;
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] != 0)
    {
      $output[] = $mr;
    }

  }
  
  if ($data[12] != 0)
  {
    checkControleBedrag($controleBedrag,$data[13]);
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_MUT()
{
  global $data, $mr;
  if ($data[13] > 0 )
  {
    do_STORT();
  }
  else
  {
    do_ONTTR();
  }
}

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

	$mr["aktie"]              = "STORT";
	do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = $actieOmschrijving;
  $mr["Grootboekrekening"] = "STORT";
  $mr["Valuta"]            = $data[14];
  $mr["Valutakoers"]       = _valutakoers($data[14]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data[13]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("GLDSTORT",$mr);
  $output[] = $mr;
  $controleBedrag       += $mr["Bedrag"];

  checkControleBedrag($controleBedrag,$data[13]);
	
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

	$mr["aktie"]              = "ONTTR";
	do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = $actieOmschrijving;
  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Valuta"]            = $data[14];
  $mr["Valutakoers"]       = _valutakoers($data[14]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]             = 0;
  $mr["Debet"]            = abs($data[13]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("GLDONTTR",$mr);
  $output[] = $mr;
  $controleBedrag       += $mr["Bedrag"];

  checkControleBedrag($controleBedrag,$data[13]);
	
}

///////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_CashAlgemeen($grootboek, $omschrijving="", $fondsOnderdrukken=false)
{
  global $fonds, $data, $mr, $output,$meldArray,$actieOmschrijving, $afw;

	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]             = $grootboek;
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  if ($omschrijving == "")
  {
    $omschrijving = $actieOmschrijving;
  }
  if ($fondsOnderdrukken)
  {
    $mr["Omschrijving"]      = $omschrijving;
    $mr["Fonds"]             =  "";
  }
  else
  {
    $mr["Omschrijving"]      = $omschrijving." ".$fonds["Omschrijving"];
    $mr["Fonds"]             =  $fonds["Fonds"];
  }

	$mr["Grootboekrekening"] = $grootboek;
	$mr["Valuta"]            = $data[14];
  $mr["Valutakoers"]       = _valutakoers($data[14]);

	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	if ($data[13] < 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr["Debet"]             = abs($data[13]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	}
	else
	{
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[13]);
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}

	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  if ($grootboek == "KOST")
  {
    $mr = $afw->reWrite("KOST",$mr);
  }
  if ($grootboek == "KOBU")
  {
    $mr = $afw->reWrite("KOBU",$mr);
  }
  if ($grootboek == "KNBA")
  {
    $mr = $afw->reWrite("KNBA",$mr);
  }
  if ($grootboek == "BEH")
  {
    $mr = $afw->reWrite("BEH",$mr);
  }
  $mr = $afw->reWrite("ALG",$mr);

	$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[13]);

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
  global $data, $row;
  $row = $data["row"];
  if ($data[13] > 0)
  {
    do_CashAlgemeen( "STORT", $data[15], true );
  }
  else
  {
    do_CashAlgemeen( "ONTTR", $data[15], true );
  }


  
}

function do_KRUIS($legA, $legB)
{


  global $mr, $output, $meldArray, $data;
  $data = $legA;
  $mr = array();
  do_algemeen();

  $mr["Fonds"]             = "";
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "KRUIS";
  if ($legA[9] == "CA3040" OR $legA[9] == "CA3046")
  {
    $DebRec = $legA;
    $CredRec = $legB;
  }
  else
  {
    $DebRec = $legB;
    $CredRec = $legA;
  }

  if ($DebRec[14] == "EUR" AND $CredRec[14] <> "EUR" )
  {
   
    $mr["Valuta"]            = $CredRec[14];
    $mr["Valutakoers"]       = abs($DebRec[13] / $CredRec[13]);
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[18];
    $mr["Rekening"]          = substr($DebRec[1] . $DebRec[14],2);
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($CredRec[13]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($CredRec[13] * $mr["Valutakoers"]) * -1;

    $output[] = $mr;
   
    $controleBedrag += $mr["Debet"];
    $mr["bankTransactieId"]  = $CredRec[18];
    $mr["Rekening"]          = substr($CredRec[1] . $CredRec[14],2);
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[13]);
    $mr["Bedrag"]            = $mr["Credit"];
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];
    $output[] = $mr;
    $controleBedrag -= $mr["Credit"];

  }
  elseif ($DebRec[14] <> "EUR" AND $CredRec[14] == "EUR" )
  {
   
    $mr["Valuta"]            = $DebRec[14];
    $mr["Valutakoers"]       = abs($CredRec[13] / $DebRec[13]);
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[18];
    $mr["Rekening"]          = substr($DebRec[1] . $DebRec[14],2);
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[13]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[13]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;
    $mr["bankTransactieId"]  = $CredRec[18];
    $mr["Rekening"]          = substr($CredRec[1],2)."EUR";
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($DebRec[13]);
    $mr["Bedrag"]            = abs($DebRec[13]* $mr["Valutakoers"]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;

  }
  elseif ($DebRec[14] <> $CredRec[14] )
  {
   
    $mr["Valuta"]            = $DebRec[14];
    
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[18];
    $mr["Rekening"]          = substr($DebRec[1] . $DebRec[14],2);
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[13]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[13]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Valuta"]            = $CredRec[14];
    $mr["bankTransactieId"]  = $CredRec[18];
    $mr["Rekening"]          = substr($CredRec[1] . $CredRec[14],2);
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[13]);
    $mr["Bedrag"]            = abs($CredRec[13]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;
  }
  else
  {
   
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Valuta"]            = $DebRec[14];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = $DebRec[15];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $DebRec[18];
    $mr["Rekening"]          = substr($DebRec[1] . $DebRec[14],2);
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[13]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[13]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Grootboekrekening"] = "STORT";
    $mr["Omschrijving"]      = $CredRec[15];
    $mr["Valuta"]            = $CredRec[14];
    $mr["bankTransactieId"]  = $CredRec[18];
    $mr["Rekening"]          = substr($CredRec[1].$CredRec[14],2);
    if (!giroCheckRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[13]);
    $mr["Bedrag"]            = abs($CredRec[13]);
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