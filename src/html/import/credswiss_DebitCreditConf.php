<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2015/05/21 12:09:57 $
 		File Versie					: $Revision: 1.7 $

 		$Log: credswiss_DebitCreditConf.php,v $
 		Revision 1.7  2015/05/21 12:09:57  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/05/06 09:37:24  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/01/05 14:45:36  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/20 12:48:18  cvs
 		dbs 2746
 		
 		Revision 1.1  2014/11/14 14:34:19  cvs
 		dbs 2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/


include_once 'credswiss_DebitCreditConf_functies.php';

function  do_DebitCreditConf($datum,$fields,$fieldData)
{
  
  /*
   *  DO_geld
   *  data 1 als MT900 is debit, MT910 is credit
   *  data 4 = rekeningnr
   *  data 5 boekdatum als > now dan gisteren
   *  data 6 valuta reknr = 4.5
   *  data 7 bedrag
   *  data 14+15+27 omschrijving
   */
  
  //debug($fields);
  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "DebitCreditConf"."-".$datum;
  $stats["regels"] = count($fieldData);
  
  $skipFaseTwoArray = array();
  $kruisArray    = array();
  $testArray = array();
  
  for ($x=0; $x < count($fieldData); $x++ )         // call 3547 verzamel set tbv KRUIS boekingen
  {
    $data = $fieldData[$x];
    if (trim($data[9]) <> "")
    {
      $testArray[$data[9]][] = $x;                    // verzamel index nummers die bij een transactie id horen (data[9])
    }  
  }
  
  foreach ($testArray as $key=>$values)             // loop door de array van transactie id's
  {
    if (count($values) == 2)                       // als transactie id in twee regels gevonden dan deze regels skippen in de volgende fase
    {
      
      $leftSide = $fieldData[$values[0]];
      $rightSide = $fieldData[$values[1]];
      
      if ( $leftSide[1] == $rightSide[1]   OR 
           substr($leftSide[4],0,12) <> substr($rightSide[4],0,12)  
          ) 
      {
        // geen geldig koppel call 3762
      }
      else
      {
        $skipFaseTwoArray[] = $values[0];
        $skipFaseTwoArray[] = $values[1];
        $kruisArray[$key] = $values;                  // array aanmaken met KRUIS setjes
      }
      
      
    }
  }
  
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
    
    if (count($data) == 0)  continue;
        
    $mr["regelnr"]           = $x+7;    
    $mr["bestand"]           = "DebitCredit-".$datum;
    $mr["Boekdatum"]         = DC_boekdatum($data);
    $mr["Rekening"]          = CS_getPortefeuille($data[4]).$data[6];  
    if (!CS_checkRekeningNr()) 
    {
      continue;
     }
    $mr["bankTransactieId"]  = Trim($data[2]);
    $mr["settlementDatum"]   = "";
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr["Valuta"]            = $data[6];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = makeOmschrijving(array($data[14],$data[15],$data[27]));

    
    switch ($data[1])
    {

        case "MT900":
          DC_do_debit();
          break;
        case "MT910":
          
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
    $mr["regelnr"]           = ($values[0]+7)." en ".($values[1]+7);    
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