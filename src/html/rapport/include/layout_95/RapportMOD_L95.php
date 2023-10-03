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
include_once($__appvar["basedir"]."/html/rapport/include/layout_95/RapportGRAFIEK_L95.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_95/RapportVOLK_L95.php");

class RapportMOD_L95
{
	function RapportMOD_L95($pdf, $portefeuille, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MOD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_naam1 = str_replace("Modelportefeuille ","",$this->pdf->rapport_naam1);
  	$this->pdf->rapport_koptext = "Portefeuille voorstel ".$this->pdf->rapport_naam1."\n".$this->pdf->selectData[mutatieportefeuille_customNaam];
		$this->pdf->rapport_titel = "";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatum = $rapportageDatum;
	}

	
	function writeRapport()
	{
		global $__appvar;
		$mod=1;
		if($mod==1)
    {
      $this->pdf->DefOrientation = 'P';
  
      $this->pdf->wPt=$this->pdf->fwPt;
      $this->pdf->hPt=$this->pdf->fhPt;
      $this->pdf->w=$this->pdf->fw;
      $this->pdf->h=$this->pdf->fh;
    }
    else
    {
      $this->pdf->DefOrientation = 'L';

      $this->pdf->wPt=$this->pdf->fhPt;
      $this->pdf->hPt=$this->pdf->fwPt;
      $this->pdf->w=$this->pdf->fh;
      $this->pdf->h=$this->pdf->fw;
    }
    
    $this->pdf->PageBreakTrigger=$this->pdf->h-$this->pdf->bMargin;
    
    
    
    $this->pdf->__appvar = $__appvar;
    $this->pdf->lastPOST = $_POST;
    $this->pdf->rapportageValuta = "EUR";
    $this->pdf->ValutaKoersEind  = 1;
    $this->pdf->ValutaKoersStart = 1;
    $this->pdf->ValutaKoersBegin = 1;
    $this->pdf->modelRapport = true;
    
    if($mod==1)
    {
      $rapport = new RapportMOD($this->pdf, $this->portefeuille, $this->rapportageDatum);
    }
    else
    {
      $rapport=new RapportVOLK_L95($this->pdf,$this->portefeuille,$this->rapportageDatum ,$this->rapportageDatum);
    }
    $rapport->writeRapport();
    


    $rapport=new RapportGRAFIEK_L95($this->pdf,$this->portefeuille,$this->rapportageDatum ,$this->rapportageDatum);
    $rapport->writeRapport();


	}
}
?>