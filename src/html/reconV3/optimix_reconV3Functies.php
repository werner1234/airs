<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function optimix_recon_readBank($filename)
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

  $dCNt = 0;
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

    $d = explode("-",$data[6]);
    $portefeuille = trim($data[0]);
    $valuta       = trim($data[5]);
    $rekeninnr    = trim($data[0]);
    $aantal       = trim($data[7]);
    $isin         = trim($data[3]);
    $bankCode     = trim($data[2]);
    $koersDatum   = "20".$d[2]."-".$d[0]."-".$d[1];
    $koers        = $data[8];
    $fonds        = trim($data[4]);


    if ($aantal == "" ) // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $data[11];
    }
    else
    {
      $record["bankCode"]     = $bankCode;
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["PE"]           = "";
      $record["valuta"]       = $valuta;
      $record["koers"]        = $koers;
      if ($portefeuille == "31267" )
      {
        $dCNt++;
        debug($record,$dCNt);
      }
    }
    $recon->addToBankPile($record);
  }
  debug($recon->bankPile, "bankPile" );
  unlink($filename);
  return $count;
}

function optimix_validateFile($filename)
{
  global $error, $bankName;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $data = fgetcsv($handle, 4096, ",");

  $validateStr1 = ($data[0] == "Portfolio ID");
  $validateStr2 = ($data[1] == "Reporting ISO");

  if ( $validateStr1 AND $validateStr2)
  {

  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Optimix bestand";
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

