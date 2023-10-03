<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2014/06/18 15:48:59 $
File Versie					: $Revision: 1.6 $

$Log: RapportTemplate_L53.php,v $
Revision 1.6  2014/06/18 15:48:59  rvv
*** empty log message ***

Revision 1.5  2014/06/04 16:13:28  rvv
*** empty log message ***

Revision 1.4  2014/05/31 13:51:07  rvv
*** empty log message ***

Revision 1.3  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.2  2014/04/30 16:03:17  rvv
*** empty log message ***

Revision 1.1  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.3  2014/04/16 15:51:22  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L53
{
	function RapportTemplate_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;


    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=1;
      $paginaNummerStart=1;
      if(!isset($this->pdf->rapportNewPage))
        $this->pdf->rapportNewPage=2;
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

	  	// $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
		  // $this->pdf->Rect($this->pdf->marge, 22, 280, 8 , 'F');


       $this->pdf->SetXY($this->pdf->marge,70);
       $this->pdf->SetWidths(array(10,140));
  	   $this->pdf->SetAligns(array('L','L'));
       $this->pdf->SetFont($pdfObject->rapport_font,'B',14);
       $this->pdf->SetTextColor(127);
	     $this->pdf->row(array('',vertaalTekst("Inhoudsopgave",$this->pdf->rapport_taal))); 
       
	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(10,100,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	     $inhoudsItems=array('PERFPaginas'=>vertaalTekst('Overzicht resultaat',$this->pdf->rapport_taal),
	                         'ATTPaginas'=>vertaalTekst('Vermogensontwikkeling',$this->pdf->rapport_taal),
                           'CASHYPaginas'=>vertaalTekst('Kasstroom projectie',$this->pdf->rapport_taal),
                           'MUTPaginas'=>vertaalTekst('Mutatie overzicht',$this->pdf->rapport_taal),
                           'PERFGPaginas'=>vertaalTekst('Historisch rendement',$this->pdf->rapport_taal),
                           'RISKPaginas'=>vertaalTekst('Risico Portefeuille',$this->pdf->rapport_taal),
                           'GRAFIEKPaginas'=>vertaalTekst('Vermogensallocatie',$this->pdf->rapport_taal),
                           'TRANSPaginas'=>vertaalTekst('Overzicht transacties',$this->pdf->rapport_taal),
//                           'ENDPaginas'=>vertaalTekst('Disclamer',$this->pdf->rapport_taal),
                           'MUTPaginas'=>vertaalTekst('Kosten',$this->pdf->rapport_taal),
                           'VOLKPaginas'=>vertaalTekst('Overzicht portefeuille',$this->pdf->rapport_taal));
           
       $this->pdf->SetFont($this->pdf->rapport_font,'',12);
       $characterwidth=&$this->pdf->CurrentFont['cw'];
       $n=1;
       foreach ($this->pdf->templateVars as $key=>$value)
       {
          if($inhoudsItems[$key]) //$this->pdf->templateVarsOmschrijving
          {
            if($key=='ENDPaginas')
            {
              $this->pdf->SetWidths(array(100,50,7));
              $this->pdf->row(array('',$inhoudsItems[$key],$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));              
            }
            else
            {
              $this->pdf->SetWidths(array(10,100,7));
              $text=$inhoudsItems[$key].' ';
            //$stringWidth=$this->pdf->GetStringWidth($text);
          //  $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
           // $text.=str_repeat('.',$dots-3);
              $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
            }
            //$startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
            $n++;
          }
        }
		  }
      //$this->pdf->SetTextColor(0);
		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-11);


	  //   $extraBlank=1;

	   if($i >= $startpagina+$paginaNummerStart)
	   {
	     $Y=$this->pdf->getY();


	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
	

	     $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,vertaalTekst($this->pdf->rapport_voettext,$this->pdf->rapport_taal),'0','L');
	     $this->pdf->setY($Y);

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