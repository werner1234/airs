<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/25 15:36:13 $
File Versie					: $Revision: 1.4 $

$Log: RapportVKMS_L75.php,v $
Revision 1.4  2020/07/25 15:36:13  rvv
*** empty log message ***

Revision 1.3  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.2  2018/06/17 15:51:53  rvv
*** empty log message ***

Revision 1.1  2018/06/16 17:42:56  rvv
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
include_once($__appvar["basedir"]."/html/rapport/include/RapportVKMD_L75.php");

class RapportVKMS_L75
{

	function RapportVKMS_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKMD_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=true;
		$this->vkm->pdf->rapport_titel = vertaalTekst("Kosten-analyse, samenvatting",$this->pdf->rapport_taal);
		$this->vkm->pdf->rapport_type = "VKMS";
		$this->vkm->writeRapport();
	}

}
