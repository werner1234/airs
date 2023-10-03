<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"] . "/html/rapport/include/layout_95/RapportINDEX_L95.php");

class RapportINDEX_L92
{
  function RapportINDEX_L92($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->grafiek = new RapportINDEX_L95($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }
  
  function writeRapport()
  {
    $this->grafiek->writeRapport();
  }
}
