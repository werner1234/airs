<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/26 19:33:28 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMD_L33.php,v $
Revision 1.1  2019/01/26 19:33:28  rvv
*** empty log message ***

Revision 1.1  2019/01/23 16:27:16  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportTRANSFEE_L33.php");

class RapportVKMD_L33
{

	function RapportVKMD_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportTRANSFEE_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		$this->vkm->skipSummary=true;
		$this->vkm->skipDetail=false;
		$this->vkm->skipLangeTermijn=true;
    $this->vkm->pdf->rapport_type = "VKMD";
    
    $this->vkm->pdf->rapport_titel='Vergelijkende kostenmaatstaf';
		$this->vkm->writeRapport();
	}

}
