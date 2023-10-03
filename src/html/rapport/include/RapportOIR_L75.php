<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L75.php");

class RapportOIR_L75
{
  function RapportOIR_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIR";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Vermogensoverzicht";

    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->tweedePerformanceStart = $this->rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->pdf->pieData = array();
    $this->totaalWaarde=0;
  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }


  function getCRMnaam($portefeuille)
  {
    $db = new DB();
    $query="SELECT naam FROM CRM_naw WHERE portefeuille='$portefeuille'";
    $db->SQL($query);
    $crmData=$db->lookupRecord();
    $naamParts=explode('-',$crmData['naam'],2);
    $naam=trim($naamParts[1]);
    if($naam<>'')
      return $naam;
    else
      return $portefeuille;
  }


  function writeRapport()
  {
    global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;


    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind."  AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' ". $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    $this->totaalWaarde=$totaalWaarde;

    if (!is_array($this->pdf->grafiekKleuren))
    {
      $q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
      $DB->SQL($q);
      $DB->Query();
      $kleuren = $DB->LookupRecord();
      $kleuren = unserialize($kleuren['grafiek_kleur']);
      $this->pdf->grafiekKleuren = $kleuren;
    }


    $this->portefeuillePerf(30);
    $this->pdf->fillCell=array();
    $this->pdf->CellBorders = array();
  }

  function portefeuillePerf($ystart)
  {
    $totaalWaarde=array();
    $portefeuilleWaarden=array();
    $DB=new DB();
    $gebruikteNamen=array();
    $verdelingHoofdcategorie=array();
    $hoofdcategorieOmschrijving=array();

    if(count($this->pdf->portefeuilles)>0)
      $portefeuilles=$this->pdf->portefeuilles;
    else
      $portefeuilles=array($this->portefeuille);

    foreach($portefeuilles as $portefeuille)
    {
      if(substr($this->rapportageDatum,5,5)=='01-01')
        $startjaar=true;
      else
        $startjaar=false;

      $DB->SQL("SELECT kleurcode,ClientVermogensbeheerder FROM Portefeuilles WHERE portefeuille='".$portefeuille."'");
      $kleur=$DB->lookupRecord();

      if($kleur['ClientVermogensbeheerder']<>'' && !in_array($kleur['ClientVermogensbeheerder'],$gebruikteNamen))
      {
        $portefeuilleNaam = $kleur['ClientVermogensbeheerder'];
        $gebruikteNamen[]=$kleur['ClientVermogensbeheerder'];
      }
      else
        $portefeuilleNaam=$portefeuille;

      $tmp=unserialize($kleur['kleurcode']);
      $portefeuilleWaarden[$portefeuille]['kleur']=array('R'=>array('value'=>$tmp[0]),'G'=>array('value'=>$tmp[1]),'B'=>array('value'=>$tmp[2]));



      $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,$startjaar,$this->pdf->rapportageValuta,$this->tweedePerformanceStart);
      //vulTijdelijkeTabel($gegevens ,$portefeuille, $this->rapportageDatum);

      foreach($gegevens as $waarde)
      {
        $portefeuilleWaarden[$portefeuille]['eind']+=$waarde['actuelePortefeuilleWaardeEuro'];
        $totaalWaarde['eind']+=$waarde['actuelePortefeuilleWaardeEuro'];
        $verdelingHoofdcategorie[$waarde['beleggingscategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
        $hoofdcategorieOmschrijving[$waarde['beleggingscategorie']]=$waarde['beleggingscategorieOmschrijving'];


        if($waarde['hoofdcategorie']=='Liquide')
        {
          $portefeuilleWaarden[$portefeuille]['eindLiquide'] += $waarde['actuelePortefeuilleWaardeEuro'];
        }
        else
        {
          $portefeuilleWaarden[$portefeuille]['eindIlLiquide'] += $waarde['actuelePortefeuilleWaardeEuro'];
        }

      }

      if(substr($this->tweedePerformanceStart,5,5)=='01-01')
        $startjaar=true;
      else
        $startjaar=false;
      $gegevens=berekenPortefeuilleWaarde($portefeuille, $this->tweedePerformanceStart,$startjaar,$this->pdf->rapportageValuta,$this->tweedePerformanceStart);
      //vulTijdelijkeTabel($gegevens ,$portefeuille, $this->tweedePerformanceStart);
      foreach($gegevens as $waarde)
      {
        $portefeuilleWaarden[$portefeuille]['begin']+=$waarde['actuelePortefeuilleWaardeEuro'];
        $totaalWaarde['begin']+=$waarde['actuelePortefeuilleWaardeEuro'];
      }

      $rendementProcent  	= performanceMeting($portefeuille, $this->tweedePerformanceStart, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

      $stortingen 			 	= getStortingen($portefeuille,$this->tweedePerformanceStart,$this->rapportageDatum,$this->pdf->rapportageValuta);
      $onttrekkingen 		 	= getOnttrekkingen($portefeuille,$this->tweedePerformanceStart,$this->rapportageDatum,$this->pdf->rapportageValuta);
      $waardeMutatie=$portefeuilleWaarden[$portefeuille]['eind']-$portefeuilleWaarden[$portefeuille]['begin'];
      $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
      //echo " $portefeuille 	$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen; <br>\n"; ob_flush();

      $portefeuilleWaarden[$portefeuille]['rendement']=$rendementProcent;
      $portefeuilleWaarden[$portefeuille]['portefeuilleWaarde']=$portefeuilleWaarden[$portefeuille]['eind'];
      $portefeuilleWaarden[$portefeuille]['portefeuilleNaam']=$portefeuilleNaam;
      $portefeuilleWaarden[$portefeuille]['resultaat']=$resultaatVerslagperiode;

    }


    $this->pdf->setY($ystart);
    $witruimte=4;
    $this->pdf->SetAligns(array('L','R','C','C','C'));
    $this->pdf->CellBorders=array('','','L','L','L');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(80,54,54,54,54,54,40));
    $this->pdf->SetAligns(array('L','R','R','R','R','R'));
    unset($this->pdf->CellBorders);
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->row(array(
      vertaalTekst("Portefeuille",$this->pdf->rapport_taal),
      vertaalTekst("Vermogen",$this->pdf->rapport_taal),
      vertaalTekst('Liquide beleggingen',$this->pdf->rapport_taal),
      vertaalTekst('Illiquide beleggingen',$this->pdf->rapport_taal)
    ));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(7,73,54,54,54,54,54,40));
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R'));

    $max=1000;
    $barGraph=false;

    if(count($portefeuilleWaarden)>5)
    {
      $witruimte = $witruimte / 2;
      $krimp=-2;
    }
    $jaarTotalen=array();
    foreach($portefeuilleWaarden as $portefeuille=>$data)
    {
      if(round($data['eindLiquide'])<>0 || round($data['eindIlLiquide'],2)<>0 || round($data['resultaat'],2)<>0 || round($data['rendement'],2)<>0)
      {
        $this->pdf->ln($witruimte);
        $this->pdf->row(array(
          '',
          vertaalTekst($data['portefeuilleNaam'], $this->pdf->rapport_taal),
          $this->formatGetal($data['portefeuilleWaarde'], 2, false, $max),
          $this->formatGetal($data['eindLiquide'], 2, false, $max),
          $this->formatGetal($data['eindIlLiquide'], 2, false, $max)
        ));
      }
      else
      {
        continue;
      }
      if($data['rendement']<0)
        $barGraph=true;
      $aandeel = $data['eind'] / $totaalWaarde['eind'] * 100;
      $jaarTotalen['resultaat']+=$data['resultaat'];
      $jaarTotalen['aandeel']+=$aandeel;
      $jaarTotalen['portefeuilleWaarde']+=$data['portefeuilleWaarde'];
      $jaarTotalen['eindLiquide']+=$data['eindLiquide'];
      $jaarTotalen['eindIlLiquide']+=$data['eindIlLiquide'];

      $categorieVerdeling['percentage'][$data['portefeuilleNaam']]=$data['rendement'];
      $categorieVerdeling['aandeel'][$data['portefeuilleNaam']]=$aandeel;
      $categorieVerdeling['kleur'][]=array($data['kleur']['R']['value'],$data['kleur']['G']['value'],$data['kleur']['B']['value']);
      $categorieVerdeling['kleurBar'][$data['portefeuilleNaam']]=array($data['kleur']['R']['value'],$data['kleur']['G']['value'],$data['kleur']['B']['value']);

    }
    $this->pdf->SetWidths(array(80,54,54,54,54,54,40));
    $this->pdf->SetAligns(array('L','R','R','R','R','R'));
    $rendementProcent  	= performanceMeting($this->portefeuille, $this->tweedePerformanceStart, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','TS','TS','TS','TS','TS');
    $this->pdf->ln($witruimte);
    $this->pdf->row(array(
      vertaalTekst("Totaal",$this->pdf->rapport_taal),
      $this->formatGetal($jaarTotalen['portefeuilleWaarde'],2,false,$max),
      $this->formatGetal($jaarTotalen['eindLiquide'],2,false,$max),
      $this->formatGetal($jaarTotalen['eindIlLiquide'],2,false,$max)
    ));

    $categorieGrafiek=array();
    foreach($verdelingHoofdcategorie as $hoofdcategorie=>$waardeEur)
    {
      $omschrijving=$hoofdcategorieOmschrijving[$hoofdcategorie];
      $categorieGrafiek['aandeel'][$omschrijving]+=$waardeEur/$jaarTotalen['portefeuilleWaarde']*100;
      $categorieGrafiek['kleur'][]=array($this->pdf->grafiekKleuren['OIB'][$hoofdcategorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$hoofdcategorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$hoofdcategorie]['B']['value']);
      // $categorieGrafiek['kleur'][$omschrijving]=$this->pdf->grafiekKleuren['OIB'][$hoofdcategorie];
    }

    //
    $xOffset=0;
    $grafiekY=130;
    $this->pdf->setXY($xOffset,$grafiekY-5);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell(297/2,4,vertaalTekst('Verdeling over portefeuilles',$this->pdf->rapport_taal),'','C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setXY(20+$xOffset,$grafiekY-5);
    PieChart_L75($this->pdf,50, 50, $categorieVerdeling['aandeel'], '%l (%p)',$categorieVerdeling['kleur'],'',array(20+$xOffset+65+5,$grafiekY+10));


    $xOffset=140;

    $this->pdf->setXY($xOffset,$grafiekY-5);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell(297/2,4,vertaalTekst('Verdeling over categorieën',$this->pdf->rapport_taal),'','C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setXY(20+$xOffset,$grafiekY-5);
    if(min($categorieGrafiek['aandeel'])<0)
      $this->BarDiagram(70, 50, $categorieGrafiek['aandeel'], '%l (%p)',$categorieGrafiek['kleur'],'',0);//array(20+$xOffset+60,$grafiekY+10));
    else
      PieChart_L75($this->pdf,50, 50, $categorieGrafiek['aandeel'], '%l (%p)',$categorieGrafiek['kleur'],'',array(20+$xOffset+60,$grafiekY+10));

    //listarray($portefeuilleWaarden);

  }





  function SetLegends2($data, $format)
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
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
    }
  }

  function BarDiagram($w, $h, $data, $format,$colorArray,$titel,$krimp=0)
  {
    $pdfObject = &$object;
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->SetLegends2($data,$format);


    $XPage = $this->pdf->GetX()+30;
    $YPage = $this->pdf->GetY()+7;
    $margin = 0;
    $nbDiv=5;
    $legendWidth=10;
    $YDiag = $YPage;
    $hDiag = floor($h);
    $XDiag = $XPage +  $legendWidth;
    $lDiag = floor($w - $legendWidth);

    $color=array(155,155,155);
    $maxVal=0;
    $minVal=0;
    if ($maxVal == 0) {
      $maxVal = max($data)*1.1;
    }
    if ($minVal == 0) {
      $minVal = min($data)*1.1;
    }
    if($minVal > 0)
      $minVal=0;

    $minVal=floor($minVal*10)/10;
    $maxVal=ceil($maxVal*10)/10;

    $offset=$minVal;
    $maxMin=ceil(($maxVal-$minVal)*10)/10;
    //$maxMin=$maxMin*1.3;
    $valIndRepere = round($maxMin / $nbDiv*10,1)/10;



    //echo "$minVal $maxVal  $maxMin <br>\n";exit;
    //	echo ($maxMin/$nbDiv)." $valIndRepere <br>\n";
    for($i=0;$i<100;$i++)
    {
      if (abs($maxMin / $nbDiv) > abs($minVal))
      {
        $minVal = floor(abs($maxMin / $nbDiv) * -1 * 100) / 100;
        $maxMin = ceil(($maxVal - $minVal) * 10) / 10;
        $valIndRepere = round($maxMin / $nbDiv * 10, 0) / 10;
        $offset = $minVal;
      }
      else
      {
        break;
      }
      //echo "$i <br>\n";
    }
    //	echo "$minVal $maxVal  $maxMin <br>\n";
    //	echo ($maxMin/$nbDiv)." $valIndRepere<br>\n";
    //	exit;

    $bandBreedte = $valIndRepere * $nbDiv;
    //echo $bandBreedte;exit;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
    $hDiag = $hBar * ($this->pdf->NbVal + 1);

    //echo "$hBar <br>\n";
    $eBaton = floor($hBar * 80 / 100);


    $legendaStep=$unit/$nbDiv*$bandBreedte;
    //echo "	$legendaStep=$unit/$nbDiv*$bandBreedte;";exit;

    //$valIndRepere=round($valIndRepere/$unit/5)*5;


    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    $nullijn=$XDiag - ($offset * $unit);

    $this->pdf->Line($nullijn, $YDiag, $nullijn, $YDiag + $hDiag,array('dash' => 0));
    $this->pdf->setXY($nullijn,$YDiag + $hDiag);
    $this->pdf->Cell(0.1, 5,"0",0,0,'C');

    $i=0;
    $nbDiv=10;

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    if(round($legendaStep,5) <> 0.0)
    {
      //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
      for($x=$nullijn;$x>=$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag,array('dash' => 1));
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
        $i++;
        if($i>100)
          break;
      }

      $i=0;
      //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
      for($x=$nullijn;$x<=($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag,array('dash' => 1));
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');

        $i++;
        if($i>100)
          break;
      }
    }

    $i=0;

    $this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->Cell($lDiag, -5, $titel,0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//listarray($colorArray);listarray($data);
    $this->pdf->setDash();
    foreach($data as $key=>$val)
    {
      $this->pdf->SetFillColor($colorArray[$i][0],$colorArray[$i][1],$colorArray[$i][2]);
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2 ;

      //$this->pdf->Line($XDiag, $yval+$eBaton/2, $XPage+$w, $yval+$eBaton/2,array('dash' => 3));


      $hval = $eBaton;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
      $this->pdf->SetXY($XPage, $yval);
      $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
      $i++;
    }

    //Scales
    $minPos=($minVal * $unit);
    $maxPos=($maxVal * $unit);

    $unit=($maxPos-$minPos)/$nbDiv;
    // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


  }


}
?>
