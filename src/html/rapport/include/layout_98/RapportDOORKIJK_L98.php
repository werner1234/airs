<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"] . "/html/rapport/include/layout_92/RapportGRAFIEK_L92.php");

class RapportDOORKIJK_L98
{
  function RapportDOORKIJK_L98($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->grafiek = new RapportGRAFIEK_L92($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->grafiek->normaleVerdeling = false;
  }
  
  function writeRapport()
  {
    $this->grafiek->writeRapport();
  }
}
