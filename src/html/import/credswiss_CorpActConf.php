<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/17 07:12:49 $
 		File Versie					: $Revision: 1.21 $
naar RVV 20121202
 		$Log: credswiss_CorpActConf.php,v $
 		Revision 1.21  2020/06/17 07:12:49  cvs
 		call 8671
 		
 		Revision 1.20  2020/06/10 12:58:34  cvs
 		call 8671
 		
 		Revision 1.19  2020/01/15 14:59:09  cvs
 		call 8298
 		
 		Revision 1.18  2019/11/22 08:39:21  cvs
 		call 8166
 		
 		Revision 1.17  2019/10/29 07:44:05  cvs
 		case mrgr
 		
 		Revision 1.16  2018/09/11 13:48:50  cvs
 		call 3775
 		
 		Revision 1.15  2018/01/31 08:04:17  cvs
 		CONV toegevoegd
 		
 		Revision 1.14  2018/01/12 15:32:13  cvs
 		call 6502
 		
 		Revision 1.13  2015/10/02 13:49:06  cvs
 		*** empty log message ***
 		
 		Revision 1.12  2015/06/11 16:13:12  cvs
 		*** empty log message ***
 		
 		Revision 1.11  2015/05/11 13:36:52  cvs
 		*** empty log message ***
 		
 		Revision 1.10  2015/05/08 12:08:58  cvs
 		*** empty log message ***
 		
 		Revision 1.9  2015/05/06 09:37:24  cvs
 		*** empty log message ***
 		
 		Revision 1.8  2015/03/26 09:48:19  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/01/05 14:45:36  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2014/12/24 09:54:51  cvs
 		call 3105
 		
 		Revision 1.4  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/20 12:48:18  cvs
 		dbs 2746
 		
 		Revision 1.2  2014/11/13 10:43:10  cvs
 		dbs2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/


include_once 'credswiss_CorpActConf_functies.php';
function  do_CorpActConf($datum,$fields,$fieldData)
{

  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;


  $actArray = array(
    "CAPD",
    "BONU",
    "CHAN",
    "SPLF",
    "CONV",
    "BUY",
    "EXOF",
    "MRGR",
    "LIQU",
    "MCAL",
    "BIDS",
    "REDM",
    "DVCA",
    "SHPR",
    "DVOP",
    "DRIP",
    "INTR",
    "DRCA",
    "RHDI",
    "SOFF",
    "WRTH",
    "OTHR"
  );
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
//debug($data);
    if(count($data) == 0) continue;
    
   
    $mr["regelnr"]           = $x+7;    
    if ($data[4] <> "NEWM")
    {
      $errorArray[] = "[".$mr["regelnr"]."] Functie geen NEWM (".$data[4].")";
      $errors++;
    } 
    else
    {

      if (!in_array($data[6], $actArray))
      {
        $errorArray[] = "regel ".$mr["regelnr"]." onbekende actiecode ".$data[6]." (Corporate Action Event Indicator)";
        $errors++;
        continue;
      }

      $fondsValuta = trim($data[24]);

      $mr["bestand"]           = "CorpActConf-".$datum;
      $mr["Boekdatum"]         = CS_toDbDate($data[7]);
      if (strtoupper($data[213]) == "VARIATION MARGIN")
      {
        $mr["Rekening"]          = CS_getPortefeuille($data[80]).trim($data[83]);
      }
      else
      {
        $mr["Rekening"]          = CS_getPortefeuille($data[80]).trim($data[113]);
      }

      if ($mr["Rekening"] == "" )
      {
        $mr["Rekening"]          = CS_getPortefeuille($data[12]."MEM");
      }

      if (!CS_checkRekeningNr() ) 
      {
        continue;
      }
      
      $mr["bankTransactieId"]  = Trim($data[3]);
      $mr["settlementDatum"]   = CS_toDbDate($data[189]);

      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $mr["Valutakoers"]       = 1;  
      //$mr["Omschrijving"]      = makeOmschrijving(array($data[23],$data[x24],$data[x25],$data[x26],$data[x27],$data[x28]));
      $mr["Omschrijving"]      = "";
      //$mr["waardeOrg"]         = (float)$data[82];
//      $mr["waardeAfr"]         = (float)$data[80];

      switch ($data[6]) // vergeet niet actie ook in de $actARray te plaatsen!!
      {
        case "CAPD":
          CA_do_DV("CAPD");
          break;
        case "BONU":
          CA_do_D();
          break;
        case "CHAN":
        case "SPLF":
        case "CONV":
          CA_do_CONV();
          break;
        case "BUY":
          debug($data);
          CA_do_BUY();
          break;
        case "EXOF":
        case "MRGR":

          if ( substr($data[18],0,8) ==  "FUND PRE" AND $data[37] == "DEBT"  AND $data[60] <> "")
          {
            CA_do_A();
          }  
          elseif ( substr($data[18],0,8) ==  "FUND PRE" AND $data[37] == "CRED"  AND $data[60] == "")
          {
            $errorArray[] = "regel ".$mr["regelnr"]." actie ".$data[6]." wordt genegeerd: opboeking fund prepayment";
            $errors++;
          }
          elseif ($data[37] <> "" AND $data[56] <> "")
          {
            CA_do_CONV();
          }
          
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." EXOF incompleet (Corporate Action Event Indicator)";
            $errors++;
          }
          break;
          
        case "LIQU":
        case "MCAL":
          if ($data[37] == "DEBT" AND $data[60] == "")
          {
            CA_do_V("LIQU");
          }
          elseif ($data[84] <> 0)
          {
            CA_do_DV("DV");
          }
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." LIQU - Uitval (Corporate Action Event Indicator)";
            $errors++;
          }
          break;
        case "BIDS":
          if ($data[60] == "")
          {
            CA_do_V("BIDS");
          }
          break;
        case "REDM":
          if ($data[60] == "")
          {
            CA_do_V("REDM");
          }
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." REDM met tweede stukregel  (Corporate Action Event Indicator)";
            $errors++;
          }
          
          break;
        case "DVCA":
        case "SHPR":
          CA_do_DV("DV");
          break;
        case "DVOP":
          if ($data[114] <> 0)
          {
            CA_do_DV("DV");
          }
          elseif ($data[37] == "CRED")
          {
            CA_do_D();
          }
          elseif ($data[37] == "DEBT")
          {
            CA_do_L();
          }
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." onmogelijke DVOP ";
            $errors++;
          }
          break;
        case "DRIP":
          if ($data[114] <> 0)
          {
            CA_do_DV("DRIP");
          }
          else if (trim($data[114]) == "" AND trim($data[40]) <> "" AND trim($data[61]) == "")
          {
            CA_do_L();
          }  
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." DRIP met 2 securities ".$data[6]." (Corporate Action Event Indicator)";
            $errors++;
          }
          break;
        case "INTR":
          CA_do_R();
          break;
        case "DRCA":
          CA_do_DRCA();
          break;
       
//        case "RHDI":
//          $errorArray[] = "regel ".$mr["regelnr"]." actie ".$data[6]." wordt genegeerd: opboeking stock";
//          $errors++;
//          break;
        case "RHDI":
        case "SOFF":
          if ($data[37] <> "CRED")
          {
            $errorArray[] = "regel ".$mr["regelnr"]." SOFF geen CRED (".$data[37].")";
            $errors++;
          }
          else
            CA_do_D();
          break;
        case "WRTH" :
          CA_do_L();
          break;

        case "OTHR":
          if (strtoupper($data[213]) == "VARIATION MARGIN")
          {
            continue; // overslaan
          }
          else
          {
            $errorArray[] = "regel ".$mr["regelnr"]." onbekende actiecode ".$data[6]." (Corporate Action Event Indicator)";
            $errors++;
          }

          break;
        default:
          $errorArray[] = "regel ".$mr["regelnr"]." onbekende actiecode ".$data[6]." (Corporate Action Event Indicator)";
          $errors++;
      }
    }    
  }
  
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
  
}