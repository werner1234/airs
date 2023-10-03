<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function jb_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 4096, "\t"))
  {

    $count++;
//    if ($count == 1)
//    {
//      continue;  // skip header
//    }

    $data[0] = stripBOM($data[0]);

    $record = array();  // reset $record per ingelezen regel

    $portefeuille = trim($data[1]);

    $rekeninnrVal = trim($data[86]);
    $valuta       = trim($data[12]); // let op alleen bij geld rekeningen
    $aantal       = trim($data[75]);
    $isin         = trim($data[9]);
    $bankcode     = $data[8];
    $saldo        = $data[89];
    $record["bankCode"]     = $bankcode;


    $rekeninnr    = substr(str_replace(" ","", trim($data[99])),-12);
    if (strlen($rekeninnr) < 11)
    {
      $rekeninnr  = trim($data[1]);
    }


    if (trim($data[85]) != 0 AND $data[97] == 2 )  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $rekeninnrVal;
      $record["bedrag"]       = $saldo;
    }
    else
    {
      $valuta          = trim($data[79]);
      $record["fonds"] = $data[10];
      // eerst AIRS fondscode ophalen
      $bankCodeNotFound = true;
      if ($bankcode <> "")
      {
        $q = "SELECT * FROM Fondsen WHERE JBcode='$bankcode' ";
        if ($fRec = $db->lookupRecordByQuery($q))
        {
          $record["fonds"] = $fRec['Fonds'];
          $bankCodeNotFound = false;
        }
      }

      if ($bankCodeNotFound)
      {
        $q = "SELECT * FROM Fondsen WHERE ISINCode='$isin' AND Valuta ='".$valuta."'";

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
      $record["koers"]        = $data[76];
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function jb_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $data = fgetcsv($handle, 4096, "\t");
  //debug($data);
  $validateStr1 = substr($data[4],0,2);
  $validateStr2 = $data[75];


  if ( $validateStr1 == "20" AND isNumeric($validateStr2) )
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen JB positie bestand";
  }


  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

