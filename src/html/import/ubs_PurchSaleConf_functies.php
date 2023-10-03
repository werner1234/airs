<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/05/16 13:35:41 $
 		File Versie					: $Revision: 1.4 $

 		$Log: ubs_PurchSaleConf_functies.php,v $
 		Revision 1.4  2018/05/16 13:35:41  cvs
 		call 6894
 		
 		naar RVV 20201104
 		


*/



function PS_debetbedrag()
{
	global $data, $mr;
  return -1 * $mr["Debet"];

}

function PS_creditbedrag()
{
	global $data, $mr;
  return $mr["Credit"];
}

function PS_do_K()
{
  global $fonds, $data, $mr, $output, $meldArray, $row;
  $mr["aktie"]             = "A";
//  debug($data, $row);
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "FONDS";
  //$mr["Valuta"]            = $data[12];
  $mr["Valuta"]            = $data[71];
  $ISIN = trim($data[27]);
  $UBSCode = _getUBScode($data);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"], $UBSCode))
  {
    return false;  // stop als fonds niet gevonden
  }


  $mr["Valutakoers"]       = _getValuta();


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Aantal"]            = $data[25];
  $mr["Fondskoers"]        = $data[11];
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Credit"]             = 0;

  $mr["Debet"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]           = PS_debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $kosten =  $data[90] + $data[96] ;
  if ($kosten <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Debet"]             = abs($kosten);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;


  $kobu =  $data[84] + $data[102] + $data[108] + $data[114] + $data[120] + $data[126] + $data[132];
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENME";
//  $mr["Valuta"]            = $data[115];
//  $mr["Valutakoers"]  1   = _getValuta();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $mr["Debet"]            = abs($data[78]);
  $mr["Credit"]           = 0;
  $mr["Bedrag"]            = PS_debetbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  addMeldarray(-1 * $controleBedrag, $mr["regelnr"],  $mr["Rekening"], $data[138]);
}
  
function PS_do_V()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "V";
  // debug($data);
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[12];
  $ISIN = trim($data[27]);

  $UBSCode = _getUBScode($data);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"], $UBSCode))
  {
    return false;  // stop als fonds niet gevonden 
  }
  

  $mr["Valutakoers"]       = _getValuta();

  
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Aantal"]            = -1 * $data[25];
  $mr["Fondskoers"]        = $data[11];
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  
  $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = PS_creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  
  $output[] = $mr;
  
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $kosten =  $data[90] + $data[96] ;
  if ($kosten <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Debet"]             = abs($kosten);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  
  $kobu =  $data[84] + $data[102] + $data[108] + $data[114] + $data[120] + $data[126] + $data[132];
  if ($kobu <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
	  $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $output[] = $mr;
	}
  
  $mr["Grootboekrekening"] = "RENOB";
//  $mr["Valuta"]            = $data[115];
//  $mr["Valutakoers"]     = _getValuta();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  
  $mr["Credit"]            = abs($data[78]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = PS_creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;
  
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[138]);
    
}

function PS_do_AS()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "AS";
  // debug($data);
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[12];
  $ISIN = trim($data[27]);
  $UBSCode = _getUBScode($data);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"], $UBSCode))
  {
    return false;  // stop als fonds niet gevonden
  }

  $mr["Valutakoers"]       = _getValuta();

  if ($data[20] == "OM")
  {
    $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = 0;
  }
  else
  {
    $mr["Omschrijving"]      = "Aankoop sluiten ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = $data[11];
  }

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[25];

  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Credit"]             = 0;

  $mr["Debet"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]           = PS_debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $kosten =  $data[90] + $data[96] ;
  if ($kosten <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Debet"]             = abs($kosten);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;


  $kobu =  $data[84] + $data[102] + $data[108] + $data[114] + $data[120] + $data[126] + $data[132];
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  addMeldarray(-1 * $controleBedrag, $mr["regelnr"],  $mr["Rekening"], $data[138]);

}

function PS_do_VS()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "VS";
  // debug($data);
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[12];
  $ISIN = trim($data[27]);
  $UBSCode = _getUBScode($data);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"], $UBSCode))
  {
    return false;  // stop als fonds niet gevonden
  }


  $mr["Valutakoers"]       = _getValuta();

  if ($data[20] == "OL")
  {
    $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = 0;
  }
  else
  {
    $mr["Omschrijving"]      = "Verkoop sluiten ".$fonds["Omschrijving"];
    $mr["Fondskoers"]        = $data[11];
  }

  $mr["Fonds"]             = $fonds["Fonds"];

  $mr["Aantal"]            = -1 * $data[25];
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;

  $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = PS_creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $kosten =  $data[90] + $data[96] ;
  if ($kosten <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Debet"]             = abs($kosten);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;


  $kobu =  $data[84] + $data[102] + $data[108] + $data[114] + $data[120] + $data[126] + $data[132];
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }



  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[138]);

}

function PS_do_AO()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "AO";
  // debug($data);
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[12];
  $ISIN = trim($data[27]);
  $UBSCode = _getUBScode($data);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"], $UBSCode))
  {
    return false;  // stop als fonds niet gevonden
  }


  $mr["Valutakoers"]       = _getValuta();


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Aankoop openen ".$fonds["Omschrijving"];
  $mr["Aantal"]            = $data[25];
  $mr["Fondskoers"]        = $data[11];
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Credit"]             = 0;

  $mr["Debet"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]           = PS_debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $kosten =  $data[90] + $data[96] ;
  if ($kosten <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Debet"]             = abs($kosten);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;


  $kobu =  $data[84] + $data[102] + $data[108] + $data[114] + $data[120] + $data[126] + $data[132];
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  addMeldarray(-1 * $controleBedrag, $mr["regelnr"],  $mr["Rekening"], $data[138]);

}

function PS_do_VO()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "VO";
  // debug($data);
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[12];
  $ISIN = trim($data[27]);
  $UBSCode = _getUBScode($data);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"], $UBSCode))
  {
    return false;  // stop als fonds niet gevonden
  }


  $mr["Valutakoers"]       = _getValuta();


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Verkoop openen ".$fonds["Omschrijving"];
  $mr["Aantal"]            = -1 * $data[25];
  $mr["Fondskoers"]        = $data[11];
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;

  $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = PS_creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $kosten =  $data[90] + $data[96] ;
  if ($kosten <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    $mr["Debet"]             = abs($kosten);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }

  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;


  $kobu =  $data[84] + $data[102] + $data[108] + $data[114] + $data[120] + $data[126] + $data[132];
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $output[] = $mr;
  }



  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[138]);
}
