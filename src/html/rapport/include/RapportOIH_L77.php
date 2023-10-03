<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/29 13:56:12 $
File Versie					: $Revision: 1.1 $

$Log: RapportOIH_L77.php,v $


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLK_L77.php");

class RapportOIH_L77
{
	function RapportOIH_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLKD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
	}

	


	function writeRapport()
	{
    $volk=new RapportVOLK_L77($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $volk->pdf->rapport_type = "VOLKD";
    $volk->verdeling='beleggingscategorie';
    $volk->pdf->rapport_titel = "Portefeuille Illiquide";
    $volk->OIHindeling=true;
    $volk->tijdelijkeRapportageFilter=" AND TijdelijkeRapportage.Hoofdcategorie='Illiquide' ";
    $volk->writeRapport();
  }
}
?>
