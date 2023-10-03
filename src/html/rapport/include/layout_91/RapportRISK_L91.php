<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/25 15:37:41 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIB_L91.php,v $
Revision 1.2  2020/07/25 15:37:41  rvv
*** empty log message ***

Revision 1.1  2020/07/01 16:22:28  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportRISK_L91
{
	function RapportRISK_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Rendement en risico";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->checkPng=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAC0AAAA1CAMAAADiQZJeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjNCMjBERDg2Q0U3NjExRUE4NzM3ODU3RUM2QkI2RUFGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjNCMjBERDg3Q0U3NjExRUE4NzM3ODU3RUM2QkI2RUFGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6M0IyMEREODRDRTc2MTFFQTg3Mzc4NTdFQzZCQjZFQUYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6M0IyMEREODVDRTc2MTFFQTg3Mzc4NTdFQzZCQjZFQUYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5O95YdAAAAMFBMVEX0+vSWz5JUsUzn9Oas2ajF5MLX7NV4wXJFqzxuvWc9pzRht1qEx3/+/v46pjH////UjrOAAAACYUlEQVR42rSWy6LrIAhFUfBRleT///YCxuZRk5PJzaADs4qIG3ZgnTwcXODZC5itUc3ewTsayC/Lkhu+oWPLiz7lE/mZZkYLXHKRX++Q+ZZmQPpo2Jywb+ApAk9oITG4ainICVlPanwLEWFsAYwxxhCIWi2dTUFDcGy+L9TkKAShUEK6Wr3vBzOWkLfdwsbbuq+1RaABar61BdzzZMntc3hdHLg8SNkwXgsKMbhUc9+iJKVLTwxhftuox3Itb3QmmIOH2kL0cl9Gh/Xv5z/Ty3saO13e0xXaE32QoNBF6IdMkOir8R77IW90XspwzvuWRhVFxncVNHhpcKCTajDTTB7WPe0rySB0U7rQ3u4EAxbdlR022oH9jqUku2kVGJKK2B2GhObgTFtpLNXFcMZ2hdeuVasj7wEUR03jDK96izIAJJ4f672TkrbXBYaPVhPWVBYfvqp3fmu8M7yiBmVYzyXsQ1APdC6oFGNpMn2CNtxBPYr/wHrIhYQGPWY8tCBVT9f5ahVFobnpvDs3+U9Th2qVkx2DTYrnntdEnM5BZknFB356otWPWWOSyechtI0/VSNsQvd0P384StZZfQvsr3LRKd7hbEKwzW1U60XJ5fFDHt4s0WqhslWFzeHDS9i7ys9xLfVoCzik5h1PbFl1M8wQxrHTr6S/IqsBLp6mF7DkTzzr1FrZD3j3S8Z0dlS5OTKXqnttDwfbLEgEKKISswt1c9obLx6O6iJGMvss528JuHwRdIfMyQ+n5YevgrOjJgJ+/OIQR22WT/GNfrQAE7cLlMRqw0Q3cGOocfrinwADADDacgmRnW/iAAAAAElFTkSuQmCC');
    
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  
  function fondsPerformance($fonds,$vanaf,$tot)
  {
    $perf=getFondsPerformance($fonds,$vanaf,$tot);
    return $perf;
  }
  
  function addPerfGrafiek()
  {
    $index = new indexHerberekening();
    $indexData = $index->getWaarden($this->rapportageDatumVanaf,$this->rapportageDatum ,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);
    $perfGrafiek=array();
    foreach ($indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $perfGrafiek['datum'][] = date("M y", db2jul($data['datum']));
        $perfGrafiek['portefeuille'][] = $data['index']-100;
      }
    }
    
   // $perfGrafiek['legenda']=array('Portefeuille');//,$this->index['Omschrijving']);
    
    $this->pdf->setXY(165,120);
    $portKleur=array($this->pdf->rapport_grafiek_color[0],$this->pdf->rapport_grafiek_color[1],$this->pdf->rapport_grafiek_color[2]);
    $indexKleur=array(0,49,60);
    $perfGrafiek['titel']='Portefeuille rendement';
    $this->LineDiagram(120, 50, $perfGrafiek,array($portKleur,$indexKleur),0,0,6,5,true);//50
    
    
  }
  
  
  
  function writeRapport()
  {
    global $__appvar;
    $query = "SELECT  Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, Client,Portefeuilles.Vermogensbeheerder, SpecifiekeIndex, Risicoklassen.verwachtRendement, Risicoklassen.klasseStd
FROM
Portefeuilles
LEFT JOIN Risicoklassen ON Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder AND Portefeuilles.Risicoklasse = Risicoklassen.Risicoklasse
WHERE Portefeuille = '" . $this->portefeuille . "' ";
    $DB = new DB();
    $DB->SQL($query);//echo $query;exit;
    $DB->Query();
    $portefeuilledata = $DB->nextRecord();
    
    
    $this->pdf->AddPage();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    $this->pdf->ln();
    

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $zorgplicht=new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($portefeuilledata,$this->rapportageDatum );
    /*
    $conclusie=array();
    foreach($zpwaarde['conclusie'] as $waarden)
    {
      $conclusie[$waarden[0]]=$waarden[5];
    }
    */
    $ystart=$this->pdf->getY();
    $extraX=150;
    $this->pdf->SetWidths(array($extraX,100));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Vermogensverdeling risicoprofiel '.$portefeuilledata['Risicoklasse']),'','');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    $this->pdf->SetWidths(array($extraX,35,20,20,20,20,15));
    $this->pdf->SetAligns(array('C','R', 'R', 'R', 'R', 'R', 'R'));
    $this->pdf->CellBorders = array('','T','T','T','T','T','T');
    $this->pdf->row(array('','','','','','',''));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders =array();
    $this->pdf->row(array('','','% Minimum','% Neutraal','% Maximum','% Actueel'));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders = array('','','U','U','U','U','U');
    $this->pdf->row(array('','','','','','',''));
    $this->pdf->CellBorders =array();
    $this->pdf->ln(2);
    krsort($zpwaarde['conclusieDetail']);
    foreach($zpwaarde['conclusieDetail'] as $categorie=>$details)
    {
      $this->pdf->row(array('',$categorie,$this->formatGetal($details['minimum'],0).'%',$this->formatGetal($details['norm'],0).'%',$this->formatGetal($details['maximum'],0).'%',$this->formatGetal($details['percentage'],0).'%',''));//,$conclusie[$categorie]
      if($zpwaarde['voldoet'] =='Ja')
        $this->pdf->memImage($this->checkPng,$this->pdf->marge+$extraX+121,$this->pdf->getY()-4,4);
      
      $this->pdf->ln(2);
    }
    $this->pdf->CellBorders = array('','T','T','T','T','T','T');
    $this->pdf->row(array('','','','','','',''));
    
    
    $this->pdf->setY($ystart+40);
    $this->pdf->SetWidths(array($extraX,130));
    $this->pdf->CellBorders = array('','T','T');
    $this->pdf->SetAligns(array('L', 'L', 'R'));
    $this->pdf->row(array('','',''));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders =array();
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Risicomaatstaven en historisch risico en rendement'));
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln(-2);
    $this->pdf->CellBorders = array('','U','U');
    $this->pdf->row(array('','',''));
    $this->pdf->CellBorders =array();
    $this->pdf->ln(2);
    $this->pdf->SetAligns(array('L', 'R', 'R'));
    $this->pdf->SetWidths(array($extraX,80,50));
    
    $afm=AFMstd($this->portefeuille,$this->rapportageDatum);
  
    $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
    $stdev->addReeks('totaal');
    $stdev->addReeks('benchmark',$portefeuilledata['SpecifiekeIndex']);
    $stdev->berekenWaarden();
    //$riskData=$stdev->riskAnalyze();
    //listarray($riskData['maxDrawdown']);exit;
  
  
    $risicomaatstaven=array(
      array('','AFM - standaarddeviatie', $this->formatGetal($afm['std'],1).'%'),
      array('','Verwacht rendement per jaar',$this->formatGetal($portefeuilledata['verwachtRendement'],1).'%'),
      array('','Historisch risico per jaar', $this->formatGetal($portefeuilledata['klasseStd'],1).'%'),
      array('','Maximum drawdown',$this->formatGetal($portefeuilledata['verwachtRendement']-(2*$portefeuilledata['klasseStd']),1).'%'),
        );
    foreach($risicomaatstaven as $regel)
    {
      $this->pdf->row($regel);
      $this->pdf->ln(2);
    }
  
    
    
    $this->pdf->setY($ystart);
    $this->printRendement($this->portefeuille,$this->rapportageDatum,$this->rapportageDatumVanaf);
    $this->pdf->setY($ystart+40);
    $this->indexVergelijking();

    $db = new DB();
    $query = "SELECT Portefeuilles.Memo FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
    $memo = $db->lookupRecordByQuery($query);
    if ( isset ($memo['Memo']) && ! empty ($memo['Memo']) ) {
      $this->pdf->ln(2);
      $this->pdf->SetWidths(array(1, 30, 110));
      $this->pdf->CellBorders = array('', '', '');
      $this->pdf->SetAligns(array('L', 'L', 'L'));
      $this->pdf->row(array('', 'Bijzonderheden', $memo['Memo']));
    }
  
    $this->addPerfGrafiek();//$stdev
   // $this->OIBgrafiek();
    
    unset($this->pdf->CellBorders);
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
    
    //$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'D','',array($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]));
    
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
    if($maxVal<0)
      $maxVal=0;
    
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
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
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
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
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
  
  function OIBgrafiek()
  {
    $db=new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder='$beheerder'";
    $db->SQL($query);
    $db->Query();
    $this->kleuren = array();
    $data = $db->nextRecord();
    $alleKleuren = unserialize($data['grafiek_kleur']);
    
    $typen = array('OIB' => 'Beleggingscategorien.Beleggingscategorie');//, 'OIR' => 'Regios.Regio', 'OIS' => 'Beleggingssectoren.Beleggingssector');
    
    foreach ($alleKleuren as $type => $kleuren)
    {
      if (isset($typen[$type]))
      {
        $parts = explode(".", $typen[$type]);
        $query = "SELECT " . $parts[1] . " as verdeling, Omschrijving FROM " . $parts[0] . "";
        $db->SQL($query);
        $db->Query();
        $omschrijvingen = array();
        while ($data = $db->nextRecord())
        {
          $omschrijvingen[$data['verdeling']] = $data['Omschrijving'];
        }
        $omschrijvingen['Liquiditeiten']='Liquiditeiten';
        foreach ($kleuren as $verdeling => $kleurData)
        {
          $this->kleuren[$parts[0]][$omschrijvingen[$verdeling]] = array($kleurData['R']['value'], $kleurData['G']['value'], $kleurData['B']['value']);
        }
      }
    }
    $doorkijkVerdeling = $this->bepaalWegingNormaal('Beleggingscategorien');
    $grafiekdata = array();
    $grafiekTonen = true;
    foreach ($doorkijkVerdeling['categorien'] as $categorie => $percentage)
    {
    
      $grafiekdata[$categorie]['kleur'] = $this->kleuren['Beleggingscategorien'][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
      $grafiekdata[$categorie]['percentage'] = $percentage;
    }
    if ($grafiekTonen == true)
    {
      $this->printPie($grafiekdata, 245, 130,'' );
      $this->pdf->setY(165);
      $this->pdf->setWidths(array(240,50));
      $this->pdf->setAligns(array('L','L'));
      $this->pdf->CellBorders =array();
      foreach($grafiekdata as $categorie=>$catData)
      {
        $this->pdf->row(array('',$this->formatGetal($catData['percentage'],1).'% '.$categorie));
      }
    } //+$yOffset);
  }
  
  
  function printPie($kleurdata, $xstart, $ystart, $titel)
  {
    $col1 = array(255, 0, 0); // rood
    $col2 = array(0, 255, 0); // groen
    $col3 = array(255, 128, 0); // oranje
    $col4 = array(0, 0, 255); // blauw
    $col5 = array(255, 255, 0); // geel
    $col6 = array(255, 0, 255); // paars
    $col7 = array(128, 128, 128); // grijs
    $col8 = array(128, 64, 64); // bruin
    $col9 = array(255, 255, 255); // wit
    $col0 = array(0, 0, 0); //zwart
    $standaardKleuren = array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col0);
    $pieData = array();
    if ($kleurdata)
    {
      $sorted = array();
      $percentages = array();
      $kleur = array();
      $valuta = array();
      
      foreach ($kleurdata as $key => $data)
      {
        $percentages[] = $data['percentage'];
        $kleur[] = $data['kleur'];
        $valuta[] = $key;
      }
      //arsort($percentages);
      
      foreach ($percentages as $key => $percentage)
      {
        $sorted[$valuta[$key]]['kleur'] = $kleur[$key];
        $sorted[$valuta[$key]]['percentage'] = $percentage;
      }
      $kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
      $grafiekKleuren = array();
      
      $a = 0;
      foreach ($kleurdata as $key => $value)
      {
        if ($value['kleur'][0] == 0 && $value['kleur'][1] == 0 && $value['kleur'][2] == 0)
        {
          $grafiekKleuren[] = $standaardKleuren[$a];
        }
        else
        {
          $grafiekKleuren[] = array($value['kleur'][0], $value['kleur'][1], $value['kleur'][2]);
        }
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
    {
      $grafiekKleuren = $standaardKleuren;
    }
    
    // $this->pdf->SetTextColor(255, 255, 255);
    
    $trapport_printpie = true;
    foreach ($pieData as $key => $value)
    {
      if (round($value,2) < 0)
      {
        $trapport_printpie = false;
      }
    }
    
    if ($trapport_printpie)
    {
      $this->pdf->SetXY($xstart, $ystart - 4);
      $y = $this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', 10);
      if($this->pdf->portefeuilledata['Layout']==95)
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'], $this->pdf->rapport_kop_fontcolor['g'], $this->pdf->rapport_kop_fontcolor['b']);
      $this->pdf->Cell(50, 4, vertaalTekst($titel, $this->pdf->rapport_taal), 0, 1, "C");
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $this->pdf->SetXY($xstart, $ystart + 2);
      $this->PieChart(160, 30, $pieData, '%l (%p)', $grafiekKleuren);
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
    else
    {
      $this->pdf->SetXY($xstart, $ystart - 4);
      $y = $this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', 10);
      if($this->pdf->portefeuilledata['Layout']==95)
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'], $this->pdf->rapport_kop_fontcolor['g'], $this->pdf->rapport_kop_fontcolor['b']);
      $this->pdf->Cell(50, 4, vertaalTekst($titel, $this->pdf->rapport_taal), 0, 1, "C");
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $this->pdf->SetXY($xstart-10, $ystart);
      $this->BarDiagram(70, $pieData, '%l (%p)', $grafiekKleuren, '');
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
  }
  
  function bepaalWegingNormaal($doorkijkSoort, $belCategorie = '')
  {
    global $__appvar;
    if (is_array($belCategorie))
    {
      $fondsFilter = "AND Beleggingscategorie IN('".implode("','",$belCategorie)."')";
    }
    elseif ($belCategorie <> '')
    {
      $fondsFilter = "AND Beleggingscategorie='$belCategorie'";
    }
    else
    {
      $fondsFilter = '';
    }
    
    $db = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();
    
    
    $vertaling = array('Beleggingscategorien' => 'Beleggingscategorie', 'Beleggingssectoren' => 'Beleggingssector', 'Regios' => 'Regio');
    $query = "SELECT TijdelijkeRapportage.type, if(" . $vertaling[$doorkijkSoort] . "Volgorde = 0,127," . $vertaling[$doorkijkSoort] . "Volgorde) as volgorde, sum(actuelePortefeuilleWaardeEuro) as waardeEUR, " . $vertaling[$doorkijkSoort] . " as verdeling , " . $vertaling[$doorkijkSoort] . "Omschrijving as Omschrijving
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='" . $this->rapportageDatum . "'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'] . "
					GROUP BY " . $vertaling[$doorkijkSoort] . ", TijdelijkeRapportage.type
					ORDER BY volgorde";
    
    $db = new DB();
    $db->SQL($query);
    $db->Query();
    
    $doorkijkVerdeling = array();
    while ($row = $db->nextRecord())
    {
      if($row['type']=='rekening')
        $categorie='Liquiditeiten';
      else
        $categorie = $row['Omschrijving'];
      if ($categorie == '')
      {
        $categorie = 'Geen ' . $vertaling[$doorkijkSoort];
      }
      $totaalPercentage = $row['waardeEUR'] / $totaalWaarde['totaal'] * 100;
      $doorkijkVerdeling['categorien'][$categorie] += $totaalPercentage;
      $doorkijkVerdeling['details'][$categorie]['percentage'] += $totaalPercentage;
      $doorkijkVerdeling['details'][$categorie]['waardeEUR'] += $row['waardeEUR'];
    }
    //listarray($doorkijkVerdeling);
    return $doorkijkVerdeling;
  }
  
  
  function PieChart($w, $h, $data, $format, $colors = null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    //	$this->pdf->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 2, $h - $margin * 2); //
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < count($data); $i++)
      {
        $gray = $i * intval(255 / count($data));
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
    
    //Sectors
    $sum = array_sum($data);
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $angle = 0;
    //echo "<br>\n";
    foreach ($data as $val)
    {
      //$angle = round(($val * 360) / doubleval($sum),1);
      $angle = round(floor(($val * 360) / doubleval($sum) * 5) / 5, 1);
      //echo "$angle <br>\n"; ob_flush();
      if ($angle > 1)
      {
        $angleEnd = $angleStart + $angle;
        // echo "$angleEnd = $angleStart + $angle <br>\n"; ob_flush();
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      else
      {
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    
  }
  
  function printRendement($portefeuille, $rapportageDatum, $rapportageDatumVanaf, $kort=false)
  {
    global $__appvar;
    // vergelijk met begin Periode rapport.
    
    $DB= new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
      " portefeuille = '".$portefeuille."' ".
      $__appvar['TijdelijkeRapportageMaakUniek'];
    
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $vergelijkWaarde = $DB->nextRecord();
    $vergelijkWaarde = $vergelijkWaarde['totaal'] /  getValutaKoers($this->pdf->rapportageValuta,$rapportageDatumVanaf);
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$rapportageDatum."' AND ".
      " portefeuille = '".$portefeuille."' ".
      $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $actueleWaardePortefeuille = $DB->nextRecord();
    $actueleWaardePortefeuille = $actueleWaardePortefeuille['totaal']  / $this->pdf->ValutaKoersEind;
    
    $resultaat = ($actueleWaardePortefeuille -
      $vergelijkWaarde -
      getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta) +
      getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->pdf->rapportageValuta)
    );
    
    $performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
  
  
  
  
    $this->pdf->SetWidths(array(1,139));
    $this->pdf->CellBorders =array();
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L', 'L', 'R'));
    $this->pdf->row(array('','Rendement & resultaat over verslagperiode'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
  
    
    $this->pdf->CellBorders = array('','T','T');
    $this->pdf->row(array('','',''));
    
    unset($this->pdf->CellBorders);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->SetWidths(array(1,60,79));
    $this->pdf->SetAligns(array('L', 'R', 'R'));
    $this->pdf->row(array('',vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->pdf->formatGetal($performance,2)."%"));
    $this->pdf->ln(2);
    $this->pdf->row(array('',vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->pdf->formatGetal($resultaat,2)));
    $this->pdf->ln(2);
    $this->pdf->CellBorders = array('','T','T','T');
    $this->pdf->row(array('','',''));
    
  }
  
  function indexVergelijking()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    else
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
  
  
    $DB=new DB();
    $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    //$this->pdf->portefeuilledata['Vermogensbeheerder']='ibe';
    $query="SELECT Indices.Beursindex,Fondsen.Omschrijving,Fondsen.Valuta,Indices.toelichting
FROM Indices Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
  
  
    $DB->SQL($query);
    $DB->Query();
    $benchmarkCategorie=array();
    $indexData=array();
    while($index = $DB->nextRecord())
    {
      if($index['toelichting'] == '')
        $index['toelichting']='Overige';
    
      $benchmarkCategorie[$index['toelichting']][]=$index['Beursindex'];
    
      $indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
      }
    
      $indexData[$index['Beursindex']]['performanceJaar'] = $this->fondsPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
      $indexData[$index['Beursindex']]['performance'] =    $this->fondsPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);
    }
  
  
  
    $this->pdf->SetWidths(array(1,101));
    $this->pdf->CellBorders = array('','','');
    $this->pdf->SetAligns(array('L', 'L', 'R'));
    $this->pdf->row(array('','',''));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders =array();
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Index-vergelijking'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln(-6);
    
    
  
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(1,59,26.6667,26.6667,26.6667,26.6667));
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    //$this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8 , 'F');
    //$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
  
    if($perioden['jan']==$perioden['begin']||1)
    {
      $this->pdf->CellBorders = array('','U','U','U','U');
      $this->pdf->row(array("","","Start periode\n".date("d-m-Y",db2jul($perioden['begin'])),"\n".date("d-m-Y",db2jul($perioden['eind'])),"\nPerformance %"));
    }
    else
    {
      $this->pdf->CellBorders = array('','U','U','U','U','U','U');
      $this->pdf->row(array("","\nIndex","Koers ".date("d-m-Y",db2jul($perioden['jan'])),"Koers ".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  
  
    foreach ($benchmarkCategorie as $categorie=>$fondsen)
    {
      //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      //$this->pdf->row(array("",$categorie));
      //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach ($fondsen as $fonds)
      {
        $fondsData=$indexData[$fonds];
        if($perioden['jan']==$perioden['begin']||1)
        {
          $this->pdf->row(array('',$fondsData['Omschrijving'],
                            $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
                            $this->formatGetal($fondsData['performance'],2)));
        }
        else
        {
          $this->pdf->row(array('',$fondsData['Omschrijving'],
                            $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],2),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
                            $this->formatGetal($fondsData['performance'],2),$this->formatGetal($fondsData['performanceJaar'],2)));
        }
        $this->pdf->ln(2);
      }
    }
    $this->pdf->CellBorders = array('','T','T','T','T');
    $this->pdf->row(array('','','','',''));
  }

}
?>