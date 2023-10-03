<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_101/RapportKERNZ_L101.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_101/RapportPERFG_L101.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_101/ATTberekening_L101.php");

class RapportOIH_L101
{
  function RapportOIH_L101($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->kernz=new RapportKERNZ_L101($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->perfg=new RapportPERFG_L101($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIH";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Rendement indivitueel";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }
  
  function writeRapport()
  {
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder,
Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q = "SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
  

  
    $portefeuilles=array($this->portefeuille);
    if(is_array($this->pdf->portefeuilles) && count($this->pdf->portefeuilles)>1)
    {
      foreach($this->pdf->portefeuilles as $portefeuille)
        $portefeuilles[]=$portefeuille;
    }

  
    foreach($portefeuilles as $portefeuille)
    {
      $this->pdf->rapport_titel = "Rendement individueel $portefeuille";
      if($portefeuille <> $this->portefeuille)
      {
        $DB=new DB();
        $query="SELECT Portefeuilles.Selectieveld1, Depotbanken.Omschrijving
          FROM
          Portefeuilles
          JOIN Depotbanken ON Portefeuilles.Depotbank=Depotbanken.Depotbank
          WHERE Portefeuilles.Portefeuille='$portefeuille' ";
        $DB->SQL($query);
        $naam=$DB->lookupRecord();
        
        $this->pdf->rapport_titel = "Rendement individueel ".($naam['Selectieveld1']<>''?$naam['Selectieveld1']:$naam['Omschrijving']);
      }
      $this->pdf->AddPage();
      if(!isset($this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas']))
      {
        $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
      }
  
      if($portefeuille <> $this->portefeuille)
      {
        $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum, (substr($this->rapportageDatum, 5, 5) == '01-01')?true:false,'EUR', $this->rapportageDatumVanaf);
        vulTijdelijkeTabel($fondswaarden, $portefeuille,  $this->rapportageDatum);
      }
      $huidigeJaarData = $this->getDataHuidigejaar($portefeuille);
      $this->addVerdelingsGrafiek($portefeuille, $allekleuren, 'OIB', $this->pdf->marge);
      $this->addVerdelingsGrafiek($portefeuille, $allekleuren, 'OIV', $this->pdf->marge + 60);
      $this->toonTopTien($huidigeJaarData);
      $this->addRendementsgrafiek($portefeuille);
      $this->getRendementsverdeling($portefeuille);
    }
    unset($this->pdf->CellBorders);
  }
  
  
  
  function addRendementsgrafiek($portefeuille)
  {
  
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder,
Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" .$portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $portefeuilledata = $DB->nextRecord();
    
    $huidigeJaarGrafiek=array();
    $index=new indexHerberekening();
    $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$portefeuille);
  
    $laatsteDatum='leeg';
    $cumulatieveWaarde=array();
    foreach ($indexData as $i=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
        $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
        $barGraph['Index'][$data['datum']]['leeg']=0;
        foreach ($data['extra']['cat'] as $categorie=>$waarde)
        {
          if($categorie=='LIQ'||$categorie=='H-Liq')
            $categorie='Liquiditeiten';
          $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
          if($waarde <> 0)
            $categorien[$categorie]=$categorie;
        }
        $cumulatieveWaarde[$data['datum']]=$data['waardeHuidige'];
      }
    
      $huidigeJaarGrafiek[$data['datum']]['performance']=$data['performance'];
      $huidigeJaarGrafiek[$data['datum']]['performanceCumu']=((1+$huidigeJaarGrafiek[$laatsteDatum]['performanceCumu']/100) * (1+$data['performance']/100)-1) * 100;
      $laatsteDatum=$data['datum'];
    }
  
    if($portefeuilledata['SpecifiekeIndex'] <> '')
    {
      $stdev = getFondsPerformanceGestappeld2($portefeuilledata['SpecifiekeIndex'], $portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, 'maanden', false, true, true);
      $laatsteDatum = 'leeg';
      foreach ($stdev->reeksen['benchmark'] as $datum => $rendementDetails)
      {
        $huidigeJaarGrafiek[$datum]['benchmark'] = $rendementDetails['perf'];
        $huidigeJaarGrafiek[$datum]['benchmarkCumu'] = ((1 + $huidigeJaarGrafiek[$laatsteDatum]['benchmarkCumu'] / 100) * (1 + $rendementDetails['perf'] / 100) - 1) * 100;
        $laatsteDatum = $datum;
        $lastMonth['benchmark']['performance'] = $rendementDetails['perf'];
      }
    }
    
    
    $this->pdf->SetXY(15,112)		;//112
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(138, 5, vertaalTekst('Rendement lopend jaar',$this->pdf->rapport_taal), 0, 1);
    $this->pdf->Line(15, $this->pdf->GetY(),15+120,$this->pdf->GetY());
   
    $this->pdf->SetXY(15,180)		;//112
    $kleurPerPortefeuille[$portefeuille]=array(203,187,160);
    $this->perfg->VBarDiagram2(120,60,$huidigeJaarGrafiek,true,false,$kleurPerPortefeuille);
  
  }
  
  
  function getRendementsverdeling($portefeuille)
  {
    $tmpPdf=new PDFRapport('L','mm');
    loadLayoutSettings($tmpPdf, $portefeuille);
    $tmpRapport=new  RapportOIH_L101($tmpPdf, $portefeuille, $this->rapportageDatumVanaf,  $this->rapportageDatum);
    $att=new ATTberekening_L101($tmpRapport);
    $waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'EUR','categorie');
    $grafiekWaarden=array();
    foreach($waarden['Periode'] as $categorie=>$categorieData)
    {
      foreach($categorieData['perfWaarden'] as $datum=>$perfWaarden)
      {
        if($categorie <> 'Liquiditeiten')
        {
          if($categorie<>'totaal')
            $grafiekWaarden[$datum][$categorie]=round($perfWaarden['bijdrage']*100,4);
          //else
          //  $grafiekWaarden[$datum]['kosten']=round($perfWaarden['kostenpercentage'],4);
        }
      }
    }
  

    $this->pdf->SetXY(160,112)		;//112
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(0, 5, vertaalTekst('Rendementsverdeling',$this->pdf->rapport_taal), 0, 1);
    $this->pdf->Line(160, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
    $this->pdf->SetXY(160,180)		;//112
    $this->VBarDiagram(80, 60, $grafiekWaarden);
    
    
   // listarray($grafiekWaarden);
   // listarray($att->categorien);
   // listarray($waarden);
  }
  
  
  function VBarDiagram($w, $h, $data)
  {
    global $__appvar;
    $legendaWidth = 00;
    $grafiekPunt = array();
    $verwijder=array();
    
    $q="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving as Omschrijving,beleggingscategorieVolgorde
     FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND Beleggingscategorie NOT IN('','Liquiditeiten')
     GROUP BY Beleggingscategorie  ORDER BY beleggingscategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
    $DB=new DB();
    $DB->SQL($q);
    $DB->Query();
    while($cdata=$DB->nextRecord())
    {
      $this->categorieVolgorde[$cdata['Beleggingscategorie']]=$cdata['Beleggingscategorie'];
      $this->categorieOmschrijving[$cdata['Beleggingscategorie']]=vertaalTekst($cdata['Omschrijving'],$this->pdf->rapport_taal);
    }
  //  $this->categorieVolgorde['kosten']='kosten';
  //  $this->categorieOmschrijving['kosten']=vertaalTekst('Kosten',$this->pdf->rapport_taal);
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    $grafiekSom=array();
    $grafiekNegatiefSom=array();
    foreach ($data as $datum=>$waarden)
    {
      $legenda[$datum] = $datum;//jul2form(db2jul($datum));
      $n=0;

      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        //foreach ($waarden as $categorie=>$waarde)
        //{
        if($categorie=='LIQ')
          $categorie='Liquiditeiten';
        $grafiek[$datum][$categorie]=$waarden[$categorie];
        $grafiekSom[$datum]+=$waarden[$categorie];
        $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
        $categorien[$categorie] = $n;
        $categorieId[$n]=$categorie ;
        
        
        
        if($waarden[$categorie] < 0)
        {
          unset($grafiek[$datum][$categorie]);
          $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
          $grafiekNegatiefSom[$datum]+=$waarden[$categorie];
        }
        else
          $grafiekNegatief[$datum][$categorie]=0;
        
        
        if(!isset($colors[$categorie]))
          $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        $n++;
      }
    }
    
    $maxVal=max($grafiekSom);
    $minVal=min($grafiekNegatiefSom);
    $numBars = count($legenda);
    
    $color=array(155,155,155);
    
    if($maxVal <= 5)
      $maxVal=5;
    elseif($maxVal <= 10)
      $maxVal=10;
    elseif($maxVal < 20)
      $maxVal=20;
    elseif($maxVal < 50)
      $maxVal=50;
    elseif($maxVal < 100)
      $maxVal=100;
    
    if($minVal >= 0)
      $minVal = 0;
    elseif($minVal > -5)
      $minVal=-5;
    elseif($minVal > -10)
      $minVal=-10;
    elseif($minVal > -20)
      $minVal=-20;
    elseif($minVal > -50)
      $minVal=-50;
    elseif($minVal > -100)
      $minVal=-100;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda
    
    $n=0;
    foreach (($this->categorieVolgorde) as $categorie)//array_reverse
    {
      if(is_array($grafiekCategorie[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
        $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
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
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / ($this->pdf->NbVal + 0.5));
    
    $eBaton = ($vBar * 0.5);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
  
    $legendaPrinted=array();
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
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
       // $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.'),0,0,'C');
        }
        //$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        
        if($legendaPrinted[$datum] != 1)
          $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,date('d-m-y',db2jul($legenda[$datum])),25);
        /*
        if($legendaPrinted[$datum] != 1)
        {
          $this->pdf->SetXY($xval,$YstartGrafiek+4);
          $this->pdf->Cell($eBaton,0,$legenda[$datum],0,0,'C');
        }
        */
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
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
       // $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.'),0,0,'C');
        }
        //$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        
        if($legendaPrinted[$datum] != 1)
        {
          //$this->pdf->SetXY($xval,$YstartGrafiek+4);
          //$this->pdf->Cell($eBaton,0,$legenda[$datum],0,0,'C');
          $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,date('d-m-y',db2jul($legenda[$datum])),25);
        }
        //$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$datum] = 1;
      }
      $i++;
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
  
  function formatGetal($waarde, $dec,$procent=false,$toonNul=false)
  {
    if($waarde==0 && $toonNul==false)
      return '';
    $data=number_format($waarde,$dec,",",".");
    if($procent==true)
      $data.="%";
    return $data;
  }
  
  function addVerdelingsGrafiek($portefeuille,$allekleuren,$type='OIB',$xLocatie=8)
  {
    global $__appvar;

    $grafiekData=array();
    
    if($type=='OIV')
    {
      $verdeling='Valuta';
    }
    else
    {
      $verdeling='beleggingscategorie';
    }
  

    $query="SELECT rapportageDatum, ".$verdeling.", ".$verdeling."Omschrijving, SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage
    WHERE TijdelijkeRapportage.rapportageDatum IN('".$this->rapportageDatum."') AND
    TijdelijkeRapportage.portefeuille =  '".$portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
    GROUP BY rapportageDatum, $verdeling ORDER BY rapportageDatum, ".$verdeling."Volgorde ";
  
    $db=new DB();
    $db->SQL($query);
    $db->Query();
    $verdelingOpDatum=array();
    $categorieOmschrijving=array();
    $totaleWaarde=array();
    while($data=$db->nextRecord())
    {
      $verdelingOpDatum[$data['rapportageDatum']][$data[$verdeling]]+=$data['actuelePortefeuilleWaardeEuro'];
      $categorieOmschrijving[$data[$verdeling]]=$data[$verdeling.'Omschrijving'];
      $totaleWaarde[$data['rapportageDatum']]+=$data['actuelePortefeuilleWaardeEuro'];
    }
    
    if(!isset($allekleuren[$type]['Geen']))
      $allekleuren[$type]['Geen']=array('R'=>array('value'=>10),'G'=>array('value'=>10),'B'=>array('value'=>110));
    foreach($verdelingOpDatum as $datum=>$hoofdCategorieData)
    {
      foreach($hoofdCategorieData as $categorie=>$waarde)
      {
        $kleur=$allekleuren[$type][$categorie];
        $percentage=$waarde/$totaleWaarde[$datum]*100;
        $grafiekData[$datum]['percentage'][$categorieOmschrijving[$categorie].' ('.$this->formatGetal($percentage,1).'%)']=$percentage;
        $grafiekData[$datum]['kleur'][] = array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
      }
    }
    $y=40;
    $this->pdf->setXY($xLocatie,$y);
    $this->kernz->PieChart(50, 50,  $grafiekData[$this->rapportageDatum]['percentage'], '%l', $grafiekData[$this->rapportageDatum]['kleur'],array($xLocatie+15,$y+50));
    
  }
  
  
  
  function getDividend($fonds,$portefeuille)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
    
    $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $totaal=0;
    while($data = $DB->nextRecord())
    {
      if($data['type']=='rente')
        $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
      elseif($data['type']=='fondsen')
        $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
    }
    
    $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
    $totaalCorrected=$totaal;
    
    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND
     Grootboekrekeningen.Opbrengst=1";
    $DB->SQL($query);
    $DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    {
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      }
      // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }
  
  function testTxtLength($txt,$cell=1)
  {
    $stringWidth=$this->pdf->GetStringWidth($txt."   ");
    if($stringWidth < $this->pdf->widths[$cell])
    {
      return $txt;
    }
    else
    {
      $tmpTxt=$txt;
      for($i=strlen($txt); $i > 0; $i--)
      {
        if($this->pdf->GetStringWidth($tmpTxt."...   ")>$this->pdf->widths[$cell])
          $tmpTxt=substr($txt,0,$i);
        else
          return $tmpTxt.'...';
      }
      return $tmpTxt;
    }
  }
  
  
  function toonTabel($xmarge,$tabeldata,$titel)
  {
    $this->pdf->setY(40);
    $this->pdf->SetWidths(array($xmarge,6,60,12));
    $this->pdf->SetAligns(array('L','L','L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('',array('L','T','U'),array('T','U','L'),array('T','U','L','R'));
    $this->pdf->row(array('','',$titel,'in %'));
    $this->pdf->CellBorders=array('',array('L'),array('L'),array('L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $n=1;
    $aantal=count($tabeldata);
    foreach($tabeldata as $fonds=>$fondsData)
    {
      //$this->pdf->row(array('',$n,$fondsData['fondsOmschrijving'],round($fondsData['rendementBijdrage']*100)));
      $omschrijving=$this->testTxtLength($fondsData['fondsOmschrijving'],2);
      if($n==$aantal)
      {
        $this->pdf->CellBorders=array('',array('L','U'),array('U','L'),array('U','L','R'));
      }
      $this->pdf->row(array('',$n,$omschrijving,$this->formatGetal($fondsData['rendementBijdrage'],2,false,true)));//
      $n++;
    }
    
  }
  
  function toonTopTien($data)
  {
  
  //  $this->pdf->ln(10);
    $this->toonTabel(115,$data['positief'],"Grootste bijdrage");
    $this->toonTabel(200,$data['negatief'],"Kleinste bijdrage");
  
    $this->pdf->fillCell=array();
  
  
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
  }
  
  
  function getDataHuidigejaar($portefeuille)
  {
    global $__appvar;
  
  
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    $this->pdf->SetDrawColor(0,0,0);
    // haal totaalwaarde op om % te berekenen
    
    $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.type,
				 Fondsen.isinCode as isinCode,
				 TijdelijkeRapportage.historischeWaarde,
				 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.rekening ".
      " FROM TijdelijkeRapportage
				  LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
      " TijdelijkeRapportage.type IN('fondsen') AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
    
    // print detail (select from tijdelijkeRapportage)
    debugSpecial($subquery,__FILE__,__LINE__);
    $DB2 = new DB();
    $DB2->SQL($subquery);
    $DB2->Query();
    $resulaten=array();
    $fondsGegevens=array();
    while($subdata = $DB2->NextRecord())
    {
      
      $dividend=$this->getDividend($subdata['fonds'],$portefeuille);
      $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
      $aandeel=$subdata['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $procentResultaatBijdrage=$procentResultaat*$aandeel;
      
      if($subdata['beginPortefeuilleWaardeEuro'] < 0)
        $procentResultaat = -1 * $procentResultaat;
      
      
      if($procentResultaat < 1000 || $procentResultaat > -1000)
      {
        $resulaten[$subdata['fonds']]=$procentResultaatBijdrage;
        $subdata['rendement']=$procentResultaat;
        $subdata['rendementBijdrage']=$procentResultaatBijdrage;
      }
      $fondsGegevens[$subdata['fonds']]=$subdata;
      
    }
    asort($resulaten);
    $i=0;
    $negatief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $negatief[$fonds]=$fondsGegevens[$fonds];
      if($i==9)
        break;
      $i++;
    }
    $resulaten=array_reverse($resulaten,true);
    $i=0;
    $positief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $positief[$fonds]=$fondsGegevens[$fonds];
      if($i==9)
        break;
      $i++;
    }
    
    return array('positief'=>$positief,'negatief'=>$negatief);
  
  }
  
}
