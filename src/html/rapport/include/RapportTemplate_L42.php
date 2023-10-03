<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/06 07:13:42 $
File Versie					: $Revision: 1.18 $

$Log: RapportTemplate_L42.php,v $
Revision 1.18  2020/05/06 07:13:42  rvv
*** empty log message ***

Revision 1.17  2016/10/16 15:14:53  rvv
*** empty log message ***

Revision 1.16  2016/07/20 16:12:00  rvv
*** empty log message ***

Revision 1.15  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.14  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.13  2015/02/15 10:36:54  rvv
*** empty log message ***

Revision 1.12  2015/02/11 07:53:26  rvv
*** empty log message ***

Revision 1.11  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.10  2014/12/17 16:14:40  rvv
*** empty log message ***

Revision 1.9  2014/12/06 18:13:44  rvv
*** empty log message ***

Revision 1.8  2014/06/04 16:13:28  rvv
*** empty log message ***

Revision 1.7  2014/05/25 14:38:33  rvv
*** empty log message ***

Revision 1.6  2014/05/21 12:39:28  rvv
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

class RapportTemplate_L42
{
	function RapportTemplate_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FOOTER";
    $lastpage = $this->pdf->page;
    $startpagina =  $this->pdf->rapportNewPage;//$this->pdf->rapportCounterLast;
    $this->pdf->rapportNewPage=$this->pdf->page+2;
    $this->pdf->SetAutoPageBreak(false);

    if(in_array('FRONT',$this->pdf->rapport_typen))
    {
      $paginaCorrectie=0;
      $paginaNummerStart=0;
    }
    else
    {
      $paginaCorrectie=1;
      $paginaNummerStart=-1;
    }

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


       $this->pdf->SetXY($this->pdf->marge,16);
       $this->pdf->SetWidths(array(11,280,7));
  	   $this->pdf->SetAligns(array('R','L','R'));
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
       $this->pdf->SetTextColor(255);
	     $this->pdf->row(array('',"Inhoudsopgave"));
       $this->pdf->SetTextColor(0);
	     $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,220,7));
       $this->pdf->ln(15);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
       $this->pdf->SetDrawColor(0,0,0);
       $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);

	     $inhoudsItems=array('PERFPaginas'=>'Resultaat- en rendementsberekening',
	                         'OIBPaginas'=>'Spreiding beleggingscategorieën en valuta\'s',
	                         'VHOPaginas'=>'Portefeuille overzicht',
	                         'ATTPaginas'=>'Performance overzicht',
                           'MUTPaginas'=>'Mutatie overzicht',
                           'TRANSPaginas'=>'Transactie overzicht',
                           'VOLKPaginas'=>'Portefeuille overzicht',
                           'CASHYPaginas'=>'Cashflow overzicht lopende jaar en op langere termijn',
                           'MODELPaginas'=>'Modelcontrole',
                           'ZORGPaginas'=>'Zorgplichtcontrole',
                           'VKMSPaginas'=>'Vergelijkende kostenmaatstaf',
                           'VKMPaginas'=>'Vergelijkende kostenmaatstaf',
                           'VARPaginas'=>'Geschiktheidsrapportage',
                           'INDEXPaginas'=>'Indices',
                           'ENDPaginas'=>'Disclaimer',
                           'NotitiePaginas'=>'Notities',
                           'SMVPaginas'=>'Saldomutatieverloop',
                           'JOURNAALPaginas'=>'Journaal',
                           'FISCAALPaginas'=>'Vergelijkend historisch overzicht',
                           'AFMPaginas'=>'Onderverdeling in AFM categorieën');
           
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
        $characterwidth=&$this->pdf->CurrentFont['cw'];
        $n=1;
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($inhoudsItems[$key] || $this->pdf->templateVarsOmschrijving[$key]) //$this->pdf->templateVarsOmschrijving
          {
            if($inhoudsItems[$key]<>'')
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
	   
	   $db=new DB();
	   $veld='Rapportagetenaamstelling';
	   $query="SHOW COLUMNS FROM `CRM_naw` LIKE '$veld'";
	   if($db->QRecords($query))
     {
       $db->SQL("SELECT $veld FROM CRM_naw WHERE Portefeuille='".mysql_real_escape_string($this->pdf->portefeuilledata['Portefeuille'])."'");
       $crmNaam=$db->lookupRecord();
       if($crmNaam[$veld]<>'')
         $naam=$crmNaam[$veld];
       else
         $naam=$this->pdf->portefeuilledata['Client'];
       
     }
     else
       $naam=$this->pdf->portefeuilledata['Client'];
	   

	   if($i > $startpagina+$paginaNummerStart)
	   {
			 if($factuurAanwezig == true && $i > $this->pdf->templateVars['FACTUURpaginasBegin'] && $i <=$this->pdf->templateVars['FACTUURpaginasEind'])
				 continue;

	     $Y=$this->pdf->getY();
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);

       
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);

	
       $this->pdf->setXY(130,$Y);
	     $this->pdf->Cell(100,4,$this->pdf->rapport_voettext,0,0,'L');
       //listarray($this->pdf->portefeuilledata['Client']);
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       $this->pdf->setXY($this->pdf->marge,$Y);
       $this->pdf->Cell(80,4,$naam,0,0,'L');
	     $this->pdf->setXY(0,$Y);

	     $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina van $totPagina";
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	     $this->pdf->Cell(297-18,4,$tekst,0,0,'R');
	   //  $this->pdf->Rect(10, $Y, 200,2, 'DF');
	    // echo $this->pdf->rapport_fontsize." $tekst <br>\n";

	   }
		}
    $this->pdf->templateVars=array();
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