<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/05/16 13:35:41 $
 		File Versie					: $Revision: 1.5 $

 		$Log: ubs_PurchSaleConf.php,v $
 		Revision 1.5  2018/05/16 13:35:41  cvs
 		call 6894
 		
 		naar RVV 20201104
 		


*/


include_once 'ubs_PurchSaleConf_functies.php';
function  do_PurchSaleConf($datum,$fields,$fieldData)
{
  
  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "PurchSaleConf"."-".$datum;
  $stats["regels"] = count($fieldData);
  
  for ($x=0; $x < count($fieldData); $x++ )
  {
    
    $mr = array();
    $data = $fieldData[$x];

    if (count($data) == 0)  continue;
//   debug($data);
    $mr["regelnr"]           = $x+9;
    if (!is_numeric($data[2]))
    {
      continue;
    }
//    debug($data, $x+9);
    if ($data[20] == "1A" OR $data[20] == "1E")
    {
      $errorArray[] = "[".$mr["regelnr"]."] admin. transactie overgeslagen ";
      $errors++;
    }
    elseif ($data[1] <> "NEWM")
    {
      $errorArray[] = "[".$mr["regelnr"]."] Functie geen NEWM (".$data[1].")";
      $errors++;
    } 
    else
    {
      $fondsValuta = trim($data[12]);
      $mr["bestand"]           = "PurchSaleConf-".$datum;
      $mr["Boekdatum"]         = UBS_toDbDate($data[7],true);
      if ($data[20] == "OL" OR $data[20] == "OM")
      {
        if ($rekNr = UBS_getRekening($data[53], "MEM"))
        {
          $mr["Rekening"] = $rekNr;
        }
      }
      else
      {
        $rekVal = ($data[54] != "")?$data[54]:$data[71];
        if ($rekNr = UBS_getRekening($data[63],$rekVal))
        {

          $mr["Rekening"] = $rekNr;
        }
        else
        {
          continue;
        }
      }

      $mr["bankTransactieId"]  = Trim($data[0]);
      $mr["settlementDatum"]   = UBS_toDbDate($data[9],true);

      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $mr["Valutakoers"]       = 1;  
      $mr["Omschrijving"]      = "";
      if ( $data[33] == "OPC")
      {
        switch ($data[20])
        {
          case "DB":
          case "QB":
          case "OM":
            PS_do_AS();
            break;
          case "DS":
          case "QS":
          case "OL":
            PS_do_VS();
            break;
          case "CB":
          case "PB":
            PS_do_AO();
            break;
          case "CS":
          case "PS":
            PS_do_VO();
            break;
          case "BC":
            $errorArray[] = "[".$mr["regelnr"]."] future tranactie ".$data[20].": handmatig boeken";
            $errors++;
            break;
          default:
            $errorArray[] = "[".$mr["regelnr"]."] onbekende Optie transactie ".$data[20].": handmatig boeken";
            $errors++;
        }
        continue;
      }

      switch ($data[15])
      {
        //case "REDM":
        case "SELL":
          PS_do_V();
          break;
        case "BUYI":
        //case "SUBS":
        //case "DIVR":
          PS_do_K();
          break;
        default:
          $errorArray[] = "[".$mr["regelnr"]."] Onbekende actiecode ".$data[15]." (CONFDET Buy/Sell Indicator)";
          $errors++;
      }
    }
  }
  
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
}