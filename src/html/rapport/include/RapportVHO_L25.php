<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLK_L25.php");

class RapportVHO_L25
{
	function RapportVHO_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->volk=new RapportVOLK_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->volk->pdf->rapport_type='VHO';
    $this->volk->pdf->rapport_titel = "Portefeuilleoverzicht (vergelijkend historische kostprijs)";
    
  }
 
  function writeRapport()
  {
    $this->volk->writeRapport();
  }
}
?>