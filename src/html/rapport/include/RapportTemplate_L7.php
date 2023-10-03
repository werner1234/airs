<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/26 16:12:54 $
File Versie					: $Revision: 1.14 $

$Log: RapportTemplate_L7.php,v $
Revision 1.14  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.13  2019/08/17 18:24:30  rvv
*** empty log message ***

Revision 1.12  2016/10/16 15:17:38  rvv
*** empty log message ***

Revision 1.11  2016/10/12 16:20:08  rvv
*** empty log message ***

Revision 1.10  2016/09/19 11:27:55  rvv
*** empty log message ***

Revision 1.9  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.8  2016/04/06 15:30:51  rvv
*** empty log message ***

Revision 1.7  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.6  2016/03/30 10:35:05  rvv
*** empty log message ***

Revision 1.5  2016/03/28 15:53:33  rvv
*** empty log message ***

Revision 1.4  2016/03/27 17:35:07  rvv
*** empty log message ***

Revision 1.3  2015/12/30 19:01:23  rvv
*** empty log message ***

Revision 1.2  2015/12/23 16:21:44  rvv
*** empty log message ***

Revision 1.1  2015/12/21 08:22:32  rvv
*** empty log message ***

Revision 1.5  2015/11/07 16:45:15  rvv
*** empty log message ***

Revision 1.4  2012/09/05 18:19:11  rvv
*** empty log message ***

Revision 1.3  2011/11/16 19:22:09  rvv
*** empty log message ***

Revision 1.2  2011/11/05 16:05:17  rvv
*** empty log message ***

Revision 1.1  2011/10/09 16:54:45  rvv
*** empty log message ***

Revision 1.1  2010/06/09 16:42:57  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L7
{
	function RapportTemplate_L7($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
		$this->DB = new DB();

    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    $totPagina = ($lastpage-$startpagina-1);

    if(!in_array("FRONT",$this->pdf->rapport_typen))
		{
		  $extraPagina=1;
		  $totPagina+=$extraPagina;
  	}
  	else
    {
  	  $extraPagina=1;
     // if($startpagina==0)
       $startpagina++;
    }

//    $paginaCorrectieInhoud=-1;

    if(isset($this->pdf->templateVars['FACTUURpaginasBegin']) && isset($this->pdf->templateVars['FACTUURpaginasEind']) && $this->pdf->templateVars['FACTUURpaginasEind'] <> 0)
    {
      $factuurAanwezig = true;
      $totPagina--;
    }
    else
    {
      $factuurAanwezig=false;
    }

//listarray($this->pdf->templateVars);
    $paginaCorrectieInhoud=$startpagina;
 //   echo "<br> $paginaCorrectieInhoud <br>";
		for($i=$startpagina +1; $i <=$lastpage; $i++)
		{


      if($factuurAanwezig == true && $i > $this->pdf->templateVars['FACTUURpaginasBegin'] && $i <=$this->pdf->templateVars['FACTUURpaginasEind'])
        continue;


		  if ($i < 1)
		    $i = 1;

		  $vanPagina = ($i-$startpagina);
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
        $inhoudsItems=array('PERFPaginas'=>array('Vermogensontwikkeling','Het resultaat dat op uw vermogen is behaald'),
                        'OIBPaginas'=>array('Verdeling naar vermogenscategorie','De omvang en verdeling van uw vermogen'),
                        'RISKPaginas'=>array('Mandaatcontrole','Mandaatcontrole'),
                        'FISCAALPaginas'=>array('Fiscaal overzicht','Fiscaal overzicht'),
                        'KERNVPaginas'=>array('KERNV overzicht','Verdeling over categorieën'),
                        'KERNZPaginas'=>array('KERNZ overzicht','Resultaatverdeling'),
                        'VOLKPaginas'=>array('Portefeuille-overzicht','De koersresultaten van uw portefeuille gedurende het lopende kalenderjaar'),
                        'VHOPaginas'=>array('Vergelijkend historisch overzicht','De koersresultaten van uw portefeuille ten opzicht van de aanschafwaarde'),
                        'TRANSPaginas'=>array('Transactie-overzicht','De aan- en verkopen die hebben plaatsgevonden'),
                        'MUTPaginas'=>array('Mutatie-overzicht in EUR','Een overzicht van de inkomsten,  kosten, overboekingen,  stortingen en onttrekkingen'));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        //$characterwidth=&$this->pdf->CurrentFont['cw'];
   // listarray($this->pdf->templateVars);
        $this->pdf->SetY(118+$this->pdf->templateVars['inhoudsPaginaExtaY']);
        $this->pdf->setWidths(array(5,200,10));
        $this->pdf->SetAligns(array('L','L','R'));
        //$this->pdf->SetTextColor(61,82,101);
        $this->pdf->SetTextColor(1,1,1);
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          
          if(is_array($inhoudsItems[$key]))
          {
          $this->pdf->SetX($this->pdf->marge+5);  
          $text1=vertaalTekst($inhoudsItems[$key][0],$this->pdf->rapport_taal);
          $text2=" - ".vertaalTekst($inhoudsItems[$key][1],$this->pdf->rapport_taal).' ';
          
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $stringWidth1=$this->pdf->GetStringWidth($text1);
          $this->pdf->Cell($stringWidth1,4,$text1);
          
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $stringWidth2=$this->pdf->GetStringWidth($text2);
          $this->pdf->Cell($stringWidth2,4,$text2);
          
          $dots=round(($dotLenght=200-($stringWidth1+$stringWidth2))/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
          $text=str_repeat('.',$dots-3);
          $this->pdf->Cell($dotLenght,4,$text,'','','R');
          $this->pdf->Cell(10,4,$this->pdf->templateVars[$key]-$paginaCorrectieInhoud,'','','R');
          
          //$this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud));
          
          //$startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
          $this->pdf->ln(5);
          }
        }
      }



		//
		 
     $this->pdf->SetAutoPageBreak(false);
   	   $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
       $this->pdf->SetTextColor(61, 82, 101);
       $this->pdf->SetXY(0, -14);
       $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize - 1);
       $this->pdf->MultiCell(297, 4, vertaalTekst("Pag.", $this->pdf->rapport_taal) . " $vanPagina van $totPagina", '0', 'C');
      $this->pdf->SetDrawColor(0,0,0);
      $this->pdf->SetAutoPageBreak(true,15);
	    
              
    //  $this->pdf->Cell(25,4,$this->pdf->rapport_voettext_rechts,'0','L');

	 	}

		$this->pdf->page = $lastpage;
		$this->pdf->rapportCounterLast = $lastpage;
    $this->pdf->SetTextColor(0,0,0);

	}


	function writeRapport()
	{
		global $__appvar;
	}




}
?>