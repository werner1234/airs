<?php
/*
    AE-ICT sourcemodule created 07 sep. 2022
    Author              : Chris van Santen
    Filename            : reconCash_BIN.php


*/

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
  }  // DIV boekingen overslaan  2008-09-26

  ////////////////////////////////////////////

  $record = array();  // reset $record per ingelezen regel

  if (trim($data[15]) == "")  // cash
  {
    $valuta         = trim($data[2]);
    $aantal         = makeNumber($data[9]);
    if (trim($data[2]) == "PNC")
    {
      $valuta         = "GBP";
      $aantal         = round(makeNumber($data[9])/100,2);
    }

    $record["isPositie"]    = false;
    $record["portefeuille"] = trim($data[0]);
    $record["rekening"]     = trim($data[0]).$valuta;
    $record["valuta"]       = $valuta;
    $record["bedrag"]       = $aantal;

    $bankRekeningen[]       = $record["rekening"];
    if ( isset($reconArray[$record["rekening"]]) )
    {
      $reconArray[$record["rekening"]]["bedrag"] += $aantal;
    }
    else
    {
      $reconArray[$record["rekening"]] = $record;
    }

  }

}
