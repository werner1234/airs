<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/11/28 10:52:32 $
 		File Versie					: $Revision: 1.2 $

 		$Log: mdz_reconFuncties.php,v $
 		Revision 1.2  2018/11/28 10:52:32  cvs
 		call 7282
 		

 		
*/



function recon_readBank($filename,$useISIN=false)
{
  global $prb, $batch, $recon, $airsOnly,$i;
  
  
  $db = new DB();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
    
  $pro_multiplier = $csvRegels/100;
  $teller = 0;
  $ndx= 0;
  $prev_step = 0;
  //$prb->show();
  while ($data = fgetcsv($handle, 4096, ","))
  {

    $teller++;
    if ($teller < 2)
    {
      continue; // sla header over
    }

//debug($data);
    $pro_step = intval( $teller /$pro_multiplier );
    if ($prev_step < $pro_step)
    {
        //  $prb->moveStep($pro_step);
          $prev_step = $pro_step;
    }


    //$prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    //$prb->moveNext();
    $row = $data;
    
    $record = array("depot" => "MDZ",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    $portefeuille = trim($data[$i["account_number"]]);
    $valuta       = trim($data[$i["instrument_currency"]]);
    $rekeninnr    = trim($data[$i["account_number"]]);
    $aantal       = trim($data[$i["nr_of_participations"]]);
    $isin         = trim($data[$i["isin"]]);
    $datum        = substr($data[$i["date"]],0,10);

    
    if ($isin == "cash" )  // bestaat niet in huidige API
    {
      
      $record["type"]      = "cash";
      $record["rekening"]  = $rekeninnr;
      $record["datum1"]    = "";
      $record["valuta"]    = $valuta;
      $record["bedragRaw"] = $aantal;
      $record["DC"]        = ($aantal >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = $rekeninnr;
      $record["bedrag"]    = $aantal;
    } 
    else
    {
      $record["type"]         = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["bankCode"]     = $isin.$valuta;  // wordt niet aangeleverd
      $record["fonds"]        = "ISIN: $isin $valuta";
      $record["valuta"]       = $valuta;

    }
    $record["batch"] = $batch;
    $output[] = $record;
  
    $recon->addRecord($record);
    
    
  }
  
  echo "<li>AIRS data ophalen";
  ob_flush();flush();
  $recon->fillTableFormAIRS();
  
    
  echo "<li>AIRS portefeuilles ophalen";
  ob_flush();flush();
  $airsOnly = $recon->getAirsPortefeuilles();

  
  echo "<li>AIRS rekeningnummers ophalen";
  ob_flush();flush();
  $airsOnly = $recon->getAirsCashRekeningen();
  
  $recon->fillVB();
  
  //$prb->hide();    
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

  $data = fgetcsv($handle, 4096, ",");
  //debug($data);
  $validateStr1 = ($data[0] == "account_number");
  $validateStr2 = ($data[1] == "isin");
  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen MDZ bestand";
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