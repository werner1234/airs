<?php
/*
    AE-ICT sourcemodule created 09 okt. 2020
    Author              : Chris van Santen
    Filename            : fvlc_reconV3Functies.php

21-10 naar RVV
*/


function fvlc_recon_readBank($filename)
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
      continue; // skip header
    }

    $data[0] = stripBOM($data[0]);
    $record = array();  // reset $record per ingelezen regel

    $portefeuille = trim($data[0]);
    $valuta       = trim($data[2]);
    $rekeninnr    = trim($data[0]);
    $aantal       = trim($data[7]);
    $isin         = trim($data[16]);
    $binck        = $data[17];

    if (trim($data[4]) == "Current Account" OR trim($data[4]) == "Commitment")  // cash
    {
      $record["isPositie"]    = false;

      if (trim($data[4]) == "Commitment")
      {
        $record["portefeuille"] = $data[1];
      }
      else
      {
        $record["portefeuille"] = $data[1].$data[4];
      }

      $record["valuta"]       = $data[3];
      $record["bedrag"]       = fvlcNumber($data[2]);
    }
    else
    {

      //$record["memo"] = $data[17];

      $record["bankCode"]     = trim($data[0]);
      $record["isPositie"]    = true;
      $record["portefeuille"] = $data[1];
      $record["aantal"]       = $data[2];
      $record["ISIN"]         = trim($data[0]);
      $record["PE"]           = "";
      $record["valuta"]       = $data[3];
      $record["koers"]        = fvlcNumber($data[5]);
     // $record["fonds"]        = $data[4];
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function fvl_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 1000, ",");
  //debug($data);
  $data[0] = stripBOM($data[0]);
  $validateStr1 = ($data[1] == "Portfolio");
  $validateStr2 = ($data[4] == "Category");

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen FVLC bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

function fvlcNumber($in)
{
  return str_replace("'","",$in);
}

