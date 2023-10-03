<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/24 13:12:12 $
File Versie					: $Revision: 1.3 $

$Log: RapportRISK_L99.php,v $


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening2.php");

class RapportRISK_L99
{
	function RapportRISK_L99($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Rendement en risico karakteristieken";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();
    
    $DB = new DB();
    $query="SELECT SpecifiekeIndex,Omschrijving FROM Portefeuilles JOIN Fondsen ON Portefeuilles.SpecifiekeIndex=Fondsen.Fonds 
            WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $this->index=$DB->lookupRecord();
  
  
    $grafiekData=array();
    $stdev=new rapportSDberekening2($this->portefeuille,$this->rapportageDatum);
    $stdev->addReeks('totaal');
    $stdev->addReeks('benchmarkTot',$this->index['SpecifiekeIndex']);
    $stdev->addReeks('afm');
    $stdev->berekenWaarden();

    $riskData=$stdev->riskAnalyze('totaal','benchmarkTot');
    $this->rechtsOnder($riskData);
    $this->addStdevGrafieken($stdev);
    $this->addPerfGrafiek($stdev);
    $this->indexVergelijking();

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();


	}
  
  function getKleuren()
  {
    $db=new DB();
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }
  }


function rechtsOnder($riskData)
{
  global $__appvar;
  $DB=new DB();
  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
    "FROM TijdelijkeRapportage WHERE ".
    " rapportageDatum ='".$this->rapportageDatum."' AND ".
    " portefeuille = '".$this->portefeuille."' "
    .$__appvar['TijdelijkeRapportageMaakUniek'];
  debugSpecial($query,__FILE__,__LINE__);
  $DB->SQL($query);
  $DB->Query();
  $totaalWaarde = $DB->nextRecord();

    $this->pdf->Ln(2);
    $this->pdf->setXY($this->pdf->marge,130);
    $this->pdf->SetWidths(array(180,55,20));
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%',$body));
    $this->pdf->ln(2);   
  	$this->pdf->row(array('','Value at Risk',"€ ".$this->formatGetal((100-$riskData['valueAtRisk'])/100*$totaalWaarde['totaal'],0).'',''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->ln(2);
  	$this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',''));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));  
  
}


function addPerfGrafiek($stdev)
{
    $portIndex=1;
    $indexIndex=1;
    foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
    {
      $benchmarkData=$stdev->reeksen['benchmarkTot'][$datum];
      $juldate=db2jul($datum);
      $portIndex=(1+$perfData['perf']/100)*$portIndex;
      $indexIndex=(1+$benchmarkData['perf']/100)*$indexIndex;
      $perfGrafiek['portefeuille'][]=($portIndex-1)*100;
      $perfGrafiek['specifiekeIndex'][]=($indexIndex-1)*100;
      $perfGrafiek['datum'][]= date("M y",$juldate);
    }
   
    $perfGrafiek['legenda']=array('Portefeuille',$this->index['Omschrijving']);
  //  $this->pdf->setXY(20,120);
    $portKleur=array(0,55,104);
    $indexKleur=array(113,98,88);
    $perfGrafiek['titel']='Portefeuille rendement';
  $this->pdf->setXY(20,37);
  $this->LineDiagram(120, 55, $perfGrafiek,array($portKleur,$indexKleur),0,0,6,5,true);//50
}

function addStdevGrafieken($stdev)
{
    foreach($stdev->standaardDeviatieReeksen['totaal'] as $datum=>$devData)
    {
      $benchmarkData=$stdev->standaardDeviatieReeksen['benchmarkTot'][$datum];
      $afmData=$stdev->standaardDeviatieReeksen['afm'][$datum];
      
      $grafiekData['totaal']['datum'][]= date("M y",db2jul($datum));
      $grafiekData['totaal']['portefeuille'][]= $devData['stdev'];
      $grafiekData['totaal']['specifiekeIndex'][]= $benchmarkData['stdev'];
      $grafiekData['totaal']['afm'][]= $afmData['stdev'];

      
      $grafiekData['afm']['datum'][]= date("M y",db2jul($datum));
      $grafiekData['afm']['portefeuille'][]= $afmData['stdev'];
    }
    $grafiekData['totaal']['titel']='Standaarddeviatie';
    $grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';
    
  //  $grafiekData['totaal']['legenda']=array('Portefeuille',$this->index['Omschrijving']);
  $grafiekData['totaal']['legenda']=array('Portefeuille',$this->index['Omschrijving'],'AFM');


    $portKleur=array(0,55,104);
    $indexKleur=array(113,98,88);
    $afmkleur=array(150,150,150);
//    $this->LineDiagram(120, 55, $grafiekData['totaal'],array($portKleur,$indexKleur),0,0,6,5,1);//50
  $this->pdf->setXY(160,37);
    $this->LineDiagram(120, 55, $grafiekData['totaal'],array($portKleur,$indexKleur,$afmkleur),0,0,6,5,false);//50
   //
   // $this->LineDiagram(120, 55, $grafiekData['afm'],array($portKleur,$indexKleur),0,0,6,5,1);//50



}


  function indexVergelijking()
  {
    $DB=new DB();

    $perioden=array('begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    $query="SELECT
Indices.Beursindex,
Indices.specialeIndex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $indices=array();
    while($index = $DB->nextRecord())
      $indices[]=$index;


    $specialeBenchmarks=array();
    $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$this->rapportageDatum);
    $query2 = "SELECT Fondsen.Fonds as SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Fondsen
	             WHERE Fondsen.Fonds  = '". mysql_real_escape_string($SpecifiekeIndexFonds)."' ";
    $DB->SQL($query2);
    $DB->Query();
    $SpecifiekeIndex=$DB->lookupRecord();
    $indices[]=$SpecifiekeIndexFonds;
    $fondsOmschrijvingen=array($SpecifiekeIndexFonds=>$SpecifiekeIndex['Omschrijving']);
  
    foreach ($perioden as $periode=>$datum)
    {
      $indexData[$SpecifiekeIndexFonds]['fondsKoers_'.$periode]=$this->getFondsKoers($SpecifiekeIndexFonds,$datum);
    }
  
    $indexBerekening = new indexHerberekening();
    $fondsWissel='';
    foreach($indices as $index)
    {
      if($index['specialeIndex']==1)
      {
        $specialeBenchmarks[]=$index['Beursindex'];
        $specialeIndexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
          $specialeIndexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
        $specialeIndexData[$index['Beursindex']]['performance'] =     ($specialeIndexData[$index['Beursindex']]['fondsKoers_eind'] - $specialeIndexData[$index['Beursindex']]['fondsKoers_begin']) / ($specialeIndexData[$index['Beursindex']]['fondsKoers_begin']/100 );
      
      }
      else
      {
        $maanden=$indexBerekening->getMaanden(db2jul($perioden['begin']),db2jul($perioden['eind']));
        foreach($maanden as $maand)
        {
          $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$maand['stop']);
          $maandPerf=getFondsPerformance($SpecifiekeIndexFonds,$maand['start'],$maand['stop']);
          $SpecifiekeIndex['performanceJaar']=((1+$SpecifiekeIndex['performanceJaar']/100)*(1+$maandPerf/100)-1)*100;
          if($SpecifiekeIndexFonds<>$SpecifiekeIndex['SpecifiekeIndex'])
          {
            if(!isset($fondsOmschrijvingen[$SpecifiekeIndexFonds]))
            {
              $query2 = "SELECT Fondsen.Fonds as SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Fondsen
	             WHERE Fondsen.Fonds  = '" . mysql_real_escape_string($SpecifiekeIndexFonds) . "' ";
              $DB->SQL($query2);
              $DB->Query();
              $omschrijving = $DB->lookupRecord();
              $fondsOmschrijvingen[$SpecifiekeIndexFonds]=$omschrijving['Omschrijving'];
            }
            $fondsWissel = vertaalTekst("Benchmark is op",$this->pdf->rapport_taal).' '. date('d-m-Y',db2jul($maand['stop'])).' '.vertaalTekst("gewijzigd van",$this->pdf->rapport_taal).' '. $fondsOmschrijvingen[$SpecifiekeIndexFonds].' '.vertaalTekst("naar",$this->pdf->rapport_taal).' '.$SpecifiekeIndex['Omschrijving'];
          }
        }
        $maanden=$indexBerekening->getMaanden(db2jul($perioden['begin']),db2jul($perioden['eind']));
        foreach($maanden as $maand)
        {
          $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$maand['stop']);
          $maandPerf=getFondsPerformance($SpecifiekeIndexFonds,$maand['start'],$maand['stop']);
          $SpecifiekeIndex['performance']=((1+$SpecifiekeIndex['performance']/100)*(1+$maandPerf/100)-1)*100;
        }
      
        $indexData[$SpecifiekeIndexFonds]['Omschrijving']=$fondsOmschrijvingen[$SpecifiekeIndexFonds];
        $indexData[$SpecifiekeIndexFonds]['performance'] =  $SpecifiekeIndex['performance'];
        $indexData[$SpecifiekeIndexFonds]['performanceJan'] =  $SpecifiekeIndex['performanceJaar'];
        $benchmarks[]=$SpecifiekeIndexFonds;
      
        //listarray($indexData);
      //echo $fondsWissel;exit;
      
        /*
        $benchmarks[]=$index['Beursindex'];
        $indexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
          $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
        $indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
   
       $indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
      $indexData[$index['Beursindex']]['performanceJanEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']*$indexData[$index['Beursindex']]['valutaKoers_jan'])/($indexData[$index['Beursindex']]['fondsKoers_jan']*$indexData[$index['Beursindex']]['valutaKoers_jan']/100 );

        */
      }
    }


    $this->pdf->SetY(130);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(10,60,20,20,20));
    $this->pdf->SetAligns(array('L','L','R','R','R'));
    $this->pdf->Rect($this->pdf->marge+10,130,120,count($benchmarks)*4+4+($fondsWissel==''?0:8));
    $this->pdf->row(array("","Vergelijkingsmaatstaven","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    foreach ($benchmarks as $fonds)
    {
      $fondsData=$indexData[$fonds];
      if($fondsData['Omschrijving']=='')
        $this->pdf->row(array(''));
      else
        $this->pdf->row(array('',$fondsData['Omschrijving'],
                          $this->formatGetal($fondsData['fondsKoers_begin'],2),
                          $this->formatGetal($fondsData['fondsKoers_eind'],2),
                          $this->formatGetal($fondsData['performance'],2)."%"));
    }
  
  
    if($fondsWissel<>'')
    {
      $this->pdf->SetWidths(array(10,60+20+20+20));
      $this->pdf->row(array('', $fondsWissel));
      $this->pdf->SetWidths(array(10,60,20,20,20));
    }


    if(count($specialeBenchmarks) > 0)
    {
      $this->pdf->SetY(150);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->SetWidths(array(10,60,20,20,20));
      $this->pdf->SetAligns(array('L','L','R','R','R'));
      $this->pdf->Rect($this->pdf->marge+10,150,120,count($specialeBenchmarks)*4+4);
      $this->pdf->row(array("","Overige marktindices ter informatie","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
      unset($this->pdf->CellBorders);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      foreach ($specialeBenchmarks as $fonds)
      {
        $fondsData=$specialeIndexData[$fonds];
        if($fondsData['Omschrijving']=='')
          $this->pdf->row(array(''));
        else
          $this->pdf->row(array('',$fondsData['Omschrijving'],
                            $this->formatGetal($fondsData['fondsKoers_begin'],2),
                            $this->formatGetal($fondsData['fondsKoers_eind'],2),
                            $this->formatGetal($fondsData['performance'],2)."%"));
      }
    }

  }

  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }

  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$vanafBegin=false)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data2 = $data['afm'];
    $data = $data['portefeuille'];


    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    if(count($data2)>0)
      $bereikdata = array_merge($bereikdata,$data2);

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY()+2;
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    //	$this->pdf->setY($Ypage-3);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color2= $color[2];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);


    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }

    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);



    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;

      if ($i>0 || $vanafBegin==true)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }

      $yval = $yval2;
    }

    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;

        if ($i>0 || $vanafBegin==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }

    if(is_array($data2))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);
      for ($i=0; $i<count($data2); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data2[$i]) * $waardeCorrectie) ;

        if ($i>0 || $vanafBegin==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }

    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
    $aantal=count($legendaItems);
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      elseif($index==1)
        $kleur=$color1;
      else
        $kleur=$color2;
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
      $this->pdf->Cell(0,3,$item);

      if($aantal==3)
        $step+=$this->pdf->GetStringWidth($item)+15;
      else
        $step+=($w/count($legendaItems));
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
 
  
}
?>