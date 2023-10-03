<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/09 16:56:11 $
File Versie					: $Revision: 1.4 $

$Log: RapportTemplate_L77.php,v $
Revision 1.4  2020/05/09 16:56:11  rvv
*** empty log message ***

Revision 1.3  2018/10/27 16:49:57  rvv
*** empty log message ***

Revision 1.2  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***

Revision 1.3  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.2  2018/04/22 09:30:29  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:18:39  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:05  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L77
{
	function RapportTemplate_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
	  $this->pdf = &$pdf;
      $this->pdf->rapport_type = "FOOTER";
      $lastpage = $this->pdf->page;


    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=1;
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
    $voetTekst= vertaalTekst("Productiedatum", $this->pdf->rapport_taal) . ' ' . date("j", mktime()) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", mktime())], $this->pdf->rapport_taal) . " " . date("Y", mktime());;
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

          
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $n=1;

        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($this->pdf->templateVarsOmschrijving[$key])
            $text=vertaalTekst($this->pdf->templateVarsOmschrijving[$key],$this->pdf->rapport_taal).' ';
          else
            $text='';

          if($text<>'') //$this->pdf->templateVarsOmschrijving
          {
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
           // $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
            $n++;
          }
        }
		}


   if($i>$this->pdf->templateVars['inhoudsPagina'])
   {
     $this->pdf->SetAutoPageBreak(false);
     $this->pdf->SetY(-12);
   //  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
     $this->pdf->cell(100, 4,$voetTekst,false,false,'L');

     // tekst in midden van footer plaatsen
     $this->pdf->setX($this->pdf->w/2 - 25);

     if ( $this->pdf->lastPOST['anoniem'] === '1' ) {
       $this->pdf->cell(100, 4,vertaalTekst('Portefeuillenummer' ,$this->pdf->rapport_taal) .': ' . substr($this->pdf->portefeuilledata['Client'], -4),false,false,'L');
     } else {
       $this->pdf->cell(100, 4,vertaalTekst('Portefeuillenummer' ,$this->pdf->rapport_taal) .': ' . $this->pdf->rapport_portefeuille,false,false,'L');
     }


     $this->pdf->setX($this->pdf->w-$this->pdf->marge);
     $this->pdf->cell(4, 4, vertaalTekst('Pagina',$this->pdf->rapport_taal).' '. ($i-$startpagina+1).' '.vertaalTekst('van' ,$this->pdf->rapport_taal).' '.($totPagina),false,false,'R');
                 //Cell($w,$h=0,$txt='',                                                                                                                                                $border=0,$ln=0,$align='',$fill=0,$link='')
     $this->pdf->SetAutoPageBreak(true,15);
   }

	}
   

		$this->pdf->page = $lastpage;
		$this->pdf->rapportCounterLast = $lastpage;
	}


	function writeRapport()
	{
		global $__appvar;
	}


}
?>
