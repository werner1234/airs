<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/09 15:52:19 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMS_L48.php,v $
Revision 1.1  2019/01/09 15:52:19  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L48
{

	function RapportVKMS_L48($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
		$this->vkm->skipLangeTermijn=false;
		if(isset($this->vkm->pdf->vmkHeaderOnderdrukken))
    {
      unset($this->vkm->pdf->vmkHeaderOnderdrukken);
    }
		$this->vkm->writeRapport();
	}

}
