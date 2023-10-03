<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function vp_recon_readBank($filename)
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

    if ($count == 1 )
    {
      continue; // skip headerregels
    }
    $data[0] = stripBOM($data[0]);
    if (!is_numeric(trim($data[0])))
    {
      continue;
    }  // sla lege regels over

    $record = array();  // reset $record per ingelezen regel

    if ($data[4] != "" AND $data[5] != "")  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $data[4];
      $record["valuta"]       = $data[5];
      $record["bedrag"]       = $data[15];
    }
    else
    {
      $record["bankCode"]     = $data[11];
      $record["isPositie"]    = true;
      $record["portefeuille"] = $data[2];
      $record["aantal"]       = $data[15];
      $record["ISIN"]         = $data[13];
      $record["PE"]           = "";
      $record["valuta"]       = $data[6];
      $record["koers"]        = $data[16];
//      debug($record);
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function vp_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 2048, ";");
  $data[0] = stripBOM($data[0]);
  $validateStr1 = $data[0];
  $validateStr2 = $data[1];


  if ( $validateStr1 == "CusNo" AND $validateStr2 == "CusCur")
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen VP bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

