<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function degirov2_recon_readBank($filename)
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
    $data[0] = stripBOM($data[0]);
//    if (!is_numeric(trim($data[0])))
//    {
//      continue;
//    }  // sla lege regels over

    $record = array();  // reset $record per ingelezen regel
debug($data);
    $portefeuille   = trim($data[0]);
    $rekeninnr      = trim($data[0]);
    $valuta         = trim($data[2]); // let op alleen bij geld rekeningen
    $aantal         = makeNumber($data[9]);
    $isin           = trim($data[3]);
    $binck          = $data[16];

    if ($data[15] == "CUR")  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = substr($data[0], 2);
      $record["valuta"]       = $data[11];
      $record["bedrag"]       = $data[10];
    }
    else
    {

      //$record["memo"] = $data[17];

      $record["bankCode"]     = $data[1];

      if (trim($record["bankCode"]) == "15694501")
      {
        $record["bankCode"]   = "15694498";  // call 7642   Fractiefondscode omzetten naar hoofdfonds
      }

      $record["isPositie"]    = true;
      $record["portefeuille"] = substr($data[0], 2);
      $record["aantal"]       = $data[7];
      $record["ISIN"]         = trim($data[18]);
      $record["PE"]           = "";
      $record["valuta"]       = $data[11];
      $record["koers"]        = $data[8];
    }
    $recon->addToBankPile($record);
  }

  unlink($filename);
  return $count;
}

function degirov2_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 4096, ";");

  if ( $data[0] == "account" AND
       $data[1] == "productId" )
  {

  }
  else
  {
    $error[] = "FOUT positiebestand DeGiro positie bestand";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

