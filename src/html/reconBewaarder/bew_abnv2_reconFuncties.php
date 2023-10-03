<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/18 06:44:47 $
 		File Versie					: $Revision: 1.1 $

 		$Log: bew_abnv2_reconFuncties.php,v $
 		Revision 1.1  2020/05/18 06:44:47  cvs
 		call 8616
 		
 		Revision 1.1  2017/09/20 06:21:04  cvs
 		megaupdate 2722
 		
 		Revision 1.5  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

function recon_readBank($filename,$useISIN=false)
{
  global $prb, $batch, $recon, $airsOnly;

  
  $db = new DB();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
    
  $pro_multiplier = $csvRegels/100;
  $row = 0;
  $ndx= 0;
  $teller = 0;
  $prev_step = 0;
  //$prb->show();
  echo "<li>inlezen bankbestand";
  ob_flush();flush();
  $db = new DB();
  while ($data = fgetcsv($handle, 4096, ";"))
  {
    if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over

    $teller++;
    if ($teller == 1)
    {
      continue; // header overslaan
    }
    $pro_step = intval( $teller /$pro_multiplier );
    if ($prev_step < $pro_step)
    {
          //$prb->moveStep($pro_step);
          $prev_step = $pro_step;
    }


    $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    //$prb->moveNext();
    $row = $data;
    
    $record = array("depot" => "b",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel

    if ($data[2] == "")
    {
      
      $record["type"]      = "cash";
      $record["rekening"]  = (int)$data[0];
      $record["datum1"]    = "";
      $record["valuta"]    = $data[3];
      $record["bedragRaw"] = $data[13];
      $record["DC"]        = ($data[13] >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = (int)$data[0];
      $record["bedrag"]    = $data[13];
    } 
    else
    {

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
      $record["fonds"]        = $fonds;
      $record["PE"]           = "";
      $record["valuta"]       = $data[3];
      $record["koersRaw"]     = $data[12];
      $record["koers"]        = $data[12];
    }
    $record["batch"] = $batch;
    $output[] = $record;
  
    $recon->addRecord($record);
    
    
  }
  echo "<li>AIRS data ophalen";
  ob_flush();flush();
  //$recon->fillTableFormAIRS();
  
  echo "<li>AIRS portefeuilles ophalen";
  ob_flush();flush();
  $airsOnly = $recon->getAirsPortefeuilles();
  
  echo "<li>AIRS rekeningnummers ophalen";
  ob_flush();flush();
  $airsOnly = $recon->getAirsCashRekeningen();
  
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

  $validateStr1 = ($data[0] == "PortfolioID");
  $validateStr2 = ($data[1] == "PortfolioCurrency");
  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen ABNv2 bestand";
  }

    

  fclose($handle);
  
  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
} 		

?>