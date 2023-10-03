<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/23 16:39:00 $
File Versie					: $Revision: 1.1 $

$Log: RapportPORTAL_L51.php,v $
Revision 1.1  2020/05/23 16:39:00  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportOIS.php");

class RapportPORTAL_L51
{
	function RapportPORTAL_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->pdf->rapport_OIS_zorgplichtpercentage=false;
		$this->ois=new RapportOIS($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->ois->pdf->rapport_type = "PORTAL";
	}

	function writeRapport()
	{
    $this->ois->writeRapport();
	}
}
?>