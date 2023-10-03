<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/03/22 12:38:24 $
 		File Versie					: $Revision: 1.2 $

 		$Log: pictet_reconFuncties.php,v $
 		Revision 1.2  2019/03/22 12:38:24  cvs
 		call 6686
 		
 		Revision 1.1  2015/12/01 09:03:06  cvs
 		update 2540, call 4352
 		
 		Revision 1.2  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/04/21 13:32:04  cvs
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
  $teller=0;
  $prev_step = 0;
  //$prb->show();
  while ($data = fgetcsv($handle, 2000, "\t"))
  {
    
    
    
    
  
    $teller++;
    if ($teller < 4) continue; // skip headerregels
    

    ////////////////////////////////////////////  
    if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over
    
    $pro_step = intval( $teller /$pro_multiplier );
    if ($prev_step < $pro_step)
    {
        //  $prb->moveStep($pro_step);
          $prev_step = $pro_step;
    }


    //$prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    //$prb->moveNext();
    $row = $data;
    
    $record = array("depot" => "b",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    $parts = explode(".",$data[56]);
    $portefeuille = intval($parts[0])."-".$parts[1];
    $valuta       = trim($data[2]);
    $rekeninnr    = $portefeuille;
    $aantal       = trim($data[3]);
    $isin         = trim($data[5]);
    $bankCode     = trim($data[9]);
    
    if ($bankCode == "" )
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
      $record["bankCode"]     = $bankCode;
      $record["fonds"]        = $data[4];
      $record["PE"]           = "";
      $record["valuta"]       = "XXXX";
      $record["koersRaw"]     = $data[11];
      $record["koers"]        = $data[11];
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

  $data = fgetcsv($handle, 2000, "\t");
  //debug($data);
  $validateStr1 = substr($data[0],0,16);
  $validateStr2 = strstr($data[0],"P3DET");
  
  if ( $validateStr1 == "HEADER12.PL3L951" AND $validateStr2 != false)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Pictet positie bestand";
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