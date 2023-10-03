<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/07/04 16:05:11 $
File Versie					: $Revision: 1.3 $

$Log: RapportTemplate_L37.php,v $
Revision 1.3  2012/07/04 16:05:11  rvv
*** empty log message ***

Revision 1.2  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.1  2012/05/02 15:53:13  rvv
*** empty log message ***

Revision 1.2  2012/04/14 16:51:17  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L37
{
	function RapportTemplate_L37($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;


		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=0;
      $paginaNummerStart=1;
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


       $this->pdf->SetXY($this->pdf->marge,120);

       $this->pdf->SetAligns(array('L','L','L','R','R'));
	     $this->pdf->SetWidths(array(10,200,20,7));
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+1);
       $this->pdf->row(array('',vertaalTekst('Inhoud',$this->pdf->rapport_taal)));
	     $this->pdf->SetWidths(array(10,10,200,20,7));
       $this->pdf->ln();

	     $inhoudsItems=array('PERFPaginas'=>vertaalTekst('Kerngegevens',$this->pdf->rapport_taal),
	                         'OISPaginas'=>vertaalTekst('Zakelijke Waarden',$this->pdf->rapport_taal),
	                         'OIS2Paginas'=>vertaalTekst('Risicomijdende beleggingen',$this->pdf->rapport_taal),
	                         'ATTPaginas'=>vertaalTekst('Performancemeting',$this->pdf->rapport_taal),
	                         'TRANSPaginas'=>vertaalTekst('Transactie-overzicht',$this->pdf->rapport_taal),
	                         'PERFGPaginas'=>vertaalTekst('Historisch rendement',$this->pdf->rapport_taal)
	                          );

	      $numbers=array(1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII');
        $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key])
          {
            $text=$inhoudsItems[$key].' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((200-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$numbers[$n],$text,vertaalTekst('Pagina',$this->pdf->rapport_taal),$this->pdf->templateVars[$key]+$paginaCorrectieInhoud));
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
	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina ".vertaalTekst("van",$this->pdf->rapport_taal)." $totPagina";
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