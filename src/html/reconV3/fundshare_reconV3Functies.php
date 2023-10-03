<?php
/*
    AE-ICT sourcemodule created 20 apr. 2022
    Author              : Chris van Santen
    Filename            : fundshare_reconV3Functies.php


*/

function fundshare_recon_readBank($filename)
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
    $data[0] = stripBOM($data[0]);
    if (trim($data[19]) == "Account")
    {
      continue;
    }  // sla header over

    // recondatum is $data[0] formaat is mm/dd/yyyy

    $record = array();  // reset $record per ingelezen regel

    $portefeuille = trim($data[19]);
    $valuta       = trim($data[9]);
    $rekeninnr    = trim($data[19]);
    $aantal       = trim($data[13]);
    $isin         = trim($data[4]);


    if (trim($data[3]) == "Cash")  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $aantal;
    }
    else
    {

      //$record["memo"] = $data[17];

      $record["bankCode"]     = "xxx_".$isin.$data[9];
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koers"]        = $data[14];
    }
//    debug($record);
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function fundshare_validateFile($filename)
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
  $validateStr1 = ($data[0] == "Date");
  $validateStr2 = ($data[1] == "Fund");

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen FSE bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

