<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2009/01/20 17:45:20 $
File Versie					: $Revision: 1.1 $

$Log: RapportTemplate_L8.php,v $
Revision 1.1  2009/01/20 17:45:20  rvv
*** empty log message ***

Revision 1.2  2008/07/01 07:12:34  rvv
*** empty log message ***

Revision 1.1  2008/05/16 08:13:46  rvv
*** empty log message ***

Revision 1.2  2008/03/18 12:39:08  rvv
*** empty log message ***

Revision 1.1  2008/03/18 09:56:48  rvv
*** empty log message ***

Revision 1.6  2008/01/23 07:37:03  rvv
*** empty log message ***

Revision 1.5  2007/11/16 11:22:27  rvv
*** empty log message ***

Revision 1.4  2007/10/04 11:57:04  rvv
*** empty log message ***

Revision 1.3  2007/09/26 15:30:33  rvv
*** empty log message ***

Revision 1.2  2007/07/05 12:28:39  rvv
*** empty log message ***

Revision 1.1  2007/06/29 11:38:56  rvv
L14 aanpassingen




*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L8
{
	function RapportTemplate_L8($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
		$this->DB = new DB();

    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    $totPagina = ($lastpage-$startpagina-1);
		for($i=$startpagina +1; $i <=$lastpage; $i++)
		{
		 if ($i < 1)
		  $i = 1; 
		 $vanPagina = ($i-$startpagina-1);
		 $this->pdf->page = $i;
		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);
	   if($vanPagina <> 0)
	   {
	   $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina";
	   $this->pdf->MultiCell(273,4,$tekst,0,'R');
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