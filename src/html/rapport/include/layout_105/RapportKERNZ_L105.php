<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_105/ATTberekening_L105.php");

class RapportKERNZ_L105
{
  function RapportKERNZ_L105($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "KERNZ";
    $this->pdf->rapport_startDatum = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Ontwikkeling van het vermogen/resultaat";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function getGrafiekdata()
  {
    $DB = new DB();
    if(isset($this->pdf->portefeuilles))
      $port= "IN('".implode("','",$this->pdf->portefeuilles)."') ";
    else
      $port= "= '".$this->portefeuille."'";
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille $port AND Categorie = 'Totaal'  ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();
  
    if($datum['id'] > 0 )//&& $this->pdf->lastPOST['perfPstart'] == 1
    {
      if($datum['month'] <10)
        $datum['month'] = "0".$datum['month'];
      $start = $datum['year'].'-'.$datum['month'].'-01';
    }
    else
      $start = $this->pdf->portefeuilledata['Startdatum'];
    
    $eind = $this->rapportageDatum;
  
    $datumStart = db2jul($start);
    $datumStop  = db2jul($eind);
  
    //
   // $indexWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles));
  
  
    if($this->pdf->portefeuilledata['PerformanceBerekening']==3 && intval(substr($this->rapportageDatum,0,4))>=2020)
    {
      $this->att = new ATTberekening_L105($this);
      $indexWaarden = $this->att->getPerf($this->portefeuille,$start, $this->rapportageDatum, $this->pdf->portefeuilledata['RapportageValuta'], true);

    }
    else
    {
      $index = new indexHerberekening();
      $indexWaarden = $index->getWaarden($start, $this->rapportageDatum , $this->portefeuille, '', 'maanden');
    }
  
    
    $aantalWaarden = count($indexWaarden);
    //echo $aantalWaarden;exit;
    $n=0;
    if(true||$aantalWaarden < 84)//13 ) // < dan een jaar gebruik maanden
    {
      $maandFilter=array(1,2,3,4,5,6,7,8,9,10,11,12);
    }
    elseif ($aantalWaarden < 336)//49) // < 4 jaar gebruik kwartalen
    {
      $maandFilter=array(3,6,9,12);
    }
    else // gebruik jaren
    {
      $maandFilter=array(12);
    }
  
    $cumulatiefResultaat=0;
    foreach ($indexWaarden as $id=>$data)
    {
      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
        $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
      else
        $koers=1;
      $grafiekData['portefeuille'][$n]=$data['waardeHuidige']/$koers;
      $grafiekData['storingen'][$n]+=($data['stortingen']-$data['onttrekkingen'])/$koers;
      $periodeResultaat=($data['resultaatVerslagperiode'])/$koers;
      $cumulatiefResultaat+=$periodeResultaat;
      $grafiekData['resultaatVerslagperiode'][$n]+=$periodeResultaat;
      $grafiekData['cumulatiefResultaat'][$n]+=$cumulatiefResultaat;
      $grafiekData['datumArray'][$n]=$data['datum'];
      $maand=date('m',db2jul($data['datum']));
      
      if(in_array($maand,$maandFilter))
        $n++;
    }
    return $grafiekData;
  }
  
  function perfG($xPositie,$yPositie,$width,$height,$title='',$title2='inclusief stortingen en onttrekkingen',$grafiekData=array(),$staven='storingen')
  {
    if($staven=='resultaatVerslagperiode')
    {
      $lijn = 'cumulatiefResultaat';
      $minMax=array('resultaatVerslagperiode','cumulatiefResultaat');
    }
    else
    {
      $lijn = 'portefeuille';
      $minMax=array('portefeuille','storingen');
    }
    
    $datumArray=$grafiekData['datumArray'];
    $w=$width;
    $this->pdf->setXY($xPositie,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,5,$title,'','C');
    $this->pdf->setXY($xPositie,$yPositie-5);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $this->pdf->Multicell($w,5,vertaalTekst($title2,$this->pdf->rapport_taal),'','C');
    //echo ($xPositie+$w);exit;
    $this->pdf->setXY($xPositie+$w,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->Multicell(20,5,'X 1.000','','L');
    
    $this->pdf->setXY($xPositie,$yPositie);
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
 
    
    
    $minVal = -1;
    $maxVal = 1;
    
    
    foreach ($grafiekData as $type=>$maxData)
    {
      if(!in_array($type,$minMax))
        continue;
      foreach ($maxData as $waarde)
      {
        $maxVal=max($maxVal,$waarde);
        $minVal=min($minVal,$waarde);
      }
    }
    
    $w=$width;
    $h=$height;
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
    $aantalWaarden=count($grafiekData['portefeuille']);
    $vBar = ($lDiag / ($aantalWaarden + 1));
    $bGrafiek = $vBar * ($aantalWaarden + 1);
    $eBaton = ($vBar * .5);
    
    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
  //  $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'D','');
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    $nulpunt = $YDiag + ($maxVal * $unith);
    $n=0;
    
    $this->pdf->Line($XDiag, $nulpunt, $XPage+$w ,$nulpunt,array('dash' => 1,'color'=>array(128,128,128)));
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$legendYstep)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
      $this->pdf->Text($XDiag+$w+2, $i, $this->formatGetal(0-($n*$legendYstep/1000)));
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$legendYstep)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag+$w+2, $i, ($this->formatGetal($n*$legendYstep/1000)));
      $n++;
      if($n >20)
        break;
    }
    $n=0;
    $laatsteI = count($datumArray)-1;
    $lijnenAantal = count($grafiekData);
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.1));
    foreach ($grafiekData[$staven] as $i=>$waarde)
    {
      $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
      $yval = $yval2;
      $xval = $XDiag + (1 + $i ) * $unitw - ($eBaton / 2);
      $lval = $eBaton;
      $hval =$nulpunt-$yval;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,array(145,182,215)); //  //0,176,88
    }
    unset($yval);
    
    $lineStyle = array('width' => 0.75, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    $cubic=true;
    
    foreach ($grafiekData[$lijn] as $i=>$waarde)
    {
      if(!isset($datumPrinted[$i]))
      {
        $datumPrinted[$i] = 1;
        $maandDag=substr($datumArray[$i], 5, 5);
        if ( $aantalWaarden<49 || ($aantalWaarden>48 && ($maandDag=='03-31' || $maandDag=='06-30' || $maandDag=='09-30' || $maandDag=='12-31') && $i>1 && $i<$laatsteI-1) || $i == $laatsteI || $i == 0) //|| ($aantalWaarden>80  && $maandDag == '12-31')
        {
           $julDatum=db2jul($datumArray[$i]);
          //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$this->pdf->rapport_taal).'-'.date("Y",$julDatum),45);
       
          $this->pdf->TextWithRotation($XDiag + ($i + 1) * $unitw-2, $YDiag + $hDiag + 10, vertaalTekst($maanden[date("n", $julDatum)], $this->pdf->rapport_taal) . '-' . date("y", $julDatum), 25);
        }
      }
      if($cubic==false && $waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
        if(isset($yval))
        {
          $markerSize=0.5;
          $this->pdf->line($XDiag+$i*$unitw, $yval, $XDiag+($i+1)*$unitw,$yval2,$lineStyle);
          $this->pdf->Rect($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,array(0,176,88));
        }
        $yval = $yval2;
      }
    }
    
    if($cubic==true)
    {
      $Index = 1;
      $XLast = -1;
      foreach ( $grafiekData[$lijn] as $Key => $Value )
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
      
        // echo "$Value <br>\n";
      
        //$YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);
        $YPos = $YDiag + (($maxVal-$Value) * $absUnit) ;
        
        $XPos = $XDiag+($X)*$unitw;
      
      
        if($X==1)
        {
          $XLast=$XPos;
          $YLast=$YPos;
        }
        if(isset($YLast))
          $this->pdf->Line($XLast,$YLast,$XPos,$YPos,$lineStyle);
        $XLast = $XPos;
        $YLast = $YPos;
      
      }
    
    
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->CellBorders = array();
  }
  
  function writeRapport()
  {
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $data=$this->getGrafiekdata();
    $this->perfG($this->pdf->marge,40,$this->pdf->w-$this->pdf->marge*3,130,vertaalTekst('Ontwikkeling van het Resultaat',$this->pdf->rapport_taal),'Cumulatief per maand',$data,'resultaatVerslagperiode');
    $this->pdf->addPage();
    $this->perfG($this->pdf->marge,40,$this->pdf->w-$this->pdf->marge*3,130,vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal),'inclusief stortingen en onttrekkingen',$data);
  }
  
  
}
