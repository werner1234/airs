<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/16 08:41:15 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMS_L54.php,v $
Revision 1.1  2019/01/16 08:41:15  rvv
*** empty log message ***

Revision 1.3  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.2  2018/01/18 23:43:10  rvv
*** empty log message ***

Revision 1.1  2017/12/16 18:44:16  rvv
*** empty log message ***

Revision 1.1  2017/06/25 14:49:37  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L54
{

	function RapportVKMS_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
