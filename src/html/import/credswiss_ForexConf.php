<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2015/01/14 08:27:39 $
 		File Versie					: $Revision: 1.4 $

 		$Log: credswiss_ForexConf.php,v $
 		Revision 1.4  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/20 12:48:18  cvs
 		dbs 2746
 		
 		Revision 1.2  2014/11/13 10:43:10  cvs
 		dbs2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/


include_once 'credswiss_ForexConf_functies.php';
function  do_ForexConf($datum,$fields,$fieldData)
{
  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "ForexConf"."-".$datum;
  $stats["regels"] = count($fieldData);
  for ($x=0; $x < count($fieldData); $x++ )
  {
    global $output;
    $mr = array();
    $data = $fieldData[$x];
    if (count($data) == 0) continue;
   
    $mr["regelnr"]           = $x+7;    
        if ($data[3] <> "NEWT")
    {
      $errorArray[] = "[".$mr["regelnr"]."] Functie geen NEWT (".$data[3].")";
      $errors++;
    } 
    else
    {
      $fondsValuta = trim($data[25]);

      $rekeningParts = explode("ACCT/",$data[18]);

      $mr["bestand"]           = ForexConf."-".$datum;
      $mr["Boekdatum"]         = CS_toDbDate($data[24]);
      //$mr["Rekening"]          = CS_getPortefeuille($rekeningParts[1]);
      $mr["bankTransactieId"]  = Trim($data[2]);
      $mr["Omschrijving"]      = "Valuta transactie";
      $mr["settlementDatum"]   = CS_toDbDate($data[25]);

      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;


      FE_do_Mutatie();
      }
  }
  
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
}