<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/15 13:42:23 $
 		File Versie					: $Revision: 1.5 $

 		$Log: jpm_reconFuncties.php,v $
*/

function recon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly,$error, $cronRun;
  $ontdubbelArray = array();

  $csvDelimiter = ";";

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

  while ($data = fgetcsv($handle, 4096, $csvDelimiter, '"'))
  {
    $pro_step += $pro_multiplier;

    $record = array("depot" => "JPM", "batch" => $batch . "/" . $teller);  // reset $record per ingelezen regel

    $teller++;

    if (trim($data[0]) == "")
    {
      continue;
    } // lege regels overslaan

    // sla header regels over
    if (strtolower(trim($data[0])) == "reference date" and strtolower(trim($data[1])) == "account number")
    {
      continue;
    }

    $record["portefeuille"] = $data[1];
    $record["valuta"]       = $data[2];
    $record["batch"]        = $batch;
    $record["datum1"]       = _cnvDate($data[0]);

    if (trim(strtoupper($data[3])) == "CASH")
    {
      $filetype               = "GLD";
      $record["type"]         = "cash";
      $record["rekening"]     = $data[29];
      $record["bedragRaw"]    = (float)$data[15]; // col market value
      $record["bedrag"]       = (float)$data[15];
      $record["iban"]         = $data[28];

      //$record["page"]         = "";

      $output[] = $record;
      $recon->addRecord($record);
    }
    else
    {
      $filetype       = "POS";
      $record["type"] = "sec";
      $record["soort"]        = "";
      $record["ISIN"]         = $data[6];
      $record["bankCode"]     = $data[35];
      $record["aantal"]       = (float)$data[10];
      $record["aantalRaw"]    = (float)$data[10];
      $record["koers"]        = (float)$data[11];
      $record["koersDatum"]   = _cnvDate($data[49]);
      $record["fonds"]        = $data[5];

      $record["PE"]           = 1;
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

  $data = fgetcsv($handle, 4096, $csvDelimiter, '"');
  if (strtolower(trim($data[0])) != "reference date" OR
      strtolower(trim($data[1])) != "account number" OR
      strtolower(trim($data[2])) != "base currency" )
  {
      $err[] = "FOUT geen JPM positiebestand ";
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

function _cnvDate($sdate)
{
  list($day, $month, $year) = explode(".", $sdate);
  return "{$year}-{$month}-{$day}";
}

?>