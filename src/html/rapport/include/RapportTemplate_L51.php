<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/10/26 16:07:44 $
File Versie					: $Revision: 1.19 $

$Log: RapportTemplate_L51.php,v $
Revision 1.19  2019/10/26 16:07:44  rvv
*** empty log message ***

Revision 1.18  2019/09/14 17:09:05  rvv
*** empty log message ***

Revision 1.17  2019/03/23 17:05:54  rvv
*** empty log message ***

Revision 1.16  2019/03/09 18:46:18  rvv
*** empty log message ***

Revision 1.15  2019/03/06 16:13:44  rvv
*** empty log message ***

Revision 1.14  2019/01/20 12:14:00  rvv
*** empty log message ***

Revision 1.13  2018/11/07 17:08:06  rvv
*** empty log message ***

Revision 1.12  2018/04/18 16:17:01  rvv
*** empty log message ***

Revision 1.11  2018/03/14 17:17:41  rvv
*** empty log message ***

Revision 1.10  2017/03/31 15:39:22  rvv
*** empty log message ***

Revision 1.9  2017/03/29 15:57:04  rvv
*** empty log message ***

Revision 1.8  2016/06/08 15:42:01  rvv
*** empty log message ***

Revision 1.7  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.6  2015/12/20 16:46:36  rvv
*** empty log message ***

Revision 1.5  2014/05/04 10:55:50  rvv
*** empty log message ***

Revision 1.4  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.3  2014/04/16 15:51:22  rvv
*** empty log message ***

Revision 1.2  2014/04/02 15:53:15  rvv
*** empty log message ***

Revision 1.1  2013/11/13 15:47:34  rvv
*** empty log message ***

Revision 1.5  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.4  2013/08/18 12:23:35  rvv
*** empty log message ***

Revision 1.3  2013/07/28 09:59:15  rvv
*** empty log message ***

Revision 1.2  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.1  2013/01/16 16:54:03  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L51
{
	function RapportTemplate_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	  	 $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		   $this->pdf->Rect($this->pdf->marge, 22, 280, 8 , 'F');


       $this->pdf->SetXY($this->pdf->marge,24);
       $this->pdf->SetWidths(array(0,280,7));
  	   $this->pdf->SetAligns(array('R','C','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+8);
       $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']); 
	     $this->pdf->row(array('',vertaalTekst("Inhoudsopgave",$this->pdf->rapport_taal))); 
       $this->pdf->SetTextColor(0);
	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,220,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	     $inhoudsItems=array('PERFPaginas'=>vertaalTekst('Performancemeting',$this->pdf->rapport_taal),
                           'HSEPaginas'=>vertaalTekst('Portefeuille risicoparameters',$this->pdf->rapport_taal),
	                         'ATTPaginas'=>vertaalTekst('Beleggingsresultaat lopend jaar',$this->pdf->rapport_taal),
                           'MUTPaginas'=>vertaalTekst('Mutatie overzicht',$this->pdf->rapport_taal),
                           'INDEXPaginas'=>vertaalTekst('Vergelijkingsmaatstaven',$this->pdf->rapport_taal),
                           'PERFGPaginas'=>vertaalTekst('Historisch rendement',$this->pdf->rapport_taal),
                           'PERFDPaginas'=>vertaalTekst('Performancemeting portefeuilles',$this->pdf->rapport_taal),
                           'GRAFIEKPaginas'=>vertaalTekst('Vermogensallocatie',$this->pdf->rapport_taal),
                           'TRANSPaginas'=>vertaalTekst('Overzicht transacties',$this->pdf->rapport_taal),
													 'TRANSFEEPaginas'=>vertaalTekst('Algemene toelichting',$this->pdf->rapport_taal),
													 'ZORGPaginas'=>vertaalTekst('Zorgplichtcontrole',$this->pdf->rapport_taal),
                           'RISKPaginas'=>vertaalTekst('Overzicht obligatieportefeuille',$this->pdf->rapport_taal),
													 'VKMPaginas'=>vertaalTekst('Vergelijkende kostenmaatstaf',$this->pdf->rapport_taal),
                           'DOORKIJKPaginas'=>vertaalTekst('Doorkijk totale portefeuille',$this->pdf->rapport_taal),
                           'DOORKIJK2Paginas'=>vertaalTekst('Doorkijk aandelenportefeuille',$this->pdf->rapport_taal),
                           'OISPaginas'=>vertaalTekst('Vermogensoverzicht per maandultimo',$this->pdf->rapport_taal),
                           'OIRPaginas'=>vertaalTekst('Performance en attributie-overzicht per beleggingscategorie en totaal',$this->pdf->rapport_taal),
													 'VOLKDPaginas'=>vertaalTekst('Portefeuille overzicht met valuta verdeling',$this->pdf->rapport_taal),
                           'VOLKPaginas'=>vertaalTekst('Vergelijkend overzicht lopend kalenderjaar',$this->pdf->rapport_taal));
           
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key] || isset($this->pdf->templateVarsOmschrijving[$key])) //$this->pdf->templateVarsOmschrijving
          {
            if($inhoudsItems[$key])
              $text=$inhoudsItems[$key].' ';
            else
              $text=$this->pdf->templateVarsOmschrijving[$key].' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
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


	     //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
	

	     $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,vertaalTekst($this->pdf->rapport_voettext,$this->pdf->rapport_taal),'0','L');
	     $this->pdf->setXY($this->pdf->marge,$Y);
	     if($this->pdf->portefeuilledata['Depotbank']=='TGB')
	       $depotbank='IGS';
	     else
	       $depotbank=$this->pdf->portefeuilledata['Depotbank'];
       $tekst = "$portefeuille -  ".$depotbank;
       $this->pdf->MultiCell(273,4,$tekst,0,'C');
       $this->pdf->setXY($this->pdf->marge,$Y);
	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina ".vertaalTekst('van',$this->pdf->rapport_taal)." $totPagina";
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