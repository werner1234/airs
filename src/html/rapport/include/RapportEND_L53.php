<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2014/05/05 15:52:25 $
File Versie					: $Revision: 1.1 $

$Log: RapportEND_L53.php,v $
Revision 1.1  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.3  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.2  2014/04/23 16:18:44  rvv
*** empty log message ***

Revision 1.1  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportEND_L53
{
	function RapportEND_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Disclamer";
	}

	function writeRapport()
	{
	  global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
    

	}
}
?>