<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/02/13 16:42:08 $
 		File Versie					: $Revision: 1.9 $

 		$Log: RapportRISK_L10.php,v $
 		Revision 1.9  2019/02/13 16:42:08  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2019/01/30 16:49:07  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2019/01/30 16:46:57  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/12/03 06:50:50  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/12/01 19:51:30  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/10/07 08:29:03  rvv
 		*** empty log message ***
 		
 		 		
 	

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

//ini_set('max_execution_time',60);
class RapportRISK_L10
{
	function RapportRISK_L10($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Rendement en Risico vergelijking";
		$this->portefeuille = $portefeuille;
    
    if($this->pdf->rapport_datumvanaf<db2jul('2012-01-01'))
    {
      $this->rapportageDatumVanaf ='2012-01-01';
    }
    else
    {
      $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    }
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->excelData[]=array();
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
    $this->pdf->templateVars['RISKPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['RISKPaginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();
    
    $DB = new DB();
    $query="SELECT SpecifiekeIndex,Omschrijving FROM Portefeuilles JOIN Fondsen ON Portefeuilles.SpecifiekeIndex=Fondsen.Fonds 
            WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $this->index=$DB->lookupRecord();

    $benchmarkVerdeling=array();
    if(count($this->pdf->portefeuilles) > 1 && $this->index['SpecifiekeIndex']=='')
    {
  /*
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
        "FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='".$this->rapportageDatum."' AND ".
        " portefeuille = '".$this->portefeuille."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
  
      $DB->SQL($query);
      $DB->Query();
      $totaleWaarde = $DB->nextRecord();
      
      foreach($this->pdf->portefeuilles as $cPortefeuille)
      {
  
        $fondswaarden = berekenPortefeuilleWaarde($cPortefeuille,  $this->rapportageDatum , (substr($this->rapportageDatum, 5, 5) == '01-01')?true:false, 'EUR',  $this->rapportageDatum );
        $portefeuilleTotaal = 0;
        foreach ($fondswaarden as $fondsData)
        {
          $portefeuilleTotaal += $fondsData['actuelePortefeuilleWaardeEuro'];
        }
        $aandeel =  $portefeuilleTotaal / $totaleWaarde['totaal'] *100;
        
        $query = "SELECT SpecifiekeIndex FROM Portefeuilles WHERE Portefeuille='" . $cPortefeuille . "'";
        $DB->SQL($query);
        $DB->Query();
        $index = $DB->lookupRecord();
        if(isset($benchmarkVerdeling[$index['SpecifiekeIndex']]))
          $benchmarkVerdeling[$index['SpecifiekeIndex']] += $aandeel;
        else
          $benchmarkVerdeling[$index['SpecifiekeIndex']] = $aandeel;
        //echo $data['datum']." $cPortefeuille ".$barGraph['Plan'][$data['datum']]." += ".$plan['ProcentRisicoDragend']."*".$aandeel['aandeel'].";<br>\n";
      }
  */
      $benchmarkFonds='';//$benchmarkVerdeling;
      $this->benchmarkTonen=false;
    }
    else
    {
      $benchmarkFonds=$this->index['SpecifiekeIndex'];
      $this->benchmarkTonen=true;
    }

  
    $grafiekData=array();
    $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,1);
    if($stdev->settings['julStartdatum']<db2jul('2012-01-01'))
    {
      $stdev->settings['julStartdatum'] = db2jul('2012-01-01');
      $stdev->settings['Startdatum'] = date('Y-m-d', $stdev->settings['julStartdatum'] );
    }
    
    $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
    if(count($this->pdf->portefeuilles) > 1)
      $stdev->consolidatiePortefeuilles=$this->pdf->portefeuilles;
    if($this->pdf->lastPOST['doorkijk']==1)
    {
      $perioden=$stdev->getPeriodeInstellingen();
      include_once($__appvar["basedir"]."/html/rapport/include/RapportATT_L10.php");
      $att=new RapportATT_L10($this->pdf, $this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
      $reeks=array();
      foreach($perioden as $periode)
      {
       $perf=$att->BerekenMutaties2($periode['start'],$periode['stop'], $this->portefeuille);
        $reeks[$periode['stop']]=array('perf'=>$perf['performance'],'datum'=>$periode['stop']);

      }
      $stdev->reeksen['totaal']=$reeks;
      //listarray($reeks);
    }
    else
      $stdev->addReeks('totaal');

  //  listarray($stdev);exit;
    if($this->benchmarkTonen)
      $stdev->addReeks('benchmarkTot',$benchmarkFonds,false);
    else
    {
      foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
      {
        $stdev->reeksen['benchmarkTot'][$datum]=array('perf'=>0);
      }
    }
   // listarray($stdev);
    $stdev->addReeks('afm');
    $stdev->berekenWaarden();

    $riskData=$stdev->riskAnalyze('totaal','benchmarkTot',true);

    $this->pdf->excelData[]=array('datum','portefeuille %','benchmark %');
    foreach($stdev->indexWaarden as $excelData)
      $this->pdf->excelData[]=array($excelData['datum'],$excelData['performance'],$excelData['specifiekeIndexPerformance']);
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array('Window:',$stdev->settings['SdWaarnemingen']);
    $this->pdf->excelData[]=array('WaarnemingenPerJaar:',$stdev->settings['aantalPerJaar']);
    $this->pdf->excelData[]=array('correctie:',$stdev->settings['correctie']);

    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array('Portefeuille');
    $velden=array('laatsteMeting','standaarddeviatie','standaarddeviatieBenchmark','jaarPerf','jaarPerfBenchmark','valueAtRisk','maxDrawdown','sharpeRatio','trackingError','informatieratio','standaarddeviatieAFM');
    $this->pdf->excelData[]=$velden;
    foreach($riskData as $id=>$regel)
    {
      $tmp=array();
      foreach ($velden as $veld)
      {
        $tmp[]=$regel[$veld];
      }
      $this->pdf->excelData[]=$tmp;
    }

    $this->pdf->excelData[]=array();
    $riskBenchmark=$stdev->riskAnalyze('benchmarkTot','totaal',true);
    $this->pdf->excelData[]=array('Benchmark');
    $this->pdf->excelData[]=$velden;
    foreach($riskBenchmark as $id=>$regel)
    {
      $tmp=array();
      foreach ($velden as $veld)
      {
        $tmp[]=$regel[$veld];
      }
      $this->pdf->excelData[]=$tmp;
    }


    foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
    {
      $grafiekData['sharpe']['datum'][$datum]= date("M y",db2jul($datum));
      $grafiekData['sharpe']['portefeuille'][$datum]=0;
      $grafiekData['sharpe']['specifiekeIndex'][$datum]=0;
    }

    if(is_array($riskData) && count($riskData) > 0)
    {
      $tmp=array();
      foreach($riskData as $data)
      {
        $tmp['portefeuille'][$data['laatsteMeting']]=$data['sharpeRatio'];
      }

      if(count($riskData)==count($riskBenchmark))
      {
        foreach ($riskBenchmark as $data)
        {
          $tmp['specifiekeIndex'][$data['laatsteMeting']] = $data['sharpeRatio'];
        }
      }
      foreach($tmp['portefeuille'] as $datum=>$sharpeRatio)
      {
        $grafiekData['sharpe']['datum'][$datum] = date("M y",db2jul($datum)); ;
        $grafiekData['sharpe']['portefeuille'][$datum] = $sharpeRatio;
        if(isset($tmp['specifiekeIndex'][$datum]))
          $grafiekData['sharpe']['specifiekeIndex'][$datum]=$tmp['specifiekeIndex'][$datum];
      }
      //$riskData=$riskData[(count($riskData)-1)];
    }

    //if(is_array($riskBenchmark) && count($riskBenchmark) > 0)
    //  $riskBenchmark=$riskBenchmark[(count($riskBenchmark)-1)];

    $riskData=$stdev->riskAnalyze('totaal','benchmarkTot',false);
    $riskBenchmark=$stdev->riskAnalyze('benchmarkTot','totaal',false);
    
    
    if(!isset($this->pdf->rapport_grafiek_pcolor))
      $this->pdf->rapport_grafiek_pcolor=array(106,100,122);//array(rand(0,255),rand(0,255),rand(0,255));
    
    if(!isset($this->pdf->rapport_grafiek_icolor))
      $this->pdf->rapport_grafiek_icolor=array(176,160,122);//array(rand(0,255),rand(0,255),rand(0,255));
   $this->rechtsOnder($riskData,$riskBenchmark);
   $this->addStdevGrafieken($stdev);
    $this->addPerfGrafiek($stdev);

    $this->pdf->setXY(160,47);
    $grafiekData['sharpe']['titel']='Sharpe-ratio';
    if($this->benchmarkTonen)
    {
      $grafiekData['sharpe']['legenda'] = array('Portefeuille', 'Benchmark');
    }
    else
    {
      $grafiekData['sharpe']['legenda'] = array('Portefeuille');
    }

    $this->LineDiagram(120, 55, $grafiekData['sharpe'],array($this->pdf->rapport_grafiek_pcolor,$this->pdf->rapport_grafiek_icolor),0,0,6,5,false);//50

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


function rechtsOnder($riskData,$riskBenchmark)
{
  global $__appvar;
  $DB=new DB();
  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
    "FROM TijdelijkeRapportage WHERE ".
    " rapportageDatum ='".$this->rapportageDatum ."' AND ".
    " portefeuille = '". $this->portefeuille ."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
  $DB->SQL($query);
  $DB->Query();
  $totaalWaarde = $DB->nextRecord();
  $totaalWaarde = $totaalWaarde['totaal'];


    $this->pdf->Ln(2);
    $this->pdf->setXY($this->pdf->marge,130);
    $this->pdf->SetWidths(array(165,55,20,20));
    $this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  if($this->benchmarkTonen)
    $this->pdf->row(array('','','Portefeuille','Benchmark'));
  else
    $this->pdf->row(array('','','Portefeuille'));
    $this->pdf->ln(2);
    if($this->benchmarkTonen)
  	  $this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%'));
    else
      $this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%'));
    $this->pdf->ln(2);

  if($riskData['valueAtRisk'] <> 0)
  {
    $riskData['valueAtRisk'] = (100 - $riskData['valueAtRisk']) / 100 * $totaalWaarde;
    $riskBenchmark['valueAtRisk'] = (100 - $riskBenchmark['valueAtRisk']) / 100 * $totaalWaarde;
  }
  if($this->benchmarkTonen)
  {
    $this->pdf->row(array('', 'Value at Risk', '€' . $this->formatGetal($riskData['valueAtRisk'], 0), '€' . $this->formatGetal($riskBenchmark['valueAtRisk'], 0)));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('', 'Maximale terugval', $this->formatGetal($riskData['maxDrawdown'], 1) . '%', $this->formatGetal($riskBenchmark['maxDrawdown'], 1) . '%'));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('', 'Tracking Error', $this->formatGetal($riskData['trackingError'], 1) . '%', ''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('', 'Sharpe ratio', $this->formatGetal($riskData['sharpeRatio'], 1) . '', $this->formatGetal($riskBenchmark['sharpeRatio'], 1)));
    $this->pdf->ln(2);
    $this->pdf->row(array('', 'Informatieratio', $this->formatGetal($riskData['informatieratio'], 1) . '', ''));
  }
  else
  {
    $this->pdf->row(array('', 'Value at Risk', '€' . $this->formatGetal($riskData['valueAtRisk'], 0)));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('', 'Maximale terugval', $this->formatGetal($riskData['maxDrawdown'], 1) . '%'));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('', 'Tracking Error', $this->formatGetal($riskData['trackingError'], 1) . '%'));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1)));
  }
  
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
//listarray($perfGrafiek);
   // $benchmark=$this->index['Omschrijving'];
   // if($benchmark=='')
  if($this->benchmarkTonen)
  {
    $benchmark = 'Benchmark';
    $perfGrafiek['legenda'] = array('Portefeuille', $benchmark);
  }
  else
  {
    $perfGrafiek['legenda'] = array('Portefeuille');
    unset($perfGrafiek['specifiekeIndex']);
  }
    $this->pdf->setXY(20,120);
     $indexKleur=array(176,160,122);
    $perfGrafiek['titel']='Portefeuille rendement';
    $this->LineDiagram(120, 55, $perfGrafiek,array($this->pdf->rapport_grafiek_pcolor,$indexKleur),0,0,6,5,true);//50


}  

function addStdevGrafieken($stdev)
{
  foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
  {
    $grafiekData['totaal']['datum'][$datum]= date("M y",db2jul($datum));
    $grafiekData['totaal']['portefeuille'][$datum]=0;
    $grafiekData['totaal']['specifiekeIndex'][$datum]=0;
    $grafiekData['totaal']['extra'][$datum]=0;

  }
    foreach($stdev->standaardDeviatieReeksen['totaal'] as $datum=>$devData)
    {
      $benchmarkData=$stdev->standaardDeviatieReeksen['benchmarkTot'][$datum];
      $afmData=$stdev->standaardDeviatieReeksen['afm'][$datum];
      
      $grafiekData['totaal']['datum'][$datum]= date("M y",db2jul($datum));
      $grafiekData['totaal']['portefeuille'][$datum]= $devData['stdev'];
      $grafiekData['totaal']['specifiekeIndex'][$datum]= $benchmarkData['stdev'];
      $grafiekData['totaal']['extra'][$datum]= $afmData['stdev'];
    }
    $grafiekData['totaal']['titel']='Standaarddeviatie';
    //$grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';
  
  $afmKleur=array($this->pdf->rapport_logoKleur[0],$this->pdf->rapport_logoKleur[1],$this->pdf->rapport_logoKleur[2]);//array(30,130,50);
  
  if($this->benchmarkTonen)
  {
    $grafiekData['totaal']['legenda'] = array('Portefeuille', 'Benchmark', 'AFM');
    $kleurData=array($this->pdf->rapport_grafiek_pcolor,$this->pdf->rapport_grafiek_icolor,$afmKleur);
  }
  else
  {
    $grafiekData['totaal']['legenda'] = array('Portefeuille', '', 'AFM');
    $kleurData=array($this->pdf->rapport_grafiek_pcolor,array(255,255,255),$afmKleur);
  }
    

    $this->pdf->setXY(20,47);
    $this->LineDiagram(120, 55, $grafiekData['totaal'],$kleurData,0,0,6,5,false);//50
    
  //$this->pdf->setXY(160,47);
  //  $this->LineDiagram(120, 55, $grafiekData['totaal'],array($portKleur,$indexKleur),0,0,6,5,1);//50


}  


  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$eerstePunt=false)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data2 = $data['extra'];
    $data = $data['portefeuille'];


    if(count($data2)>0)
      $bereikdata = array_merge(array_values($data),array_values($data1),array_values($data2));
    elseif(count($data1)>0)
      $bereikdata = array_merge(array_values($data),array_values($data1));
    else
      $bereikdata =  array_values($data);

   // listarray($bereikdata);exit;
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color2= $color[2];
      $color1= $color[1];
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
    if($titel=='Sharpe-ratio')
      $yAs='';
    else
      $yAs=' %';
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      //$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) .$yAs);

      $this->pdf->setXY($XDiag-11, $i-2);
      $this->pdf->cell(10,4,0-($n*$stapgrootte) .$yAs,0,1,'R');
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
      {
        $this->pdf->setXY($XDiag-11, $i-2);
        $this->pdf->cell(10,4,(($n*$stapgrootte) + 0) . $yAs,0,1,'R');

       // $this->pdf->Text($XDiag - 7, $i, ($n * $stapgrootte) + 0 . $yAs);
      }
      $n++;
      if($n >20)
         break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    //for ($i=0; $i<count($data); $i++)
    $i=0;
    $start=false;
    foreach($data as $datum=>$waarde)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$datum],25);
      $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
      
      if ($i>0 && $start==true || $eerstePunt==true)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }
      if($waarde<>0)
        $start=true;

      $yval = $yval2;
      $i++;
    }
    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

     // for ($i=0; $i<count($data1); $i++)
      $i=0;
      $start=false;
      foreach($data1 as $datum=>$waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        
        if ($i>0 && $start==true || $eerstePunt==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        if($waarde<>0)
          $start=true;
        $yval = $yval2;
        $i++;
      }
    }

    if(is_array($data2))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);

      //for ($i=0; $i<count($data2); $i++)
      $i=0;
      $start=false;
      foreach($data2 as $datum=>$waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;

        if ($i>0 && $start==true || $eerstePunt==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        if($waarde<>0)
          $start=true;
        $yval = $yval2;
        $i++;
      }
    }

    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
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
    $step+=($w/3);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
 

}
?>