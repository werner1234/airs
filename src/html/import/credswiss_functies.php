<?php
/*
	File Versie					: $Revision: 1.12 $

 		$Log: credswiss_functies.php,v $
 		Revision 1.12  2020/06/10 12:58:34  cvs
 		call 8671
 		
 		Revision 1.11  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.9  2015/05/11 13:36:52  cvs
 		*** empty log message ***
 		
 		Revision 1.8  2015/05/06 09:41:09  cvs
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


function CS_getFondskoers($fondscode, $datum="now")
{
  $sqlDatum = ($datum = "now")?" NOW() ":" '".$datum."' ";
  $query = "
    SELECT
      Fondskoersen.Fonds,
      Fondskoersen.Datum,
      Fondskoersen.Koers
    FROM
      `Fondskoersen`
    WHERE
      Fonds = '".$fondscode."'
    AND 
      Datum <= NOW()
    ORDER BY
      Datum DESC
";
  $db = new DB();
  if (!$rec = $db->lookupRecordByQuery($query))
  {
    return false;
  }
  else
  {
    return $rec["Koers"];
  }
}

function CS_getRekeningRec($reknr)  // call 3525  rekeningRec ophalen om valuta te bepalen
{
  global $errorArray,$errors,$mr;
  
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening LIKE '".$reknr."%'  ";
  
  if ($out = $db->lookupRecordByQuery($query))
  {
    
    return $out;
    
  }
  else
  {
    $errorArray[] = "[".$mr["regelnr"]."] Rekening onbekend: <b>".$reknr."</b> ";
    $errors++;
    return false;
  }
}


function CS_checkRekeningNr()
{
  global $errorArray,$errors,$mr;
  
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$mr["Rekening"]."'  ";
  if ($out = $db->lookupRecordByQuery($query))
  {
    return true;
  }
  else
  {
    $errorArray[] = "[".$mr["regelnr"]."] Rekening onbekend: <b>".$mr["Rekening"]."</b> ";
    $errors++;
    return false;
  }
}


function CS_getPortefeuille($in)
{
  if (substr($in,0,1) == "/" )
  {
    return substr($in,1);
  }
  else
  {
    return $in;
  }
  
  //$parts = explode("-",$in);
  //return $parts[0]."-".$parts[1]."-".$parts[2];
  // return (int) $parts[0].$parts[1].$parts[2];
}

function CS_forex_rekening($in)
{
  
  $in =substr($in,1);
  $out = substr($in,0,4)."-".substr($in,4,7)."-".substr($in,11,2)."-".substr($in,13,3);
  return $out;
}

function CS_toDbDate($in)
{
  return substr($in,0,4)."-".substr($in,4,2)."-".substr($in,6,2);
}

function clipDoubleSpace($string)
{
  while (strstr($string,"  "))
  {
    $string = str_replace("  ", " ", $string);
  }
  return $string;
}


function _getFonds($ISIN, $val)
{
  global $errorArray,$errors,$mr;

  $db = new DB();
  $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$ISIN."' AND Valuta = '".$val."' ";
  
  
  if ($ISIN == "")
  {
    $out = array();
    $out["Fonds"] = "optie/future";
    return $out;
  }
  else
  {
    if ($out = $db->lookupRecordByQuery($query))
  {
    return $out;
  }
  else
  {
    $errorArray[] = "[".$mr["regelnr"]."] Fonds onbekend: <b>".$ISIN."/".$val."</b> ";
    $errors++;
    return false;
  }
  }
  
    
}

function _getFondsBankcode($CSCode)
{
  global $errorArray,$errors,$mr;

  if ($CSCode=="")
  {
    return false;
  }

  $db = new DB();
  $query = "SELECT * FROM Fondsen WHERE CSCode='{$CSCode}'";
  return $db->lookupRecordByQuery($query);

}

function _getFondsKoers($fonds, $datum)
{
  $db = new DB();
  $query = "SELECT * FROM Fondskoersen WHERE Fonds = '".$fonds."' AND Datum <= '".$datum."' ORDER BY Datum DESC";
  
  if ($out = $db->lookupRecordByQuery($query))
  {
    return $out;
  }
  else
  {
    return false;
  }
}

function _fondskoers($bedrag)
{
	global $mr;

	$valuta  = $mr["Valuta"];
	if ($valuta <> "PNC")
		return $bedrag;
	else
	  return $bedrag/100;
}

function _valutakoers()
{
	global $mr;
	$db = new DB();
	if ($mr["Valuta"] <> "EUR" )
	{
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
  return 1;
}

function _getValuta()
{
  global $mr;
  $valuta  = $mr["Valuta"];
  $datum   = $mr["Boekdatum"];
  $waardeOrg = $mr["waardeOrg"];
  $waardeAfr = $mr["waardeAfr"];
  unset($mr["waardeOrg"]);
  unset($mr["waardeAfr"]);
  
  $db = new DB();
  
  if ($waardeOrg <> 0 AND $waardeAfr <> 0)  //wisselkoers berekenen vanuit bestand
  {
    return ($waardeAfr/$waardeOrg);
  }
  
  if ($valuta == "EUR")
  {
    return 1;
  }
  $query = "SELECT Koers FROM Valutakoersen WHERE Valuta = '".$valuta."' AND Datum <= '".$datum."' ORDER BY Datum DESC ";
  
  if ($out = $db->lookupRecordByQuery($query))
    return $out["Koers"];
  else
    return false;
}

function addMeldarray($controleBedrag, $regelNr, $rekening, $notabedrag)
{
  global $meldArray;

  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);

  if ( $controleBedrag <> $notabedrag )
    $meldArray[] = "[".$regelNr."] ".$rekening." :: notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "[".$regelNr."] ".$rekening." :: notabedrag sluit aan ";
}

function makeOmschrijving($oms)
{
  if (is_array($oms))
  {
    foreach($oms as $regel)
    {
      $regel = clipDoubleSpace($regel);
      $out .= " ".trim($regel);
    }
  }
  else
  {
    $out = $oms;  
  }
  return trim($out);
}


?>