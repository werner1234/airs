<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/11/07 16:45:15 $
File Versie					: $Revision: 1.5 $

$Log: RapportTemplate_L34.php,v $
Revision 1.5  2015/11/07 16:45:15  rvv
*** empty log message ***

Revision 1.4  2012/09/05 18:19:11  rvv
*** empty log message ***

Revision 1.3  2011/11/16 19:22:09  rvv
*** empty log message ***

Revision 1.2  2011/11/05 16:05:17  rvv
*** empty log message ***

Revision 1.1  2011/10/09 16:54:45  rvv
*** empty log message ***

Revision 1.1  2010/06/09 16:42:57  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L34
{
	function RapportTemplate_L34($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

  	$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

		for($i=$startpagina +1; $i <=$lastpage; $i++)
		{
		 if ($i < 1)
		  $i = 1;
		 $vanPagina = ($i-$startpagina-1)+$extraPagina;
		 $this->pdf->page = $i;
		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
		 $this->pdf->SetAutoPageBreak(false);

	  // if($vanPagina <> 0)
	   //{
	    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	    $this->pdf->SetXY(8,-10);
      if($i >= $this->pdf->templateVars['VHOPaginas'] && $i <= $this->pdf->templateVars['VHOPaginas2'])
	      $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');
      else
        $this->pdf->MultiCell(240,4,'Aan deze opgave kunnen geen rechten worden ontleend.','0','L');
          
      $this->pdf->Cell(25,4,$this->pdf->rapport_voettext_rechts,'0','L');
      $this->pdf->SetXY(8,-10);
	    $this->pdf->MultiCell(282,4,vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina",'0','R');
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