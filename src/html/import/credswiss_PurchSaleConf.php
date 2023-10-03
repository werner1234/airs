<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/24 09:36:37 $
 		File Versie					: $Revision: 1.10 $

naar RVV 20201201
 		$Log: credswiss_PurchSaleConf.php,v $
 		Revision 1.10  2020/06/24 09:36:37  cvs
 		call 8711
 		
 		Revision 1.9  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.8  2015/12/01 07:28:25  cvs
 		update 2540, call 4352
 		
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

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("CS");

include_once 'credswiss_PurchSaleConf_functies.php';
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
//  debug($data);
    if (count($data) == 0)  continue;
   
    $mr["regelnr"]           = $x+7;    
    
    if ($data[69] == "FUND PRE-PAYM")
    {
      $errorArray[] = "[".$mr["regelnr"]."] FUND PRE-PAYM overgeslagen ";
      $errors++;
    } 
    elseif ($data[3] <> "NEWM")
    {
      $errorArray[] = "[".$mr["regelnr"]."] Functie geen NEWM (".$data[3].")";
      $errors++;
    } 
    else
    {
      $fondsValuta = trim($data[31]);
      $mr["bestand"]           = PurchSaleConf."-".$datum;
      $mr["Boekdatum"]         = CS_toDbDate($data[25]);
      //$mr["Rekening"]          = CS_getPortefeuille($data[x4]);
      $mr["bankTransactieId"]  = Trim($data[2]);
      $mr["settlementDatum"]   = CS_toDbDate($data[27]);

      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $mr["Valutakoers"]       = 1;  
      $mr["Omschrijving"]      = "";

      switch ($data[46])
      {

        case "REDM":
        case "SELL":
          //$mr["Portefeuille"]      = CS_getPortefeuille($data[x45]);  
          
          if ($rekRec = CS_getRekeningRec($data[56])) 
          {
            
            $mr["Rekening"] = $rekRec["Rekening"];
            $data["AirsRekValuta"] = $rekRec["Valuta"];
            
            $bankAfrValuta = ($data[128] <> "")?$data[128]:$data[124]; 
            if ($rekRec["Valuta"] <> $bankAfrValuta)
            {
              $errorArray[] = "[".$mr["regelnr"]."] valuta mismacth rekening A:".$rekRec["Valuta"].", B: ".$bankAfrValuta;
              $errors++;
            }
            
          }
          else
          {
            continue;
          }
          
          PS_do_V();
          break;
        case "BUYI":
        case "SUBS":
        case "DIVR":
          //$mr["Portefeuille"]      = CS_getPortefeuille($data[x39]);  
          
          $bankAfrValuta = ($data[128] <> "")?$data[128]:$data[124];
          if ($rekRec = CS_getRekeningRec($data[50])) 
          {
            $mr["Rekening"] = $rekRec["Rekening"];
            $data["AirsRekValuta"] = $rekRec["Valuta"];
            if ($rekRec["Valuta"] <> $bankAfrValuta)
            {
              $errorArray[] = "[".$mr["regelnr"]."] valuta mismacth rekening A:".$rekRec["Valuta"].", B: ".$bankAfrValuta;
              $errors++;
            }
          }
          else
          {
            continue;
          }

          PS_do_K();
          break;
        default:
          $errorArray[] = "[".$mr["regelnr"]."] Onbekende actiecode ".$data[46]." (CONFDET Buy/Sell Indicator)";
          $errors++;
      }
    }
  }
  
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
}