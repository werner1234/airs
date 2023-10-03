<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/31 13:13:05 $
 		File Versie					: $Revision: 1.10 $

 		$Log: RapportPERFG_L83.php,v $
 		Revision 1.10  2019/08/31 13:13:05  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2019/07/13 17:50:20  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2019/05/15 15:31:34  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2019/05/12 15:45:36  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/05/11 16:49:13  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2019/04/20 16:59:05  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/04/14 15:42:05  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/04/06 17:13:45  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/03/10 14:09:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/03/02 18:23:01  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/01/16 11:02:07  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/09/05 16:48:04  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/10/26 15:42:47  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/09/28 14:43:25  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/09/25 15:54:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/06 16:00:55  rvv
 		*** empty log message ***

*/

include_once('../indexBerekening.php');


class RapportPERFG_L83
{

  function RapportPERFG_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = vertaalTekst("Portefeuille-ontwikkeling in",$this->pdf->rapport_taal).' '. vertaalTekst($this->pdf->valutaOmschrijvingen[$this->pdf->portefeuilledata['RapportageValuta']],$this->pdf->rapport_taal);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  
  
  function maandenNaarKwartalen($maandDataIn)
  {
//listarray($maandData);
    $tmp=array();
    $somVelden=array('stortingen','onttrekkingen','resultaatVerslagperiode','kosten','opbrengsten','ongerealiseerd','rente','gerealiseerd');
    $stapelItems=array('performance');
    $gemiddeldeVelden=array('gemiddelde');
    // listarray($maandDataIn);
    $eersteDag=array();
    $laatsteKwartaal='';
    $lastKwartaal='';
    $laasteJulDatum=0;
    foreach($maandDataIn as $totaalData)
    {
      // $beginJul=db2jul();
      $julDatum=db2jul($totaalData['datum']);
      $kwartaal=ceil(date("m",$julDatum) / 3)." ".date("Y",$julDatum);
      
      //echo $kwartaal." ".$totaalData['periode']."<br>\n";
      if(!isset($eersteDag[$kwartaal]))
        $eersteDag[$kwartaal]=substr($totaalData['periode'],0,10);
      
      if($kwartaal<>$laatsteKwartaal)
      {
        $laatsteDag[$laatsteKwartaal]=date("-m-d",$laasteJulDatum);
      }
      
      $laasteJulDatum=$julDatum;
      $laatsteKwartaal=$kwartaal;
    }
    $laatsteDag[$laatsteKwartaal]=date("-m-d",$laasteJulDatum);
    
    $aantalWaarden=0;
    foreach($maandDataIn as $totaalData)
    {
      $julDatum=db2jul($totaalData['datum']);
      $kwartaal=ceil(date("m",$julDatum) / 3)." ".date("Y",$julDatum);
      $dateBegin=$eersteDag[$kwartaal];
      if($kwartaal <> '')
      {
        
        if($kwartaal <> $lastKwartaal)
        {
          $lastKwartaal='';
          if($lastKwartaal <> '')
          {
            foreach ($gemiddeldeVelden as $item)
              $tmp['perfWaarden'][$kwartaal][$item]=$tmp['perfWaarden'][$kwartaal][$item] /($aantalWaarden+1);
          }
          $aantalWaarden=0;
          
        }
        
        if(!isset($tmp['perfWaarden'][$kwartaal]['waardeBegin']))
          $tmp['perfWaarden'][$kwartaal]['waardeBegin']=$totaalData['waardeBegin'];
        $tmp['perfWaarden'][$kwartaal]['waardeHuidige']=$totaalData['waardeHuidige'];
        $tmp['perfWaarden'][$kwartaal]['index']=$totaalData['index'];
        $tmp['perfWaarden'][$kwartaal]['beginDatum']=$dateBegin;
        $tmp['perfWaarden'][$kwartaal]['eindDatum']=date('Y-m-d',$julDatum);
        
        
        
        
        foreach($somVelden as $veld)
          $tmp['perfWaarden'][$kwartaal][$veld]+=$totaalData[$veld];
        
        foreach ($stapelItems as $item)
          $tmp['perfWaarden'][$kwartaal][$item] = ((($tmp['perfWaarden'][$kwartaal][$item]/100+1)  * ($totaalData[$item]/100+1))-1)*100;
        
        foreach ($gemiddeldeVelden as $item)
          $tmp['perfWaarden'][$kwartaal][$item] += $totaalData[$item];
        
        $lastKwartaal=$kwartaal;
        $aantalWaarden++;
      }
    }
    foreach ($gemiddeldeVelden as $item)
      $tmp['perfWaarden'][$kwartaal][$item] =$tmp['perfWaarden'][$kwartaal][$item]/($aantalWaarden+1);
    //foreach ($stapelItems as $item)
    //   $tmp['perfWaarden'][$kwartaal.$dateEnd][$item] =$tmp['perfWaarden'][$kwartaal.$dateEnd][$item]-1;
    
    
    //listarray($tmp);
    return $tmp;
  }


  function writeRapport()
	{

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
    
    $eind = $this->rapportageDatum;
    $datumStop  = db2jul($eind);
    $start=$this->pdf->PortefeuilleStartdatum;
    if(db2jul($start)<db2jul((date("Y",$datumStop)-5).'-01-01'))
      $start=(date("Y",$datumStop)-5).'-01-01';



$index = new indexHerberekening();
$indexWaarden = $index->getWaarden($this->pdf->PortefeuilleStartdatum,$eind,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);
$kwartaalPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1
$indexWaardenKwartaal = $index->getWaarden(date('Y-m-d',$kwartaalPeriode),$eind,array($this->portefeuille,$this->pdf->portefeuilles),'','maanden',$this->pdf->rapportageValuta);
$tmp=$this->maandenNaarKwartalen($indexWaardenKwartaal);
$indexWaardenKwartaal=$tmp['perfWaarden'];


$kwartaalPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1
$n=0;
$minVal = 99;
$maxVal = 101;
$aantalWaarden=count($indexWaarden);
$lijngrafiek=array();
foreach ($indexWaarden as $id=>$data)
{ //echo "". $data['datum']." ".$data['performance']."<br>\n";
  if($data['performance'] == -100)
    $data['performance']=0;
  $lijngrafiek['portefeuille'][$data['datum']]=$data['index'];
  $jaar=substr($data['datum'],0,4);
  $juldate=db2jul($data['datum']);
  if($juldate<db2jul($start))
    continue;
  
  if($juldate < $kwartaalPeriode)
  {
    if(empty($jaarPerf[$jaar]))
      $jaarPerf[$jaar]=100;
    $jaarPerf[$jaar] =($jaarPerf[$jaar]*(100+$data['performance'])/100);

    if(!isset($jaarWaarden[$jaar]['waardeBegin']))
      $jaarWaarden[$jaar]['waardeBegin']=$data['waardeBegin'];

    $jaarWaarden[$jaar]['stortingen']+=$data['stortingen'];
    $jaarWaarden[$jaar]['onttrekkingen']+=$data['onttrekkingen'];

    if(substr($data['datum'],5,5)=='12-31')
    {
       $grafiekData['jaren']['portefeuille'][]=$jaarPerf[$jaar]-100;
       $grafiekData['jaren']['datum'][]= $jaar;
       $tmp=array_merge($data,$jaarWaarden[$jaar]);
       $grafiekData['jaren']['waarde'][]=$tmp;
    }
  }


  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] = ($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$jaar]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];
  if(substr($data['datum'],5,5) == '12-31' || $aantalWaarden == $id)
  {
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;
    $indexTabel[$jaar]['portefeuille']['cumulatief'] = $data['index'];
  }
}

foreach ($indexWaardenKwartaal as $id=>$data)
{
  if($data['performance'] == -100)
    $data['performance']=0;

  $jaar=substr($data['eindDatum'],0,4);
  $juldate=db2jul($data['eindDatum']);
  $grafiekData['kwartalen']['portefeuille'][]=$data['performance'];
  //$grafiekData['kwartalen']['datum'][]= "Q".(floor(date("m",$juldate)/4)+1)."-".date("Y",$juldate);
  $grafiekData['kwartalen']['datum'][]= ceil(date("m",$juldate)/3)."e kwartaal ".date("Y",$juldate);
  $grafiekData2['portefeuille'][]=$data['waardeHuidige'];
  $grafiekData['kwartalen']['waarde'][]=$data;
}





$this->pdf->CellBorders=array();
    $this->pdf->setY(30);
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);

    
    $widths=array(35,30,28,28,28);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),array_sum($widths),7,'F');
    $this->pdf->Rect($this->pdf->marge+array_sum($widths)+20,$this->pdf->getY(),105,7,'F');
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->ln(1);
    $this->pdf->SetTextColor(255);
    
    $this->pdf->Cell(array_sum($widths)+20,4,vertaalTekst('Vermogens- en rendementsontwikkeling van de portefeuille',$this->pdf->rapport_taal));
    $this->pdf->Cell(100,4,vertaalTekst("Rendement",$this->pdf->rapport_taal));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->ln(3);

    $this->pdf->ln();
    $this->pdf->CellBorders = array(array('U'),array('U'),array('U'),array('U'),array('U'),array('U'));
    $this->pdf->setWidths($widths);
    $this->pdf->setAligns(array('L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $this->pdf->Row(array(vertaalTekst('Periode',$this->pdf->rapport_taal), vertaalTekst('Vermogen',$this->pdf->rapport_taal), vertaalTekst('Storting / Onttrekking',$this->pdf->rapport_taal),
                      vertaalTekst('Resultaat',$this->pdf->rapport_taal), vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n%"));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->Row(array(vertaalTekst('Jaar',$this->pdf->rapport_taal),'','','',''));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    foreach($grafiekData['jaren']['datum'] as $i=>$datum)
    {
      $this->pdf->Row(array($datum,
                        $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige'],0),
                        $this->formatGetal($grafiekData['jaren']['waarde'][$i]['stortingen']-$grafiekData['jaren']['waarde'][$i]['onttrekkingen'],0),
                        $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige']-$grafiekData['jaren']['waarde'][$i]['waardeBegin']+$grafiekData['jaren']['waarde'][$i]['onttrekkingen']-$grafiekData['jaren']['waarde'][$i]['stortingen'],0),
                        $this->formatGetal($indexTabel[$datum]['portefeuille']['jaar']-100,2)));
    }

    $this->pdf->ln();
    $this->pdf->setAligns(array('L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->Row(array(vertaalTekst('Kwartalen',$this->pdf->rapport_taal),'','','',''));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    foreach($grafiekData['kwartalen']['datum'] as $i=>$datum)
    {
      $this->pdf->Row(array($datum,
      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'],0),
      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['stortingen']-$grafiekData['kwartalen']['waarde'][$i]['onttrekkingen'],0),
      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige']-$grafiekData['kwartalen']['waarde'][$i]['waardeBegin']+$grafiekData['kwartalen']['waarde'][$i]['onttrekkingen']-$grafiekData['kwartalen']['waarde'][$i]['stortingen'],0),
      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['performance'],2)));
    }

$this->pdf->setXY(185,44);
$this->lineGrafiek(95,50,$lijngrafiek);

$this->toonGrafieken();


$this->pdf->CellBorders=array();




	}



  function lineGrafiek($w, $h, $grafiekData)
  {
  
    $horDiv=5;
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
  
    $color=array(255,255,255);
    $this->pdf->SetLineWidth(0.3);
  
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    if($this->pdf->rapport_layout == 8)
      $procentWhiteSpace = 20;
    else
      $procentWhiteSpace = 5;
    $maxVal=max($grafiekData['portefeuille']);
    $minVal=min($grafiekData['portefeuille']);
    $maxVal = $maxVal * (1 + ($procentWhiteSpace/100));
    $minVal = $minVal * (1 - ($procentWhiteSpace/100));
    $legendYstep = ($maxVal - $minVal) / $horDiv;
  
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
  
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
  
    $unit = $lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor(128,128,128);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'D','',array(128,128,128));
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
  
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
  
    $nulpunt = $YDiag + (($maxVal-100) * $waardeCorrectie);
    $n=0;
  
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
      $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
        break;
    }
  
    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte) ." %");
    
      $n++;
      if($n >20)
        break;
    }
  
    $n=0;
    $laatsteI = count($datumArray)-1;
   // $lijnenAantal = count($grafiekData);
  
   // listarray($grafiekData);exit;

   // if($this->pdf->debug==1)
      $cubic=true;
   // else
   //   $cubic=false;
    foreach ($grafiekData as $fonds=>$data)
    {
      $oldData=$data;
      $data=array(100);
      foreach ($oldData as $datum=>$value)
      {
        $datumArray[] = $datum;
        $data[] = $value;
      }
      $kleur = array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);//55.96.145
      $yval=$YDiag + (($maxVal-100) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $kleur);
    
      if($cubic==true)
      {
        $Index = 1;
        $XLast = -1;
        foreach ( $data as $Key => $Value )
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
          $YPos = $YDiag + (($maxVal-$Value) * $waardeCorrectie) ;
          $XPos = $XDiag+($X-1)*$unit;
        
        
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
    
    
      //  listarray($Yt);
  
  
      $laatsteX=0;
      for ($i=0; $i<count($data); $i++)
      {
        if(!isset($datumPrinted[$i]))
        {
          $datumPrinted[$i] = 1;
          if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
          {
            $xPositie=$XDiag+($i+1)*$unit-6;
            if($xPositie-$laatsteX<4)
              continue;
            $this->pdf->TextWithRotation($xPositie,$YDiag+$hDiag+10,substr(vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($datumArray[$i]))],$this->pdf->rapport_taal),0,3).date("-Y",db2jul($datumArray[$i])),45);
            $laatsteX=$xPositie;
          }
        }
      
        if($data[$i] != 0)
        {
          $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
          $xval=$XDiag+($i)*$unit;
        
          if($i==0)
            $XvalLast=$XDiag;
          if($cubic == false)
            $this->pdf->line($XvalLast, $yval, $xval, $yval2,$lineStyle );
          $yval = $yval2;
          $XvalLast=$xval;
        }
      
      }

    }
    $this->pdf->SetTextColor(0,0,0);
  }
  
  function toonGrafieken()
  {
    global $__appvar;
    $DB=new DB();
    $rapportageDatum = $this->rapportageDatum;
        $portefeuille = $this->portefeuille;
    $fontsizeBackup=$this->pdf->rapport_fontsize;
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rapport_fontsize=8;
    $this->pdf->rowHeight = 4;
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal, sum(IF(TijdelijkeRapportage.beleggingscategorie='AAND',actuelePortefeuilleWaardeEuro,0)) as totaalAAND ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$rapportageDatum."' AND ".
      " portefeuille = '".$portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaardeAAND = $totaalWaarde['totaalAAND'];
    $totaalWaarde = $totaalWaarde['totaal'];
  
    //Kleuren instellen
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
  
  
    $query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";
  
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($cat = $DB->nextRecord())
    {
      $data['beleggingscategorieEind']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
      $data['beleggingscategorieEind']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
      $data['beleggingscategorieEind']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
      $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
      $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
    }
  
    $query="SELECT
TijdelijkeRapportage.valuta as categorie,
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
TijdelijkeRapportage.valutaOmschrijving as categorieOmschrijving
FROM
TijdelijkeRapportage
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'  "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY valuta
HAVING WaardeEuro <> 0
ORDER BY TijdelijkeRapportage.valutaVolgorde";
  
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($cat = $DB->nextRecord())
    {
      $data['valutaVerdeling']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
      $data['valutaVerdeling']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
      $data['valutaVerdeling']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
      $data['valutaVerdeling']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIV'][$cat['categorie']];
      $data['valutaVerdeling']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
    }
  
  
    $query="SELECT
TijdelijkeRapportage.regio,
Sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
TijdelijkeRapportage.regioOmschrijving as Omschrijving
FROM
TijdelijkeRapportage
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' AND TijdelijkeRapportage.beleggingscategorie='AAND'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY regio
HAVING WaardeEuro <> 0
ORDER BY TijdelijkeRapportage.regioVolgorde";
  
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($cat = $DB->nextRecord())
    {
      if($cat['regio']=='')
        $cat['regio']='geen';
      if($cat['Omschrijving']=='')
        $cat['Omschrijving']='geen';
    
      $data['regioVerdeling']['data'][$cat['regio']]['waardeEur']=$cat['WaardeEuro'];
      $data['regioVerdeling']['data'][$cat['regio']]['Omschrijving']=$cat['Omschrijving'];
      $data['regioVerdeling']['pieData'][$cat['Omschrijving']]= $cat['WaardeEuro']/$totaalWaardeAAND;
      $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIR'][$cat['regio']];
      $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaardeAAND*100;
    }
  
  
    $this->pdf->setXY(30,125);
//$this->pdf->setXY(65,40);
    $this->printPie($data['beleggingscategorieEind']['pieData'],$data['beleggingscategorieEind']['kleurData'], vertaalTekst('Categorieverdeling',$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($rapportageDatum)),30,30);
    $this->pdf->wLegend=0;
    $this->pdf->setXY(120,125);
//$this->pdf->setXY(175,40);
    $this->printPie($data['valutaVerdeling']['pieData'],$data['valutaVerdeling']['kleurData'], vertaalTekst('Valutaverdeling',$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($rapportageDatum)),30,30);
    $this->pdf->wLegend=0;
  
    $this->pdf->setXY(210,125);
    $this->printPie($data['regioVerdeling']['pieData'],$data['regioVerdeling']['kleurData'],vertaalTekst('Regioverdeling aandelen',$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($rapportageDatum)),30,30);
  
    $yHoogte=165;
    $this->pdf->setXY($this->pdf->marge,$yHoogte);
    foreach ($data as $type=>$typeData)
    {
      $xStart = 24 ;
      $x2 = $xStart + 2 ;
      $y1 = $yHoogte + 3;
      $n=0;
      foreach ($typeData['data'] as $categorie=>$gegevens)
      {
        if(!is_array($regelData[$n]))
          $regelData[$n]=array('','','','','','','','','','');
        if($type=='beleggingscategorieEind')
          $offset=0;
        if($type=='valutaVerdeling')
          $offset=4;
        if($type=='regioVerdeling')
          $offset=8;
      
        $x1=$xStart+$offset*22.5;
      
        $kleur=array($data[$type]['kleurData'][$gegevens['Omschrijving']]['R']['value'],
          $data[$type]['kleurData'][$gegevens['Omschrijving']]['G']['value'],
          $data[$type]['kleurData'][$gegevens['Omschrijving']]['B']['value']);
      
        $this->pdf->SetFillColor($kleur[0],$kleur[1],$kleur[2]);
        $this->pdf->Rect($x1, $y1, 2, 2, 'DF');
        $this->pdf->SetXY($x2,$y1);
      
        $y1+=4;
        
        $omschrijving=vertaalTekst($gegevens['Omschrijving'],$this->pdf->rapport_taal);

        	$omschrijvingWidth = $this->pdf->GetStringWidth('    ' . $omschrijving);
					$cellWidth = 40 - 2;
					if ($omschrijvingWidth > $cellWidth)
          {
            $dotWidth = $this->pdf->GetStringWidth('...');
            $chars = strlen($omschrijving);
            $newOmschrijving = $omschrijving;
            for ($i = 3; $i < $chars; $i++)
            {
              $omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
              if ($cellWidth > ($omschrijvingWidth + $dotWidth))
              {
                $omschrijving = substr($newOmschrijving, 0, $chars - $i) . '...';
                break;
              }
            }
          }
						
      
        $regelData[$n][0]='';
        $regelData[$n][1+$offset]=$omschrijving;
        $regelData[$n][2+$offset]=$this->formatGetal($gegevens['waardeEur'],0);
        $regelData[$n][3+$offset]=$this->formatGetal($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2).'%';
        $regelData[$n][4+$offset]='';
        $n++;
      
        $regelTotaal[$type]['waardeEur']+=$gegevens['waardeEur'];
        $regelTotaal[$type]['percentage']+=round($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2);
      }
    
    }
    $this->pdf->setXY($this->pdf->marge,$yHoogte);
  
    foreach ($regelData as $regelNr=>$regel)
    {
      ksort($regel);
      $regelData[$regelNr]=$regel;
    }
  
  
    $this->pdf->SetWidths(array(20, 40,20,15, 15, 40,20,15, 15, 40,20,15));
    $this->pdf->SetAligns(array('L', 'L','R','R',  'L',  'L','R','R',  'L',  'L','R','R'));



//
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    $this->pdf->ln(2);
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach ($regelData as $regel)
    {
      $this->pdf->row($regel);
    }
  
    $this->pdf->underlinePercentage=0.8;
    $this->pdf->CellBorders = array('','','TS','TS','','','TS','TS','','','TS','TS');
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal), $this->formatGetal($regelTotaal['beleggingscategorieEind']['waardeEur']),$this->formatGetal($regelTotaal['beleggingscategorieEind']['percentage'],0).'%','',
                      vertaalTekst('Totaal',$this->pdf->rapport_taal), $this->formatGetal($regelTotaal['valutaVerdeling']['waardeEur']),$this->formatGetal($regelTotaal['valutaVerdeling']['percentage'],0).'%'
                    ,'',vertaalTekst('Totaal',$this->pdf->rapport_taal), $this->formatGetal($regelTotaal['regioVerdeling']['waardeEur']),$this->formatGetal($regelTotaal['regioVerdeling']['percentage'],0).'%'
                    ));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
  
    $this->pdf->rapport_fontsize=$fontsizeBackup;
    $this->pdf->rowHeight = $rowHeightBackup;
  
  }
  
  
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
  {
    
    $col1=array(255,0,0); // rood
    $col2=array(0,255,0); // groen
    $col3=array(255,128,0); // oranje
    $col4=array(0,0,255); // blauw
    $col5=array(255,255,0); // geel
    $col6=array(255,0,255); // paars
    $col7=array(128,128,128); // grijs
    $col8=array(128,64,64); // bruin
    $col9=array(255,255,255); // wit
    $col0=array(0,0,0); //zwart
    $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();
    
    if(isset($kleurdata))
    {
      $grafiekKleuren = array();
      $a=0;
      while (list($key, $value) = each($kleurdata))
      {
        if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
      $grafiekKleuren = $standaardKleuren;
    
    while (list($key, $value) = each($pieData))
      if ($value < 0)
        $pieData[$key] = -1 * $value;
    
    //$this->pdf->SetXY(210, $this->pdf->headerStart);
    $y = $this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($startX-7,$y-4);
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"L");
    $this->pdf->setXY($startX,$y);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->setX($startX);
    $this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
    $hoogte = ($this->pdf->getY() - $y) + 8;
    $this->pdf->setY($y);
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->setX($startX);
    
    //	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);
    
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 4;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
    $radius=min($w,$h);
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < $this->pdf->NbVal; $i++) {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $aantal=count($data);
    foreach($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        $factor=1.5;
        
        if($i==($aantal-1))
          $angleEnd=360;
        
        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }
    
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage ;
    $x2 = $x1 + $hLegend + $margin;
    $y1 = $YDiag + ($radius) + $margin;
    /*
          for($i=0; $i<$this->pdf->NbVal; $i++) {
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
              $this->pdf->SetXY($x2,$y1);
              $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
              $y1+=$hLegend + 2;
          }
    */
  }
  
  function SetLegends($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.1f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
}
?>