<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2015/01/14 08:27:39 $
 		File Versie					: $Revision: 1.6 $

 		$Log: credswiss_ForexConf_functies.php,v $
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
function FE_debetbedrag()
{
	global $data, $mr;
  if ($mr["Valuta"] <> "EUR" )
  {
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else
  {
    return -1 * $mr["Debet"];
  }  
}

function FE_creditbedrag()
{
	global $data, $mr;
  if ($mr["Valuta"] <> "EUR" )
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    return $mr["Credit"];
  }
	 
}
// gebaseerd op stroeve do_dv()
function FE_do_Mutatie()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;
  
  $mr["Grootboekrekening"] = "KRUIS";

  if ($data[27] == "EUR" AND $data[47] <> "EUR")
  {
    $mr["Valuta"]            = $data[47];
    $mr["Valutakoers"]       = $mr["Valuta"] <> "EUR"?1/$data[26]:1;  
    //$mr["Valutakoers"]       = $data[28]/$data[48];  

    //$mr["Omschrijving"]      = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Rekening"]          = CS_forex_rekening($data[42]).$data[27];
    $mr["Debet"]             = abs($data[48]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = FE_debetbedrag();
    $controleBedrag         += $mr["Bedrag"];
    $output[] = $mr;

    $mr["Rekening"]          = CS_forex_rekening($data[62]).$data[47];
    $mr["Valuta"]            = $data[47];
    $mr["Valutakoers"]       = $mr["Valuta"] <> "EUR"?1/$data[26]:1;  
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[48]);
    $mr["Bedrag"]            = $mr["Credit"];
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];
    if (CS_checkRekeningNr()) 
    {
      $output[] = $mr;
    }
    

  }
  elseif ($data[27] <> "EUR" AND $data[47] == "EUR")
  {
    $mr["Valuta"]            = $data[27];
    $mr["Valutakoers"]       = $mr["Valuta"] <> "EUR"?1/$data[26]:1;  
    //$mr["Valutakoers"]       = $data[28]/$data[48];  

    //$mr["Omschrijving"]      = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Rekening"]          = CS_forex_rekening($data[42]).$data[27];
    $mr["Debet"]             = abs($data[28]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * -1;
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];
    $output[] = $mr;

    $mr["Rekening"]          = CS_forex_rekening($data[62]).$data[47];
    $mr["Valuta"]            = $data[27];
    $mr["Valutakoers"]       = $mr["Valuta"] <> "EUR"?1/$data[26]:1;  
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[28]);
    $mr["Bedrag"]            = FE_creditbedrag();
    $controleBedrag         += $mr["Bedrag"];
    if (CS_checkRekeningNr()) 
    {
      $output[] = $mr;
    }
  }
  else
  {
    $error = "";
  }
  
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], 0);
    
}
  