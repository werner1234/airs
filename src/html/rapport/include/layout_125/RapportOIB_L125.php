<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L125
{
	function RapportOIB_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Portefeuilleprofiel: ".$this->pdf->portefeuilledata['Risicoklasse'];

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->aandeel=1;
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  
  function VBarRisico($w, $h)
  {
  
    $DB=new DB();
  
    $query="SELECT verwachtRendement,verwachtMaxVerlies,verwachtMaxWinst FROM Risicoklassen WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."'";
    $DB->SQL($query);
    $DB->Query();
    $verwachtRendement = $DB->LookupRecord();
    
    $maxVal=ceil(max($verwachtRendement)/10)*10;
    $minVal=floor(min($verwachtRendement)/10)*10;
  
    $grafiek=array('Prognoserendement'=>$verwachtRendement['verwachtRendement'],
      'Maximaal verwachten verlies in enig jaar'=>$verwachtRendement['verwachtMaxVerlies'],
      'Maximaal verwachte winst in enig jaar'=>$verwachtRendement['verwachtMaxWinst']);
    
    $legendaWidth = 0;
    
    $numBars = 3;
    
    
    if($maxVal <= 10)
      $maxVal=10;
    
    if($minVal >= 0)
      $minVal = 0;
    elseif($minVal > -10)
      $minVal=-10;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) ; // - legenda
    
    
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
  
    $bereik = $hGrafiek/$unit;
    $horDiv = round(abs($bereik)/10);
    
  //echo $horDiv;exit;
    
   // $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->setTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
    
    
    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
  
    $n=0;
    $this->pdf->setTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
    $skipNull=false;
    for($i=$nulpunt; round($i) >= $top; $i-= $absUnit*$stapgrootte)
    {
      $skipNull=true;
      // $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-10, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }
  
    
    $n=0;
    $this->pdf->setTextColor($this->pdf->foutRood[0],$this->pdf->foutRood[1],$this->pdf->foutRood[2]);
    for($i=$nulpunt; round($i)<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      if($skipNull == true)
        $skipNull = false;
      else
      {
        //  $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek - 10, $i - 1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n * $stapgrootte * -1) . " %", 0, 0, 'R');
      }
      $n++;
      if($n >20)
        break;
    }
    

    
    
    $vBar = ($bGrafiek / ($numBars));
    $bGrafiek = $vBar * ($numBars);
    $eBaton = ($vBar * 80 / 100);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor(255,249,196);
    $i=0;
    $legendaPrinted=array();
    foreach ($grafiek as $categorie=>$val)
    {

       $YstartGrafiekLast = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (.5 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null);

        if($val>=0)
        {
          $this->pdf->SetTextColor($this->pdf->okeGroen[0], $this->pdf->okeGroen[1], $this->pdf->okeGroen[2]);
          $percentageY=$nulpunt+2;
        }
        else
        {
          $this->pdf->SetTextColor($this->pdf->foutRood[0], $this->pdf->foutRood[1], $this->pdf->foutRood[2]);
          $percentageY=$nulpunt-5;
        }
        
        
        if($percentageY>$top && $percentageY<$bodem)
          $this->pdf->SetXY($xval, $percentageY);
        else
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
      
        $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        
        
        if($legendaPrinted[$categorie] != 1)
        {
          $this->pdf->setTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
          $this->pdf->SetXY($xval,$YstartGrafiek+1);
          $this->pdf->MultiCell($eBaton,4,$categorie,0,'C');//$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda,0);
          $legendaPrinted[$categorie]=1;
        }
        
  
      
      $i++;
    }
    $this->pdf->Line($XstartGrafiek, $nulpunt, $XstartGrafiek + $bGrafiek ,$nulpunt,array('width' => 0.2,'color'=>array($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2])));
    
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
  
  function getKleuren()
  {
    $db=new DB();
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    $this->categorieKleuren=$this->kleuren['OIB'];
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
  
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }
  }
  
  
  function plotZorgBar2($barHeight,$barWidth,$zorgdata)
  {
    $DB=new DB();
    $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      $categorien[$data['Zorgplicht']]=$data['Omschrijving'];
    }
    $this->pdf->setXY(200,50);
    $hProcent=$barWidth/100;
    $marge=1;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();
    
    foreach($zorgdata as $categorie=>$data)
    {

      $data['percentage']=str_replace(',','.',$data['percentage']);
      
      $this->pdf->setXY($xPage-$marge-4,$yPage);
    //  $this->pdf->Rect($xPage, $yPage+$extraY,$hProcent*100, $barHeight,  'D');
      $this->pdf->setXY($xPage-40,$yPage);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+1);
      $this->pdf->cell(40,4,$categorien[$categorie],0,0,'L');
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->setXY($xPage-4,$yPage-$marge-4);
      $this->pdf->cell(4,4,"0%",0,0,'C');
      $this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-4);
      $this->pdf->cell(4,4,"100%",0,0,'C');
      $this->pdf->setXY($xPage+$hProcent*$data['Minimum']-1,$yPage-$marge-4);
      if($data['Minimum']<>0)
        $this->pdf->cell(4,4,"".$data['Minimum'].'%',0,0,'C');
      $this->pdf->setXY($xPage+$hProcent*$data['Maximum']-1,$yPage-$marge-4);
      if($data['Maximum']<>100)
        $this->pdf->cell(4,4,"".$data['Maximum'].'%',0,0,'C');
      
      //$this->pdf->setXY($xPage+$hProcent*$data['Norm']-2,$yPage+$marge+5);
      //$this->pdf->cell(4,4,"Norm ".$data['Norm'],0,0,'R');
      
      $this->pdf->SetFillColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
      $this->pdf->Rect($xPage, $yPage, $hProcent*$data['Minimum'], $barHeight, 'F');
      $this->pdf->SetFillColor(27,159,17);
  
      if(isset($this->kleuren['OIB'][$categorie]))
      {
        $this->pdf->SetFillColor($this->kleuren['OIB'][$categorie]['R']['value'],$this->kleuren['OIB'][$categorie]['G']['value'],$this->kleuren['OIB'][$categorie]['B']['value']);
      }
      
      $this->pdf->Rect($xPage+$hProcent*$data['Minimum'], $yPage,$hProcent*($data['Maximum']-$data['Minimum']), $barHeight, 'F');
      $this->pdf->SetFillColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
      $this->pdf->Rect($xPage+$hProcent*$data['Maximum'], $yPage, $hProcent*(100-$data['Maximum']),$barHeight, 'F');
  
      
      if($data['conclusie']=='Voldoet')
      {
        $this->pdf->SetDrawColor($this->pdf->okeGroen[0],$this->pdf->okeGroen[1],$this->pdf->okeGroen[2]);
      }
      else
      {
        $this->pdf->SetDrawColor($this->pdf->foutRood[0], $this->pdf->foutRood[1],$this->pdf->foutRood[2]);
      }
      //$this->pdf->Line($xPage+$hProcent*$data['Norm'], $yPage,$xPage+$hProcent*$data['Norm'],$yPage+$barHeight);
      $this->pdf->Line($xPage+$hProcent*$data['percentage'], $yPage,$xPage+$hProcent*$data['percentage'],$yPage+$barHeight,array('width' => 1,'cap'=>'butt'));

  
      
   //   $this->pdf->Rect($xPage,$yPage , $hProcent*$data['percentage'], $barHeight,  'DF');
      $this->pdf->setXY($xPage+$hProcent*$data['percentage']-1,$yPage+$barHeight+$marge);
      $this->pdf->cell(4,4,$this->formatGetal($data['percentage'],1).'%',0,0,'C');
      $yPage+=20;
    }
    
    $ystart=85;
    $this->pdf->TextWithRotation($xPage+$barWidth+15,$ystart,'Binnen bandbreedte',90);
    $this->pdf->Line($xPage+$barWidth+14, $ystart+2,$xPage+$barWidth+14,$ystart+6,array('width' => 1,'cap'=>'butt','color'=>$this->pdf->okeGroen));
    $this->pdf->TextWithRotation($xPage+$barWidth+23,85,'Buiten bandbreedte',90);
    $this->pdf->Line($xPage+$barWidth+22, $ystart+2,$xPage+$barWidth+22,$ystart+6,array('width' => 1,'cap'=>'butt','color'=>$this->pdf->foutRood));
    
  }
  
  
  function addZorgBar()
  {
    include_once("rapport/Zorgplichtcontrole.php");
    $zorgplicht = new Zorgplichtcontrole();
    $pdata=$this->pdf->portefeuilledata;
    $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum);
    $gebruikteCategorien=array();
    foreach($zpwaarde['categorien'] as $categorie=>$data)
    {
      if(!isset($data['fondsGekoppeld']))
      {
        $gebruikteCategorien[$categorie]=$data;
      }
    }
    foreach($zpwaarde['conclusie'] as $data)
    {
      foreach($gebruikteCategorien as $categorie=>$gebruikteCategorie)
      {
        if($data[0]==$gebruikteCategorie['Zorgplicht'])
        {
          $gebruikteCategorien[$categorie]['percentage']=$data[2];
          $gebruikteCategorien[$categorie]['conclusie']=$data[5];
        }
      }
    }
    return $gebruikteCategorien;
  }
  
  function vermogensverdeling()
  {
    $index=new indexHerberekening();
    $index->extraVerdeling='hoofdcategorie';
    $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
    $barGraph=array();
    foreach ($indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
        $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
        //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)

        foreach ($data['extra']['hoofdcategorie'] as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
        
          $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
          if($waarde <> 0)
            $categorien[$categorie]=$categorie;
        }
      }
    }
  
    $DB=new DB();
    $q="SELECT Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde
FROM
Beleggingscategorien
WHERE Beleggingscategorien.Beleggingscategorie IN('".implode("','",$categorien)."')
ORDER BY Beleggingscategorien.Afdrukvolgorde desc";
    $DB->SQL($q); //CategorienPerVermogensbeheerder.Vermogensbeheerder='$beheerder' AND
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
      $this->categorieOmschrijving[$data['Beleggingscategorie']]=$data['Omschrijving'];
    }
  
    if (count($barGraph) > 0)
    {
      $this->pdf->SetXY(20,90)		;//112
      $this->VBarDiagram(140, 45, $barGraph['Index']);
    }
    
    
  }
  
  
  
  function VBarDiagram($w, $h, $data)
  {
    
    $legendaWidth = 0;
    $grafiekPunt = array();

    foreach ($data as $datum=>$waarden)
    {
      $legenda[$datum] = date('m',db2jul($datum));
      $n=0;
      $minVal=0;
      $maxVal=100;

      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(isset($waarden[$categorie]))
        {
          $waarde=$waarden[$categorie];
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          
          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));
          
          if($waarde < 0)
          {
            unset($grafiek[$datum][$categorie]);
            $grafiekNegatief[$datum][$categorie]=$waarde;
          }
          else
            $grafiekNegatief[$datum][$categorie]=0;
          
          
          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }
    }
    
    
    
    $numBars = 12;
    if($color == null)
    {
      $color=array(155,155,155);
    }
    
    
    if($maxVal <= 100)
      $maxVal=100;
    elseif($maxVal < 125)
      $maxVal=125;
    
    if($minVal >= 0)
      $minVal = 0;
    elseif($minVal > -25)
      $minVal=-25;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda
    
    $n=0;
    foreach (array_reverse($this->categorieVolgorde) as $categorie)
    {
      if(is_array($grafiekCategorie[$categorie]))
      {
        //$this->pdf->Rect($XstartGrafiek+3+$n*30 , $YstartGrafiek+10+2, 2, 2, 'F',null,$colors[$categorie]);//-$hGrafiek+$n*10
        $this->pdf->Circle($XstartGrafiek+4+$n*30 , $YstartGrafiek+10+3, 1, 0, 360,'F', null, $colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+6+$n*30 ,$YstartGrafiek+10+1.5 );
        $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
        $n++;
      }
    }
    
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
    
    
    $horDiv = 5;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
   // $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->setTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
    
    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
    //  $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-10, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
     // $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-10, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
    $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
    $eBaton = ($vBar * 85 / 100);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    
    foreach ($grafiek as $datum=>$data)
    {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
/*
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
*/
        if($legendaPrinted[$datum] != 1)
        {
          $this->pdf->SetXY($xval,$YstartGrafiek+1);
          $this->pdf->Cell($eBaton,4,$legenda[$datum],0,0,'C');//$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
        }
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'F',null,array(128,128,128));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$datum] = 1;
      }
      $i++;
    }
    
    $i=0;
    $YstartGrafiekLast=array();
    foreach ($grafiekNegatief as $datum=>$data)
    {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
      }
      $i++;
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
  function writeRapport()
  {
    global $__appvar;
  
    $this->pdf->addPage();
  
    $this->getKleuren();
    
    subHeader_L125($this->pdf, 28, array(140, 140), array('Vermogensverdeling', 'Mandaatcontrole in % '));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->vermogensverdeling();
  
    $gebruikteCategorie=$this->addZorgBar();
    $this->plotZorgBar2(5,60,$gebruikteCategorie);
  
  
    $y=115;
    subHeader_L125($this->pdf, $y, array(100, 280), array('Risicoanalyse van uw portefeuilleprofiel', ''));
  
  
  
    $this->pdf->SetXY(20,130+50);
    $this->VBarRisico(120,50);
  
    $this->pdf->ln();
    $this->pdf->setTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
  
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $txt="Op basis van historische gegevens valt te verwachten dat de fluctuaties van het rendement in 95 procent van de beleggingsjaren binnen deze uiterste waarden vallen. Het verschil in rendement kan groter zijn wanneer er in een jaar zeer uitzonderlijke gebeurtenissen plaatsvinden, zoals bijvoorbeeld in het laatste decennium: de internetbubbel, de aanslag op de Twin Towers, de kredietcrisis en niet te vergeten de coronacrisis.";
    $this->pdf->SetXY(160,135);
    $this->pdf->MultiCell(90,6,$txt,0,'L');
  
  
    return '';
    
    

  }
  
  
}

