<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/01 12:11:26 $
 		File Versie					: $Revision: 1.11 $

 		$Log: credswiss_CashPositions.php,v $
 		Revision 1.11  2020/07/01 12:11:26  cvs
 		call 8714
 		
 		Revision 1.10  2020/06/17 07:12:49  cvs
 		call 8671
 		
 		Revision 1.9  2020/06/12 06:19:55  cvs
 		call 8671
 		
 		Revision 1.8  2019/11/11 13:27:14  cvs
 		call 8245
 		
 		Revision 1.7  2018/10/03 11:29:41  cvs
 		importafwijking KNBA
 		
 		Revision 1.6  2018/09/11 14:12:34  cvs
 		call 7152
 		
 		Revision 1.5  2018/02/07 13:08:53  cvs
 		call 6578
 		
 		Revision 1.4  2015/10/02 13:49:06  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/06/11 16:13:12  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/05/08 12:08:58  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/03/26 09:48:19  cvs
 		*** empty log message ***
 		


*/

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("CS");

include_once 'credswiss_CashPositions_functies.php';
function  do_CashPositions($datum,$fields,$fieldData)
{
  
  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  
  $verwerkt =0;
  $errorArray = array();
  $NFEXArray = array();
  $stats= array();
//  debug($fields);
  $stats["module"] = "CashPositions-".$datum;
  $stats["regels"] = count($fieldData);
  for ($x=0; $x < count($fieldData); $x++ )
  {
    global $output;
    $mr = array();
    $data = $fieldData[$x];
    if (count($data) == 0 ) continue;
    if ($data[16] == "" )   continue;;  // geen transactiecode

    //debug($data);
    $mr["regelnr"]           = $x+7;


    $fondsValuta = trim($data[23]);

    $mr["bestand"]           = "CashPositions-".$datum;
    $mr["Boekdatum"]         = CP_boekdatum($data);
    $mr["Rekening"]          = $data[4].$data[9];
    if (!CS_checkRekeningNr())
    {
      continue;
     }

    //$mr["bankTransactieId"]  = Trim($data[3]);
    $mr["settlementDatum"]   = CS_toDbDate($data[11]);

    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr["Valutakoers"]       = 1;
    $mr["Omschrijving"]      = makeOmschrijving(array($data[19]));
//    $mr["waardeOrg"]         = 0;
//    $mr["waardeAfr"]         = 0;

    switch ($data[16])   // transactiecode
    {


      case "NTRF":
//        $errorArray[] = "regel ".$mr["regelnr"]." code ".$data[16]." overgeslagen. Controleren!";
//        continue;
//        break;
      case "NRTI":
      case "NDIV":
      case "S910":
      case "NSEC":
      case "NCOM":

        // evt rapporteren data 4/8/13/15

        continue;  // negeren
        break;
      case "NSTO":
        continue;  // negeren call 3525
        break;
      case "S101":
        if (stristr($data[22],"fee"))
        {
          $mr["Omschrijving"] = $data[22];
          CP_do_KNBA();
          $verwerkt++;
        }
        else
        {
          $errorArray[] = "regel ".$mr["regelnr"]." code S101 zonder fee";
          $errors++;
        }
        break;
      case "NCLR":
        if ($data[17] <> "NONREF")  // alleen NONREF mutaties meennemn
        {
          continue;
        }
        CP_do_Mutatie();
        $verwerkt++;
        break;
      case "NCHG":
        CP_do_Mutatie(makeOmschrijving(array($data[17],$data[19])));
        $verwerkt++;
        break;
      case "NFEX":
        $data["mr"] = $mr;
        $NFEXArray[] =$data;
        break;
      case "NTRN":
        CP_do_KNBA();
        $verwerkt++;
        break;
      case "NINT":
        CP_do_RENTE();
        $verwerkt++;
        break;


      case "NMSC":
        if ($data[19] == "Variation - margin")
        {
          CP_do_VMAR();
          $verwerkt++;
          break;
        }

        if ($data[19] == "Balance of closing entries"  )
        {
          CP_do_KNBA();
          $verwerkt++;
        }
        else
        {
          CP_do_Mutatie();
          $verwerkt++;
        }

        break;
      default:
        $errorArray[] = "regel ".$mr["regelnr"]." onbekende TT code ".$data[16]."";
        $errors++;
    }
    
  }    
  
  
  for ($x=0; $x < count($NFEXArray); $x++)
  {
      $NFEXPairs[$NFEXArray[$x][17]][] = $NFEXArray[$x];
  }
  foreach( $NFEXPairs as $dataSet)
  {
    CP_do_FX($dataSet);
    $verwerkt++;
    $verwerkt++;
  }
      
  if ($stats["regels"] - $verwerkt <> 0)
  {
    $errorArray[] = "Regels verwerkt ".$verwerkt;
  }
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
  
}