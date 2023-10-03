<?php
/*
	File Versie					: $Revision: 1.5 $

 		$Log: ubs_functies.php,v $
 		Revision 1.5  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		naar RVV 20201104
 		
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

function UBS_getRekening($reknr,$val)
{
  global $errorArray,$errors,$mr;
  
  $db = new DB();
  $rekeningNr = trim($reknr).trim($val);
  $depot      = "UBS";
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie` = 0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie` = 0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $errorArray[] = "[".$mr["regelnr"]."] Rekening onbekend: <b>".$reknr.trim($val)."</b> ";
      $errors++;
      return false;
    }
  }

}

function UBS_toDbDate($in,$y4=true)
{
  $y4 = (strlen($in) != 6);  // detectie datum met of zonder 20 jaarprefix
  if ($y4)
  {
    return substr($in,0,4)."-".substr($in,4,2)."-".substr($in,6,2);
  }
  else
  {
    return "20".substr($in,0,2)."-".substr($in,2,2)."-".substr($in,4,2);
  }

}

function clipDoubleSpace($string)
{
  while (strstr($string,"  "))
  {
    $string = str_replace("  ", " ", $string);
  }
  return $string;
}

function _getUBScode($data)
{
  //call 6894  voor optie USBCode uit data[28] filteren
  $UBSCode = trim($data[29]);
  if ($data[33] == "OPC" AND trim($data[29]) == "")
  {
    $f = explode("+EP:", $data[28]);
    $UBSCode = $f[1];
  }
  return $UBSCode;
}

function _getFonds($ISIN, $val, $UBScode="")
{
  global $errorArray,$errors,$mr;
  $out = array();
  $db = new DB();

  $UBSCodeNotFound = true;
  if ($UBScode != "")
  {
    $query = "SELECT * FROM Fondsen WHERE UBSCode = '".$UBScode."' ";

    if ($out = $db->lookupRecordByQuery($query))
    {
      $UBSCodeNotFound = false;
    }
  }

  if ($UBSCodeNotFound)
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '" . $ISIN . "' AND Valuta = '" . $val . "' ";
    $out = $db->lookupRecordByQuery($query);
  }

  if ($out["id"] > 0)
  {
    return $out;
  }
  else
  {
    $errorArray[] = "[".$mr["regelnr"]."] Fonds onbekend: <b>".$ISIN."/".$val.", UBS: ".$UBScode."</b> ";
    $errors++;
    return false;
  }
  
    
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
  global $mr, $data, $errorArray,$errors;

  $datum   = $mr["Boekdatum"];

  $db = new DB();

  if ($data[71] == "EUR" AND $data[54] == "EUR" )
  {
    return 1;
  }
  elseif ($data[71] == $data[54] AND $data[71] != "EUR")
  {
    $query = "SELECT Koers FROM Valutakoersen WHERE Valuta = '".$data[54]."' AND Datum <= '".$datum."' ORDER BY Datum DESC ";
    if ($out = $db->lookupRecordByQuery($query))
      return $out["Koers"];
    else
      return false;
  }
  elseif ($data[71] <> "EUR" AND $data[54] <> "EUR")
  {
    $errorArray[] = "[".$mr["regelnr"]."] aan-/verkoop met onbekende wisselkoers </b> ";
    $errors++;
    return true;
  }

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

function CA_checkRekeningNr()
{
  global $errorArray,$errors,$mr;

  UBS_checkRekeningNr();
}


function UBS_checkRekeningNr()
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

