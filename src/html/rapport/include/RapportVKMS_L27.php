<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/23 08:49:58 $
File Versie					: $Revision: 1.2 $

$Log: RapportVKMS_L27.php,v $
Revision 1.2  2019/01/23 08:49:58  rvv
*** empty log message ***

Revision 1.1  2019/01/19 13:54:10  rvv
*** empty log message ***

Revision 1.2  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

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
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L27
{

	function RapportVKMS_L27($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=false;
		$this->vkm->writeRapport();
	}

}
