<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/17 07:12:49 $
 		File Versie					: $Revision: 1.15 $
naar RVV 20201202
 		$Log: credswiss_CorpActConf_functies.php,v $
 		Revision 1.15  2020/06/17 07:12:49  cvs
 		call 8671
 		
 		Revision 1.14  2020/06/10 12:58:34  cvs
 		call 8671
 		
 		Revision 1.13  2020/01/15 14:59:09  cvs
 		call 8298
 		
 		Revision 1.12  2018/09/11 13:48:50  cvs
 		call 3775
 		
 		Revision 1.11  2018/01/12 15:32:13  cvs
 		call 6502
 		
 		Revision 1.10  2015/10/02 13:49:06  cvs
 		*** empty log message ***
 		
 		Revision 1.9  2015/06/11 16:13:12  cvs
 		*** empty log message ***
 		
 		Revision 1.8  2015/05/11 13:36:52  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2015/05/06 09:42:56  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/03/26 09:48:19  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/01/05 14:45:36  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/20 12:48:18  cvs
 		dbs 2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/
function CA_debetbedrag()
{
	global $data, $mr;
  if (($data[113] == "EUR" AND $data[106] <> "EUR"))
  {
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else
  {
    return -1 * $mr["Debet"];
  }  
}

function CA_creditbedrag()
{
	global $data, $mr;
  if (($data[113] == "EUR" AND $data[106] <> "EUR"))
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    return $mr["Credit"];
  }
	 
}



function CA_bepaalValutakoers()
{
  global $data;
  $valutaKoers  = 0;
  
  if ($data[113] == "EUR" AND ($data[107] == "EUR" OR $data[107] == "" ) )
  {
    $valutaKoers = 1;
  }
  elseif ($data[113] == "EUR" AND $data[87] <> "EUR")
  {
    $valutaKoers       = $data[86]/$data[88];
  }
  elseif ($data[113] <> "EUR" AND $data[107] == $data[113] )
  {
    $valutaKoers = _getValuta();
  }
  return $valutaKoers;
  
}


// gebaseerd op stroeve do_dv()
function CA_do_DV($type)
{ 
  
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = $type;
  $controleBedrag = 0;
  
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $data[107];
  
  $ISIN = trim($data[17]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"]."";
  
  switch ($type)
  {
    case "CAPD":
      $mr["Omschrijving"]      = "Terugbetaling kap. ".$fonds["Omschrijving"]."";
      $mr["Valutakoers"]       = CA_bepaalValutakoers();
      break;
    case "DV":
    case "DRIP":
      $mr["Valutakoers"]       = CA_bepaalValutakoers();
      break;
    default:
  }    
  
  
  
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $brutoDividend = $data[108];
  if ($brutoDividend < 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr[Debet]             = abs($brutoDividend);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = CA_debetbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
	else
	{
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($brutoDividend);
	  $mr[Bedrag]            = CA_creditbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
  
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  
  
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  //debug($data);
  if ($data[122] <> 0)
  {
    $divbe = $data[122];
    $divVal = $data[121];
  }
  else
  {
    if ($data[124] <> 0)
    {
      $divbe = $data[124];
      $divVal = $data[123];
    }
    else
    {
      $divbe = $data[126];
      $divVal = $data[125];
    }
  }
  
  
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
  
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[114]);
    
  }


function CA_do_R()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "R";
  $controleBedrag = 0;
  
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $data[24];
  $ISIN = trim($data[17]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  
  
   $mr["Valutakoers"] = CA_bepaalValutakoers();
  
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"]."";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $brutoRente = $data[108];
  if ($brutoRente < 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr[Debet]             = abs($brutoRente);
	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = CA_debetbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
	else
	{
    $mr[Debet]             = 0;
	  $mr[Credit]            = abs($brutoRente);
	  $mr[Bedrag]            = CA_creditbedrag();
    $controleBedrag       += $mr[Bedrag];

	}
  
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";

  if ($data[122] <> 0)
  {
    $divbe = $data[122];
    $divVal = $data[121];
  }
  else
  {
    if ($data[124] <> 0)
    {
      $divbe = $data[124];
      $divVal = $data[123];
    }
    else
    {
      $divbe = $data[126];
      $divVal = $data[125];
    }
  }



  if ($divbe <> 0)
  {
    $mr["Grootboekrekening"] = "DIVBE";
    $mr["Valuta"]            = $divVal;
    //$mr["Valutakoers"]       = _getValuta();
    if ($divbe < 0)
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
    
    $controleBedrag  -= $mr["Bedrag"];
    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[114]);
   
}


function CA_do_D()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  
  $mr["aktie"]             = "D";
  $controleBedrag = 0;
   $mr["Valuta"]            = $data[47];
  $ISIN = trim($data[40]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
	$mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Aantal"]            = $data[49];
  $mr["Fonds"]             = $fonds["Fonds"];
	$mr["Grootboekrekening"] = "FONDS";
  
  if ($data[47] == "EUR")
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
  
  if ($data[37] == "DEBT")    // eerste fondes wordt gelicht
  {
    $mr["Valuta"]            = $data[47];
    $ISIN = trim($data[40]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = -1 * $data[49];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";
  
    if ($data[47] == "EUR")
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

    $mr["Valuta"]            = $data[66];
    $ISIN = trim($data[59]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = $data[68];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";

    if ($data[66] == "EUR")
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
    $mr["Valuta"]            = $data[66];
    $ISIN = trim($data[59]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = $data[68] * -1;
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";

    if ($data[66] == "EUR")
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
    
    $mr["Valuta"]            = $data[47];
    $ISIN = trim($data[40]);
    if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
    {
      return false;  // stop als fonds niet gevonden 
    }
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Aantal"]            = $data[49];
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Grootboekrekening"] = "FONDS";
  
    if ($data[47] == "EUR")
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
  $mr["aktie"]             = $type;
  $controleBedrag = 0;
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[47];
  $ISIN = trim($data[40]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  
  
  $mr["Valutakoers"] = CA_bepaalValutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data[49];
  
  switch ($type)
  {
    case "REDM":
      $mr["Fondskoers"]        = 100;
      $mr["Omschrijving"]      = "Lossing ".$fonds["Omschrijving"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[49]);
      $mr["Bedrag"]            = CA_creditbedrag();
     break;
    case "LIQU";
      $mr["Fondskoers"]        = ($data[84]/$data[49]) / $fonds["Fondseenheid"];
      $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            = CA_creditbedrag();
      break;
    case "BIDS";
      $mr["Fondskoers"]        = ($data[84]/$data[49]) / $fonds["Fondseenheid"];
      $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            = CA_creditbedrag();
      break;
    default:
  }    
  
  
   

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  
  $output[] = $mr;
  
  
  $bankControle = ($data[114]<>0)?$data[114]:$data[108];
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $bankControle);
    
}

function CA_do_BUY($type="BUY")
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = $type;
  $controleBedrag = 0;
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[47];
  $ISIN = trim($data[40]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  
  
  $mr["Valutakoers"] = CA_bepaalValutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[49];
  
  $mr["Fondskoers"]        = ($data[108]/$data[49]) / $fonds["Fondseenheid"];
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = CA_debetbedrag();

  
   

  $controleBedrag       += ($mr["Bedrag"] * -1);
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  
  $output[] = $mr;
  
  $kosten = $data[152];
  //$mr["Valuta"]            = $data[65];
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
  
  
  
  $bankControle = $data[114];
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $bankControle);
    
}

function CA_do_A($type="A")
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = $type;
  $controleBedrag = 0;
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[66];
  $ISIN = trim($data[59]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden
  }



  $mr["Valutakoers"] = CA_bepaalValutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[68];

  $mr["Fondskoers"]        = ($data[108]/$data[68]) / $fonds["Fondseenheid"];
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = CA_debetbedrag();




  $controleBedrag       += ($mr["Bedrag"] * -1);
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $kosten = $data[114];
  //$mr["Valuta"]            = $data[65];
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



  $bankControle = $data[108]+$data[114];
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $bankControle);

}



function CA_do_L()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  
  $mr["aktie"]             = "L";
  $controleBedrag = 0;
  $mr["Valuta"]            = $data[47];
  $ISIN = trim($data[40]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
	$mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Fonds"]             = $fonds["Fonds"];
	$mr["Grootboekrekening"] = "FONDS";
  
  if ($data[47] == "EUR")
  {
    $mr["Valutakoers"] = 1;
  }
  else
  {
    $mr["Valutakoers"] = _getValuta();
  }
  
	$mr["Aantal"]            = -1 * $data[49];
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
	
  $mr["Valuta"]            = $data[113];
	$ISIN = trim($data[17]);
  if (!$fonds = _getFonds($ISIN, $data[24]) )
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
	$mr["Credit"]            = abs($data[114]);
	$mr["Bedrag"]            = CA_creditbedrag();
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  
	$output[] = $mr;
  
}


