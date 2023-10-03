<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/21 12:35:10 $
File Versie					: $Revision: 1.1 $

$Log: RapportPERFG_L88.php,v $
Revision 1.1  2020/03/21 12:35:10  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_88/RapportATT_L88.php");

class RapportPERFG_L88
{

	function RapportPERFG_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  
    $rapportageDatumVanaf=substr($pdf->PortefeuilleStartdatum,0,10);
		$this->att=new RapportATT_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->att->pdf->rapport_titel = "Beleggingsresultaat jaren";
    $this->att->jaren=true;
    $this->att->pdf->rapport_type='PERFG';

	}


	function writeRapport()
	{

    //$this->att->pdf->templateVars['PERFGPaginas'] = $this->vkm->pdf->page+1;
   // $this->att->pdf->templateVarsOmschrijving['PERFGPaginas']=$this->vkm->pdf->rapport_titel;
		$this->att->writeRapport();
	}

}
