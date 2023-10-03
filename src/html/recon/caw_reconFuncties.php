<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/09 13:37:47 $
 		File Versie					: $Revision: 1.2 $

 		$Log: caw_reconFuncties.php,v $
 		Revision 1.2  2020/03/09 13:37:47  cvs
 		call 8464
 		
 		Revision 1.1  2020/02/05 13:57:56  cvs
 		call 8264
 		
 		Revision 1.4  2019/11/20 16:02:57  cvs
 		call 8025
 		
 		Revision 1.3  2019/10/09 09:59:20  cvs
 		call 8025
 		
 		Revision 1.2  2019/08/27 08:24:35  cvs
 		call 8025
 		
 		Revision 1.1  2019/08/23 12:36:10  cvs
 		call 8025
 		

 		
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
  $nrs = array(3,9,10,11,12,13,14);
  while ($data = fgetcsv($handle, 4096, ";"))
  {
//    debug($data);
    $teller++;
    if ($teller == 1)  // header overslaan
    {
      continue;
    }


    $data[2] = cawDate($data[2]);
    foreach ($nrs as $idx)
    {
      $data[$idx] = cawNumber($data[$idx]);
    }

//    debug($data);


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

    $portefeuille = trim($data[0]);
    $rekeninnrVal = trim($data[8]);
    $aantal       = trim($data[10]);
    $isin         = trim($data[5]);
    $datum        = trim($data[2]);
    $bankcode     = trim($data[6]);
    $koers        = trim($data[9]);
    $saldo        = trim($data[10]);
    $record["bankCode"]     = $bankcode;




    if (trim($data[4]) != ""  )  // cash
    {
      $record["type"]      = "cash";
      $record["rekening"]  = $portefeuille;
      $record["datum1"]    = $datum;
      $record["valuta"]    = $rekeninnrVal;
      $record["datum2"]    = "";
      $record["iban"]      = "";
      $record["bedrag"]    = $saldo;
      $record["koers"]     = $koers;
    }
    else
    {
      $valuta          = $rekeninnrVal;
      $record["fonds"] = trim($data[7]);

      // eerst AIRS fondscode ophalen
      $bankCodeNotFound = true;
      if ($bankcode <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE CAWcode='$bankcode' ";
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
      $record["datum1"]       = $datum;
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;



      $record["valuta"]       = $valuta;
      $record["koers"]        = $koers;
      $record["koersDatum"]   = "";
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

  //$data = fgetcsv($handle, 4096, ";");
  $data = fgetcsv($handle, 4096, ";");
  debug($data);
  $validateStr1 = ($data[0] == "root");
  $validateStr2 = ($data[1] == "shortname");


  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen caw positie bestand";
  }

    

  fclose($handle);
  
  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

function cawNumber($in)
{

  $in =  preg_replace('/[^0-9, \-]/', '', $in);
//
//  $in = str_replace(".", "", $in);
  return str_replace(",", ".", $in);
}

function cawDate($in)
{

  $s = explode("/",$in);
  return $s[2]."-".$s[1]."-".$s[0];
}

