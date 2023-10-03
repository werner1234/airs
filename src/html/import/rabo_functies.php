<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/08 13:09:16 $
 		File Versie					: $Revision: 1.32 $

 		$Log: rabo_functies.php,v $
 		Revision 1.32  2020/06/08 13:09:16  cvs
 		call 8208
 		

20201023-naar TEST
20201030- opnieuw naar RVV
20210111- naar RVV verse copy voor master
20210122- naar RVV verse copy voor master
*/


function raboDatum  ($date)
{
  $date = substr($date,0,8);
  return substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
}


function getRaboFonds($data)
{
  $db = new DB();
  $query = "SELECT * FROM Fondsen WHERE raboCode = '".trim($data["bankcode"])."' ";
  if (!$fonds = $db->lookupRecordByQuery($query) )
  {

  }
  return $fonds;
}

function checkControleBedrag($controleBedrag, $nettoBedrag)
{
  global $meldArray, $data, $mr;
//debug(array($controleBedrag,$nettoBedrag,$data),"in controle");
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($nettoBedrag,2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan BANK = ".$notabedrag." / AIRS = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
}



function _debetbedrag()
{
  global $data, $mr;

  if ($data["rekValuta"] == "EUR" AND $mr["Valuta"] != "EUR")
  {
    return  -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else if ($data["rekValuta"] == $mr["Valuta"] )
  {
    return -1 * $mr["Debet"];
  }
  else
  {
    return  ($mr["Debet"] * 1/$mr["Debet"]);
  }

}

function _creditbedrag()
{

  global $data, $mr;


  if ($data["rekValuta"] == "EUR" AND $mr["Valuta"] != "EUR")
  {
    return  ($mr["Credit"] * $mr["Valutakoers"]);
  }
  else if ($data["rekValuta"] == $mr["Valuta"] )
  {
    return $mr["Credit"];
  }
  else
  {
    return  ($mr["Credit"] * 1/$mr["Valutakoers"]);
  }
}

function belastingValuta($rekValuta, $belastingValuta)
{
  global $fonds, $data, $mr, $valutaLookup, $DB;

  if ($rekValuta == "EUR" AND $belastingValuta == "EUR")
  {
    $mr["Valutakoers"] = 1;
    return "EUR";
  }

  if ($rekValuta == "EUR" AND $belastingValuta <> $rekValuta)
  {
    return $belastingValuta;
  }
  elseif ( ($rekValuta != "EUR" AND  $belastingValuta == "EUR"))
  {
    $data["factor"]    = $data["wisselkoers"];
    $mr["Valutakoers"] = $data["wisselkoers"];
    return $rekValuta;
  }
  return $belastingValuta;
}

function _valutakoers($rekValuta, $fondsValuta)
{
  global $fonds, $fData, $mr, $valutaLookup, $DB;

  if ($rekValuta == "EUR" AND $fondsValuta == "EUR")
  {
    return 1;
  }

  if ($rekValuta == "EUR" AND $fondsValuta <> $rekValuta)
  {
    return $fData["wisselkoers"];
  }
  elseif ( ($rekValuta != "EUR" AND  $fondsValuta == $rekValuta))
  {
    return $fData["wisselkoers"];
  }


  return 999;
}

function raboCheckRekening($rekeningNr="", $depot="RABO")
{
  global $row, $meldArray;
  $db = new DB();
  $rekParts = explode("-",$rekeningNr);
  if ( count($rekParts) > 1 )
  {
    $rekeningNr = $rekParts[1];
  }
  if (trim($rekeningNr) == "")
  {
    $meldArray[] = "Leeg rekeningnr, rekeninglookup afgebroken.";
    return false;
  }
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
//debug($query, $rekeningNr);
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
//    debug($query, $rekeningNr);
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


function raboCheckFonds($bankCode="")
{

  global $error, $fonds;

  $db = new DB();

  if ($bankCode <> "")
  {
    $query = "SELECT * FROM Fondsen WHERE raboCode = '".$bankCode."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec;
    }
  }

  return false;

}

function do_algemeen()
{
	global $mr, $data, $controleBedrag;

  $mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["settlementdatum"];
  $mr["Rekening"]          = $data["rekening"].$data["valuta"];
	$mr["bestand"]           = $data["file"];
	$mr["regelnr"]           = $data["row"];
  $mr["aktie"]             = $data["transactiecode"];
  $mr["bankTransactieId"]  = $data["transactieId"];
}


function _kostpost($gb, $bedrag, $valuta)
{
  global $mr, $output, $data;
  $mr["Grootboekrekening"] = $gb;
  $mr["Valuta"]            = $valuta;
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $valuta);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($bedrag < 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]            = 0;
    $mr["Credit"]           = abs($bedrag);
    $mr["Bedrag"]           = _creditbedrag();
  }

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  return $mr["Bedrag"];
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output;
//  debug($data);
//  debug($fonds);
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]             = "A";
	do_algemeen();
	$mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);
	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data["koersValuta"];
	$mr["Valutakoers"]       = $data["wisselkoers"];

	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data["aantal"];
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "RENME";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["divCoupBedrag"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  if ($data["interneKostenValuta"] == "EUR" AND $data["rekValuta"] != "EUR")
  {
    $mr["Valuta"]            = $data["rekValuta"];
    $mr["Debet"]             = abs($data["interneKosten"] / $data["wisselkoers"]);
    $mr["Valutakoers"]       = $data["wisselkoers"];
  }
  else
  {
    $mr["Valuta"]            = $data["interneKostenValuta"];
    $mr["Debet"]             = abs($data["interneKosten"]);
    $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  }
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";

  //$mr[Fonds]             = "";
  if ($data["externeKostenValuta"] == "EUR" AND $data["rekValuta"] != "EUR")
  {
    $mr["Valuta"]            = $data["rekValuta"];
    $mr["Debet"]             = abs($data["externeKosten"] / $data["wisselkoers"]);
    $mr["Valutakoers"]       = $data["wisselkoers"];
  }
  else
  {
    $mr["Valuta"]            = $data["externeKostenValuta"];
    $mr["Debet"]             = abs($data["externeKosten"]);
    $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  }
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen

  if ($data["belastingenValuta"] == "EUR" AND $data["rekValuta"] != "EUR")
  {
    $mr["Valuta"]            = $data["rekValuta"];
    $mr["Debet"]             = abs($data["belastingen"] / $data["wisselkoers"]);
    $mr["Valutakoers"]       = $data["wisselkoers"];
  }
  else
  {
    $mr["Valuta"]            = $data["belastingenValuta"];
    $mr["Debet"]             = abs($data["belastingen"]);
    $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  }
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
    
  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);
}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AO()  // Aankoop Openen
{

  global $fonds, $data, $mr, $output;
//  debug($data);
//  debug($fonds);
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "AO";
  do_algemeen();
  $mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $data["interneKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["interneKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $data["externeKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["externeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen
  $mr["Valuta"]            = $data["belastingenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["belastingen"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS()  // Aankoop sluiten
{

  global $fonds, $data, $mr, $output;
//  debug($data);
//  debug($fonds);
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "AS";
  do_algemeen();
  $mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $data["interneKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["interneKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $data["externeKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["externeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen
  $mr["Valuta"]            = $data["belastingenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["belastingen"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_V()  // Verkoop van stukken
{

  global $fonds, $data, $mr, $output;
//  debug($fonds);


  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "V";
  do_algemeen();

  $mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "RENOB";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["divCoupBedrag"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  if ($data["interneKostenValuta"] == "EUR" AND $data["rekValuta"] != "EUR")
  {
    $mr["Valuta"]            = $data["rekValuta"];
    $mr["Debet"]             = abs($data["interneKosten"] / $data["wisselkoers"]);
    $mr["Valutakoers"]       = $data["wisselkoers"];
  }
  else
  {
    $mr["Valuta"]            = $data["interneKostenValuta"];
    $mr["Debet"]             = abs($data["interneKosten"]);
    $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  }
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";

  //$mr[Fonds]             = "";
  if ($data["externeKostenValuta"] == "EUR" AND $data["rekValuta"] != "EUR")
  {
    $mr["Valuta"]            = $data["rekValuta"];
    $mr["Debet"]             = abs($data["externeKosten"] / $data["wisselkoers"]);
    $mr["Valutakoers"]       = $data["wisselkoers"];
  }
  else
  {
    $mr["Valuta"]            = $data["externeKostenValuta"];
    $mr["Debet"]             = abs($data["externeKosten"]);
    $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  }
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen

  if ($data["belastingenValuta"] == "EUR" AND $data["rekValuta"] != "EUR")
  {
    $mr["Valuta"]            = $data["rekValuta"];
    $mr["Debet"]             = abs($data["belastingen"] / $data["wisselkoers"]);
    $mr["Valutakoers"]       = $data["wisselkoers"];
  }
  else
  {
    $mr["Valuta"]            = $data["belastingenValuta"];
    $mr["Debet"]             = abs($data["belastingen"]);
    $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  }
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_VO()  // Verkoop openen
{

  global $fonds, $data, $mr, $output;
//  debug($fonds);


  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "VO";
  do_algemeen();
//  debug($data);
  $mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $data["interneKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["interneKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";

  //$mr[Fonds]             = "";
  $mr["Valuta"]            = $data["externeKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["externeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen
  $mr["Valuta"]            = $data["belastingenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["belastingen"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_VS()  // Verkoop sluiten
{

  global $fonds, $data, $mr, $output;
//  debug($fonds);


  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "VS";
  do_algemeen();
//  debug($data);
  $mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $data["interneKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["interneKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";

  //$mr[Fonds]             = "";
  $mr["Valuta"]            = $data["externeKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["externeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen
  $mr["Valuta"]            = $data["belastingenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["belastingen"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);
}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output, $afw, $globFonds, $globOmschrijving;

  $mr = array();
  $mr["aktie"]              = "DV";


  do_algemeen();

//  debug($data);

  $controleBedrag = 0;
  $mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $globOmschrijving        = $mr["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $data["divCoupValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $globFonds               = $mr["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["divCoupBedrag"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("DIV", $mr);
  $controleBedrag       += $mr["Bedrag"];

  $output[] = $mr;

  $data["factor"] = 1;
  $mr["Grootboekrekening"] = "DIVBE";       // boeking buitenlandse belastingen
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $data["belastingenValuta"]);
  $mr["Valuta"]            = belastingValuta($data["rekValuta"], $data["belastingenValuta"]);

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["belastingen"]) / $data["factor"];
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $data["interneKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["interneKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $data["externeKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["externeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

//  $mr["Grootboekrekening"] = "KOBU";       // boeking buitenlandse belastingen
//  $mr["Valuta"]            = $data["belastingenValuta"];
//  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]            = 0;
//  $mr["Debet"]             = abs($data["belastingen"]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }

  checkControleBedrag($controleBedrag, ($data["nettoBedrag"] ) );

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENOB()  //Coupon
{
  global $fonds, $data, $mr, $output, $afw;

  $mr = array();
  $mr["aktie"]              = "RENOB";

  do_algemeen();
  $controleBedrag = 0;
//  debug($fonds,"do_RENOB");
  $mr["Rekening"]          = raboCheckRekening($data["rekening"].$data["rekValuta"]);
//  debug($data,$mr["Rekening"]);
  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $data["divCoupValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["divCoupBedrag"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("RENOB", $mr);
  $controleBedrag       += $mr["Bedrag"];

  $output[] = $mr;


  $mr["Grootboekrekening"] = "DIVBE";       // boeking buitenlandse belastingen
  $mr["Valuta"]            = $data["belastingenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $data["belastingenValuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["belastingen"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Valuta"]            = $data["interneKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["interneKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $data["externeKostenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["rekValuta"], $mr["Valuta"]);
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["externeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


//  aetodo: 20200527 NVT moet nog geskipt worden
//  global $transactieCodes, $meldArray;
//
//  foreach ($data["kosten"] as $kostRec)
//  {
//    debug($transactieCodes["T"]);
//    if ($gb = $transactieCodes["T"][$kostRec["transactiecode"]])
//    {
//      $controleBedrag += _kostpost($gb,$kostRec["bedrag"],$kostRec["valuta"]);
//    }
//    else
//    {
//      $meldArray[] = $kostRec["row"]." kosten transcode onbekend: ".$kostRec["transactiecode"];
//    }
//
//  }

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENTE()
{
  global $data,$mr,$output, $transactieCodes, $afw;

  $mr = array();
  $mr["aktie"]              = "Mut.";
  $data["rekValuta"] = $data["valuta"];
  do_algemeen();
  $controleBedrag = 0;
  $mr["Rekening"]        = raboCheckRekening($data["rekening"].$data["valuta"]);
  $mr["Omschrijving"]      = $data["omschrijving"];

  $mr["Valuta"]            = $data["valuta"];    // dbs2778
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($data["bedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "RENTE";

    $mr["Debet"]             = abs($data["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $mr = $afw->reWrite("RENTE", $mr);

  }
  else
  {
    $mr["Grootboekrekening"] = "RENTE";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedrag"]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr = $afw->reWrite("RENTE", $mr);

  }

  $controleBedrag += $mr["Bedrag"];

  $output[] = $mr;

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_CA1()
{
  global $fonds, $data, $mr, $output;
//  debug($data);
//  debug($fData);
//  debug($fonds);
  $mr = array();
  $controleBedrag = 0;

  do_algemeen();

  if ($data["cashTransactie"])
  {
    if ($data["aantal"] > 0)
    {
      do_A();
    }
    else
    {
      do_V();
    }

  }
  else
  {
    do_STUKMUT();
  }

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_STUKMUT()  // mutatie van stukken
{

  global $fonds, $data, $mr, $output;
//  debug($data);
//  debug($fData);
//  debug($fonds);
  $mr = array();
  $controleBedrag = 0;
  $data["rekValuta"]         = "EUR";
  do_algemeen();
  $mr["Rekening"]          = raboCheckRekening($data["portefeuille"]."MEM");
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Aantal"]            = $data["aantal"];
  if ($data["aantal"] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["aktie"]             = "D";
    $mr["Transactietype"]    = "D";
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["aktie"]             = "L";
    $mr["Transactietype"]    = "L";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = _creditbedrag();
  }
  $controleBedrag         += $mr["Bedrag"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Transactietype"]    = "";
  $mr["Fonds"]             = "";

  if ($data["aantal"] > 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Credit"]            = $mr["Bedrag"];
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Bedrag"];
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = $mr["Bedrag"];
    $mr["Bedrag"]            = $mr["Bedrag"] * -1;
  }

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // rente boeken
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Transactietype"]    = "";

  if ($data["aantal"] > 0)
  {
    $mr["Grootboekrekening"] = "RENME";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data["divCoupBedrag"]);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Credit"]            = abs($data["divCoupBedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Transactietype"]    = "";
  $mr["Fonds"]             = "";

  if ($data["aantal"] > 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Credit"]            = $mr["Bedrag"];
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Bedrag"];
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = $mr["Bedrag"];
    $mr["Bedrag"]            = $mr["Bedrag"] * -1;
  }

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["stukmutNetto"] );
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUT0()  // mutatie van stukken
{

  global $fonds, $data, $mr, $output;
//  debug($data);
//  debug($fData);
//  debug($fonds);
  $mr = array();
  $controleBedrag = 0;
  $data["rekValuta"]         = "EUR";
  do_algemeen();
  $mr["Rekening"]          = raboCheckRekening($data["portefeuille"]."MEM");
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Fondskoers"]        = 0;
  $mr["Aantal"]            = $data["aantal"];
  if ($data["aantal"] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["aktie"]             = "D";
    $mr["Transactietype"]    = "D";
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["aktie"]             = "L";
    $mr["Transactietype"]    = "L";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = _creditbedrag();
  }
  $controleBedrag         += $mr["Bedrag"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Transactietype"]    = "";
  $mr["Fonds"]             = "";

  if ($data["aantal"] > 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Credit"]            = $mr["Bedrag"];
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Bedrag"];
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = $mr["Bedrag"];
    $mr["Bedrag"]            = $mr["Bedrag"] * -1;
  }

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // rente boeken
  $mr["Valuta"]            = $data["koersValuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Transactietype"]    = "";

  if ($data["aantal"] > 0)
  {
    $mr["Grootboekrekening"] = "RENME";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data["divCoupBedrag"]);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Credit"]            = abs($data["divCoupBedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Transactietype"]    = "";
  $mr["Fonds"]             = "";

  if ($data["aantal"] > 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Credit"]            = $mr["Bedrag"];
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Bedrag"];
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = $mr["Bedrag"];
    $mr["Bedrag"]            = $mr["Bedrag"] * -1;
  }

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, 0);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



function do_MUT()
{
	global $data,$mr,$output, $transactieCodes, $afw;

	$mr = array();
	$mr["aktie"]              = "Mut.";
	do_algemeen();
  $controleBedrag = 0;
  $data["rekValuta"] = $data["valuta"];
	$mr["Rekening"]        = raboCheckRekening($data["rekening"].$data["valuta"]);
	if (strtolower($mr["Omschrijving"]) == "contant dividend")
  {
    $mr["Omschrijving"]      = "Overboeking Contant dividend";
  }
	else
  {
    $mr["Omschrijving"]      = $data["omschrijving"];
  }

	$mr["Valuta"]            = $data["valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];

	$mr["Fonds"]             = "";
	$mr["Aantal"]            = '';
	$mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	if ($data["bedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";

    $mr["Debet"]             = abs($data["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $mr = $afw->reWrite("ONTTR", $mr);

  }
	else
  {
    $mr["Grootboekrekening"] = "STORT";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedrag"]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr = $afw->reWrite("STORT", $mr);

  }

	$controleBedrag += $mr["Bedrag"];

  $output[] = $mr;

	checkControleBedrag($controleBedrag, $data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KOBU()
{

  global $data,$mr,$output, $transactieCodes, $afw;


  $mr = array();
  $mr["aktie"]              = "Mut.";
  $data["rekValuta"] = $data["valuta"];
  do_algemeen();
  $controleBedrag = 0;

  $mr["Rekening"]        = raboCheckRekening($data["rekening"].$data["valuta"]);
  $mr["Omschrijving"]      = $data["omschrijving"];

  $mr["Valuta"]            = $data["valuta"];    // dbs2778
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $data["fonds"];
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($data["bedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "KOBU";

    $mr["Debet"]             = abs($data["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  else
  {
    $mr["Grootboekrekening"] = "KOBU";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedrag"]);
    $mr["Bedrag"]            = _creditbedrag();

  }
  $controleBedrag += $mr["Bedrag"];
  $mr = $afw->reWrite("KOBU", $mr);
  if($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KNBA()
{

  global $data,$mr,$output, $transactieCodes, $afw;


  $mr = array();
  $mr["aktie"]              = "Mut.";
  $data["rekValuta"] = $data["valuta"];
  do_algemeen();
  $controleBedrag = 0;

  $mr["Rekening"]        = raboCheckRekening($data["rekening"].$data["valuta"]);
  $mr["Omschrijving"]      = $data["omschrijving"];

  $mr["Valuta"]            = $data["valuta"];    // dbs2778
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = $data["fonds"];
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($data["bedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "KNBA";

    $mr["Debet"]             = abs($data["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  else
  {
    $mr["Grootboekrekening"] = "KNBA";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedrag"]);
    $mr["Bedrag"]            = _creditbedrag();

  }
  $controleBedrag += $mr["Bedrag"];
  $mr = $afw->reWrite("KNBA", $mr);
  if($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEH()
{

  global $data,$mr,$output, $transactieCodes, $afw;

  $mr = array();
  $mr["aktie"]              = "Mut.";
  $data["rekValuta"] = $data["valuta"];
  do_algemeen();
  $controleBedrag = 0;
  $mr["Rekening"]        = raboCheckRekening($data["rekening"].$data["valuta"]);
  $mr["Omschrijving"]      = $data["omschrijving"];

  $mr["Valuta"]            = $data["valuta"];    // dbs2778
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($data["bedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "BEH";

    $mr["Debet"]             = abs($data["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedrag"]);
    $mr["Bedrag"]            = _creditbedrag();

  }
  $mr = $afw->reWrite("BEH", $mr);
  $controleBedrag += $mr["Bedrag"];
  if($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEW()
{

  global $data,$mr,$output, $transactieCodes, $afw;

  $mr = array();
  $mr["aktie"]              = "Mut.";
  $data["rekValuta"] = $data["valuta"];
  do_algemeen();
  $controleBedrag = 0;
  $mr["Rekening"]        = raboCheckRekening($data["rekening"].$data["valuta"]);
  $mr["Omschrijving"]      = $data["omschrijving"];

  $mr["Valuta"]            = $data["valuta"];    // dbs2778
  $mr["Valutakoers"]       = $data["wisselkoers"];

  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($data["bedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "BEW";

    $mr["Debet"]             = abs($data["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  else
  {
    $mr["Grootboekrekening"] = "BEW";

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["bedrag"]);
    $mr["Bedrag"]            = _creditbedrag();

  }
  $controleBedrag += $mr["Bedrag"];
  $mr = $afw->reWrite("BEW", $mr);
  if($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag, $data["nettoBedrag"]);


}



function do_NVT()
{
  global $meldArray, $data;
//  $meldArray[] = "regel ".$data["regelnr"].":<b> met aktie NVT overgeslagen</b>";
}

function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}

