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


function recon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly,$error, $cronRun;
  $ontdubbelArray = array();

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
  
  
  

  while ($data = fgetcsv($handle, 4096, $set["filePrefix"]))
  {
    $pro_step += $pro_multiplier;
    $record = array("depot" => $set["depot"], "batch" => $batch . "/" . $teller);  // reset $record per ingelezen regel
    $teller++;

    if ($teller == 1 AND $set["headerRow"])
    {
      continue;
    }

    if ($data[0] == "recordType")
    {
      continue;
    }


    //////////////////////////////////////////////////////////////////
    ///
    /// hieronder worden de CASH en SEC velden gemapped
    ///
    if (strtoupper(substr($data[2],0,4)) == "CASH")
    {

      $record["type"]         = "cash";
      $rParts=explode("/", $data[8]);

      $record["rekening"]     = $data[16];
      $record["datum1"]       = $data[1];
      $record["valuta"]       = $data[9];
      $record["iban"]         = "";
      $record["page"]         = "";
      $record["bedragRaw"]    = $data[22];
      $record["bedrag"]       = $data[22];
      $record["batch"]        = $batch;

      $output[] = $record;
      $recon->addRecord($record);

    }
    else
    {
      $record["type"]         = "sec";
      $record["portefeuille"] = $data[16];
      $record["datum1"]       = $data[1];
      $record["soort"]        = "";
      $record["aantal"]       = $data[20];
      $record["ISIN"]         = trim($data[11]);
      $record["valuta"]       = $data[9];
      $record["bankCode"]     = $data[17].$data[9];
      $record["fonds"]        = $data[35];
      $record["PE"]           = 1;
      $record["koers"]        = $data[33];
      $record["koersDatum"]   = $data[34];
      $record["batch"] = $batch;

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
    if ($filetype == "GLD")
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
    strtoupper($data[0] == "recordType") AND
    strtoupper($data[1] == "effDate") AND
    strtoupper($data[2] == "positionDesc")
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

