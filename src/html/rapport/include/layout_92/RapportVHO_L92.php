<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_92/RapportHSE_L92.php");

class RapportVHO_L92
{

	function RapportVHO_L92($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->hse=new RapportHSE_L92($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->hse->pdf->rapport_type='VHO';
    $this->hse->pdf->rapport_titel = "Vergelijkend historisch overzicht";
	}


	function writeRapport()
	{
		$this->hse->writeRapport();
	}

}
