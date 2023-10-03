<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/01 12:11:26 $
 		File Versie					: $Revision: 1.4 $

 		$Log: credswiss_CashPositions_functies.php,v $
 		Revision 1.4  2020/07/01 12:11:26  cvs
 		call 8714
 		
 		Revision 1.3  2020/06/17 07:13:47  cvs
 		call 8671
 		
 		Revision 1.2  2018/02/07 13:10:42  cvs
 		call 6578
 		
 		Revision 1.1  2015/03/26 09:48:19  cvs
 		*** empty log message ***
 		
 		

*/
function CP_debetbedrag()
{
	global $data, $mr;
  	
  if ($mr["Valuta"] == "EUR"  )
  {
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else
  {
    return -1 * $mr["Debet"];
  }  
}

function CP_creditbedrag()
{
	global $data, $mr;
  if ($mr["Valuta"] == "EUR"  )
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    return $mr["Credit"];
  }
}


////////////////////////////////////

function CP_boekdatum($data)
{
  $y = substr($data[27],0,4);
  $m = substr($data[12],0,2);
  $d = substr($data[12],2,2);
  $juldate = mktime(0,0,0,$m,$d,$y);
  return date("Y-m-d",$juldate);
}


function CP_do_Mutatie($omschrijving="")

{
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  
  $mr["Valuta"]            = $data[9];
  $mr["Valutakoers"]        = _getValuta();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $omschrijving;
  if ($data[13] == "C" OR $data[13] == "RD")
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Credit"]            = abs($data[15]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = CP_creditbedrag();
    $mr = $afw->reWrite("STORT", $mr);
  
  }
  elseif ($data[13] == "D" OR $data[13] == "RC")
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[15]);
    $mr["Bedrag"]            = CP_debetbedrag();
    $mr = $afw->reWrite("ONTTR", $mr);
  }
  else
    $mr["Bedrag"]            = 0;

  if (stristr($mr["Omschrijving"], "Management fee"))
  {
    $mr["Grootboekrekening"] = "BEH";
  }

  if (stristr($mr["Omschrijving"], "Safekeeping fees"))
  {
    $mr["Grootboekrekening"] = "BEW";
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;  
}

function CP_do_VMAR()
{
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $mr["Grootboekrekening"] = "VMAR";
  $mr["Valuta"]            = $data[9];
  $mr["Valutakoers"]        = _getValuta();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[13] == "C" OR $data[13] == "RD")
  {
    $mr["Credit"]            = abs($data[15]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = CP_creditbedrag();
  }
  elseif ($data[13] == "D" OR $data[13] == "RC")
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[15]);
    $mr["Bedrag"]            = CP_debetbedrag();
  }
  else
    $mr["Bedrag"]            = 0;
  

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;  
}

function CP_do_KNBA()
{
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $mr["Grootboekrekening"] = "KNBA";
  $mr["Valuta"]            = $data[9];
  $mr["Valutakoers"]        = _getValuta();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[13] == "C" OR $data[13] == "RD")
  {
    $mr["Credit"]            = abs($data[15]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = CP_creditbedrag();
  }
  elseif ($data[13] == "D" OR $data[13] == "RC")
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[15]);
    $mr["Bedrag"]            = CP_debetbedrag();
  }
  else
    $mr["Bedrag"]            = 0;


  $controleBedrag       += $mr[Bedrag];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KNBA", $mr);
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;
}


function CP_do_RENTE()
{
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $mr["Grootboekrekening"] = "RENTE";
  $mr["Valuta"]            = $data[9];
  $mr["Valutakoers"]        = _getValuta();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[13] == "C" OR $data[13] == "RD")
  {
    $mr["Credit"]            = abs($data[15]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = CP_creditbedrag();
  }
  elseif ($data[13] == "D" OR $data[13] == "RC")
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[15]);
    $mr["Bedrag"]            = CP_debetbedrag();
  }
  else
    $mr["Bedrag"]            = 0;


  $controleBedrag       += $mr[Bedrag];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("RENTE", $mr);
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;
}



function CP_do_FX($dataArray)
{
  global $fonds, $data, $mr, $output, $meldArray;
  if ($dataArray[0][9] == "EUR")
  {
    $rec1 = $dataArray[0];
    $rec2 = $dataArray[1];
  }
  else
  {
    $rec1 = $dataArray[1];
    $rec2 = $dataArray[0];
  }
  $mr = $rec1["mr"];
  unset($mr["waardeOrg"]);
  unset($mr["waardeAfr"]);
 
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;
  $mr["Rekening"]          = $rec1[4].$rec1[9];
  $mr["Grootboekrekening"] = "KRUIS";

  $mr["Valuta"]            = $rec2[9];
  $mr["Valutakoers"]       = $rec1[15]/$rec2[15];
   
  $mr["Omschrijving"]      = "Valutatransactie ".$rec1[17];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
    
  if ($rec1[13] == "C")
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($rec2[15]);
    $mr["Bedrag"]            = $rec2[15] * $mr["Valutakoers"];
  }
  else
  {
    $mr["Debet"]             = abs($rec2[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $rec2[15] * $mr["Valutakoers"];
  }

  


  if (CS_checkRekeningNr()) 
  {
    $output[] = $mr;
  }

  $mr["Rekening"]          = $rec2[4].$rec2[9];
  $mr["Valuta"]            = $rec2[9];
  $mr["Valutakoers"]       = $rec1[15]/$rec2[15];

  $mr["Omschrijving"]      = "Valutatransactie ".$rec1[17];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ($rec2[13] == "C")
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($rec2[15]);
    $mr["Bedrag"]            = $rec2[15];
  }
  else
  {
    $mr["Debet"]             = abs($rec2[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $rec2[15];
  }


  if (CS_checkRekeningNr()) 
  {
    $output[] = $mr;
  }

  
  
   
}
