<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.6 $

 		$Log: credswiss_ChargesAdvice.php,v $
 		Revision 1.6  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.5  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/01/05 14:45:36  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/13 10:43:10  cvs
 		dbs2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/

function CA_boekdatum($data)
{
  $datumIn = ($data[8] <> "")?$data[8]:$data[5];
  $juldate = mktime(0,0,0,substr($datumIn,4,2),substr($datumIn,6,2),substr($datumIn,0,4));
  $julNow  = mktime(0,0,0,date("n"),date("j"),date("Y"));
  if ($juldate > $julNow)
    return date("Y-m-d",$julNow-86400);
  else
    return date("Y-m-d",$juldate);
}

function  do_ChargesAdvice($datum,$fields,$fieldData)
{
  global $statsArray, $mr, $meldArray,$errorArray, $errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "ChargesAdvice"."-".$datum;
  $stats["regels"] = count($fieldData);
  for ($x=0; $x < count($fieldData); $x++ )
  {
    global $output;
    $mr = array();
    $data = $fieldData[$x];
    
    $mr["bestand"]           = ChargesAdvice."-".$datum;
    $mr["regelnr"]           = $x+7;    
    $mr["Boekdatum"]         = CA_boekdatum($data);
    $mr["Rekening"]          = CS_getPortefeuille($data[4]);
    $mr["bankTransactieId"]  = Trim($data[2]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr["Valutakoers"]       = 1;  // AE_todo valuta altijd EUR bij KD?
    $mr["aktie"]             = "KD";

    $mr["Omschrijving"]      = makeOmschrijving(array($data[46],$data[49],$data[52],$data[55],$data[58],$data[61]));
   
    if (stristr($mr["Omschrijving"],"CUST FEE"))
    {
      $mr["Grootboekrekening"] = "BEW";
    }
    else
    {
      	$mr["Grootboekrekening"] = "KNBA";
    }
    //$mr[Grootboekrekening] = "BEH";
    //$mr[Grootboekrekening] = "VKSTO";
	  if ($data[7] <> 0)  //credit
    {
      $mr["Valuta"]            = $data[6];
      $mr["Rekening"]          .= $mr["Valuta"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[7]);
      $mr["Bedrag"]            = $mr["Credit"];
      $mr["settlementDatum"]   = CS_toDbDate($data[5]);
    }
    else  //debit
    {
      $mr["Valuta"]            = $data[9];
      $mr["Rekening"]          .= $mr["Valuta"];
      $mr["Debet"]             = abs($data[10]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
      $mr["settlementDatum"]   = CS_toDbDate($data[8]);
    }
    
    if ($mr["Bedrag"] <> 0 OR CS_checkRekeningNr())
    {
      $output[] = $mr;
    }
  }
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
}