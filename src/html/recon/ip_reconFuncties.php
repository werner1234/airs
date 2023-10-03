<?php
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Chris van Santen
    Filename            : _template_reconFuncties.php


*/

///////////////////////////////////////////////////////////////////////////////
///
/// TEMPLATE file voor bankimport, dit bestand niet aanpassen
/// maar opslaan als html/recon/FILEPREFIX_reconFuncties.php
///
///////////////////////////////////////////////////////////////////////////////

function _julDag($datum)
{
  $d = explode("-",$datum);
  $julian = mktime(1,1,1,(int)$d[1], (int) $d[2], (int) $d[0]);
  return floor($julian / 86400);
}

function recon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly,$error, $cronRun, $set;
  $ontdubbelArray = array();

  $fmt = new AE_cls_formatter();
  $filetype = "";

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


  //debug($set["filePrefix"]);
  
  $hiDay = 0;

  while ($data = fgetcsv($handle, 4096, $set["fileDelimit"]))
  {
    $pro_step += $pro_multiplier;
    $record = array("depot" => $set["depot"], "batch" => $batch . "/" . $teller);  // reset $record per ingelezen regel
    $teller++;



    if ($data[0] == "account")
    {
      continue;
    }


    $index = _julDag($data[4]);

    if ($hiDay < $index)
    {
      $hiDay = $index;
    }

    $rawData[$index][] = $data;

  }
//  debug($rawData, $hiDay);
  $dataSet = $rawData[$hiDay];
//  debug($dataSet);

  foreach ($dataSet as $data)
  {

    $record["type"]         = "sec";
    $record["portefeuille"] = $data[0];
    $record["datum1"]       = $data[4];
    $record["soort"]        = "";
    $record["aantal"]       = $data[3];
    $record["ISIN"]         = $data[2];
    $record["valuta"]       = "EUR";
    $record["bankCode"]     = $record["ISIN"];
    $record["fonds"]        = $data[1];
    $record["PE"]           = 1;
    $record["koers"]        = $data[8];
    $record["koersDatum"]   = $data[4];
    $record["batch"] = $batch;

    $output[] = $record;
    $recon->addRecord($record);
  }

//debug($output);

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
//    if ($filetype == "GLD")
    {
      $airsOnly = $recon->getAirsCashRekeningen();
    }
  }

  $recon->fillVB();
  
  //$prb->hide();  
  
  unlink($filename);
  return $teller;
}

function validateFile($filename, $filetype)
{   
  global $error, $set, $cronRun;

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT positiebestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 4096, $set["fileDelimit"]);

  // valideer de eerste regel van het bestand

  if (
    strtoupper($data[0] == "account") AND
    strtoupper($data[1] == "fonds") AND
    strtoupper($data[2] == "ISIN")
  )
  {

  }
  else
  {
    $error[] = "FOUT geen {$set["banknaam"]} positiebestand ";
  }
  fclose($handle);

  return (Count($error) == 0);

} 		

