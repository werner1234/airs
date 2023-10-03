<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/07/17 07:03:57 $
 		File Versie					: $Revision: 1.6 $

 		$Log: abnv2_reconFuncties.php,v $
 		Revision 1.6  2019/07/17 07:03:57  cvs
 		call 7048
 		
 		Revision 1.5  2019/01/18 15:17:23  cvs
 		call 7048
 		
 		Revision 1.4  2019/01/16 13:34:32  cvs
 		call 7048
 		
 		Revision 1.3  2018/11/28 10:55:34  cvs
 		call 7048
 		
 		Revision 1.2  2018/11/26 13:47:01  cvs
 		call 7048
 		
 		Revision 1.1  2018/11/23 13:28:39  cvs
 		call 7048
 		


 		
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
  while ($data = fgetcsv($handle, 4096, ";"))
  {

    $ndx++;
    if ($ndx == 1)
    {
      continue; // skip headerregel
    }

  ////////////////////////////////////////////  
    $teller++;
    $pro_step += $pro_multiplier;

   //if ($teller < 3)
//   {
//     debug($data);
//   }

    $row = $data;
    
    $record = array("depot" => "b",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    if ($data[2] != "")
    {
     
      $db = new DB();

      $q = "SELECT * FROM Fondsen WHERE AABCode='".trim($data[2])."' OR ABRCode='".trim($data[2])."' ";
      if (!$fondsRec = $db->lookupRecordByQuery($q))
      {
       // $q = "SELECT * FROM Fondsen WHERE ISINCode='".$data[8]."' AND  Valuta = '".$data[9]."'";
       // $fondsRec = $db->lookupRecordByQuery($q);
      }

      $fonds = ($fondsRec["Fonds"] != "")?$fondsRec["Fonds"]:$data[4];
      $isin  = ($fondsRec["ISINCode"] != "")?$fondsRec["ISINCode"]:"XXX";
      $bankCode = ($fondsRec["AABCode"] != "")?$fondsRec["AABCode"]:$data[2];

      $record["type"]         = "sec";
      $record["portefeuille"] = (int) $data[0];
      $record["datum1"]       = $data[10];
      $record["datum2"]       = $data[10];
      $record["soort"]        = "";
      $record["aantalRaw"]    = $data[11];
      $record["aantal"]       = $data[11];
      $record["ISIN"]         = $isin;
      $record["bankCode"]     = $bankCode;
      $record["fileBankCode"] = $data[2];
      $record["fonds"]        = $fonds;
      $record["PE"]           = "";
      $record["valuta"]       = $data[3];
      $record["koersRaw"]     = $data[12];
      $record["koers"]        = $data[12];

    }
    else
    {
      $record["type"]      = "cash";
      $record["rekening"]  = (int)$data[0];
      $record["datum1"]    = "";
      $record["valuta"]    = $data[3];
      $record["bedragRaw"] = $data[13];
      $record["DC"]        = ($data[13] >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = "";
      $record["bedrag"]    = $data[13];
      
    }
    
      
    $output[] = $record;
    $recon->addRecord($record);
    
    
  }
  
//  if ($record["type"] == "cash")
//  {
//    echo "<li>in //CASH//";
//  }
//  else
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

function validateFile($filename)
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

  $data = fgetcsv($handle, 4096, ";");

  if ($data[0] != "PortfolioID" OR
      $data[1] != "PortfolioCurrency")
  {
    $error[] = "Bestand is geen ABN v2 ";
  }
  fclose($handle);

  return (Count($error) == 0);

} 		

function convertFixedLine($rawData,$debug=false)
{
  
  $data[1] = trim(textPart($rawData,1,15));
  
  
  if ($data[1] == "SECURITYPOS")
  {
    $data[3] = textPart($rawData,21,55);                // portefeuille
    $data[5] = ontnullen(textPart($rawData,60,79));     // Fondscode
    $data[7] = textPart($rawData,84,101);               // aantal stukken
    $data[7] = str_replace(",",".",$data[7]);

  }
  else
  {
    $data[5]  = textPart($rawData,60,77);               // saldo
    $data[5] = str_replace(",",".",$data[5]);
    $data[6]  = textPart($rawData,33,35);               // valuta
    $data[11] = textPart($rawData,21,32);  



  }
  if ($debug)
    listarray($data);
  return $data;
}

function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta'";
  $DB->SQL($query);
  $record = $DB->lookupRecord();
  return $record["Rekening"];

}

?>