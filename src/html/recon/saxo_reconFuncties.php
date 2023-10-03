<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/15 13:42:23 $
 		File Versie					: $Revision: 1.5 $

 		$Log: kbc_reconFuncties.php,v $
 		Revision 1.5  2020/01/15 13:42:23  cvs
 		call 8152
 		
 		Revision 1.4  2019/10/30 15:21:45  cvs
 		call 8152
 		
 		Revision 1.3  2019/10/21 07:19:27  cvs
 		call 8152
 		
 		Revision 1.1  2019/10/07 07:59:28  cvs
 		call 8152
 		
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

function recon_readBank($filename)
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

  $filetype = "GLD";


  while ($data = fgetcsv($handle, 4096, ","))
  {


    $pro_step += $pro_multiplier;

    $record = array("depot" => "SAXO", "batch" => $batch . "/" . $teller);  // reset $record per ingelezen regel

    $teller++;

//    if ($teller == 1)
//    {
//      continue;
//    }

    if ($data[0] == "CounterpartID")
    {
      continue;
    }

    if ($data[23] == "Cash")
    {

      $filetype = "GLD";
      $record["type"] = "cash";


      $record["rekening"]   = trim($data[0]);
      $record["datum1"]     = substr($data[6],0,4)."-".substr($data[6],0,5)."-".substr($data[6],0,7);
      $record["valuta"]     = $data[2];
      $record["iban"]       = "";
      $record["page"]       = "";
      $record["bedragRaw"]  = $data[21];
      $record["bedrag"]     = $data[21];
      $record["batch"]      = $batch;
      $output[] = $record;
      $recon->addRecord($record);

    }
    else
    {
      $filetype = "POS";
      $record["type"]           = "sec";
      $record["portefeuille"]   = trim($data[0]);
      $record["datum1"]         = substr($data[6],0,4)."-".substr($data[6],0,5)."-".substr($data[6],0,7);
      $record["soort"]          = "";
      $record["aantalRaw"]      = $data[17];
      $record["aantal"]         = $data[17];
      $record["ISIN"]           = trim($data[8]);
      $record["valuta"]         = $data[12];
      $record["bankCode"]       = $data[25];
      $record["fonds"]          = $data[26];
      $record["PE"]             = 1;
      $record["koers"]          = $data[13];
      $record["koersDatum"]     = substr($data[6],0,4)."-".substr($data[6],0,5)."-".substr($data[6],0,7);

      $record["batch"] = $batch;
//debug($record);
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

function validateFile($filename, $filetype)
{   
  global $error, $cronRun;

  $err = array();

    if (!$handle = @fopen($filename, "r"))
    {
      $error[] = "FOUT positiebestand $filename is niet leesbaar";
      return false;
    }

    $data = fgetcsv($handle, 4096, ",");
//    debug($data, $filetype);
    if (
      strtoupper($data[0] == "CounterpartID") AND
      strtoupper($data[1] == "AccountNumber") AND
      strtoupper($data[2] == "AccountCurrency")
    )
    {

    }
    else
    {
      $err[] = "FOUT geen SAXO positiebestand ";
    }
    fclose($handle);


  $error = array_merge($error, $err);

  if (Count($err) == 0)
  {
    return true;
  }
  else
  {
    return false;
  }
} 		




