<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/01/18 13:31:05 $
File Versie					: $Revision: 1.3 $

$Log: RapportVKMS_L87.php,v $
Revision 1.3  2020/01/18 13:31:05  rvv
*** empty log message ***

Revision 1.2  2020/01/12 14:02:20  rvv
*** empty log message ***

Revision 1.1  2019/12/14 17:46:56  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L87
{

	function RapportVKMS_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    
    $this->vkm->waardeZonderKostenKleur=array(236,102,8);
    $this->vkm->waardeNaKostenKleur=array(36,17,47);
    $this->vkm->cumulatieveKostenKleur=array(125,186,185);
		//e VKMS lijngrafiek kleuren aanpassen naar: groen wordt 19.41.75 en rood wordt 180.198.231
	//	$this->vkm->pdf->rapport_titel='';
	}


	function writeRapport()
	{
		$this->vkm->skipSummary=false;
		$this->vkm->skipDetail=true;
    $this->vkm->pdf->templateVars['VKMSPaginas'] = $this->vkm->pdf->page+1;
    $this->vkm->pdf->templateVarsOmschrijving['VKMSPaginas']=$this->vkm->pdf->rapport_titel;
		$this->vkm->writeRapport();
	}

}
