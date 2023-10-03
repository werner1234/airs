<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/10 14:26:19 $
File Versie					: $Revision: 1.30 $

$Log: RapportTemplate_L33.php,v $
Revision 1.30  2019/02/10 14:26:19  rvv
*** empty log message ***

Revision 1.29  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.28  2019/01/26 19:33:28  rvv
*** empty log message ***

Revision 1.27  2019/01/02 16:18:56  rvv
*** empty log message ***

Revision 1.26  2018/09/09 16:43:36  rvv
*** empty log message ***

Revision 1.25  2018/09/08 17:43:29  rvv
*** empty log message ***

Revision 1.24  2018/09/06 15:32:16  rvv
*** empty log message ***

Revision 1.23  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.22  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.21  2016/10/13 12:30:21  rvv
*** empty log message ***

Revision 1.20  2016/10/12 16:20:08  rvv
*** empty log message ***

Revision 1.19  2015/06/27 15:52:41  rvv
*** empty log message ***

Revision 1.18  2014/10/04 15:22:54  rvv
*** empty log message ***

Revision 1.17  2014/04/05 15:33:48  rvv
*** empty log message ***

Revision 1.16  2013/04/27 16:29:28  rvv
*** empty log message ***

Revision 1.15  2013/04/24 13:22:02  rvv
*** empty log message ***

Revision 1.14  2013/04/20 16:34:57  rvv
*** empty log message ***

Revision 1.13  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.12  2012/10/07 14:57:18  rvv
*** empty log message ***

Revision 1.11  2012/07/29 10:24:33  rvv
*** empty log message ***

Revision 1.10  2012/04/21 15:38:14  rvv
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

class RapportTemplate_L33
{
	function RapportTemplate_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;


		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;
    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=-1;
      $paginaNummerStart=1;
    }
    else
      $paginaCorrectie=0;
    $paginaCorrectieInhoud=$paginaCorrectie;
    $totPagina = ($lastpage-$startpagina+$paginaCorrectie);//-1
		for($i=$startpagina ; $i <=$lastpage; $i++)
		{
		  if($i<=0)
      {
        continue;
      }
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


       $this->pdf->SetXY($this->pdf->marge,55);
       $this->pdf->SetWidths(array(0,140,7));
  	   $this->pdf->SetAligns(array('R','L','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+8);
	     $this->pdf->row(array('',vertaalTekst("Inhoudsopgave",$this->pdf->rapport_taal)));
	     $this->pdf->SetWidths(array(20,135,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	     $inhoudsItems=array('OIBPaginas'=>'Kerngegevens rapportage',
	                         'KERNVPaginas'=>'Kerngegevens obligatieportefeuille',
                           'KERNZPaginas'=>'Kerngegevens risicodragende portefeuille',
	                         'ATTPaginas'=>'Beleggingsresultaat',
	                         'PERFPaginas'=>'Ontwikkeling vermogen',
	                         'VHOPaginas'=>'Vermogensoverzicht (met gemiddelde kostprijs)',
	                         'VOLKPaginas'=>'Vermogensoverzicht (met beginkoers)',
	                         'VOLKVPaginas'=>'Overzicht obligatieportefeuille',
				                   'PERFGPaginas'=>'Overzicht obligaties per regio',
	                         'CASHYVPaginas'=>'Kasstroom uit de obligatieportefeuille',
	                         'CASHYPaginas'=>'Kasstroom uit de portefeuille',
													 'CASHPaginas'=>'Kasstroom uit de portefeuille',
	                         'TRANSPaginas'=>'Transactieoverzicht',
	                         'TRANSFEEPaginas'=>'Overzicht kosten afgelopen jaar',
	                         'MUTPaginas'=>'Mutatieoverzicht',
                           'RISKPaginas'=>'Allocatie per regio',
	                         'DEFPaginas'=>'Definitieoverzicht',
	                         'INDEXPaginas'=>'Vergelijkingsmaatstaven',
                           'ZORGPaginas'=>'Afspraken cliënt',
                           'HSEPaginas'=>'Performance overzicht',
													 'VKMSPaginas'=>'Vergelijkende kostenmaatstaf',
                           'VKMDPaginas'=>'Vergelijkende kostenmaatstaf',
                           'OISPaginas'=>'Overzicht risicodragende beleggingen per sector (met beginkoers)',
                           'HUISPaginas'=>'Overzicht beleggingen per sector (met beginkoers)',
													 'OIRPaginas'=>'Overzicht risicodragende beleggingen per regio (met beginkoers)',
													 'DUURZAAMPaginas'=>'Overzicht risicomijdende beleggingen per regio (met beginkoers)');
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];

        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key])
          {
            $text=vertaalTekst($inhoudsItems[$key],$this->pdf->rapport_taal).' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((135-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
          }
        }
		  }

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);

     if(isset($this->pdf->templateVars['FACTUURpaginasBegin']) && $this->pdf->templateVars['FACTUURpaginasBegin'] <> 0 && isset($this->pdf->templateVars['FACTUURpaginasEind']) && $this->pdf->templateVars['FACTUURpaginasEind'] <> 0)
		   $factuurAanwezig=true;
		 else
		   $factuurAanwezig=false;

	   if($i > $startpagina+$paginaNummerStart)
	   {
			 if($factuurAanwezig == true && $i > $this->pdf->templateVars['FACTUURpaginasBegin'] && $i <=$this->pdf->templateVars['FACTUURpaginasEind'])
			   continue;

	     $Y=$this->pdf->getY();
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetTextColor(0,0,0);
	     $this->pdf->SetFillColor(0,0,0);

       if($this->pdf->selectData['type'] <> 'factuur')
	       $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');
	     $this->pdf->setY($Y);

	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina ".vertaalTekst("van",$this->pdf->rapport_taal)." $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	     $this->pdf->MultiCell(273,4,$tekst,0,'R');

	     //$this->pdf->Rect(10, $Y, 200,2, 'DF');
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