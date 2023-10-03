<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L117
{

	function RapportOIB_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
		$this->pdf->rapport_titel = "Analyse portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->verdeling = 'hoofdcategorie';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
		global $__appvar;

		$DB = new DB();
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    $query = "SELECT TijdelijkeRapportage.".$this->verdeling."Omschrijving as Omschrijving, ".
      " TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
      " TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.".$this->verdeling.", ".
      " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
      " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS waardeEUR ".
      " FROM TijdelijkeRapportage ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.".$this->verdeling.", TijdelijkeRapportage.valuta ".
      " ORDER BY TijdelijkeRapportage.".$this->verdeling."Volgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";
    $DB->SQL($query);
    $DB->Query();
    
    $dataRegels=array();
    $dataRegelsValuta=array();
    $categorieGrafiek=array();
    while($categorien = $DB->NextRecord())
    {
      $dataRegels[$categorien['Omschrijving']]['waardeEUR']+=$categorien['waardeEUR'];
      $categorieGrafiek[$categorien['Omschrijving']]['percentage']+=$categorien['waardeEUR']/$totaalWaarde*100;;
      $categorieGrafiek[$categorien['Omschrijving']]['kleur']=array($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);//array($allekleuren['OIB'][$categorien[$this->verdeling]]['R']['value'],$allekleuren['OIB'][$categorien[$this->verdeling]]['G']['value'],$allekleuren['OIB'][$categorien[$this->verdeling]]['B']['value']);
      $dataRegelsValuta[$categorien['valuta']]+=$categorien['waardeEUR']/$totaalWaarde*100;
    }
    arsort($dataRegelsValuta);
    foreach($dataRegelsValuta as $valuta=>$percentage)
    {
      $valutaKleuren[$valuta]=array($allekleuren['OIV'][$valuta]['R']['value'],$allekleuren['OIV'][$valuta]['G']['value'],$allekleuren['OIV'][$valuta]['B']['value']);
    }


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $poly=array($this->pdf->marge,25,
      $this->pdf->marge+85,25,
      $this->pdf->marge+85,50,
      $this->pdf->marge+80,55,
      $this->pdf->marge,55);
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(85));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(26);

    if( $this->pdf->rapport_taal == 2 ) {
      $this->pdf->Row(array(vertaalTekst("This overview provides the current allocation of your portfolio across the equity, fices income, alternative investment and money market asset classes.", $this->pdf->rapport_taal)));
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $this->pdf->Row(array(vertaalTekst("Ce tableau illustre l'allocation actuelle de votre portefeuille par classes d'actifs.", $this->pdf->rapport_taal)));
    } else {
      $this->pdf->Row(array(vertaalTekst("In dit overzicht vindt u de huidige verdeling van uw vermogen over de vermogenscategorieën aandelen, obligaties, alternatieve beleggingen en liquiditeiten.
    
Onderaan geven we een grafische voorstelling van de vermogenscategorieën alsook de valutaverdeling van uw portefeuille.", $this->pdf->rapport_taal)));
    }

    $this->pdf->setY(25);
    $this->pdf->fillCell=array(0,1,1,1);
    $this->pdf->setAligns(array('L','L','R','R'));
    $this->pdf->SetWidths(array(95,115,40,30));
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=6;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->Row(array('','','',vertaalTekst('Portefeuille', $this->pdf->rapport_taal)));
    $this->pdf->Row(array('','','',''));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','',vertaalTekst('Waarde', $this->pdf->rapport_taal),vertaalTekst('Weging', $this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','','',''));
    unset($this->pdf->fillCell);
    $this->pdf->SetDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    $this->pdf->CellBorders=array('','U','U','U','U');
    $totalen=array();
    foreach($dataRegels as $omschrijving=>$regel)
    {
      $weging=$regel['waardeEUR']/$totaalWaarde*100;
      $this->pdf->Row(array('',vertaalTekst($omschrijving, $this->pdf->rapport_taal),$this->formatGetal($regel['waardeEUR'],2),$this->formatGetal($weging,2).'%'));
      $totalen['waarde']+=$regel['waardeEUR'];
      $totalen['weging']+=$weging;
    }
    $this->pdf->SetFillColor($this->pdf->rapport_donkergrijs[0],$this->pdf->rapport_donkergrijs[1],$this->pdf->rapport_donkergrijs[2]);
    $this->pdf->fillCell=array(0,1,1,1);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',vertaalTekst('Totaal', $this->pdf->rapport_taal),$this->formatGetal($totalen['waarde'],2),$this->formatGetal($totalen['weging'],2).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    
    $this->pdf->setXY(95+$this->pdf->marge,170);
    $this->VBarDiagram(75,60, $categorieGrafiek,vertaalTekst('Samenvatting vermogen', $this->pdf->rapport_taal));
    
    $this->pdf->setXY($this->pdf->w-105,100);
    $hoogte=60;
    $yCorrectie=($hoogte/2)-(count($dataRegelsValuta)*3)/2;
    PieChart_L117($this->pdf,$hoogte,$hoogte,$dataRegelsValuta,'%l - %p',array_values($valutaKleuren),vertaalTekst('Valuta verdeling', $this->pdf->rapport_taal),array($this->pdf->getX()+$hoogte+5,$this->pdf->getY()+$yCorrectie+5));
    
    unset($this->pdf->fillCell);
    unset($this->pdf->CellBorders);
  }
  
  
  function VBarDiagram($w, $h, $data, $titel)
  {
    $grafiekPunt = array();
  
    $grafiek=array();
    $grafiekNegatief=array();
    $minEnMax=array();
    $omschrijving=array();
    $kleuren=array();
    $i=0;
    foreach($data as $categorie=>$waarden)
    {
      $minEnMax[]=$waarden['percentage'];
      if($waarden['percentage']>=0)
      {
        $grafiek[$i] = $waarden;
        $grafiekNegatief[$i]=0;
      }
      else
      {
        $grafiekNegatief[$i] = $waarden;
      }
      $omschrijving[$i]=$categorie;
      $kleuren[$i]=$waarden['kleur'];
      $i++;
    }
    
    
    $maxVal=max($minEnMax);
    $minVal=min($minEnMax);
    $numBars = count($data);
    $color=array(155,155,155);
    
    
    if($maxVal <= 10)
      $maxVal=10;
    elseif($maxVal < 20)
      $maxVal=20;
    elseif($maxVal < 50)
      $maxVal=50;
    elseif($maxVal < 75)
      $maxVal=75;
    elseif($maxVal < 100)
      $maxVal=100;
    elseif($maxVal < 150)
      $maxVal=150;
    elseif($maxVal < 200)
      $maxVal=200;
    
    if($minVal >= 0)
      $minVal = 0;
    elseif($minVal > -10)
      $minVal=-10;
    elseif($minVal > -20)
      $minVal=-20;
    elseif($minVal > -50)
      $minVal=-50;
    elseif($minVal > -100)
      $minVal=-100;
    elseif($minVal > -150)
      $minVal=-150;
    elseif($minVal > -200)
      $minVal=-200;
 // echo "$minVal $maxVal <br>\n";exit;
  
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
  
    $this->pdf->setXY($XPage,$YPage-$h-10);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,4,$titel,0,1,'L');
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + 7 ;
    $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda
    
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
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
  
    $this->pdf->Line($XstartGrafiek, $YstartGrafiek, $XstartGrafiek ,$top,array('dash' => 0,'color'=>array(0,0,0)));
    for($i=$nulpunt; round($i)<= round($bodem); $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-10, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1,0)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= round($top); $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 0,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-10, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte,0)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / ($this->pdf->NbVal + 0.5));
    
    $eBaton = ($vBar * 0.5);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    $legendaPrinted=array();
    
    foreach ($grafiek as $index=>$data)
    {
      $categorieNaam=$omschrijving[$index];
    //  foreach($data as $categorie=>$val)
   //   {
        if(!isset($YstartGrafiekLast[$categorieNaam]))
          $YstartGrafiekLast[$categorieNaam] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$categorieNaam] + $nulYpos ;
        $hval = ($data['percentage'] * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$kleuren[$index]);
        $YstartGrafiekLast[$categorieNaam] = $YstartGrafiekLast[$categorieNaam]+$hval;
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($data['percentage'],1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        
        if($legendaPrinted[$categorieNaam] != 1)
        {
          $this->pdf->SetXY($xval,$YstartGrafiek+4);
          $this->pdf->Cell($eBaton,0,vertaalTekst($categorieNaam,$this->pdf->rapport_taal),0,0,'C');
        }
        
        if($grafiekPunt[$categorie][$categorieNaam])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$categorieNaam] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
          if(isset($lastX))
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$categorieNaam] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$categorieNaam] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$categorieNaam] = 1;
    //  }
      
      $i++;
    }
    

    $unsetNegatief=true;
    foreach($grafiekNegatief as $i=>$waarde)
    {
      if(is_array($waarde))
        $unsetNegatief=false;
    }
    
    if($unsetNegatief==true)
      $grafiekNegatief=array();
  
    $i=0;
    $YstartGrafiekLast=array();
  
    foreach ($grafiekNegatief as $index=>$data)
    {
      $categorieNaam=$omschrijving[$index];
      //foreach($data as $categorie=>$val)
     // {
        if(!isset($YstartGrafiekLast[$categorieNaam]))
          $YstartGrafiekLast[$categorieNaam] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$categorieNaam] + $nulYpos ;
        $hval = ($data['percentage'] * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$kleuren[$index]);
        $YstartGrafiekLast[$categorieNaam] = $YstartGrafiekLast[$categorieNaam]+$hval;
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($data['percentage'],1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        
        if($legendaPrinted[$categorieNaam] != 1)
        {
          $this->pdf->SetXY($xval,$YstartGrafiek+4);
          $this->pdf->Cell($eBaton,0,$categorieNaam,0,0,'C');
        }
        //$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
        
        if($grafiekPunt[$categorie][$categorieNaam])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$categorieNaam] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$categorieNaam] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$categorieNaam] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$categorieNaam] = 1;
     // }
      $i++;
    }
  
    
    $this->pdf->Rect($XstartGrafiek+$w/2-8, $YPage+7.25 , 1.5, 1.5, 'F',null,$this->pdf->rapport_donkergroen);
    $this->pdf->setXY($XstartGrafiek,$YPage+6);
    $this->pdf->Cell($w,4, vertaalTekst('Portefeuille', $this->pdf->rapport_taal),0,0,'C');
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
 
}
?>