<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/01/13 19:10:29 $
File Versie					: $Revision: 1.8 $

$Log: RapportTemplate_L35.php,v $
Revision 1.8  2018/01/13 19:10:29  rvv
*** empty log message ***

Revision 1.7  2017/11/08 17:12:56  rvv
*** empty log message ***

Revision 1.6  2016/10/16 15:14:53  rvv
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

class RapportTemplate_L35
{
	function RapportTemplate_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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


       $this->pdf->SetXY($this->pdf->marge,20);
       $this->pdf->SetWidths(array(0,280,7));
  	   $this->pdf->SetAligns(array('R','C','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+8);
       $this->pdf->SetTextColor(0,0,0);
	     $this->pdf->row(array('',vertaalTekst("Inhoudsopgave",$this->pdf->rapport_taal)));
	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,220,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


       $inhoudsItems = array(
         'PERFPaginas'            => 'Kerngegevens rapportage',
         'PERFGPaginas'           => 'Rendement op het belegd vermogen versus gewogen benchmark',
         'PERFG2Paginas'          => 'Rendement per beleggingscategorie over lopende jaar versus bijbehorende benchmark',
         'PERFG3Paginas'          => 'Historisch rendement per beleggingscategorie versus bijbehorende benchmark',
         'OIBPaginas'             => 'Portefeuille in relatie tot de strategische weging',
         'consolidatiePaginas'    => 'Verdeling vermogen over de verschillende portefeuilles',
         'OIVPaginas'             => 'Verdeling vermogen over de verschillende regio\'s en valuta',
         'OISPaginas'             => 'Verdeling van de zakelijke waarden over de verschillende sectoren',
         'RISKPaginas'            => 'Verdeling van de vastrentende waarden naar kwaliteit en looptijd',
         'VOLKPaginas'            => 'Vergelijkend overzicht lopend kalenderjaar',
         'VHOPaginas'             => 'Portefeuille overzicht',
         'VHO2Paginas'            => 'De 10 grootste posities per beleggingscategorie',
         'ATTPaginas'             => 'Performance en attributie-overzicht per beleggingscategorie en totaal',
         'ATTlaterPaginas'        => 'Risico/rendementsanalyse per portefeuille/beheerder',
         'ATT2Paginas'            => 'Stortingen, onttrekkingen, inkomsten en uitgaven',
         'CASHYPaginas'           => 'Cashflow overzicht vastrentende waarden',
         'TRANSPaginas'           => 'Specificatie effectentransacties',
         'MUTPaginas'             => 'Mutatie-overzicht',
         'KERNVPaginas'           => 'Performancemeting over de categorieën',
         'KERNZPaginas'           => 'Performancemeting over de categorieën',
         'HSEPaginas'             => 'Performance overzicht',
         'TRANSFeePaginas'        => 'Specificatie effectentransacties',

         'INDEXPaginas'           => 'Indices',
         'DOORKIJKPaginas'        => 'Allocaties inclusief uitsplitsing zakelijke waarden',
         'VARPaginas'             => 'Allocaties inclusief uitsplitsing zakelijke waarden',
         'DOORKIJKVRPaginas'      => 'Allocaties inclusief uitsplitsing op vastrentende waarden',
         'PERFDPaginas'           => 'Kerngegevens rapportage bij categorie',
       );

        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
       // $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
//        debug($this->pdf->templateVars);
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key])
          {
            $text=vertaalTekst($inhoudsItems[$key],$this->pdf->rapport_taal).' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array($n,$text,$this->pdf->templateVars[$key]-$startpagina+$paginaCorrectieInhoud));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(3);
            $n++;
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
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
	     /*

	     $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');
	     $this->pdf->setY($Y);
*/
	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	     $this->pdf->MultiCell(273,4,$tekst,0,'R');
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