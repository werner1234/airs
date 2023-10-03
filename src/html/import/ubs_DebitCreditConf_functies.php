<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/01/03 15:48:03 $
 		File Versie					: $Revision: 1.2 $

 		$Log: ubs_DebitCreditConf_functies.php,v $
 		Revision 1.2  2018/01/03 15:48:03  cvs
 		call 6472
 		
 		Revision 1.1  2017/09/20 06:18:05  cvs
 		megaupdate 2722
 		


*/


function DC_boekdatum($data)
{
  global $date;
  $fileDate = substr($date,0,8);
  
  $datumIn = $data[5];
  if ($datumIn > $fileDate)
  {
    
  }
  $juldate = mktime(0,0,0,substr($datumIn,4,2),substr($datumIn,6,2),substr($datumIn,0,4));
  $fileDate = mktime(0,0,0,substr($fileDate,4,2),substr($fileDate,6,2),substr($fileDate,0,4));
  
  $julNow  = mktime(0,0,0,date("n"),date("j"),date("Y"));
  if ($juldate >= $fileDate)
  {
    $fileDag = date("w", $fileDate);
    if ($fileDag < 2) 
    {
      $fileDate = $fileDate - ((2+$fileDag) * 86400);
    }          
    return date("Y-m-d",$fileDate);
  }  
  else
    return date("Y-m-d",$juldate);
}



function DC_debetbedrag()
{
	global $mr;
  return -1 * $mr["Debet"];
}

function DC_creditbedrag()
{
	global $mr;
  return $mr["Credit"];
}

function DC_do_debit()
{ 
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $mr["aktie"]             = "Deb";
  $controleBedrag = 0;
  if (stristr($data[14], "Management Fee") )
  {
    $mr["Grootboekrekening"] = "BEH";
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
  }




  $mr["Debet"]             = abs($data[5]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = DC_debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("ONTTR",$mr);
  $mr = $afw->reWrite("BEH",$mr);
  $output[] = $mr;

  //addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], -1 * $data[98]);
    
}
  
function DC_do_credit()
{ 
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $mr["aktie"]             = "Cred";
  $controleBedrag = 0;
  
  $mr["Grootboekrekening"] = "STORT";
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data[5]);
  $mr["Bedrag"]            = DC_creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("STORT",$mr);
  $output[] = $mr;

  //addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], -1 * $data[98]);
    
}


function DC_do_FX($datafx)
{ 
  global $mr, $output, $meldArray;
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;
  
  $mr["Grootboekrekening"] = "KRUIS";
  

  if ($datafx[900][1] == "EUR" AND $datafx[910][1] <> "EUR" )
  {
    $mr["Valuta"]            = $datafx[910][1];
    $mr["Valutakoers"]       = $datafx[900][2] / $datafx[910][2];
    $mr["Omschrijving"]      = "Valutatransactie ".$datafx[910][1]."/".$datafx[900][1].", koers: ".round($mr["Valutakoers"],5);
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;

    $mr["Rekening"]          = $datafx[900][0]."EUR";
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }

    $mr["Debet"]             = abs($datafx[910][2]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($datafx[910][2] * $mr["Valutakoers"]) * -1;

    $output[] = $mr;
    $controleBedrag += $mr["Bedrag"];

    $mr["Rekening"]          = $datafx[910][0].$datafx[910][1];
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($datafx[910][2]);
    $mr["Bedrag"]            = $mr["Credit"];
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];
    $output[] = $mr;
    //$controleBedrag -= $mr["Credit"];

  }
  elseif ($datafx[900][1] <> "EUR" AND $datafx[910][1] == "EUR" )
  {
    $mr["Valuta"]            = $datafx[900][1];
    $mr["Valutakoers"]       = $datafx[910][2] / $datafx[900][2];
    $mr["Omschrijving"]      = "Valutatransactie ".$datafx[900][1]."/".$datafx[910][1].", koers: ".round($mr["Valutakoers"],5);
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;

    $mr["Rekening"]          = $datafx[900][0].$datafx[900][1];
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }

    $mr["Debet"]             = abs($datafx[900][2]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($datafx[900][2]) * -1;
    $controleBedrag += $mr["Debet"] * $mr["Valutakoers"];
    $output[] = $mr;

    $mr["Rekening"]          = $datafx[910][0]."EUR";
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($datafx[900][2]);
    $mr["Bedrag"]            = abs($datafx[900][2]* $mr["Valutakoers"]);
    $controleBedrag -= $mr["Bedrag"];
    $output[] = $mr;

  }
  elseif ($datafx[900][1] <> $datafx[910][1] )
  {
    $mr["Valuta"]            = $datafx[900][1];
    
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;

    $mr["Rekening"]          = $datafx[900][0].$datafx[900][1];
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }

    $mr["Debet"]             = abs($datafx[900][2]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($datafx[900][2]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Valuta"]            = $datafx[910][1];
    $mr["Rekening"]          = $datafx[910][0].$datafx[910][1];
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($datafx[910][2]);
    $mr["Bedrag"]            = abs($datafx[910][2]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Valuta"]            = $datafx[900][1];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;

    $mr["Rekening"]          = $datafx[900][0].$datafx[900][1];
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }

    $mr["Debet"]             = abs($datafx[900][2]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($datafx[900][2]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Grootboekrekening"] = "STORT";
    $mr["Valuta"]            = $datafx[910][1];
    $mr["Rekening"]          = $datafx[910][0].$datafx[910][1];
    if (!CS_checkRekeningNr()) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($datafx[910][2]);
    $mr["Bedrag"]            = abs($datafx[910][2]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], 0);
    
}