<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/10/19 10:58:45 $
File Versie					: $Revision: 1.2 $

$Log: RapportTemplate_L71.php,v $
Revision 1.2  2016/10/19 10:58:45  rvv
*** empty log message ***

Revision 1.1  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/06 14:37:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L71
{
	function RapportTemplate_L71($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
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

		if(isset($this->pdf->templateVars['FACTUURpaginasBegin']) && isset($this->pdf->templateVars['FACTUURpaginasEind']) && $this->pdf->templateVars['FACTUURpaginasEind'] <> 0)
		{
			$factuurAanwezig = true;
			$totPagina--;
		}
		else
		{
			$factuurAanwezig=false;
		}

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


       $this->pdf->SetXY($this->pdf->marge,13);
       $this->pdf->SetWidths(array(0,280,7));
  	   $this->pdf->SetAligns(array('R','C','R'));
       $this->pdf->SetFont($pdfObject->rapport_font,'',$this->pdf->rapport_fontsize+8);

	     $this->pdf->row(array('',"Inhoudsopgave"));

	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,220,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	     $inhoudsItems=array('PERFPaginas'=>'Resultaat en rendementsberekening',
	                         'OIBPaginas'=>'Onderverdeling in beleggingscategorie',
                           'OIVPaginas'=>'Onderverdeling in beleggingscategorie',
	                         'VHOPaginas'=>"Portefeuille overzicht per ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum),
	                         'VOLKPaginas'=>'Vergelijkend overzicht lopend kalenderjaar',
                           'ATTPaginas'=>'Performancemeting',
                           'OIHPaginas'=>'Performance overzicht',
                           'PERFGPaginas'=>'Beleggingsresultaat lopend jaar',
                           'MUTPaginas'=>'Mutatie-overzicht',
                           'RISKPaginas'=>'Risico verdeling');
           
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)  
        {
          if($inhoudsItems[$key])
          {
            $text=$inhoudsItems[$key].' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
            $n++;
          }
        }
		  }

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);



	   if($i > $startpagina+$paginaNummerStart)
	   {
			 if($factuurAanwezig == true && $i > $this->pdf->templateVars['FACTUURpaginasBegin'] && $i <=$this->pdf->templateVars['FACTUURpaginasEind'])
				 continue;
	     $Y=$this->pdf->getY();


	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
	

	     $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,$this->pdf->rapport_voettext,'0','L');
	     $this->pdf->setY($Y);

	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	     $this->pdf->MultiCell(297-2*$this->pdf->marge,4,$tekst,0,'R');
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