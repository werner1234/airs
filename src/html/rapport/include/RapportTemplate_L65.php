<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/11 17:30:27 $
File Versie					: $Revision: 1.10 $

$Log: RapportTemplate_L65.php,v $
Revision 1.10  2020/07/11 17:30:27  rvv
*** empty log message ***

Revision 1.9  2020/04/08 15:42:42  rvv
*** empty log message ***

Revision 1.8  2019/03/31 12:19:56  rvv
*** empty log message ***

Revision 1.7  2019/02/23 18:32:59  rvv
*** empty log message ***

Revision 1.6  2019/01/23 16:27:16  rvv
*** empty log message ***

Revision 1.5  2018/10/21 09:42:37  rvv
*** empty log message ***

Revision 1.4  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.3  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.2  2016/04/21 19:31:19  rvv
*** empty log message ***

Revision 1.1  2015/11/29 13:13:22  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:05  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L65
{
	function RapportTemplate_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	  	 $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
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

	     $inhoudsItems=array('PERFPaginas'=>"Performancemeting (in ".$this->pdf->rapportageValuta.")",
                           'PERFDPaginas'=>'Performance over de beleggingscategorieën',
	                         'ATTPaginas'=>'Performancemeting in de tijd',
                           'OIRPaginas'=>'Onderverdeling in regio',
                           'OISPaginas'=>'Onderverdeling in beleggingssector',
                           'OIBPaginas'=>'Onderverdeling in beleggingscategorieën',
                           'OIVPaginas'=>'Onderverdeling in valuta',
                           'VKMDPaginas'=>'Vergelijkende kostenmaatstaf',
                           'VKMSPaginas'=>'Vergelijkende kostenmaatstaf',
                           'CASHYPaginas'=>'Cashflow overzicht lopende jaar en op langere termijn',
													 'KERNZPaginas'=>'Toelichting Asset Allocatie',
													 'KERNZ2Paginas'=>'Verantwoorde fondsen in Portefeuille',
                           'PERFGPaginas'=>'Rendement lopend kalenderjaar',
                           'HUISPaginas'=>'Portefeuilledetails',
                           'TRANSFEEPaginas'=>'Overzicht DoubleDividend fondsen',
                           'OIHPaginas'=>'Overzicht DoubleDividend fondsen',
                           'SMVPaginas'=>'Portefeuille overzicht DoubleDividend fonfdsen',
                           'KERNVPaginas'=>'Risico karakteristieken',
                           'AFMPaginas'=>'Onderverdeling in AFM beleggingscategorieën',
                           'VOLKPaginas'=>'Vergelijkend overzicht verslagperiode inclusief geschiktheidsverklaring',
                           'VHOPaginas'=>'Vergelijkend historisch overzicht',
                           'HSEPaginas'=>'Huidige samenstelling effectenportefeuille',
                           'MUTPaginas'=>'Mutatie-overzicht',
                           'TRANSPaginas'=>'Transactie-overzicht',
                           'INDEXPaginas'=>'Benchmark');
	     
	     foreach($inhoudsItems as $key=>$value)
         $inhoudsItems[$key]=vertaalTekst($value,$this->pdf->rapport_taal);
	     
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