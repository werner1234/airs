<?

include_once('../../indexBerekening.php');

class RapportPERFG_L109
{

  function RapportPERFG_L109($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_PERFGRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERFG_titel;
		else
			$this->pdf->rapport_titel = "Historisch rendement vanaf aanvang portefeuille";

    $this->pdf->lastPOST['perfPstart']=1;
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function writeRapport()
	{
  global $__appvar;
		$query = "SELECT startdatumMeerjarenrendement, Startdatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));


$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();

if($this->pdf->lastPOST['perfPstart'] == 1)
{
  if($this->portefeuilledata['startdatumMeerjarenrendement']<>'' && $this->portefeuilledata['startdatumMeerjarenrendement']<>'0000-00-00')
    $start=$this->portefeuilledata['startdatumMeerjarenrendement'];
  elseif($datum['id'] > 0)
  {
    if($datum['month'] <10)
      $datum['month'] = "0".$datum['month'];
    $start = $datum['year'].'-'.$datum['month'].'-01';
  }
  else
  {
    $start=$this->portefeuilledata['Startdatum'];
  }
}
else
  $start = $this->rapportageDatumVanaf;
  $eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$index = new indexHerberekening();
$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille);

$n=0;
$minVal = 99;
$maxVal = 101;
    $buffer=array();
    $rendamentWaardenTabel=array();
    $aantal=count($indexWaarden);
foreach ($indexWaarden as $id=>$data)
{

  $grafiekData['portefeuille'][$n]=$data['index'];
  $datumArray[$n] = $data['datum'];
  $jaar=substr($data['datum'],0,4);

  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] =($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$jaar]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];


  $maand=substr($data['datum'],5,5);
  if(!isset($buffer['waardeBegin']))
    $buffer['waardeBegin']= $data['waardeBegin'];
  $buffer['stortingen']+= $data['stortingen'];
  $buffer['onttrekkingen']+= $data['onttrekkingen'];
  $buffer['gerealiseerd']+= $data['gerealiseerd'];
  $buffer['ongerealiseerd']+= $data['ongerealiseerd'];
  $buffer['opbrengsten']+= $data['opbrengsten'];
  $buffer['kosten']+= $data['kosten'];
  $buffer['rente']+= $data['rente'];
  $buffer['resultaatVerslagperiode']+= $data['resultaatVerslagperiode'];
  $buffer['waardeHuidige']= $data['waardeHuidige'];
  $buffer['datum'] = $data['datum'];
  $buffer['index'] = $data['index'];
  $buffer['specifiekeIndex'] = $data['specifiekeIndex'];
  //$tmp=$buffer['performance'];
  $buffer['performance'] = ((1+$buffer['performance']/100)*(1+$data['performance']/100)-1)*100;
  //echo "$jaar ".$buffer['performance']." = ((1+".$tmp."/100)*(1+".$data['performance']."/100)-1)*100 <br>\n";
  if($maand=='12-31'||$id==($aantal))
  {
    $rendamentWaardenTabel[]=$buffer;
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;
    $indexTabel[$jaar]['portefeuille']['cumulatief'] = $data['index'];
    $indexTabel[$jaar]['portefeuille']['onttrekkingen'] =  $buffer['onttrekkingen']/$buffer['waardeBegin']*100;
  
    $buffer=array();
    
  }
  
  
  $grafiekData['portefeuille'][$n]=$data['index'];
  $datumArray[$n] = $data['datum'];


  if($data['index'] != 0)
  {
    $maxVal=max($maxVal,$data['index']);
    $minVal=min($minVal,$data['index']);
  }
  $n++;
}
    
    if(count($rendamentWaardenTabel) > 0)
    {
      $n=1;
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
      $this->pdf->underlinePercentage=0.8;
      
      //$this->pdf->SetFillColor(230,230,230);
      //$this->pdf->SetFillColor(200,240,255);
      
      $this->pdf->SetFillColor($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]);
      
      $totaalRendament=100;
      $totaalRendamentIndex=100;
      foreach ($rendamentWaardenTabel as $row)
      {
        //listarray($row);
        $resultaat = $row['Opbrengsten']-$row['Kosten'];
        $datum = db2jul($row['datum']);
        
        if($fill==true)
        {
          $this->pdf->fillCell=array();
          $fill=false;
        }
        else
        {
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
          $fill=true;
        }
        
        $this->pdf->CellBorders = array();
        $this->pdf->row(array(date("Y",$datum) ,//.' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal)
                          $this->formatGetal($row['waardeBegin'],2),
                          $this->formatGetal($row['stortingen'],2),
                          $this->formatGetal($row['onttrekkingen'],2),
                          $this->formatGetal($row['resultaatVerslagperiode'],2),
                          $this->formatGetal($row['waardeHuidige'],2),
                          $this->formatGetal($row['performance'],2),
                          $this->formatGetal($row['index']-100,2)));
        
        
        
        if(!isset($waardeBegin))
          $waardeBegin=$row['waardeBegin'];
        $totaalWaarde = $row['waardeHuidige'];
        $totaalResultaat += $row['resultaatVerslagperiode'];
        $totaalGerealiseerd += $row['gerealiseerd'];
        $totaalOngerealiseerd += $row['ongerealiseerd'];
        $totaalOpbrengsten += $row['opbrengsten'];
        $totaalKosten += $row['kosten'];
        $totaalRente += $row['rente'];
        $totaalStortingen += $row['stortingen'];
        $totaalOntrekkingen += $row['onttrekkingen'];
        $totaalRendament = $row['index'];
        
        $n++;
        $i++;
      }
      $this->pdf->fillCell=array();
      
      
      
      $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','','TS');
      $this->pdf->row(array('','','','','','','',''));
      $this->pdf->SetY($this->pdf->GetY()-4);
      
      
      $this->pdf->ln(3);
      
      //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array();
      $this->pdf->row(array('Totaal',
                        $this->formatGetal($waardeBegin,2),
                        $this->formatGetal($totaalStortingen,2),
                        $this->formatGetal($totaalOntrekkingen,2),
                        $this->formatGetal($totaalResultaat,2),
                        $this->formatGetal($totaalWaarde,2),
                        '',
                        $this->formatGetal($totaalRendament-100,2)
                      ));//$this->formatGetal($totaalRendamentIndex-100,2)
      $this->pdf->CellBorders = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
    }

$indexTabelFondsen = array('portefeuille',$this->portefeuilledata['SpecifiekeIndex']);

$tmpArray0 = array('','');
$tmpArray1 = array('','Jaar');
/*
foreach ($indexTabelFondsen as $fonds)
{

  array_push($tmpArray0,($indexNaam[$fonds] <> ""?"benchmark":$fonds));
  array_push($tmpArray1,"per jaar");
  array_push($tmpArray1,"cumu.");
}
$this->pdf->setY(35);
$this->pdf->Row(array(''));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', 8);
$this->pdf->setAligns(array('L','L','C','C'));
$this->pdf->CellBorders = array('',array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array(200,15,15+15,15+15));
$this->pdf->Row($tmpArray0);
$this->pdf->CellBorders = array('',array('U','L','R'),'U',array('U','R'),'U',array('U','R'));
$this->pdf->setWidths(array(200,15,15,15,15,15));
$this->pdf->setAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
$this->pdf->Row($tmpArray1);

//listarray($indexTabel);
foreach ($indexTabel as $datum=>$fondsen)
{
  if(is_numeric($datum))
  {
    $tmpArray = array('');
    array_push($tmpArray,$datum);

    foreach ($indexTabelFondsen as $fonds)
    {
      $waarden = $indexTabel[$datum][$fonds];
   //  echo $fonds." "; listarray($waarden);
      if(in_array($fonds,$indexTabelFondsen))
      {
        if(!empty($waarden['jaar']))
          array_push($tmpArray,$this->formatGetal(($waarden['jaar']-100),1)."%");
        else
          array_push($tmpArray,"0,0%");

        if(!empty($waarden['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($waarden['cumulatief']-100),1)."%");
        elseif(!empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($indexTabel['cumulatief'][$fonds]['cumulatief']-100),1)."%");
        else
          array_push($tmpArray,"");

      }
    }
    $this->pdf->Row($tmpArray);
  }
}
*/

$this->pdf->CellBorders=array();

$YendIndex = 110;

$w=135;
$h=70;
$horDiv = 10;


//$this->pdf->setXY(15,30);
 //$this->pdf->SetFont($this->pdf->rapport_font, 'B', 16);
 // $this->pdf->Multicell($w,4,"Index Grafiek",'','C');
  $this->pdf->setXY(20,95);

    $legendDatum= $data['Datum'];
    $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);



  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    if($this->pdf->rapport_layout == 8)
      $procentWhiteSpace = 20;
    else
      $procentWhiteSpace = 5;
    $maxVal = $maxVal * (1 + ($procentWhiteSpace/100));
    $minVal = $minVal * (1 - ($procentWhiteSpace/100));

     $waardeCorrectie = $hDiag / ($maxVal - $minVal);

     $unit = $lDiag / count($grafiekData['portefeuille']);
     $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
     $this->pdf->SetTextColor(0,0,0);
     $this->pdf->SetDrawColor(0,0,0);
     $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'F','',array($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]));
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
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array($this->pdf->grijsBlauw[0],$this->pdf->grijsBlauw[1],$this->pdf->grijsBlauw[2])));
      $this->pdf->Text($XDiag-7, $i, 100-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
  }
  
  $n=0;
  for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
  {
    $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array($this->pdf->grijsBlauw[0],$this->pdf->grijsBlauw[1],$this->pdf->grijsBlauw[2])));
    if($skipNull == true)
      $skipNull = false;
    else
      $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+100 ." %");

    $n++;
    if($n >20)
       break;
  }

$n=0;
$laatsteI = count($datumArray)-1;
$lijnenAantal = count($grafiekData);


 $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
//$this->pdf->Rect($XPage, $YendIndex+$hDiag, 40, 6 * $lijnenAantal ,'F','',array(240,240,240));

if($this->pdf->debug==1)
  $cubic=true;
else
  $cubic=false;
    foreach ($grafiekData as $fonds=>$data)
    {
      $oldData=$data;
      $data=array(100);
      foreach ($oldData as $value)
        $data[]=$value;

      $kleur = $this->pdf->rapport_grafiek_color;//array(184,134,17);
      
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
  
      $lastXpos=0;
      $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
      for ($i=0; $i<count($data); $i++)
      {
        if(!isset($datumPrinted[$i]))
        {
          $datumPrinted[$i] = 1;
          if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
          {
            $xpos=$XDiag+($i+1)*$unit-6;
           // echo ($xpos-$lastXpos).' '.$datumArray[$i]."<br>\n";
            if($xpos-$lastXpos>5)
              $this->pdf->TextWithRotation($xpos,$YDiag+$hDiag+10,$maanden[date("n",db2jul($datumArray[$i]))].'-'.date("Y",db2jul($datumArray[$i])),45);
            $lastXpos=$xpos;
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
      
    
      
      //$fondsNaam = ($indexNaam[$fonds] <> "")?$indexNaam[$fonds]:$fonds;
     // $this->pdf->Text($XPage+7 , $YendIndex+$hDiag+3 +$n*6,$fondsNaam);
     // $this->pdf->Rect($XPage+5 , $YendIndex+$hDiag+2 +$n*6, 1, 1 ,'F','',$kleur);
 

         $n++;

 }
      $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
      $this->pdf->SetFillColor(0,0,0);
      $this->pdf->CellBorders = array();

$this->pdf->setXY(180,$YPage+70);
//$color=array(184,134,17);
$color=$this->pdf->rapport_grafiek_color;//array(215,188,110);
unset($indexTabel['cumulatief']);
$grafiekData=array();
foreach($indexTabel as $jaar=>$jaarData)
{ //listarray($jaarPerf);
       $grafiekData['portefeuille'][]=$jaarData['portefeuille']['jaar']-100;
 //      $grafiekData['onttrekkingen'][]=$jaarData['portefeuille']['onttrekkingen'];
       $grafiekData['datum'][]= $jaar;
}

$this->VBarDiagram(90,70,$grafiekData,'',$color);

	}


  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      $onttrekkingen = $data['onttrekkingen'];
      $data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);
  
      $this->pdf->Rect($XPage, $YPage-$h, $w, $h,'F','',array($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]));
     
      //$maxVal=0;
      $minVal=0;
      foreach($data as $i=>$value)
      {
        if($value>$maxVal)
          $maxVal=ceil($value);
        if($value<$minVal)
          $minVal=floor($value);
  
        if($onttrekkingen[$i]*-1>$maxVal)
          $maxVal=ceil($onttrekkingen[$i]*-1);
        if($onttrekkingen[$i]*-1<$minVal)
          $minVal=floor($onttrekkingen[$i]*-1);
        
        $total=$value+$onttrekkingen[$i]*-1;
        if($total>$maxVal)
          $maxVal=ceil($total);
        if($total<$minVal)
          $minVal=floor($total);
      }
      
      if($color == null)
          $color=array(155,155,155);
      //if ($maxVal == 0)
      //  $maxVal = ceil(max($data));
      //$minVal = floor(min($data));

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
//echo "$minVal $maxVal<br>\n";exit;
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
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array($this->pdf->grijsBlauw[0],$this->pdf->grijsBlauw[1],$this->pdf->grijsBlauw[2])));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array($this->pdf->grijsBlauw[0],$this->pdf->grijsBlauw[1],$this->pdf->grijsBlauw[2])));
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
      foreach($data as $x=>$val)
      {
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
  
          $onttrekking=$onttrekkingen[$x];
          $onth= ($onttrekking * $unit *-1);
  
          $this->pdf->Rect($xval, $yval, $lval, $onth, 'DF',null,array(165,186,199));

          /*
        if(abs($onth) > 3)
        {
          $this->pdf->SetXY($xval, $yPercentage);//$yval+($onth/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($onttrekking,1,',','.')."%",0,0,'C');
        }*/
  
      //  echo "$xval, $yval, $lval, $onth; <br>\n";
          if($hval>0)
            $yval+=$onth;
      //
       //  echo "$xval, $yval, $lval, $hval <br>\n";
  
        if($hval>-0)
          $yPercentage=$yval+$hval;
        else
          $yPercentage=$yval+$hval-4;
        
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
         // $this->pdf->SetTextColor(0,0,0);
          if(1)//abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yPercentage);//$yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
      //   $this->pdf->SetTextColor(0,0,0);
          $i++;
      }

      //datum onder grafiek
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      foreach ($legendDatum as $i=>$datum)
      {
       $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
       $this->pdf->SetXY($xval,$YstartGrafiek);
       $this->pdf->Cell($eBaton, 4,$datum,0,0,'C');
      }
  }
//listarray($indexWaarden);
//listarray($tmp);
}
?>