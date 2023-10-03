<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/20 06:21:04 $
 		File Versie					: $Revision: 1.1 $

 		$Log: bew_binck_reconFuncties.php,v $
 		Revision 1.1  2017/09/20 06:21:04  cvs
 		megaupdate 2722
 		

 		
*/

function recon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly;
  
  
  $db = new DB();
  
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
  
  $dbFonds = new DB();
  
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;

  //$prb->show();
  while ($data = fgetcsv($handle, 4096, ","))
  {
    //listarray($data);
    
    if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over
    if (trim($data[4]) == "DIV") continue;  // DIV boekingen overslaan  2008-09-26
  
  ////////////////////////////////////////////  
    $teller++;
    $pro_step += $pro_multiplier;
//    $prb->moveStep($pro_step);
//    $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    
    $row = $data;
    $record = array("depot" => "b","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    $portefeuille = trim($data[0]);
    $rekeninnr    = trim($data[0]);
    $valuta       = trim($data[2]); // let op alleen bij geld rekeningen
    $aantal       = trim($data[9]);
    $isin         = trim($data[3]);
    $binck        = $data[16];
    
    $record["bankCode"]     = $binck;
    
    if (trim($data[15]) == "")  // cash
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
      
      
      $record["fonds"] = $data[17];
      if ($data[4] == "CALL" OR $data[4] == "PUT")  // opties
      {
        $split = explode(" ", $data[17]);

        $end = count($split);
        $binckCode = $split[0]." %".$split[$end-4]." ".$split[$end-3]." ".$split[$end-2]." ".$split[$end-1];

        $q = "SELECT * FROM Fondsen WHERE binckCode LIKE '".$binckCode."' ";
        if ($fRec = $db->lookupRecordByQuery($q))
        {
          $record["bankCode"] = $fRec['binckCode'];
          $record["fonds"]    = $fRec['Fonds'];
        }  
      }
      else // aandelen etc
      {
        
        // eerst AIRS fondscode ophalen
        
        if ($binck <> "")
        {
          $q = "SELECT * FROM Fondsen WHERE binckCode='$binck' ";
          if ($fRec = $db->lookupRecordByQuery($q))
          {
            $record["fonds"] = $fRec['Fonds'];
          }  
        }
        else
        {
          $q = "SELECT * FROM Fondsen WHERE ISINCode='$isin' AND Valuta ='".$valuta."'";
          if ($fRec = $db->lookupRecordByQuery($q) AND $isin <> "")
          {
            $record["fonds"] = $fRec['Fonds'];
          }
  
        }
      }  
      $record["type"]         = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      
      
      $record["PE"]           = "";
      $record["valuta"]       = trim($data[7]);
      $record["koersRaw"]     = $data[8];
      $record["koers"]        = $data[8];
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
  //$airsOnly = $recon->getAirsPortefeuilles();

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
  global $error, $filetype;
  $error = array();
  echo "<li>start validatie bestanden";
  ob_flush();flush();
  if (!$handle = @fopen($filename, "r"))
  {
	$error[] = "FOUT bestand $filename is niet leesbaar";
	return false;
  }

  $data = fgetcsv($handle, 4096, ",");

  $validateStr1 = is_numeric($data[0]);     // portefeuille veld is numeriek
  $validateStr2 = (substr($data[1],0,2) == "20");  // datumveld begin met 20


  if ( $validateStr1 AND $validateStr2 )
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Binck bestand";
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