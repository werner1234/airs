<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/25 17:11:27 $
 		File Versie					: $Revision: 1.4 $

 		$Log: RapportATT_L72.php,v $
 		Revision 1.4  2018/08/25 17:11:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.2  2017/02/08 12:32:32  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/09/28 15:53:55  rvv
 		*** empty log message ***
 		
 
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L72.php");

class RapportATT_L72
{
	function RapportATT_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->rapJaar =$RapStopJaar;
	}

	function formatGetal($waarde, $dec,$nulTonen=false)
	{
	  if($waarde==0 && $nulTonen==false)
	    return '';
	  else
  		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
    global $USR;
    $this->pdf->addPage();
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

    $att=new ATTberekening_L72($this);
    $att->indexPerformance=true;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum);
    $this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum);
    
    $historischeStart=substr($portefeuilledata['startDatum'],0,10);
    $kwartalen=$att->getKwartalen(db2jul($historischeStart), db2jul($this->rapportageDatum));
   // echo "|".count($kwartalen)."|";
  
    if(count($kwartalen) > 12)
    {
      $historischeStart=$kwartalen[count($kwartalen)-12]['start'];
    }
  //listarray($historischeStart);exit;
    
    $this->waarden['Historie']=$att->bereken($historischeStart,  $this->rapportageDatum);
    
    $this->pdf->SetWidths(array(50,19,19,19,19,19,19,19,19,19,19,19,19));
    $xStart=$this->pdf->marge;
    for($i=0;$i<9;$i++){$xStart+=$this->pdf->widths[$i];}
    $yStart=$this->pdf->getY();
   	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));
		$this->pdf->CellBorders=array('U','U','U','U','U','U','U','U','U','U','U','U','U');
		$this->pdf->setX($xStart);
		$this->pdf->Cell(70, 4, "--- ".vertaalTekst("Cumulatief",$this->pdf->rapport_taal)." ".$this->rapJaar." ---",0,0,'C');
    $this->pdf->setX($this->pdf->marge);
		$this->pdf->row(array(vertaalTekst("Performance naar asset\ncategorie (%)",$this->pdf->rapport_taal),
											vertaalTekst("Port.\nverdeling",$this->pdf->rapport_taal),
											vertaalTekst("Norm\nverdeling",$this->pdf->rapport_taal),
											vertaalTekst("Porte-\nfeuille",$this->pdf->rapport_taal),
											vertaalTekst("Port.\nContrib.",$this->pdf->rapport_taal),
											vertaalTekst("Index",$this->pdf->rapport_taal),
											vertaalTekst("Index\nContrib.",$this->pdf->rapport_taal),
											vertaalTekst("Over-\nperf.",$this->pdf->rapport_taal),
											vertaalTekst("Rel.\nContrib.",$this->pdf->rapport_taal),
											vertaalTekst("\nPort.",$this->pdf->rapport_taal),
											vertaalTekst("\nIndex",$this->pdf->rapport_taal),
											vertaalTekst("\nOverperf.",$this->pdf->rapport_taal),
											vertaalTekst("Rel.\nContrib.",$this->pdf->rapport_taal)));
	  $this->pdf->excelData[]=array("Performance naar asset categorie (%)","Port.verdeling","Normverdeling","Portefeuille","Port.Contrib.","Index","IndexContrib.","Over-perf.","Rel.Contrib.","Port.","Index","Overperf.","Rel.Contrib.");
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

	 // listarray($this->waarden['Periode']);
    $bovencat=$att->categorien;
   // $bovencat['totaal']='Totaal';
    foreach ($bovencat as $categorie=>$categorieOmschrijving)
    {
	    $this->pdf->row(array(vertaalTekst($categorieOmschrijving,$this->pdf->rapport_taal),
                        $this->formatGetal($this->waarden['Periode'][$categorie]['weging'],2,true),
                        $this->formatGetal($this->waarden['Periode'][$categorie]['indexBijdrageWaarde'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['procent'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['bijdrage'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['indexPerf'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['indexBijdrage'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['overPerf'],2,true),
	                      $this->formatGetal($this->waarden['Periode'][$categorie]['relContrib'],2,true),
	                      $this->formatGetal($this->waarden['Jaar'][$categorie]['procent'],2,true),
	                      $this->formatGetal($this->waarden['Jaar'][$categorie]['indexPerf'],2,true),
	                      $this->formatGetal($this->waarden['Jaar'][$categorie]['overPerf'],2,true),
                        $this->formatGetal($this->waarden['Jaar'][$categorie]['relContrib'],2,true)));
      $this->pdf->excelData[]=array($categorieOmschrijving,$this->waarden['Periode'][$categorie]['procent'],$this->waarden['Periode'][$categorie]['bijdrage'],
	                                                    $this->waarden['Periode'][$categorie]['indexPerf'],$this->waarden['Periode'][$categorie]['indexBijdrage'],$this->waarden['Periode'][$categorie]['overPerf'],
	                                                    $this->waarden['Periode'][$categorie]['relContrib'],$this->waarden['Jaar'][$categorie]['procent'],$this->waarden['Jaar'][$categorie]['indexPerf'],$this->waarden['Jaar'][$categorie]['overPerf']);
    }

    $this->pdf->line($xStart,$yStart,$xStart,$this->pdf->getY());
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

    foreach($this->waarden['Periode'] as $categorie=>$weging)
    {
      $this->jaarTotalen['weging']+=$weging['weging'];
      $this->jaarTotalen['indexBijdrageWaarde']+=$weging['indexBijdrageWaarde'];
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
    $this->pdf->row(array("Totaal",$this->formatGetal($this->jaarTotalen['weging'],2),$this->formatGetal($this->jaarTotalen['indexBijdrageWaarde'],2),'',$this->formatGetal($this->jaarTotalen['portBijdrage'],2),'',$this->formatGetal($this->jaarTotalen['indexBijdrage'],2),'',$this->formatGetal($this->jaarTotalen['overperfBijdrage'],2)));
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

    if($this->pdf->getY()>125)
			$this->pdf->addPage();
    $this->pdf->setXY($this->pdf->marge,125);

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(70, 4, "Performance over totaal gemiddeld belegd vermogen",0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(15,18,15,24,20,20,20));
    $this->pdf->CellBorders=array('U','U','U','U','U','U','U');
    $this->pdf->row(array(vertaalTekst("\nMaand",$this->pdf->rapport_taal),
											vertaalTekst("\nPortefeuille",$this->pdf->rapport_taal),
											vertaalTekst("\nIndex",$this->pdf->rapport_taal),
											vertaalTekst("\nOverperf.",$this->pdf->rapport_taal),
											vertaalTekst("Allocatie\nEffect",$this->pdf->rapport_taal),
											vertaalTekst("Selectie\nEffect",$this->pdf->rapport_taal),
											vertaalTekst("Interactie\nEffect",$this->pdf->rapport_taal)));

    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array("Maand","Portefeuille","Index","Overperf.","Allocatie Effect","Selectie Effect","Interactie Effect");
   	unset($this->pdf->CellBorders);

   	foreach ($totalen as $maand=>$maandWaarden)
   	{
   	  $this->pdf->row(array(date("m-Y",db2jul($maand)),$this->formatGetal($maandWaarden['portBijdrage'],2),$this->formatGetal($maandWaarden['indexBijdrage'],2),
   	  $this->formatGetal($maandWaarden['overperfBijdrage'],2),$this->formatGetal($maandWaarden['allocateEffect'],2),$this->formatGetal($maandWaarden['selectieEffect'],2),
   	  $this->formatGetal($maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect']),2)));

   	  $this->pdf->excelData[]=array(date("m-Y",db2jul($maand)),$maandWaarden['portBijdrage'],$maandWaarden['indexBijdrage'],$maandWaarden['overperfBijdrage'],
   	  $maandWaarden['allocateEffect'],$maandWaarden['selectieEffect'],($maandWaarden['overperfBijdrage']-($maandWaarden['allocateEffect']+$maandWaarden['selectieEffect'])));
   	}
   	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Totaal',$this->formatGetal($this->jaarTotalen['portBijdrage'],2),$this->formatGetal($this->jaarTotalen['indexBijdrage'],2),
   	  $this->formatGetal($this->jaarTotalen['overperfBijdrage'],2),$this->formatGetal($this->jaarTotalen['allocateEffect'],2),$this->formatGetal($this->jaarTotalen['selectieEffect'],2),
   	  $this->formatGetal($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']+$this->jaarTotalen['selectieEffect']),2)));
   	$this->pdf->excelData[]=array('Totaal',$this->jaarTotalen['portBijdrage'],$this->jaarTotalen['indexBijdrage'],$this->jaarTotalen['overperfBijdrage'],
   	            $this->jaarTotalen['allocateEffect'],$this->jaarTotalen['selectieEffect'],($this->jaarTotalen['overperfBijdrage']-($this->jaarTotalen['allocateEffect']+$this->jaarTotalen['selectieEffect'])));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

   	//listarray($this->jaarTotalen);exit;
   	$this->pdf->setXY(140+$this->pdf->marge,125);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(70, 4, "Performance historie vanaf ".DatumShort_L72($this->pdf,db2jul($portefeuilledata['startDatum'])),0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(140,18,18,15,24,20,20,20));
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
    $this->pdf->CellBorders=array('','U','U','U','U','U','U','U');
    $this->pdf->row(array('',vertaalTekst("\nPeriode",$this->pdf->rapport_taal),
											vertaalTekst("\nPortefeuille",$this->pdf->rapport_taal),
											vertaalTekst("\nIndex",$this->pdf->rapport_taal),
											vertaalTekst("\nOverperf.",$this->pdf->rapport_taal),
											vertaalTekst("Allocatie\nEffect",$this->pdf->rapport_taal),
											vertaalTekst("Selectie\nEffect",$this->pdf->rapport_taal),
											vertaalTekst("Interactie\nEffect",$this->pdf->rapport_taal)));
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array("Periode","Portefeuille","Index","Overperf.","Allocatie Effect","Selectie Effect","Interactie Effect");
   	unset($this->pdf->CellBorders);


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
/*
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
*/
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
   	  $this->pdf->row(array('',$kwartaal,$this->formatGetal($kwartaalWaarden['portBijdrage'],2),$this->formatGetal($kwartaalWaarden['indexBijdrage'],2),
   	  $this->formatGetal($kwartaalWaarden['overperfBijdrage'],2),$this->formatGetal($kwartaalWaarden['allocateEffect'],2),$this->formatGetal($kwartaalWaarden['selectieEffect'],2),
   	  $this->formatGetal($kwartaalWaarden['overperfBijdrage']-($kwartaalWaarden['allocateEffect']+$kwartaalWaarden['selectieEffect']),2)));

   	  $this->pdf->excelData[]=array($kwartaal,$kwartaalWaarden['portBijdrage'],$kwartaalWaarden['indexBijdrage'],$kwartaalWaarden['overperfBijdrage'],
   	  $kwartaalWaarden['allocateEffect'],$kwartaalWaarden['selectieEffect'],($kwartaalWaarden['overperfBijdrage']-($kwartaalWaarden['allocateEffect']+$kwartaalWaarden['selectieEffect'])));
   	}
   	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Totaal',$this->formatGetal($this->historieTotalen['portBijdrage'],2),$this->formatGetal($this->historieTotalen['indexBijdrage'],2),
   	  $this->formatGetal($this->historieTotalen['overperfBijdrage'],2),$this->formatGetal($this->historieTotalen['allocateEffect'],2),$this->formatGetal($this->historieTotalen['selectieEffect'],2),
   	  $this->formatGetal($this->historieTotalen['overperfBijdrage']-($this->historieTotalen['allocateEffect']+$this->historieTotalen['selectieEffect']),2)));
   	$this->pdf->excelData[]=array('Totaal',$this->historieTotalen['portBijdrage'],$this->historieTotalen['indexBijdrage'],$this->historieTotalen['overperfBijdrage'],
   	            $this->historieTotalen['allocateEffect'],$this->historieTotalen['selectieEffect'],($this->historieTotalen['overperfBijdrage']-($this->historieTotalen['allocateEffect']+$this->historieTotalen['selectieEffect'])));


//listarray($att->categorien);
//listarray($this->waarden);
//listarray($kwartalen);
	}

}
?>