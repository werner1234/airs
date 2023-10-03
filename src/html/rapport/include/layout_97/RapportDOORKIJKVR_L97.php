<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/29 16:23:08 $
File Versie					: $Revision: 1.3 $

$Log: RapportDOORKIJK_L97.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_97/RapportDOORKIJK_L97.php");

class RapportDOORKIJKVR_L97
{
	function RapportDOORKIJKVR_L97($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->doorkijk=new RapportDOORKIJK_L97($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    
		$this->pdf = $pdf;
		$this->pdf->rapport_type = "DOORKIJKVR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Allocaties inclusief uitsplitsing";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
		if($this->pdf->lastPOST['debug'])
		  $this->debug=true;
		else
			$this->debug=false;
		$this->debugData=array();
		$this->consolidatie=false;
		
	}

	
	function writeRapport()
	{
	  $this->doorkijk->categorie='Obligaties';
    $this->doorkijk->writeRapport();
	}
}