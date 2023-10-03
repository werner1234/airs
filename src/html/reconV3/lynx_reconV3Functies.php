<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function lynx_recon_readBank($filename)
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
    $data = reformat($data);

    $data[0] = stripBOM($data[0]);

    $record = array();  // reset $record per ingelezen regel

    $portefeuille = trim($data[1]);
    $valuta       = trim($data[4]);
    $rekeninnr    = trim($data[1]);
    $aantal       = trim($data[6]);
    $bedrag       = trim($data[7]);
    $isin         = trim($data[3]);
    $fonds         = trim($data[5]);
    $bankcode        = $data[12];

    if (trim($data[0]) == "CP" ) // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $bedrag;
    }
    else
    {

      $record["bankCode"]     = $bankcode;
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koers"]        = 0;
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function lynx_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $data = fgetcsv($handle, 4096, ",");
  //debug($data);
  $validateStr1 = ($data[0] == "Type");
  $validateStr2 = ($data[1] == "Account");

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste row
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen LYNX bestand";
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

