<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/05/18 10:52:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: optimix_reconFuncties.php,v $
 		Revision 1.2  2018/05/18 10:52:13  cvs
 		call 6880
 		
 		Revision 1.1  2018/05/14 08:56:36  cvs
 		call 6880
 		
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


    $pro_step = intval( $teller /$pro_multiplier );
    if ($prev_step < $pro_step)
    {
        //  $prb->moveStep($pro_step);
          $prev_step = $pro_step;
    }


    //$prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    //$prb->moveNext();
    $row = $data;
    
    $record = array("depot" => "OPT",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel


    $d = explode("-",$data[6]);
    $portefeuille = trim($data[0]);
    $valuta       = trim($data[5]);
    $rekeninnr    = trim($data[0]);
    $aantal       = trim($data[7]);
    $isin         = trim($data[3]);
    $bankCode     = trim($data[2]);
    $koersDatum   = "20".$d[2]."-".$d[0]."-".$d[1];
    $koers        = $data[8];
    $fonds        = trim($data[4]);


    if ($aantal == "" )
    {
      
      $record["type"]      = "cash";
      $record["rekening"]  = $rekeninnr;
      $record["datum1"]    = "";
      $record["valuta"]    = $valuta;
      $record["bedragRaw"] = $data[11];
      $record["DC"]        = ($data[11] >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = $rekeninnr;
      $record["bedrag"]    = $data[11];
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
      $record["bankCode"]     = $bankCode;  // wordt niet aangeleverd
      $record["fonds"]        = $fonds;
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
  $validateStr1 = ($data[0] == "Portfolio ID");
  $validateStr2 = ($data[1] == "Reporting ISO");
  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Optimix bestand";
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