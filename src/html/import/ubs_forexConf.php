<?php
/*
    AE-ICT sourcemodule created 15 jul. 2022
    Author              : Chris van Santen
    Filename            : ubs_forexConf.php


*/


include_once 'ubs_ForexConf_functies.php';
function  do_ForexConf($datum,$fields,$fieldData)
{
  global $statsArray, $fonds, $data, $mr, $output, $meldArray,$errorArray,$errors;
  $errors = 0;
  $errorArray = array();
  $stats= array();
  $stats["module"] = "ForexConf"."-".$datum;
  $stats["regels"] = count($fieldData);
  for ($x=0; $x < count($fieldData); $x++ )
  {
    global $output;
    $mr = array();
    $data = $fieldData[$x];

    if (count($data) == 0) continue;

    $mr["regelnr"]           = $x+7;
    if ($data[2] <> "NEWT")
    {
      $errorArray[] = "[".$mr["regelnr"]."] Functie geen NEWT (".$data[2].")";
      $errors++;
    }
    else
    {
      $mr["bestand"]           = "ForexConf-".$datum;
      $mr["Boekdatum"]         = UBS_toDbDate($data[7]);
      $mr["bankTransactieId"]  = Trim($data[4]);
      $mr["Omschrijving"]      = "Valuta transactie";
      $mr["settlementDatum"]   = UBS_toDbDate($data[8]);

      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;

      FX_do_Forex();
    }
  }

  $stats["fouten"]    = (int)$errors;
  $stats["controle"]  = implode("<br/>",$meldArray);
  $stats["errors"]    = implode("<br/>",$errorArray);
  $statsArray[]       = $stats;
}
