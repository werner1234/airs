<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIV_L51.php");

class RapportSMV_L51
{
	function RapportSMV_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->rapport=new RapportOIV_L51($pdf,$portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
    $this->rapport->pdf->volkRapport=true;
    $this->rapport->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";
    
    $this->rapport->writeRapport();
    unset($this->rapport->pdf->volkRapport);
  }
  
}
?>