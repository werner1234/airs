<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/08/22 14:00:18 $
 		File Versie					: $Revision: 1.5 $

 		$Log: jb_reconFuncties.php,v $
 		Revision 1.5  2018/08/22 14:00:18  cvs
 		call 5923
 		
 		Revision 1.4  2018/08/21 09:43:34  cvs
 		call 7049
 		
 		Revision 1.3  2018/06/08 09:37:40  cvs
 		call 5923
 		
 		Revision 1.2  2018/04/30 13:53:53  cvs
 		call 5923
 		
 		Revision 1.4  2017/09/20 06:19:49  cvs
 		megaupdate 2722
 		
 		Revision 1.3  2016/05/04 12:41:44  cvs
 		kolomwijzigingen na wijzigen aanleverbestanden
 		
 		Revision 1.2  2016/04/20 12:28:14  cvs
 		no message
 		
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
  while ($data = fgetcsv($handle, 4096, "\t"))
  {
  
    $teller++;

//    if ($teller > 4) break; // lees 4 records

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

    $portefeuille = trim($data[1]);

    $rekeninnrVal = trim($data[86]);
    $valuta       = trim($data[12]); // let op alleen bij geld rekeningen
    $aantal       = trim($data[75]);
    $isin         = trim($data[9]);
    $bankcode     = $data[8];
    $saldo        = $data[89];
    $record["bankCode"]     = $bankcode;


    $rekeninnr    = substr(str_replace(" ","", trim($data[99])),-12);
    if (strlen($rekeninnr) < 11)
    {
      $rekeninnr  = trim($data[1]);
    }


    if (trim($data[85]) != 0 AND $data[97] == 2 )  // cash
    {
      $record["type"]      = "cash";
      $record["rekening"]  = $rekeninnr;
      $record["datum1"]    = "";
      $record["valuta"]    = $rekeninnrVal;
      $record["bedragRaw"] = $aantal;
      $record["DC"]        = ($aantal >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = $rekeninnr;
      $record["bedrag"]    = $saldo;
    }
    else
    {
      $valuta          = trim($data[79]);
      $record["fonds"] = $data[10];
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
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;


      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koersRaw"]     = $data[76];
      $record["koers"]        = $data[76];
      $record["koersDatum"]   = substr($data[77],0,4)."-".substr($data[77],5,2)."-".substr($data[77],7,2);
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

  $data = fgetcsv($handle, 4096, "\t");
  //debug($data);
  $validateStr1 = substr($data[4],0,2);
  $validateStr2 = $data[75];

  
  if ( $validateStr1 == "20" AND isNumeric($validateStr2) )
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen JB positie bestand";
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