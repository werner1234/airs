<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/29 11:10:47 $
 		File Versie					: $Revision: 1.4 $

 		$Log: jblux_reconFuncties.php,v $
 		Revision 1.4  2020/06/29 11:10:47  cvs
 		call 8716
 		
 		Revision 1.3  2020/03/13 15:26:43  cvs
 		call 7829
 		
 		Revision 1.2  2019/08/27 08:25:30  cvs
 		call 7829
 		
 		Revision 1.1  2019/08/23 12:36:29  cvs
 		call 7829
 		

 		
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
  while ($data = fgetcsv($handle, 4096, ";"))
  {
  
    $teller++;

    //if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over
    
    $pro_step = intval( $teller /$pro_multiplier );
    if ($prev_step < $pro_step)
    {
        //  $prb->moveStep($pro_step);
          $prev_step = $pro_step;
    }


    //$prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    //$prb->moveNext();
    $row = $data;
    $record = array("depot" => "b","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel

    $portefeuille = trim($data[2]);
    $rekeninnr    = trim($data[10]);
    $rekeninnrVal = trim($data[21]);

    $aantal       = trim($data[35]);
    $isin         = trim($data[16]);
    $bankcode     = $data[10];
    $saldo        = $data[35];
    $record["bankCode"]     = $bankcode;


    if (trim($data[5]) == 4)  // cash
    {
      $record["type"]      = "cash";
      $record["rekening"]  = $rekeninnr;
      $record["datum1"]    = substr($data[0],0,4)."-".substr($data[0],4,2)."-".substr($data[0],6,2);
      $record["valuta"]    = $rekeninnrVal;
      $record["bedragRaw"] = $aantal;
      $record["iban"]      = $data[19];
      $record["datum2"]    = "";
      $record["bedrag"]    = $saldo;
    }
    else
    {
      $valuta          = trim($data[8]);
      $record["fonds"] = $data[17];
      // eerst AIRS fondscode ophalen
      $bankCodeNotFound = true;
      if ($bankcode <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE JBcode='$bankcode' ";
//        debug($q,"JBcode $teller");
        if ($fRec = $db->lookupRecordByQuery($q))
        {
          $record["fonds"] = $fRec['Fonds'];
          $bankCodeNotFound = false;
        }
      }

      if ($bankCodeNotFound)
      {
        $q = "SELECT * FROM Fondsen WHERE ISINCode='$isin' AND Valuta ='".$valuta."'";
//        debug($q,"ISIN $teller");
        if ($fRec = $db->lookupRecordByQuery($q) AND $isin <> "")
        {
          $record["fonds"] = $fRec['Fonds'];
        }

      }
//      }
      $record["type"]         = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"]       = substr($data[0],0,4)."-".substr($data[0],4,2)."-".substr($data[0],6,2);
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;


      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koersRaw"]     = $data[23];
      $record["koers"]        = $data[23];
      $record["koersDatum"]   = substr($data[27],0,4)."-".substr($data[27],4,2)."-".substr($data[27],6,2);
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

  $data = fgetcsv($handle, 4096, ";");
  //debug($data);
  $validateStr1 = ($data[0] == "FROM_DATE");
  $validateStr2 = ($data[1] == "THIRD_CODE");

  
  if ( $validateStr1 AND $validateStr2 )
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen jblux positie bestand";
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