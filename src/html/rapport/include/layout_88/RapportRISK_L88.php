<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/28 15:46:18 $
File Versie					: $Revision: 1.2 $

$Log: RapportRISK_L88.php,v $
Revision 1.2  2020/03/28 15:46:18  rvv
*** empty log message ***

Revision 1.1  2020/03/21 12:35:10  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportRISK_L88
{

	function RapportRISK_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Rendement- en risicokenmerken";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatumVanafJul = db2jul($rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
    $this->filterJul=db2jul($this->pdf->PortefeuilleStartdatum);

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

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor(0);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();

    $DB = new DB();
    $query="SELECT SpecifiekeIndex,Omschrijving FROM Portefeuilles JOIN Fondsen ON Portefeuilles.SpecifiekeIndex=Fondsen.Fonds 
            WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $this->index=$DB->lookupRecord();
    
    $query="SELECT Minimum,Maximum,Risicoklasse FROM StandaarddeviatiePerRisicoklasse WHERE Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."'AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $this->standaarddeviatieMarge=$DB->lookupRecord();
  
    $grafiekData=array();

    $query="SELECT count(*) as aantal FROM HistorischePortefeuilleIndex WHERE portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $data=$DB->nextRecord();
    if($data['aantal'] > 0)
      $gebruikHistorischePortefeuilleIndex=1;//2;
    else
      $gebruikHistorischePortefeuilleIndex=0;

    $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,$gebruikHistorischePortefeuilleIndex);

/*
    if($this->pdf->portefeuilledata['Depotbank']=='TGB')
    {

      if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul('2017-01-01'))
        $this->filterJul=db2jul($this->pdf->PortefeuilleStartdatum);
      else
        $this->filterJul=db2jul('2017-01-01');
    }
*/
    // $stdev->setStartdatum($startDatum);
    //$reeksen=$stdev->standaardDeviatieReeksen['totaal'];
    
    $stdev->addReeks('totaal');
    $stdev->addReeks('benchmark',$this->index['SpecifiekeIndex']);
    $stdev->addReeks('afm');
    $stdev->berekenWaarden();

    $riskData=$stdev->riskAnalyze();
    $this->rechtsOnder($riskData);
    $this->addStdevGrafieken($stdev);
    $this->addPerfGrafiek($stdev);
    $this->linksOnder();

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();


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


function rechtsOnder($riskData)
{
  global $__appvar;
  $DB=new DB();
  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
    "FROM TijdelijkeRapportage WHERE ".
    " rapportageDatum ='".$this->rapportageDatum."' AND ".
    " portefeuille = '".$this->portefeuille."' "
    .$__appvar['TijdelijkeRapportageMaakUniek'];
  debugSpecial($query,__FILE__,__LINE__);
  $DB->SQL($query);
  $DB->Query();
  $totaalWaarde = $DB->nextRecord();

    $this->pdf->Ln(2);
    $this->pdf->setXY($this->pdf->marge,124);
    $this->pdf->SetWidths(array(180,65,20));
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array('','Standaarddeviatie portefeuille',$this->formatGetal($riskData['standaarddeviatie'],1).'%'));
    $this->pdf->ln(2);
   	$this->pdf->row(array('','Maximale standaarddeviatie volgens profiel',$this->formatGetal($this->standaarddeviatieMarge['Maximum'],1).'%'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%'));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%'));
    $this->pdf->ln(2);   
  	$this->pdf->row(array('','Value at Risk','€'.$this->formatGetal((100-$riskData['valueAtRisk'])/100*$totaalWaarde['totaal'],0).''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->ln(2);
  	$this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',''));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));  
  
}

function linksOnder()
{
    $this->pdf->ln();
    $this->pdf->setXY($this->pdf->marge,120);
    $this->pdf->SetWidths(array(10,160));
    $this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Toelichting'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','De "Standaarddeviatie" geeft de volatiliteit van de effectenportefeuille weer. Deze wordt gebruikt als een maatstaf voor de risicograad van de beleggingen.'));
    $this->pdf->row(array('','Bij de "AFM-standaarddeviatie" wordt geen berekening gemaakt van het werkelijke risico maar wordt gebruik gemaakt van lange termijn standaarden.'));
    $this->pdf->row(array('','De "Standaarddeviatie Benchmark" geeft de volatiliteit van de benchmark weer.'));
    $this->pdf->row(array('','De "Value at Risk" geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95% over een periode van 1 jaar.'));
    $this->pdf->row(array('','De "Maximum Drawdown" geeft de maximale daling weer vanaf de hoogste waarde van de effectenportefeuille vanaf de start.'));
    $this->pdf->row(array('','De "Tracking-error" geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'));
    $this->pdf->row(array('','De "Sharpe-ratio" is een meting van het voor risico gecorrigeerde rendement van de effectenportefeuille.'));
    $this->pdf->row(array('','De "Informatieratio" is een meting van het risico van de portefeuille gecorrigeerd voor het risico van de benchmark. Het deelt het actieve rendement door de tracking error.'));
}


function addPerfGrafiek($stdev)
{
    $portIndex=1;
    $indexIndex=1;
    $perfGrafiek=array();
    $perfGrafiek['portefeuille'][]=0;
    $perfGrafiek['specifiekeIndex'][]=0;
    $perfGrafiek['datum'][]= '';

    foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
    {
      $benchmarkData=$stdev->reeksen['benchmark'][$datum];
      $juldate=db2jul($datum);
      if($juldate >= $this->filterJul)
      {
        $portIndex = (1 + $perfData['perf'] / 100) * $portIndex;
        $indexIndex = (1 + $benchmarkData['perf'] / 100) * $indexIndex;
        $perfGrafiek['portefeuille'][] = ($portIndex - 1) * 100;
        $perfGrafiek['specifiekeIndex'][] = ($indexIndex - 1) * 100;
        $perfGrafiek['datum'][] = date("M y", $juldate);
      }
    }
    $benchmark=$this->getFondsOmschrijving($this->index['SpecifiekeIndex']);
    if($benchmark=='')
      $benchmark='benchmark';
    $perfGrafiek['legenda']=array('Portefeuille',$benchmark);

    $this->pdf->setXY(160,47);
    $portKleur=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $indexKleur=array(135,125,127);
    $perfGrafiek['titel']='Portefeuille rendement';
    $this->LineDiagram(120, 55, $perfGrafiek,array($portKleur,$indexKleur),0,0,6,5);//50


}

  function getFondsOmschrijving($fonds)
  {
    $db=new DB();
    $query="SELECT Omschrijving FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
    $db->sql($query);
    $omschrijving=$db->lookupRecord();
    return $omschrijving['Omschrijving'];
  }

function addStdevGrafieken($stdev)
{
  //$vanafJul=db2jul($this->pdf->PortefeuilleStartdatum);
    foreach($stdev->standaardDeviatieReeksen['totaal'] as $datum=>$devData)
    {
      $juldate=db2jul($datum);
      if(1|| $juldate >= $this->filterJul)
      {
        $benchmarkData = $stdev->standaardDeviatieReeksen['benchmark'][$datum];
//      $afmData=$stdev->standaardDeviatieReeksen['afm'][$datum];

        $grafiekData['totaal']['datum'][] = date("M y", db2jul($datum));
        $grafiekData['totaal']['portefeuille'][] = $devData['stdev'];
        $grafiekData['totaal']['specifiekeIndex'][] = $benchmarkData['stdev'];

//      $grafiekData['afm']['datum'][]= date("M y",db2jul($datum));
//      $grafiekData['afm']['portefeuille'][]= $afmData['stdev'];
      }
    }
    $grafiekData['totaal']['titel']='Standaarddeviatie';
//    $grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';
    $benchmark=$this->getFondsOmschrijving($this->index['SpecifiekeIndex']);



  if($benchmark=='')
      $benchmark='benchmark';
    $grafiekData['totaal']['legenda']=array('Portefeuille',$benchmark);

    $this->pdf->setXY(20,40);
    $this->pdf->Cell(100,4,"Bandbreedte ".$this->standaarddeviatieMarge['Risicoklasse'].": ".$this->standaarddeviatieMarge['Minimum']."% - ".$this->standaarddeviatieMarge['Maximum']."%");
    //$this->pdf->Row('Bandbreedte:',$this->standaarddeviatieMarge['Minimum']."% - ".$this->standaarddeviatieMarge['Maximum']."%");


    $this->pdf->setXY(20,47);
    
    $portKleur=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
   // $indexKleur=array(135,125,175);
    $indexKleur=array(135,125,127);
    $this->LineDiagram(120, 55, $grafiekData['totaal'],array($portKleur,$indexKleur),0,0,6,5,true);//50
  //  $this->pdf->setXY(160,47);
  //  $this->LineDiagram(120, 55, $grafiekData['afm'],array($portKleur,$indexKleur),0,0,6,5,false);//50
    


}  


  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4, $afmMinMax=false)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    

    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
      
    if($afmMinMax==true)
    {
      $bereikdata[]=$this->standaarddeviatieMarge['Minimum'];
      $bereikdata[]=$this->standaarddeviatieMarge['Maximum'];
    }
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
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

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $offset=0-$minVal;
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      
      $yGetal=$offset-($n*$stapgrootte)+$minVal;
      if($yGetal>=$minVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      }
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $yGetal=$offset-(-1*$n*$stapgrootte)+$minVal;
      if($yGetal<=$maxVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
          $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
      }
      $n++;
      if($n >20)
         break;
    }
    
    if($afmMinMax==true)
    {
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50,50,50));
      $yval = $YDiag + (($maxVal-$this->standaarddeviatieMarge['Minimum']) * $waardeCorrectie) ;
      $this->pdf->line($XDiag, $yval, $XPage+$w, $yval,$lineStyle );
      $this->pdf->Text($XDiag+$w, $yval,"Min.");
      
       $yval = $YDiag + (($maxVal-$this->standaarddeviatieMarge['Maximum']) * $waardeCorrectie) ;
      $this->pdf->line($XDiag, $yval, $XPage+$w, $yval,$lineStyle );
      $this->pdf->Text($XDiag+$w, $yval,"Max.");     
    }
    
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      
      if ($i>0)
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
        
        if ($i>0)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
    $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
    $this->pdf->Cell(0,3,$item);
    $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
 
  
}
?>