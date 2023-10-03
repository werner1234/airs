<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/04/04 14:51:33 $
 		File Versie					: $Revision: 1.3 $

 		$Log: ubs_DebitCreditConf.php,v $
 		Revision 1.3  2018/04/04 14:51:33  cvs
 		call 6770
 		
 		naar RVV 20201104
    naar RVV 20201216

*/


include_once 'ubs_DebitCreditConf_functies.php';

function  do_DebitCreditConf($datum,$fields,$module,$fieldData)
{
  

//  debug($fields);
//  debug($fieldData);
  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "DebitCreditConf"."-".$datum;
  $stats["regels"] = count($fieldData);
  
  $skipFaseTwoArray = array();
  $kruisArray    = array();
  $testArray = array();
  
//  for ($x=0; $x < count($fieldData); $x++ )         // call 3547 verzamel set tbv KRUIS boekingen
//  {
//    $data = $fieldData[$x];
//    if (trim($data[9]) <> "")
//    {
//      $testArray[$data[9]][] = $x;                    // verzamel index nummers die bij een transactie id horen (data[9])
//    }
//  }
//
//  foreach ($testArray as $key=>$values)             // loop door de array van transactie id's
//  {
//    if (count($values) == 2)                       // als transactie id in twee regels gevonden dan deze regels skippen in de volgende fase
//    {
//
//      $leftSide = $fieldData[$values[0]];
//      $rightSide = $fieldData[$values[1]];
//
//      if ( $leftSide[1] == $rightSide[1]   OR
//           substr($leftSide[4],0,12) <> substr($rightSide[4],0,12)
//          )
//      {
//        // geen geldig koppel call 3762
//      }
//      else
//      {
//        $skipFaseTwoArray[] = $values[0];
//        $skipFaseTwoArray[] = $values[1];
//        $kruisArray[$key] = $values;                  // array aanmaken met KRUIS setjes
//      }
//
//
//    }
//  }
  
  $testArray = null;                                // maak var leeg om geheugen vrij te maken
  
  
  
  // start Fase 2
  //
  for ($x=0; $x < count($fieldData); $x++ )
  {
    if (in_array($x, $skipFaseTwoArray))
    {
      continue;
    }
    $mr = array();
    $data = $fieldData[$x];
    
    if (count($data) < 2)  continue;

        
    $mr["regelnr"]           = $x+9;
    $mr["bestand"]           = "DebitCredit-".$datum;
    $mr["Boekdatum"]         = UBS_toDbDate($data[21]);
    $mr["Rekening"]          = $data[11]."s1".$data[4];
    if (!UBS_checkRekeningNr())
    {
      continue;
    }
    $mr["bankTransactieId"]  = Trim($data[0]);
    $mr["settlementDatum"]   = UBS_toDbDate($data[3]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr["Valuta"]            = $data[4];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = makeOmschrijving(array($data[12],$data[6],$data[7]));

    
    switch ($module)
    {

        case "AI900":
          DC_do_debit();
          break;
        case "AI910":
          
          DC_do_credit();
          break;
        default:
          $errorArray[] = "[".$mr["regelnr"]."] Onbekende actiecode ".$data[1]." ";
          $errors++;
     
    }
  }
  
  
  // start Fase 3
  //
 
  foreach ($kruisArray as $key => $values)
  {
    $row1 = $fieldData[$values[0]];
    $row2 = $fieldData[$values[1]];
    if ($row1[1] == "MT900")
    {
      $data900 = $row1;
      $data910 = $row2;
    }
    else
    {
      $data900 = $row2;
      $data910 = $row1;
    }
 
    
    
    $datafx[900] = array($data900[4],$data900[6],$data900[7]);
    $datafx[910] = array($data910[4],$data910[6],$data910[7]);
    //debug($datafx);
    $mr["regelnr"]           = ($values[0]+9)." en ".($values[1]+9);
    $mr["bestand"]           = "DebitCredit-".$datum;
    $mr["Boekdatum"]         = DC_boekdatum($data900);
    
    $mr["bankTransactieId"]  = Trim($data900[9]);
    $mr["settlementDatum"]   = CS_toDbDate($data[5]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    DC_do_FX($datafx);
    
  }
  
  
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
}