<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/09/21 16:09:23 $
File Versie					: $Revision: 1.5 $

$Log: RapportTemplate_L70.php,v $
Revision 1.5  2016/09/21 16:09:23  rvv
*** empty log message ***

Revision 1.4  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.3  2016/06/12 10:20:31  rvv
*** empty log message ***

Revision 1.2  2016/06/08 15:40:53  rvv
*** empty log message ***

Revision 1.1  2016/05/29 10:19:26  rvv
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

class RapportTemplate_L70
{
	function RapportTemplate_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
			$vanPagina = ($i - $startpagina + $paginaCorrectie);//-1
			$this->pdf->page = $i;
			if ($i == $this->pdf->templateVars['inhoudsPagina'])
			{
				if ($this->pdf->CurOrientation == 'P')
				{
					$this->pdf->CurOrientation = 'L';
					$this->pdf->wPt = $this->pdf->fhPt;
					$this->pdf->hPt = $this->pdf->fwPt;
					$this->pdf->w = $this->pdf->fh;
					$this->pdf->h = $this->pdf->fw;
					$this->pdf->PageBreakTrigger = $this->pdf->h - $this->pdf->bMargin;
				}
				unset($this->pdf->CellBorders);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b']);


				$this->pdf->SetXY($this->pdf->marge, 33);
				$this->pdf->SetAligns(array('R', 'L', 'R'));
				$this->pdf->SetWidths(array(20, 220, 7));
				$this->pdf->ln(10);
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


				$this->pdf->SetFont($this->pdf->rapport_font, '', 11);
				$n = 1;
				foreach ($this->pdf->templateVars as $key=>$value)
				{
            if($this->pdf->templateVarsOmschrijving[$key])
						{
						$text = $this->pdf->templateVarsOmschrijving[$key]. '   ';
						$stringWidth = $this->pdf->GetStringWidth($text);
						$dots = round((210 - $stringWidth) / ($this->pdf->CurrentFont['cw']['.'] * $this->pdf->FontSize / 1000));
						$text .= str_repeat('.', $dots - 3);
						$this->pdf->row(array('', $text, $this->pdf->templateVars[$key] + $paginaCorrectieInhoud - $startpagina));
						//$startX = $this->pdf->marge + $this->pdf->widths[0] + $stringWidth;
						$this->pdf->ln(5);
						$n++;
					}
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
       
  
		  //$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		     
 //      $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');

       $this->pdf->setXY($this->pdf->marge,$Y);
       
	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina";// van $totPagina"; $portefeuille
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,$tekst,'0','R');

	   //  $this->pdf->Rect(10, $Y, 200,2, 'DF');
	    // echo $this->pdf->rapport_fontsize." $tekst <br>\n";
	   }
 
		}
   
		$this->pdf->SetAutoPageBreak(true,15);
	*/
			$this->pdf->page = $lastpage;

		$this->pdf->rapportCounterLast = $lastpage;
	}


	function writeRapport()
	{
		global $__appvar;
	}


}
?>