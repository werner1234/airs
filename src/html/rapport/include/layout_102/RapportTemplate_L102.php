<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/11/22 17:03:24 $
File Versie					: $Revision: 1.1 $

$Log: RapportTemplate_L102.php,v $
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

class RapportTemplate_L102
{
	function RapportTemplate_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
      $paginaNummerStart=0;
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


       $this->pdf->SetXY($this->pdf->marge,38);
       $this->pdf->SetAligns(array('R','L','R'));
	     $this->pdf->SetWidths(array(20,110,7,10,110,7));
       $this->pdf->ln(1);
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	     
	     $query="SELECT Export_data_frontOffice FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	     $db=new DB();
	     $db->SQL($query);
	     $db->Query();
	     $data=$db->lookupRecord();
	     $exportData=unserialize($data['Export_data_frontOffice']);
       //$hoofdstukken=array('V'=>"Hoofdstuk 1 - Vermogen",'P'=> "Hoofdstuk 2 - Prestatiemeting",'K'=>"Hoofdstuk 3 - Kostenstructuur",'R'=>"Hoofdstuk 4 - Risico's",'O'=>"Hoofdstuk 5 - Portefeuille opbouw / winst en verlies",'T'=>"Hoofdstuk 6 - Verrichtingen");
       $hoofdstukken=array('V'=>"Vermogen",'P'=> "Prestatiemeting",'K'=>"Kostenstructuur",'R'=>"Risico's",'O'=>"Portefeuille opbouw / winst en verlies",'T'=>"Verrichtingen");
       $hoofdstukPerRapport=array();
	     foreach($exportData as $rapport=>$rapportDetails)
       {
         $hoofdstuk=explode("-",$rapportDetails['shortName']);
         if(count($hoofdstuk)>1)
         {
           $hoofdstukPerRapport[$rapport]=$hoofdstuk[0];
         }
       }
	   //  listarray($hoofdstukPerRapport);

	     $inhoudsItems=array('PERFPaginas'=>'Performance over de beleggingscategorieën',
													 'PERFDPaginas'=>'Performance over de portefeuilles',
	                         'ATTPaginas'=>'Performancemeting in de tijd',
                           'OIRPaginas'=>'Onderverdeling in regio',
													 'GRAFIEK1Paginas'=>'Asset allocatie per rekening',
													 'GRAFIEK2Paginas'=>'Rekeningverdeling per asset class',
                           'OISPaginas'=>'Onderverdeling in beleggingssector',
                           'OIBPaginas'=>'Onderverdeling in beleggingscategorieën',
                           'OIVPaginas'=>'Onderverdeling in valuta',
                           'CASHYPaginas'=>'Cashflow overzicht lopende jaar en op langere termijn',
                           'PERFGPaginas'=>'Historische performanceverloop',
                           'AFMPaginas'=>'Onderverdeling in AFM beleggingscategorieën',
                           'VHOPaginas'=>'Vergelijkend historisch overzicht',
                           'HSEPaginas'=>'Overzicht portefeuilles',
                           'MUTPaginas'=>'Mutatie-overzicht',
                           'TRANSPaginas'=>'Transactie-overzicht',
                           'INDEXPaginas'=>'Vergelijkingsmaatstaven');
          
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
       $paginasPerHoofdstuk=array();
       $totaalRegels=0;
      
       $hoofdStukGetal=1;
       $laatsteHoofdstuk='';
       $hoofdstukkenMetNummer=array();
      

  
         foreach ($this->pdf->templateVars as $key => $value)
         {
           if ($inhoudsItems[$key] || $this->pdf->templateVarsOmschrijving[$key]) //
           {
 
             
             $rapport=str_replace('Paginas','',$key);
             $hoofdstuk=vertaalTekst('Hoofdstuk',$this->pdf->rapport_taal) . " ".$hoofdStukGetal." - ". vertaalTekst($hoofdstukken[$hoofdstukPerRapport[$rapport]],$this->pdf->rapport_taal);
             if($hoofdstukken[$hoofdstukPerRapport[$rapport]]<> '' && $hoofdstukken[$hoofdstukPerRapport[$rapport]]<>$laatsteHoofdstuk)
             {
               $hoofdStukGetal++;
             }
             $hoofdstukkenMetNummer[$rapport]=$hoofdstuk;

            // $rapport = str_replace('Paginas', '', $key);
            // $hoofdstuk = $hoofdstukken[$hoofdstukPerRapport[$rapport]];
             $paginasPerHoofdstuk[$hoofdstuk][] = $key;
             $totaalRegels++;
             $laatsteHoofdstuk=$hoofdstukken[$hoofdstukPerRapport[$rapport]];
           }
         }
       if(count($this->pdf->templateVars)<20)
       {
         $nextCol = 20;
       }
       else
       {
         $n = 0;
         ksort($paginasPerHoofdstuk);
         foreach ($paginasPerHoofdstuk as $h => $rapporten)
         {
           $aantal = count($rapporten);
           $n += $aantal;
           if ($n >= $totaalRegels / 2)
           {
             $nextCol = $n;
             break;
           }
         }
       }


        $n=1;
        $tweedeGestart=false;
        //if(count($this->pdf->templateVars)<15)
        //  $witruimte=2;
        //else
        $witruimte=1;

        $yStart=$this->pdf->getY();
        foreach ($this->pdf->templateVars as $key=>$value)
        {
          if($n>$nextCol && $tweedeGestart==false)
          {
            $tweedeGestart=true;
            $this->pdf->setY($yStart);
          }
          if($inhoudsItems[$key] || $this->pdf->templateVarsOmschrijving[$key]) //
          {
            if($this->pdf->templateVarsOmschrijving[$key])
              $text=$this->pdf->templateVarsOmschrijving[$key].' ';
            else
              $text=$inhoudsItems[$key].' ';

            $rapport=str_replace('Paginas','',$key);
           // $hoofdstuk="Hoofdstuk ".$hoofdStukGetal." - ".$hoofdstukken[$hoofdstukPerRapport[$rapport]];
            $hoofdstuk=$hoofdstukkenMetNummer[$rapport];
            $text="$n ". vertaalTekst($text,$this->pdf->rapport_taal);

            //listarray($hoofdstukken);


            if($hoofdstukken[$hoofdstukPerRapport[$rapport]]<>$laatsteHoofdstuk && $hoofdstukken[$hoofdstukPerRapport[$rapport]] <>'')
            {

              $this->pdf->ln();
              $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
              if($n>$nextCol)
              {
                $this->pdf->Row(array('','','','', $hoofdstuk));
              }
              else
              {
                $this->pdf->Row(array('',$hoofdstuk));
              }
              $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
              $this->pdf->ln($witruimte);
            }
            $stringWidth=$this->pdf->GetStringWidth($text);
            $dots=round((110-$stringWidth)/($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000));
            $text.=str_repeat('.',$dots-3);
            if($n>$nextCol)
              $this->pdf->row(array('','','','',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
            else
              $this->pdf->row(array('',$text,$this->pdf->templateVars[$key]+$paginaCorrectieInhoud-$startpagina));
            $startX=$this->pdf->marge+$this->pdf->widths[0]+$stringWidth;
            $this->pdf->ln($witruimte);
            $laatsteHoofdstuk=$hoofdstukken[$hoofdstukPerRapport[$rapport]];
            $n++;
          }
        }
		  }

		 $this->pdf->SetAutoPageBreak(false);
	   $this->pdf->SetY(-8);


	  //   $extraBlank=1;

	   if($i >= $startpagina+$paginaNummerStart)
	   {
	     //$Y=$this->pdf->getY();


	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
	     $this->pdf->SetDrawColor(0,0,0);
	     $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetFillColor(0,0,0);
       
  
		  //$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		     
 //      $this->pdf->MultiCell(240,4,$this->pdf->rapport_voettext,'0','L');

       $this->pdf->setXY($this->pdf->marge,-10);
       
	    // $tekst = vertaalTekst("Pagina",$this->pdf->rapport_taal)." $vanPagina";// van $totPagina"; $portefeuille
	     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
       $this->pdf->MultiCell(297-(2*$this->pdf->marge),4,$vanPagina,'0','R');

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