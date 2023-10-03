<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/23 18:32:59 $
File Versie					: $Revision: 1.1 $

$Log: RapportMOD_L77.php,v $
Revision 1.1  2019/02/23 18:32:59  rvv
*** empty log message ***

Revision 1.5  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.4  2015/06/13 13:16:01  rvv
*** empty log message ***

Revision 1.3  2015/04/04 15:15:15  rvv
*** empty log message ***

Revision 1.2  2014/09/03 15:56:32  rvv
*** empty log message ***

Revision 1.1  2014/08/23 15:45:01  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportFRONT_L77.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIB_L77.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLKD_L77.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVAR_L77.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportEND_L77.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportTemplate_L77.php");

class RapportMOD_L77
{
	function RapportMOD_L77($pdf, $portefeuille, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MOD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_naam1 = str_replace("Modelportefeuille ","",$this->pdf->rapport_naam1);
  	$this->pdf->rapport_koptext = "Portefeuille voorstel ".$this->pdf->rapport_naam1."\n".$this->pdf->selectData['mutatieportefeuille_customNaam'];
		$this->pdf->rapport_titel = "";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatum = $rapportageDatum;
	}

	
	function writeRapport()
	{
		global $__appvar;
    $this->pdf->DefOrientation='L';
    $this->pdf->wPt=$this->pdf->fhPt;
    $this->pdf->hPt=$this->pdf->fwPt;
    
    $this->pdf->pagebreak = 190;
    $this->pdf->__appvar = $__appvar;
    $this->pdf->lastPOST = $_POST;
    $this->pdf->rapportageValuta = "EUR";
    $this->pdf->ValutaKoersEind  = 1;
    $this->pdf->ValutaKoersStart = 1;
    $this->pdf->ValutaKoersBegin = 1;
    $this->pdf->modelRapport = true;
    
    
    $rapport=new RapportFRONT_L77($this->pdf,$this->portefeuille,$this->rapportageDatum ,$this->rapportageDatum);
    $rapport->writeRapport();
    $rapport=new RapportOIB_L77($this->pdf,$this->portefeuille,$this->rapportageDatum ,$this->rapportageDatum);
    $rapport->writeRapport();
    $rapport=new RapportVOLKD_L77($this->pdf,$this->portefeuille,$this->rapportageDatum ,$this->rapportageDatum);
    $rapport->writeRapport();
    $this->pdf->excelData=$rapport->volk->xls;
    
    $rapport=new RapportVAR_L77($this->pdf,$this->portefeuille,$this->rapportageDatum ,$this->rapportageDatum);
    $rapport->writeRapport();
    $rapport=new RapportEND_L77($this->pdf,$this->portefeuille,$this->rapportageDatum ,$this->rapportageDatum);
    $rapport->writeRapport();
    $rapport = new RapportTemplate_L77($this->pdf, $this->portefeuille, $this->rapportageDatum ,$this->rapportageDatum);

	}
}
?>