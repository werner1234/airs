<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/04/06 16:16:31 $
File Versie					: $Revision: 1.4 $

$Log: RapportTemplate_L41.php,v $
Revision 1.4  2013/04/06 16:16:31  rvv
*** empty log message ***

Revision 1.3  2013/01/06 10:09:57  rvv
*** empty log message ***

Revision 1.2  2012/12/30 14:27:12  rvv
*** empty log message ***

Revision 1.1  2012/12/02 11:05:56  rvv
*** empty log message ***

Revision 1.5  2012/11/10 15:42:19  rvv
*** empty log message ***

Revision 1.4  2012/09/19 16:53:18  rvv
*** empty log message ***

Revision 1.3  2012/05/02 15:53:13  rvv
*** empty log message ***

Revision 1.2  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.1  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.9  2011/12/24 16:35:21  rvv
*** empty log message ***

Revision 1.8  2011/12/18 14:26:44  rvv
*** empty log message ***

Revision 1.7  2011/06/08 18:19:04  rvv
*** empty log message ***

Revision 1.6  2011/04/11 17:55:48  rvv
*** empty log message ***

Revision 1.5  2011/04/09 14:35:27  rvv
*** empty log message ***

Revision 1.4  2011/04/03 08:35:46  rvv
*** empty log message ***

Revision 1.3  2011/03/17 05:01:11  rvv
*** empty log message ***

Revision 1.2  2011/02/13 17:50:29  rvv
*** empty log message ***

Revision 1.1  2011/02/06 14:36:59  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L41
{
	function RapportTemplate_L41($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;


		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=-1;
      $paginaNummerStart=2;
    }
    else
      $paginaCorrectie=0;
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

       $this->pdf->SetFillColor($this->pdf->midblue[0],$this->pdf->midblue[1],$this->pdf->midblue[2]);
       $this->pdf->Rect(0,0,297,210,'F');

       $this->pdf->SetXY($this->pdf->marge,20);
       $this->pdf->SetWidths(array(0,280,7));
  	   $this->pdf->SetAligns(array('R','L','R'));
       $this->pdf->SetFont('garmond','',30);
       $this->pdf->SetTextColor(255,255,255);
       
	     $this->pdf->row(array('',"Inhoudsopgave"));
	     $this->pdf->SetAligns(array('R','L','L'));
	     $this->pdf->SetWidths(array(80,7,220));
       $this->pdf->ln(20);
       $this->pdf->SetLineWidth(0.176);
       $this->pdf->SetDrawColor(255,255,255);

	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	     $inhoudsItems=array('PERFPaginas'=>'Resultaat en rendementsberekening',
	                         'PERFGPaginas'=>'Attributie-analyse',
	                         'PERFG2Paginas'=>'Rendement per beleggingscategorie over lopende jaar versus bijbehorende benchmark',
	                         'OIBPaginas'=>'Opbouw en verdeling van het vermogen',
	                         'consolidatiePaginas'=>'Verdeling vermogen over de verschillende portefeuilles',
	                         'OIVPaginas'=>'Verdeling vermogen over de verschillende regio\'s en valuta',
	                         'OISPaginas'=>'Verdeling van de zakelijke waarden over de verschillende sectoren',
	                         'RISKPaginas'=>'Verdeling van de vastrentende waarden naar kwaliteit en looptijd',
	                         'VOLKPaginas'=>'Vergelijkend overzicht lopend kalenderjaar',
	                         'VHOPaginas'=>'Vergelijkend historisch overzicht',
	                         'VHO2Paginas'=>'De 10 grootste posities per beleggingscategorie',
	                         'ATTPaginas'=>'Maandelijks verloop en verdeling',
	                         'ATTlaterPaginas'=>'Risico/rendementsanalyse per portefeuille/beheerder',
	                         'ATT2Paginas'=>'Stortingen, onttrekkingen, inkomsten en uitgaven',
	                         'CASHYPaginas'=>'Cashflow overzicht lopend jaar en langere termijn',
	                         'TRANSPaginas'=>'Specificatie effectentransacties',
	                         'MUTPaginas'=>'Mutatie-overzicht',
                           'ENDPaginas'=>'Disclaimer',
                           'HSEPaginas'=>'Vermogensverloop en rendementsbijdrage',
                           'VARPaginas'=>'Kenmerken vastrentende instrumenten',
                           'INDEXPaginas'=>'Indices');
        //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        //$characterwidth=&$this->pdf->CurrentFont['cw'];
        //$n=1;
        asort($this->pdf->templateVars);
        //listarray($this->pdf->templateVars );
        $this->pdf->CellFontStyle=array('',array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize),
                                           array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));

        $this->pdf->line($this->pdf->marge+80,$this->pdf->GetY()-3,297-15,$this->pdf->GetY()-3);
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key])
          {
            //$text=$inhoudsItems[$key].' ';
            //$stringWidth=$this->pdf->GetStringWidth($text);
            //$dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            //$text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$this->pdf->templateVars[$key]+$paginaCorrectieInhoud,$inhoudsItems[$key]));
            //$startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(3);
            //$n++;
          }
        }
        $this->pdf->line($this->pdf->marge+80,$this->pdf->GetY(),297-15,$this->pdf->GetY());
        unset($this->pdf->CellFontStyle);
		  }

$this->pdf->SetTextColor(0,0,0);
		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);


	  //   $extraBlank=1;

	   if($i > $startpagina+$paginaNummerStart)
	   {
	     $Y=$this->pdf->getY();


	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
	     /*

	     $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');
	     $this->pdf->setY($Y);
*/
	     //$tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       $this->pdf->setX($this->pdf->marge,$Y);
       $this->pdf->Cell(260,4,$this->pdf->rapport_voettext,0,0,'R',0);
	     $this->pdf->Cell(297-(2*$this->pdf->marge)-260,4,"$vanPagina",0,0,'R',0);
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