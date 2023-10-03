<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.3 $

$Log: RapportVKMD_L107.php,v $
Revision 1.3  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.2  2018/02/28 16:48:22  rvv
*** empty log message ***

Revision 1.1  2018/02/24 18:33:46  rvv
*** empty log message ***

Revision 1.1  2018/02/22 07:45:39  rvv
*** empty log message ***

Revision 1.1  2018/01/18 13:36:02  rvv
*** empty log message ***

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
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMS_L107.php");

class RapportVKMD_L107
{

	function RapportVKMD_L107($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMS_L107($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=true;
		$this->vkm->skipDetail=false;
		$this->vkm->skipLangeTermijn=true;
		unset($this->vkm->pdf->vmkHeaderOnderdrukken);
		$this->vkm->writeRapport();
	}

}
