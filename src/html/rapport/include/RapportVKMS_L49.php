<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.3 $

$Log: RapportVKMS_L49.php,v $
Revision 1.3  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.2  2018/06/30 17:43:55  rvv
*** empty log message ***

Revision 1.1  2017/06/25 14:49:37  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMD_L49.php");

class RapportVKMS_L49
{

	function RapportVKMS_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMD_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->vkm->pdf->marge=$this->vkm->margeBackup;
		$this->vkm->pdf->rapport_type = "VKMS";
		$this->vkm->pdf->rapport_fontsize=$this->vkm->fontsizeBackup;
		$this->vkm->pdf->SetLeftMargin($this->vkm->pdf->marge);
		$this->vkm->pdf->SetRightMargin($this->vkm->pdf->marge);
		$this->vkm->pdf->SetTopMargin($this->vkm->pdf->marge);
		$this->vkm->pdf->rapport_titel='';
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->writeRapport();
	}

}
