<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERFG_L125
{
	function RapportPERFG_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Vanaf start";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->aandeel=1;
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function writeRapport()
  {
    global $__appvar;


    $index=new indexHerberekening();
    $indexData = $index->getWaarden($this->pdf->portefeuilledata['Startdatum'] ,$this->rapportageDatum ,$this->portefeuille);
    $totalen=array();
    $waardeOpDatum=array();
    $rendementen=array();
    foreach($indexData as $waarden)
    {
      $totalen['stortingen']+=$waarden['stortingen'];
      $totalen['onttrekkingen']+=$waarden['onttrekkingen'];
      $totalen['resultaatVerslagperiode']+=$waarden['resultaatVerslagperiode'];
      $totalen['eindwaarde']=$waarden['waardeHuidige'];
      $waardeOpDatum[$waarden['datum']]=$waarden['waardeHuidige'];
      $rendementen[$waarden['datum']]['performance']=$waarden['performance'];
      $rendementen[$waarden['datum']]['index']=$waarden['index']-100;
  //   listarray($waarden);
    }

  
    $this->pdf->AddPage();
    subHeader_L125($this->pdf, 28, array(120, 280), array('Resultaat portefeuille', 'Vermogensverloop'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setAligns(array('L','L','R'));
    $this->pdf->setWidths(array(20-$this->pdf->marge,60,30));
    $this->pdf->ln(15);
    $this->pdf->row(array('','Stortingen','€ '.$this->formatGetal($totalen['stortingen'],0)));
    $this->pdf->ln();
    $this->pdf->row(array('','Opnames','€ '.$this->formatGetal($totalen['onttrekkingen'],0)));
    $this->pdf->ln();
    $this->pdf->row(array('','Totale inleg ','€ '.$this->formatGetal($totalen['stortingen']-$totalen['onttrekkingen'],0)));
    $this->pdf->ln();
    $this->pdf->Line(20,$this->pdf->GetY() ,110,$this->pdf->GetY(),array('color'=>$this->pdf->textGrijs));
    $this->pdf->ln();
    $this->pdf->row(array('','Eindvermogen per '.date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal).' '.date("Y",db2jul($this->rapportageDatum)),
                      '€ '.$this->formatGetal($totalen['eindwaarde'],0)));
    $this->pdf->ln(10);
  
    $this->pdf->CellFontStyle=array('','',array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4),array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize));
    $this->pdf->CellFontColor=array('',array('r'=>$this->pdf->textGroen[0],'g'=>$this->pdf->textGroen[1],'b'=>$this->pdf->textGroen[2]),array('r'=>0,'g'=>0,'b'=>0));
    $this->pdf->row(array('','Totale beleggingsresultaat','€ '.$this->formatGetal($totalen['resultaatVerslagperiode'],0)));
    unset($this->pdf->CellFontStyle);
    unset($this->pdf->CellFontColor);
    
    $this->pdf->setXY(150,45);
    $this->verloopGrafiek(130,50,$waardeOpDatum);
  
  
    subHeader_L125($this->pdf, 105, array(100, 280), array('Rendement', ''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
    $this->pdf->setXY(30,120);
    $this->rendementGrafiek(200,50,$rendementen);
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $txt="De punten in de grafiek zijn werkelijk gemeten waarden. De punten worden met elkaar verbonden middels een gestileerde vloeiende lijn. In werkelijkheid is het verloop tussen de punten veel grilliger";
    $this->pdf->SetXY(240,120);
    $this->pdf->MultiCell(40,6,$txt,0,'L');
  }
  
  
  function rendementGrafiek($w,$h,$data)
  {
    
    $aantalWaarden = count($data);
    if($aantalWaarden < 13) // < dan een jaar gebruik maanden
    {
      $maandFilter=array(1,2,3,4,5,6,7,8,9,10,11,12);
    }
    elseif ($aantalWaarden < 49) // < 4 jaar gebruik kwartalen
    {
      $maandFilter=array(3,6,9,12);
    }
    else // gebruik jaren
    {
      $maandFilter=array(12);
    }
    $n=0;
    foreach ($data as $datum=>$waarde)
    {
      $grafiekData['index'][$n]=$waarde['index'];
      $grafiekData['rendement'][$n]=$waarde['performance'];
      $datumArray[$n]=$datum;
      $n++;
    }
    
    
    $minVal = 0;
    $maxVal = 1;
    
    
    foreach ($grafiekData as $type=>$maxData)
    {
      foreach ($maxData as $waarde)
      {
        $maxVal=max($maxVal,$waarde);
        $minVal=min($minVal,$waarde);
      }
    }
    
    
    $horDiv = 10;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $bottomY=$YPage+$h;
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 0.10;
    
    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    $horDiv=($maxVal - $minVal)/$stepSize*2;
    if($horDiv > 10)
      $horDiv=($maxVal - $minVal)/$stepSize;
    
    $legendYstep = round(($maxVal - $minVal) / $horDiv);
    $vBar = ($lDiag / (count($grafiekData['index'])+ 1));
    
    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
 //   $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
    $this->pdf->SetDrawColor(0,0,0);
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    $nulpunt = $YDiag + ($maxVal * $unith);
    $n=0;
    
    $this->pdf->Line($XDiag, $YDiag, $XDiag ,$YDiag+$h,array('dash' => 0,'color'=>$this->pdf->kopGrijs));
    $this->pdf->Line($XDiag, $nulpunt, $XPage+$w ,$nulpunt,array('dash' => 1,'color'=>$this->pdf->kopGrijs));
    for($i=$nulpunt; round($i)<= round($bodem); $i+= $absUnit*$legendYstep)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.3,'dash' => 1,'color'=>$this->pdf->kopGrijs));
      $this->pdf->setXY($XDiag-10,$i-2.5);
      $this->pdf->Cell(10,5,$this->formatGetal(0-($n*$legendYstep),0).'%',0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= round($top); $i-= $absUnit*$legendYstep)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.3,'dash' => 1,'color'=>$this->pdf->kopGrijs));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        // $this->pdf->Text($XDiag, $i, ($this->formatGetal($n * $legendYstep, 0)));
        $this->pdf->setXY($XDiag-10,$i-2.5);
        $this->pdf->Cell(10,5,$this->formatGetal(($n*$legendYstep),0).'%',0,0,'R');
        
      }
      $n++;
      if($n >20)
        break;
    }
    
    unset($yval);
  
    foreach ($grafiekData['rendement'] as $i=>$waarde)
    {
      if($waarde)
      {
        $yval2 = $waarde * $unith*-1 ;
        
        $barWidth=0.8;
        $extraWidth=(1-$barWidth)/2;
          $this->pdf->Rect($XDiag+($i+0.5+$extraWidth)*$unitw,$nulpunt,$unitw*$barWidth, $yval2,'F', null, $this->pdf->textGroen);//Circle($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,$this->pdf->textGroen);
      }

      
    }
    
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' =>$this->pdf->grafiekBruin);
    $circleStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->pdf->grafiekBruin);
    $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    
    $cubic=true;
    if($cubic==true)
    {
      $Index = 1;
      $XLast = -1;
      foreach ( $grafiekData['index'] as $Key => $Value )
      {
        $XIn[$Key] = $Index;
        $YIn[$Key] = $Value;
        $Index++;
      }
      
      $Index--;
//         $Index=count($data);
      $Yt[0] = 0;
      $Yt[1] = 0;
      $U[1]  = 0;
      for($i=1;$i<=$Index-1;$i++)
      {
        $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
        $p      = $Sig * $Yt[$i-1] + 2;
        $Yt[$i] = ($Sig - 1) / $p;
        $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
        $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
      }
      $qn = 0;
      $un = 0;
      $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);
      
      for($k=$Index-1;$k>=1;$k--)
        $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];
      
      
      $Accuracy=0.1;
      for($X=1;$X<=$Index;$X=$X+$Accuracy)
      {
        $klo = 1;
        $khi = $Index;
        $k   = $khi - $klo;
        while($k > 1)
        {
          $k = $khi - $klo;
          If ( $XIn[$k] >= $X )
            $khi = $k;
          else
            $klo = $k;
        }
        $klo = $khi - 1;
        
        $h     = $XIn[$khi] - $XIn[$klo];
        $a     = ($XIn[$khi] - $X) / $h;
        $b     = ($X - $XIn[$klo]) / $h;
        $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;
        $YPos = $YDiag + (($maxVal-$Value) * $unith) ;
        $XPos = $XDiag+($X)*$unitw;
        
        
        if($X==1)
        {
          $XLast=$XPos;
          $YLast=$YPos;
        }
        
        $this->pdf->Line($XLast,$YLast,$XPos,$YPos,$lineStyle);
        $XLast = $XPos;
        $YLast = $YPos;
      }
    }
    
    
    foreach ($grafiekData['index'] as $i=>$waarde)
    {
      if(!isset($datumPrinted[$i]))
      {
        $datumPrinted[$i] = 1;
        $julDatum=db2jul($datumArray[$i]);
        $maand=date('m',$julDatum);
        if(in_array($maand,$maandFilter))
        {
          $this->pdf->setXY($XDiag + ($i + 1) * $unitw - 5, $YDiag + $hDiag);
          $this->pdf->Line($XDiag + ($i + 1) * $unitw, $YDiag + $hDiag, $XDiag + ($i + 1) * $unitw,$YDiag + $hDiag +1,array('width' => 0.3,'dash' => 0,'color'=>$this->pdf->kopGrijs));
          $this->pdf->Cell(10, 5, vertaalTekst($maanden[date("n", $julDatum)], $this->pdf->rapport_taal) . ' ' . date("y", $julDatum), 0, 0, 'C');
        }
      }
      if($waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
        if(isset($yval))
        {
          $markerSize=0.5;
          if($cubic==false)
            $this->pdf->line($XDiag+$i*$unitw, $yval, $XDiag+($i+1)*$unitw, $yval2,$lineStyle );
          $this->pdf->Circle($XDiag+$i*$unitw, $yval, $markerSize, 0,360, $style = 'F', $circleStyle, $this->pdf->grafiekBruin);//Circle($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,$this->pdf->textGroen);
        }
        $yval = $yval2;
      }
    }

    $extraX=$w/2-25;
    $this->pdf->setXY($XPage+$extraX,$bottomY+8);
    $this->pdf->Rect($this->pdf->GetX()-3,$this->pdf->GetY()+1.25,2.5, 2.5,'F', null, $this->pdf->textGroen);
    $this->pdf->Cell(25, 5, vertaalTekst('Rendement', $this->pdf->rapport_taal), 0, 0, 'L');
    $this->pdf->Circle($this->pdf->GetX()-3,$this->pdf->GetY()+2.5, 1.25, 0,360, $style = 'F', $circleStyle, $this->pdf->grafiekBruin);
    $this->pdf->Cell(25, 5, vertaalTekst('Cumulatief', $this->pdf->rapport_taal), 0, 0, 'L');
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->CellBorders = array();
  }
  
  function verloopGrafiek($w,$h,$data)
  {
    
    $aantalWaarden = count($data);
    if($aantalWaarden < 13) // < dan een jaar gebruik maanden
    {
      $maandFilter=array(1,2,3,4,5,6,7,8,9,10,11,12);
    }
    elseif ($aantalWaarden < 37) // < 3 jaar gebruik kwartalen
    {
      $maandFilter=array(3,6,9,12);
    }
    else // gebruik jaren
    {
      $maandFilter=array(12);
    }
    $n=0;
    foreach ($data as $datum=>$waarde)
    {
      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
        $koers=getValutaKoers($this->pdf->rapportageValuta,$datum);
      else
        $koers=1;
      $grafiekData['portefeuille'][$n]=$waarde/$koers;
      $datumArray[$n]=$datum;
      $n++;
    }
    
    
    $minVal = 0;
    $maxVal = 1;
    
    
    foreach ($grafiekData as $type=>$maxData)
    {
      foreach ($maxData as $waarde)
      {
        $maxVal=max($maxVal,$waarde);
        $minVal=min($minVal,$waarde);
      }
    }
    

    $horDiv = 10;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 0.10;
    
    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    $horDiv=($maxVal - $minVal)/$stepSize*2;
    if($horDiv > 10)
      $horDiv=($maxVal - $minVal)/$stepSize;
    
    $legendYstep = round(($maxVal - $minVal) / $horDiv);
    $vBar = ($lDiag / (count($grafiekData['portefeuille'])+ 1));
    
    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
  //  $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
    $this->pdf->SetDrawColor(0,0,0);
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    $nulpunt = $YDiag + ($maxVal * $unith);
    $n=0;
    
    $this->pdf->Line($XDiag, $YDiag, $XDiag ,$YDiag+$h,array('dash' => 0,'color'=>$this->pdf->kopGrijs));
    $this->pdf->Line($XDiag, $nulpunt, $XPage+$w ,$nulpunt,array('dash' => 1,'color'=>$this->pdf->kopGrijs));
    for($i=$nulpunt; round($i)<=round($bodem); $i+= $absUnit*$legendYstep)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.3,'dash' => 1,'color'=>$this->pdf->kopGrijs));
      $this->pdf->setXY($XDiag-10,$i-2.5);
      $this->pdf->Cell(10,5,$this->formatGetal(0-($n*$legendYstep),0),0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= round($top); $i-= $absUnit*$legendYstep)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.3,'dash' => 1,'color'=>$this->pdf->kopGrijs));
      if($skipNull == true)
        $skipNull = false;
      else
      {
       // $this->pdf->Text($XDiag, $i, ($this->formatGetal($n * $legendYstep, 0)));
        $this->pdf->setXY($XDiag-10,$i-2.5);
        $this->pdf->Cell(10,5,$this->formatGetal(($n*$legendYstep),0),0,0,'R');
  
      }
      $n++;
      if($n >20)
        break;
    }

    unset($yval);
    
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' =>$this->pdf->textGroen);
    $circleStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->pdf->textGroen);
    $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
  
    $cubic=true;
    if($cubic==true)
    {
      $Index = 1;
      $XLast = -1;
      foreach ( $grafiekData['portefeuille'] as $Key => $Value )
      {
        $XIn[$Key] = $Index;
        $YIn[$Key] = $Value;
        $Index++;
      }
    
      $Index--;
//         $Index=count($data);
      $Yt[0] = 0;
      $Yt[1] = 0;
      $U[1]  = 0;
      for($i=1;$i<=$Index-1;$i++)
      {
        $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
        $p      = $Sig * $Yt[$i-1] + 2;
        $Yt[$i] = ($Sig - 1) / $p;
        $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
        $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
      }
      $qn = 0;
      $un = 0;
      $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);
    
      for($k=$Index-1;$k>=1;$k--)
        $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];
    
    
      $Accuracy=0.1;
      for($X=1;$X<=$Index;$X=$X+$Accuracy)
      {
        $klo = 1;
        $khi = $Index;
        $k   = $khi - $klo;
        while($k > 1)
        {
          $k = $khi - $klo;
          If ( $XIn[$k] >= $X )
            $khi = $k;
          else
            $klo = $k;
        }
        $klo = $khi - 1;
      
        $h     = $XIn[$khi] - $XIn[$klo];
        $a     = ($XIn[$khi] - $X) / $h;
        $b     = ($X - $XIn[$klo]) / $h;
        $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;
        $YPos = $YDiag + (($maxVal-$Value) * $unith) ;
        $XPos = $XDiag+($X)*$unitw;
      
      
        if($X==1)
        {
          $XLast=$XPos;
          $YLast=$YPos;
        }
      
        $this->pdf->Line($XLast,$YLast,$XPos,$YPos,$lineStyle);
        $XLast = $XPos;
        $YLast = $YPos;
      }
    }
    
    
    foreach ($grafiekData['portefeuille'] as $i=>$waarde)
    {
      if(!isset($datumPrinted[$i]))
      {
        $datumPrinted[$i] = 1;
        $julDatum=db2jul($datumArray[$i]);
        $maand=date('m',$julDatum);
        if(in_array($maand,$maandFilter))
        {
          $this->pdf->setXY($XDiag + ($i + 1) * $unitw - 5, $YDiag + $hDiag);
          $this->pdf->Line($XDiag + ($i + 1) * $unitw, $YDiag + $hDiag, $XDiag + ($i + 1) * $unitw,$YDiag + $hDiag +1,array('width' => 0.3,'dash' => 0,'color'=>$this->pdf->kopGrijs));
          $this->pdf->Cell(10, 5, vertaalTekst($maanden[date("n", $julDatum)], $this->pdf->rapport_taal) . ' ' . date("y", $julDatum), 0, 0, 'C');
        }
      }
      if($waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
        if(isset($yval))
        {
          $markerSize=0.5;
          if($cubic==false)
            $this->pdf->line($XDiag+$i*$unitw, $yval, $XDiag+($i+1)*$unitw, $yval2,$lineStyle );
          $this->pdf->Circle($XDiag+$i*$unitw, $yval, $markerSize, 0,360, $style = 'F', $circleStyle, $this->pdf->textGroen);//Circle($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,$this->pdf->textGroen);
        }
        $yval = $yval2;
      }
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->CellBorders = array();
  }
  
  
}

