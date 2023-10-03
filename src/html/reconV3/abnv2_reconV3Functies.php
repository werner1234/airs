<?php
/*
    AE-ICT sourcemodule created 22 mei 2019
    Author              : Chris van Santen
    Filename            : binck_reconV3Functies.php

*/



function abnv2_recon_readBank($filename)
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

    $record = array();  // reset $record per ingelezen regel

    if ($data[2] == "")  // cash
    {
      $record["isPositie"]    = false;
      $record["portefeuille"] = $data[0] * 1;
      $record["valuta"]       = $data[3];
      $record["bedrag"]       = $data[13];
    }
    else
    {

      //$record["memo"] = $data[17];

//      $db = new DB();
//
//      $q = "SELECT * FROM Fondsen WHERE AABCode='".trim($data[2])."' OR ABRCode='".trim($data[2])."' ";
//      if (!$fondsRec = $db->lookupRecordByQuery($q))
//      {
//        // $q = "SELECT * FROM Fondsen WHERE ISINCode='".$data[8]."' AND  Valuta = '".$data[9]."'";
//        // $fondsRec = $db->lookupRecordByQuery($q);
//      }
//
//      $fonds = ($fondsRec["Fonds"] != "")?$fondsRec["Fonds"]:$data[4];
//      $isin  = ($fondsRec["ISINCode"] != "")?$fondsRec["ISINCode"]:"XXX";
//      $bankCode = ($fondsRec["AABCode"] != "")?$fondsRec["AABCode"]:$data[2];


      $record["bankCode"]     = $data[2];
      $record["isPositie"]    = true;
      $record["portefeuille"] = (int) $data[0];
      $record["aantal"]       = $data[11];
      $record["ISIN"]         = $data[16];
      $record["PE"]           = "";
      $record["valuta"]       = $data[3];
      $record["koers"]        = $data[12];
    }
    $recon->addToBankPile($record);
  }
  unlink($filename);
  return $count;
}

function abnv2_validateFile($filename)
{
  global $error, $filetype;
  $error = array();
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }

  $data = fgetcsv($handle, 4096, ";");

  if ($data[0] != "PortfolioID" OR
    $data[1] != "PortfolioCurrency")
  {
    $error[] = "Bestand is geen ABN v2 ";
  }



  fclose($handle);

  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}

