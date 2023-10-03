<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/11/25 20:25:19 $
File Versie					: $Revision: 1.6 $

$Log: RapportTemplate_L73.php,v $
Revision 1.6  2017/11/25 20:25:19  rvv
*** empty log message ***

Revision 1.5  2017/10/11 14:56:55  rvv
*** empty log message ***

Revision 1.4  2017/08/26 17:37:43  rvv
*** empty log message ***

Revision 1.3  2017/06/21 16:10:57  rvv
*** empty log message ***

Revision 1.2  2017/06/10 18:09:58  rvv
*** empty log message ***

Revision 1.1  2017/05/14 09:57:45  rvv
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

class RapportTemplate_L73
{
	function RapportTemplate_L73($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;


		$this->pdf->rapport_type = "FOOTER";
		if($this->pdf->lastPortefeuille != $this->pdf->portefeuilledata['Portefeuille'])
			$this->pdf->rapportNewPage = $this->pdf->page;

    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;

    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=0;
      $paginaNummerStart=0;
    }
    else
		{
			$paginaCorrectie = 1;
			$paginaNummerStart=-1;
		}
    $paginaCorrectieInhoud=$paginaCorrectie;
    $totPagina = ($lastpage-$startpagina+$paginaCorrectie);//-1

		if(isset($this->pdf->templateVars['FACTUURpaginasBegin']) && isset($this->pdf->templateVars['FACTUURpaginasEind']) && $this->pdf->templateVars['FACTUURpaginasEind'] <> 0)
		{
			$factuurAanwezig = true;
			$totPagina--;
			//if($this->pdf->templateVars['FACTUURpaginasBegin']==$startpagina)
		  //	$paginaNummerStart++;
		//	$startpagina++;$startpagina++;

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


			 $this->pdf->SetXY($this->pdf->marge,20);
       $this->pdf->SetWidths(array(0,280,7));
  	   $this->pdf->SetAligns(array('R','C','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+8);
			 $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
			 $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8 , 'F');
			 $this->pdf->ln(2);
			 $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
	     $this->pdf->row(array('',"Inhoudsopgave"));
			 $this->pdf->ln(2);
			 $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,220,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	     $inhoudsItems=array('PERFPaginas'=>'Kerngegevens rapportage',
	                         'PERFGPaginas'=>'Rendement op het belegd vermogen versus gewogen benchmark',
	                         'PERFG2Paginas'=>'Rendement per beleggingscategorie over lopende jaar versus bijbehorende benchmark',
	                         'OIBPaginas'=>'Portefeuille in relatie tot de strategische weging',
	                         'consolidatiePaginas'=>'Verdeling vermogen over de verschillende portefeuilles',
	                         'OIVPaginas'=>'Verdeling vermogen over de verschillende regio\'s en valuta',
	                         'OISPaginas'=>'Verdeling van de zakelijke waarden over de verschillende sectoren',
	                         'RISKPaginas'=>'Verdeling van de vastrentende waarden naar kwaliteit en looptijd',
	                         'VOLKPaginas'=>'Vergelijkend overzicht lopend kalenderjaar',
	                         'VHOPaginas'=>'Portefeuille overzicht',
	                         'VHO2Paginas'=>'De 10 grootste posities per beleggingscategorie',
	                         'ATTPaginas'=>'Performance en attributie-overzicht per beleggingscategorie en totaal',
	                         'ATTlaterPaginas'=>'Risico/rendementsanalyse per portefeuille/beheerder',
	                         'ATT2Paginas'=>'Stortingen, onttrekkingen, inkomsten en uitgaven',
	                         'CASHYPaginas'=>'Cashflow overzicht vastrentende waarden',
	                         'TRANSPaginas'=>'Specificatie effectentransacties',
	                         'MUTPaginas'=>'Mutatie-overzicht',
                           'HSEPaginas'=>'Performance overzicht',);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        //$characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
			  foreach ($this->pdf->templateVars as $key=>$value)
        {
					$text='';
					if($this->pdf->templateVarsOmschrijving[$key])
					{
						$text = $this->pdf->templateVarsOmschrijving[$key] . '   ';
					}
					elseif($inhoudsItems[$key])
					{
						$text=$inhoudsItems[$key];
					}

					if($text <> '')
          {
            
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array($n,$text,$this->pdf->templateVars[$key]-$startpagina+$paginaCorrectieInhoud));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
            $n++;
          }
        }
		  }

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);

//listarray($this->pdf->templateVars);

	  //   $extraBlank=1;


	   if($i > $startpagina+$paginaNummerStart) // &&
	   {

			// echo "<br> $i > ".$this->pdf->templateVars['FACTUURpaginasBegin']." && $i <=".$this->pdf->templateVars['FACTUURpaginasEind']." | $portefeuille <br>\n";
			 if($factuurAanwezig == true && $i > $this->pdf->templateVars['FACTUURpaginasBegin'] && $i <=$this->pdf->templateVars['FACTUURpaginasEind'])
				 continue;

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
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-1);
			 $this->pdf->Cell(100, 4, $portefeuille, 0, 0, 'L');
			 $this->pdf->Cell(197 - $this->pdf->marge * 2, 4, $tekst, 0, 0, 'R');
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