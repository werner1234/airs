<?php
/*
    AE-ICT sourcemodule created 28 okt. 2020
    Author              : Chris van Santen
    Filename            : binck_cashReconV3Functies.php


*/



function binck_cashRecon_readBank($filename)
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

    if (!is_numeric(trim($data[0])))
    {
      continue;
    }  // sla lege regels over
    if (trim($data[4]) == "DIV")
    {
      continue;
    }
    ////////////////////////////////////////////

    $record = array();  // reset $record per ingelezen regel


    $portefeuille = trim($data[0]);
    $valuta       = trim($data[2]);
    $rekeninnr    = trim($data[0]);
    $aantal       = makeNumber($data[9]);


    if (trim($data[15]) == "" )
    {
      if (trim($data[2]) == "PNC")
      {
        $valuta         = "GBP";
        $aantal         = makeNumber($data[9])/100;
      }
      $record["isPositie"]    = false;
      $record["portefeuille"] = $rekeninnr;
      $record["valuta"]       = $valuta;
      $record["bedrag"]       = $aantal;
      $recon->addToBankPile($record);
    }

  }

//  unlink($filename);
  return $count;
}

function binck_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 2048, ";");
  //debug($data);
  $data[0] = stripBOM($data[0]);
  $validateStr1 = is_numeric($data[0]);
  $validateStr2 = (strlen($data[2]) >= 3);

  if ( $validateStr1 AND $validateStr2 )
  {
    // eerste veld is numeriek
  }
  else
  {
    $error[] = "Bestand validatie mislukt, geen Binck bestand";
  }

  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

