<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/22 08:39:21 $
 		File Versie					: $Revision: 1.6 $

 		$Log: credswiss_ReceiveDeliverConf_functies.php,v $
 		Revision 1.6  2019/11/22 08:39:21  cvs
 		call 8166
 		
 		Revision 1.5  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.4  2015/05/06 09:40:50  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/20 12:48:18  cvs
 		dbs 2746
 		


*/


function RD_boekdatum($data)
{
  $datumIn = ($data[16]<>"")?$data[16]:$data[20];
  $juldate = mktime(0,0,0,substr($datumIn,4,2),substr($datumIn,6,2),substr($datumIn,0,4));
  $julNow  = mktime(0,0,0,date("n"),date("j"),date("Y"));
  //debug(array("in:".$juldate,"nu:".$julNow));
  if ($juldate > $julNow)
    return date("Y-m-d",$nowDate-86400);
  else
    return date("Y-m-d",$juldate);
}



function RD_debetbedrag()
{
	global $mr;
  return -1 * $mr["Debet"] * $mr["Valutakoers"];
}

function _creditbedrag()
{
	global $mr;
  return $mr["Credit"] * $mr["Valutakoers"];
}

function RD_do_D()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "D";
  $controleBedrag = 0;
  
	$mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	
	$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
//	if ($data[13] == "PNC")
//	  $mr["Debet"]  = $mr["Debet"]/100;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = RD_debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	$mr["Transactietype"]    = "D";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 1;

	$output[] = $mr;

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

  if ($mr["Bedrag"] <> 0)
	 $output[] = $mr;

  //addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[x12]);
    
}
  
function RD_do_L()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  
  $controleBedrag = 0;
	$mr["aktie"]              = "L";
	$mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Debet"]             = 0;
  $mr["Aantal"]            = $mr["Aantal"] * -1;
	$mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
//  if ($data[13] == "PNC")
//	  $mr["Credit"]  = $mr["Credit"]/100;
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "L";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 1;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "ONTTR";
	$mr["Fonds"]             = "";
	$mr["Valuta"]            = "EUR";
	$mr["Valutakoers"]       = 1;
	$mr["Aantal"]            = 0;
	$mr["Fonds"]             = "";
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs($mr["Bedrag"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = -1 * $mr["Debet"];
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";

	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

}

function RD_do_LA()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "LA";
  $controleBedrag = 0;
  
  $mr["Rekening"]          = CS_getPortefeuille($data[40]).$data[109];
	$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	

	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
  //	$mr["Credit"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = 0;
  

	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 1;

	$output[] = $mr;
  
}

function RD_do_DA()
{ 
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "DA";
  $controleBedrag = 0;
  
  $mr["Rekening"]          = CS_getPortefeuille($data[40]).$data[109];
	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	
//	$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = RD_debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 1;

	$output[] = $mr;
  
}