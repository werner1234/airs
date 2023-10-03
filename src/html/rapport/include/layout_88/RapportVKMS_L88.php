<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/21 12:35:10 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMS_L88.php,v $
Revision 1.1  2020/03/21 12:35:10  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMS_L88
{

	function RapportVKMS_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->vkm=new RapportVKM($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->vkm->pdf->rapport_type = "VKMS";
    $this->vkm->waardeZonderKostenKleur=array(100,100,200);
    $this->vkm->waardeNaKostenKleur=array(19,41,75);
    $this->vkm->cumulatieveKostenKleur=array(180,198,231);
    $this->vkm->nullTonen=true;
    $this->vkm->btwTonen=true;

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
