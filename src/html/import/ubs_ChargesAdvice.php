<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/15 12:10:00 $
 		File Versie					: $Revision: 1.3 $

 		$Log: ubs_ChargesAdvice.php,v $
 		Revision 1.3  2020/04/15 12:10:00  cvs
 		rekening uit data[80]
 		
 		naar RVV 20201104

*/
include_once 'ubs_ChargesAdvice_functies.php';
function  do_ChargesAdvice($datum,$fields,$fieldData)
{
  global $statsArray, $mr, $meldArray,$errorArray, $errors, $data;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "ChargesAdvice"."-".$datum;
  $stats["regels"] = count($fieldData);
  for ($x=0; $x < count($fieldData); $x++ )
  {
    global $output;
    if (in_array($x, $skipFaseTwoArray))
    {
      continue;
    }
    $mr = array();
    $data = $fieldData[$x];

    if (count($data) == 0)  continue;
//debug($data);
    $mr["regelnr"]           = $x+9;
    $mr["bestand"]           = "ChargesAdvice-".$datum;
    $mr["Boekdatum"]         = UBS_toDbDate($data[4]);
    //$mr["Rekening"]          = substr($data[3],1,14)."s1".$data[5];
    $mr["Rekening"]          = $data[80]."s1".$data[5];
//    debug($mr, $data[80]);
    if (!UBS_checkRekeningNr())
    {
      continue;
    }
//    debug($mr);
    $mr["bankTransactieId"]  = Trim($data[0]);
    $mr["settlementDatum"]   = UBS_toDbDate($data[4]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr["Valuta"]            = $data[5];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = $data[7];

    $transSplit = explode("/",$data[7]);
    switch ($transSplit[0])
    {

      case "DCP001":
        CA_do_KNBA();
        break;
      case "DIS001":
        CA_do_RENTE();
        break;
      default:
        $errorArray[] = "[".$mr["regelnr"]."] Onbekende actiecode ".$transSplit[0]." ";
        $errors++;

    }

  }
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
}