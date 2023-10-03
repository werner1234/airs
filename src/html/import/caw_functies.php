<?
/*
    AE-ICT sourcemodule created 01 nov. 2019
    Author              : Chris van Santen
    Filename            : caw_functies.php

*/


function cawNumber($in)
{
 // $in = str_replace(".", "", $in);
  return str_replace(",", ".", $in);
}

function cawDate($in)
{
  $parts = explode(".", $in);
  return $parts[2]."-".$parts[1]."-".$parts[0];
}

function getFonds()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();

  $fonds = array();
  if (trim($data["bankCode"]) != ""  )
  {

    $query = "SELECT * FROM Fondsen WHERE CAWcode = '".trim($data["bankCode"])."' ";

    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $error[] = "$row: fonds CAWcode ".$data["bankCode"]." niet gevonden ";
    }
  }
  else
  {
    $ISIN = trim($data["isin"]);
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$ISIN' AND Valuta ='".$data["fondsValuta"]."' ";

    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $error[] = "$row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
    }
  }


}

function getFondsDiv()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();



  $fonds = array();

  $ISIN = trim($data["isin"]);


  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$ISIN'  ";

    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
  }
  $error[] = "$row: DIV fonds $ISIN niet gevonden ";

}

function getRekening()
{
  global $data, $error, $row;

	$depot = "CAW";
  $db = new DB();
  $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);

  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return array("rekening" => $rec["Rekening"],
                 "valuta"   => $rec["Valuta"]);
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      //$error[] = $mr["regelnr"]." Rekening niet gevonden ($rekeningNr)";
      return false;
    }
  }
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "CAW|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr;

	if ($data["afrekenValuta"] == $mr["Valuta"] )
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr;

  if ($data["afrekenValuta"] == $mr["Valuta"] )
	  return $mr["Credit"];
	else
	  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers($rekValuta)
{
  global $data, $mr, $valutaLookup;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if ($rekValuta == "EUR" AND $mr["Valuta"] != "EUR")
  {
    return $data["valutakoers"];
  }

  if ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta)
  {
      return 1/$data["valutakoers"];
  }
  else
  {
    return 9999;
  }

}

function checkVoorDubbelInRM($mr)
{
  global $meldArray;
  return false;
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
	$mr["bankTransactieId"]  = $data["transactieId"];
	$mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["settledatum"];

	if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec;
  }


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

function do_SA()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "A/S";
  do_algemeen();

  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  if (strtolower(substr($data[57],0,17)) == "uitboeking opties")
  {
    $mr["Omschrijving"]      = "Sluiten ".$fonds["Omschrijving"];
  }

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kost"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kobu"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SV()  // Vernkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V/S";
  do_algemeen();

  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  if (strtolower(substr($data[57],0,17)) == "uitboeking opties")
  {
    $mr["Omschrijving"]      = "Sluiten ".$fonds["Omschrijving"];
  }


  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
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
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kost"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kobu"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,abs($data["nettoBedrag"]));
}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  
  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = $data["valutakoers"];
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data["aantal"];
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data["kost"]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data["kobu"]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Vernkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  checkVoorDubbelInRM($mr);


  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
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
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]            = 0;
  $mr["Credit"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kost"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kobu"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,abs($data["nettoBedrag"]));
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_MUT()  // geld/stukken
{
  global $data;

  if ($data["aantal"] != 0 )
  {
    do_STUKMUT();
  }
  else
  {
    do_GELDMUT();
  }
}




/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray;

	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "DIV";
	do_algemeen();
  getFondsDiv($data["isin"]);
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";


	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = $data["valutakoers3"];



	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;

  $mr["Debet"]             = 0;
  if ($data["aantal"] == 0)
  {
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Valutakoers"]       = $data["valutakoersAccount"];
    $mr["Valuta"]            = $data["afrekenValuta"];
  }
  else
  {
    $mr["Credit"]            = abs($data["aantal"] * $data["koers"]);
  }

  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;



	$mr["Grootboekrekening"] = "DIVBE";

	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;


  $mr["Debet"]             = abs($data["tax"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "ROER";

  if (substr($mr["Rekening"],-3) == 'EUR')
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }

	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;


  $mr["Debet"]             = abs($data["roer"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KO()
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = $data["transactieCode"];
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[57];

  if (strtolower(substr($data[57],0,10)) == "beheerloon" )
  {
    $mr["Grootboekrekening"] = "BEH";
  }
  else
  {
    $mr["Grootboekrekening"] = "KNBA";
  }



  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_KOBU()
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = $data["transactieCode"];
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[57];
  $mr["Grootboekrekening"] = "KOBU";
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

function do_BEH()
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = $data["transactieCode"];
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[57];
  $mr["Grootboekrekening"] = "BEH";
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_DIVBE()
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "DIVBE";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[57];
  $mr["Grootboekrekening"] = "DIVBE";
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_KNBA()
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = $data["transactieCode"];
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[57];
  $mr["Grootboekrekening"] = "KNBA";
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = $data["transactieCode"];
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoersFondsEur"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = "Valutatransactie";
  $mr["Grootboekrekening"] = "KRUIS";
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $afw;

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Omschrijving"]      = "Opname";
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Omschrijving"]      = "Storting";
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("GELDMUT",$mr);
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

    checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_STUKMUT()  //mutatie stukken
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  checkVoorDubbelInRM($mr);

/////////////////////

    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = $data["valutakoers"];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data["aantal"];
    $mr["Fondskoers"]        = $data["koers"];
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;
    $mr["Rekening"]          = trim($data["rekening"])."MEM";
    $mr["Grootboekrekening"] = "FONDS";

    $totaal                  = 0;
    if (  $data["transactieCode"] == "ST" )
    {
      $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];

      $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
      $controleBedrag         += $mr["Bedrag"];

      $mr["Transactietype"]    = "D";
      $totaal                  = $mr["Bedrag"];
      $output[] = $mr;



      $mr["Grootboekrekening"] = "RENME";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($data["opgelopenRente"]);
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag         += $mr["Bedrag"];

      $mr["Transactietype"]    = "";

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }

      $totaal += $mr["Bedrag"];

      if ($totaal <> 0)
      {
        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
        $mr["Grootboekrekening"] = "STORT";
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($totaal);
        $mr["Bedrag"]            = abs($totaal);
        $controleBedrag         += $mr["Bedrag"];
        $mr["Transactietype"]    = "";
        $output[] = $mr;
      }



    }
    else
    {

      $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];

      $mr["Aantal"]            = $data["aantal"] * -1;
      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
      $controleBedrag         += $mr["Bedrag"];

      $mr["Transactietype"]    = "L";
      $totaal                  = $mr["Bedrag"];
      $output[] = $mr;

      $mr["Grootboekrekening"] = "RENOB";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["opgelopenRente"]);
      $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

      $mr["Transactietype"]    = "";
      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }


      $totaal += $mr["Bedrag"];

      if ($totaal <> 0)
      {
        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Credit"]            = 0;
        $mr["Debet"]             =  abs($totaal);
        $mr["Bedrag"]            = -1 * ($totaal);
        $controleBedrag         += $mr["Bedrag"];
        $mr["Transactietype"]    = "";
        $output[] = $mr;
      }
    }

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_D()  //deponering stukken
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "D";
  do_algemeen();
  checkVoorDubbelInRM($mr);

/////////////////////

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;
  $mr["Rekening"]          = trim($data["rekening"])."MEM";
  $mr["Grootboekrekening"] = "FONDS";

  $totaal                  = 0;

  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];

  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "D";
  $totaal                  = $mr["Bedrag"];
  $output[] = $mr;



  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $totaal += $mr["Bedrag"];

  if ($totaal <> 0)
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($totaal);
    $mr["Bedrag"]            = abs($totaal);
    $controleBedrag         += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_Dnul()  //deponering stukken
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "D";
  do_algemeen();
  checkVoorDubbelInRM($mr);

/////////////////////

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;
  $mr["Rekening"]          = trim($data["rekening"])."MEM";
  $mr["Grootboekrekening"] = "FONDS";

  $totaal                  = 0;

  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];

  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "D";
  $totaal                  = $mr["Bedrag"];
  $output[] = $mr;



  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $totaal += $mr["Bedrag"];

  if ($totaal <> 0)
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($totaal);
    $mr["Bedrag"]            = abs($totaal);
    $controleBedrag         += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_L()  //lichting stukken
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "L";
  do_algemeen();
  checkVoorDubbelInRM($mr);

/////////////////////

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;
  $mr["Rekening"]          = trim($data["rekening"])."MEM";
  $mr["Grootboekrekening"] = "FONDS";

  $totaal                  = 0;


  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];

  $mr["Aantal"]            = $data["aantal"] * -1;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "L";
  $totaal                  = $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }


  $totaal += $mr["Bedrag"];

  if ($totaal <> 0)
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             =  abs($totaal);
    $mr["Bedrag"]            = -1 * ($totaal);
    $controleBedrag         += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_Lnul()  //lichting stukken
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "L";
  do_algemeen();
  checkVoorDubbelInRM($mr);

/////////////////////

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;
  $mr["Rekening"]          = trim($data["rekening"])."MEM";
  $mr["Grootboekrekening"] = "FONDS";

  $totaal                  = 0;


  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];

  $mr["Aantal"]            = $data["aantal"] * -1;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "L";
  $totaal                  = $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }


  $totaal += $mr["Bedrag"];

  if ($totaal <> 0)
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             =  abs($totaal);
    $mr["Bedrag"]            = -1 * ($totaal);
    $controleBedrag         += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



function do_R()
{
  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = $data["transactieCode"];
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = $data["valutakoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[57];
  $mr["Grootboekrekening"] = "RENTE";
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NVT()
{
  return true;
}

function do_error()
{
	global $do_func,$transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


?>