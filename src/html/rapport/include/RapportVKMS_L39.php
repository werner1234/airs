<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.2 $

$Log: RapportVKMS_L39.php,v $
Revision 1.2  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.1  2018/04/21 17:55:51  rvv
*** empty log message ***

Revision 1.1  2017/06/25 14:49:37  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMD_L39.php");

class RapportVKMS_L39
{

	function RapportVKMS_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMD_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);

	//	$this->vkm->pdf->rapport_titel='';
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->writeRapport();
	}

}
