<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/06 15:48:23 $
File Versie					: $Revision: 1.4 $

$Log: RapportVOLKD_L77.php,v $
Revision 1.4  2020/06/06 15:48:23  rvv
*** empty log message ***

Revision 1.3  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.2  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.1  2018/09/29 16:19:30  rvv
*** empty log message ***

Revision 1.2  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLK_L77.php");

class RapportVOLKD_L77
{
	function RapportVOLKD_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		global $__appvar;
    
    $this->volk=new RapportVOLK_L77($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $this->volk->pdf->rapport_type = "VOLKD";
    $this->volk->verdeling='beleggingscategorie';
    $this->volk->pdf->rapport_titel = "Portefeuille";
    $this->volk->writeRapport();

    
  }
}
?>
