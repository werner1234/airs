<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.7 $

$Log: RapportVKMS_L25.php,v $
Revision 1.7  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.6  2018/01/13 19:10:29  rvv
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
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMS_L57.php");

class RapportVKMS_L25
{

	function RapportVKMS_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMS_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=false;
    $this->vkm->pdf->rapport_type = "VKMS";
		$this->vkm->writeRapport();
	}
  
}
