<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/11/22 17:03:24 $
File Versie					: $Revision: 1.1 $

$Log: RapportTemplate_L74.php,v $
Revision 1.1  2017/11/22 17:03:24  rvv
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

class RapportTemplate_L25
{
	function RapportTemplate_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
	//	$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    

    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      if(isset($this->pdf->selectData['type']))
        $paginaCorrectie=-1;
      else
        $paginaCorrectie=0;
      //$paginaNummerStart=1;
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

	     $inhoudsItems=array();
          
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
  
          if(isset($inhoudsItems[$key]))
            $text=$inhoudsItems[$key];
          elseif($this->pdf->templateVarsOmschrijving[$key])
            $text=vertaalTekst($this->pdf->templateVarsOmschrijving[$key],$this->pdf->rapport_taal).' ';
          else
            $text='';
          
          if($text<>'') //$this->pdf->templateVarsOmschrijving
          {
            $text.=' ';
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
      /*
           $this->pdf->SetAutoPageBreak(false);
           $this->pdf->SetY(-8);
      
      
          //   $extraBlank=1;
      
           if($i >= $startpagina+$paginaNummerStart)
           {
             $Y=$this->pdf->getY();
      
      
             $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
             $this->pdf->SetDrawColor(0,0,0);
             $this->pdf->SetTextColor(0,0,0);
             $this->pdf->SetFillColor(0,0,0);
             
        
             $this->pdf->setXY($this->pdf->marge,$Y);
             
             $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina";// van $totPagina"; $portefeuille
             $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
             $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,$tekst,'0','R');
           }
       */
		}
  
	//	$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->page = $lastpage;
		$this->pdf->rapportCounterLast = $lastpage;
  
	}


	function writeRapport()
	{
		global $__appvar;
	}


}
?>