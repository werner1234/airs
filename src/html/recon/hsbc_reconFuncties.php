<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/08/19 14:19:16 $
 		File Versie					: $Revision: 1.3 $

 		$Log: hsbc_reconFuncties.php,v $
 		Revision 1.3  2019/08/19 14:19:16  cvs
 		call 8017
 		
 		Revision 1.2  2019/01/16 13:35:34  cvs
 		call 7347
 		
 		Revision 1.1  2018/11/23 13:35:41  cvs
 		call 7347
 		
 		Revision 1.5  2018/03/28 12:37:00  cvs
 		call 3503
 		
 		Revision 1.4  2018/01/03 16:23:59  cvs
 		validatie fail na aanpassing bestandsformaat
 		
 		Revision 1.3  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 	
 		
*/

function hsbc_recon_readBank($filename,$useISIN=false)
{
  global $prb, $batch, $recon, $airsOnly, $cronRun;
  
  
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
  $teller = 0;
  $prev_step = 0;
  //$prb->show();
  while ($line = fgets($handle))
  {

    if ($teller == 0)
    {
      $teller++;
      continue; //skip header
    }
    $line = stripBOM($line);
    if (substr($line,0,9) == "[Bestand:" )
    {
      $data = explode(";",substr(trim($line),9,-1));
      $data["type"] = "stukken";
    }
    else
    {
      $data = explode(";",substr(trim($line),7,-1));
      $data["type"] = "geld";

    }


//debug($data);
    

    if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over
    if (trim($data[4]) == "DIV") continue;  // DIV boekingen overslaan  2008-09-26
  
  ////////////////////////////////////////////  
    $teller++;
    
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
    


    
    if ($data["type"] == "geld" )
    {

      $valuta       = trim($data[3]);
      $rekeningnr    = trim($data[0]);
      $aantal       = str_replace(",",".",trim($data[1]));

      
      $record["type"]      = "cash";
      $record["rekening"]  = $rekeningnr;
      $record["datum1"]    = "";
      $record["valuta"]    = $valuta;
      $record["bedragRaw"] = $aantal;
      $record["DC"]        = ($aantal >= 0)?"D":"C";
      $record["datum2"]    = "";
      $record["iban"]      = $rekeningnr;
      $record["bedrag"]    = $aantal;
    } 
    else
    {
      $portefeuille = trim($data[0]);
      $aantal       = str_replace(",",".",trim($data[2]));
      $isin         = trim($data[1]);

      $fondsRec = getHSBCFonds($isin);

      $record["type"]         = "sec";
      $record["portefeuille"] = $portefeuille;
      $record["datum1"]       = "";
      $record["datum2"]       = "";
      $record["soort"]        = "";
      $record["aantalRaw"]    = $aantal;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["bankCode"]     = $isin;
      $record["fonds"]        = $fondsRec["Fonds"];
      $record["PE"]           = "";
      $record["valuta"]       = $fondsRec["Valuta"];
      $record["koersRaw"]     = 0;
      $record["koers"]        = 0;
    }
    $record["batch"] = $batch;
    $output[] = $record;
if ($record["portefeuille"]  == "7198339002")
{
  debug($record);
}
    $recon->addRecord($record);
    
    
  }
  if (!$cronRun)
  {
    echo "<li>AIRS data ophalen";
    ob_flush();flush();
  }
  $recon->fillTableFormAIRS();

  if (!$cronRun)
  {
    echo "<li>AIRS portefeuilles ophalen";
    ob_flush();
    flush();
  }
  if ($recon->AirsVerwerkingIntern)
  {
    $airsOnly = $recon->getAirsPortefeuilles();
  }


  if (!$cronRun)
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();
  }
  if ($recon->AirsVerwerkingIntern)
  {
    $airsOnly = $recon->getAirsCashRekeningen();
  }
  
  $recon->fillVB();
  
  //$prb->hide();    
  unlink($filename);
  return $teller;
}

function getHSBCFonds($isin)
{
  $db = new DB();
  //$query = "SELECT * FROM Fondsen WHERE HSBCCode = '$isin' ";
//  if ($fondsRec = $db->lookupRecordByQuery($query))
//  {
//
//  }
//  else
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$isin' ";
    $fondsRec = $db->lookupRecordByQuery($query);
  }

  return $fondsRec;
}

function hsbc_validateFile($filename)
{   
  global $error,$cronRun;
  return true;
  $error = array();
  if (!$cronRun)
  {
    echo "<li>start validatie bestanden";
    ob_flush();
    flush();
  }
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 1000, ";");
  //debug($data);
  $data[0] = stripBOM($data[0]);
  $validateStr1 = is_numeric($data[0]);
  $validateStr2 = (substr(trim($data[1]),0,3) == "100");
  
  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen FVL bestand";
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