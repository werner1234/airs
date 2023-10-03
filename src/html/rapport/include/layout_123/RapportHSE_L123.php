<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_123/RapportOIS_L123.php");

//ini_set('max_execution_time',60);
class RapportHSE_L123
{

	function RapportHSE_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->ois = new RapportOIS_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht liquiditeiten";
    $this->ois->filterCategorie='H-MoneyMkt';
    $this->ois->filterVariabele='hoofdcategorie';
    $this->ois->selectVariabele='beleggingscategorie';
	}
 

	function writeRapport()
	{
		global $__appvar;
		$this->ois->writeRapport();
  
  }
  
 
}
?>