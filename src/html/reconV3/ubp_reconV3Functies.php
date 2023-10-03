<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/

function ubp_recon_readBank($filename)
{
  global $bankName, $batch, $recon, $airsOnly, $cronRun;

  $verbose = !$cronRun;
  $count   = 0;
  $db = new DB();

  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  while ($data = fgetcsv($handle, 8192, ";"))
  {
    $count++;
    if (!is_numeric(trim($data[1]))) continue;  // sla lege regels over


    $record = array();  // reset $record per ingelezen regel

    $tcSoort = trim(strtolower($data[0]));
    $stat[$tcSoort]++;
    $record["portefeuille"] = ontnullen(trim($data[2]));
    switch($tcSoort)
    {
      case "forward":
        $record = array();
        $record["isPositie"]    = false;
        $record["portefeuille"] = ontnullen(trim($data[2]))."FWD";
        $record["valuta"]       = trim($data[34]);
        $record["bedrag"]       = $data[37];
        $recon->addToBankPile($record);

        $record = array();
        $record["isPositie"]    = false;
        $record["portefeuille"] = ontnullen(trim($data[2]))."FWD";
        $record["valuta"]       = trim($data[35]);
        $record["bedrag"]       = $data[38];
        $recon->addToBankPile($record);
        break;
      case "account":
        $record = array();
        $record["isPositie"]    = false;
        $record["portefeuille"] = ontnullen(trim($data[6]));
        $record["valuta"]       = trim($data[4]);
        $record["bedrag"]       = $data[8];
        $recon->addToBankPile($record);
        break;
      case "loans":
        $record = array();
        $record["isPositie"]    = false;
        $record["portefeuille"] = ontnullen(trim($data[2]))."LEN";
        $record["valuta"]       = trim($data[4]);
        $record["bedrag"]       = $data[43];
        $recon->addToBankPile($record);
        break;
      case "security":
        $record["bankCode"]     = trim($data[47]);
        $record["isPositie"]    = true;
        $record["portefeuille"] = ontnullen(trim($data[2]));
        $record["aantal"]       = trim($data[52]);
        $record["ISIN"]         = trim($data[48]);
        $record["PE"]           = "";
        $record["valuta"]       = trim($data[4]);
        $record["koers"]        = $data[53];
        $recon->addToBankPile($record);
        break;
    }
    /////////////////////////////
  }

  unlink($filename);
  return $count;
}

function ubp_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $data = fgetcsv($handle, 1000, ";");
  //debug($data);
  $validateStr1 = $data[0] == "COMMON_ASSET_TYPE";
  $validateStr2 = $data[1] == "COMMON_NBR_CO";

  if ( $validateStr1 AND $validateStr2)
  {

  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen UBP bestand";
  }


  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

function reformat($data)
{
  foreach ($data as $item)
  {
    $out[] = trim($item);
  }
  return $out;
}

