<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/10/16 15:14:53 $
File Versie					: $Revision: 1.2 $

$Log: RapportTemplate_L26.php,v $
Revision 1.2  2016/10/16 15:14:53  rvv
*** empty log message ***

Revision 1.1  2010/06/09 16:42:57  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L26
{
	function RapportTemplate_L26($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
		$this->DB = new DB();

    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    $totPagina = ($lastpage-$startpagina-1);

    if(!in_array("FRONT",$this->pdf->rapport_typen))
		{
		  $extraPagina=1;
		  $totPagina+=$extraPagina;
  	}
  	 else
  	  $extraPagina=0;

		if(isset($this->pdf->templateVars['FACTUURpaginasBegin']) && isset($this->pdf->templateVars['FACTUURpaginasEind']) && $this->pdf->templateVars['FACTUURpaginasEind'] <> 0)
		{
			$factuurAanwezig = true;
			$totPagina--;
		}
		else
		{
			$factuurAanwezig=false;
		}

		for($i=$startpagina +1; $i <=$lastpage; $i++)
		{
			if($factuurAanwezig == true && $i > $this->pdf->templateVars['FACTUURpaginasBegin'] && $i <=$this->pdf->templateVars['FACTUURpaginasEind'])
				continue;

		 if ($i < 1)
		  $i = 1;
		 $vanPagina = ($i-$startpagina-1)+$extraPagina;
		 $this->pdf->page = $i;
		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
		 $this->pdf->SetAutoPageBreak(false);

	   if($vanPagina <> 0)
	   {
	    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	    $this->pdf->SetY(-10);
	    $this->pdf->MultiCell(282,4,vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina",'0','R');
	   }
		}
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->page = $lastpage;
		$this->pdf->rapportCounterLast = $lastpage;
	}


	function writeRapport()
	{
		global $__appvar;
	}




}
?>