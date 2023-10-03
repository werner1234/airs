<?php
session_start();
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/03 06:56:40 $
    File Versie         : $Revision: 1.1 $

    $Log: algemeneImportFuncties.php,v $
    Revision 1.1  2018/07/03 06:56:40  cvs
    call 6734



*/

function checkcontroleBedrag($controleBedrag,$notabedrag)
{
  algCheckControleBedrag2($controleBedrag,$notabedrag);
}

function algCheckControleBedrag2($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;


  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $verschil       = $notabedrag - $controleBedrag;

  if ($verschil == 0)
  {
    $meldArray["gelijk"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
  }
  else if (abs($verschil) < 0.05 )
  {
    $meldArray["verschil005"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".number_format($verschil,2);
  }
  else
  {
    $meldArray["verschil"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".number_format($verschil,2);
  }

}


function addMeldarray($controleBedrag, $regelNr, $rekening, $notabedrag)
{
  algAddMeldarray2($controleBedrag, $regelNr, $rekening, $notabedrag);
}

function algAddMeldarray2($controleBedrag, $regelNr, $rekening, $notabedrag)
{
  global $meldArray;

  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $verschil       = $notabedrag - $controleBedrag;

  if ($verschil == 0)
  {
    $meldArray["gelijk"][] = "regel ".$regelNr.": ".$rekening." --> notabedrag sluit aan ";
  }
  else if (abs($verschil) < 0.05 )
  {
    $meldArray["verschil005"][] = "regel ".$regelNr.": ".$rekening." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".number_format($verschil,2);
  }
  else
  {
    $meldArray["verschil"][] = "regel ".$regelNr.": ".$rekening." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".number_format($verschil,2);
  }

}

//function checkVoorDubbelInRM($mr)
//{
//  algCheckVoorDubbelInRM($mr);
//}

function algCheckVoorDubbelInRM($mr)
{
  returnIfSkipDoubleCheck();

  global $meldArray;
  $db = new DB();
  $query = "
  SELECT 
    id 
  FROM 
    Rekeningmutaties 
  WHERE 
    bankTransactieId = '{$mr["bankTransactieId"]}' AND 
    Rekening         = '{$mr["Rekening"]}' AND
    Boekdatum        = '{$mr["Boekdatum"]}'
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"].")";
    return true;
  }
  return false;
}

//function checkForDoubleImport($mutatiedata)
//{
 // algCheckForDoubleImport($mutatiedata);
//}

function algCheckForDoubleImport($mutatiedata)
{
  returnIfSkipDoubleCheck();

  $Tdb = new DB();

  $query = "
SELECT
 *
FROM
  Rekeningmutaties
WHERE
  Rekening          = '{$mutatiedata["Rekening"]}'          AND
  Boekdatum         = '{$mutatiedata["Boekdatum"]}'         AND
  Grootboekrekening = '{$mutatiedata["Grootboekrekening"]}' AND
  Valuta            = '{$mutatiedata["Valuta"]}'            AND
  Valutakoers       = '{$mutatiedata["Valutakoers"]}'       AND
	Fonds             = '{$mutatiedata["Fonds"]}'             AND
	Aantal            = '{$mutatiedata["Aantal"]}'            AND
	Fondskoers        = '{$mutatiedata["Fondskoers"]}'        AND
	ROUND(Debet,2)    = '".round($mutatiedata["Debet"],2)."'  AND
	ROUND(Credit,2)   = '".round($mutatiedata["Credit"],2)."' AND
	ROUND(Bedrag,2)   = '".round($mutatiedata["Bedrag"],2)."'
";

  // wel/geen record gevonden die voldoet aan criteria?
  return ($rec = $Tdb->lookupRecordByQuery($query));
}

function returnIfSkipDoubleCheck()
{
  if(isTestingSession() and $_SESSION["skipDoubleCheck"])
  {
    return false;
  }
}

function isTestingSession()
{
  global $__appvar;
  return in_array($__appvar["bedrijf"], array("HOME"));
}
?>