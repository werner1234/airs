<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 14:09:21 $
File Versie					: $Revision: 1.1 $

$Log: RapportTemplate_L94.php,v $


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L94
{
	function RapportTemplate_L94($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    

    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=0;
      $paginaNummerStart=1;
      if(!isset($this->pdf->rapportNewPage))
        $this->pdf->rapportNewPage=1;
    }
    else
    {
      if(!isset($this->pdf->rapportNewPage))
        $this->pdf->rapportNewPage=1;
      $paginaCorrectie=1;
    }
      
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;  
    $paginaCorrectieInhoud=$paginaCorrectie;
    $totPagina = ($lastpage-$startpagina+$paginaCorrectie);//-1
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
       unset($this->pdf->CellBorders);
       $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  	 $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		   //$this->pdf->Rect($this->pdf->marge, 31, 297-2*$this->pdf->marge, 8 , 'F');


       $this->pdf->SetXY($this->pdf->marge,33);
       $this->pdf->SetWidths(array(0,280,7));
  	   $this->pdf->SetAligns(array('R','C','R'));
       //$this->pdf->SetFont($pdfObject->rapport_font,'',$this->pdf->rapport_fontsize+8);
	     //$this->pdf->row(array('',"Inhoudsopgave"));
	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,220,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	     $inhoudsItems=array('PERFPaginas'=>'Performance over de beleggingscategorieën',
													 'PERFDPaginas'=>'Performance over de portefeuilles',
	                         'ATTPaginas'=>'Performancemeting in de tijd',
                           'OIRPaginas'=>'Onderverdeling in regio',
													 'GRAFIEKPaginas'=>"Verdeling",
													 'GRAFIEK2Paginas'=>'Rekeningverdeling per asset class',
                           'OISPaginas'=>'Onderverdeling in beleggingssector',
                           'OIBPaginas'=>'Onderverdeling in beleggingscategorieën',
                           'OIVPaginas'=>'Onderverdeling in valuta',
                           'VKMPaginas'=>'Vergelijkende kostenmaatstaf',
                           'CASHYPaginas'=>'Cashflow overzicht lopende jaar en op langere termijn',
                           'PERFGPaginas'=>'Historische performanceverloop',
                           'AFMPaginas'=>'Onderverdeling in AFM beleggingscategorieën',
                           'VOLKPaginas'=>'Vergelijkend overzicht lopend kalenderjaar',
                           'VHOPaginas'=>'Vergelijkend historisch overzicht',
                           'HSEPaginas'=>'Huidige samenstelling effectenportefeuille',
                           'MUTPaginas'=>'Mutatie-overzicht',
                           'TRANSPaginas'=>'Transactie-overzicht',
                           'INDEXPaginas'=>'Indices');
          
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key]) //$this->pdf->templateVarsOmschrijving
          {
            $text=$inhoudsItems[$key].' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
            $n++;
          }
        }
		  }

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);


	  //   $extraBlank=1;

	   if($i >= $startpagina+$paginaNummerStart)
	   {
	     $Y=$this->pdf->getY();


	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
       
  
		  //$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		     
 //      $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');

       $this->pdf->setXY($this->pdf->marge,$Y);
       
	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina";// van $totPagina"; $portefeuille
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,$tekst,'0','R');

	   //  $this->pdf->Rect(10, $Y, 200,2, 'DF');
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