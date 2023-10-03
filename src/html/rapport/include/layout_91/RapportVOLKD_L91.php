<?php

include_once("rapport/include/layout_91/RapportHUIS_L91.php");

class RapportVOLKD_L91
{
	function RapportVOLKD_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->huis = new RapportHUIS_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		global $__appvar;
    $this->huis->layoutNr=1;
    $this->huis->writeRapport();
	}
}

