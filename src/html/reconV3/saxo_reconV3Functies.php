<?php
/*
    AE-ICT sourcemodule created 30 aug. 2022
    Author              : Chris van Santen
    Filename            : saxo_reconV3Functies.php


*/

function saxo_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 8000, ","))
  {
    $count++;
//    if ($count < 2 )
//    {
//      continue; // skip headerregels
//    }

    if (trim($data[0]) == "CounterpartID" OR trim($data[0]) == "")
    {
      continue; // header overslaan
    }

    $record = array();  // reset $record per ingelezen regel


    if (strtolower($data[23]) == "cash")// cash
    {
      $record["portefeuille"] = trim($data[0]);
      $record["bedrag"]       = $data[21];
      $record["isPositie"]    = false;
      $record["valuta"]       = trim($data[2]);
    }
    else
    {
      $bankcode               = trim($data[25]);
      $valuta                 = trim($data[12]);
      $record["isPositie"]    = true;
      $record["portefeuille"] = trim($data[0]);
      $record["aantal"]       = trim($data[17]);
      $record["ISIN"]         = trim($data[8]);
      $record["fonds"]        = trim($data[26]);
      $record["bankCode"]     = $bankcode;
      $record["valuta"]       = $valuta;
      $record["koers"]        = $data[13];
      $bankCodeNotFound = true;
      if ($bankcode <> "")
      {
        $q = "SELECT * FROM `Fondsen` WHERE `SAXOcode` = '{$bankcode}' ";
        if ($fRec = $db->lookupRecordByQuery($q))
        {
          $record["fonds"]  = $fRec['Fonds'];
          $bankCodeNotFound = false;
        }
      }

      if ($bankCodeNotFound)
      {
        $q = "SELECT * FROM `Fondsen`
              WHERE `ISINCode` = '{$record["ISIN"]}' AND `Valuta` ='{$valuta}'";
        if ($fRec = $db->lookupRecordByQuery($q) AND $record["ISIN"] <> "")
        {
          $record["fonds"] = $fRec['Fonds'];
        }
      }
   }

    $recon->addToBankPile($record);

  }

  unlink($filename);
  return $count;
}

function saxo_validateFile($filename)
{
  global $error, $filetype;

  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 8000, ",");

  $validateStr1 = ($data[0] == "CounterpartID");
  $validateStr2 = ($data[8] == "InstrumentISINCode");

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen SAXO bestand";
  }

  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

