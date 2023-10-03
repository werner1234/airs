<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/12/15 17:49:14 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMD_L72.php,v $
Revision 1.1  2018/12/15 17:49:14  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMS_L72.php");

class RapportVKMD_L72
{

	function RapportVKMD_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMS_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
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
