<?php
/*
    AE-ICT sourcemodule created 19 jul 2019
    Author              : Chris van Santen
    Filename            : facmod_vars.php

*/

$__factuur["debug"] = false;
$__factuur["mailFromFullname"] = "Airs ";
$__factuur["mailFrom"] = "factuur@airs.nl";
$__factuur["mailBCC"]  = array("cvsmob@gmail.com","helpdesk@aeict.nl");
$__factuur["pdfVoorzet"]  = "AIRS_factuur";
$__factuur["keepPdf"] = true;
$__factuur["subject"] = "Uw digitale factuur {factuurnummer} van AIRS";

$__facmod["decimaalSeperator"] = ",";
$__facmod["promilleSeperator"] = ".";


$__facmod["users"] = array("FEN");

$__facmod["eenheden"] = array(
    "stuk" 		=>  "stuk",
    "maand"   =>  "maand",
    "uur" 		=>	"uur",
);

$__facmod["btw"] = array(
  "H"	=>  "Hoog",
  "L" =>  "Laag",
  "0" =>	"0%",
);

$__facmod["periodes"] = array(
  "M" => "maand",
  "K" => "kwartaal",
  "H" => "half jaar",
  "J" => "jaar",
);

$cfg = new AE_config();
$__facmod["btwL"] = $cfg->getData("btw_L");
$__facmod["btwH"] = $cfg->getData("btw_H");;

$__facmod["debiteurStatus"] = array("G"=>"Gefactureerd","V"=>"Voldaan","D"=>"Deels voldaan","N"=>"Niet inbaar");

$__facmod["rubriek"] = array();
for ($i=1; $i < 11; $i++)
{
  $r = trim($cfg->getData("rubriek_$i"));
  if ($r != "")
  {
    $__facmod["rubriek"][] = $r;
  }

  sort($__facmod["rubriek"]);
}

function facmodAccess()
{
  global $USR, $__facmod;
  return in_array($USR, $__facmod["users"]);
}

if (!function_exists("fBedrag"))
{
  function fBedrag($in)
  {
    return number_format($in, 2);
  }
}

function makeFileName($strIn="")
{
  $strIn = trim($strIn);   // spatie voor en achterweg halen
  $strLength = strlen($strIn);
  if ($strLength > 0)
  {
    for ($ndx=0; $ndx < $strLength; $ndx++)
    {
      $char = $strIn[$ndx];
      if ($char == " ")                      $strOut .= "_";    // spatie vervangen door underscore
      if (ereg("([0-9a-zA-Z_]|\-)" , $char)) $strOut .= $char;  // alleen geldige tekens overnemen in bestandsnaam
    }
  }
  else
    $strOut = "onbekende_bestandsnaam";

  return $strOut;
}




