<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/04/03 11:54:47 $
 		File Versie					: $Revision: 1.3 $

 		$Log: degiro_reconFuncties.php,v $
 		Revision 1.3  2017/04/03 11:54:47  cvs
 		no message
 		
 		Revision 1.2  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/22 08:05:49  cvs
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
  
  
  
  if ($filetype == "FND")
  {
    while ($data = fgetcsv($handle, 1000, ","))
    {

      //debug($data);

      $teller++;
      if ($teller == 1)                             continue;  // eerste regels overslaan
      if ($data[0] == "" OR $data[1] == "Totaal")   continue;  // totaliserings regels skippen
      $pro_step += $pro_multiplier;

      $record = array("depot" => "GIRO","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel

      $record["type"]         = "sec";
      $record["portefeuille"] = $data[0];
      $record["datum1"]       = $recon->testDate;
      $record["datum2"]       = $recon->testDate;
      $record["soort"]        = "";
      $record["aantalRaw"]    = $data[6];
      $record["aantal"]       = $data[6];
      $record["ISIN"]         = trim($data[3]);
      $record["valuta"]       = $data[7];
      $record["bankCode"]     = $data[2];
      $record["fonds"]        = $data[1];
      $record["PE"]           = 1;

      $record["koersRaw"]     = $data[8];
      $record["koers"]        = $data[8];

      $record["batch"] = $batch;
      $output[] = $record;

      $recon->addRecord($record);
    }
  
  }
  else
  {
     // eerst matrix inlezen
    $header = fgetcsv($handle, 1000, ";");  
    
    for($x=0; $x < count($header); $x++)
    {
      if (strlen($header[$x])==3) // waarschijnlijk een valuta
      {
        $index[$header[$x]] = $x;
      }
    }
    
    $matrix = array();
    while ($data = fgetcsv($handle, 1000, ";"))
    {
      
      $matrixRow = array();
      if ( $data[0] <> "" AND
           $data[1] == "Cash Account Balance" )
      {
        $matrixRow["account"] = $data[0];
        foreach ($index as $key=>$pos)
        {
          $matrixRow[$key] = $data[$pos];
        }
        $matrix[] = $matrixRow;
      }  
      
    }  
    
     //debug($matrix);
     for ($x=0; $x < count($matrix); $x++)
     {
       $row = $matrix[$x];
       
       $reknr = $row["account"];
       foreach($row as $key=>$val)
       {
         if ($key == "account") { continue;  }
         $record = array("depot" => "GIRO","batch"=>$batch."/".$teller);  // reset $record per ingelezen regel
         
         $record["type"]      = "cash";
        // $record["portefeuille"] = $reknr;
         $record["rekening"]  = $reknr;
         $record["datum1"]    = $recon->testDate;
         $record["valuta"]    = $key;
         $record["bedragRaw"] = $val;
         $record["DC"]        = ($val < 0)?"D":"C";
         $record["datum2"]    = $recon->testDate;
         $record["iban"]      = "";
         $record["page"]      = "";
         $record["bedrag"]    = $val;
         $record["batch"] = $batch;

         $output[] = $record;

         $recon->addRecord($record);
         
       }
      
    
     }
    // debug($output);
    
    
  
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
    $error[] = "FOUT positiebestand $filename is niet leesbaar";
    return false;
  }
  
  $data = fgetcsv($handle, 1000, ",");
    
  if ( $data[0] == "Account" AND 
       $data[1] == "Product" )
  {
    $filetype = "FND";
  }
  else
  {
    $error[] = "FOUT positiebestand DeGiro positie bestand";
  }
  fclose($handle);
  
  
  if (!$handle = @fopen($filename2, "r"))
  {
	$error[] = "FOUT geldbestand $filename is niet leesbaar";
	return false;
  }

  $data = fgetcsv($handle, 1000, ";");
  
  if ( $data[0] == "Account" AND 
       $data[1] == "Description" )
  {
    $filetype = "GLD";
  }
  else
  {
    $error[] = "FOUT geldbestand  geen DeGiro geld bestand";
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