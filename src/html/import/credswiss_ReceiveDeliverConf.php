<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/22 08:39:21 $
 		File Versie					: $Revision: 1.6 $

 		$Log: credswiss_ReceiveDeliverConf.php,v $
 		Revision 1.6  2019/11/22 08:39:21  cvs
 		call 8166
 		
 		Revision 1.5  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.4  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/01/05 14:45:36  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/20 12:48:18  cvs
 		dbs 2746
 		
 		Revision 1.1  2014/11/14 14:34:19  cvs
 		dbs 2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/

include_once ('credswiss_ReceiveDeliverConf_functies.php');

function  do_ReceiveDeliverConf($datum,$fields,$fieldData)
{

  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "ReceiveDeliverConf"."-".$datum;
  $stats["regels"] = count($fieldData);
  
  for ($x=0; $x < count($fieldData); $x++ )
  {
    
    $mr = array();
    $data = $fieldData[$x];
    
    if (count($data) == 0)  continue;

    $mr["regelnr"]           = $x+7;    
    $mr["bestand"]           = "ReceiveDeliverConf-".$datum;
    $mr["Boekdatum"]         = RD_boekdatum($data);
    $mr["Rekening"]          = CS_getPortefeuille($data[38])."MEM";  // voor do_L en do_D
    if (!CS_checkRekeningNr()) 
    {
      continue;
    }
    $mr["bankTransactieId"]  = Trim($data[2]);
    $mr["settlementDatum"]   = CS_toDbDate($data[20]);
    $mr["Aantal"]            = $data[36];
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr["Valuta"]            = $data[34];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = "";
    $Isin              = $data[26];
    if (!$fonds = _getFonds($Isin,$mr["Valuta"]))  // als fonds niet bestaat dan skippen
    {
      continue;
    }
    $mr["Fonds"]             = $fonds["Fonds"];
    $fondsKoers              = _getFondsKoers($fonds["Fonds"],$mr["Boekdatum"]);
    $mr["Fondskoers"]        = $fondsKoers["Koers"];
    switch ($data[1])
    {
        case "MT544":
          RD_do_D();
          break;
        case "MT545":
          RD_do_DA();
          break;
        case "MT546":
          RD_do_L();
          break;
        case "MT547":
          RD_do_VA();
          break;
        default:
          $errorArray[] = "[".$mr["regelnr"]."] Onbekende actiecode ".$data[1]." ";
          $errors++;
     
    }
  }
  
  $stats["fouten"] = (int)$errors;
  $stats["controle"] = implode("<br/>",$meldArray);
  $stats["errors"] = implode("<br/>",$errorArray);
  $statsArray[] = $stats;
}