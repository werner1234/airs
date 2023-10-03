<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_102/RapportVKMS_L102.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMD_L102
{

	function RapportVKMD_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMS_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->vkm->pdf->rapport_type='VKMD';
    $this->vkm->pdf->rapport_titel = "Lopende kosten";
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=true;
		$this->vkm->skipDetail=false;
		$this->vkm->skipLangeTermijn=true;
		unset($this->vkm->pdf->vmkHeaderOnderdrukken);
		$this->vkm->writeRapport();
	}

}
