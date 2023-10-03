<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/10/23 13:34:01 $
File Versie					: $Revision: 1.8 $

$Log: RapportOIB_L54.php,v $
Revision 1.8  2019/10/23 13:34:01  rvv
*** empty log message ***

Revision 1.7  2019/10/20 16:48:17  rvv
*** empty log message ***

Revision 1.6  2019/10/19 16:45:25  rvv
*** empty log message ***

Revision 1.5  2019/10/13 09:30:54  rvv
*** empty log message ***

Revision 1.4  2019/10/11 17:40:07  rvv
*** empty log message ***

Revision 1.3  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.2  2018/03/14 17:17:41  rvv
*** empty log message ***

Revision 1.1  2014/03/02 10:26:23  rvv
*** empty log message ***

Revision 1.1  2014/01/26 15:08:14  rvv
*** empty log message ***

Revision 1.3  2012/06/23 15:20:24  rvv
*** empty log message ***

Revision 1.2  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L54
{
	function RapportOIB_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->pdf->underlinePercentage=0.8;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}


	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(40,35,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,35,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');


		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		//getTypeGrafiekData($object,$type,$extraWhere='',$items=array())
		getTypeGrafiekData($this,'Beleggingscategorie');
		getTypeGrafiekData($this,'Valuta');
		//listarray($this->pdf->grafiekData);
		//listarray($this->pdf->veldOmschrijvingen);

		$this->pdf->setY(35);
		$this->pdf->setWidths(array(160,35,35,25,25));
		$this->pdf->setAligns(array('L','L','L','R','R'));
  	$this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->fillCell = array(0,1,1,1,1);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Valuta','Beleggingscategorie','in EUR','in %'));
    unset($this->pdf->fillCell);
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
  	foreach ($this->pdf->grafiekData['Valuta']['port']['waarde'] as $valuta=>$waarde)
		{
		  $valData=array();
		  $query="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving,SUM(actuelePortefeuilleWaardeEuro) as  actuelePortefeuilleWaardeEuro
		          FROM TijdelijkeRapportage
		          WHERE valuta='$valuta' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
		          GROUP BY Beleggingscategorie
		          ORDER BY BeleggingscategorieVolgorde";
		  $DB->SQL($query);
		  $DB->Query();
		  while($data=$DB->nextRecord())
		  {
		    if($data['Beleggingscategorie']=='')
		      $data['Beleggingscategorie']="Overige";
		    if($data['BeleggingscategorieOmschrijving']=='')
		      $data['BeleggingscategorieOmschrijving']="Overige";
         $valData[]=$data;
		  }


 		  foreach ($valData as $row=>$data)
		  {
		    if($row==0)
		    {
		      $y=$this->pdf->getY();
		      $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
		      $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Valuta'][$valuta]));
		      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		      $this->pdf->setY($y);
		    }

		    $this->pdf->row(array('','',
		                        $data['BeleggingscategorieOmschrijving'],
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100,1)));

         if($row==(count($valData)-1))
         {
           $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
           $this->pdf->CellBorders = array('','','','TS');
		  		 $this->pdf->row(array('','','',$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Valuta']['port']['procent'][$valuta]*100,1)));
		  		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  		 $this->pdf->CellBorders = array();
		  		 $this->pdf->ln(2);
         }
		  }
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Valuta']['port']['procent'][$valuta];
		}

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','','',array('TS','UU'));
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();


		$this->pdf->setXY(23,130);
	  //PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Valuta']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur']);


	  $this->pdf->setY(35);
		$this->pdf->setWidths(array(5,35,35,25,25));
		$this->pdf->setAligns(array('L','L','L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->fillCell = array(0,1,1,1,1);
		$this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->row(array('','Beleggingscategorie','Valutasoort','in EUR','in %'));
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    unset($this->pdf->fillCell);
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;

    //$this->pdf->veldOmschrijvingen['Beleggingscategorie']['geenWaarden']="Liquiditeiten";
   	foreach ($this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'] as $categorie=>$waarde)
		{
		 // if($categorie=='geenWaarden')
		//    $realCat='';
		//  else
		//    $realCat=$categorie;

		  $catData=array();
		  $query="SELECT valuta,valutaOmschrijving,SUM(actuelePortefeuilleWaardeEuro) as  actuelePortefeuilleWaardeEuro
		          FROM TijdelijkeRapportage
		          WHERE Beleggingscategorie='$categorie' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
		          GROUP BY valuta
		          ORDER BY valutaVolgorde";
		  $DB->SQL($query);
		  $DB->Query();
		  while($data=$DB->nextRecord())
         $catData[]=$data;

      if(count($catData)==0)
        $catData[]=array($categorie);

		  foreach ($catData as $row=>$data)
		  {
		    if($row==0)
		    {
		      $y=$this->pdf->getY();
		      $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
		      $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorie]));
		      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		      $this->pdf->setY($y);
		    }

		    $this->pdf->row(array('','',
		                        $data['valutaOmschrijving'],
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
		                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100,1)));

         if($row==(count($catData)-1))
         {
           $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
           $this->pdf->CellBorders = array('','','','TS');
		  		 $this->pdf->row(array('','','',$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie]*100,1)));
		  		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  		 $this->pdf->CellBorders = array();
		  		 $this->pdf->ln(2);
         }
		  }
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie];
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','','',array('TS','UU'));
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();

    $this->pdf->setXY(170,130);
	  //PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Beleggingscategorie']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
    $this->pdf->ln(8);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


/*
if(isset($this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige']))
{
  $this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Liquiditeiten']=$this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige'];
  unset($this->pdf->grafiekData['Beleggingscategorie']['grafiek']['Overige']);
}
*/



//print_r($grafiekData);exit;
foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
{
  //if($waarde<0)
//		$waarde=abs($waarde);
  $grafiekData['OIB']['Percentage'][$omschrijving]=$waarde;
}

foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
{
	//if($waarde<0)
//		$waarde=abs($waarde);
  $grafiekData['OIV']['Percentage'][$omschrijving]=$waarde;
}

    $headerHeight=30;
    $i=0;
    $width=155;
    $vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
    $chartsize=55;
      $ystart=120;
		$extraBarW=25;
    $this->pdf->setXY($this->pdf->marge+7+$width*$i , $ystart);
    $legendaStart=array($this->pdf->marge+10+$width*$i+$chartsize,$ystart+20);
		if(min($grafiekData['OIB']['Percentage']) < 0)
		{
			$this->pdf->Rect($this->pdf->marge+7+$width*$i , $ystart-3, 110, 71);
			$this->BarDiagram($chartsize + $extraBarW, $chartsize, $grafiekData['OIB']['Percentage'], '%l', $this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'], vertaalTekst('Beleggingscategorie', $this->pdf->rapport_taal), $legendaStart);
		}
		else
		{
			PieChart_L54($this->pdf, $chartsize, $vwh, $grafiekData['OIB']['Percentage'], '%l', $this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'], vertaalTekst('Beleggingscategorie', $this->pdf->rapport_taal), $legendaStart);
		}
    $i++;
    $this->pdf->setXY($this->pdf->marge+7+$width*$i , $ystart);
    $legendaStart=array($this->pdf->marge+10+$width*$i+$chartsize,$ystart+20);
		if(min($grafiekData['OIV']['Percentage']) < 0)
		{
			//$this->pdf->setXY($this->pdf->marge+17+$width*$i , $ystart);
			$this->pdf->Rect($this->pdf->marge+7+$width*$i , $ystart-3, 110, 71);
			$this->BarDiagram($chartsize + $extraBarW, $chartsize, $grafiekData['OIV']['Percentage'], '%l', $this->pdf->grafiekData['Valuta']['grafiekKleur'], vertaalTekst('Valuta', $this->pdf->rapport_taal), $legendaStart);
		}
		else
		{
			PieChart_L54($this->pdf, $chartsize, $vwh, $grafiekData['OIV']['Percentage'], '%l', $this->pdf->grafiekData['Valuta']['grafiekKleur'], vertaalTekst('Valuta', $this->pdf->rapport_taal), $legendaStart);
		}
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function SetLegends2($data, $format)
	{
		$this->pdf->legends=array();
		$this->pdf->wLegend=0;

		$this->pdf->sum=array_sum($data);

		$this->pdf->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			//$p=sprintf('%.1f',$val/$this->sum*100).'%';
			if($val <> 0)
			{
				$p=sprintf('%.1f',$val).'%';
				$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			}
			else
				$legend='';
			$this->pdf->legends[]=$legend;
			$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
		}
	}

	function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
	{

		$this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
		$this->SetLegends2($data,$format);


		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$nbDiv=5;
		$legendWidth=35;
		$YDiag = $YPage+30-((count($data)*5)/2);
		$XDiag = $XPage +  $legendWidth;
		$lDiag = floor($w - $legendWidth);
		if(!isset($color))
			$color=array(155,155,155);

   	$maxVal = max($data)*1.1;
		$minVal = min($data)*1.1;
		if($minVal > 0)
			$minVal=0;
		$maxVal=ceil($maxVal/10)*10;

		$offset=$minVal;
		$valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
		$bandBreedte = $valIndRepere * $nbDiv;
		$unit = $lDiag / $bandBreedte;
		$hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));

		//echo "$hBar <br>\n";
		$eBaton = floor($hBar * 80 / 100);
		$this->pdf->SetLineWidth(0.2);
		//$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
		$nullijn=$XDiag - ($offset * $unit);

		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
		$i=0;
		$this->pdf->setXY($XPage+3,$YPage);
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.5);
		$this->pdf->Cell($w,4,$titel,0,1,'L');
		$this->pdf->SetFont($this->pdf->rapport_font, '', 7);
		foreach($data as $key=>$val)
		{
			$this->pdf->SetFillColor($colorArray[$i][0],$colorArray[$i][1],$colorArray[$i][2]);
			$xval = $nullijn;
			$lval = ($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$this->pdf->Rect($xval, $yval, $lval, $hval, 'F');
			$this->pdf->SetXY($XPage, $yval);
			$this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
			$i++;
		}
	}
}
?>