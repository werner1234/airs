<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/16 15:57:02 $
File Versie					: $Revision: 1.13 $

$Log: RapportTemplate_L75.php,v $
Revision 1.13  2020/05/16 15:57:02  rvv
*** empty log message ***

Revision 1.12  2019/10/02 15:12:58  rvv
*** empty log message ***

Revision 1.11  2019/09/07 16:07:48  rvv
*** empty log message ***

Revision 1.10  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.9  2018/07/28 14:45:48  rvv
*** empty log message ***

Revision 1.8  2018/07/25 15:37:42  rvv
*** empty log message ***

Revision 1.7  2018/07/11 16:16:40  rvv
*** empty log message ***

Revision 1.6  2018/06/13 15:54:32  rvv
*** empty log message ***

Revision 1.5  2018/04/18 16:17:44  rvv
*** empty log message ***

Revision 1.4  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.3  2018/03/31 18:06:01  rvv
*** empty log message ***

Revision 1.2  2018/03/14 17:17:41  rvv
*** empty log message ***

Revision 1.1  2018/02/28 16:48:45  rvv
*** empty log message ***

Revision 1.4  2017/01/15 08:01:57  rvv
*** empty log message ***

Revision 1.3  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.2  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.1  2016/05/15 17:15:00  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L75
{
	function RapportTemplate_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
			$paginaNummerStart=0;
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



        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($this->pdf->templateVarsOmschrijving[$key]) //
          {
						if(!$this->pdf->lastPOST['nummeringUit'])
						{
							$text = vertaalTekst($this->pdf->templateVarsOmschrijving[$key],$this->pdf->rapport_taal) . ' ';
							$stringWidth = $this->pdf->GetStringWidth($text);
							$dots = round((220 - $stringWidth) / ($this->pdf->CurrentFont['cw']['.'] * $this->pdf->FontSize / 1000));
							$text .= str_repeat('.', $dots - 3);
							$this->pdf->row(array('', $text, $this->pdf->templateVars[$key] + $paginaCorrectieInhoud - $startpagina));
						}
						else
						{
							$this->pdf->row(array('', $this->pdf->templateVarsOmschrijving[$key]));
						}
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(2);

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

	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);

       $this->pdf->setXY($this->pdf->marge,$Y);
			 $this->pdf->SetFont('arial','',$this->pdf->rapport_voetfontsize);
			 $this->pdf->Cell(100,4,'');
			 $this->pdf->setXY($this->pdf->marge,$Y);
			 $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_voetfontsize);
			 if($_POST['anoniem']==1)
				 $this->pdf->Cell(100,4,vertaalTekst('Productiedatum',$this->pdf->rapport_taal).' '.date('d-m-Y'),'0','L');
			 else
			   $this->pdf->Cell(100,4,$this->pdf->rapport_client.' / '.$this->pdf->rapport_clientVermogensbeheerderReal.' / '.vertaalTekst('Productiedatum',$this->pdf->rapport_taal).' '.date('d-m-Y'),'0','L');
			 if(!$this->pdf->lastPOST['nummeringUit'])
			 {
				 $tekst = vertaalTekst("Pagina", $this->pdf->rapport_taal) . " $vanPagina " . vertaalTekst("van", $this->pdf->rapport_taal) . " $totPagina";// van $totPagina"; $portefeuille
				 $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_voetfontsize);
				 $this->pdf->MultiCell(297 - (2 * $this->pdf->marge) - 100, 4, $tekst, '0', 'R');
			 }
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