<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.2 $

$Log: RapportPERF_L102.php,v $
Revision 1.2  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.1  2017/11/18 18:58:17  rvv
*** empty log message ***

Revision 1.9  2016/03/27 17:33:28  rvv
*** empty log message ***

Revision 1.8  2015/04/01 16:00:45  rvv
*** empty log message ***

Revision 1.7  2015/03/29 07:43:24  rvv
*** empty log message ***

Revision 1.6  2015/03/11 17:13:49  rvv
*** empty log message ***

Revision 1.5  2015/03/04 16:30:29  rvv
*** empty log message ***

Revision 1.22  2014/12/31 18:09:06  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_102/ATTberekening_L102.php");

class RapportPERF_L102
{

	function RapportPERF_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

  
    $this->pdf->rapport_titel="Performancemeting over de categorieŽn";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L102($this);
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
  $this->getZorgplichtCategorien();
  $DB = new DB();
  
  // voor data
  $this->pdf->widthA = array(5,80,30,5,30,5,30,120);
  $this->pdf->alignA = array('L','L','R','L','R');
  
  // voor kopjes
  $this->pdf->widthB = array(0,85,30,5,30,5,30,120);
  $this->pdf->alignB = array('L','L','R','L','R');
  
  
  $this->pdf->AddPage();
  $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
  $this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;
  
  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
  $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
  
  $this->getKleuren();
  $this->addResultaat();
  //$this->addSectorBar();
  
  //$gebruikteCategorien=$this->addZorgBar();
  //$this->plotZorgBar4(65,35,$gebruikteCategorien);
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

function addSectorBar()
{
  //$att=new ATTberekening_L42($this);
  $this->att->indexPerformance=false;
  $this->waarden['sector']=$this->att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'sector');
  $categorien=array_keys($this->waarden['sector']);
  $min=0;
  $max=1;
  $kleuren=$this->kleuren['OIS'];
  $kleuren['totaal']['R']['value']=150;
  $kleuren['totaal']['G']['value']=150;
  $kleuren['totaal']['B']['value']=150;
  
  foreach($categorien as $categorie)
  {
    $perc=round($this->waarden['sector'][$categorie]['aandeelOpTotaal']*100,2);
    if($perc <> 0 && $categorie <> 'totaal')
    {
      $grafiekData[$this->att->categorien[$categorie]]=round($this->waarden['sector'][$categorie]['bijdrage'],8);
      if($this->waarden['sector'][$categorie]['bijdrage'] > $max)
        $max=$this->waarden['sector'][$categorie]['bijdrage'];
      if($this->waarden['sector'][$categorie]['bijdrage'] > $min)
        $min=$this->waarden['sector'][$categorie]['bijdrage'];
      $grafiekKleurData[$this->att->categorien[$categorie]]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
    }
  }
  
  //$this->pdf->setXY(189,$this->headerTop+4);
  //$this->BarDiagram(100, 56, $grafiekData, '%l (%p)',$grafiekKleurData,$max);
}

function getZorgplichtCategorien()
{
  $this->zorgplichtCategorien=array();
  $db=new DB();
  $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $this->zorgplichtCategorien[$data['Zorgplicht']]=$data['Omschrijving'];
  }
  return $this->zorgplichtCategorien;
}

function getGrootboeken()
{
  $vertaling=array();
  $db=new DB();
  $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    if($data['Grootboekrekening']=='RENTE')
      $data['Omschrijving']="Rente (spaar)rekeningen";
    //if($data['Grootboekrekening']=='KOBU')
    //  $data['Omschrijving']='Overige kosten';
    
    $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
  }
  $vertaling['overige']='Overige kosten';
  return $vertaling;
}


function addZorgBar()
{
  global $__appvar;
  include_once("rapport/Zorgplichtcontrole.php");
  $zorgplicht = new Zorgplichtcontrole();
  $pdata=$this->pdf->portefeuilledata;
  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum); //listarray($zpwaarde);
  $categorien=array();
  foreach($zpwaarde['categorien'] as $categorie=>$data)
  {
    $data['Norm']=($data['Maximum']-$data['Minimum'])/2;
    $categorien[$categorie]=$data;
    if(!isset($data['fondsGekoppeld']))
    {
      $gebruikteCategorie=$data;
    }
  }
  // listarray($zpwaarde);
  /*
      foreach($zpwaarde['conclusie'] as $data)
      {
        if($data[0]==$gebruikteCategorie['Zorgplicht'])
        {
          $gebruikteCategorie['percentage']=$data[2];
        }
        $gebruikteCategorie['categorien'][$data[0]]=$data[2];
      }
  */
  foreach($zpwaarde['conclusie'] as $data)
  {
    foreach($categorien as $categorie=>$categorieData)
    {
      if($data[0]==$categorie)
      {
        $categorien[$categorie]['percentage']=$data[2];
      }
      // $categorien[$categorie]['categorien'][$data[0]]=$data[2];
    }
  }
//listarray($categorien);
  return $categorien;
  // return $gebruikteCategorie;
}


function plotZorgBar4($width,$height,$categorieData)
{
  
  $yBegin=32;//$this->hoogteBeleggingsresultaat;
  $xBegin=220;
  $volgorde=array('ZAK','ALT','VAR');
  foreach($categorieData as $categorie=>$catData)
    if(!in_array($categorie,$volgorde))
      $volgorde[]=$categorie;
  $newData=array();
  
  foreach($volgorde as $categorie)
  {
    if(isset($categorieData[$categorie]))
      $newData[$categorie]=$categorieData[$categorie];
  }
//listarray($newData);
  $this->pdf->setXY($xBegin,$yBegin);//105 93
  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  $this->pdf->Cell($width,5,vertaalTekst("Beleggersprofiel",$this->pdf->rapport_taal).': '.$this->pdf->portefeuilledata['Risicoklasse'],0,0,'L');
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
  
  foreach($newData as $categorie=>$data)
  {
    $data['percentage']=str_replace(',','.',$data['percentage']);
    
    //echo $yBegin." ";//exit;
    $this->pdf->setXY($xBegin,$yBegin+$height);//105 93
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($width,5,strtolower($this->zorgplichtCategorien[$data['Zorgplicht']]),0,0,'L');
    
    $this->pdf->Rect($xBegin-1,$yBegin+5,$width+2,$height);
    
    
    $this->pdf->setXY($xBegin+5,$yBegin+$height);
    
    $marge=1;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();
    
    $steps=200;
    $debug=0;
    $barWith=$width-10;
    $barHeight=5;
    $barStep=$barWith/$steps;
    $barYbegin=$yBegin+$height/2+$barHeight/2+2;
    
    
    for($i=0;$i<=$steps;$i++)
    {
      
      $percentage=$i/$steps*100;
      
      $rood=array(200,0,0);
      $groen=array(0,100,0);
      $geel=array(180,180,0);
      $marge=10;
      
      for($j=0;$j<$marge;$j++)
      {
        $factor=$j/$marge;
        $roodGeelOpbouw[$j]=array($rood[0]+($geel[0]-$rood[0])*$factor,$rood[1]+($geel[1]-$rood[1])*$factor,$rood[2]+($geel[2]-$rood[2])*$factor);
        $groenGeelOpbouw[$j]=array($groen[0]+($geel[0]-$groen[0])*$factor,$groen[1]+($geel[1]-$groen[1])*$factor,$groen[2]+($geel[2]-$groen[2])*$factor);
      }
      
      if($i>0)
      {
        
        if($percentage<=$data['Minimum']-$marge)
        {
          $fill_color=$rood;
          if($debug==1){echo "$percentage 1 rood <br>\n";}
        }
        elseif($percentage<=$data['Minimum']+$marge)
        {
          $fill_color=$geel;
          if($percentage<=$data['Minimum'])//geelopbouw;
          {
            $j=$percentage-$data['Minimum']+$marge-1;
            $fill_color=$roodGeelOpbouw[$j];
            if($debug==1){echo "$percentage 2 rood->geel $j<br>\n";}
          }
          elseif($percentage<=$data['Minimum']+$marge)
          {
            $j=$marge-($percentage-$data['Minimum']);
            $fill_color=$groenGeelOpbouw[$j];
            if($debug==1){echo "$percentage 3 geel->groen $j <br>\n";}
            // echo "$percentage $j <br>\n";
          }
        }
        elseif($percentage<=$data['Maximum']-$marge)
        {
          $fill_color=$groen;//array(0,100,0);
          if($debug==1){echo "$percentage 4 groen <br>\n";}
          
        }
        elseif($percentage<=$data['Maximum']+$marge)
        {
          if($percentage<=$data['Maximum'])
          {
            $j = $marge - (($data['Maximum']) - $percentage)-1;
            
            $fill_color = $groenGeelOpbouw[$j];
            if($debug==1){echo "$percentage 5 groen->geel $j<br>\n";}
          }
          elseif($percentage<=$data['Maximum']+$marge)//rood opbouw;
          {
            $j=$marge-($percentage-$data['Maximum']);
            //  echo "$percentage<".$data['Maximum']." $j<br>\n";
            $fill_color=$roodGeelOpbouw[$j];
            if($debug==1){echo "$percentage 6 geel->rood $j<br>\n";}
          }
          else
          {
            $fill_color = $geel;//array(200,200,0);
            if($debug==1){echo "$percentage 7 geel <br>\n";}
          }
          
        }
        else
        {
          
          $fill_color=$rood;
          if($debug==1){echo "$percentage 8 rood <br>\n";}
        }
        
        $this->pdf->SetFillColor($fill_color[0],$fill_color[1],$fill_color[2]);
        $this->pdf->rect($xBegin+5+$i*$barStep,$barYbegin,$barStep,$barHeight,'F');
        
        
      }
      
    }
    
    $this->pdf->Rect($xBegin+5,$barYbegin,$barWith,5);
    
    $this->pdf->Line($xcenter+cos($step*0+M_PI)*$buitenVerhouding*$radius,
                     $ycenter+sin($step*0+M_PI)*$buitenVerhouding*$radius,
                     $xcenter+cos($step*$steps+M_PI)*$buitenVerhouding*$radius,
                     $ycenter+sin($step*$steps+M_PI)*$buitenVerhouding*$radius);
    //  exit;
    $pstep=$data['percentage']/$steps*100;
    $this->pdf->Line($xcenter,
                     $ycenter,
                     $xcenter+cos($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius,
                     $ycenter+sin($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius);
    $percentages=array(0=>array('align'=>'L','yOffset'=>-3,'width'=>10),
                       100=>array('align'=>'R','yOffset'=>-3,'width'=>0.1),
                       $data['percentage']=>array('align'=>'L','yOffset'=>12,'xOffset'=>-3,'width'=>10,'extraText'=>' : actueel','line'=>array(-2,9)));
    $this->pdf->SetLineWidth(0.5);
    foreach($percentages as $percentage=>$options)
    {
      $pstep=$percentage/100*$steps;
      $this->pdf->setXY($xBegin+5+$pstep*$barStep+$options['xOffset'],$barYbegin+$options['yOffset']);
      $this->pdf->Cell($options['width'],1,round($percentage).'% '.$options['extraText'],0,0,$options['align']);
      if(isset($options['line']))
        $this->pdf->line($xBegin+5+$pstep*$barStep,$barYbegin+$options['line'][0],$xBegin+5+$pstep*$barStep,$barYbegin+$options['line'][1]);
    }
    $this->pdf->SetLineWidth(0.2);
    
    $percentages=array($data['Minimum']=>array('align'=>'C','yOffset'=>-7,'width'=>0.1,'xOffset'=>1,'line'=>array(-4,0,-9,-11)),
                       $data['Maximum']=>array('align'=>'C','yOffset'=>-7,'width'=>0.1,'xOffset'=>1,'line'=>array(-4,0,-9,-11)));
    foreach($percentages as $percentage=>$options)
    {
      $pstep=$percentage/100*$steps;
      $this->pdf->setXY($xBegin+5+$pstep*$barStep+$options['xOffset'],$barYbegin+$options['yOffset']);
      $this->pdf->Cell($options['width'],1,round($percentage).'% '.$options['extraText'],0,0,$options['align']);
      if(isset($options['line']))
      {
        $this->pdf->line($xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][0], $xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][1]);
        $this->pdf->line($xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][2], $xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][3]);
      }
    }
    
    $pstep=($data['Minimum'] + $data['Maximum'])/200*$steps;
    $this->pdf->setXY($xBegin+5+$pstep*$barStep,$barYbegin-15);
    $this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(0.1,1,vertaalTekst("mandaat",$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setDash(1,1);
    $this->pdf->line($xBegin + 5 + ($data['Minimum']/100*$steps) * $barStep, $barYbegin-12, $xBegin + 5 + ($data['Maximum']/100*$steps) * $barStep, $barYbegin-12);
    $this->pdf->setDash(0);
    
    $yBegin+=38;
  }
  if($debug==1)
    exit;
  
}
function plotZorgBar3($width,$height,$categorieData)
{
  
  $yBegin=32;//$this->hoogteBeleggingsresultaat;
  $xBegin=205;
  $volgorde=array('ZAK','ALT','VAR');
  foreach($categorieData as $categorie=>$catData)
    if(!in_array($categorie,$volgorde))
      $volgorde[]=$categorie;
  $newData=array();
  
  foreach($volgorde as $categorie)
  {
    if(isset($categorieData[$categorie]))
      $newData[$categorie]=$categorieData[$categorie];
  }
  
  foreach($newData as $categorie=>$data)
  {
    $data['percentage']=str_replace(',','.',$data['percentage']);
    
    //echo $yBegin." ";//exit;
    $this->pdf->setXY($xBegin,$yBegin);//105 93
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($width,5,vertaalTekst("Mandaatcontrole",$this->pdf->rapport_taal).' '.strtolower($this->zorgplichtCategorien[$data['Zorgplicht']]),0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Rect($xBegin,$yBegin+5,$width,40);
    $this->pdf->setXY($xBegin+5,$yBegin+$height);
    
    $marge=1;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();
    
    $steps=100;
    $this->pdf->setXY($xBegin+25,$yBegin+45);
    $xcenter=$xBegin+$width/2;
    $ycenter=$yBegin+42;
    $radius=15;
    
    $x1=$this->pdf->getX();
    $y1=$this->pdf->getY();
    $y2=$y1;
    $xb1=$x1;
    $yb1=$y1;
    
    
    
    $step=M_PI/$steps;
    $buitenVerhouding=2;
    for($i=0;$i<=$steps;$i++)
    {
      $hoekx=cos($step*$i+M_PI);
      $hoeky=sin($step*$i+M_PI);
      $x2=$xcenter+($hoekx*$radius);
      $y2=$ycenter+($hoeky*$radius);
      
      $xb2=$xcenter+($hoekx*$buitenVerhouding*$radius);
      $yb2=$ycenter+($hoeky*$buitenVerhouding*$radius);
      
      $percentage=$i/$steps*100;
      if($percentage > $data['Maximum'])
        $fill_color=array(200,0,0);
      elseif($percentage < $data['Minimum'])
        $fill_color=array(200,0,0);
      else
        $fill_color=array(0,100,0);
      
      if($i>0)
      {
        // $polly=array($x2,$y2,$x1,$y1,$xb1,$yb1,$xb2,$yb2); //,$x1,$y1
        $polly[]=$x2;
        $polly[]=$y2;
        $polly[]=$x1;
        $polly[]=$y1;
        $polly[]=$xb1;
        $polly[]=$yb1;
        $polly[]=$xb2;
        $polly[]=$yb2;
        
        
        // if($fill_color <> $last_fill_color || $i==100)
        //{
        $this->pdf->Polygon($polly, 'F', null, $fill_color);
        $polly = array();
        // }
        $this->pdf->Line($x1,$y1,$x2,$y2);
        $this->pdf->Line($xb1,$yb1,$xb2,$yb2);
      }
      
      $x1=$x2;
      $y1=$y2;
      
      $xb1=$xb2;
      $yb1=$yb2;
      
    }
    $this->pdf->Line($xcenter+cos($step*0+M_PI)*$buitenVerhouding*$radius,
                     $ycenter+sin($step*0+M_PI)*$buitenVerhouding*$radius,
                     $xcenter+cos($step*$steps+M_PI)*$buitenVerhouding*$radius,
                     $ycenter+sin($step*$steps+M_PI)*$buitenVerhouding*$radius);
    
    $pstep=$data['percentage']/$steps*100;
    $this->pdf->Line($xcenter,
                     $ycenter,
                     $xcenter+cos($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius,
                     $ycenter+sin($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius);
    
    $percentages=array(0,100,$data['percentage']);
    foreach($percentages as $percentage)
    {
      $pstep=$percentage/$steps*100;
      $this->pdf->setXY($xcenter+cos($step*$pstep+M_PI)*$buitenVerhouding*1.15*$radius,
                        $ycenter+sin($step*$pstep+M_PI)*$buitenVerhouding*1.15*$radius);
      $this->pdf->Cell(1,0,$percentage.'%',0,0,'C');
    }
    $yBegin+=50;
  }
  
  
}

function plotZorgBarold($width,$height,$categorieData)
{
  // listarray($categorieData);
  $yBegin=32;//$this->hoogteBeleggingsresultaat;
  $xBegin=205;
  foreach($categorieData as $categorie=>$data)
  {
    $data['percentage']=str_replace(',','.',$data['percentage']);
    
    //echo $yBegin." ";//exit;
    $this->pdf->setXY($xBegin,$yBegin);//105 93
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($width,5,vertaalTekst("Mandaatcontrole",$this->pdf->rapport_taal).' '.$data['Zorgplicht'],0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Rect($xBegin,$yBegin+5,$width,40);
    $this->pdf->setXY($xBegin+5,$yBegin+$height);
    
    $marge=1;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();
    
    $steps=100;
    $this->pdf->setXY($xBegin+25,$yBegin+45);
    $xcenter=$xBegin+$width/2;
    $ycenter=$yBegin+42;
    $radius=15;
    
    $x1=$this->pdf->getX();
    $y1=$this->pdf->getY();
    $y2=$y1;
    $xb1=$x1;
    $yb1=$y1;
    
    
    $kleurenGroen=array(array(0,100,0),array(140,140,0));
    $kleurenOranje=array(array(140,140,0),array(250,180,80));
    $kleurenRood=array(array(255,140,80),array(200,0,0));
    
    $kleurenGroenStap=array(($kleurenGroen[1][0]-$kleurenGroen[0][0])/$steps/(($data['Norm']-$data['Minimum'])/100),
      ($kleurenGroen[1][1]-$kleurenGroen[0][1])/$steps/(($data['Norm']-$data['Minimum'])/100),
      ($kleurenGroen[1][2]-$kleurenGroen[0][2])/$steps/(($data['Norm']-$data['Minimum'])/100));
    $kleurenOranjeStap=array(($kleurenOranje[1][0]-$kleurenOranje[0][0])/$steps/(($data['Maximum']-$data['Norm'])/100),
      ($kleurenOranje[1][1]-$kleurenOranje[0][1])/$steps/(($data['Maximum']-$data['Norm'])/100),
      ($kleurenOranje[1][2]-$kleurenOranje[0][2])/$steps/(($data['Maximum']-$data['Norm'])/100));
    $kleurenRoodStap=array(($kleurenRood[1][0]-$kleurenRood[0][0])/$steps/((100-$data['Maximum'])/100),
      ($kleurenRood[1][1]-$kleurenRood[0][1])/$steps/((100-$data['Maximum'])/100),
      ($kleurenRood[1][2]-$kleurenRood[0][2])/$steps/((100-$data['Maximum'])/100));
    
    $step=M_PI/$steps;
    $buitenVerhouding=2;
    for($i=0;$i<=$steps;$i++)
    {
      $hoekx=cos($step*$i+M_PI);
      $hoeky=sin($step*$i+M_PI);
      $x2=$xcenter+($hoekx*$radius);
      $y2=$ycenter+($hoeky*$radius);
      
      $xb2=$xcenter+($hoekx*$buitenVerhouding*$radius);
      $yb2=$ycenter+($hoeky*$buitenVerhouding*$radius);
      
      if($i>0)
      {
        $polly=array($x2,$y2,$x1,$y1,$xb1,$yb1,$xb2,$yb2); //,$x1,$y1
        $percentage=$i/$steps*100;
        if($percentage > $data['Maximum'])
        {
          //$cstep=$i-$data['Maximum']/$steps*100;
          // $fill_color=array($kleurenRood[0][0]+$kleurenRoodStap[0]*$cstep,$kleurenRood[0][1]+$kleurenRoodStap[1]*$cstep,$kleurenRood[0][2]+$kleurenRoodStap[2]*$cstep);
          $fill_color=array(200,0,0);
        }
        //elseif($percentage > $data['Norm'])
        //{
        // $cstep=$i-$data['Norm']/$steps*100;
        //   $fill_color=array($kleurenOranje[0][0]+$kleurenOranjeStap[0]*$cstep,$kleurenOranje[0][1]+$kleurenOranjeStap[1]*$cstep,$kleurenOranje[0][2]+$kleurenOranjeStap[2]*$cstep);
        //   $fill_color=array(0,0,100);
        // listarray($fill_color);
        //   }
        elseif($percentage < $data['Minimum'])
        {
          // $cstep=$i-$data['Minimum']/$steps*100;
          //$fill_color=array($kleurenGroen[0][0]+$kleurenGroenStap[0]*$cstep,$kleurenGroen[0][1]+$kleurenGroenStap[1]*$cstep,$kleurenGroen[0][2]+$kleurenGroenStap[2]*$cstep);
          $fill_color=array(200,0,0);
        }
        else
          $fill_color=array(0,100,0);
        //  listarray($fill_color);
        $this->pdf->Polygon($polly, 'F', null, $fill_color) ;
        $this->pdf->Line($x1,$y1,$x2,$y2);
        $this->pdf->Line($xb1,$yb1,$xb2,$yb2);
      }
      
      $x1=$x2;
      $y1=$y2;
      
      $xb1=$xb2;
      $yb1=$yb2;
      
    }
    $this->pdf->Line($xcenter+cos($step*0+M_PI)*$buitenVerhouding*$radius,
                     $ycenter+sin($step*0+M_PI)*$buitenVerhouding*$radius,
                     $xcenter+cos($step*$steps+M_PI)*$buitenVerhouding*$radius,
                     $ycenter+sin($step*$steps+M_PI)*$buitenVerhouding*$radius);
    
    $pstep=$data['percentage']/$steps*100;
    $this->pdf->Line($xcenter,
                     $ycenter,
                     $xcenter+cos($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius,
                     $ycenter+sin($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius);
    
    $percentages=array(0,100,$data['percentage']);
    foreach($percentages as $percentage)
    {
      $pstep=$percentage/$steps*100;
      $this->pdf->setXY($xcenter+cos($step*$pstep+M_PI)*$buitenVerhouding*1.15*$radius,
                        $ycenter+sin($step*$pstep+M_PI)*$buitenVerhouding*1.15*$radius);
      $this->pdf->Cell(1,0,$percentage.'%',0,0,'C');
    }
    $yBegin+=50;
  }
  
  /*
   $xLegenda=$xcenter-($radius*$buitenVerhouding);
   $yLegenda=$ycenter+8;
 //  $this->pdf->Rect($xLegenda,$yLegenda,4,4,'DF',null,array(($kleurenGroen[0][0]+$kleurenGroen[1][0])/2,($kleurenGroen[0][1]+$kleurenGroen[1][1])/2,($kleurenGroen[0][2]+$kleurenGroen[1][2])/2));
   $this->pdf->SetXY($xLegenda,$yLegenda);
   $this->pdf->cell($radius*$buitenVerhouding*2,4,$data['categorien']['RISM'].'% Risicomijdend',0,0,'C');
   $yLegenda+=6;
 //  $this->pdf->Rect($xLegenda,$yLegenda,4,4,'DF',null,array(($kleurenRood[0][0]+$kleurenRood[1][0])/2,($kleurenRood[0][1]+$kleurenRood[1][1])/2,($kleurenRood[0][2]+$kleurenRood[1][2])/2));
   $this->pdf->SetXY($xLegenda,$yLegenda);
   $this->pdf->cell($radius*$buitenVerhouding*2,4,$data['categorien']['RISD'].'% Risicodragend',0,0,'C');
      */
  
}

function plotZorgBar2($width,$height,$data)
{
  // listarray($data);
  
  $data['percentage']=str_replace(',','.',$data['percentage']);
  $yBegin=32;//$this->hoogteBeleggingsresultaat;
  $xBegin=205;
  //echo $yBegin;exit;
  $this->pdf->setXY($xBegin,$yBegin);//105 93
  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  $this->pdf->Cell($width,5,vertaalTekst("Overeengekomen risicoprofiel",$this->pdf->rapport_taal),0,0,'C');
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->setXY($xBegin,$yBegin+10);//115
  $this->pdf->Cell($width,5,vertaalTekst("Mandaat",$this->pdf->rapport_taal).': '.$data['Risicoklasse'],0,0,'C');
  
  $this->pdf->Rect($xBegin,$yBegin+5,$width,80);
  $this->pdf->setXY(210,130);
  
  $hProcent=$height/100;
  
  $marge=1;
  $extraY=30;
  $xPage=$this->pdf->getX();
  $yPage=$this->pdf->getY();
  
  $steps=100;
  $this->pdf->setXY(230,$yBegin+45);
  $xcenter=$xBegin+$width/2;
  $ycenter=$yBegin+55;
  $radius=15;
  
  $x1=$this->pdf->getX();
  $y1=$this->pdf->getY();
  $y2=$y1;
  $xb1=$x1;
  $yb1=$y1;
  
  
  $kleurenGroen=array(array(0,100,0),array(140,140,0));
  $kleurenOranje=array(array(250,220,80),array(250,180,80));
  $kleurenRood=array(array(255,140,80),array(200,0,0));
  
  $kleurenGroenStap=array(($kleurenGroen[1][0]-$kleurenGroen[0][0])/$steps/(($data['Norm']-$data['Minimum'])/100),
    ($kleurenGroen[1][1]-$kleurenGroen[0][1])/$steps/(($data['Norm']-$data['Minimum'])/100),
    ($kleurenGroen[1][2]-$kleurenGroen[0][2])/$steps/(($data['Norm']-$data['Minimum'])/100));
  $kleurenOranjeStap=array(($kleurenOranje[1][0]-$kleurenOranje[0][0])/$steps/(($data['Maximum']-$data['Norm'])/100),
    ($kleurenOranje[1][1]-$kleurenOranje[0][1])/$steps/(($data['Maximum']-$data['Norm'])/100),
    ($kleurenOranje[1][2]-$kleurenOranje[0][2])/$steps/(($data['Maximum']-$data['Norm'])/100));
  $kleurenRoodStap=array(($kleurenRood[1][0]-$kleurenRood[0][0])/$steps/((100-$data['Maximum'])/100),
    ($kleurenRood[1][1]-$kleurenRood[0][1])/$steps/((100-$data['Maximum'])/100),
    ($kleurenRood[1][2]-$kleurenRood[0][2])/$steps/((100-$data['Maximum'])/100));
  
  $step=M_PI/$steps;
  $buitenVerhouding=2;
  for($i=0;$i<=$steps;$i++)
  {
    $hoekx=cos($step*$i+M_PI);
    $hoeky=sin($step*$i+M_PI);
    $x2=$xcenter+($hoekx*$radius);
    $y2=$ycenter+($hoeky*$radius);
    
    $xb2=$xcenter+($hoekx*$buitenVerhouding*$radius);
    $yb2=$ycenter+($hoeky*$buitenVerhouding*$radius);
    
    if($i>0)
    {
      $polly=array($x2,$y2,$x1,$y1,$xb1,$yb1,$xb2,$yb2); //,$x1,$y1
      $percentage=$i/$steps*100;
      if($percentage > $data['Maximum'])
      {
        $cstep=$i-$data['Maximum']/$steps*100;
        $fill_color=array($kleurenRood[0][0]+$kleurenRoodStap[0]*$cstep,$kleurenRood[0][1]+$kleurenRoodStap[1]*$cstep,$kleurenRood[0][2]+$kleurenRoodStap[2]*$cstep);
      }
      elseif($percentage > $data['Norm'])
      {
        $cstep=$i-$data['Norm']/$steps*100;
        $fill_color=array($kleurenOranje[0][0]+$kleurenOranjeStap[0]*$cstep,$kleurenOranje[0][1]+$kleurenOranjeStap[1]*$cstep,$kleurenOranje[0][2]+$kleurenOranjeStap[2]*$cstep);
        // listarray($fill_color);
      }
      elseif($percentage > $data['Minimum'])
      {
        $cstep=$i-$data['Minimum']/$steps*100;
        $fill_color=array($kleurenGroen[0][0]+$kleurenGroenStap[0]*$cstep,$kleurenGroen[0][1]+$kleurenGroenStap[1]*$cstep,$kleurenGroen[0][2]+$kleurenGroenStap[2]*$cstep);
      }
      //  listarray($fill_color);
      $this->pdf->Polygon($polly, 'F', null, $fill_color) ;
      $this->pdf->Line($x1,$y1,$x2,$y2);
      $this->pdf->Line($xb1,$yb1,$xb2,$yb2);
    }
    
    $x1=$x2;
    $y1=$y2;
    
    $xb1=$xb2;
    $yb1=$yb2;
    
  }
  $this->pdf->Line($xcenter+cos($step*0+M_PI)*$buitenVerhouding*$radius,
                   $ycenter+sin($step*0+M_PI)*$buitenVerhouding*$radius,
                   $xcenter+cos($step*$steps+M_PI)*$buitenVerhouding*$radius,
                   $ycenter+sin($step*$steps+M_PI)*$buitenVerhouding*$radius);
  
  $pstep=$data['percentage']/$steps*100;
  $this->pdf->Line($xcenter,
                   $ycenter,
                   $xcenter+cos($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius,
                   $ycenter+sin($step*$pstep+M_PI)*$buitenVerhouding*1.05*$radius);
  
  $percentages=array(0,100,$data['percentage']);
  foreach($percentages as $percentage)
  {
    $pstep=$percentage/$steps*100;
    $this->pdf->setXY($xcenter+cos($step*$pstep+M_PI)*$buitenVerhouding*1.15*$radius,
                      $ycenter+sin($step*$pstep+M_PI)*$buitenVerhouding*1.15*$radius);
    $this->pdf->Cell(1,0,$percentage.'%',0,0,'C');
  }
  
  
  
  $xLegenda=$xcenter-($radius*$buitenVerhouding);
  $yLegenda=$ycenter+8;
  //  $this->pdf->Rect($xLegenda,$yLegenda,4,4,'DF',null,array(($kleurenGroen[0][0]+$kleurenGroen[1][0])/2,($kleurenGroen[0][1]+$kleurenGroen[1][1])/2,($kleurenGroen[0][2]+$kleurenGroen[1][2])/2));
  $this->pdf->SetXY($xLegenda,$yLegenda);
  $this->pdf->cell($radius*$buitenVerhouding*2,4,$data['categorien']['RISM'].'% Risicomijdend',0,0,'C');
  $yLegenda+=6;
  //  $this->pdf->Rect($xLegenda,$yLegenda,4,4,'DF',null,array(($kleurenRood[0][0]+$kleurenRood[1][0])/2,($kleurenRood[0][1]+$kleurenRood[1][1])/2,($kleurenRood[0][2]+$kleurenRood[1][2])/2));
  $this->pdf->SetXY($xLegenda,$yLegenda);
  $this->pdf->cell($radius*$buitenVerhouding*2,4,$data['categorien']['RISD'].'% Risicodragend',0,0,'C');
  
  
  
  // listarray($data);
  
  /*
$data['percentage']=str_replace(',','.',$data['percentage']);

$this->pdf->setXY($xPage-$marge-4,$yPage-2);
$this->pdf->Rect($xPage, $yPage, $hProcent*100, $barWidth, 'D');
$this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-8);
$this->pdf->cell(4,4,"Risicodragend",0,0,'R');
$this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-4);
$this->pdf->cell(4,4,"100%",0,0,'R');
$this->pdf->setXY($xPage+$hProcent*$data['Minimum']-2,$yPage-$marge-4);
$this->pdf->cell(4,4,"Min. ".$data['Minimum'].'%',0,0,'R');
$this->pdf->setXY($xPage+$hProcent*$data['Maximum']-2,$yPage-$marge-4);
$this->pdf->cell(4,4,"Max. ".$data['Maximum'].'%',0,0,'R');
$this->pdf->setXY($xPage+$hProcent*$data['Norm']-2,$yPage+$marge+5);
$this->pdf->cell(4,4,"Norm ".$data['Norm'].'%',0,0,'R');

//listarray($this->kleuren['OIS']);exit;
$this->pdf->SetFillColor(200,0,0);
$this->pdf->Rect($xPage, $yPage, $hProcent*$data['Minimum'], $barWidth,  'DF');
$this->pdf->SetFillColor(224,246,10);
$this->pdf->Rect($xPage+$hProcent*$data['Minimum'], $yPage,$hProcent*$data['Maximum'], $barWidth,   'DF');
$this->pdf->Rect($xPage+$hProcent*$data['Minimum'], $yPage+$extraY,$hProcent*$data['Maximum'], $barWidth,   'DF');
$this->pdf->SetFillColor(200,0,0);
$this->pdf->Rect($xPage+$hProcent*$data['Maximum'], $yPage, $hProcent*(100-$data['Maximum']),$barWidth,  'DF');
$this->pdf->Rect($xPage+$hProcent*$data['Maximum'], $yPage+$extraY, $hProcent*(100-$data['Maximum']),$barWidth,  'DF');
$this->pdf->Line($xPage+$hProcent*$data['Norm'], $yPage,$xPage+$hProcent*$data['Norm'],$yPage+$barWidth);



$this->pdf->Rect($xPage, $yPage+$extraY,$hProcent*100, $barWidth,  'D');
$this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-8+$extraY);
$this->pdf->cell(4,4,"Risicodragend",0,0,'R');
$this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-4+$extraY);
$this->pdf->cell(4,4,"100%",0,0,'R');
$this->pdf->setXY($xPage+$hProcent*$data['Minimum']-2,$yPage-$marge-4+$extraY);
$this->pdf->cell(4,4,"Min. ".$data['Minimum'].'%',0,0,'R');
$this->pdf->setXY($xPage+$hProcent*$data['Maximum']-2,$yPage-$marge-4+$extraY);
$this->pdf->cell(4,4,"Max. ".$data['Maximum'].'%',0,0,'R');


$this->pdf->SetFillColor(0,0,0);
$this->pdf->Rect($xPage+$hProcent*($data['percentage']-1),$yPage+$extraY , $hProcent*(2), $barWidth,  'DF');
$this->pdf->setXY($xPage+$hProcent*$data['percentage']+2,$yPage+$barWidth+$marge+$extraY);
$this->pdf->cell(4,4,'werkelijk '.$data['percentage'].'%',0,0,'R');
*/
}

function RectRotate($x,$y,$w,$h,$f)
{
  // $this->pdf->Rect($x,$y-$h,$h,$w,$f);
}


function addResultaat()
{
  
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
    $this->pdf->__appvar['consolidatie']=1;
    $this->pdf->portefeuilles=array($this->portefeuille);
  }
  
  $vetralingGrootboek=$this->getGrootboeken();
  
  $att=new ATTberekening_L102($this);
  $this->att->indexPerformance=false;
  $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Hoofdcategorie');
  $categorien=array_keys($this->waarden['Periode']);
  
  //listarray($this->att->totalen);exit;
//listarray($this->waarden['Periode']);
  
  $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
  $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
  $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
  
  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
  $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
  // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('U','U');
  $subBoven=array('','');
  $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
  $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));

  
  foreach($categorien as $categorie)
  {
    $volOnder[]='U';
    $volOnder[]='U';
    $subOnder[]='U';
    $subOnder[]='';
    $subBoven[]='T';
    $subBoven[]='';
    $fillArray[]=1;
    $fillArray[]=1;
    $header[]=vertaalTekst($this->att->categorien[$categorie],$this->pdf->rapport_taal);
    $header[]='';
    $samenstelling[]='';
    $samenstelling[]='';
    // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  }
  
  $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $effectenmutaties=array("",vertaalTekst("Aankopen/verkopen gedurende verslagperiode",$this->pdf->rapport_taal));
  
  
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $ongerealiseerdFonds=array("",vertaalTekst("Latente meer/minwaarde fondsen",$this->pdf->rapport_taal)); //
  $ongerealiseerdValuta=array("",vertaalTekst("Latente meer/minwaarde valuta",$this->pdf->rapport_taal)); //
  
  $gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde meer/minwaarde fondsen",$this->pdf->rapport_taal)); //
  $gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde meer/minwaarde valuta",$this->pdf->rapport_taal)); //
  $valutaResultaat=array("",vertaalTekst("Resultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
  $rente=array("",vertaalTekst("Verlopen interest",$this->pdf->rapport_taal));//
  $totaalOpbrengst=array("","");//totaalOpbrengst
  
  $totaalKosten=array("","");   //totaalKosten
  $totaal=array("","");   //totaalOpbrengst-totaalKosten
  
  
  foreach($categorien as $categorie)
  {
    unset($this->waarden['Periode'][$categorie]['perfWaarden']);
  }
  
  //listarray($this->waarden['Periode']);exit;
  foreach($categorien as $categorie)
  {
    $perfWaarden=$this->waarden['Periode'][$categorie];
    $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
    $mutwaarde[]='';
    
    if($categorie=='totaal')
    {
      $effectenmutaties[]='';
      $effectenmutaties[]='';
      //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
      //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
      $onttrekking[]='';
    }
    else
    {
      $effectenmutaties[]=$this->formatGetal($perfWaarden['stort'],0);
      $effectenmutaties[]='';
      $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
      $stortingen[]='';
      $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
      $onttrekking[]='';
    }
    
    $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
      $perfWaarden['ongerealiseerdFondsResultaat']+
      $perfWaarden['ongerealiseerdValutaResultaat']+
      $perfWaarden['gerealiseerdFondsResultaat']+
      $perfWaarden['gerealiseerdValutaResultaat']+
      $perfWaarden['opgelopenrente'];
    
    $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR+$perfWaarden['kosten']);
    $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
    
    $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $resultaat[]='';
    $rendement[]=$this->formatGetal($perfWaarden['procent'],2).' %';
    $rendement[]='';
    $ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0);
    $ongerealiseerdFonds[]='';
    $ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
    $ongerealiseerdValuta[]='';
    $gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0);
    $gerealiseerdFonds[]='';
    $gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
    $gerealiseerdValuta[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
    $rente[]='';
    $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0);
    $totaalOpbrengst[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
    $totaalKosten[]='';
    $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $totaal[]='';
  
  
    $kostenCategorien=array('BEH'=>'BEH','BEW'=>'BEW','KOST'=>'KOST','overige'=>'overige');
    foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $opbrengstCategorien[$categorie]=$categorie;
    foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;
    
  }
  
  $cellWidth=27;
  $cellWidthP=2;
  $this->pdf->widthB = array(0,70,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP);
  $this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R');
  $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,5,30,5,30,5,30,5,30,5,30,5);
  $this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R');


//listarray($perfWaarden);

  $this->pdf->SetWidths($this->pdf->widthB);
  $this->pdf->SetAligns($this->pdf->alignB);
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
  $this->headerTop=$this->pdf->GetY();
  $this->pdf->row($header);
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
  $this->pdf->SetWidths($this->pdf->widthA);
  $this->pdf->SetAligns($this->pdf->alignA);

  $this->pdf->row($perbegin);
  //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
  $this->pdf->CellBorders = $subOnder;
  $this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
  $this->pdf->CellBorders = array();
  // subtotaal
  $this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
  $this->pdf->ln();
  $this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
  $this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
  $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
  $this->pdf->CellBorders = $subOnder;
  $this->pdf->row($effectenmutaties);
  $this->pdf->ln();
  $this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
  $this->pdf->ln();

  $this->pdf->CellBorders = $volOnder;
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->CellBorders = array();
  $ypos = $this->pdf->GetY();


  $this->pdf->SetY($ypos);
  $this->pdf->ln();
//listarray($this->pdf->widthB);

  $this->pdf->SetWidths(array(0,100));
  $this->pdf->SetAligns($this->pdf->alignB);
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
  $YSamenstelling=$this->pdf->GetY();
  $this->pdf->row($samenstelling);//,"","","",""));
  $this->pdf->SetWidths($this->pdf->widthB);
  //$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
  $this->pdf->fillCell=array();
  $this->pdf->SetTextColor(0,0,0);
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->ln();
  $this->hoogteBeleggingsresultaat=$this->pdf->getY();
  $this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


  $this->pdf->SetWidths($this->pdf->widthA);
  $this->pdf->SetAligns($this->pdf->alignA);
  $this->pdf->row($ongerealiseerdFonds);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
  $this->pdf->row($ongerealiseerdValuta);
  $this->pdf->row($gerealiseerdFonds);
  $this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
  //	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
  $this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
  $keys=array();
  //foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
  //  $keys[]=$key;



  foreach ($opbrengstCategorien as $grootboek)
  {
    $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
    // foreach($perfWaarden as $port=>$waarden)

    foreach($categorien as $categorie)
    {
      $perfWaarden=$this->waarden['Periode'][$categorie];
      $tmp[]=$this->formatGetal($perfWaarden['grootboekOpbrengsten'][$grootboek],0);
      $tmp[]='';
    }
    //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
    $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
  }

  $this->pdf->CellBorders = $subBoven;
  $this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
  //$this->pdf->ln();
  $this->pdf->CellBorders = array();

  $this->pdf->SetWidths($this->pdf->widthB);
  $this->pdf->SetAligns($this->pdf->alignB);

  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->SetWidths($this->pdf->widthA);
  $this->pdf->SetAligns($this->pdf->alignA);
  foreach ($kostenCategorien as $grootboek)
  {
    $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
    foreach($categorien as $categorie)
    {
      $perfWaarden=$this->waarden['Periode'][$categorie];

      $tmp[]=$this->formatGetal($perfWaarden['grootboekKosten'][$grootboek],0);
      $tmp[]='';
    }
    //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
    $this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
  }
  $this->pdf->CellBorders = $subBoven;
  $this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
  $posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
  $this->pdf->CellBorders = array();
  //$this->pdf->CellBorders = $volOnder;
  $this->pdf->Ln();
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
  $actueleWaardePortefeuille = 0;
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->CellBorders = array();
  
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
    $p=sprintf('%.2f',$val).'%';
    $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
    $this->pdf->legends[]=$legend;
    $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
  }
}

function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
{
  
  $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
  $this->SetLegends($data,$format);
  
  
  $XPage = $this->pdf->GetX();
  $YPage = $this->pdf->GetY();
  $margin = 0;
  $legendWidth=50;
  $YDiag = $YPage + $margin;
  $hDiag = floor($h - $margin * 2);
  $XDiag = $XPage + $margin * 2 + $legendWidth;
  $lDiag = floor($w - $margin * 3 - $legendWidth);
  if($color == null)
    $color=array(155,155,155);
  if ($maxVal == 0) {
    $maxVal = max($data)*1.1;
  }
  if ($minVal == 0) {
    $minVal = min($data)*1.1;
  }
  if($minVal >0)
    $minVal=0;
  
  $offset=$minVal;
  $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
  $bandBreedte = $valIndRepere * $nbDiv;
  $lRepere = floor($lDiag / $nbDiv);
  $unit = $lDiag / $bandBreedte;
  $hBar = ($hDiag / ($this->pdf->NbVal + 1));
  $hDiag = $hBar * ($this->pdf->NbVal + 1);
  $eBaton = floor($hBar * 80 / 100);
  $legendaStep=$unit;
  
  $legendaStep=$unit/$nbDiv*$bandBreedte;
  //echo "$bandBreedte / $legendaStep = ".$bandBreedte/$legendaStep." ".$nbDiv;exit;
  //if($bandBreedte/$legendaStep > $nbDiv)
  
  if($bandBreedte/$legendaStep > $nbDiv)
    $legendaStep=$legendaStep*5;
  if($bandBreedte/$legendaStep > $nbDiv)
    $legendaStep=$legendaStep*2;
  if($bandBreedte/$legendaStep > $nbDiv)
    $legendaStep=$legendaStep/2*5;
  $valIndRepere=round($valIndRepere/$unit/5)*5;
  
  
  $this->pdf->SetLineWidth($this->pdf->lineWidth);
  $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
  
  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  
  $nullijn=$XDiag - ($offset * $unit) +$margin;
  
  $i=0;
  $nbDiv=10;
  
  $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
  if(round($legendaStep,5) <> 0.0)
  {
    for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
    {
      $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
      $this->pdf->setXY($x,$YDiag + $hDiag);
      $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
    }
    
    for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
    {
      $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
      $this->pdf->setXY($x,$YDiag + $hDiag);
      $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
    }
  }
  $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
  $i=0;
  
  //$this->pdf->SetXY(0, $YDiag);
  //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
  //$this->pdf->SetXY($nullijn, $YDiag);
  //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
  $this->pdf->SetXY($XDiag, $YDiag);
  $this->pdf->Cell($lDiag, $hval-4, vertaalTekst('Contributie rendement',$this->pdf->rapport_taal),0,0,'C');
  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
  foreach($data as $key=>$val)
  {
    $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
    //Bar
    $xval = $nullijn;
    $lval = ($val * $unit);
    $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
    $hval = $eBaton;
    $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
    //Legend
    $this->pdf->SetXY($XPage, $yval);
    $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
    $i++;
  }
  
  //Scales
  $minPos=($minVal * $unit);
  $maxPos=($maxVal * $unit);
  
  $unit=($maxPos-$minPos)/$nbDiv;
  // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
  
  for ($i = $nullijn+$XDiag; $i <= $maxVal; $i=$i+$unit)
  {
    $xpos = $XDiag +  $i;
    $this->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
    $val = $i * $valIndRepere;
    $xpos = $XDiag +  $i - $this->pdf->GetStringWidth($val) / 2;
    $ypos = $YDiag + $hDiag - $margin;
    $this->pdf->Text($xpos, $ypos, $val);
  }
}

function PieChart($w, $h, $data, $format, $colors=null)
{
  
  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  $this->pdf->SetLegends($data,$format);
  
  $XPage = $this->pdf->GetX();
  $YPage = $this->pdf->GetY();
  $margin = 0;
  $hLegend = 2;
  $radius = min($w - $margin * 4  , $h - $margin * 2); //
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
  $this->pdf->SetLineWidth($this->pdf->lineWidth);
  $angleStart = 0;
  $angleEnd = 0;
  $i = 0;
  foreach($data as $val) {
    $angle = floor(($val * 360) / doubleval($this->pdf->sum));
    if ($angle != 0) {
      $angleEnd = $angleStart + $angle;
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
      $angleStart += $angle;
    }
    $i++;
  }
  if ($angleEnd != 360) {
    $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
  }
  
  //Legends
  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
  $x1 = $XPage - $radius - 22 ;
  $x2 = $x1 + $hLegend + $margin - 12;
  $y1 = $YDiag - $radius + $hLegend*2;
  
  for($i=0; $i<$this->pdf->NbVal; $i++) {
    $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
    $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
    $this->pdf->SetXY($x2,$y1);
    $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
    $y1+=$hLegend + $hLegend;
  }
  
}



}
?>