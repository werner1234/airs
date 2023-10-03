<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/24 06:30:58 $
File Versie					: $Revision: 1.8 $

$Log: RapportRISK_L80.php,v $
Revision 1.8  2020/05/24 06:30:58  rvv
*** empty log message ***

Revision 1.7  2020/05/23 16:39:00  rvv
*** empty log message ***

Revision 1.6  2019/12/01 08:15:05  rvv
*** empty log message ***

Revision 1.5  2019/12/01 07:51:04  rvv
*** empty log message ***

Revision 1.4  2019/07/06 15:43:47  rvv
*** empty log message ***

Revision 1.3  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.2  2019/01/12 17:08:31  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportRISK_L80
{

	function RapportRISK_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting over de categorieën";
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
    
    $query="SELECT Minimum,Maximum,Risicoklasse FROM StandaarddeviatiePerRisicoklasse WHERE Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."'AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $this->standaarddeviatieMarge=$DB->lookupRecord();
   // $this->standaarddeviatieMarge=array('Minimum'=>2,'Maximum'=>10,'Risicoklasse'=>'test');
  
  
    $grafiekData=array();
    $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
    $stdev->addReeks('totaal');
    $stdev->addReeks('benchmark',$this->index['SpecifiekeIndex']);
    $stdev->addReeks('afm');
    $stdev->berekenWaarden();
    
    $riskData=$stdev->riskAnalyze('totaal','benchmark',true);
    $riskBenchmark=$stdev->riskAnalyze('benchmark','totaal',true);
    $riskDataLast=$riskData[count($riskData)-1];

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
        $grafiekData['sharpe']['datum'][] = date("M y",db2jul($datum)); ;
        $grafiekData['sharpe']['portefeuille'][] = $sharpeRatio;
        if(isset($tmp['specifiekeIndex'][$datum]))
          $grafiekData['sharpe']['specifiekeIndex'][]=$tmp['specifiekeIndex'][$datum];
      }
      //$riskData=$riskData[(count($riskData)-1)];
    }

    $this->rechtsOnder($riskDataLast);
    $this->addStdevGrafieken($stdev);
    $this->addPerfGrafiek($stdev);
    
    $this->pdf->setXY(160,40);
    $grafiekData['sharpe']['titel']='Sharpe-ratio';
    $grafiekData['sharpe']['legenda']=array('Portefeuille','Benchmark');
    $kleuren=array(array(74,166,77),array(61,59,56));
    $this->LineDiagram(120, 55, $grafiekData['sharpe'],$kleuren,0,0,6,5,false);//50
    

    
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
  	$this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%'));
    $this->pdf->ln(2);
  	$this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',''));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));
  $this->pdf->ln(2);
  //$this->pdf->row(array('','Value at Risk',$this->formatGetal((100-$riskData['valueAtRisk'])/100*$totaalWaarde['totaal'],0).'',''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
  
  
  $query="SELECT verwachtRendement FROM Risicoklassen
    WHERE Vermogensbeheerder='".$this->settings['Vermogensbeheerder']."' AND Risicoklasse='".$this->settings['Risicoklasse']."'";
  $db=new DB();
  $db->SQL($query);
  $verwachtRendement=$db->lookupRecord();

  $VaR=($verwachtRendement['verwachtRendement']-(1*$riskData['standaarddeviatieAFM'])*-0.01);
  $this->pdf->row(array('','Value at Risk (68% zekerheid)',$this->formatGetal($VaR*$totaalWaarde['totaal'],0).'',''));
  $this->pdf->ln(2);
  $VaR=($verwachtRendement['verwachtRendement']-(2*$riskData['standaarddeviatieAFM'])*-0.01);
  $this->pdf->row(array('','Value at Risk (95% zekerheid)',$this->formatGetal($VaR*$totaalWaarde['totaal'],0).'',''));
  $this->pdf->ln(2);
  $VaR=($verwachtRendement['verwachtRendement']-(3*$riskData['standaarddeviatieAFM'])*-0.01);
  $this->pdf->row(array('','Value at Risk (99% zekerheid)',$this->formatGetal($VaR*$totaalWaarde['totaal'],0).'',''));
  
}


function addPerfGrafiek($stdev)
{
    $portIndex=1;
    $indexIndex=1;
    foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
    {
      $benchmarkData=$stdev->reeksen['benchmark'][$datum];
      $juldate=db2jul($datum);
      $portIndex=(1+$perfData['perf']/100)*$portIndex;
      $indexIndex=(1+$benchmarkData['perf']/100)*$indexIndex;
      $perfGrafiek['portefeuille'][]=($portIndex-1)*100;
      $perfGrafiek['specifiekeIndex'][]=($indexIndex-1)*100;
      $perfGrafiek['datum'][]= date("M y",$juldate);
    }
   
    $perfGrafiek['legenda']=array('Portefeuille',$this->index['Omschrijving']);
    $this->pdf->setXY(20,120);
    $portKleur=array($this->pdf->rapport_kop2_fontcolor['r'],$this->pdf->rapport_kop2_fontcolor['g'],$this->pdf->rapport_kop2_fontcolor['b']);
    $indexKleur=array(100,100,100);//$this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $perfGrafiek['titel']='Portefeuille rendement';
  $kleuren=array(array(74,166,77),array(61,59,56));
    $this->LineDiagram(120, 55, $perfGrafiek,$kleuren,0,0,6,5);//50


}  

function addStdevGrafieken($stdev)
{
    foreach($stdev->standaardDeviatieReeksen['totaal'] as $datum=>$devData)
    {
      $benchmarkData=$stdev->standaardDeviatieReeksen['benchmark'][$datum];
      $afmData=$stdev->standaardDeviatieReeksen['afm'][$datum];
      
      $grafiekData['totaal']['datum'][]= date("M y",db2jul($datum));
      $grafiekData['totaal']['portefeuille'][]= $devData['stdev'];
      $grafiekData['totaal']['specifiekeIndex'][]= $benchmarkData['stdev'];
      if($afmData['stdev']==0)
        $grafiekData['totaal']['extra'][]='F';
      else
        $grafiekData['totaal']['extra'][]= $afmData['stdev'];
      
     // $grafiekData['afm']['datum'][]= date("M y",db2jul($datum));
     // $grafiekData['afm']['portefeuille'][]= $afmData['stdev'];
    }
    $grafiekData['totaal']['titel']='Standaarddeviatie';
   // $grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';
    
    $grafiekData['totaal']['legenda']=array('Portefeuille',$this->index['Omschrijving'],'AFM');

    $this->pdf->setXY(20,40);

    
    $portKleur=array($this->pdf->rapport_kop2_fontcolor['r'],$this->pdf->rapport_kop2_fontcolor['g'],$this->pdf->rapport_kop2_fontcolor['b']);

  $kleuren=array(array(74,166,77),array(61,59,56),array(130,130,215));
   // $indexKleur=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->LineDiagram(120, 55, $grafiekData['totaal'],$kleuren,0,0,6,5,true);//50
   // $this->pdf->setXY(160,40);
   // $this->LineDiagram(120, 55, $grafiekData['afm'],array($portKleur,$indexKleur),0,0,6,5,1);//50
    


}  


  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4, $afmMinMax=false)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data2 = $data['extra'];
    $data = $data['portefeuille'];
  
    if(count($data1)>0 && count($data2)>0)
      $bereikdata = array_merge($data,$data1,$data2);
    elseif(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
    
    //listarray($this->standaarddeviatieMarge);
    
    if(count($this->standaarddeviatieMarge)<2)
      $afmMinMax=false;

    if($afmMinMax==true)
    {
      $bereikdata[]=$this->standaarddeviatieMarge['Minimum'];
      $bereikdata[]=$this->standaarddeviatieMarge['Maximum'];
    }
    

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
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) .$yAs);
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
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 .$yAs);

      $n++;
      if($n >20)
         break;
    }
    
    if($afmMinMax==true)
    {
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50,50,50));
      $yval = $YDiag + (($maxVal-$this->standaarddeviatieMarge['Minimum']) * $waardeCorrectie) ;
      $this->pdf->line($XDiag, $yval, $XPage+$w, $yval,$lineStyle );
      $this->pdf->Text($XDiag+$w, $yval,"Min.");
      
       $yval = $YDiag + (($maxVal-$this->standaarddeviatieMarge['Maximum']) * $waardeCorrectie) ;
      $this->pdf->line($XDiag, $yval, $XPage+$w, $yval,$lineStyle );
      $this->pdf->Text($XDiag+$w, $yval,"Max.");     
    }
    
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      
      if ($i>0)
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
        
        if ($i>0)
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
  
      $lastValue='';
      for ($i=0; $i<count($data2); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data2[$i]) * $waardeCorrectie) ;
        
        if ($i>0 && $data2[$i] <> 'F' && $lastValue <> 'F')
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
        $lastValue=$data2[$i];
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