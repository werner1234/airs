<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/24 09:38:32 $
 		File Versie					: $Revision: 1.11 $

naar RVV 20201201
 		$Log: credswiss_PurchSaleConf_functies.php,v $
 		Revision 1.11  2020/06/24 09:38:32  cvs
 		call 8711
 		
 		Revision 1.10  2015/12/01 07:28:25  cvs
 		update 2540, call 4352
 		
 		Revision 1.9  2015/10/02 13:49:06  cvs
 		*** empty log message ***
 		
 		Revision 1.8  2015/05/06 09:40:50  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2015/03/26 09:48:19  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/01/05 14:45:36  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/20 12:48:18  cvs
 		dbs 2746
 		
 		Revision 1.2  2014/11/13 10:43:10  cvs
 		dbs2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/



function PS_debetbedrag()
{
	global $data, $mr;
  if ($mr["Valuta"] <> "EUR" AND $data["AirsRekValuta"] == "EUR" )
  {
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else
  {
    return -1 * $mr["Debet"];
  }  
}

function PS_creditbedrag()
{
	global $data, $mr;
  if ($mr["Valuta"] <> "EUR" AND $data["AirsRekValuta"] == "EUR" )
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    return $mr["Credit"];
  }
	 
}

function PS_do_K()
{ 
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $mr["aktie"]             = "K";
  $controleBedrag = 0;
 
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = ($data[31] <> "")?$data[31]:$data[77];
  $ISIN = trim($data[68]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]) )
  {
    return false;  // stop als fonds niet gevonden 
  }
  

  
  if ($data[128] == "EUR" AND $data["AirsRekValuta"] == "EUR" AND is_numeric($data[129]) )
  {
    $mr["Valutakoers"]       = $data[129];
  }
  else
  {
    $mr["Valutakoers"]       = _getValuta();
  }
  
  
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"]."";
  $mr["Aantal"]            = $data[67];
  $mr["Fondskoers"]        = _fondskoers($data[30]);
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  
  $mr["Debet"]             = abs($data[155]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = PS_debetbedrag();
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

  
  
  $kosten = $data[151] + $data[147];

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
  
  
  
  $kobu =  $data[153]; // AMT Country and National Federal Tax;
  if ($kobu <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
	  $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr = $afw->reWrite("C154", $mr);
	  $output[] = $mr;
	}

  $kobu =  $data[139]; // AMT Executing Brokers Commission
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr = $afw->reWrite("C140", $mr);
    $output[] = $mr;
  }

  $kobu =  $data[189]; //AMT Regulatory Fees
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr = $afw->reWrite("C190", $mr);
    $output[] = $mr;
  }
  
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  
  $mr["Debet"]             = abs($data[135]);
  $mr["Bedrag"]            = PS_debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  $bankControle = ($data[131]<>0)?$data[131]:$data[125];
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], -1 * $bankControle);
    
  }
  
function PS_do_V()
{ 
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $mr["aktie"]             = "V";
  // debug($data);
  $controleBedrag = 0;
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = ($data[31] <> "")?$data[31]:$data[77];
  $ISIN = trim($data[68]);
  if (!$fonds = _getFonds($ISIN, $mr["Valuta"]))
  {
    return false;  // stop als fonds niet gevonden 
  }
  
  if ($data[128] == "EUR" AND $data["AirsRekValuta"] == "EUR" AND is_numeric($data[129]))
  {
    
    $mr["Valutakoers"]       = $data[129];
  }
  else
  {
    $mr["Valutakoers"]       = _getValuta();
  }
  
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Aantal"]            = -1 * $data[67];
  $mr["Fondskoers"]        = _fondskoers($data[30]);
  //$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  
  $mr["Credit"]            = abs($data[155]);
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

  $kosten = $data[151] + $data[147];

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

  
//  $kobu =  $data[153] + $data[139] + $data[189];
//  if ($kobu <> 0)
//	{
//    $mr["Grootboekrekening"] = "KOBU";
//    $mr["Debet"]             = abs($kobu);
//	  $mr["Bedrag"]            = PS_debetbedrag();
//    $controleBedrag  += $mr["Bedrag"];
//	  $output[] = $mr;
//	}


  $kobu =  $data[153]; // AMT Country and National Federal Tax;
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr = $afw->reWrite("C154", $mr);
    $output[] = $mr;
  }

  $kobu =  $data[139]; // AMT Executing Brokers Commission
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr = $afw->reWrite("C140", $mr);
    $output[] = $mr;
  }

  $kobu =  $data[189]; //AMT Regulatory Fees
  if ($kobu <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Debet"]             = abs($kobu);
    $mr["Bedrag"]            = PS_debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr = $afw->reWrite("C190", $mr);
    $output[] = $mr;
  }
  
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  
  $mr["Credit"]            = abs($data[135]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = PS_creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;
  
  
  $bankControle = ($data[131]<>0)?$data[131]:$data[125];
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $bankControle);
    
}

