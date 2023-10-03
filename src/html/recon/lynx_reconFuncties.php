<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/11/22 09:13:08 $
 		File Versie					: $Revision: 1.2 $

 		$Log: lynx_reconFuncties.php,v $
 		Revision 1.2  2017/11/22 09:13:08  cvs
 		veld mapping herzien
 		
 		Revision 1.1  2017/09/20 06:20:06  cvs
 		megaupdate 2722
 		
 		Revision 1.3  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 	
 		
*/
function reformat($data)
{
  foreach ($data as $item)
  {
    $out[] = trim($item);
  }
  return $out;
}


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

  while ($data = fgetcsv($handle, 4096, ","))
  {
    //if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over

    $data = reformat($data);
  ////////////////////////////////////////////  
    $teller++;

//    if ($teller < 6 )
//    {
//      debug($data);
//      continue;
//    }
//
//    exit;
    $row = $data;
    
    $record = array("depot" => "b",                // regel uit bankbestand
                    "batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    $portefeuille = trim($data[1]);
    $valuta       = trim($data[4]);
    $rekeninnr    = trim($data[1]);
    $aantal       = trim($data[7]);
    $isin         = trim($data[3]);
    $fonds         = trim($data[5]);
    $bankcode        = $data[12];
    
    if (trim($data[0]) == "CP" )
    {
      $aantal       = trim($data[7]);
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
      $aantal = trim($data[6]);
      $record["type"]         = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["bankCode"]     = $bankcode;
      $record["fonds"]        = $fonds;
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koersRaw"]     = 0;
      $record["koers"]        = 0;
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
  $validateStr1 = "Type";
  $validateStr2 = "Account";
  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste row
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen LYNX bestand";
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