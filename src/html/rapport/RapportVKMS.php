<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.4 $

$Log: RapportVKMS.php,v $
Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2017/11/12 13:26:02  rvv
*** empty log message ***

Revision 1.2  2017/07/12 10:28:20  rvv
*** empty log message ***

Revision 1.1  2017/04/16 10:33:36  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS
{
	function RapportVKMS($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	   $this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}


	function writeRapport()
	{
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=true;
		$this->vkm->writeRapport();

	}
  
}
