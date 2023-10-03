<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/04 15:59:25 $
File Versie					: $Revision: 1.7 $

$Log: RapportTemplate_L67.php,v $
Revision 1.7  2020/07/04 15:59:25  rvv
*** empty log message ***

Revision 1.6  2019/10/18 17:40:37  rvv
*** empty log message ***

Revision 1.5  2018/10/27 16:49:57  rvv
*** empty log message ***

Revision 1.4  2017/04/21 15:10:13  rvv
*** empty log message ***

Revision 1.3  2017/03/08 16:53:32  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTemplate_L67
{
	function RapportTemplate_L67($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;


		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;

    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=-4;
      $paginaNummerStart=1;
    }
    else
      $paginaCorrectie=0;
    $paginaCorrectieInhoud=$paginaCorrectie;
    $totPagina = ($lastpage-$startpagina+$paginaCorrectie);//-1

		$vars=array('CurOrientation','wPt','hPt','w','h','PageBreakTrigger');
		$oudeInstellingen=array();
		foreach($vars as $var)
			$oudeInstellingen[$var]=$this->pdf->$var;

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
      
       $this->pdf->SetFillColor(0);
       $this->pdf->SetTextColor(0);
       $this->pdf->SetXY($this->pdf->marge,40);
       $this->pdf->SetWidths(array(20,140,7));
  	   $this->pdf->SetAligns(array('R','L','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+1);
	     $this->pdf->row(array('',vertaalTekst("Inhoudsopgave",$this->pdf->rapport_taal)));
	     $this->pdf->SetWidths(array(20,135,7));
       $this->pdf->ln(10);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	     $inhoudsItems=array('ATTPaginas'=>'Vermogensontwikkeling',
                           'OIBPaginas'=>'Onderverdeling in beleggingscategorie',
	                         'VHOPaginas'=>'Vermogensoverzicht (met gemiddelde kostprijs)',
	                         'CASHYPaginas'=>'Kasstroom uit de portefeuille',
	                         'PERFPaginas'=>'Ontwikkeling vermogen',
	                         'TRANSPaginas'=>'Transactieoverzicht',
	                         'MUTPaginas'=>'Mutatieoverzicht',
                           'MUT2Paginas'=>'Mutatieoverzicht',
	                         'ENDPaginas'=>'Overzicht portefeuille',
                           'VOLKPaginas'=>'Overzicht portefeuille');

			 foreach($this->pdf->templateVarsOmschrijving as $key=>$value)
				 $inhoudsItems[$key]=$value;
			 
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+1);
        $characterwidth=&$this->pdf->CurrentFont['cw'];

        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key])
          {
            if($this->pdf->templateVarsOmschrijving[$key])
              $text=vertaalTekst($this->pdf->templateVarsOmschrijving[$key],$this->pdf->rapport_taal).' ';
            else
              $text=vertaalTekst($inhoudsItems[$key],$this->pdf->rapport_taal).' ';
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((135-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$vanPagina));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln(3);
          }
        }
		  }
     }

		foreach($oudeInstellingen as $var=>$value)
			$this->pdf->$var=$value;

		$this->pdf->page = $lastpage;
		$this->pdf->rapportCounterLast = $lastpage;
	}


	function writeRapport()
	{
		global $__appvar;
	}


}
?>