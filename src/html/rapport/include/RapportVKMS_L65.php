<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/23 16:27:16 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMS_L65.php,v $
Revision 1.1  2019/01/23 16:27:16  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMD_L65.php");

class RapportVKMS_L65
{

	function RapportVKMS_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMD_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=false;
    $this->vkm->pdf->rapport_type = "VKMS";
		$this->vkm->writeRapport();
	}

}
