<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_109/RapportVOLK_L109.php");

class RapportVHO_L109
{
  function RapportVHO_L109($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {	
    $this->volk=new RapportVOLK_L109($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->volk->pdf->rapport_type='VHO';
    $this->volk->pdf->rapport_titel = "Portefeuille-overzicht (vergelijkend historische kostprijs)";
  }
 
  function writeRapport()
  {
    $this->volk->writeRapport();
  }
}
?>