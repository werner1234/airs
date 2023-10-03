<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"] . "/html/rapport/include/layout_92/RapportGRAFIEK_L92.php");

class RapportGRAFIEK_L94
{
  function RapportGRAFIEK_L94($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->grafiek = new RapportGRAFIEK_L92($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }
  
  function writeRapport()
  {
    $this->grafiek->writeRapport();  
  }
}
