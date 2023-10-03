<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_123/RapportOIS_L123.php");

//ini_set('max_execution_time',60);
class RapportOIH_L123
{

	function RapportOIH_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->ois = new RapportOIS_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht alternatieve beleggingen";
    $this->ois->filterCategorie='H-Alternatives';
    $this->ois->filterVariabele='hoofdcategorie';
    $this->ois->selectVariabele='beleggingscategorie';
    $this->ois->skipVerdeling = true;
	}
 

	function writeRapport()
	{
		global $__appvar;
		$this->ois->writeRapport();
  
  }
  
 
}
?>