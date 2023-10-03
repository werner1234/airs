<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/12 09:32:47 $
 		File Versie					: $Revision: 1.9 $

 		$Log: snssec_reconFuncties.php,v $
 		Revision 1.9  2018/08/12 09:32:47  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/09/21 08:30:24  cvs
 		call 5200
 		
 		Revision 1.7  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/24 12:16:24  cvs
 		dbs 3330
 		
 		Revision 1.3  2014/12/24 09:54:51  cvs
 		call 3105
 		
 		Revision 1.2  2014/11/21 10:04:27  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/10/17 14:29:31  cvs
 		dbs 2745
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
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

  //$prb->show();
  
  while (!feof($handle))
  {
    $ndx++;
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;
     
    $data = convertFixedLine($dataRaw);
  ////////////////////////////////////////////  
    $teller++;
    $pro_step += $pro_multiplier;
//    $prb->moveStep($pro_step);
//    $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
//    $prb->moveNext();
    $row = $data;
    
    $record = array("depot" => "b",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    if ($data[1] == "SECURITYPOS")
    {
     
      $db = new DB();
      if ($data[5] <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE snsSecCode='$data[5]' ";
        if (!$fondsRec = $db->lookupRecordByQuery($q))
        {
          $q = "SELECT * FROM Fondsen WHERE ISINCode='".$data[8]."' AND  Valuta = '".$data[9]."'";
          $fondsRec = $db->lookupRecordByQuery($q);
        }
      }
      
      
      $record["type"]         = "sec";
      $record["portefeuille"] = $data[3];
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $data[7];
      $record["aantal"]       = $data[7];
      $record["ISIN"]         = $fondsRec["ISINCode"];
      $record["bankCode"]     = $data[5];
      $record["fonds"]        = $fondsRec["Fonds"];
      $record["PE"]           = "";
      $record["valuta"]       = $fondsRec["Valuta"];
      //$record["koersRaw"]     = $data[11]/$data[8];
      //$record["koers"]        = $data[11]/$data[8];

    }
    else
    {
      $record["type"]      = "cash";
      $record["rekening"]  = $data[11];
      $record["datum1"]    = "";
      $record["valuta"]    = $data[6];
      $record["bedragRaw"] = $data[5];
      $record["DC"]        = ($data[5] >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = $data[11];
      $record["bedrag"]    = str_replace(",",".",$data[5]);
      
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
  
  if (!$skipAirsRekening)
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

function convertFixedLine($rawData,$debug=false)
{
  
  $data[1] = textPart($rawData,1,15);
  if ($data[1] == "SECURITYPOS")
  {
    $data[3] = textPart($rawData,21,55);                // portefeuille
    $data[5] = ontnullen(textPart($rawData,60,79));     // Fondscode
    $data[7] = textPart($rawData,84,101);               // aantal stukken
    $data[7] = str_replace(",",".",$data[7]);
    $data[8] = textPart($rawData,422,433);               // ISIN
    $data[9] = textPart($rawData,109,112);               // valuta
  }
  else
  {
    $data[5]  = textPart($rawData,60,77);               // saldo
    $data[5] = str_replace(",",".",$data[5]);
    $data[6]  = textPart($rawData,78,80);               // valuta
    $data[11] = textPart($rawData,21,29);               // RekeneningNr
    $data[20] = textPart($rawData,196,213);               // aantal stukken
    $data[20] = str_replace(",",".",$data[20]);
    $data[21] = textPart($rawData,214,231);               // aantal stukken
    $data[21] = str_replace(",",".",$data[21]);
    
    $data[5] = $data[5] + $data[21] - $data[20];

  }
  if ($debug)
    listarray($data);
  return $data;
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