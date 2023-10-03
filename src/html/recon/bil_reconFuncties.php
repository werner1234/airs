<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/20 06:19:10 $
 		File Versie					: $Revision: 1.1 $

 		$Log: bil_reconFuncties.php,v $
 		Revision 1.1  2017/09/20 06:19:10  cvs
 		megaupdate 2722
 		
 		Revision 1.10  2017/04/03 12:04:41  cvs
 		no message
 		
 		Revision 1.9  2016/07/22 12:03:34  cvs
 		binckcode optie wildcard % stond verkeerd
 		
 		Revision 1.8  2016/05/11 14:08:49  cvs
 		call 4907
 		
 		Revision 1.7  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/03/26 09:47:00  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.2  2014/10/01 13:38:44  cvs
 		meenemen opties
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
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
  while ($data = fgetcsv($handle, 4096, "|"))
  {
    $teller++;

    $pro_step += $pro_multiplier;
//    $prb->moveStep($pro_step);
//    $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    
    $row = $data;
    $record = array("depot" => "b","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    
    $portefeuille = trim($data[0]);
    $rekeninnr    = trim($data[2]);
    $valuta       = trim($data[3]);
    $aantal       = trim($data[5]);
    $isin         = trim($data[27]);
    $bankCode     = $data[2];
    
    $record["bankCode"]     = $bankCode;
    
    if (trim($data[23]) == "Cash Account")  // cash
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
      $record["fonds"] = $data[21];
//      if ($data[4] == "CALL" OR $data[4] == "PUT")  // opties
//      {
//        $split = explode(" ", $data[17]);
//
//        $end = count($split);
//        $binckCode = $split[0]." %".$split[$end-4]." ".$split[$end-3]." ".$split[$end-2]." ".$split[$end-1];
//
//        $q = "SELECT * FROM Fondsen WHERE binckCode LIKE '".$binckCode."' ";
//        if ($fRec = $db->lookupRecordByQuery($q))
//        {
//          $record["bankCode"] = $fRec['binckCode'];
//          $record["fonds"]    = $fRec['Fonds'];
//        }
//      }
//      else // aandelen etc
      {
        
        // eerst AIRS fondscode ophalen
        
        if ($bankCode <> "")
        {
          $q = "SELECT * FROM Fondsen WHERE BILcode='$bankCode' ";
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
      $record["valuta"]       = $valuta;
      $record["koersRaw"]     = $data[6];
      $record["koers"]        = $data[6];
      $ds = explode("/",$data[36]);
      $record["koersDatum"]   = $ds[2]."-".$ds[1]."-".$ds[0];
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

  $data = fgetcsv($handle, 4096, "|");

  $validateStr1 = ($data[0] == "portfolio");
  $validateStr2 = ($data[1] == "val_date");
  $validateStr3 = ($data[2] == "instrument");

  if ( $validateStr1 AND $validateStr2 AND $validateStr3)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen BIL bestand";
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