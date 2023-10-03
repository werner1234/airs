<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"] . "/html/rapport/include/layout_98/RapportOIR_L98.php");

class RapportOIS_L98
{
  function RapportOIS_L98($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->oir = new RapportOIR_L98($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->oir->normaleVerdeling = false;
    $this->oir->verdeling = 'Beleggingssectoren';
  }
  
  function writeRapport()
  {
    $this->oir->writeRapport();
  }
}
