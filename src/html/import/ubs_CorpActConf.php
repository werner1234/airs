<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/11/28 10:59:31 $
 		File Versie					: $Revision: 1.8 $

 		$Log: ubs_CorpActConf.php,v $
 		Revision 1.8  2018/11/28 10:59:31  cvs
 		call 7366
 		
 		naar RVV 20201104

*/



include_once 'ubs_CorpActConf_functies.php';
function  do_CorpActConf($datum,$fields,$fieldData)
{
  
  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "CorpActConf-".$datum;
  $stats["regels"] = count($fieldData);


  for ($x=0; $x < count($fieldData); $x++ )
  {
    global $output;
    $mr = array();
    $data = $fieldData[$x];

    if(count($data) < 2) continue;

    if( $data[1] == "Party qualifier") continue;

    $mr["regelnr"]           = $x+8;
    if ($data[3] <> "NEWM")
    {

      $errorArray[] = "[".$mr["regelnr"]."] Functie geen NEWM (".$data[3].")";
      $errors++;
    } 
    else
    {


//      $fondsValuta = trim($data[31]);

      $mr["bestand"]           = "CorpActConf-".$datum;
      $mr["Boekdatum"]         = UBS_toDbDate($data[451], true);
      $mr["Rekening"]          = $data[411].$data[433];


      if (
        ($data[4] == "DVCA" OR $data[4] == "INTR" OR $data[4] == "PRED")      AND
        trim ($data[411]) == "" AND
        trim ($data[451]) == ""
      )
      {
        //$errorArray[] = "[".$mr["regelnr"]."] overgeslagen. Overbodige dividend/coupon regel";
//        $errors++;
        continue;
      }

      if ($rekRec = UBS_getRekening($data[411],$data[433]))
      {
        $mr["Rekening"] = $rekRec;
      }
      else
      {
        continue;
      }





      
      $mr["bankTransactieId"]  = Trim($data[1]); //call 7366
      $mr["settlementDatum"]   = UBS_toDbDate($data[457],true);

      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $mr["Valutakoers"]       = 1;  
      //$mr["Omschrijving"]      = makeOmschrijving(array($data[23],$data[24],$data[25],$data[26],$data[27],$data[28]));
      $mr["Omschrijving"]      = "";
      //$mr["waardeOrg"]         = (float)$data[78];
//      $mr["waardeAfr"]         = (float)$data[76];

      switch ($data[4])
      {
        //case "LIQU":
//        case "MCAL":
//          if ($data[36] == "DEBT" AND $data[55] == "")
//          {
//            CA_do_V("LIQU");
//          }
//          elseif ($data[74] <> 0)
//          {
//            CA_do_DV("DV");
//          }
//          else
//          {
//            $errorArray[] = "regel ".$mr["regelnr"]." LIQU - Uitval (Corporate Action Event Indicator)";
//            $errors++;
//          }
//          break;
        case "ACCU":
        case "OTHR":
          $errorArray[] = "regel ".$mr["regelnr"]." actiecode ".$data[4]." overgeslagen";
          $errors++;
          break;
        case "INTR":
        case "PRED":
          CA_do_R();
          break;
        case "REDM":
        case "MCAL":
          if ($data[410] == "CASH")
          {
            CA_do_V("REDM");
          }
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." REDM/MCALL met extra stukregel  (Corporate Action Event Indicator)";
            $errors++;
          }

          break;
        case "DVCA":

          CA_do_DV("DV");
          break;
// onverwerkt hieronder --------------------------------------------------------------------------
//        case "CHAN":
//          CA_do_CONV();
//          break;
//        case "EXOF":
//
//          if ( substr($data[17],0,8) ==  "FUND PRE" AND $data[36] == "DEBT"  AND $data[55] <> "")
//          {
//            CA_do_A();
//          }
//          elseif ( substr($data[17],0,8) ==  "FUND PRE" AND $data[36] == "CRED"  AND $data[55] == "")
//          {
//            $errorArray[] = "regel ".$mr["regelnr"]." actie ".$data[6]." wordt genegeerd: opboeking fund prepayment";
//            $errors++;
//          }
//          elseif ($data[36] <> "" AND $data[53] <> "")
//          {
//            CA_do_CONV();
//          }
//
//          else
//          {
//            $errorArray[] = "regel ".$mr["regelnr"]." EXOF incompleet (Corporate Action Event Indicator)";
//            $errors++;
//          }
//          break;
//


        case "BONU":
          if ($data[287] == "CRED")
          {
            CA_do_D();
          }
          else if ($data[287] != "")
          {
            $errorArray[] = "regel ".$mr["regelnr"]." BONU met onbekende actiecode ".$data[287];
            $errors++;
          }
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." BONU zonder actie, overgeslagen";
            $errors++;
          }



//          }
//          elseif ($data[36] == "DEBT")
//          {
//            CA_do_L();
//          }
          break;
//        case "DRIP":
//          if ($data[104] <> 0)
//          {
//            CA_do_DV("DRIP");
//          }
//          else if (trim($data[104]) == "" AND trim($data[37]) <> "" AND trim($data[56]) == "")
//          {
//            CA_do_L();
//          }
//          else
//          {
//            $errorArray[] = "regel ".$mr["regelnr"]." DRIP met 2 securities ".$data[6]." (Corporate Action Event Indicator)";
//            $errors++;
//          }
//          break;

//        case "DRCA":
//          CA_do_DRCA();
//          break;
//
////        case "RHDI":
////          $errorArray[] = "regel ".$mr["regelnr"]." actie ".$data[6]." wordt genegeerd: opboeking stock";
////          $errors++;
////          break;
//        case "RHDI":
//        case "SOFF":
//          if ($data[36] <> "CRED")
//          {
//            $errorArray[] = "regel ".$mr["regelnr"]." SOFF geen CRED (".$data[36].")";
//            $errors++;
//          }
//          else
//            CA_do_D();
//          break;
//        case "WRTH" :
//          CA_do_L();
//          break;
        default:
          $errorArray[] = "regel ".$mr["regelnr"]." onbekende actiecode ".$data[4]." (Corporate Action Event Indicator)";
          $errors++;
      }
    }    
  }
  
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
  
}