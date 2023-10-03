<?php
include_once("rapport/include/layout_126/RapportHUIS_L126.php");

class RapportVOLKD_L126
{
	function RapportVOLKD_L126($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->huis = new RapportHUIS_L126($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		global $__appvar;
    $this->huis->layoutNr=1;
    $this->huis->writeRapport();
	}
}

