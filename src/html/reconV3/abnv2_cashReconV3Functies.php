<?php
/*
    AE-ICT sourcemodule created 28 okt. 2020
    Author              : Chris van Santen
    Filename            : abnv2_cashReconV3Functies.php


*/



function abnv2_cashRecon_readBank($filename)
{
  global $prb, $batch, $recon, $airsOnly, $cronRun;

  $verbose = !$cronRun;
  $count   = 0;
  $db = new DB();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $count++;
    $data[0] = stripBOM($data[0]);

    if (!is_numeric(trim($data[0])))
    {
      continue;
    }  // sla lege regels over

    $record = array();  // reset $record per ingelezen regel


    if (trim($data[2]) == "" )
    {

      $record["isPositie"]    = false;
      $record["portefeuille"] = $data[0] * 1;
      $record["valuta"]       = $data[3];
      $record["bedrag"]       = $data[13];
      $recon->addToBankPile($record);
    }

  }

//  unlink($filename);
  return $count;
}

function abnv2_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 2048, ";");
  //debug($data);
  $data[0] = stripBOM($data[0]);

  if ($data[0] != "PortfolioID" OR
    $data[1] != "PortfolioCurrency")
  {
    $error[] = "Bestand is geen ABN v2 ";
  }

  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

