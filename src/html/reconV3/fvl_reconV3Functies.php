<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function fvl_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $count++;
    $data[0] = stripBOM($data[0]);
    if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over
    if (trim($data[4]) == "DIV") continue;  // DIV boekingen overslaa

    $record = array();  // reset $record per ingelezen regel

    $portefeuille = trim($data[0]);
    $valuta       = trim($data[2]);
    $rekeninnr    = trim($data[0]);
    $aantal       = trim($data[7]);
    $isin         = trim($data[16]);
    $binck        = $data[17];

    if (trim($data[3]) == "")  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $aantal;
    }
    else
    {

      //$record["memo"] = $data[17];

      $record["bankCode"]     = $data[12];
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koers"]        = $data[11]/$data[8];
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

  $data = fgetcsv($handle, 1000, ";");
  //debug($data);
  $data[0] = stripBOM($data[0]);
  $validateStr1 = is_numeric($data[0]);
  $validateStr2 = (substr(trim($data[1]),0,3) == "100");

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen FVL bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

