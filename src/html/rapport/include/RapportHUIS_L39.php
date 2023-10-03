<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/28 06:59:00 $
File Versie					: $Revision: 1.12 $
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportPERFG_L39.php");

class RapportHUIS_L39
{

	function RapportHUIS_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    
    $this->pdf = &$pdf;
    $this->portefeuille=$portefeuille;
    $this->perfg=new RapportPERFG_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->pdf->rapport_type = "HUIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "";
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;
    
    $this->kleurKop1=array(134,114,100);
    $this->kleurKop2=array(179,165,153);
    $this->kleurRegel=array(220,214,210);
    $this->tussencell=1;
	}

	function kop($kopData)
  {
    $totalWidth=0;
    $paginaWidth=210-$this->pdf->marge*2;
    $kopArray=array();
    $kopWidth=array();
    $fillCell=array();
    foreach($kopData as $kopTxt=>$width)
    {
      $totalWidth+=$width;
     
    }
    $i=1;
    $aantaKoppen=count($kopData);
    foreach($kopData as $kopTxt=>$width)
    {
      if($i==$aantaKoppen)
        $tussencell=0;
      else
        $tussencell=$this->tussencell;
      $cellWidth=$width/$totalWidth*$paginaWidth;
      
      $kopWidth[]=$cellWidth-$tussencell;
      $kopArray[]=$kopTxt;
      $fillCell[]=1;
  
      $kopWidth[] = $tussencell;
      $kopArray[]= '';
      $fillCell[]=0;

      
      $i++;
    }
    
    $this->pdf->fillCell=$fillCell;
    $this->pdf->setWidths($kopWidth);
    $this->pdf->setAligns(array('L','L','L','L','L'));
    $this->pdf->SetFillColor($this->kleurKop1[0],$this->kleurKop1[1],$this->kleurKop1[2]);
  //  $this->pdf->rect($this->pdf->marge,$this->pdf->getY()-1,$this->pdf->w-$this->pdf->marge*2,6,'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(255,255,255);
    $oldRowHeight=$this->pdf->rowHeight;
    $this->pdf->rowHeight=6;
    $this->pdf->row($kopArray);
    $this->pdf->rowHeight=$oldRowHeight;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->ln(2);
    unset($this->pdf->fillCell);
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

	function writeRapport()
	{
	  global $__appvar;
    $this->pdf->addPage('P');
   
    $this->pdf->setWidths(array($this->pdf->w-$this->pdf->marge*2));
    /*
    $this->kop(array('Beleggingsvisie'=>100));
 
    $this->pdf->row(array('Capitael belegt in kwalitatief goede, winstgevende ondernemingen die een structurele omzet- en winstgroei realiseren. Bedrijven die veelal dividend uitkeren en internationaal opereren. Deze ondernemingen hebben vaak ook omzet- en winstgroei geboekt in de jaren dat er crisis was. Bedrijven die de waan van de dag naast zich neerleggen, maar wel een duidelijke visie hebben ten aanzien de snel veranderende wereld. Ondernemingen die niet zo afhankelijk zijn van banken en een hoge kasstroom hebben.

De portefeuille wordt samengesteld door een team van ervaren beleggers op basis van professionele research.'));
    $this->pdf->ln(4);
    */

    include_once($__appvar["basedir"]."/html/indexBerekening.php");
    $index=new indexHerberekening();
    $jaarRendement=array();
    $jaarRendementCumulatief=array();
    $cumulatiefRendement=0;
    $maandRendement=array();
    $indices=array();
    $indicesBoven=array('portefeuille'=>'Portefeuille');
    $indicesOverig=array();
    $db=new DB();
    
    $query="SELECT Fondsen.Fonds,Fondsen.Omschrijving, if(Fondsen.Fonds='AEX',1,0) as volgorde FROM Fondsen WHERE Fondsen.Fonds IN('".mysql_real_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."','AEX') ORDER BY volgorde";
    $db->SQL($query);
    $db->query();
    while($data=$db->nextRecord())
      if($data['Fonds']<>'')
      {
        $indices[$data['Fonds']] = $data['Omschrijving'];
        $indicesBoven[$data['Fonds']] = $data['Omschrijving'];
      }

    
    $query="SELECT Fondsen.Fonds,Fondsen.Omschrijving FROM Fondsen JOIN Indices ON Fondsen.fonds=Indices.BeursIndex AND Indices.specialeIndex=1 AND Indices.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' WHERE Indices.BeursIndex <> ''";
    $db->SQL($query);
    $db->query();
    while($data=$db->nextRecord())
    {
      $indices[$data['Fonds']]=$data['Omschrijving'];
      $indicesOverig[$data['Fonds']]=$data['Omschrijving'];
    }
    
    $query="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $db->query();
    $kleuren=$db->nextRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    $totaleWaarde=0;
    $verdeling=array();
    $omschrijvingen=array();
    $volgorde=array();
    $top10=array('Top holdings'=>'');
    $fondsen=array();
    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatum,(substr($this->rapportageDatum, 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$this->rapportageDatumVanaf);
    foreach($fondswaarden as $fonds)
    {
      $totaleWaarde+=$fonds['actuelePortefeuilleWaardeEuro'];
      if($fonds['fonds']<>'')
        $fondsen[$fonds['fondsOmschrijving']]+=$fonds['actuelePortefeuilleWaardeEuro'];
    }
    arsort($fondsen);
    $i=0;
    foreach($fondsen as $fonds=>$waarde)
    {
      $percentage=$waarde/$totaleWaarde*100;
      $top10[$fonds]=$percentage;
      $top10['Top holdings']+=$percentage;
      if($i==9)
        break;
      $i++;
    }
  
   
    $verdelingSoorten=array('beleggingssector','valuta','Regio');
    foreach($fondswaarden as $fonds)
    {
      foreach($verdelingSoorten as $soort)
      {
        if($fonds[$soort]=='')
          $fonds[$soort]='Overigen';
        if($fonds[strtolower($soort).'Omschrijving']=='')
          $fonds[strtolower($soort).'Omschrijving']='Overigen';
        if($fonds[strtolower($soort).'Volgorde']=='')
          $fonds[strtolower($soort).'Volgorde']=127;
        
        $verdeling[$soort][$fonds[$soort]] += $fonds['actuelePortefeuilleWaardeEuro'] / $totaleWaarde;
        $omschrijvingen[$soort][$fonds[$soort]] = $fonds[strtolower($soort).'Omschrijving'] ;
        $volgorde[$soort][$fonds[$soort]] = $fonds[strtolower($soort).'Volgorde'] ;
      }
    }
    


    
    
    
    $rendementData=$index->getWaarden($this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex']);
    $grafiekData=array();
    $indexMaandRendementen=array();
    $jaarRendement=array();
    $jaarRendementCumulatief=array();
    $indexCumulatief=array();
    $indexFonds=array();
    foreach($rendementData as $details)
    {
      $periode=explode('->',$details['periode']);
      $jaar=substr($details['datum'],0,4);
      $maandRendement[$details['datum']]=$details['performance'];
  
      $grafiekData['Datum'][] = $details['datum'];
      $grafiekData['Index'][] = $details['index']-100;
      if($this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
      {
        $grafiekData['IndexExtra'][$this->pdf->portefeuilledata['SpecifiekeIndex']][] = $details['specifiekeIndex'] - 100;
        $indexFonds[1]=$details['specifiekeIndex'];
        $indexCumulatief[$this->pdf->portefeuilledata['SpecifiekeIndex']][$details['datum']]=$details['specifiekeIndex'] - 100;
      }
  
      if($this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
        $indexMaandRendementen[$this->pdf->portefeuilledata['SpecifiekeIndex']][$details['datum']]=$details['specifiekeIndexPerformance'];
      
      $jaarRendement[$jaar]['portefeuille']=((1+$jaarRendement[$jaar]['portefeuille']/100)*(1+$details['performance']/100)-1)*100;
      $jaarRendementCumulatief[$jaar]['portefeuille']=$details['index']-100;
      if($this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
      {
        $jaarRendement[$jaar][$this->pdf->portefeuilledata['SpecifiekeIndex']] = ((1 + $jaarRendement[$jaar][$this->pdf->portefeuilledata['SpecifiekeIndex']] / 100) * (1 + $details['specifiekeIndexPerformance'] / 100) - 1) * 100;
        $jaarRendementCumulatief[$jaar][$this->pdf->portefeuilledata['SpecifiekeIndex']] = $details['specifiekeIndex'] - 100;
      }
      $i=2;
      foreach($indices as $index=>$omschrijving)
      {
        if($index==$this->pdf->portefeuilledata['SpecifiekeIndex'])
          continue;
        
        $indexMaandRendementen[$index][$details['datum']] = getFondsPerformance($index, $periode[0], $periode[1]);
  
        $jaarRendement[$jaar][$index]=((1+$jaarRendement[$jaar][$index]/100)*(1+$indexMaandRendementen[$index][$details['datum']] /100)-1)*100;
        
        $indexCumulatief[$index]=((1+$indexCumulatief[$index]/100)*(1+$indexMaandRendementen[$index][$details['datum']] /100)-1)*100;
        $jaarRendementCumulatief[$jaar][$index] = $indexCumulatief[$index];
  
        $grafiekData['IndexExtra'][$index][] = $indexCumulatief[$index];
        $indexFonds[$i]=$index;
        $i++;
      }
     

      //$cumulatiefRendement=((1+$cumulatiefRendement/100)*(1+$details['performance']/100)-1)*100;
     // $jaarRendementCumulatief[$jaar]=$cumulatiefRendement;
    }
    
    $cumulatiefFondsRendement=array();
    foreach($indexMaandRendementen as $fonds=>$fondsRendementen)
    {
      foreach($fondsRendementen as $datum=>$percentage)
      {
        $cumulatiefFondsRendement[$fonds]=(1+$cumulatiefFondsRendement[$fonds])*(1+$percentage/100)-1;
      }
    }




    $this->kop(array('Top 10 grootste belangen'=>40+20+1,'Rendement'=>133-1));
    
    $valX = $this->pdf->GetX();
    $valY = $this->pdf->GetY();
    $this->pdf->setWidths(array(40,20,133));
    $this->pdf->setAligns(array('L','R'));
    $i=0;
    
    /*
     *  $this->pdf->fillCell=$fillCell;
    $this->pdf->setWidths($kopWidth);
    $this->pdf->setAligns(array('L','L','L','L','L'));
    $this->pdf->SetFillColor($this->kleurKop1[0],$this->kleurKop1[1],$this->kleurKop1[2]);
  //  $this->pdf->rect($this->pdf->marge,$this->pdf->getY()-1,$this->pdf->w-$this->pdf->marge*2,6,'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
     */
    foreach($top10 as $omschrijving=>$percentage)
    {
      if($i==0)
      {
        $this->pdf->SetFillColor($this->kleurKop2[0],$this->kleurKop2[1],$this->kleurKop2[2]);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->fillCell=array(1,1,1);
      }
      elseif($i==1)
      {
        $this->pdf->SetFillColor($this->kleurRegel[0],$this->kleurRegel[1],$this->kleurRegel[2]);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      }
      if($i%2==0)
        $this->pdf->fillCell=array(1,1,1);
      else
        $this->pdf->fillCell=array();
  
    
      $omschrijvingTonen=$omschrijving;
      $width=$this->pdf->GetStringWidth($omschrijving);
      if($width>38)
      {
        for($k=0;$k<100;$k++)
        {
          $omschrijvingTonen = substr($omschrijvingTonen, 0, strlen($omschrijvingTonen) - 1);
          $width = $this->pdf->GetStringWidth($omschrijvingTonen . '...');
          if($width<38)
          {
            $omschrijvingTonen=$omschrijvingTonen . '...';
            break;
          }
        }
        
      }
      
      $this->pdf->row(array($omschrijvingTonen,$this->formatGetal($percentage,2).'%'));
      $i++;
    }
    unset($this->pdf->fillCell);
     // listarray($top10);
    
    $w=132/7;
    $widths=array(61,$w);
    $aligns=array('C','C');
    for($i=0;$i<15;$i++)
    {
      $widths[] = $w;
      $aligns[] ='R';
    }
    $this->pdf->setAligns(array('L','C','C','C','C'));
    $this->pdf->setWidths(array(61,$w,$w*2,$w*2,$w*2));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $row=array('','');
    foreach($indicesBoven as $index=>$omschrijving)
    {
      if($index==$this->pdf->portefeuilledata['SpecifiekeIndex'])
        $omschrijving='Benchmark';
      $row[]=$omschrijving;
    }
    $this->pdf->setY($valY);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array(0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1);
    $this->pdf->SetFillColor($this->kleurKop2[0],$this->kleurKop2[1],$this->kleurKop2[2]);
    $this->pdf->row($row);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setWidths($widths);

 
    $this->pdf->SetFillColor($this->kleurRegel[0],$this->kleurRegel[1],$this->kleurRegel[2]);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $i=0;
    $this->pdf->setAligns($aligns);
    foreach($jaarRendement as $jaar=>$indexData)
    {
      $row=array('',$jaar);
      $n=0;
     // listarray($indexData);
      foreach($indicesBoven as $element=>$omschrijving)//
      {
        $rendement=$indexData[$element];
        if($n>2)
          break;
        $row[] = $this->formatGetal($rendement,1).'%';
        $row[] = $this->formatGetal($jaarRendementCumulatief[$jaar][$element],1).'%';
        $n++;
      }
      if($i%2==1)
        $this->pdf->fillCell=array(0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1);
      else
        $this->pdf->fillCell=array();
      
      $this->pdf->row($row);
      $i++;
    }
    
   // for($i=0;$i<8;$i++)
   //   $this->pdf->row(array('regel',$i));

    $valY=73;
    if (count($grafiekData) > 1)
    {
      if($this->pdf->getY()>$valY)
        $hoogtecorrectie=$valY-$this->pdf->getY();
      else
        $hoogtecorrectie=0;
     // echo $hoogtecorrectie;exit;
      //echo $this->pdf->getY();exit;
      $this->pdf->SetXY($valX+10, $valY+2-$hoogtecorrectie);
      
      $query="SELECT Portefeuilles.kleurcode FROM Portefeuilles WHERE portefeuille='".$this->portefeuille."'";
      $db->SQL($query);
      $pKleur=$db->lookupRecord();
      $pKleur=unserialize($pKleur['kleurcode']);
      if(count($pKleur)<3)
        $pKleur=array(0,38,84);
      $query="SELECT grafiekKleur FROM BeleggingscategoriePerFonds WHERE Fonds='".mysql_real_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->SQL($query);
      $fKleur=$db->lookupRecord();
      $fKleur=unserialize($fKleur['grafiekKleur']);
      $fKleur=array($fKleur['R']['value'],$fKleur['G']['value'],$fKleur['B']['value']);
      if(count($fKleur)<3)
        $fKleur=array(0,38,84);
      $colors=array('Portefeuille'=>$pKleur,$this->pdf->portefeuilledata['SpecifiekeIndex']=>$fKleur);
  
      $legenda=array('Portefeuille'=>'Portefeuille',$this->pdf->portefeuilledata['SpecifiekeIndex']=>$this->pdf->portefeuilledata['SpecifiekeIndex']);
      foreach($indexFonds as $index=>$fonds)
      {
        $query="SELECT grafiekKleur FROM BeleggingscategoriePerFonds WHERE Fonds='".mysql_real_escape_string($fonds)."' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
        $db->SQL($query);
        $Kleur=$db->lookupRecord();
        $Kleur=unserialize($Kleur['grafiekKleur']);
        $Kleur=array($Kleur['R']['value'],$Kleur['G']['value'],$Kleur['B']['value']);
       // $Kleur=array(rand(0,255),rand(0,255),rand(0,255));
        $colors[$fonds]=$Kleur;
        $legenda[$fonds]=$indices[$fonds];
      }
      
      
      $this->LineDiagram(180, 70+$hoogtecorrectie, $grafiekData,$colors,0,0,6,5,1);//50

      
      $i=0;
      $j=0;
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $ystart=156;
      $extraY=0;
      foreach($legenda as $fonds=> $item)
      {
        if($i%2==0)
          $this->pdf->SetXY($valX+4+80*($i/2), $ystart+$extraY);
        else
          $this->pdf->SetXY($valX+4+80*($i-1)/2, $ystart+$extraY);
        if($item<>'')
        {
          $j++;
          $this->pdf->SetFillColor($colors[$fonds][0],$colors[$fonds][1],$colors[$fonds][2]);
          $this->pdf->rect($this->pdf->getX()-3, $this->pdf->getY(), 3, 3, 'F');
          if($item==$this->pdf->portefeuilledata['SpecifiekeIndex'])
          {
            $omschrijving = 'Benchmark, ' . $indices[$this->pdf->portefeuilledata['SpecifiekeIndex']];
            if($this->pdf->portefeuilledata['SpecifiekeIndex']=='')
              continue;
          }
          else
          {
            $omschrijving = $item;
          }
          $this->pdf->MultiCell(80,4, vertaalTekst($omschrijving,$this->pdf->rapport_taal), 0,"L");
        }
        else
        {
          $j++;
          continue;
        }

        if($i%2==0)
        {
          $extraY = 5;
        }
        else
        {
          $extraY = 0;
        }
        $i++;
        
      }
      $this->pdf->SetXY($valX, 158);
    }
    $this->pdf->setDrawColor(0,0,0);
    
    
    
   
    
    /*  */
    
    $this->pdf->ln(8);
    
    
    if(count($indicesOverig)>0)
    {
    $startY=$this->pdf->getY();
    $this->pdf->setXY($this->pdf->marge,$startY);
    $this->pdf->setWidths(array(210-$this->pdf->marge*2));
    $this->pdf->setAligns(array('L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->kleurKop2[0],$this->kleurKop2[1],$this->kleurKop2[2]);
    $this->pdf->fillCell=array(1,1);
    $this->pdf->row(array('Rendement overige indices'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->kleurRegel[0],$this->kleurRegel[1],$this->kleurRegel[2]);
    
    
    $i=0;
    $row=array();
    $widhts=array();
    $aligns=array();
    $fill=array();
    $this->pdf->SetFillColor($this->kleurKop2[0],$this->kleurKop2[1],$this->kleurKop2[2]);
    
    //foreach($cumulatiefFondsRendement as $fonds=>$rendement)
    foreach($indicesOverig as $fonds=>$omschrijving)
    {
      $rendement=$cumulatiefFondsRendement[$fonds];
      $row[]=$omschrijving;//$indices[$fonds];
      $row[]=$this->formatGetal($rendement*100,1).'%';
      $row[]='';
      $widhts[]=42;
      $widhts[]=15;
      $widhts[]=5;
      $aligns[]='L';
      $aligns[]='R';
      $aligns[]='C';
    }
    unset($this->pdf->fillCell);
    $this->pdf->setWidths($widhts);
    $this->pdf->setAligns($aligns);
      $this->pdf->ln(1);
    $this->pdf->row($row);

    
    $this->pdf->ln(2);
    }
    
    $this->kop(array('Vermogensverdeling'=>100));
    
    
     //  listarray($verdeling);
    //  listarray($omschrijvingen);
    //  listarray($volgorde);
    
   // listarray($verdeling);
    $grafiekData=array();
    // $allekleuren
    
    $soorten=array_keys($verdeling);
    $kleurVertaling=array('beleggingssector'=>'OIS','valuta'=>'OIV','Regio'=>'OIR');
    //listarray($allekleuren);exit;
    foreach($soorten as $soort)
    {
      asort($volgorde[$soort]);
      foreach($volgorde[$soort] as $categorie=>$v)
      {
        $grafiekData[$soort][$omschrijvingen[$soort][$categorie]]['percentage']=$verdeling[$soort][$categorie];
        $grafiekData[$soort][$omschrijvingen[$soort][$categorie]]['kleur']=array($allekleuren[$kleurVertaling[$soort]][$categorie]['R']['value'],$allekleuren[$kleurVertaling[$soort]][$categorie]['G']['value'],$allekleuren[$kleurVertaling[$soort]][$categorie]['B']['value']);
      }
    
    } //listarray($grafiekData);
    $startX=20;
    $startY=$this->pdf->getY();
    $width=63;
    $this->printPie($grafiekData['beleggingssector'],$startX+$width*0,$startY,'Sectorverdeling');
    $this->printPie($grafiekData['valuta'],$startX+$width*1,$startY,'Valutaverdeling');
    $this->printPie($grafiekData['Regio'],$startX+$width*2,$startY,'Regioverdeling');


    
    //$this->pdf->row(array($indices[$this->pdf->portefeuilledata['SpecifiekeIndex']],$this->formatGetal($laatsteIndex,1).'%'));
    unset($this->pdf->fillCell);
	}
  
  
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $legendDatum= $data['Datum'];
    $allData = $data;
    $data = $data['Index'];
    $bereikdata =   $data;
    foreach($allData['IndexExtra'] as $fonds=>$indexData)
    {
      if(count($indexData) > 0)
        $bereikdata = array_merge($bereikdata, $indexData);
    }

    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );
    
    $allColors=$color;
    if(is_array($allColors))
    {
     // $color1= $color[1];
      $color = $allColors['Portefeuille'];
    }
    
    if($color == null)
      $color=array(0,38,84);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }
    
    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);
    
    if($jaar && count($data) < 12)
      $unit = $lDiag / 12;
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }
    
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
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
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
    
    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
    // $color=array(200,0,0);
    if(count($data) > 12)
      $jaarlijks=true;
    else
      $jaarlijks=false;
      
    $aantalPunten=count($data);
    for ($i=0; $i<$aantalPunten; $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      
      $maand=substr($legendDatum[$i],5,2);
      
      if(($jaarlijks==false && ($maand=='03'||$maand=='06'||$maand=='09'||$maand=='12')) || ($jaarlijks==true && $maand=='12') )
      {
        $this->pdf->line($XDiag+($i+1)*$unit+$extrax, $YDiag + $hDiag +1 , $XDiag+($i+1)*$unit+$extrax, $YDiag + $hDiag + 3,array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        $this->pdf->TextWithRotation($XDiag + ($i) * $unit - 10 + $unit, $YDiag + $hDiag + 8, jul2form(db2jul($legendDatum[$i])), 25);
      }
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
    //  $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
    //  if($data[$i] <> 0 && ($maand=='03'||$maand=='06'||$maand=='09'||$maand=='12'))
        if($i==$aantalPunten-1)
          $this->pdf->Text($XDiag+($i+1)*$unit-1+2,$yval2,$this->formatGetal($data[$i],1).'%');
      
      
      $yval = $yval2;
    }
//  listarray($allColors);
    foreach($allData['IndexExtra'] as $fonds=>$data1)
    {
      $aantalPunten=count($data1);
      if($aantalPunten > 0)
      {
        $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
        $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $allColors[$fonds]);
        for ($i=0; $i<count($data1); $i++)
        {
          $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
          $yval = $yval2;
          if($i==$aantalPunten-1)
            $this->pdf->Text($XDiag+($i+1)*$unit-1+2,$yval2,$this->formatGetal($data1[$i],1).'%');
        }
      }
    }

    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }
  
  
  
  function printPie($kleurdata,$xstart,$ystart,$titel)
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
    if($kleurdata)
    {
      $sorted 		= array();
      $percentages 	= array();
      $kleur			= array();
      $valuta 		= array();
      
      //$kleurdata=	array_reverse($kleurdata);
      //	listarray($kleurdata);
      foreach($kleurdata as $key=>$data)
      {
        $percentages[] 	= $data['percentage'];
        $kleur[] 			= $data['kleur'];
        $valuta[] 		= $key;
      }
      //arsort($percentages);
      
      foreach($percentages as $key=>$percentage)
      {
        $sorted[$valuta[$key]]['kleur']=$kleur[$key];
        $sorted[$valuta[$key]]['percentage']=$percentage;
      }
      $kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
      
      $pieData=array();
      $grafiekKleuren = array();
      
      $a=0;
      foreach($kleurdata as $key=>$value)
      {
        if ($value['kleur'][0] == 0 && $value['kleur'][1] == 0 && $value['kleur'][2] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['kleur'][0],$value['kleur'][1],$value['kleur'][2]);
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
      $grafiekKleuren = $standaardKleuren;
    
    $this->pdf->SetTextColor($this->pdf->pdf->rapport_fontcolor['r'],$this->pdf->pdf->rapport_fontcolor['g'],$this->pdf->pdf->rapport_fontcolor['b']);
    
    $this->pdf->rapport_printpie = true;
    foreach($pieData as $key=>$value)
    {
      if ($value < 0)
        $this->pdf->rapport_printpie = false;
    }
    
    if($this->pdf->rapport_printpie)
    {
      $this->pdf->SetXY($xstart, $ystart);
      $y = $this->pdf->getY();
      $this->pdf->SetFont($this->pdf->pdf->rapport_font,'b',10);
      $this->pdf->Cell(50,4,vertaalTekst($titel, $this->pdf->rapport_taal),0,1,"C");
      $this->pdf->SetFont($this->pdf->pdf->rapport_font,'',$this->pdf->pdf->rapport_fontsize);
      $this->pdf->SetX($xstart);
      $this->PieChart(50, 50, $pieData, '%l (%p)', $grafiekKleuren);
      $this->pdf->setY($y+50);
     // listarray($grafiekKleuren);
      $i=0;
      foreach($pieData as $omschrijving=>$percentage)
      {
        $this->pdf->Rect($xstart-8,$this->pdf->getY()+1,3,3,'F','',$grafiekKleuren[$i]);
        $this->pdf->SetX($xstart-5);
        $this->pdf->Cell(45,5,$omschrijving,0,0,'L');
        $this->pdf->Cell(10,5,$this->formatGetal($percentage*100,1).'%',0,1,'R');
          //Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
        $i++;
      }
    //  listarray($pieData);
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    //	$this->pdf->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < count($data); $i++) {
        $gray = $i * intval(255 / count($data));
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $sum=array_sum($data);
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    foreach($data as $val) {
      $angle = floor(($val * 360) / doubleval($sum));
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
    /*
        //Legends
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
        $x1 = $XPage ;
        $x2 = $x1 + $hLegend + $margin - 12;
        $y1 = $YDiag + ($radius) + $margin;
    
        for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $margin;
        }
    */
  }

}
