<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_117/RapportOIS_L117.php");

//ini_set('max_execution_time',60);
class RapportOIR_L117
{

	function RapportOIR_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->ois = new RapportOIS_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht aandelen naar regio";
    $this->ois->filterCategorie='HAA-Equity';
    $this->ois->filterVariabele='hoofdcategorie';
    $this->ois->selectVariabele='regio';
	}
 

	function writeRapport()
	{
		global $__appvar;
		$this->ois->writeRapport();
  
  }
  
 
}
?>