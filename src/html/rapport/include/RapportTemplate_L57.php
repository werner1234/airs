<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/11 16:33:41 $
File Versie					: $Revision: 1.9 $

$Log: RapportTemplate_L57.php,v $
Revision 1.9  2020/04/11 16:33:41  rvv
*** empty log message ***

Revision 1.8  2020/03/07 14:41:15  rvv
*** empty log message ***

Revision 1.6  2020/03/01 09:53:26  rvv
*** empty log message ***

Revision 1.5  2016/10/16 15:14:53  rvv
*** empty log message ***

Revision 1.4  2015/10/21 07:26:16  rvv
*** empty log message ***

Revision 1.3  2015/01/07 17:25:26  rvv
*** empty log message ***

Revision 1.2  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.1  2014/12/21 13:23:18  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L57
{
	function RapportTemplate_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
      $paginaNummerStart=0;
      if(!isset($this->pdf->rapportNewPage))
        $this->pdf->rapportNewPage=1;
      $paginaCorrectie=1;
    }
  // echo "paginaCorrectie: $paginaCorrectie ";   
 
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;  
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
  //  echo "startpagina: $startpagina ";  
  //  echo "totPagina: $totPagina ";  
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


	     $inhoudsItems=array('PERFPaginas'=>'Performancemeting',
	                         'VOLKPaginas'=>'Portefeuille overzicht',
                           'HUISPaginas'=>'Geschiktheidsrapportage',
                           'OIRPaginas'=>'Onderverdeling in regio',
                           'OISPaginas'=>'Onderverdeling in beleggingssector',
                           'OIBPaginas'=>'Onderverdeling in beleggingscategorie',
                           'OIVPaginas'=>'Onderverdeling in valuta',
                           'CASHYPaginas'=>'Cashflow overzicht lopende jaar en op langere termijn',
                           'PERFGPaginas'=>'Beleggingsresultaat lopend jaar',
                           'AFMPaginas'=>'Onderverdeling in AFM categorieën',
                           'HSEPaginas'=>'Huidige samenstelling effectenportefeuille',
                           'MUTPaginas'=>'Mutatie-overzicht');
          
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key] || $this->pdf->templateVarsOmschrijving[$key]) //
          {
            if($inhoudsItems[$key] <> '')
              $text=$inhoudsItems[$key].' ';
            else
              $text=$this->pdf->templateVarsOmschrijving[$key].' ';
              
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((220-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
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
			 if($factuurAanwezig == true && $i > $this->pdf->templateVars['FACTUURpaginasBegin'] && $i <=$this->pdf->templateVars['FACTUURpaginasEind'])
				 continue;

	     $Y=$this->pdf->getY();
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
       
  
		 // $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		     
      // $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');

       $this->pdf->setXY($this->pdf->marge,$Y);
       
	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,$tekst,0,'R');

	   //  $this->pdf->Rect(10, $Y, 200,2, 'DF');
	   //  echo $this->pdf->marge.",$Y | ".$this->pdf->rapport_fontsize." $tekst <br>\n";
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