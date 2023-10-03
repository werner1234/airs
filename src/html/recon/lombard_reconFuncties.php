<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/20 06:19:49 $
 		File Versie					: $Revision: 1.4 $

 		$Log: lombard_reconFuncties.php,v $
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
  while ($data = fgetcsv($handle, 8000, ";"))
  {
  
    $teller++;
    if ($teller < 2) continue; // skip headerregels

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
    
    
    $portefeuille = $data[146];
    $valuta       = trim($data[62]);
    $rekeninnr    = $data[146];

    $isin         = trim($data[37]);
    $bankCode     = trim($data[152]);

    $extra        = array();
    if (substr($data[125],0,16) == "ORDINARY ACCOUNT" OR
        substr($data[125],0,12) == "INCOME TO BE"   )
    {
      $aantal              = trim($data[54]);  // was 238 call 4186
      if (substr($data[125],0,12) == "INCOME TO BE")
      {
        $record["rekening"]  = "TBR-".$rekeninnr;
        $record["bedrag"]    = $aantal;
        $record["bedragRaw"] = $aantal;
        // 2 regels aanmaken
        $extra["rekening"]  = $rekeninnr;
        $extra["bedrag"]    = $data[237] - $aantal;
        $extra["bedragRaw"] = $data[237] - $aantal;
      }
      else
      {
        $record["rekening"]  = $rekeninnr;
        $record["bedrag"]    = $data[237];
        $record["bedragRaw"] = $data[237];
      }

      $record["type"]      = "cash";
      $record["datum1"]    = "";
      $record["valuta"]    = $valuta;

      $record["DC"]        = ($aantal >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = $rekeninnr;

    } 
    else
    {
      if ($data[313] > 0)
      {
        $aantal = trim($data[237])/trim($data[313]);  // opties
      }
      else
      {
        $aantal = trim($data[237]);
      }

      $record["type"]         = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["bankCode"]     = $bankCode;
      $record["fonds"]        = $data[125];
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koersRaw"]     = $data[50];
      $record["koers"]        = $data[50];
    }
    $record["batch"] = $batch;
    $output[] = $record;
  
    $recon->addRecord($record);
    if ($extra["rekening"] <> "")  // tweede record aanmaken boij TBR mutatie
    {
      $record["rekening"]  = $extra["rekening"];
      $record["bedrag"]    = $extra["bedrag"];
      $record["bedragRaw"] = $extra["bedragRaw"];
//      $record["batch"] = $batch;
//      $output[] = $record;
//      $recon->addRecord($record);
    }

    
    
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

  $data = fgetcsv($handle, 8000, ";");
  //debug($data);
  $validateStr1 = trim($data[0]);
  
  
  if ( $validateStr1 == "FCDateF" )
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