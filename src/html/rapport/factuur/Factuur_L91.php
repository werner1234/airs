<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/06 16:00:44 $
File Versie					: $Revision: 1.25 $

$Log: Factuur_L22.php,v $
Revision 1.25  2019/02/06 16:00:44  rvv
*** empty log message ***

Revision 1.24  2018/07/05 16:17:22  rvv
*** empty log message ***

Revision 1.23  2018/06/09 15:57:27  rvv
*** empty log message ***

Revision 1.22  2018/04/11 09:13:31  rvv
*** empty log message ***

Revision 1.21  2017/04/26 15:16:49  rvv
*** empty log message ***

Revision 1.20  2016/04/16 17:13:32  rvv
*** empty log message ***

Revision 1.19  2016/03/30 16:03:14  rvv
*** empty log message ***

Revision 1.18  2015/10/19 14:24:25  rvv
*** empty log message ***

Revision 1.17  2015/02/11 16:50:58  rvv
*** empty log message ***

Revision 1.16  2014/10/22 15:49:18  rvv
*** empty log message ***

Revision 1.15  2013/07/08 17:45:46  rvv
*** empty log message ***

Revision 1.14  2013/07/06 16:01:29  rvv
*** empty log message ***

Revision 1.13  2013/07/04 15:38:29  rvv
*** empty log message ***

Revision 1.12  2013/06/19 15:55:51  rvv
*** empty log message ***

Revision 1.11  2013/06/16 11:46:31  rvv
*** empty log message ***

Revision 1.10  2012/12/16 10:37:29  rvv
*** empty log message ***

Revision 1.9  2012/06/30 14:45:30  rvv
*** empty log message ***

Revision 1.8  2012/06/09 13:44:27  rvv
*** empty log message ***

Revision 1.7  2012/06/06 18:17:47  rvv
*** empty log message ***

Revision 1.6  2012/06/03 09:55:37  rvv
*** empty log message ***

Revision 1.5  2012/04/13 06:46:29  rvv
*** empty log message ***

Revision 1.4  2012/04/11 08:07:53  rvv
*** empty log message ***

Revision 1.3  2012/04/04 16:10:05  rvv
*** empty log message ***

Revision 1.2  2012/01/04 16:37:28  rvv
*** empty log message ***

Revision 1.1  2011/08/11 15:39:22  rvv
*** empty log message ***

Revision 1.7  2011/04/11 19:49:19  rvv
*** empty log message ***

*/


//listarray($this->waarden);



    //$this->pdf->marge = 20;
//    $this->pdf->rowHeight=4;
//		$this->pdf->SetLeftMargin($this->pdf->marge);
//		$this->pdf->SetRightMargin($this->pdf->marge);
//		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->rapport_type = "FACTUUR";

$this->pdf->SetFont($font,"",10);
$this->pdf->AddPage('P');
$this->pdf->frontPage = true;


$this->pdf->oddPageReportStart[$this->portefeuille][$this->pdf->rapport_type]=$this->pdf->page;

		$font='Times';
		/*
		if(file_exists(FPDF_FONTPATH.'tahoma.php'))
		{
  	  if(!isset($this->pdf->fonts['tahoma']))
	    {
		    $this->pdf->AddFont('tahoma','','tahoma.php');
		    $this->pdf->AddFont('tahoma','B','tahomab.php');
		    $this->pdf->AddFont('tahoma','I','tahoma.php');
		    $this->pdf->AddFont('tahoma','BI','tahomab.php');
	    }
	    $font='tahoma';
		}
  

		if(file_exists(FPDF_FONTPATH.'calibri.php'))
		{
  	  if(!isset($this->pdf->fonts['calibri']))
	    {
		    $this->pdf->AddFont('calibri','','calibri.php');
		    $this->pdf->AddFont('calibri','B','calibrib.php');
		    $this->pdf->AddFont('calibri','I','calibrii.php');
		    $this->pdf->AddFont('calibri','BI','calibribi.php');
	    }
		  $font='calibri';
		}
    */






	 $startY=$this->pdf->GetY();
/*
	 $lichtblauw=array(0,170,236);
   $donkerblauw=array(0,55,124);
   $steps=100;
   $gstep=($lichtblauw[1]-$donkerblauw[1])/$steps;
   $bstep=($lichtblauw[2]-$donkerblauw[2])/$steps;
   $xstep=210/$steps;
   $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
	 $this->pdf->Rect(0, 0, 210, 100 , 'F');
   $R=$donkerblauw[0];
   $G=$donkerblauw[1];
   $B=$donkerblauw[2];
   for($x=0;$x<210;$x+=$xstep)
   {
     $this->pdf->SetFillColor($R,$G,$B);
     $G+=$gstep;
     $B+=$bstep;
     $this->pdf->Rect($x,0,$x+$xstep,5, 'F');
   }
*/   
   		if(is_file($this->pdf->rapport_logo))
		{
		  if($this->waarden['Vermogensbeheerder']=='CAS')
      {
        $factor = 0.06;
        $x = 885 * $factor;//$x=885*$factor;
        $this->pdf->Image($this->pdf->rapport_logo, 25, 20, $x);
      }
      else //SEQ
      {
        $factor = 0.06;
        $x = 885 * $factor;//$x=885*$factor;
        $y = 386 * $factor;//$y=849*$factor;
        $this->pdf->Image($this->pdf->rapport_logo, 25, 20, $x, $y);
      }
		}
    
    $this->pdf->SetWidths(array(210-(2*$this->pdf->marge)));
  	$this->pdf->SetAligns(array('C'));
    $this->pdf->SetFont($font,'',8);
    $this->pdf->SetTextColor(0,55,124);
    $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(280);
    
    $this->pdf->Row(array("Sequoia Vermogensbeheer B.V. - Stationsweg 6 - 6861 EG Oosterbeek - Nederland - T +31(0)88-2057979"));
    $this->pdf->Ln(2);
    $stringWidth=$this->pdf->GetStringWidth('www.sequoiabeheer.nl');
    $spaces=round(($stringWidth)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $spaceText.=str_repeat(' ',$spaces);
            
    $this->pdf->Row(array("E info@sequoiabeheer.nl - $spaceText - Rabobank NL66RABO0355054272 - KvK 62851799 - BTW nr. NL.8549.83.685.B01"));
    $stringWidthVoor=$this->pdf->GetStringWidth('E info@sequoiabeheer.nl - ');
    $stringWidthAchter=$this->pdf->GetStringWidth(' - Rabobank NL66RABO0355054272 - KvK 62851799 - BTW nr. NL.8549.83.685.B01');
    $this->pdf->SetTextColor(0,170,236);
    $spacesVoor=round(($stringWidthVoor)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $spacesAchter=round(($stringWidthAchter)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $this->pdf->Ln(-4);
    $this->pdf->Row(array(str_repeat(' ',$spacesVoor).'www.sequoiabeheer.nl'.str_repeat(' ',$spacesAchter)));
    $this->pdf->AutoPageBreak=true;
    $this->pdf->SetTextColor(0);
    $this->pdf->SetY($startY);

//$this->waarden['clientNaam']='testnaam';

    $this->pdf->SetFont($font,"",10);
		$this->pdf->SetY(42);
		$this->pdf->SetWidths(array(12,100,80));
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
		$this->pdf->row(array('',$this->waarden['clientAdres']));
		$plaats='';
		if($this->waarden['clientPostcode'] != '')
		  $plaats .= $this->waarden['clientPostcode']." ";
		$plaats .= $this->waarden['clientWoonplaats'];
		$this->pdf->row(array('',$plaats));
		$this->pdf->SetY(75);
		$this->pdf->ln();
		$this->pdf->SetFont($font,"",10);

    $vanjul=db2jul($this->waarden['datumVan']);
		if(substr($this->waarden['datumVan'],5,5) != '01-01')
		  $vanjul+=86400;

		$vanDatum=date("j",$vanjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanjul)],$this->pdf->rapport_taal)." ".date("Y",$vanjul);
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
		// start eerste block
		$this->pdf->SetWidths(array(12,40,6,20,60,6,40));
		$this->pdf->SetAligns(array("L","L","C","L","R","C","L"));
		//$this->pdf->row(array("Debiteurnummer", ':',$this->waarden['debiteurnr'],"Datum",':',date("j")." ".$this->pdf->__appvar["Maanden"][date("n")]." ".date("Y")));
		$this->pdf->row(array('',"Debiteurnummer", ':',$this->waarden['debiteurnr'],"Datum",':',$totDatum));

		$this->pdf->row(array('',"Factuurnummer", ':',sprintf("%06d",$this->waarden['factuurNummer']),"Pagina",':',"1"));
		$this->pdf->ln(6);
   // $this->pdf->Line($this->pdf->marge ,$this->pdf->GetY()-2,$this->pdf->marge +165 ,$this->pdf->GetY()-2);

    $this->pdf->SetWidths(array(12,180));
    $this->pdf->SetAligns(array("L","L",'L','L'));
    $this->pdf->Rect($this->pdf->marge+11,$this->pdf->GetY()-1,165+2,4+2);
    $this->pdf->row(array('',"Omschrijving: Nota voor beheerkosten"));
   // $this->pdf->Line($this->pdf->marge ,$this->pdf->GetY()+2,$this->pdf->marge +165 ,$this->pdf->GetY()+2);
    $this->pdf->ln(6);
    $this->pdf->SetWidths(array(12,115,10,25));
    $this->pdf->SetAligns(array("L","L",'R','R'));
   //
   // $this->pdf->row(array("",""));
   //
   $this->pdf->ln(5);
    $this->pdf->row(array("","Waarde portefeuille per $totDatum:  € ".$this->formatGetal($this->waarden['totaalWaarde'],2)));
   //$this->pdf->row(array("","EUR ".$this->formatGetal($this->waarden['totaalWaarde'],2),''));
   $this->pdf->ln(3);
   
  // $this->waarden['beheerfeePerPeriode'] -= $this->waarden['huisfondsKorting'];
    if($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'] > 0)
    {
      if(count($this->waarden['huisfondsKortingFondsen'])>0)
      {
        $huisfondsWaarde=0;
        foreach($this->waarden['huisfondsKortingFondsen'] as $fonds=>$waarde)
        {
          $huisfondsWaarde+=$waarde;
        }
        $this->pdf->row(array('', 'Waarde beleggingen in Sequoia-fondsen : € '.$this->formatGetal($huisfondsWaarde, 2)));//, '€', $this->formatGetal($huisfondsWaarde, 2)));
        $this->pdf->ln(3);
        $this->pdf->row(array('', 'Grondslag voor fee-berekening : € '.$this->formatGetal($this->waarden['rekenvermogen'], 2)));//, '€', $this->formatGetal($this->waarden['rekenvermogen'], 2)));
        $this->pdf->ln(3);
      }
    }
   $this->pdf->row(array("","Beheervergoeding $vanDatum t/m $totDatum","€",$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));

   foreach ($this->waarden['extraFactuurregels']['regels'] as $regel)
   {
     if($regel['btw']>0)//$this->waarden['btwTarief']>0)
     {

       $this->pdf->row(array('', $regel['omschrijving'], '€', $this->formatGetal($regel['bedrag'], 2)));
       $this->waarden['beheerfeePerPeriode']+=$regel['bedrag'];
     }
   }

//listarray($this->waarden['extraFactuurregels']);
   //listarray($this->waarden);
   
   // listarray($this->waarden[huisfondsFactuurWaarden]); //periodeDeelVanJaar
   $btw=$this->waarden['btwTarief']*$this->waarden['beheerfeePerPeriode']/100;
    
   $this->pdf->ln(3);
   $this->pdf->row(array('',"BTW ".$this->formatGetal($this->waarden['btwTarief'],1)."%",'€',$this->formatGetal($btw,2)));
$this->pdf->ln(5);

   $this->pdf->Rect($this->pdf->marge+11,$this->pdf->GetY()-1,165+2,4+2);
   $bedrag=$this->waarden['beheerfeePerPeriode']+$btw;
   $this->pdf->row(array('',"Factuurtotaal",'€',$this->formatGetal($bedrag,2)));
   $this->pdf->ln(7);
   
   
   if(count($this->waarden['huisfondsFactuurWaarden'])>0 || $this->formatGetal($this->waarden['bestandsvergoeding'] <> 0))
   {
      $this->pdf->row(array('',"Voor u ontvangen:"));
      $this->pdf->ln(5);
   }
   
   if(count($this->waarden['huisfondsFactuurWaarden'])>0)
   {
     foreach($this->waarden['huisfondsFactuurWaarden'] as $fonds)
     {
       $this->pdf->row(array('',"Teruggave managementvergoeding ".$fonds['omschrijving'],'€',$this->formatGetal(-1*$fonds['retourJaar']*$this->waarden['periodeDeelVanJaar'],2)));
       $bedrag-=$fonds['retourJaar']*$this->waarden['periodeDeelVanJaar'];
     }
     //if($this->waarden['btwTarief']==0)
     //{
     foreach ($this->waarden['extraFactuurregels']['regels'] as $regel)
     {
       if($regel['btw']==0)//$this->waarden['btwTarief']>0)
       {
         $this->pdf->row(array('', $regel['omschrijving'], '€', $this->formatGetal($regel['bedrag'], 2)));
         $bedrag += $regel['bedrag'];
       }
     }
     //}
     $this->pdf->ln(5);
   }

  
	 if($this->waarden['BestandsvergoedingUitkeren']==1 && $this->formatGetal($this->waarden['bestandsvergoeding'] <> 0))
   {
      $this->pdf->row(array('',"Retourprovisie beleggingsfondsen",'€',$this->formatGetal($this->waarden['bestandsvergoeding']*-1 ,2)));
      $this->pdf->ln(5);
      $bedrag-=$this->waarden['bestandsvergoeding'];
   }


   if(count($this->waarden['huisfondsFactuurWaarden'])>0 || $this->formatGetal($this->waarden['bestandsvergoeding'] <> 0))
   {
     $this->pdf->ln(3);
     $this->pdf->Rect($this->pdf->marge+11,$this->pdf->GetY()-1,165+2,4+2);
     $this->pdf->row(array("","Te betalen","€",$this->formatGetal($bedrag,2)));
   }
   $this->waarden['btw']=round($btw,2);
   $this->waarden['beheerfeeBetalenIncl']=round($bedrag,2);
    /*
    $this->pdf->Line($this->pdf->marge ,$this->pdf->GetY()-2,$this->pdf->marge +165 ,$this->pdf->GetY()-2);

    $this->pdf->row(array("Subtotaal", "",'EUR',$this->formatGetal($this->waarden['beheerfeePerPeriodeNor'],2)));

    $this->pdf->Line($this->pdf->marge ,$this->pdf->GetY()+2,$this->pdf->marge +165 ,$this->pdf->GetY()+2);

  	$this->pdf->ln(8);
  	$this->pdf->SetFont($font,"B",10);
		$this->pdf->row(array("Factuurtotaal",'','EUR',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
		$this->pdf->Line($this->pdf->marge+100 ,$this->pdf->GetY()+1,$this->pdf->marge +165 ,$this->pdf->GetY()+1);
		$this->pdf->Line($this->pdf->marge+100 ,$this->pdf->GetY()+2,$this->pdf->marge +165 ,$this->pdf->GetY()+2);
		$this->pdf->SetFont($font,"",10);
*/

		$this->pdf->SetWidths(array(12,160));
		$this->pdf->ln(25);

		$this->pdf->row(array('',"Bovenvermelde vergoeding zal ten laste worden gebracht van uw rekening"));

		$this->pdf->SetTextColor($this->pdf->rapport_kop2_fontcolor['r'],$this->pdf->rapport_kop2_fontcolor['g'],$this->pdf->rapport_kop2_fontcolor['b']);
$this->pdf->ln(6);
$this->pdf->row(array('',"Vergoeding beheerwerkzaamheden is inclusief:

  •   Jaarlijkse inventarisatie en financiële planning op hoofdlijnen
  •   Dagelijks bewaken en beheren van uw effectenportefeuille
  •   Uitvoeren, monitoren en controleren van alle effectentransacties
  •   Samenstellen, controleren en verzenden per kwartaal van de vermogensrapportage
  •   Persoonlijke gesprekken conform uw wensen
  •   Gebruikmaking van de voor u afgesproken kortingsregeling bij banken
  •   Begeleiding bij openen van (effecten) rekening en overboeking effecten
  •   Introductie in ons netwerk bij financieel specialisten zoals accountants, fiscalisten, estate planners en\n       financieel planners"));
		$this->pdf->SetTextColor(0,0,0);
//$this->pdf->rapport_voettext='';

?>
