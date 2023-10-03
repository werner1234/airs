<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportPERFD_L104
{
	function RapportPERFD_L104($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
    $this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->huidigeJaar=substr($rapportageDatum,0,4);
    $this->startJaarJul=db2jul($this->huidigeJaar.'-01-01');
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

	function toonRegel($maandData,$variabele,$cumulatief=array())
  {
    $type=str_replace(array('rendement','stdev'),array('',''),$variabele);
    $row=array($type);
    for($i=1;$i<13;$i++)
    {
      if(isset($maandData[$i][$variabele]))
        $row[]=$this->formatGetal($maandData[$i][$variabele],2).'%';
      else
        $row[]='';
    }
    if(isset($cumulatief[$variabele]))
      $row[]=$this->formatGetal($cumulatief[$variabele],2).'%';
    else
      $row[]='';
    $this->pdf->row($row);
   
  }
  
  
  function VBarDiagram2($w, $h, $data,$colors)
  {
    global $__appvar;
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
   
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1);
    
   // $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
    $color=array(155,155,155);
    
    $maxVal=0;
    $minVal=0;
    $jaren=array();
    foreach($data as $jaar=>$maandData)
    {
      $jaren[$jaar]=$jaar;
      foreach($maandData as $type=>$waarde)
      {
        if($waarde > $maxVal)
          $maxVal = $waarde;
        if($waarde < $minVal)
          $minVal = $waarde;
      }
    }
    if($maxVal > 1)
      $maxVal=ceil($maxVal);
    if($minVal < -1)
      $minVal=floor($minVal);
    $minVal = $minVal * 1.1;
    $maxVal = $maxVal * 1.1;
    if ($maxVal <0)
      $maxVal=0;
    
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
    
    $horDiv = 10;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }
    
    $numBars=count($data);
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    

    $vBar = ($bGrafiek / ($this->pdf->NbVal ))/3; //4
    $bGrafiek = $vBar * ($this->pdf->NbVal );
    $eBaton = ($vBar * 80 / 100);
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    foreach($data as $jaar=>$maandData)
    {
      
      foreach($maandData as $type=>$val)
      {
        $color=$colors[$type];
        //Bar
        $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiek + $nulYpos;
        $hval = ($val * $unit);
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3 && $eBaton > 4)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        $i++;
      }
      $i++;
      
      
      $this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,$jaar);
      
    }

  }

	function writeRapport()
	{
	  global $__appvar;
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    

    
    $stdev = new rapportSDberekening($this->portefeuille, $this->rapportageDatum);
    $stdev->addReeks('totaal');
    $stdev->addReeks('benchmarkTot', $this->pdf->portefeuilledata['SpecifiekeIndex']);
    $stdev->berekenWaarden();
    $maandwaarden=array();
    $jaarWaarden=array();
    foreach($stdev->reeksen['totaal'] as $maand=>$perfData)
    {
      $jaar=substr($maand,0,4);
      if(db2jul($maand)>=$this->startJaarJul)
      {
        $maandKort=intval(substr($maand,5,2));
        $maandwaarden[$maandKort]['rendementPortefeuille']=$perfData['perf'];
        $maandwaarden[$maandKort]['rendementBenchmark']=$stdev->reeksen['benchmarkTot'][$maand]['perf'];
        $maandwaarden[$maandKort]['stdevPortefeuille']=$stdev->standaardDeviatieReeksen['totaal'][$maand]['stdev'];
        $maandwaarden[$maandKort]['stdevBenchmark']=$stdev->standaardDeviatieReeksen['benchmarkTot'][$maand]['stdev'];
  

        
      }
      $jaarWaarden[$jaar]['rendementPortefeuille']=((1+$jaarWaarden[$jaar]['rendementPortefeuille']/100)*(1+$perfData['perf']/100)-1)*100;
      $jaarWaarden[$jaar]['rendementBenchmark']=((1+$jaarWaarden[$jaar]['rendementBenchmark']/100)*(1+$stdev->reeksen['benchmarkTot'][$maand]['perf']/100)-1)*100;
      $jaarWaarden[$jaar]['stdevPortefeuille']=$stdev->standaardDeviatieReeksen['totaal'][$maand]['stdev'];
      $jaarWaarden[$jaar]['stdevBenchmark']=$stdev->standaardDeviatieReeksen['benchmarkTot'][$maand]['stdev'];
    }
    
    
    $header=array('','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec','YTD');
    $w=15;
    $widths=array(30,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
    
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetWidths($widths);
    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 6, 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row($header);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->row(array('Rendement'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->toonRegel($maandwaarden,'rendementPortefeuille',$jaarWaarden[$this->huidigeJaar]);
    $this->toonRegel($maandwaarden,'rendementBenchmark',$jaarWaarden[$this->huidigeJaar]);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Risico'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->toonRegel($maandwaarden,'stdevPortefeuille');
    $this->toonRegel($maandwaarden,'stdevBenchmark');
    $this->pdf->ln();
    foreach($jaarWaarden as $jaar=>$jaarData)
    {
      $grafiekData['Rendement'][$jaar]=array('Portefeuille'=>$jaarData['rendementPortefeuille'],'Benchmark'=>$jaarData['rendementBenchmark']);
      $grafiekData['Risico'][$jaar]=array('Portefeuille'=>$jaarData['stdevPortefeuille'],'Benchmark'=>$jaarData['stdevBenchmark']);

    }
    $colors=array('Portefeuille'=>array(68,114,196),'Benchmark'=>array(237,125,49));//,'totaalEffect'=>array(0, 52, 121)); //
    $this->pdf->SetWidths(array(95,95));
    $this->pdf->SetAligns(array('C','C'));
    $this->pdf->row(array('Rendement','Risico'));
    $grafiekY=$this->pdf->getY()+50;
    $this->pdf->setXY($this->pdf->marge+10,$grafiekY);
    $this->VBarDiagram2(80,50,$grafiekData['Rendement'],$colors);
    
    $this->pdf->setXY($this->pdf->marge+105,$grafiekY);
    $this->VBarDiagram2(80,50,$grafiekData['Risico'],$colors);
    $this->pdf->setXY($this->pdf->marge,$grafiekY+10);
    $this->pdf->rect(29,$grafiekY+11,2,2,'F','',$colors['Portefeuille']);
    $this->pdf->rect(49,$grafiekY+11,2,2,'F','',$colors['Benchmark']);
    $this->pdf->rect(100+29,$grafiekY+11,2,2,'F','',$colors['Portefeuille']);
    $this->pdf->rect(100+49,$grafiekY+11,2,2,'F','',$colors['Benchmark']);
    $this->pdf->setWidths(array(15,20,20,45,15,20,20));
    $this->pdf->SetAligns(array('L','R','R','C','L','R','R'));
    $this->pdf->row(array('','Portefeuille','Benchmark','','','Portefeuille','Benchmark'));
    foreach($jaarWaarden as $jaar=>$jaarWaarden)
    {
      $this->pdf->row(array($jaar,$this->formatGetal($jaarWaarden['rendementPortefeuille'],1).'%',$this->formatGetal($jaarWaarden['rendementBenchmark'],1).'%','',
                            $jaar,$this->formatGetal($jaarWaarden['stdevPortefeuille'],1).'%',$this->formatGetal($jaarWaarden['stdevBenchmark'],1).'%'));
    }
    

 	}
}
?>
