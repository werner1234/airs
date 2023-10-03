<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function hsbc_recon_readBank($filename)
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


  while ($line = fgets($handle))
  {

    $count++;
    if ($count == 1)
    {
      continue;  // skip header
    }

    $line = stripBOM($line);
    $record = array();  // reset $record per ingelezen regel
    if (substr($line,0,9) == "[Bestand:" )
    {
      $data = explode(";",substr(trim($line),9,-1));
      $data["type"] = "stukken";
    }
    else
    {
      $data = explode(";",substr(trim($line),7,-1));
      $data["type"] = "geld";
    }

    if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over
    if (trim($data[4]) == "DIV") continue;      // DIV boekingen overslaan  2008-09-26

    if ($data["type"] == "geld" ) // cash
    {
      $valuta       = trim($data[3]);
      $rekeningnr    = trim($data[0]);
      $aantal       = str_replace(",",".",trim($data[1]));
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeningnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $aantal;
    }
    else
    {
      $portefeuille = trim($data[0]);
      $aantal       = str_replace(",",".",trim($data[2]));
      $isin         = trim($data[1]);

      $fondsRec = getHSBCFonds($isin);

      $record["bankCode"]     = $isin;
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["fonds"]        = $fondsRec["Fonds"];
      $record["valuta"]       = $fondsRec["Valuta"];
      $record["koers"]        = 0;
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function hsbc_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $data = fgetcsv($handle, 1000, ";");
  $data = fgetcsv($handle, 1000, ";");
  //debug($data);
  $d = explode(":", stripBOM($data[0]));
  $validateStr1 = ($d[0] == "[Bestand" OR $d[0] == "[Saldo");
  $validateStr2 = is_numeric(trim($d[1]));

  if ( $validateStr1 AND $validateStr2)
  {
    // eerste row
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen HSBC bestand";
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

function getHSBCFonds($isin)
{
  $db = new DB();
  //$query = "SELECT * FROM Fondsen WHERE HSBCCode = '$isin' ";
//  if ($fondsRec = $db->lookupRecordByQuery($query))
//  {
//
//  }
//  else
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$isin' ";
    $fondsRec = $db->lookupRecordByQuery($query);
  }

  return $fondsRec;
}

