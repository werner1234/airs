<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportRISK_L74.php");

class RapportRISK
{
	function RapportRISK($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->risk=new RapportRISK_L74($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
    $this->risk->writeRapport();
	}
}
?>