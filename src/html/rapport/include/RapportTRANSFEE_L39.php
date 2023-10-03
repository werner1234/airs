<?php

include_once($__appvar["basedir"]."/html/rapport/RapportTRANS.php");

class RapportTRANSFEE_L39
{
	function RapportTRANSFEE_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->trans=new RapportTRANS($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "TRANSFEE";
	}

	function writeRapport()
	{
    $this->trans->writeRapport();
	}
}
?>