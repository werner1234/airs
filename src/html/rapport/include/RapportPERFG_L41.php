<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.11 $

 		$Log: RapportPERFG_L41.php,v $
 		Revision 1.11  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.10  2013/04/24 16:01:30  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/04/06 16:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/03/02 17:14:06  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/12/30 14:27:12  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/12/02 11:05:56  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/11/21 16:29:06  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/11/18 18:05:39  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/11/17 16:02:20  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/11/10 15:42:19  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/11/07 17:08:05  rvv
 		*** empty log message ***
 		
 		
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L41.php");

class RapportPERFG_L41
{
	function RapportPERFG_L41($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Attributie-analyse";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->rapJaar =$RapStopJaar;
	}

	function formatGetal($waarde, $dec,$toonNul=false)
	{
	  if($toonNul==false && $waarde==0)
	    return '';
	  else
  		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
    global $USR;
    $this->pdf->addPage();
    $this->pdf->templateVars['PERFGPaginas']=$this->pdf->page;
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		if(db2jul($portefeuilledata['startDatum']) < db2jul(date("Y-01-01",$this->pdf->rapport_datumvanaf)))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datumvanaf);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
	  $this->tweedePerformanceStart=$rapportageStartJaar;
    

    $att=new ATTberekening_L41($this);
    $att->indexPerformance=true;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum);
    $this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum);
    $this->waarden['Historie']=$att->bereken(substr($portefeuilledata['startDatum'],0,10),  $this->rapportageDatum);
    
    
    $lwb=297-($this->pdf->marge*2); 
    $cols=12;
    $cellWidthKop=$lwb/5;
    $cellWidth=$lwb/($cols+3);
    $widths=array(3*$cellWidth);
    $aligns=array('L');
    $fill=array(1);
    for($i=1;$i<=$cols;$i++)
    {
      $widths[]=$cellWidth;
      $aligns[]='R';
      $fill[]=1;
    }
    $xStart=$this->pdf->marge;
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=$fill;
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);
 

		$this->pdf->setX($xStart);
//		$this->pdf->Cell(70, 4, "--- Cumulatief ".$this->rapJaar." ---",0,0,'C');
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->SetAligns(array('L','C','C','C','C'));
    $this->pdf->SetWidths(array($cellWidthKop,$cellWidthKop,$cellWidthKop,$cellWidthKop,$cellWidthKop));
    $this->pdf->row(array('',"Portefeuille","Index","Verschil","Attributie Effecten"));
    $this->pdf->SetWidths($widths);
  	$this->pdf->SetAligns($aligns);
		$this->pdf->row(array("Categorie",  "Weging","Resultaat","Bijdrage",  "Weging","Resultaat","Bijdrage",  "Weging","Resultaat","Bijdrage",  "Allocatie","Selectie","Totaal"));
//	  $this->pdf->excelData[]=array("Performance naar asset categorie (%)","Porte- feuille","Port. Contrib.","Index","Index Contrib.","Over- perf.","Rel. Contrib.","Port.","Index","Overperf.");

    $this->pdf->CellBorders = array();
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

	  //listarray($this->waarden['Periode']);
    $bovencat=$att->categorien;
   // $bovencat['totaal']='Totaal';
    foreach ($bovencat as $categorie=>$categorieOmschrijving)
    {
      $allocateEffect=$this->waarden['Periode'][$categorie]['verschilWeging']*$this->waarden['Periode'][$categorie]['indexPerf'];
      $selectieEffect=($this->waarden['Periode'][$categorie]['overPerf'])*$this->waarden['Periode'][$categorie]['weging'];
	    $this->pdf->row(array($categorieOmschrijving,
                        $this->formatGetal($this->waarden['Periode'][$categorie]['weging']*100,2),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['procent'],2),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['bijdrage'],2),
                        $this->formatGetal($this->waarden['Periode'][$categorie]['indexBijdrageWaarde']*100,2),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['indexPerf'],2),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['indexBijdrage'],2), 
                        $this->formatGetal($this->waarden['Periode'][$categorie]['verschilWeging']*100,2),
                        $this->formatGetal($this->waarden['Periode'][$categorie]['overPerf'],2),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['relContrib'],2),
	                      $this->formatGetal($allocateEffect,2),
	                      $this->formatGetal($selectieEffect,2),
                        $this->formatGetal($allocateEffect+$selectieEffect,2),));
                        $this->pdf->excelData[]=array($categorieOmschrijving,$this->waarden['Periode'][$categorie]['procent'],$this->waarden['Periode'][$categorie]['bijdrage'],
	                                                    $this->waarden['Periode'][$categorie]['indexPerf'],$this->waarden['Periode'][$categorie]['indexBijdrage'],$this->waarden['Periode'][$categorie]['overPerf'],
	                                                    $this->waarden['Periode'][$categorie]['relContrib'],$this->waarden['Jaar'][$categorie]['procent'],$this->waarden['Jaar'][$categorie]['indexPerf'],$this->waarden['Jaar'][$categorie]['overPerf']);
      $somBoven['allocateEffect']+=$allocateEffect;
      $somBoven['selectieEffect']+=$selectieEffect;
      $somBoven['total']+=$allocateEffect+$selectieEffect;
      $somBoven['weging']+=$this->waarden['Periode'][$categorie]['weging']*100;
      $somBoven['indexBijdrageWaarde']+=$this->waarden['Periode'][$categorie]['indexBijdrageWaarde']*100;
      $somBoven['verschilWeging']+=$this->waarden['Periode'][$categorie]['verschilWeging']*100;
    }

    
    $totalen=array();
    unset($this->waarden['Periode']['totaal']);
    foreach ($this->waarden['Periode'] as $categorie=>$categorieData)
    {
      foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
      {
        if($maand <> '')
        {
          $totalen[$maand]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
          $totalen[$maand]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['weging']*100;
          $totalen[$maand]['portBijdrage']+=$maandWaarden['bijdrage']*100;
          $totalen[$maand]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
          $totalen[$maand]['overperfBijdrage']+=$maandWaarden['relContrib']*100;
          //echo "$categorie ".round($maandWaarden['bijdrage']*100,3)." -> ".round($totalen[$maand]['portBijdrage'],2)."<br>\n";
        }
      }
    }

    foreach ($totalen as $maand=>$maandWaarden)
   	{
   	  foreach ($maandWaarden as $veld=>$waarde)
   	  {
   	    if(!isset($laatste[$veld]))
   	      $laatste[$veld]=0;
   	    $this->jaarTotalen[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatste[$veld]/100)-1)*100;
   	    $laatste[$veld]=$this->jaarTotalen[$veld];
   	  }
   	}
   	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array(array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'));

    $this->pdf->row(array("Totaal",'',
    '',$this->formatGetal($this->jaarTotalen['portBijdrage'],2),
    '','',$this->formatGetal($this->jaarTotalen['indexBijdrage'],2),
    '','',$this->formatGetal($this->jaarTotalen['overperfBijdrage'],2),
    $this->formatGetal($somBoven['allocateEffect'],2),
    $this->formatGetal($somBoven['selectieEffect'],2),
    $this->formatGetal($somBoven['total'],2)));
    unset($this->pdf->CellBorders);

    unset($totalen);
    unset($this->waarden['Jaar']['totaal']);
    foreach ($this->waarden['Jaar'] as $categorie=>$categorieData)
    {
      foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
      {
        if($maand <> '')
        {
          $totalen[$maand]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
          $totalen[$maand]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['weging']*100;
          $totalen[$maand]['portBijdrage']+=$maandWaarden['bijdrage']*100;
          $totalen[$maand]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
          $totalen[$maand]['overperfBijdrage']+=$maandWaarden['relContrib']*100;
          //echo "$categorie ".round($maandWaarden['bijdrage']*100,3)." -> ".round($totalen[$maand]['portBijdrage'],2)."<br>\n";
          //echo "$categorie  $maand ".$totalen[$maand]['selectieEffect']."= (".$maandWaarden['procent']."-".$maandWaarden['indexPerf'].")*".$maandWaarden['weging']."*100 <br>\n";

        }
      }
    }
    unset($laatste);
    unset($this->jaarTotalen);
    foreach ($totalen as $maand=>$maandWaarden)
   	{
   	  foreach ($maandWaarden as $veld=>$waarde)
   	  {
   	    if(!isset($laatste[$veld]))
   	      $laatste[$veld]=0;
   	    $this->jaarTotalen[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatste[$veld]/100)-1)*100;
   	    $laatste[$veld]=$this->jaarTotalen[$veld];
   	  }
   	}
    
    
    
        $lwb=(297-($this->pdf->marge*2))/2; 
    $cols=6;
    $cellWidth=$lwb/($cols+0.25);
    $widths=array($cellWidth);
    $aligns=array('L');
    $fill=array(1);
    for($i=1;$i<=$cols;$i++)
    {
      $widths[]=$cellWidth;
      $aligns[]='R';
      $fill[]=1;
    }
    $xStart=$this->pdf->marge;
    $this->pdf->Ln(4);
   
    $this->pdf->SetWidths($widths);
  	$this->pdf->SetAligns($aligns);
    
    
    $this->pdf->setXY($this->pdf->marge,125);
    //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->Cell(70, 4, "Performance over totaal gemiddeld belegd vermogen",0,1,'L');
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=$fill;
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);



    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->SetWidths(array(15,18,15,24,20,20,20));
    $this->pdf->row(array("\nMaand","\nPortefeuille","\nIndex","\nOverperf.","Allocatie\nEffect","Selectie\nEffect"));
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array("Maand","Portefeuille","Index","Overperf.","Allocatie Effect","Selectie Effect","Interactie Effect");
    $this->pdf->CellBorders = array();
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    

   	foreach ($totalen as $maand=>$maandWaarden)
   	{
   	  /*
   	  $this->pdf->row(array(date("m-Y",db2jul($maand)),
       $this->formatGetal($maandWaarden['portBijdrage'],2),
       $this->formatGetal($maandWaarden['indexBijdrage'],2),
   	  $this->formatGetal($maandWaarden['overperfBijdrage'],2),
       $this->formatGetal($maandWaarden['allocateEffect'],2),
       $this->formatGetal($maandWaarden['selectieEffect'],2),
   	  $this->formatGetal($maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect']),2)));
*/
   	  $this->pdf->row(array(date("m-Y",db2jul($maand)),
       $this->formatGetal($maandWaarden['portBijdrage'],2,true),
       $this->formatGetal($maandWaarden['indexBijdrage'],2,true),
   	  $this->formatGetal($maandWaarden['overperfBijdrage'],2,true),
       $this->formatGetal($maandWaarden['allocateEffect'],2,true),
   	  $this->formatGetal($maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']),2,true)));
      
   	  $this->pdf->excelData[]=array(date("m-Y",db2jul($maand)),$maandWaarden['portBijdrage'],$maandWaarden['indexBijdrage'],$maandWaarden['overperfBijdrage'],
   	  $maandWaarden['allocateEffect'],$maandWaarden['selectieEffect'],($maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect'])));
      
   	}
   	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    $this->pdf->CellBorders = array(array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'));
    $this->pdf->row(array('Totaal',$this->formatGetal($this->jaarTotalen['portBijdrage'],2,true),$this->formatGetal($this->jaarTotalen['indexBijdrage'],2,true),
   	  $this->formatGetal($this->jaarTotalen['overperfBijdrage'],2,true),$this->formatGetal($this->jaarTotalen['allocateEffect'],2,true),
   	  $this->formatGetal($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']),2,true)));
      unset($this->pdf->CellBorders);
   	$this->pdf->excelData[]=array('Totaal',$this->jaarTotalen['portBijdrage'],$this->jaarTotalen['indexBijdrage'],$this->jaarTotalen['overperfBijdrage'],
   	            $this->jaarTotalen['allocateEffect'],$this->jaarTotalen['selectieEffect'],($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']+$this->jaarTotalen['selectieEffect'])));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

   	//listarray($this->jaarTotalen);exit;
   	//$this->pdf->setXY(140+$this->pdf->marge,125);
    $this->pdf->setY(125);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
   // $this->pdf->Cell(70, 4, "Performance historie vanaf ".date('d-m-Y',db2jul($this->pdf->PortefeuilleStartdatum)),0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(140,18,18,15,24,20,20,20));
    
    //echo $lwb;exit;
    $w=array_reverse($widths);
    array_pop($w);
    array_push($w,$lwb+$cellWidth/8); 
    $w=array_reverse($w);
    
    $f=array_reverse($fill);
    //array_pop($w);
    array_push($f,0); 
    $f=array_reverse($f);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=$f;
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);

    $this->pdf->SetWidths($w);
    
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
    $this->pdf->row(array('',"\nPeriode","\nPortefeuille","\nIndex","\nOverperf.","Allocatie\nEffect","Selectie\nEffect"));
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array("Periode","Portefeuille","Index","Overperf.","Allocatie Effect","Selectie Effect");
   	unset($this->pdf->CellBorders);
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $maandTotalen=array();
    unset($this->waarden['Historie']['totaal']);
   	foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
    {
      foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
      {
        if($maand <> '')
        {
          $maandTotalen[$maand]['allocateEffect']+=($maandWaarden['weging']-$maandWaarden['indexBijdrageWaarde'])*$maandWaarden['indexPerf']*100;
          $maandTotalen[$maand]['selectieEffect']+=($maandWaarden['procent']-$maandWaarden['indexPerf'])*$maandWaarden['weging']*100;
          $maandTotalen[$maand]['portBijdrage']+=$maandWaarden['bijdrage']*100;
          $maandTotalen[$maand]['indexBijdrage']+=$maandWaarden['indexBijdrage']*100;
          $maandTotalen[$maand]['overperfBijdrage']+=$maandWaarden['relContrib']*100;
        }
      }
    }

   	$laatste=array();
   	foreach ($maandTotalen as $maand=>$maandWaarden)
   	{
   	  $jaar=substr($maand,0,4);
   	  $kwartaal=ceil(substr($maand,5,2)/3);
   	  foreach ($maandWaarden as $veld=>$waarde)
   	  {
   	    if(!isset($laatste[$veld]))
   	      $laatste[$veld]=0;
   	    $this->historieTotalen[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatste[$veld]/100)-1)*100;
   	    $laatste[$veld]=$this->historieTotalen[$veld];

   	    if(!isset($laatsteQWaarde[$veld]) || $laatsteQ <> $kwartaal)
   	      $laatsteQWaarde[$veld]=0;

   	    $laatsteQWaarde[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatsteQWaarde[$veld]/100)-1)*100;
   	    $kwartaalTotalen["$jaar Q$kwartaal"][$veld]=$laatsteQWaarde[$veld];
   	  }
   	  $laatsteQ=$kwartaal;
   	}

   	foreach ($kwartaalTotalen as $kwartaal=>$kwartaalWaarden)
   	{
   	  $this->pdf->row(array('',$kwartaal,$this->formatGetal($kwartaalWaarden['portBijdrage'],2,true),$this->formatGetal($kwartaalWaarden['indexBijdrage'],2,true),
   	  $this->formatGetal($kwartaalWaarden['overperfBijdrage'],2,true),$this->formatGetal($kwartaalWaarden['allocateEffect'],2,true),
   	  $this->formatGetal($kwartaalWaarden['overperfBijdrage']-($kwartaalWaarden['allocateEffect']),2,true)));

   	  $this->pdf->excelData[]=array($kwartaal,$kwartaalWaarden['portBijdrage'],$kwartaalWaarden['indexBijdrage'],$kwartaalWaarden['overperfBijdrage'],
   	  $kwartaalWaarden['allocateEffect'],$kwartaalWaarden['selectieEffect'],($kwartaalWaarden['overperfBijdrage']-($kwartaalWaarden['allocateEffect']+$kwartaalWaarden['selectieEffect'])));
   	}
   	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('',array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'));
    $this->pdf->row(array('','Totaal',$this->formatGetal($this->historieTotalen['portBijdrage'],2,true),$this->formatGetal($this->historieTotalen['indexBijdrage'],2,true),
   	  $this->formatGetal($this->historieTotalen['overperfBijdrage'],2,true),$this->formatGetal($this->historieTotalen['allocateEffect'],2,true),
   	  $this->formatGetal($this->historieTotalen['overperfBijdrage']-$this->historieTotalen['allocateEffect'],2,true)));
   	unset($this->pdf->CellBorders);
     $this->pdf->excelData[]=array('Totaal',$this->historieTotalen['portBijdrage'],$this->historieTotalen['indexBijdrage'],$this->historieTotalen['overperfBijdrage'],
   	            $this->historieTotalen['allocateEffect'],$this->historieTotalen['selectieEffect'],($this->historieTotalen['overperfBijdrage']-($this->historieTotalen['allocateEffect']+$this->historieTotalen['selectieEffect'])));


$this->pdf->AddPage();

$regelItems=array('beginwaarde','storting','onttrekking','gerealiseerd','ongerealiseerd','opbrengst','rente','kosten','eindwaarde','resultaat','procent');

$vertalingen=array('beginwaarde'=>'Beginwaarde','storting'=>'Stortingen','onttrekking'=>'Onttrekkingen',
'gerealiseerd'=>'Gerealiseerd resultaat','ongerealiseerd'=>'Ongerealiseerd resultaat',
'opbrengst'=>'Directe opbrengsten','rente'=>'Opgelopen rente','kosten'=>'Kosten','eindwaarde'=>'Eindwaarde',
'resultaat'=>'Totaal resultaat','procent'=>'Procent');
//listarray($att->categorien);


$categorieOmschrijving=$att->categorien;
  $vertaling=array('Aandelen - Mature Markets'=>"Aandelen -\n Mature\nMarkets",'Aandelen - Emerging Markets'=>"Aandelen -\nEmerging\nMarkets",
  'Staatsobligaties'=>"\nStaats-\nobligaties",'Bedrijfsobligaties'=>"\nBedrijfs-\nobligaties",'Vastgoed'=>"\n\nVastgoed",
  'Private Equity'=>"\nPrivate\nEquity",'Alternatieven'=>"\n\nAlternatieven",
  'Liquiditeiten'=>"\n\nLiquiditeiten",'Overigen'=>"\n\nOverigen");
foreach($categorieOmschrijving as $cat=>$omsch)
{
  if(isset($vertaling[$omsch]))
    $categorieOmschrijving[$cat]=$vertaling[$omsch];
}
        
foreach ($this->waarden['Periode'] as $categorie=>$categorieData)
{
  if($categorie <> '')
    $categorien[$categorie]=$categorie;
}
$grafiekData=array();
foreach($categorien as $categorie)
{
  foreach($regelItems as $item)
  {
    $regels[$item][$categorie]=$this->waarden['Periode'][$categorie][$item];
  }
  $grafiekData[$vertaling[$att->categorien[$categorie]]]['procent']=$this->waarden['Periode'][$categorie]['procent'];;
}

$header=array(" \n \n ");
foreach($categorien as $categorie)
  $header[]= $categorieOmschrijving[$categorie];  

    $lwb=297-($this->pdf->marge*2); 
    $cols=count($header);
    $cellWidth=$lwb/($cols+1);
    $widths=array(2*$cellWidth);
    $aligns=array('L');
    $fill=array(1);
    for($i=1;$i<=$cols;$i++)
    {
      $widths[]=$cellWidth;
      $aligns[]='R';
      $fill[]=1;
    }
    $xStart=$this->pdf->marge;
  
    $this->pdf->SetWidths($widths);
  	$this->pdf->SetAligns($aligns);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=$fill;
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);
$this->pdf->row($header); 
    $this->pdf->CellBorders = array();
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


foreach($regels as $key=>$itemData)
{
  if($key=='procent')
  {
    $dec=2;
  }
  else
    $dec=0;  
  $tmp=array($vertalingen[$key]);
  foreach($itemData as $value)
   $tmp[]=$this->formatGetal($value,$dec);
  
  if($key=='procent'||$key=='resultaat' )
  {
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array(array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'));
  }
  else
  {
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }  
  $this->pdf->row($tmp);   
}

		      $this->pdf->SetXY(15,180)		;//112
		      $this->VBarDiagram(297-40, 70, $grafiekData,'Rendement');
//listarray($att->categorien);
//listarray($this->waarden);
//listarray($kwartalen);
    unset($this->pdf->CellBorders);
	}
  
  
  function VBarDiagram($w, $h, $data,$title)
  {
      global $__appvar;
      $XPage=$this->pdf->GetX();
      $YPage=$this->pdf->GetY();
      $this->pdf->SetXY($XPage,$YPage-$h);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.5);
		  $this->pdf->Cell(0, 5, $title, 0, 1);
  		//$this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),282,$this->pdf->GetY());
      $h=$h-10;
      $this->pdf->SetXY($XPage+10,$YPage);
          
          
      $legendaWidth = 0;
      $grafiekPunt = array();
      $verwijder=array();
      
      
            $tinten=array(1,0.5,0.7);
foreach($tinten as $tint)
{
$col[]=array($this->pdf->blue[0]*$tint,$this->pdf->blue[1]*$tint,$this->pdf->blue[2]*$tint);
$col[]=array($this->pdf->midblue[0]*$tint,$this->pdf->midblue[1]*$tint,$this->pdf->midblue[2]*$tint);//$this->pdf->midblue;
$col[]=array($this->pdf->lightblue[0]*$tint,$this->pdf->lightblue[1]*$tint,$this->pdf->lightblue[2]*$tint);//$this->pdf->lightblue;
$col[]=array($this->pdf->green[0]*$tint,$this->pdf->green[1]*$tint,$this->pdf->green[2]*$tint);//$this->pdf->green;
$col[]=array($this->pdf->kopkleur[0]*$tint,$this->pdf->kopkleur[1]*$tint,$this->pdf->kopkleur[2]*$tint);//$this->pdf->kopkleur;
$col[]=array($this->pdf->lightgreen[0]*$tint,$this->pdf->lightgreen[1]*$tint,$this->pdf->lightgreen[2]*$tint);//$this->pdf->lightgreen;
}
      $minVal=0;
      $maxVal=25;
      foreach ($data as $cat=>$waarden)
      {
        $legenda[$cat] = $cat;
        $n=0;

        foreach ($waarden as $categorie=>$waarde)
        {
          
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$cat][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$cat]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if(!isset($this->categorieVolgorde[$categorie]))
          {
            $this->categorieVolgorde[$categorie]=$categorie;
            $this->categorieOmschrijving[$categorie]=$categorie;
          } 
          if(!isset($colors[$categorie])) 
            $colors[$categorie]=$col[$n];//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }

      $numBars = count($legenda);
      //$numBars=10;

      if($color == null)
      {
        $color=array(155,155,155);
      }


      if($maxVal <= 25)
        $maxVal=25;
      elseif($maxVal < 100)
        $maxVal=100;

      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;


      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }


      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      $lineW=1;
      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull=true;
        if($i != $nulpunt)
          $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $lineW ,$i,array('dash' => 0,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)."%",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        if($i != $nulpunt)
          $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $lineW ,$i,array('dash' => 0,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."%",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 0.5));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 0.5);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(255,255,255)));
      $this->pdf->SetLineWidth(0.3527);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      

   
   foreach ($grafiek as $datum=>$data)
   {
      foreach (($this->categorieVolgorde) as $categorie)
      {
        if(isset($data[$categorie]))
        {
          $val=$data[$categorie];
        //foreach($data as $categorie=>$val)
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          //$this->pdf->Line($xval, $yval+$hval, $xval + $lval ,$yval+$hval,array('dash' => 0,'color'=>array(255,255,255)));
          
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
         {
           //$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
           $this->pdf->SetXY($xval-$eBaton/2, $YstartGrafiek);
           $this->pdf->multiCell($eBaton*2, 4, $legenda[$datum],0,'C');
           //($w,$h,$txt,$border=0,$align='J',$fill=0)
         }

         $legendaPrinted[$datum] = 1;
         }
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
      foreach (($this->categorieVolgorde) as $categorie)
      {
        if(isset($data[$categorie]))
        {
          $val=$data[$categorie];
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'D',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         }
      }
      $i++;
   }
   $this->pdf->SetLineWidth(0.1);
   $this->pdf->Line($XstartGrafiek, $nulpunt, $XstartGrafiek + $bGrafiek ,$nulpunt,array('dash' => 0,'color'=>array(0,0,0)));
   $this->pdf->Line($XstartGrafiek, $bodem, $XstartGrafiek ,$top,array('dash' => 0,'color'=>array(0,0,0)));
   $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }

}
?>