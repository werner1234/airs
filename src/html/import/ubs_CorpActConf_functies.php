<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/11/28 11:00:15 $
 		File Versie					: $Revision: 1.6 $

 		$Log: ubs_CorpActConf_functies.php,v $
 		Revision 1.6  2018/11/28 11:00:15  cvs
 		call 7366
 		
 		naar RVV 20201104
 		

*/
function CA_debetbedrag()
{
	global $data, $mr;
  if (($data[433] == "EUR" AND $data[439] <> "EUR"))
  {
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else
  {
    if ($data[433] == $data[439])
    {
      return -1 * $mr["Debet"];
    }
    else
    {
      return 999;
    }

  }  
}

function CA_creditbedrag()
{
	global $data, $mr;
  if (($data[433] == "EUR" AND $data[439] <> "EUR"))
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    if ($data[433] == $data[439])
    {
      return $mr["Credit"];
    }
    else
    {
      return 999;
    }
  }
	 
}



function CA_bepaalValutakoers()
{
  global $data, $mr;
  $valutaKoers  = 0;
  
  if ($data[433] == "EUR" AND $data[439] == "EUR" )
  {
    $valutaKoers = 1;
  }
  elseif ($data[433] == "EUR" AND $data[439] <> "EUR")
  {
    $valutaKoers       = $data[434]/$data[440];
  }
  elseif ($data[433] <> "EUR" AND $data[439] == $data[433] )
  {
    $db = new DB();
    $query = "SELECT Koers FROM Valutakoersen WHERE Valuta = '".$data[439]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    if ($out = $db->lookupRecordByQuery($query))
    {
      $valutaKoers = $out["Koers"];
    }
    else
    {
      $valutaKoers = 99;
    }

  }
  return $valutaKoers;
  
}


// gebaseerd op stroeve do_dv()
function CA_do_DV($type)
{ 
  
  global $fonds, $data, $mr, $output, $meldArray;

  if (!UBS_checkRekeningNr() )
  {
    return;
  }

  $mr["aktie"]             = $type;
  $controleBedrag = 0;
  
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $data[439];
  
  $ISIN = trim($data[20]);
  if (!$fonds = _getFonds($ISIN, $data[439]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"]."";
  
  switch ($type)
  {
    case "DV":
      //$mr["Valutakoers"]       = _getValuta();
      $mr["Valutakoers"]       = CA_bepaalValutakoers();
      break;
//    case "DRIP":
//      $mr["Valutakoers"]       = CA_bepaalValutakoers();
//      break;
    default:
  }    
  
  
  
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $brutoDividend = $data[440];
  if ($brutoDividend < 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr["Debet"]             = abs($brutoDividend);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = CA_debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($brutoDividend);
	  $mr["Bedrag"]            = CA_creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
  
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  
  
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";

  $divbe = $data[443];
  $divVal = $data[442];

  if ($divbe <> 0)
  {
    $mr["Grootboekrekening"] = "DIVBE";
    $mr["Valuta"]            = $divVal;
    //$mr["Valutakoers"]       = _getValuta();
    if ($divbe > 0)
    {
      $mr["Debet"]          = abs($divbe);
      $mr["Credit"]         = 0;
      $mr["Bedrag"]         = CA_debetbedrag();
    }
    else
    {
      $mr["Credit"]         = abs($divbe);
      $mr["Debet"]          = 0;
      $mr["Bedrag"]         = CA_creditbedrag();
    }
    
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }
  
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[434]);
    
  }
  
function CA_do_R()
{
  global $fonds, $data, $mr, $output, $meldArray;
  if (!UBS_checkRekeningNr() )
  {
    return;
  }

  
  $mr["aktie"]    = $data[4];
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $data[439];

  $ISIN = trim($data[20]);
  if (!$fonds = _getFonds($ISIN, $data[439]))
  {
    return false;  // stop als fonds niet gevonden
  }

  $mr["Fonds"]             = $fonds["Fonds"];
  if ($data[4] == "PRED")
  {
    $mr["Omschrijving"]      = "Uitkering ".$fonds["Omschrijving"]."";

  }
  else
  {
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"]."";
  }

  $mr["Valutakoers"]       = CA_bepaalValutakoers();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $brutoDividend = $data[440];
  if ($brutoDividend < 0)  // als veld negatief betreft een correctie Dividend
  {
    $mr["Debet"]             = abs($brutoDividend);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = CA_debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($brutoDividend);
    $mr["Bedrag"]            = CA_creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";

  $divbe = $data[443];
  $divVal = $data[442];

  if ($divbe <> 0)
  {
    $mr["Grootboekrekening"] = "DIVBE";
    $mr["Valuta"]            = $divVal;
    //$mr["Valutakoers"]       = _getValuta();
    if ($divbe > 0)
    {
      $mr["Debet"]          = abs($divbe);
      $mr["Credit"]         = 0;
      $mr["Bedrag"]         = CA_debetbedrag();
    }
    else
    {
      $mr["Credit"]         = abs($divbe);
      $mr["Debet"]          = 0;
      $mr["Bedrag"]         = CA_creditbedrag();
    }

    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[434]);

}


function CA_do_D()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  
  $mr["aktie"]             = "D";
  $controleBedrag = 0;
  $mr["Rekening"]          = $data[14]."MEM";
  $mr["Valuta"]            = "MEM";
  $ISIN = trim($data[37]);
  if (!$fonds = _getFonds($ISIN, "MEM",$data[21]))
  {
    return false;  // stop als fonds niet gevonden 
  }
	$mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Aantal"]            = $data[317];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Valuta"]            = $fonds["Valuta"];
	$mr["Grootboekrekening"] = "FONDS";
  
  if ($mr["Valuta"] == "EUR")
  {
    $mr["Valutakoers"] = 1;
  }
  else
  {
    $mr["Valutakoers"] = _getValuta();
  }
	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = 0;
  

	$mr["Transactietype"]    = "D";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 1;

	$output[] = $mr;

}


function CA_do_CONV()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  
  $mr["aktie"]             = "D";
  $controleBedrag = 0;
  
  if ($data[36] == "DEBT")    // eerste fondes wordt gelicht
  {
    $mr["Valuta"]            = $data[44];
    $ISIN = trim($data[37]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = -1 * $data[46];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";
  
    if ($data[44] == "EUR")
    {
      $mr["Valutakoers"] = 1;
    }
    else
    {
      $mr["Valutakoers"] = _getValuta();
    }
    
    
    $mr["Fondskoers"] = CS_getFondskoers($fonds["Fonds"]);
    
    
    //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
    $controleBedrag       += $mr["Bedrag"];
    
    $tussenBedrag = $mr["Bedrag"];
    
    $mr["Transactietype"]    = "L";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;

    $output[] = $mr;
    // blok 2 start

    $mr["Valuta"]            = $data[61];
    $ISIN = trim($data[54]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = $data[63];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";

    if ($data[61] == "EUR")
    {
      $mr["Valutakoers"] = 1;
    }
    else
    {
      $mr["Valutakoers"] = _getValuta();
    }
    
    $mr["Fondskoers"] = $tussenBedrag / $mr["Valutakoers"] / $mr["Aantal"];
    
    
    $mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
    $controleBedrag       += $mr["Bedrag"];

    $mr["Transactietype"]    = "D";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;

    $output[] = $mr;
  }
  else  // tweede fondes wordt gelicht
  {
    $mr["Valuta"]            = $data[61];
    $ISIN = trim($data[54]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = $data[63] * -1;
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";

    if ($data[61] == "EUR")
    {
      $mr["Valutakoers"] = 1;
    }
    else
    {
      $mr["Valutakoers"] = _getValuta();
    }

    //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Debet"]             = 0;
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = 0;
    $controleBedrag       += $mr["Bedrag"];

    $mr["Transactietype"]    = "L";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;

    $output[] = $mr;
  //blok 2 start
    
    $mr["Valuta"]            = $data[44];
    $ISIN = trim($data[37]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = $data[46];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";
  
    if ($data[44] == "EUR")
    {
      $mr["Valutakoers"] = 1;
    }
    else
    {
      $mr["Valutakoers"] = _getValuta();
    }

    //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Debet"]             = 0;
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = 0;
    $controleBedrag       += $mr["Bedrag"];

    $mr["Transactietype"]    = "D";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;

    $output[] = $mr;

  }
    
}

function CA_do_V($type)
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  if (!UBS_checkRekeningNr() )
  {
    continue;
  }
  $mr["aktie"]             = $type;
  $controleBedrag = 0;
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[31];
  $ISIN = trim($data[20]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  
  
  $mr["Valutakoers"]       = CA_bepaalValutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data[49];
  
  switch ($type)
  {
    case "REDM":
      $mr["Fondskoers"]        = $data[225];
      $mr["Omschrijving"]      = "Lossing ".$fonds["Omschrijving"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[49]*$data[225]*$fonds["Fondseenheid"]);
      $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];;
      break;
//    case "LIQU";
//      $mr["Fondskoers"]        = ($data[74]/$data[46]) / $fonds["Fondseenheid"];
//      $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
//      $mr["Debet"]             = 0;
//      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
//      $mr["Bedrag"]            = CA_creditbedrag();
//      break;

    default:
  }    
  
  
   

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  
  $output[] = $mr;
  
  
  $bankControle = $data[434];
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $bankControle);
    
}

function CA_do_A($type="A")
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = $type;
  $controleBedrag = 0;
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[61];
  $ISIN = trim($data[54]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  
  
  $mr["Valutakoers"] = CA_bepaalValutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[63];
  
  $mr["Fondskoers"]        = ($data[98]/$data[63]) / $fonds["Fondseenheid"];
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = CA_debetbedrag();

  
   

  $controleBedrag       += ($mr["Bedrag"] * -1);
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  
  $output[] = $mr;
  
  $kosten = $data[104];
  //$mr["Valuta"]            = $data[61];
  if ($kosten <> 0)
  {
     $mr["Credit"]            = 0;
    $mr["Fondskoers"]         = 0;
    $mr["Aantal"]             = 0;
    $mr["Grootboekrekening"] = "KOST";
    $mr["Debet"]             = abs($kosten);
    $mr["Bedrag"]            = CA_debetbedrag();
    $controleBedrag  += (-1 * $mr["Bedrag"]);
    $output[] = $mr;
  }
  
  
  
  $bankControle = $data[98]+$data[104];
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $bankControle);
    
}



function CA_do_L()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  
  $mr["aktie"]             = "L";
  $controleBedrag = 0;
  $mr["Valuta"]            = $data[44];
  $ISIN = trim($data[37]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
	$mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Fonds"]             = $fonds["Fonds"];
	$mr["Grootboekrekening"] = "FONDS";
  
  if ($data[44] == "EUR")
  {
    $mr["Valutakoers"] = 1;
  }
  else
  {
    $mr["Valutakoers"] = _getValuta();
  }
  
	$mr["Aantal"]            = -1 * $data[46];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = 0;
	$mr["Transactietype"]    = "L";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 1;

	$output[] = $mr;

    
}



// nieuw per 27-3

function CA_do_DRCA()  //verrekening fracties
{
	global $fonds, $data, $mr, $output, $meldArray;

	$mr["aktie"]              = "DRCA";
	
  $mr["Valuta"]            = $data[103];
	$ISIN = trim($data[16]);
  if (!$fonds = _getFonds($ISIN, $data[23]) )
  {
    return false;  // stop als fonds niet gevonden 
  }
	
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Verrekening inz. ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "VKSTO";
  
	$mr["Valutakoers"] = CA_bepaalValutakoers();
	
  $mr["Fondskoers"]        = 0;
  $mr["Aantal"]            = 0;;
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($data[104]);
	$mr["Bedrag"]            = CA_creditbedrag();
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  
	$output[] = $mr;
  
}


