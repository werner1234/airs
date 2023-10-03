<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/22 08:38:56 $
 		File Versie					: $Revision: 1.9 $

 		$Log: credswiss_reconFuncties.php,v $
 		Revision 1.9  2019/11/22 08:38:56  cvs
 		call 8166
 		
 		Revision 1.8  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.6  2016/05/11 13:50:56  cvs
 		call 4859
 		
 		Revision 1.5  2015/12/01 09:03:06  cvs
 		update 2540, call 4352
 		
 		Revision 1.4  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/03/16 12:40:16  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/16 07:30:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/25 07:55:50  cvs
 		dbs 2746
 		
 		Revision 1.3  2014/11/13 12:33:01  cvs
 		dbs 3118
 		
 		Revision 1.2  2014/11/13 10:46:04  cvs
 		dbs  3118
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

function recon_readBank($filename, $filetype)
{
  global $prb, $batch, $recon, $airsOnly,$error;
  $ontdubbelArray = array();
  if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
  $csvRegels = Count(file($filename));
//  $prb->max = 100;
//  $prb->addLabel('text','txt1','Inlezen bankbestand, '.count($rawData).' regels');	// add Text as Label 'txt1' and value 'Please wait'
//  $prb->moveMin();
  $pro_multiplier = 100/$csvRegels;
  //$prb->show();	
  $teller = 0;
  
  $tmpGeldArray = array();

  while ($data = fgetcsv($handle, 1000, ";"))
  {
    
    //debug($data);
    
    $teller++;
    if ($teller < 7)      continue;  // eerste regels overslaan
    
    $pro_step += $pro_multiplier;
   // $prb->setLabelValue('txt1','Inlezen bankbestand, '.$teller." / ".$csvRegels.' regels');
    
    //$prb->moveNext();
    
    $record = array("depot" => "CS","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
    if ($filetype == "FND")
    {
     
      if ($data[1] <> "MT535")
      {
        $error[] = "[".$teller."] Geen MT535 regel, overgeslagen";
        continue;
      }
      if ($data[5] <> "NEWM")
      {
        $error[] = "[".$teller."] Geen NEWM regel, overgeslagen";
        continue;
      }
      if ($data[18] <> "Y")
      {
        //$error[] = "[".$teller."] Inaktieve regel, overgeslagen";
        continue;
      }

      $record["type"]         = "sec";
      $record["portefeuille"] = CS_getPortefeuille($data[17]);
      $record["datum1"]       = CS_toDbDate($data[9]);
      $record["datum2"]       = CS_toDbDate($data[9]);
      $record["soort"]        = "";
      $record["aantalRaw"]    = $data[55];
      $record["aantal"]       = $data[55];
      $record["ISIN"]         = trim($data[27]);
      if (trim($data[52]) <> "")
      {  
        $record["valuta"]       = $data[52];
      }  
      else
      {  
        $record["valuta"]       = $data[36];
      }  

      if ($data[35] != "")
      {
        $record["ISIN"]         = "OL: ".trim($data[45]);
        $omsParts = explode("BBI:", $data[46] );
        $record["bankCode"]     = "{$omsParts[1]} ".$data[35]."/".$data[38]."/".$data[42];
      }
      else
      {
        $record["bankCode"]     = $record["ISIN"].$record["valuta"];
      }

      $record["fonds"]        = trim($data[28]);
      $record["PE"]           = 1;
      
      $record["koersRaw"]     = 0;
      $record["koers"]        = $data[51];
      $record["koersDatum"]   = $record["datum1"];

// FILE formaat t/m 21-11-2019
//      $record["type"]         = "sec";
//      $record["portefeuille"] = CS_getPortefeuille($data[17]);
//      $record["datum1"]       = CS_toDbDate($data[9]);
//      $record["datum2"]       = CS_toDbDate($data[9]);
//      $record["soort"]        = "";
//      $record["aantalRaw"]    = $data[53];
//      $record["aantal"]       = $data[53];
//      $record["ISIN"]         = trim($data[26]);
//      if (trim($data[51]) <> "")
//      {
//        $record["valuta"]       = $data[51];
//      }
//      else
//      {
//        $record["valuta"]       = $data[35];
//      }
//      $record["bankCode"]     = $record["ISIN"].$record["valuta"];
//      $record["fonds"]        = trim($data[27]);
//      $record["PE"]           = 1;
//
//      $record["koersRaw"]     = 0;
//      $record["koers"]        = $data[50];
//      $record["koersDatum"]   = $record["datum1"];


      $record["batch"] = $batch;
      $output[] = $record;
      //debug($data);
      //debug($record);

      if ($record["datum1"] == $recon->testDate)
      {
        $recon->addRecord($record);
      }  
      
    }
    else  // GLD
    {
     
      if ($data[1] <> "MT940")
      {
        $error[] = "[".$teller."] Geen MT940 regel, overgeslagen";
        continue;
      }

      $record["type"]      = "cash";
      $record["rekening"]  = CS_getPortefeuille($data[4],true);
      $record["datum1"]    = CS_toDbDate($data[27]);
      $record["valuta"]    = $data[28];
      $record["bedragRaw"] = $data[29];
      $record["DC"]        = $data[26];
      $record["datum2"]    = CS_toDbDate($data[27] );
      $record["iban"]      = "";
      $record["page"]      = $data[6];
      $factor = $record["DC"] == "C"?1:-1; 
      $record["bedrag"]    = $factor * $record["bedragRaw"];
      
      
      //start overslaan van evt dubbele waardes
      $tmpRecord = $record;
      unset($tmpRecord["batch"]);
      $ontdubbelKey = md5(serialize($tmpRecord));
      if (in_array($ontdubbelKey,$ontdubbelArray))
      {
        continue;
      }
      else
      {
        $ontdubbelArray[] = $ontdubbelKey;
      }
      //stop overslaan van evt dubbele waardes
      $record["batch"] = $batch;

      if ($record["datum1"] == $recon->testDate)
      {
        $tmpGeldArray[$record["rekening"]][] = $record;  
      }  
    }
     
    
    
  }
  if ($filetype == "FND")
  {
    echo "<li>AIRS data ophalen";
    ob_flush();flush();    
    $recon->fillTableFormAIRS();
    
      
    echo "<li>AIRS portefeuilles ophalen";
    ob_flush();flush();
    $airsOnly = $recon->getAirsPortefeuilles();

  }
  else  // GLD
  {
    echo "<li>AIRS rekeningnummers ophalen";
    ob_flush();flush();
    foreach ($tmpGeldArray as $key => $values)  // extra loop om hoogste pagina te selecteren voor saldo call 3529
    {
      $tmpRecord = $values[0];                                       // vul temp rec met eerst gevonden record
      if (count($values) > 1 )                                       // als er meer dan 1 pagina is gevonden dan hoogste pagina gaan zoeken
      {
        for ($x=1; $x < count($values); $x++) 
        {
          if ( (int) $values[$x]["page"] > (int) $tmpRecord["page"])
          {
            $tmpRecord = $values[$x];                               // als huidige pagenr > dan opgeslagen dan overschrijven
          }
        }
      }
      unset($tmpRecord["page"]);                                    // verwijder paginanr om SQL error te voorkomen
      $output[] = $tmpRecord;
      $recon->addRecord($tmpRecord);
    }

    $airsOnly = $recon->getAirsCashRekeningen();
  }
  
  $recon->fillVB();
  
  //$prb->hide();  
  
  unlink($filename);
  return $teller;
}

function validateFile($filename, $filename2)
{   
  global $error, $filetype;
  $error = array();
  $DB = new DB();

  if (!$handle = @fopen($filename, "r"))
  {
	$error[] = "FOUT bestand $filename is niet leesbaar";
	return false;
  }

  $dataRaw = fgets($handle, 4096);
  
  if (trim($dataRaw) == "SecurityPositions")
  {
    $filetype = "FND";
  }
  else
  {
      $error[] = "FOUT geen CredSwiss positie bestand";
  }
  fclose($handle);
  
  
  if (!$handle = @fopen($filename2, "r"))
  {
	$error[] = "FOUT bestand $filename is niet leesbaar";
	return false;
  }

  $dataRaw = fgets($handle, 4096);
  
  if (trim($dataRaw) == "CashPositions")
  {
    $filetype = "GLD";
  }
  else
  {
      $error[] = "FOUT geen CredSwiss cash bestand";
  }
  fclose($handle);
  
  
  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
} 		


function CS_getPortefeuille($in,$reknr=false)
{
  return $in;
  //$parts = explode("-",$in);
  //return $parts[0]."-".$parts[1]."-".$parts[2];
  // return (int) $parts[0].$parts[1].$parts[2];
}



function CS_toDbDate($in)
{
  return substr($in,0,4)."-".substr($in,4,2)."-".substr($in,6,2);
}

?>