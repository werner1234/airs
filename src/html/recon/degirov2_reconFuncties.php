<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/03/22 08:21:48 $
 		File Versie					: $Revision: 1.4 $

 		$Log: degirov2_reconFuncties.php,v $
 		Revision 1.4  2019/03/22 08:21:48  cvs
 		call 7642
 		
 		Revision 1.3  2018/07/20 07:24:03  cvs
 		no message
 		
 		Revision 1.2  2018/06/12 12:58:34  cvs
 		alleen "-" in prodsuctcode van CASH rekening meenemen in de recon
 		
 		Revision 1.1  2018/05/30 14:44:28  cvs
 		call 6851
 		
 		Revision 1.3  2017/04/03 11:54:47  cvs
 		no message
 		
 		Revision 1.2  2015/12/01 09:03:15  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/22 08:05:49  cvs
 		*** empty log message ***
 		

 		
*/

function recon_readBank($filename, $filetype)
{
  global $prb, $batch, $recon, $airsOnly,$error, $cronRun;
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
  
  
  

  while ($data = fgetcsv($handle, 4096, ";"))
  {


    $pro_step += $pro_multiplier;

    $record = array("depot" => "GIRO", "batch" => $batch . "/" . $teller);  // reset $record per ingelezen regel

    $teller++;

    if ($teller == 1)
    {
      continue;
    }


    if ($data[15] == "CUR")
    {

      $record["type"] = "cash";
      // $record["portefeuille"] = $reknr;
      $record["rekening"] = substr($data[0], 2);
      $record["datum1"] = $recon->testDate;
      $record["datum2"] = $recon->testDate;
      $record["valuta"] = $data[11];
      $record["iban"] = "";
      $record["page"] = "";
      $record["bedragRaw"] = $data[10];
      $record["bedrag"] = $data[10];
      $record["batch"] = $batch;

      $output[] = $record;

      if ($data[1] < 0)
      {
        $recon->addRecord($record);
      }

    }
    else
    {
      $record["type"] = "sec";
      $record["portefeuille"] = substr($data[0], 2);
      $record["datum1"] = $recon->testDate;
      $record["datum2"] = $recon->testDate;
      $record["soort"] = "";
      $record["aantalRaw"] = $data[7];
      $record["aantal"] = $data[7];
      $record["ISIN"] = trim($data[18]);
      $record["valuta"] = $data[11];
      $record["bankCode"] = $data[1];
      $record["fonds"] = $data[2];
      $record["PE"] = 1;

      $record["koersRaw"] = $data[8];
      $record["koers"] = $data[8];

      $record["batch"] = $batch;

      if (trim($record["bankCode"]) == "15694501")
      {
        $record["bankCode"] = "15694498";  // call 7642   Fractiefondscode omzetten naar hoofdfonds
      }

      $output[] = $record;

      $recon->addRecord($record);
    }


  }

  if (!$cronRun)
  {
    echo "<li>AIRS data ophalen";
    ob_flush();
    flush();
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
    ob_flush();
    flush();
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

function validateFile($filename, $filename2)
{   
  global $error, $filetype, $cronRun;
  $error = array();
  $DB = new DB();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT positiebestand $filename is niet leesbaar";
    return false;
  }
  
  $data = fgetcsv($handle, 4096, ";");
    
  if ( $data[0] == "account" AND
       $data[1] == "productId" )
  {

  }
  else
  {
    $error[] = "FOUT positiebestand DeGiro positie bestand";
  }
  fclose($handle);

  if (Count($error) == 0)
  {
    return true;
  }
  else
  {
    return false;
  }
} 		




?>