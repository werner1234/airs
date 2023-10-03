<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/13 15:13:06 $
File Versie					: $Revision: 1.1 $

$Log: RapportTemplate_L90.php,v $
Revision 1.1  2020/06/13 15:13:06  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L90
{
	function RapportTemplate_L90($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;


		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=0;
      $paginaNummerStart=0;
    }
    else
    {
      $paginaNummerStart=-1;
      $paginaCorrectie=1;
    }
    $paginaCorrectieInhoud=$paginaCorrectie;
    $totPagina = ($lastpage-$startpagina+$paginaCorrectie);//-1
// echo $this->pdf->templateVars['inhoudsPagina']."for($i=$startpagina ; $i <=$lastpage; $i++) <br>\n";     
		for($i=$startpagina ; $i <=$lastpage; $i++)
		{
		 $vanPagina = ($i-$startpagina+$paginaCorrectie);//-1
		 $this->pdf->page = $i;
		 if($i==$this->pdf->templateVars['inhoudsPagina'])
		 {
		   if($this->pdf->CurOrientation=='P')
		   {
		     $this->pdf->CurOrientation='L';
		     $this->pdf->wPt=$this->pdf->fhPt;
			   $this->pdf->hPt=$this->pdf->fwPt;
			   $this->pdf->w=$this->pdf->fh;
			   $this->pdf->h=$this->pdf->fw;
  		   $this->pdf->PageBreakTrigger=$this->pdf->h-$this->pdf->bMargin;
		   }
	     $this->pdf->SetTextColor(0,0,0);
	     $this->pdf->SetFillColor(0,0,0);

       $this->pdf->SetXY($this->pdf->marge,25);
       $this->pdf->SetWidths(array(0,140,7));
  	   $this->pdf->SetAligns(array('R','L','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+8);
	     $this->pdf->row(array('',vertaalTekst("Inhoudsopgave",$this->pdf->rapport_taal)));
	     $this->pdf->SetWidths(array(20,200,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	     $inhoudsItems=array('OIBPaginas'=>'Onderverdeling in beleggingscategorie',
	                         'ATTPaginas'=>'Beleggingsresultaat',
	                         'PERFPaginas'=>'Ontwikkeling vermogen',
	                         'VHOPaginas'=>'Vermogensoverzicht (met gemiddelde kostprijs)',
	                         'VOLKPaginas'=>'Vermogensoverzicht (met beginkoers)',
	                         'TRANSPaginas'=>'Transactieoverzicht',
                           'MUTPaginas'=>'Mutatie-overzicht',);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];

        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key])
          {
            $text=vertaalTekst($inhoudsItems[$key],$this->pdf->rapport_taal).' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((200-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
          }
        }
		  }

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);


	  //   $extraBlank=1;

	   if($i > $startpagina+$paginaNummerStart)
	   {
	     $Y=$this->pdf->getY();
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetTextColor(0,0,0);
	     $this->pdf->SetFillColor(0,0,0);
//	     $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');
	     $this->pdf->setY($Y);

	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina ".vertaalTekst("van",$this->pdf->rapport_taal)." $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	     $this->pdf->MultiCell(273,4,$tekst,0,'R');
	     //$this->pdf->Rect(10, $Y, 200,2, 'DF');
	    // echo $this->pdf->rapport_fontsize." $tekst <br>\n";
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