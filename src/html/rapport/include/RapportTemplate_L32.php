<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/07/07 17:35:19 $
File Versie					: $Revision: 1.5 $

$Log: RapportTemplate_L32.php,v $
Revision 1.5  2018/07/07 17:35:19  rvv
*** empty log message ***

Revision 1.4  2017/10/25 15:59:31  rvv
*** empty log message ***

Revision 1.3  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.2  2017/06/10 18:09:58  rvv
*** empty log message ***

Revision 1.1  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.9  2016/05/28 14:21:21  rvv
*** empty log message ***

Revision 1.8  2016/05/25 14:15:31  rvv
*** empty log message ***

Revision 1.7  2015/06/18 06:01:58  rvv
*** empty log message ***

Revision 1.6  2015/04/26 12:26:58  rvv
*** empty log message ***

Revision 1.5  2015/04/01 16:00:45  rvv
*** empty log message ***

Revision 1.4  2015/03/11 17:13:49  rvv
*** empty log message ***

Revision 1.3  2015/03/01 14:08:16  rvv
*** empty log message ***

Revision 1.2  2015/02/18 17:09:13  rvv
*** empty log message ***

Revision 1.1  2015/02/15 10:35:28  rvv
*** empty log message ***

Revision 1.3  2015/01/07 17:25:26  rvv
*** empty log message ***

Revision 1.2  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.1  2014/12/21 13:23:18  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L32
{
	function RapportTemplate_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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




			 $this->pdf->setTextColor(255,255,255);
			 $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
			 $this->pdf->SetDrawColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
			 $this->pdf->Rect($this->pdf->marge, 32, 297-$this->pdf->marge*2, 8, 'F');
			 $this->pdf->SetFillColor(0);
			 unset($this->pdf->fillCell);



       $this->pdf->SetXY($this->pdf->marge,34);
       $this->pdf->SetWidths(array(0,280,7));
  	   $this->pdf->SetAligns(array('R','C','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	     //$this->pdf->row(array('',"Inhoudsopgave"));
	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,200,27));
			 if($this->pdf->lastPOST['nummeringUit'])
				 $this->pdf->row(array('',vertaalTekst("Rapportage",$this->pdf->rapport_taal)));
			 else
			   $this->pdf->row(array('',vertaalTekst("Rapportage",$this->pdf->rapport_taal),vertaalTekst("Pagina",$this->pdf->rapport_taal)));
			 $this->pdf->SetAligns(array('R','L','R'));
			 $this->pdf->SetWidths(array(20,220,7));
			 $this->pdf->SetTextColor(0,0,0);
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	     $inhoudsItems=array('PERFPaginas'=>'Performance over de beleggingscategorie�n',
													 'PERFDPaginas'=>'Performance over de portefeuilles',
	                         'ATTPaginas'=>'Performancemeting in de tijd',
                           'OIRPaginas'=>'Onderverdeling in regio',
													 'GRAFIEK1Paginas'=>'Asset allocatie per rekening',
													 'GRAFIEK2Paginas'=>'Rekeningverdeling per asset class',
                           'OISPaginas'=>'Onderverdeling in beleggingssector',
                           'OIBPaginas'=>'Onderverdeling in beleggingscategorie�n',
                           'OIVPaginas'=>'Onderverdeling in valuta',
                           'CASHYPaginas'=>'Cashflow overzicht lopende jaar en op langere termijn',
                           'PERFGPaginas'=>'Historische performanceverloop',
                           'AFMPaginas'=>'Onderverdeling in AFM beleggingscategorie�n',
                           'VOLKPaginas'=>'Vergelijkend overzicht lopend kalenderjaar',
                           'VHOPaginas'=>'Vergelijkend historisch overzicht',
                           'HSEPaginas'=>'Huidige samenstelling effectenportefeuille',
                           'MUTPaginas'=>'Mutatie-overzicht',
                           'TRANSPaginas'=>'Transactie-overzicht',
                           'INDEXPaginas'=>'Vergelijkingsmaatstaven');
	     
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
					$tonen=false;
					if($this->pdf->templateVarsOmschrijving[$key])
					{
						$text = $text = $this->pdf->templateVarsOmschrijving[$key] . ' ';
						$tonen=true;
					}
          elseif($inhoudsItems[$key]) //$this->pdf->templateVarsOmschrijving
					{
						$text = $inhoudsItems[$key] . ' ';
						$tonen=true;
					}
          if($tonen==true)
          {
						$text=vertaalTekst($text,$this->pdf->rapport_taal);

						if($this->pdf->lastPOST['nummeringUit'])
							$this->pdf->row(array('',$text));
						else
						{
							$stringWidth=$this->pdf->GetStringWidth($text."   ");
							$dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
							$text.=str_repeat('.',$dots-3);
							$this->pdf->row(array('', $text, $this->pdf->templateVars[$key] + $paginaCorrectieInhoud - $startpagina));
						}
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(5);
            $n++;
          }
        }
		  }

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-9);


	  //   $extraBlank=1;

	   if($i >= $startpagina+$paginaNummerStart)
	   {
	     $Y=$this->pdf->getY();


	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
       
  
		  //$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		     
 //      $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');

       $this->pdf->setXY($this->pdf->marge,$Y);
       
	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina ".vertaalTekst("van",$this->pdf->rapport_taal)." $totPagina";// $portefeuille
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			 if(!$this->pdf->lastPOST['nummeringUit'])
         $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,$tekst,'0','R');

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