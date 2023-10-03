<?php
/*
    AE-ICT sourcemodule created 15 jul. 2022
    Author              : Chris van Santen
    Filename            : ubs_ForexConf_functies.php


*/

function fx_do_forex()
{
  global $fonds, $data, $mr, $output, $meldArray, $errorArray, $errors;

  debug($data);
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "KRUIS";

  if ($data[10] != "EUR" AND $data[16] == "EUR")
  {
    $mr["Valuta"]            = $data[10];
    $mr["Valutakoers"]       = 1/$data[9];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Rekening"]          = UBS_getRekening($data[20],$data[16]);
    $mr["Debet"]             = abs($data[11]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
    $controleBedrag         += $mr["Debet"] * -1;
    $output[] = $mr;

    $mr["Rekening"]          = UBS_getRekening($data[14],$data[10]);
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[11]);
    $mr["Bedrag"]            = $mr["Credit"] ;
    $controleBedrag         += $mr["Bedrag"];

    if ($mr["Rekening"])
    {
      $output[] = $mr;
    }

  }
  elseif ($data[10] == "EUR" AND $data[16] != "EUR")
  {
    $mr["Valuta"]            = $data[16];
    $mr["Valutakoers"]       = 1/$data[9];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Rekening"]          = UBS_getRekening($data[14],$data[10]);
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
    $controleBedrag         += $mr["Bedrag"] ;
    $output[] = $mr;

    $mr["Rekening"]          = UBS_getRekening($data[20],$data[16]);
    $mr["Debet"]             = abs($data[17]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * -1 ;
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];

    if ($mr["Rekening"])
    {
      $output[] = $mr;
    }
  }
  else
  {
    $errorArray[] = "Fout: FX zonder EUR handmatig boeken";
    $errors++;
  }

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], 0);

}
  