<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/02 12:28:42 $
    File Versie         : $Revision: 1.1 $

    $Log: ubs_ChargesAdvice_functies.php,v $
    Revision 1.1  2018/02/02 12:28:42  cvs
    call 6474



*/

function CA_do_KNBA()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr["aktie"]             = "KNBA";

  $mr["Grootboekrekening"] = "KNBA";
  if ($data[6] < 0)
  {
    $mr["Debet"]             = abs($data[6]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[6]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

}


function CA_do_RENTE()
{
  global $fonds, $data, $mr, $output, $meldArray;

  $mr["aktie"]             = "RENTE";

  $mr["Grootboekrekening"] = "RENTE";
  if ($data[6] < 0)
  {
    $mr["Debet"]             = abs($data[6]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[6]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

}