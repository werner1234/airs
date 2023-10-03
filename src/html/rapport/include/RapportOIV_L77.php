<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/08 05:43:48 $
 		File Versie					: $Revision: 1.18 $

 		$Log: RapportOIV_L77.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIB_L77.php");

class RapportOIV_L77
{
	function RapportOIV_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	}

	function writeRapport()
	{
    $oib=new RapportOIB_L77($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $oib->pdf->rapport_type = "OIV";
    $oib->pdf->rapport_titelKort = vertaalTekst("Vermogensverdeling Liquide",$this->pdf->rapport_taal);
    $oib->pdf->rapport_titel = $this->pdf->rapport_titelKort." ".vertaalTekst("per",$this->pdf->rapport_taal)." ".date('d.m.Y',$this->pdf->rapport_datum);
    $oib->tijdelijkeRapportageFilter=" AND TijdelijkeRapportage.Hoofdcategorie='Liquide' ";
    $oib->writeRapport();
  }
}
?>