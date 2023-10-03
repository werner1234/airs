<?
include_once('../indexBerekening.php');


class RapportPERFG_L126
{

  function RapportPERFG_L126($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Meerjaren vermogensontwikkeling";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec,$eur=false)
	{
		return ($eur==true?'€ ':'').number_format($waarde,$dec,",",".");
	}


  function writeRapport()
	{

    $this->gatherData('PERFG');
	

	}

  function gatherData($plotData)
  {
  	$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    if($plotData=='PERFG')
    {
      $this->pdf->AddPage();
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
    }
    if(is_array($this->pdf->portefeuilles))
      $portefeuilles="Portefeuille IN ('".implode("','",$this->pdf->portefeuilles)."') AND";
    else
      $portefeuilles="Portefeuille = '".$this->portefeuille."' AND";


    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE $portefeuilles  Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();

if(1)//8726 Altijd vanaf portefeuilleStart $this->pdf->lastPOST['perfPstart'] == 1 || $plotData=='VAR')
{
  if($datum['id'] > 0)
  {
    if($datum['month'] <10)
      $datum['month'] = "0".$datum['month'];
    $start = $datum['year'].'-'.$datum['month'].'-01';
  }
  else
    $start=$this->pdf->PortefeuilleStartdatum;
}
else
  $start = $this->rapportageDatumVanaf;
  
    $eind = $this->rapportageDatum;
    $datumStart = db2jul($start);
    $datumStop  = db2jul($eind);
    
$rapportageJaar=substr($this->rapportageDatum,0,4);
if($plotData=='VAR' && $datumStart<db2jul(($rapportageJaar-5).'-01-01'))
{
  $start=($rapportageJaar-5).'-01-01';
  $datumStart=db2jul($start);
}



$index = new indexHerberekening();
//$index->geenCacheGebruik=true;
//$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille,'','kwartaal');
    $indexWaarden=array();
if($datumStop>=db2jul('2015-01-01'))
{
  $indexWaarden=array();
  if($datumStart < db2jul('2015-01-01'))
  {
    if($datumStop < db2jul('2014-12-31'))
      $stop=$eind;
    else
      $stop='2014-12-31';
    $tmpWaarden = $index->getWaarden($start,$stop,array($this->portefeuille,$this->pdf->portefeuilles),'','kwartaal');
    foreach($tmpWaarden as $i=>$tmp)
    {
      $tmp['type']='k';
      $indexWaarden[]=$tmp;
    }
  }
  if($datumStart > db2jul('2015-01-01'))
    $start2=$start;
  else
    $start2='2015-01-01';  
 
  $tmpWaarden = $index->getWaarden($start2,$eind,array($this->portefeuille,$this->pdf->portefeuilles),'','maanden');
  //echo "$start2,$eind <br>\n";
  //listarray($tmpWaarden);
  foreach($tmpWaarden as $i=>$tmp)
  {
    $tmp['type']='m';
    $indexWaarden[]=$tmp;
  }  

}
else
{
  $tmpWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles),'','kwartaal');
  foreach($tmpWaarden as $i=>$tmp)
  {
    $tmp['type']='k';
    $indexWaarden[]=$tmp;
  }
}

$kwartaalPeriode=mktime(0,0,0,1,1,date("Y",$datumStop)+1);//uitgezet
$n=0;
$minVal = 99;
$maxVal = 101;
$aantalWaarden=count($indexWaarden)-1;
$jarenPerfCumu=100;
$jaarPerfCumu=100;
foreach ($indexWaarden as $id=>$data)
{
  if($data['performance'] == -100)
    $data['performance']=0;

  $jaar=substr($data['datum'],0,4);
  $juldate=db2jul($data['datum']);
  if($juldate < $kwartaalPeriode)
  {
    if(empty($jaarPerf[$jaar]))
      $jaarPerf[$jaar]=100;
      
    $jaarPerf[$jaar] =($jaarPerf[$jaar]*(100+$data['performance'])/100);
    $jarenPerfCumu =($jarenPerfCumu*(100+$data['performance'])/100);
    

    if(!isset($jaarWaarden[$jaar]['waardeBegin']))
      $jaarWaarden[$jaar]['waardeBegin']=$data['waardeBegin'];

    $jaarWaarden[$jaar]['stortingen']+=$data['stortingen'];
    $jaarWaarden[$jaar]['onttrekkingen']+=$data['onttrekkingen'];
    $jaarWaarden[$jaar]['index']=$jarenPerfCumu;

    if(substr($data['datum'],5,5)=='12-31' || $aantalWaarden == $id) //aangezet
    { 
       $grafiekData['jaren']['portefeuille'][]=$jaarPerf[$jaar]-100;
       $grafiekData['jaren']['datum'][]= $jaar;
       $tmp=array_merge($data,$jaarWaarden[$jaar]);
       $grafiekData['jaren']['waarde'][]=$tmp;
    }
  }
  else
  {
//    echo $data['datum']." | ".($jarenPerfCumu*(100+$data['performance'])/100)."=($jarenPerfCumu*(100+".$data['performance'].")/100)<br>\n";
    $jaarPerfCumu =($jaarPerfCumu*(100+$data['performance'])/100);
    $jarenPerfCumu =($jarenPerfCumu*(100+$data['performance'])/100);
    if($data['type']=='m')
    {
      
      $juldate=db2jul($data['datum']);
      $kwartaal=(ceil(date("m",$juldate)/3));
      $periode=date("Y",$juldate).'-'.$kwartaal;
      
      if(empty($kwartaalPerf[$periode]))
        $kwartaalPerf[$periode]=100;
      $kwartaalPerf[$periode] =($kwartaalPerf[$periode]*(100+$data['performance'])/100);
      $jarenPerf[$periode] =$jarenPerfCumu;
      //echo $periode." ".round($data['performance'],2)." ".(round($kwartaalPerf[$periode],2)-100)."<br>\n";
      if(!isset($kwartaalWaarden[$periode]['waardeBegin']))
      {
       // listarray($data);
        $kwartaalWaarden[$periode]['waardeBegin']=$data['waardeBegin'];
      }
      $kwartaalWaarden[$periode]['stortingen']+=$data['stortingen'];
      $kwartaalWaarden[$periode]['onttrekkingen']+=$data['onttrekkingen'];

      if($lastPeriode<>'' && $lastPeriode <> $periode)
      {
        // echo $id.' '.$aantalWaarden." $periode $lastPeriode<br>\n";  listarray($data);    
        $kwartaalWaarden[$lastPeriode]['performance']=$kwartaalPerf[$lastPeriode]-100;
        $kwartaalWaarden[$lastPeriode]['index']= $jarenPerf[$lastPeriode];
        $grafiekData['kwartalen']['portefeuille'][]=$kwartaalPerf[$lastPeriode]-100;
        $grafiekData['kwartalen']['datum'][]= "Q".(ceil(date("m",$lastJuldate)/3))."-".date("Y",$lastJuldate);
        $grafiekData2['portefeuille'][]=$lastData['waardeHuidige'];
        $tmp=array_merge($lastData,$kwartaalWaarden[$lastPeriode]);
        //listarray($kwartaalWaarden[$lastPeriode]);
        $grafiekData['kwartalen']['waarde'][]=$tmp;
      }
      
      if($aantalWaarden == $id)
      {
      //  if($lastJuldate=='')
      //  {
          $lastPeriode=$periode;
          $lastJuldate=$juldate;
          $lastData=$data;
      //  }
        $kwartaalWaarden[$lastPeriode]['performance']=$kwartaalPerf[$lastPeriode]-100;
        $kwartaalWaarden[$lastPeriode]['index']= $jarenPerfCumu;
        $grafiekData['kwartalen']['portefeuille'][]=$kwartaalPerf[$lastPeriode]-100;
        $grafiekData['kwartalen']['datum'][]= "Q".(ceil(date("m",$lastJuldate)/3))."-".date("Y",$lastJuldate);
        $grafiekData2['portefeuille'][]=$data['waardeHuidige'];
        $tmp=array_merge($data,$kwartaalWaarden[$periode]);
        $grafiekData['kwartalen']['waarde'][]=$tmp;    
      }
      $lastPeriode=$periode;
      $lastJuldate=$juldate;
      $lastData=$data;
    }
    else
    {
     
      $grafiekData['kwartalen']['portefeuille'][]=$data['performance'];
      $grafiekData['kwartalen']['datum'][]= "Q".(ceil(date("m",$juldate)/3))."-".date("Y",$juldate);
      $grafiekData2['portefeuille'][]=$data['waardeHuidige'];
      $grafiekData['kwartalen']['waarde'][]=$data;
    }
    
    // $grafiekData['kwartalen']['portefeuille'][]['cumulatief'] = $data['index'];
  }
//listarray($grafiekData['kwartalen']);
  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] = ($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$jaar]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];
  if(substr($data['datum'],5,5) == '12-31' || $aantalWaarden == $id)//aangezet
  {
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;
    $indexTabel[$jaar]['portefeuille']['cumulatief'] = $jarenPerfCumu;
  }
}
  
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);

if($plotData=='PERFG')
{
$this->pdf->CellBorders=array();
$this->pdf->setY(25);
$color=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
$this->pdf->ln();
//$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
  $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U');
  $this->pdf->setWidths(array(30,30,30,30,30,30,30,30));
$this->pdf->setAligns(array('L','R','R','R','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
if(count($grafiekData['jaren']['datum'])>0)
  $this->pdf->Row(array('Periode', 'Beginvermogen', 'Stortingen', 'Onttrekkingen', 'Resultaat', 'Eindvermogen','Per jaar','Cumulatief'));

//$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
unset($this->pdf->CellBorders);
$som=array();
$aantal=count($grafiekData['jaren']['datum'])-1;
foreach($grafiekData['jaren']['datum'] as $i=>$datum)
{
  //if($i==$aantal)
  //  $this->pdf->CellBorders = array('','','US','US','','','','');
    
   $this->pdf->Row(array($datum,
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeBegin'],2,false),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['stortingen'],2,false),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['onttrekkingen'],2,false),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige']-$grafiekData['jaren']['waarde'][$i]['waardeBegin']+$grafiekData['jaren']['waarde'][$i]['onttrekkingen']-$grafiekData['jaren']['waarde'][$i]['stortingen'],2,false),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige'],2,false),
   $this->formatGetal($indexTabel[$datum]['portefeuille']['jaar']-100,1)."%",
   $this->formatGetal($indexTabel[$datum]['portefeuille']['cumulatief']-100,1)."%"
   ));
  $som['stortingen']+=$grafiekData['jaren']['waarde'][$i]['stortingen'];
  $som['onttrekkingen']+=$grafiekData['jaren']['waarde'][$i]['onttrekkingen'];
}
$this->pdf->ln();
  //$this->pdf->CellBorders = array('','','UU','UU');
  //$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
  //$this->pdf->Row(array('','',$this->formatGetal($som['stortingen'],2,true),$this->formatGetal($som['onttrekkingen'],2,true),'','','',''));
  //$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U');
  $this->pdf->Row(array('','','','','','','',''));
$this->pdf->CellBorders = array();

if(count($grafiekData['kwartalen']['datum'])>0)
{
  $this->pdf->ln();
//$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
  $this->pdf->CellBorders = array('U', 'U', 'U', 'U', 'U', 'U', 'U', 'U');
  $this->pdf->setWidths(array(30, 30, 30, 30, 30, 30, 30, 30));
  $this->pdf->setAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
//$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
  $this->pdf->Row(array('periode', 'beginvermogen', 'stortingen', 'onttrekkingen', 'resultaat', 'eindvermogen', 'per kwartaal', 'cumulatief'));
  unset($this->pdf->CellBorders);
//$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  foreach ($grafiekData['kwartalen']['datum'] as $i => $datum)
  {
    $this->pdf->Row(array($datum,
                      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeBegin'], 2),
                      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['stortingen'], 2),
                      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['onttrekkingen'], 2),
                      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'] - $grafiekData['kwartalen']['waarde'][$i]['waardeBegin'] + $grafiekData['kwartalen']['waarde'][$i]['onttrekkingen'] - $grafiekData['kwartalen']['waarde'][$i]['stortingen'], 2),
                      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'], 2),
                      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['performance'], 1) . "%",
                      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['index'] - 100, 1) . "%"));
  }
  $this->pdf->CellBorders = array();
}
////
$this->pdf->setXY(15,190);
if(count($grafiekData['jaren']['datum'])>0)
  $this->VBarDiagram(233,70,$grafiekData['jaren'],'',$color);
}
else
{
  return array('grafiekData'=>$grafiekData,'indexTabel'=>$indexTabel);
}
    
  }

  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      $data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0)
        $maxVal = ceil(max($data));
      $minVal = floor(min($data));

      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.2;

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

      $stapgrootte = ceil(abs($bereik)/$horDiv);
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

      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

      $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
      $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $val)
      {
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
      }

      //datum onder grafiek
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
      foreach ($legendDatum as $i=>$datum)
      {
       $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
       $this->pdf->SetXY($xval,$YstartGrafiek);
       $this->pdf->Cell($eBaton, 4,$datum,0,0,'C');
      }
  }
}
?>
