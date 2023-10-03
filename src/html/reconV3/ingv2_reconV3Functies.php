<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function ingv2_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 4096, ","))
  {

    $count++;
    if ($count == 1)
    {
      continue;  // skip header
    }

    $data[0] = stripBOM($data[0]);

    $record = array();  // reset $record per ingelezen regel

    $portefeuille = trim($data[0]);
    $valuta       = trim($data[7]);
    $rekeninnr    = trim($data[0]);
    $aantal       = trim($data[9]);
    $isin         = trim($data[5]);
    $ingCode      = $data[2];

    if (substr($isin,0,4) == "DIV:")
    {
      continue;   // DIV toekenningen overslaan
    }

    if (trim($data[4]) == "CASH" ) // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $aantal;
    }
    else
    {
      if (
          (stristr($data[4], "obligaties") AND $data[11] == 0.01) OR
          (stristr($data[4], "bonds") AND $data[11] == 0.01) OR
          stristr($data[4], "opties")   OR
          stristr($data[4], "options")
        )
      {
        $aantalFactor = 1;
      }
      else
      {
        $aantalFactor = $data[11];
      }

      $record["bankCode"]     = $ingCode;
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal * $aantalFactor;
      $record["ISIN"]         = $isin;
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koers"]        = $data[10];
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function ingv2_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $data = fgetcsv($handle, 4096, ","); // header
  $data = fgetcsv($handle, 4096, ","); // dataregel
  //debug($data);
  $validateStr1 = is_numeric($data[0]);
  $validateStr2 = is_numeric($data[2]);

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen ING bestand";
  }


  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

