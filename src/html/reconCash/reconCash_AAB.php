<?php
/*
    AE-ICT sourcemodule created 09 sep. 2022
    Author              : Chris van Santen
    Filename            : reconCash_ABN.php


*/



while ($data = fgetcsv($handle, 4096, ";"))
{
  $count++;
  $data[0] = stripBOM($data[0]);
  if (!is_numeric(trim($data[0])))
  {
    continue;
  }  // sla lege regels over

  $record = array();  // reset $record per ingelezen regel

  if (trim($data[2]) == "")  // cash
  {

    $record["isPositie"]    = false;
    $record["portefeuille"] = $data[0] * 1;
    $record["valuta"]       = $data[3];
    $record["rekening"]     = $record["portefeuille"].$record["valuta"];
    $record["bedrag"]       = $data[13];
    $bankRekeningen[]       = $record["rekening"];

    if ( isset($reconArray[$record["rekening"]]) )
    {
      $reconArray[$record["rekening"]]["bedrag"] += $data[13];
    }
    else
    {
      $reconArray[$record["rekening"]] = $record;
    }

  }

}

