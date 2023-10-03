<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

    $Log: lombard_reconV3Functies.php,v $
    Revision 1.3  2020/06/29 11:12:21  cvs
    call 8447

    Revision 1.2  2020/03/20 15:37:11  cvs
    no message

*/



function lombard_recon_readBank($filename)
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


  while ($data = fgetcsv($handle, 8000, ";"))
  {
    $count++;
    if ($count < 2 )
    {
      continue; // skip headerregels
    }
    $data[0] = stripBOM($data[0]);
    if (!is_numeric(trim($data[0])))
    {
      continue;
    }  // sla lege regels over

    $record = array();  // reset $record per ingelezen regel
    $portefeuille   = $data[146];
    $rekeninnr      = $portefeuille;

    $isin           = trim($data[37]);
    $bankCode       = trim($data[152]);
    $valuta         = trim($data[62]);
    $extra        = array();

    if (substr($data[125],0,16) == "ORDINARY ACCOUNT" OR
      substr($data[125],0,12) == "INCOME TO BE"   )  // CASH
    {
      $aantal              = trim($data[54]);
      if (substr($data[125],0,12) == "INCOME TO BE")
      {
        $record["portefeuille"]  = "TBR-".$rekeninnr;
        $record["bedrag"]    = $aantal;
        // 2 regels aanmaken
        $extra["rekening"]  = $rekeninnr;
        $extra["bedrag"]    = $data[237] - $aantal;
      }
      else
      {
        $record["portefeuille"] = $rekeninnr;
        $record["bedrag"]    = $data[237];
      }
      $record["isPositie"]    = false;
      $record["valuta"]       = $valuta;
    }
    else
    {
      if ($data[313] > 0)
      {
        $aantal = trim($data[237])/trim($data[313]);  // opties
      }
      else
      {
        $aantal = trim($data[237]);
      }

      $record["bankCode"]     = $bankCode;
      $record["isPositie"]    = true;
      $record["portefeuille"] = $portefeuille;
      $record["aantal"]       = $aantal;
      $record["ISIN"]         = $isin;
      $record["fonds"]        = $data[125];
      $record["valuta"]       = $valuta;
      $record["koers"]        = $data[50];
//      debug($record);
    }
    $recon->addToBankPile($record);

  }

  unlink($filename);
  return $count;
}

function lombard_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 8000, ";");
  //debug($data);
  $validateStr1 = trim($data[0]);

  if ( $validateStr1 == "FCDateF" )
  {
    // eerste veld is numeriek en tweede veld gevuld met 1000
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Lombard bestand";
  }

  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

