<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/30 06:44:59 $
 		File Versie					: $Revision: 1.3 $

 		$Log: bnpbgl_reconFuncties.php,v $
 		Revision 1.3  2020/03/30 06:44:59  cvs
 		call 7605
 		
 		Revision 1.2  2019/08/27 08:25:09  cvs
 		call 7605
 		
 		Revision 1.1  2019/07/18 07:54:18  cvs
 		call 7605
 		

 		
*/


function recon_readBank($filename,$useISIN=false,$skipAirsRekening=false)
{
  global $prb, $batch, $recon, $airsOnly;
  
  
  $db = new DB();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
    
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;
  $startData = false;
  //$prb->show();
  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $row++;
    if ($data[0] == null)  // skip lege regels
    {
      continue;
    }
    if ($data[0] == "accrual_rule_e" ) // laatste headerregel
    {
     $startData = true;
     continue;
    }
    if (!$startData)  // als nog in de header
    {
      continue;
    }






  ////////////////////////////////////////////  
    $teller++;
    $pro_step += $pro_multiplier;
//    $prb->moveStep($pro_step);
//    $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
//    $prb->moveNext();
    ;
    
    $record = array("depot" => "b",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel

    $recType = ($data[38] == "Cash Account")?"cash":"sec";

    if ($recType == "sec")
    {
     $bankCode = $data[6];
     $isin = substr($data[6],0,12);
     $fondsVal = $data[54];
      $db = new DB();
      if ($data[5] <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE JBcode='$bankCode' ";
        if (!$fondsRec = $db->lookupRecordByQuery($q))
        {
          $q = "SELECT * FROM Fondsen WHERE ISINCode='".$isin."' AND  Valuta = '".$fondsVal."'";
          $fondsRec = $db->lookupRecordByQuery($q);
        }
      }
      
      
      $record["type"]         = "sec";
      $record["portefeuille"] = (int)$data[48];
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = bnpbglNumber($data[29]);
      $record["aantal"]       = bnpbglNumber($data[29]);
      $record["ISIN"]         = $isin;
      $record["bankCode"]     = $bankCode;
      $record["fonds"]        = $data[37];

      $record["valuta"]       = $fondsVal;
      $record["koersDatum"]   = bnpbglDate($data[78]);
      $record["koers"]        = bnpbglNumber($data[94]);

    }
    else
    {
      $rekeningRaw = explode("_",$data[6]);
      $record["type"]      = "cash";
      $record["rekening"]  = $rekeningRaw[0];
      $record["datum1"]    = "";
      $record["valuta"]    = $data[54];
      $record["bedragRaw"] = bnpbglNumber($data[29]);

      $record["datum2"]    = "";

      $record["bedrag"]    = bnpbglNumber($data[29]);
      
    }

    $output[] = $record;
    $recon->addRecord($record);
    
    
  }
  
  if ($record["type"] == "cash")
  {
    
  }
  else
  {
    echo "<li>AIRS data ophalen";
    ob_flush();flush();
    $recon->fillTableFormAIRS();
    
      
    echo "<li>AIRS portefeuilles ophalen";
    ob_flush();flush();
    $airsOnly = $recon->getAirsPortefeuilles();

  }
  
//  if (!$skipAirsRekening)
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();    
    $airsOnly = $recon->getAirsCashRekeningen();
  }  
  
  
  $recon->fillVB();
  
  $prb->hide();    
  unlink($filename);
  return $teller;
}

function validateFile($filename,$filename2)
{
  return true;
  global $error;
  $error = array();
  echo "<li>start validatie bestanden";
  ob_flush();flush();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }
  
  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;
     
    $data = convertFixedLine($dataRaw);
    if ($data[1] <> "SECURITYPOS" )
    {
        $error[] = "Bestand validatie mislukt, geen NIBC/SNS Spos bestand";
        return false;
    }
  }  
  fclose($handle);
  
  if (!$handle = @fopen($filename2, "r"))
  {
    $error[] = "FOUT bestand $filename2 is niet leesbaar";
    return false;
  }
  
  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;
     
    $data = convertFixedLine($dataRaw);
    if ( $data[1] <> "CASHPOS"    )
    {
        $error[] = "Bestand validatie mislukt, geen NIBC/SNS Cpos bestand";
        return false;
    }
  }  
  fclose($handle);
  
  
  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

function bnpbglDate($inDate)
{
  $d = explode("/",$inDate);
  return $d[2]."-".$d[1]."-".$d[0];
}

function bnpbglNumber($in)
{
  return str_replace(",", ".", $in);
}
function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta'";
  $DB->SQL($query);
  $record = $DB->lookupRecord();
  return $record["Rekening"];

}

?>