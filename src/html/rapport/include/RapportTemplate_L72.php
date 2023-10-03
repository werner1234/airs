<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/02/08 12:32:32 $
File Versie					: $Revision: 1.5 $

$Log: RapportTemplate_L72.php,v $
Revision 1.5  2017/02/08 12:32:32  rvv
*** empty log message ***

Revision 1.4  2017/01/29 10:25:25  rvv
*** empty log message ***

Revision 1.3  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.2  2016/10/30 13:02:59  rvv
*** empty log message ***

Revision 1.1  2016/09/28 15:53:55  rvv
*** empty log message ***




*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L72
{
	function RapportTemplate_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
		$this->DB = new DB();

    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    $totPagina = ($lastpage-$startpagina);

		$extraPagina=0;
    if(in_array("FRONT",$this->pdf->rapport_typen))
		{
			$startpagina++;
			$totPagina--;
  	}


  	$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

		for($i=$startpagina +1; $i <=$lastpage; $i++)
		{
		 //if ($i < 1)
		//  $i = 1;
		 $vanPagina = ($i-$startpagina)+$extraPagina;
		 $this->pdf->page = $i;
		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		 $this->pdf->SetAutoPageBreak(false);

	  // if($vanPagina <> 0)
	   //{
	 //   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	  //  $this->pdf->SetXY(8,-10);
     // if($i >= $this->pdf->templateVars['VHOPaginas'] && $i <= $this->pdf->templateVars['VHOPaginas2'])
	   //   $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');
     // else
    //    $this->pdf->MultiCell(240,4,'Aan deze opgave kunnen geen rechten worden ontleend.','0','L');
          
    //  $this->pdf->Cell(25,4,$this->pdf->rapport_voettext_rechts,'0','L');
      $this->pdf->SetXY(297-$this->pdf->marge-80,-10);
	    $this->pdf->MultiCell(80,4,vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina ".vertaalTekst("van",$this->pdf->rapport_taal)." $totPagina",'0','R');
	   //}
		}
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->page = $lastpage;
		$this->pdf->rapportCounterLast = $lastpage;
    	$this->pdf->SetTextColor(0,0,0);

	}


	function writeRapport()
	{
		global $__appvar;
	}




}
?>