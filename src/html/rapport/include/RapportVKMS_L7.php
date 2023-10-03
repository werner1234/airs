<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/15 18:29:05 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMS_L7.php,v $
Revision 1.1  2020/02/15 18:29:05  rvv
*** empty log message ***

Revision 1.1  2019/01/23 16:27:16  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L7
{

	function RapportVKMS_L7($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=false;
    $this->vkm->pdf->rapport_type = "VKM";
		$this->vkm->writeRapport();
	}

}
