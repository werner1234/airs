<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function ib_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 4096, "|"))
  {

    $count++;
//    if ($count == 1)
//    {
//      continue;  // skip header
//    }

    $data[0] = stripBOM($data[0]);

    $record = array();  // reset $record per ingelezen regel









    if (trim($data[8]) == "CASH" )  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $data[1];
      $record["valuta"]       = $data[9];
      $record["bedrag"]       = $data[11];
    }
    else
    {
      $valuta             = trim($data[9]);
      $isin               = trim($data[3]);
      $bankcode           = $data[6];
      $portefeuille       = trim($data[1]);
      $aantal             = trim($data[11]);
      $record["bankCode"] = $bankcode;
      $record["fonds"]    = $data[8];
      // eerst AIRS fondscode ophalen
      $bankCodeNotFound = true;
      if ($bankcode <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE JBcode='{$bankcode}' ";
        if ($fRec = $db->lookupRecordByQuery($q))
        {
          $record["fonds"]  = $fRec['Fonds'];
          $bankCodeNotFound = false;
        }
      }

      if ($bankCodeNotFound)
      {
        $q = "SELECT * FROM Fondsen WHERE ISINCode='{$isin}' AND Valuta ='{$valuta}'";

        if ($fRec = $db->lookupRecordByQuery($q) AND $isin <> "")
        {
          $record["fonds"] = $fRec['Fonds'];
        }

      }


      $record["bankCode"]     = $bankcode;
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koers"]        = $data[16];
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function ib_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $data = fgetcsv($handle, 4096, "|");
  //debug($data);
  $validateStr1 = $data[0];
  $validateStr2 = $data[2];


  if ( $validateStr1 == "H" AND $validateStr2 == "Position" )
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen IB positie bestand";
  }


  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

