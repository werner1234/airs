<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/16 08:41:15 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMS_L93.php,v $


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L93
{

	function RapportVKMS_L93($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->skipLangeTermijn=false;
		$this->vkm->writeRapport();
	}

}
