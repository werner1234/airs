<?php
/*
    AE-ICT sourcemodule created 07 sep. 2022
    Author              : Chris van Santen
    Filename            : reconCash_TGB.php

*/

while ($data = fgetcsv($handle, 4096, ";"))
{
  $record = array();
  $count++;
  $data[0] = stripBOM($data[0]);
  if (!is_numeric(trim($data[0])))
  {
    continue;
  }

  if (trim($data[3]) == "")  // cash
  {
    $valuta       = trim($data[2]);

    $record["isPositie"]    = false;
    $record["portefeuille"] = trim($data[0]);
    $record["rekening"]     = trim($data[0]).$valuta;
    $record["valuta"]       = $valuta;
    $record["bedrag"]       = trim($data[7]);

    if ( isset($reconArray[$record["rekening"]]) )
    {
      $reconArray[$record["rekening"]]["bedrag"] += $record["bedrag"];
    }
    else
    {
      $reconArray[$record["rekening"]] = $record;
    }
  }

}
