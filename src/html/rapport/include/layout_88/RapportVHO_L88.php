<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/28 15:46:18 $
File Versie					: $Revision: 1.1 $

$Log: RapportVHO_L88.php,v $
Revision 1.1  2020/03/28 15:46:18  rvv
*** empty log message ***

Revision 1.1  2020/03/21 12:35:10  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_88/RapportVOLK_L88.php");

class RapportVHO_L88
{

	function RapportVHO_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  
  
		$this->vho=new RapportVOLK_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->vho->pdf->rapport_titel = "Vergelijkend historisch overzicht";
    $this->vho->vho=true;
    $this->vho->pdf->rapport_type='VHO';

	}


	function writeRapport()
	{
		$this->vho->writeRapport();
	}

}
