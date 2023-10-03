<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/04/06 16:16:31 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportEND_L41.php,v $
 		Revision 1.1  2013/04/06 16:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/01/06 10:09:57  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/12/30 14:27:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportEND_L41
{
	function RapportEND_L41($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Disclaimer";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
	  $this->pdf->SetWidths(array(10,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));


  }
}
?>