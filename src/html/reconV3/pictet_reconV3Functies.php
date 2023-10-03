<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function pictet_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 4096, "\t"))
  {
    $count++;
    if ($count < 4 )
    {
      continue; // skip headerregels
    }
    $data[0] = stripBOM($data[0]);
    if (!is_numeric(trim($data[0])))
    {
      continue;
    }  // sla lege regels over

    $record = array();  // reset $record per ingelezen regel
    $parts = explode(".",$data[56]);
    $portefeuille   = intval($parts[0])."-".$parts[1];
    $rekeninnr      = $portefeuille;

    $isin           = trim($data[5]);
    $bankCode       = trim($data[9]);
    $aantal         = trim($data[3]);
    $valuta         = trim($data[2]);

    if ($bankCode == "")  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $aantal;
    }
    else
    {
      $record["bankCode"]     = $bankCode;
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["PE"]           = "";
      $record["valuta"]       = "XXXX";
      $record["koers"]        = $data[11];
//      debug($record);
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function pictet_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 2048, "\t");
  $data[0] = stripBOM($data[0]);
  $validateStr1 = substr($data[0],0,16);
  $validateStr2 = strstr($data[0],"P3DET");


  if ( $validateStr1 == "HEADER12.PL3L951" AND $validateStr2 != false)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Binck bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

