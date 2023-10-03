<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_123/RapportOIS_L123.php");

//ini_set('max_execution_time',60);
class RapportVOLK_L123
{

	function RapportVOLK_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->ois = new RapportOIS_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht obligaties";
    $this->ois->filterCategorie='H-FixInc';
    $this->ois->filterVariabele='hoofdcategorie';
    $this->ois->selectVariabele='beleggingscategorie';

    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;

	}
 

	function writeRapport()
	{
		global $__appvar;
		$this->ois->writeRapport();

    $this->toonGrafiek();
  
  }


  function toonGrafiek ()
  {
    if( ($this->pdf->GetY() + 100) > $this->pdf->PageBreakTrigger) {
      $this->pdf->AddPage($this->pdf->CurOrientation);
    }

    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);


    $categorieChartData = array();
    $categorieKleuren = array();
    foreach ( $this->ois->fondsData as $fondsData ) {
      $categorieChartData[$fondsData['Omschrijving']] += $fondsData['actuelePortefeuilleWaardeEuro'];
      $categorieKleuren[$fondsData['beleggingscategorie']]=array($allekleuren['OIB'][$fondsData['beleggingscategorie']]['R']['value'],$allekleuren['OIB'][$fondsData['beleggingscategorie']]['G']['value'],$allekleuren['OIB'][$fondsData['beleggingscategorie']]['B']['value']);
    }
    ksort($categorieChartData);

    foreach ( $categorieChartData as $key => $value) {
      $categorieChartData[$key] = $value/$this->ois->totaalWaarde*100;
    }

    $this->pdf->ln(5);
    $thisX = $this->pdf->getX();
    $thisY = $this->pdf->getY();
    $hoogte=40;
    $yCorrectie=($hoogte/2)-(count($categorieChartData)*3)/2;
    PieChart_L123($this->pdf,$hoogte,$hoogte,$categorieChartData,'%l - %p',array_values($categorieKleuren),vertaalTekst('Categorie', $this->pdf->rapport_taal),array($this->pdf->getX()+$hoogte+5,$this->pdf->getY()+$yCorrectie+5),true);

    $rating = new Rating();
    $ratings = $rating->getList('', 'rating', 'Afdrukvolgorde', 'Order by `Afdrukvolgorde`');

    $ratingChartData = array();
    $ratingKleuren = array();
    foreach ( $this->ois->fondsData as $fondsData ) {
      if ( empty($fondsData['fondsRating']) ) {
        $fondsData['fondsRating'] = 'Geen rating';
      }
      $ratingChartData[$fondsData['fondsRating']] += $fondsData['actuelePortefeuilleWaardeEuro'];
      $ratingKleuren[$fondsData['fondsRating']]=array($allekleuren['Rating'][$fondsData['fondsRating']]['R']['value'],$allekleuren['Rating'][$fondsData['fondsRating']]['G']['value'],$allekleuren['Rating'][$fondsData['fondsRating']]['B']['value']);
    }

    $ratingChartDataSorted = array();
    foreach ( $ratings as $key => $value) {
      if ( isset ($ratingChartData[$key]) ) {
        $ratingChartDataSorted[$key] = $ratingChartData[$key]/$this->ois->totaalWaarde*100;
      }
    }

    $this->pdf->setXY($thisX+100,$thisY);
    $yCorrectie=($hoogte/2)-(count($ratingChartDataSorted)*3)/2;
    PieChart_L123($this->pdf,$hoogte,$hoogte,$ratingChartDataSorted,'%l - %p',array_values($ratingKleuren),vertaalTekst('Rating', $this->pdf->rapport_taal),array($this->pdf->getX()+$hoogte+5,$this->pdf->getY()+$yCorrectie+5),true);




//    debug($this->ois->verdeling);
//    debug($this->ois->fondsData);
  //  debug($this->ois->oblData);
////    $dataRegelsValuta[$categorien['valuta']]+=$categorien['waardeEUR']/$this->oistotaalWaarde*100;
//    $ratingChartData = array();
//    $ratingKleuren = array();
//    foreach ( $this->ois->fondsData as $fondsData ) {
//      if ( empty($fondsData['fondsRating']) ) {
//        $fondsData['fondsRating'] = 'Geen rating';
//      }
//      $ratingChartData[$fondsData['fondsRating']] += $fondsData['actuelePortefeuilleWaardeEuro'];
//      $ratingKleuren[$fondsData['fondsRating']]=array($allekleuren['Rating'][$fondsData['fondsRating']]['R']['value'],$allekleuren['Rating'][$fondsData['fondsRating']]['G']['value'],$allekleuren['Rating'][$fondsData['fondsRating']]['B']['value']);
//    }
//    ksort($ratingChartData);
//
//    foreach ( $ratingChartData as $key => $value) {
//      $ratingChartData[$key] = $value/$this->ois->totaalWaarde*100;
//    }
//
    $durationChart=array('0-1'=>0,'1-3'=>0,'3-7'=>0,'7-12'=>0,'>12'=>0,'overig'=>0);
    $totaalOverig=100;
    foreach ($this->ois->oblData as $dataRegel)
    {
      $aandeel=$dataRegel['aandeelCategorie']*100;
      $totaalOverig-=$aandeel;
      $duration=$dataRegel['modifiedDuration'];
      if($duration<>0)
      {
      if($duration<1)
        $durationChart['0-1']+=$aandeel;
      elseif($duration<3)
        $durationChart['1-3']+=$aandeel;
      elseif($duration<7)
        $durationChart['3-7']+=$aandeel;
      elseif($duration<12)
        $durationChart['7-12']+=$aandeel;
      else
        $durationChart['>12']+=$aandeel;
      }
      else
      {
        $durationChart['overig']+=$aandeel;
      }
    }
    $durationChart['overig']+=$totaalOverig;
    
    foreach($allekleuren['OIB'] as $kleur)
      $kleuren[]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
    $durationChartFiltered=array();
    $i=0;
    foreach($durationChart as $key=>$value)
    {
      if($value<>0)
      {
        $durationChartKleuren[] = $kleuren[$i];
        $durationChartFiltered[$key]=$value;
        $i++;
      }
      else
      {
        unset($durationChart[$key]);
      }
    }
    $this->pdf->setXY($thisX+200,$thisY);
    $yCorrectie=($hoogte/2)-(count($ratingChartData)*3)/2;
    PieChart_L123($this->pdf,$hoogte,$hoogte,$durationChartFiltered,'%l - %p',array_values($durationChartKleuren),vertaalTekst('Modified duration', $this->pdf->rapport_taal),array($this->pdf->getX()+$hoogte+5,$this->pdf->getY()+$yCorrectie+5),true);



  }
  
 
}
?>