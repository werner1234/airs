<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2014/12/31 18:09:06 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIR_L57.php,v $
Revision 1.2  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.1  2014/12/28 14:29:08  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L57.php");

class RapportOIR_L57
{
	function RapportOIR_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    

	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

function setFontColor($type)
{
  if($type=='fonds')
   	$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
  else
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
}

	function writeRapport()
	{
	  $oir=new RapportOIS_L57($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $oir->pdf->hoofdSortering='Regio';
    $oir->pdf->tweedeSortering='Beleggingscategorie';
    $oir->pdf->rapport_titel = "Onderverdeling in regio";
    $oir->paginaVar='OIRPaginas';
    $oir->writeRapport();



	}
}
?>