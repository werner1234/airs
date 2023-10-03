<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.2 $

$Log: RapportVKMS_L66.php,v $
Revision 1.2  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.1  2018/07/07 17:35:19  rvv
*** empty log message ***

Revision 1.1  2018/04/22 09:30:29  rvv
*** empty log message ***

Revision 1.2  2018/01/18 23:43:10  rvv
*** empty log message ***

Revision 1.1  2017/12/16 18:44:16  rvv
*** empty log message ***

Revision 1.1  2017/06/25 14:49:37  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMD_L66.php");

class RapportVKMS_L66
{

	function RapportVKMS_L66($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMD_L66($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=true;
		$this->vkm->writeRapport();
	}

}
