<?php
/*
    AE-ICT sourcemodule created 02 dec. 2020
    Author              : Chris van Santen
    Filename            : gs_reconV3Functies.php


*/


function gs_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 8000, "|"))
  {
    $count++;
//    if ($count < 2 )
//    {
//      continue; // skip headerregels
//    }

    if (trim($data[0]) == "Header" OR trim($data[0]) == "Trailer")
    {
      continue; // header overslaan

    }

    $record = array();  // reset $record per ingelezen regel


    if (strtolower(trim($data[29])) == "curr")// cash
    {
      $record["portefeuille"] = trim($data[0]);
      $record["bedrag"]       = trim($data[12]);
      $record["isPositie"]    = false;
      $record["valuta"]       = trim($data[2]);
    }
    else
    {
      $bankcode               = trim($data[3]);
      $valuta                 = trim($data[11]);
      $record["isPositie"]    = true;
      $record["portefeuille"] = trim($data[0]);
      $record["aantal"]       = trim($data[12]);
      $record["ISIN"]         = trim($data[5]);
      $record["fonds"]        = trim($data[30]);
      $record["bankCode"]     = $bankcode;
      $record["valuta"]       = $valuta;
      $record["koers"]        = $data[15];
      $bankCodeNotFound = true;
//      if ($bankcode <> "")
//      {
//        $q = "SELECT * FROM Fondsen WHERE HHBcode='$bankcode' ";
//        if ($fRec = $db->lookupRecordByQuery($q))
//        {
//          $record["fonds"] = $fRec['Fonds'];
//          $bankCodeNotFound = false;
//        }
//      }
//
//      if ($bankCodeNotFound)
//      {
        $q = "SELECT * FROM Fondsen WHERE ISINCode='{$record["ISIN"]}' AND Valuta ='{$valuta}'";
        if ($fRec = $db->lookupRecordByQuery($q) AND $record["ISIN"] <> "")
        {
          $record["fonds"] = $fRec['Fonds'];
        }
//      }


//      debug($record);
    }
    $recon->addToBankPile($record);

  }

  unlink($filename);
  return $count;
}

function gs_validateFile($filename)
{
  global $error, $filetype;

  return true;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 8000, "|");

  $validateStr1 = (strlen($data[10]) == 3);
  $validateStr2 = (strlen($data[11]) == 3);

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen GS bestand";
  }

  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

