<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/13 17:51:11 $
File Versie					: $Revision: 1.3 $

$Log: RapportTemplate_L84.php,v $
Revision 1.3  2019/07/13 17:51:11  rvv
*** empty log message ***

Revision 1.2  2019/07/06 15:40:47  rvv
*** empty log message ***

Revision 1.1  2019/07/05 16:47:00  rvv
*** empty log message ***

Revision 1.9  2018/04/04 15:48:38  rvv
*** empty log message ***

Revision 1.8  2016/10/16 15:14:53  rvv
*** empty log message ***

Revision 1.7  2016/03/02 16:59:05  rvv
*** empty log message ***

Revision 1.6  2015/09/26 15:57:57  rvv
*** empty log message ***

Revision 1.5  2014/12/17 16:14:40  rvv
*** empty log message ***

Revision 1.4  2014/09/13 14:38:35  rvv
*** empty log message ***

Revision 1.3  2014/08/09 15:06:36  rvv
*** empty log message ***

Revision 1.2  2014/08/06 15:41:01  rvv
*** empty log message ***

Revision 1.1  2014/04/19 16:16:18  rvv
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

class RapportTemplate_L84
{
	function RapportTemplate_L84($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    $this->portefeuille=$portefeuille;
    

    $this->pdf->SetAutoPageBreak(false);
    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=0;
      $paginaNummerStart=1;
    }
    else
    {
      $paginaCorrectie=1;
    }

    if(!isset($this->pdf->rapportNewPage))
        $this->pdf->rapportNewPage=1;
              
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;  
    $paginaCorrectieInhoud=$paginaCorrectie;
    $totPagina = ($lastpage-$startpagina+$paginaCorrectie);//-1

		for($i=$startpagina ; $i <=$lastpage; $i++)
		{
		 $vanPagina = ($i-$startpagina+$paginaCorrectie);//-1
		 $this->pdf->page = $i;
		/*
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


           
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);

        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key]) //$this->pdf->templateVarsOmschrijving
          {
            $text=$inhoudsItems[$key].' ';
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
		*/

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-14);


	  //   $extraBlank=1;
      
      $pageWidth=$this->pdf->w;
      $pageHeight=$this->pdf->h;

	   if($i >= $startpagina+$paginaNummerStart)
	   {
	     $Y=$this->pdf->getY();


	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(255,255,255);
       
       $this->pdf->SetFillColor(0,0,0);
		   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);   
       $this->pdf->MultiCell(240,4,$this->portefeuille,'0','L');
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
       $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');

       $tekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum)."\n";
	     $tekst .= vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       $this->pdf->setXY($pageWidth-50-$this->pdf->marge,$Y);
       $this->pdf->MultiCell(50,4,$tekst,'0','R');

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