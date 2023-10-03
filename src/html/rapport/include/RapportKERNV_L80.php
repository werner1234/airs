<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/16 15:57:02 $
File Versie					: $Revision: 1.5 $

$Log: RapportKERNV_L80.php,v $
Revision 1.5  2020/05/16 15:57:02  rvv
*** empty log message ***

Revision 1.4  2019/06/08 16:06:01  rvv
*** empty log message ***

Revision 1.3  2019/06/05 16:39:04  rvv
*** empty log message ***

Revision 1.2  2019/05/30 06:03:33  rvv
*** empty log message ***

Revision 1.1  2019/05/29 15:45:16  rvv
*** empty log message ***

Revision 1.4  2019/04/07 11:06:41  rvv
*** empty log message ***

Revision 1.3  2019/04/06 17:11:28  rvv
*** empty log message ***

Revision 1.2  2019/02/03 13:43:54  rvv
*** empty log message ***

Revision 1.1  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.3  2018/12/09 13:00:15  rvv
*** empty log message ***

Revision 1.2  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVKM.php");

class RapportKERNV_L80
{
  function RapportKERNV_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    if(count($pdf->portefeuilles)>1)
    {
      $this->consolidatie=true;
      $this->verdeling1='beleggingscategorie';
      
    }
    else
    {
      $this->consolidatie=false;
    }
    $this->pdf->rapport_type = "KERNZ";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Onderverdeling in beleggingscategorieën";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->pdf->pieData = array();
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
  
  function getVerdeling($verdeling)
  {
    global $__appvar;
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum ."' AND ".
      " portefeuille = '". $this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];

    if($verdeling=='Liquiditeit6mnd')
    {
      $query="DESC FondsExtraInformatie";
      $DB->SQL($query);
      $DB->Query();
      $velden=array();
      while ($data = $DB->NextRecord())
      {
        $velden[]=$data['Field'];
      }
      if(!in_array('Liquiditeit6mnd',$velden))
      {
        return array ('pieData'=>array(),'kleurData'=>array(),'detailsPerCategorie'=>array());
      }
      
      $query="SELECT omschrijving FROM FondsExtraVelden WHERE veldnaam='Liquiditeit6mnd'";
      $DB->SQL($query);
      $DB->Query();
      $omschrijving = $DB->nextRecord();
      $liquiditeit6mndOmschrijving=$omschrijving['omschrijving'];
      
      $query="SELECT
FondsExtraInformatie.Liquiditeit6mnd as verdeling1,
if(FondsExtraInformatie.Liquiditeit6mnd=1,'$liquiditeit6mndOmschrijving','Overige') AS verdeling1Omschrijving,
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel
FROM
TijdelijkeRapportage
LEFT JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds = FondsExtraInformatie.fonds
			 WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND
			 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY
	verdeling1
ORDER BY
	TijdelijkeRapportage.beleggingscategorieVolgorde ASC";
    }
    else
    {
      $query = "SELECT TijdelijkeRapportage.type ,
       " . $verdeling . " as verdeling1,
       " . $verdeling . "Omschrijving as verdeling1Omschrijving,
	SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,
	SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel,
  if(TijdelijkeRapportage.type='fondsen',1,(if(TijdelijkeRapportage.type='rente',2,3))) as volgorde
			 FROM TijdelijkeRapportage
			 WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND
			 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY
TijdelijkeRapportage.type,
	" . $verdeling . "
ORDER BY
  volgorde,
	TijdelijkeRapportage." . $verdeling . "Volgorde ASC";
    }
    debugSpecial($query, __FILE__, __LINE__);
    
    $DB->SQL($query);
    $DB->Query();
    
    $kleurdata = array();
    $grafiekCategorien = array();
    $kleurLookup=array('beleggingscategorie'=>'OIB','beleggingssector'=>'OIS','valuta'=>'OIV','regio'=>'OIR');
    $regels=array();
    $percentagePerCategorie=array();
    $kleurenPerCategorie=array();
    $detailsPerCategorie=array();
    
    $n=0;
    
    $overigeCategorieOmschrijving = 'Overigen';
    while ($data = $DB->NextRecord())
    {
      if($verdeling == 'beleggingssector')
      {
        
        if($data['type']=='rekening')
        {
          $data['verdeling1']='A-Diversen';
          $data['verdeling1Omschrijving']=$overigeCategorieOmschrijving;
          
        }
      }
      
      $data['percentageVanTotaal'] = $data['subtotaalactueel'] / $totaalWaarde * 100;
      
      $percentagePerCategorie[$data['verdeling1Omschrijving']]+=$data['percentageVanTotaal'];
      
      $kleurenPerCategorie[$data['verdeling1Omschrijving']]= $this->allekleuren[$kleurLookup[$verdeling]][$data['verdeling1']];
      
      $regels[$n]=$data;
    }
    
    
    
    $percentagePerCategorieSorted=$percentagePerCategorie;
    asort($percentagePerCategorieSorted);
    $percentagePerCategorieSorted=array_reverse($percentagePerCategorieSorted,true);
    $overige=array();
    if(count($percentagePerCategorieSorted)> 8)
    {
      $n=0;
      foreach($percentagePerCategorieSorted as $categorie=>$percentage)
      {
        if($n>6 && $categorie <> $overigeCategorieOmschrijving)
        {
          $overige[$categorie]=$categorie;
        }
        if($categorie<>$overigeCategorieOmschrijving)
          $n++;
      }
    }
    
    
    foreach($percentagePerCategorie as $categorieOmschrijving=>$percentage)
    {
      if(in_array($categorieOmschrijving,$overige))
      {
        $percentagePerCategorie[$overigeCategorieOmschrijving] += $percentage;
        unset($percentagePerCategorie[$categorieOmschrijving]);
      }
    }
    
    foreach($percentagePerCategorie as $categorieOmschrijving=>$percentage)
    {
      
      if(!isset($kleurdata[$categorieOmschrijving]))
        $kleurdata[$categorieOmschrijving]=$kleurenPerCategorie[$categorieOmschrijving];
      $kleurdata[$categorieOmschrijving]['percentage'] += $percentage;
      $grafiekCategorien[$categorieOmschrijving] += $percentage;
  
      $detailsPerCategorie[$categorieOmschrijving]['waardeEur']+=$percentage*$totaalWaarde/100;
      $detailsPerCategorie[$categorieOmschrijving]['percentage']+=$percentage;
    }
    
    return array ('pieData'=>$grafiekCategorien,'kleurData'=>$kleurdata,'detailsPerCategorie'=>$detailsPerCategorie);
    
  }
  
  
  function writeRapport()
  {
    global $__appvar;

    $this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;
    
    //Kleuren instellen
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $this->allekleuren = unserialize($kleuren['grafiek_kleur']);
  
    $velden = array();
    $checkVelden = array('ToelichtingPag1','ToelichtingPag2');//'MarktInfo',
    $query = "desc CRM_naw";
    $DB->SQL($query);
    $DB->query();
    while ($data = $DB->nextRecord('num'))
    {
      $velden[] = $data[0];
    }
    $extraVeld = '';
    foreach ($checkVelden as $check)
    {
      if (in_array($check, $velden))
      {
        $extraVeld .= ',' . $check;
      }
    }
  
    $query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '" . $this->portefeuille . "' ";
    $DB->SQL($query);
    $this->crmData = $DB->lookupRecord();
    
    
    $verdelingen=array('beleggingscategorie'=>array());//,'valuta'=>array(),'beleggingssector'=>array(),'regio'=>array()
    
    $n=0;
    foreach($verdelingen as $categorie=>$data)
    {
      $verdeling=  $this->getVerdeling($categorie);
      $this->pdf->setXY(10+$n*74, 37+4);
      $this->printPie($verdeling,ucfirst($categorie) , 50, 50);
      $this->pdf->wLegend = 0;
      $n++;
    }
    
    $this->toonPerfGrafiek();
    $this->infoPagina('1');
    
    $this->pdf->addPage();
  
  
    $verdeling=  $this->getVerdeling('Liquiditeit6mnd');
  
  
    $query="SELECT omschrijving FROM FondsExtraVelden WHERE veldnaam='Liquiditeit6mnd'";
    $DB->SQL($query);
    $DB->Query();
    $omschrijving = $DB->nextRecord();
    $liquiditeit6mndOmschrijving=$omschrijving['omschrijving'];
    
    $this->pdf->setXY(10, 37+4);
    $this->printPie($verdeling,ucfirst($liquiditeit6mndOmschrijving) , 50, 50);
    $this->perfG(170-155,130,110,50,vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal));
    $this->infoPagina('2');

    
  }

function infoPagina($pagina='')
{
  if($this->crmData['ToelichtingPag'.$pagina]<>'')
  {
    $this->pdf->setXY($this->pdf->marge, 37);
    $this->pdf->setWidths(array(150, 130));
    $this->pdf->setAligns(array('L', 'L'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', 'Toelichting'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', $this->crmData['ToelichtingPag'.$pagina]));
  }
}
  
  
  function perfG($xPositie,$yPositie,$width,$height,$title='')
  {
    $w=$width;
    $this->pdf->setXY($xPositie,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,5,$title,'','C');
    $this->pdf->setXY($xPositie,$yPositie-5);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $this->pdf->Multicell($w,5,vertaalTekst('inclusief stortingen en onttrekkingen',$this->pdf->rapport_taal),'','C');
    
    $this->pdf->setXY($xPositie+10,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->Multicell($w,5,'X 1.000','','R');
    
    $this->pdf->setXY($xPositie,$yPositie);
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
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
      $start = $this->portefeuilleStartdatum;
    
    $eind = $this->rapportageDatum;
    
    $datumStart = db2jul($start);
    $datumStop  = db2jul($eind);
    
    $index = new indexHerberekening();
    $indexWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles));
    $aantalWaarden = count($indexWaarden);
    //echo $aantalWaarden;exit;
    $n=0;
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
    
    foreach ($indexWaarden as $id=>$data)
    {
      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
        $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
      else
        $koers=1;
      $grafiekData['portefeuille'][$n]=$data['waardeHuidige']/$koers;
      $grafiekData['storingen'][$n]+=($data['stortingen']-$data['onttrekkingen'])/$koers;
      $datumArray[$n]=$data['datum'];
      $maand=date('m',db2jul($data['datum']));
      if(in_array($maand,$maandFilter))
        $n++;
    }
    
    
    $minVal = -1;
    $maxVal = 1;
    
    
    foreach ($grafiekData as $type=>$maxData)
    {
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
    $vBar = ($lDiag / (count($grafiekData['portefeuille'])+ 1));
    $bGrafiek = $vBar * (count($grafiekData['portefeuille']) + 1);
    $eBaton = ($vBar * .5);
    
    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'FD','',array(245,245,245));
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
    foreach ($grafiekData['storingen'] as $i=>$waarde)
    {
      $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
      $yval = $yval2;
      $xval = $XDiag + (1 + $i ) * $unitw - ($eBaton / 2);
      $lval = $eBaton;
      $hval = ($waarde * $unit);
      $hval =$nulpunt-$yval;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,array(145,182,215)); //  //0,176,88
    }
    unset($yval);
    
    $lineStyle = array('width' => 0.75, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    foreach ($grafiekData['portefeuille'] as $i=>$waarde)
    {
      if(!isset($datumPrinted[$i]))
      {
        $datumPrinted[$i] = 1;
        //if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
        $julDatum=db2jul($datumArray[$i]);
        $this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);
      }
      if($waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
        if($yval)
        {
          $markerSize=0.5;
          $this->pdf->line($XDiag+$i*$unitw, $yval, $XDiag+($i+1)*$unitw, $yval2,$lineStyle );
          $this->pdf->Rect($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,array(0,176,88));
        }
        $yval = $yval2;
      }
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->CellBorders = array();
  }
  
  function toonPerfGrafiek()
  {
    
    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();
    
    
    if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
    {
      if($datum['month'] <10)
        $datum['month'] = "0".$datum['month'];
      $start = $datum['year'].'-'.$datum['month'].'-01';
    }
    else
      $start = substr($this->pdf->PortefeuilleStartdatum,0,10);
    $eind = $this->rapportageDatum;
    
    
    $index = new indexHerberekening();
    $indexData = $index->getWaarden($start,$eind,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex']);
  
    $extraIndices=array();
    $extraIndicesPerformance=array();
    $extraIndicesTmp=array();
    foreach($this->pdf->lastPOST as $key=>$value)
    {
      if(substr($key,0,8)=='mmIndex_')
      {
        $extraIndices[$value]=$value;
        $extraIndicesTmp[$value]=0;
      }
    }
    
    foreach ($indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
      //  $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
  
        $periode=$data['periode'];
        $datum=substr($periode,12,10);
        foreach($extraIndices as $fonds)
        {
          $perf=getFondsPerformance($fonds,substr($periode,0,10),$datum);
          $extraIndicesPerformance[$datum][$fonds]=((1+$extraIndicesTmp[$fonds]/100)*(1+$perf/100)-1)*100;
          $extraIndicesTmp[$fonds]=$extraIndicesPerformance[$datum][$fonds];
        }
        if(count($extraIndicesPerformance)>0)
          $grafiekData['extraIndices'][]=$extraIndicesPerformance[$datum];
        
      }
    }
    //listarray($grafiekData);
    if (count($grafiekData) > 1)
    {
      $yShift=-3;
      $this->pdf->SetXY(8,111+$yShift);//104
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.
                        vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                        vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
      $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+140,$this->pdf->GetY());
      $this->pdf->SetXY(15,117+$yShift)		;//112
      $valX = $this->pdf->GetX();
      $valY = $this->pdf->GetY();
      //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
      $kleuren=array(array(74,166,77),array(61,59,56));
      $kleurExtraIndices=$this->LineDiagram(130, 60, $grafiekData,$kleuren,0,0,6,5,1);//50
      $this->pdf->SetXY($valX, $valY + 75+$yShift);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($kleuren as $index=>$kleur)
      {
       
        if($index==0)
        {
          $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
          $this->pdf->Cell(50, 4, 'Portefeuille', 0, 0, "L");
        }
        //if($index==1)
        //  $this->pdf->Cell(50, 4, $this->pdf->portefeuilledata['SpecifiekeIndex'], 0, 0, "L");
      }
      foreach($kleurExtraIndices['extraIndicesLegenda'] as $fondsOmschrijving=>$kleur)
      {
        $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
        $this->pdf->Cell(45, 4, $fondsOmschrijving, 0, 0, "L");
      }
      
    }
  }
  
  
  function printPie($verdeling,$title='',$width=100,$height=100)
  {
    $pieData=$verdeling['pieData'];
    $kleurdata=$verdeling['kleurData'];
    $detailsPerCategorie=$verdeling['detailsPerCategorie'];
    
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
    
    foreach($pieData as $key=>$value)
      if ($value < 0)
        $pieData[$key] = -1 * $value;
    
    //$this->pdf->SetXY(210, $this->pdf->headerStart);
    $y = $this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($startX,$y-4);
    //	$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->Cell($width+5,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
    $this->pdf->setXY($startX,$y);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->setX($startX);
    $this->PieChart($width, $height, $pieData, $detailsPerCategorie, $grafiekKleuren);
    
    
    
    $this->pdf->setY($y);
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->setX($startX);
    
    //	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);
    
  }
  
  function PieChart($w, $h, $data, $legendaData, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->legends=array();
    $this->pdf->wLegend=0;
    $this->sum=array_sum($data);
    $this->NbVal=count($data);
    foreach($legendaData as $categorie=>$catData)
    {
      $this->legends[]=array($categorie,$this->formatGetal($catData['waardeEur'],0),$this->formatGetal($catData['percentage'],2).'%');
    }
  

    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 4;
    $hLegend = 2;
    $radius=min($w,$h);
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < count($this->legends); $i++) {
        $gray = $i * intval(255 / $this->NbVal);
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
      $angle = floor(($val * 360) / doubleval($this->sum));
      
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
  /*
    $this->pdf->Cell(30,$hLegend,'Categorie',0,0,'L');
    $this->pdf->Cell(20,$hLegend,'Waarde',0,0,'R');
    $this->pdf->Cell(15,$hLegend,'Percentage',0,0,'R');
    */
    $x1 = $XPage + $w + $margin*3 ;
    $x2 = $x1 + $hLegend + $margin;
    $y1 = $YDiag - ($radius) + $margin;
    
    for($i=0; $i<$this->NbVal; $i++) {
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1);
      //function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
      $this->pdf->Cell(30,$hLegend,$this->legends[$i][0],0,0,'L');
      $this->pdf->Cell(20,$hLegend,$this->legends[$i][1],0,0,'R');
      $this->pdf->Cell(15,$hLegend,$this->legends[$i][2],0,0,'R');
      $y1+=$hLegend + 2;
    }
    
  }

  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $legendDatum= $data['Datum'];
    $data1 = $data['benchmarkIndex'];
    $extraIndicesIn=$data['extraIndices'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
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
  
    $db=new DB();
    $extraIndicesKleur=array();
    $extraIndicesLegenda=array();
    $extraIndices=array();
    foreach($extraIndicesIn as $i=>$indices)
    {
       foreach($indices as $fonds=>$rendement)
       {
         if(!isset($extraIndicesKleur[$fonds]))
         {
           $query = "SELECT grafiekKleur,Omschrijving FROM BeleggingscategoriePerFonds
JOIN Fondsen ON BeleggingscategoriePerFonds.Fonds=Fondsen.Fonds
WHERE BeleggingscategoriePerFonds.Fonds='$fonds' AND Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
           $db->SQL($query);
           $kleurData = $db->lookupRecord();
           if($kleurData['Omschrijving']=='')
             $kleurData['Omschrijving']=$fonds;
           $tmp = unserialize($kleurData['grafiekKleur']);
           $extraIndicesKleur[$fonds] = array($tmp['R']['value'], $tmp['G']['value'], $tmp['B']['value']);
           if($kleurData['Omschrijving']<>'')
             $extraIndicesLegenda[$kleurData['Omschrijving']] = array($tmp['R']['value'], $tmp['G']['value'], $tmp['B']['value']);
         }
         $extraIndices[$fonds][$i] = $rendement;
         
         if ($rendement < $minVal)
         {
           $minVal = $rendement;
         }
         if ($rendement > $maxVal)
         {
           $maxVal = $rendement;
         }
       }
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
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
    // $color=array(200,0,0);
    
    $aantal=count($data);
    $legendaStep=ceil($aantal/12);
    
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      
      if($i%$legendaStep==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);
      
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      //  $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      // if($data[$i] <> 0)
      //   $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
      
      
      $yval = $yval2;
    }
    
    if(is_array($data1))
    {
      // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        //  $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        
        //    $this->pdf->Text($XDiag+($i+1)*$unit-1,$yval2-2.5,$this->formatGetal($data1[$i],1));
        
        $yval = $yval2;
      }
    }
  
    if(is_array($extraIndices))
    {
      // listarray($data1);
      foreach($extraIndices as $fonds=>$rendementsWaarden)
      {
        $color = $extraIndicesKleur[$fonds];
        $yval = $YDiag + (($maxVal) * $waardeCorrectie);
        $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
        for ($i = 0; $i < count($rendementsWaarden); $i++)
        {
          $yval2 = $YDiag + (($maxVal - $rendementsWaarden[$i]) * $waardeCorrectie);
          $this->pdf->line($XDiag + $i * $unit, $yval, $XDiag + ($i + 1) * $unit, $yval2, $lineStyle);
          //  $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
    
          //    $this->pdf->Text($XDiag+($i+1)*$unit-1,$yval2-2.5,$this->formatGetal($data1[$i],1));
    
          $yval = $yval2;
        }
      }
    }
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    return array('extraIndicesLegenda'=>$extraIndicesLegenda);
  }
  
}
?>