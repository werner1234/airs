<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/10 15:50:36 $
File Versie					: $Revision: 1.3 $

$Log: RapportVKMD_L99.php,v $


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_99/RapportVKMS_L99.php");

class RapportVKMD_L99
{

	function RapportVKMD_L99($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMS_L99($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}


	function writeRapport()
	{
    $this->vkm->pdf->rapport_type = "VKMD";
		$this->vkm->skipSummary=true;
		$this->vkm->skipDetail=false;
		$this->vkm->skipLangeTermijn=true;
		$this->vkm->writeRapport();
	}

}
