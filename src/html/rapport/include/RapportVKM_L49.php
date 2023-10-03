<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.2 $

$Log: RapportVKM_L49.php,v $
Revision 1.2  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.1  2017/05/17 15:57:50  rvv
*** empty log message ***

Revision 1.5  2017/05/15 06:37:26  rvv
*** empty log message ***

Revision 1.4  2017/05/14 09:57:45  rvv
*** empty log message ***

Revision 1.3  2017/05/13 16:27:34  rvv
*** empty log message ***

Revision 1.2  2017/05/07 08:09:24  rvv
*** empty log message ***

Revision 1.1  2017/05/06 17:28:52  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKM_L49
{

	function RapportVKM_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=false;
		$this->vkm->pdf->rapport_titel='';
		$fontsize=$this->vkm->pdf->rapport_fontsize;
		$marge=$this->vkm->pdf->marge;
		$this->vkm->pdf->rapport_fontsize=$this->vkm->pdf->rapport_fontsize-1;
		$this->vkm->pdf->marge=8;
		$this->vkm->pdf->SetLeftMargin($this->vkm->pdf->marge);
		$this->vkm->pdf->SetRightMargin($this->vkm->pdf->marge);
		$this->vkm->pdf->SetTopMargin($this->vkm->pdf->marge);

		$this->vkm->writeRapport();
		$this->vkm->pdf->rapport_fontsize=$fontsize;
		$this->vkm->pdf->marge=$marge;
		$this->vkm->pdf->SetLeftMargin($marge);
		$this->vkm->pdf->SetRightMargin($marge);
		$this->vkm->pdf->SetTopMargin($marge);

	}

}
