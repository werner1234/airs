<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/19 08:25:36 $
 		File Versie					: $Revision: 1.8 $

 		$Log: modulez_functies.php,v $
 		Revision 1.8  2020/06/19 08:25:36  cvs
 		call 8700
 		
 		Revision 1.7  2020/06/17 08:26:30  cvs
 		call 8700
 		
 		Revision 1.6  2018/10/08 06:26:11  cvs
 		call 7175, bevindingen 5-10
 		
 		Revision 1.5  2018/10/03 15:31:34  cvs
 		no message
 		
 		Revision 1.4  2018/10/03 12:50:36  cvs
 		no message
 		
 		Revision 1.3  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.2  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/06/18 06:56:32  cvs
 		update naar VRY omgeving
 		
 		Revision 1.10  2018/02/02 12:25:20  cvs
 		call 6556
 		
 		Revision 1.9  2018/01/11 07:38:08  cvs
 		no message
 		
 		Revision 1.8  2017/12/06 12:33:45  cvs
 		omschrijving valautatransactie aangepast
 		
 		Revision 1.7  2017/12/05 12:15:47  cvs
 		call 6224
 		
 		Revision 1.6  2017/11/27 10:01:29  cvs
 		call 6224
 		
 		Revision 1.5  2017/11/24 16:28:10  cvs
 		call 6224
 		
 		Revision 1.4  2017/11/17 15:13:20  cvs
 		call 6224
 		
 		Revision 1.3  2017/10/25 13:59:18  cvs
 		call 6224 Lynx import
 		
 		Revision 1.2  2017/10/20 10:15:10  cvs
 		call 6224
 		
 		Revision 1.1  2017/09/29 12:15:48  cvs
 		call 6224
 		


*/

function convertDataRow($dataRaw)
{
  $split = explode('"', $dataRaw);
  $items = explode(",",$split[1]);
  return trimRecord($items);
}


function trimRecord($data)
{
  foreach ($data as $key => $value)
  {
    if (trim($value) == ".00")
      $dataOut[] = 0;
    else
      $dataOut[] = trim($value);
  }
  return $dataOut;
}

function getRekening($rekeningNr="-1", $depot="MDZ")
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
    LEFT(bankTransactieId,25) = '".substr($mr["bankTransactieId"],0,25)."' AND 
    Rekening         = '".$mr["Rekening"]."' 
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"].")";
    return true;
  }
  return false;
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "???|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr, $valutaLookup;

  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;

  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers($val="")
{
	global $data, $mr;
	$db = new DB();


  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];


}

function stripRecordNA($data)
{
  $out = array();
  foreach ($data as $k=>$v)
  {
    $out[$k] = ($v == "N/A")?0:trim($v);
  }
  return $out;
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

function transidSpecial()
{
  global $mr, $data;
  $mr["bankTransactieId"]  = trim($data[2]).trim($data[4]).substr(trim($data[20]),-5);
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file,$i;

  $data = stripRecordNA($data);
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data[$i["ref_nr"]];
  $mr["Boekdatum"]         = $data[$i["transaction_date"]];
  $mr["settlementDatum"]   = $data[$i["settlement_date"]];
  $mr["Rekening"] = trim($data[$i["account_number"]]).trim($data[$i["currency"]]);
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

function do_MUT()  // geld mutaties
{

  global $fonds, $data, $mr, $output,$i;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
//debug($data);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data[$i["currency"]];
  $mr["Valutakoers"]       = ($data[$i["exchange_rate"]] != 0)?$data[$i["exchange_rate"]]:1;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[$i["mutation_liquid"]] > 0)
  {
    $mr["Omschrijving"]      = $data["transOms"];
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[$i["mutation_liquid"]]);
    $mr["Bedrag"]          = _creditbedrag();
  }
  else
  {
    $mr["Omschrijving"]      = $data["transOms"];
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data[$i["mutation_liquid"]]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
//  debug($mr);
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[$i["mutation_liquid"]]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_KNBA()  //  kosten
{

  global $fonds, $data, $mr, $output,$i;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data[$i["currency"]];
  $mr["Valutakoers"]       = ($data[$i["exchange_rate"]] != 0)?$data[$i["exchange_rate"]]:1;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["transOms"];
  if ($data[$i["mutation_liquid"]] > 0)
  {
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[$i["mutation_liquid"]]);
    $mr["Bedrag"]          = _creditbedrag();
  }
  else
  {
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Debet"]             = abs($data[$i["mutation_liquid"]]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[$i["mutation_liquid"]]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_KODI()  //  kosten
{

  global $fonds, $data, $mr, $output,$i;
  $controleBedrag = 0;


  $mr = array();
  $mr["aktie"]              = "KODI";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data[$i["currency"]];
  $mr["Valutakoers"]       = ($data[$i["exchange_rate"]] != 0)?$data[$i["exchange_rate"]]:1;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KNBA";
  $mr["Omschrijving"]      = $data[$i["description"]];
  if (strtolower($mr["Omschrijving"]) == "kosten openen rekening")
  {
    $mr["Grootboekrekening"] = "OPKO";
  }
  if ($data[$i["mutation_liquid"]] > 0)
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[$i["mutation_liquid"]]);
    $mr["Bedrag"]          = _creditbedrag();
  }
  else
  {
    $mr["Debet"]             = abs($data[$i["mutation_liquid"]]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[$i["mutation_liquid"]]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray,$i;


//debug($data, $mr["regelnr"]);

  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "A";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = ($data[$i["exchange_rate"]] != 0)?$data[$i["exchange_rate"]]:1;
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[$i["nr_of_participations"]];
  $mr["Fondskoers"]        = $data[$i["fund_value_instrument_currency"]];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "A";


  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  checkControleBedrag($controleBedrag,$data[$i["value"]]*-1);


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_V()  // Verkoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray,$i;

  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = ($data[$i["exchange_rate"]] != 0)?$data[$i["exchange_rate"]]:1;
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data[$i["nr_of_participations"]];
  $mr["Fondskoers"]        = $data[$i["fund_value_instrument_currency"]];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  checkControleBedrag($controleBedrag,-1 * $data[$i["value"]]);


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEH()  //Kosten advies
{

  global $fonds, $data, $mr, $output,$i;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data[$i["currency"]];
  $mr["Valutakoers"]       = ($data[$i["exchange_rate"]] != 0)?$data[$i["exchange_rate"]]:1;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[$i["description"]];
  if ($data[$i["mutation_liquid"]] > 0)
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[$i["mutation_liquid"]]);
    $mr["Bedrag"]          = _creditbedrag();
  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = abs($data[$i["mutation_liquid"]]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[$i["mutation_liquid"]]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error($do_func)
{

	echo "<BR>ModuleZ transactiecode $do_func bestaat niet!";
}


?>