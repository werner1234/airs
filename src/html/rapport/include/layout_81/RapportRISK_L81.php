<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/27 15:11:17 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportRISK_L81.php,v $
 		Revision 1.1  2018/12/27 15:11:17  rvv
 		*** empty log message ***
 		


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L35.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportRISK_L81
{
	function RapportRISK_L81($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Risico verdeling";

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
    $this->pdf->AddPage();

    $stdev = new rapportSDberekening($this->portefeuille, $this->rapportageDatum);
    $stdev->addReeks('Beleggingscategorien');
    $stdev->addReeks('benchmark',$this->pdf->portefeuilledata['SpecifiekeIndex']);

    $categorien = array();
    $datumVelden = array();
//listarray($stdev->reeksen);exit;
    foreach ($stdev->reeksen as $reeks => $reeksdata)
    {
      if ($reeks == 'VAR' || $reeks == 'ZAK' || $reeks == 'ALTER' || $reeks == 'totaal')
      {
        $categorien[] = $reeks;
      }
    }
    /*
    foreach ($stdev->reeksen as $reeks => $waarden)
    {
      $nieuweReeks = array();
      $startDatum = substr($stdev->settings['Startdatum'], 0, 10);
      if ($reeks == 'VAR' || $reeks == 'ZAK' || $reeks == 'ALTER' || $reeks == 'totaal')
      {
        foreach ($waarden as $datum => $perfWaarden)
        {
          $tmp = $this->indexPerformance($reeks, $startDatum, $datum);
          $nieuweReeks[$datum]['perf'] = $tmp['perf'] * 100;
          $nieuweReeks[$datum]['datum'] = $datum;
          $startDatum = $datum;
          $datumVelden[$datum] = $datum;
        }
        $stdev->reeksen[$reeks . 'Index'] = $nieuweReeks;
      }
    }
    */
  
    $indexCounter = 0;
    $this->pdf->excelData[$indexCounter][] = 'Datum';
    foreach ($categorien as $categorie)
    {
      $this->pdf->excelData[$indexCounter][] = 'perf ' . $categorie;
      $this->pdf->excelData[$indexCounter][] = 'index ' . $categorie;
    }
    $indexCounter++;
    foreach ($datumVelden as $datum)
    {
      $this->pdf->excelData[$indexCounter][0] = $datum;
      foreach ($categorien as $categorie)
      {
        $this->pdf->excelData[$indexCounter][] = (100 + $stdev->reeksen[$categorie][$datum]['perf']);
        $this->pdf->excelData[$indexCounter][] = (100 + $stdev->reeksen[$categorie . 'Index'][$datum]['perf']);
      }
      $indexCounter++;
    }
  
    $stdev->addReeks('afm');
    $stdev->berekenWaarden(true);
  
    foreach ($stdev->standaardDeviatieReeksen as $reeks => $reeksData)
    {
      if(!isset($rendement[$reeks]))
        $rendement[$reeks]=0;
      foreach ($reeksData as $datum => $meting)
      {
        if ($reeks == 'VAR' || $reeks == 'ZAK' || $reeks == 'ALTER' || $reeks == 'totaal')
        {
          $tmp = array('laatsteMeting' => $datum, 'stdev' => $meting['stdev']);
         // $tmp['stdevIndex'] = $stdev->standaardDeviatieReeksen[$reeks . 'Index'][$datum]['stdev'];
          $rendement[$reeks]=((1+$rendement[$reeks]/100)*(1+ $stdev->reeksen[$reeks][$datum]['perf']/100)-1)*100;
          //echo $rendement[$reeks]."<br>\n";
          //$tmp['stdevIndex'] = $stdev->reeksen[$reeks][$datum]['perf'];
          $tmp['stdevIndex'] =$rendement[$reeks];
          $standaardDeviatieReeksen[$reeks][] = $tmp;
        }
        if ($reeks == 'afm')
        {
          $standaardDeviatieReeksen[$reeks][] = array('laatsteMeting' => $datum, 'stdevIndex' => $meting['stdev']);
        }
      }
    }

//procent
$grafiekData=array();
$counterBegin=count($this->pdf->excelData);
$catId=-1;
$xlsHeader=array('datum');
$skipIndex=array('afm','totaalInformatieratio','totaalTrackingError');
foreach($standaardDeviatieReeksen as $cat=>$reeksData)
{
  array_push($xlsHeader,'perf '.$cat);
  array_push($xlsHeader,'index '.$cat);
  $catId=$catId+2;
  $counter=$counterBegin;
  foreach($reeksData as $waarden)
  {
    
    $grafiekData[$cat]['portefeuille'][]=$waarden['stdevIndex'];
    if(!in_array($cat,$skipIndex))
    {
      $grafiekData[$cat]['specifiekeIndex'][] = $waarden['stdev'];
    }
    $grafiekData[$cat]['datum'][]=date('Y-M',db2jul($waarden['laatsteMeting']));

    $this->pdf->excelData[$counter+1][0] = $waarden['laatsteMeting'];  
    $this->pdf->excelData[$counter+1][$catId] = $waarden['stdev'];     
    $this->pdf->excelData[$counter+1][$catId+1] = $waarden['stdevIndex'];  
    

    $counter++;
  }
}
$this->pdf->excelData[$counterBegin]=$xlsHeader;
    $riskData=$stdev->riskAnalyze('totaal');

$grafiekData['ZAK']['titel']='Verloop zakelijke waarden';
$grafiekData['VAR']['titel']='Verloop vastrentende waarden';
$grafiekData['ALTER']['titel']='Verloop alternatieve waarden';

$grafiekData['totaal']['titel']='Verloop portefeuille';
$grafiekData['afm']['titel']='Verloop AFM-standaarddeviatie wekelijks gemeten';
$grafiekData['totaalTrackingError']['titel']='Tracking error';
$grafiekData['totaalInformatieratio']['titel']='Informatieratio';

$grafiekData['ZAK']['legenda']=array('rendement','standaarddeviatie');
$grafiekData['VAR']['legenda']=array('rendement','standaarddeviatie');
$grafiekData['ALTER']['legenda']=array('rendement','standaarddeviatie');
$grafiekData['totaal']['legenda']=array('rendement','standaarddeviatie');
$grafiekData['afm']['legenda']=array('portefeuille');

    $kopKleur=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $benchKleur=array(100,100,100);

  $this->pdf->setXY(20,45);
    $this->LineDiagram(80, 55, $grafiekData['totaal'],array($kopKleur,$benchKleur),0,0,6,5,1);//50
  $this->pdf->setXY(110,45);
    $this->LineDiagram(80, 55, $grafiekData['afm'],array($kopKleur,$benchKleur),0,0,6,5,1);//50


  $this->pdf->setXY(20,120);
    $this->LineDiagram(80, 55, $grafiekData['ZAK'],array($kopKleur,$benchKleur),0,0,6,5,1);//50
  $this->pdf->setXY(110,120);
    $this->LineDiagram(80, 55, $grafiekData['VAR'],array($kopKleur,$benchKleur),0,0,6,5,1);//50
  $this->pdf->setXY(200,120);
    $this->LineDiagram(80, 55, $grafiekData['ALTER'],array($kopKleur,$benchKleur),0,0,6,5,1);//50

    $this->rechtsBoven($riskData);
	}



  function rechtsBoven($riskData)
  {

    $this->pdf->Ln(2);
    $this->pdf->setXY($this->pdf->marge,50);
    $this->pdf->SetWidths(array(195,55,20));
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%',$body));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Value at Risk',$this->formatGetal($riskData['valueAtRisk'],1).'%',''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
    $this->pdf->ln(2);
    $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',''));
    $this->pdf->ln(2);
    $this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));

  }

  function indexPerformance($categorie,$van,$tot)
	{
	  global $__appvar;
    $DB = new DB();
	  if(!is_array($this->indexLookup) || count($this->indexLookup) < 1)
	  {
	    $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie 
      WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
            AND (IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($index=$DB->nextRecord())
        $this->indexLookup[$index['Beleggingscategorie']]=$index['Fonds'];
      $this->indexLookup['totaal']=$this->pdf->portefeuilledata['SpecifiekeIndex'];
    }

     if(!is_array($this->normData) || count($this->normData) < 1)
     {
       $this->normData['totaal']=100;
       $q="SELECT ZorgplichtPerBeleggingscategorie.Beleggingscategorie,ZorgplichtPerRisicoklasse.norm,ZorgplichtPerRisicoklasse.Zorgplicht,CategorienPerHoofdcategorie.Hoofdcategorie
       FROM
       ZorgplichtPerRisicoklasse
       Inner Join ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoklasse.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
       Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
       WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
       ORDER by CategorienPerHoofdcategorie.Hoofdcategorie";
		   $DB->SQL($q);
		   $DB->Query();
		   while($data=$DB->nextRecord())
		     $this->normData[$data['Hoofdcategorie']]=$data['norm'];

      $q="SELECT
      ZorgplichtPerBeleggingscategorie.Beleggingscategorie,
      CategorienPerHoofdcategorie.Hoofdcategorie,
      ZorgplichtPerPortefeuille.Zorgplicht,
      ZorgplichtPerPortefeuille.norm
      FROM ZorgplichtPerPortefeuille
      JOIN ZorgplichtPerBeleggingscategorie  ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      ORDER by CategorienPerHoofdcategorie.Hoofdcategorie
      ";
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		  $this->normData[$data['Hoofdcategorie']]=$data['norm'];
     }


	  $fonds=$this->indexLookup[$categorie];
   
    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
      $verdeling[$data['fonds']]=$data['percentage'];

    if(count($verdeling)==0)
      $verdeling[$fonds]=100;
    
    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $janKoers=$DB->lookupRecord();
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	    $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      
      $perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perf=$perfJaar-$perfVoorPeriode;
      //$perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
      $totalPerf+=($perf*$percentage/100);
    }

    $perf= $totalPerf;
    
    if($_POST['debug']==1)
      echo "$categorie | $fonds | $van | $tot | $perf<br>\n";
    
    
    /*
    
    echo "$fonds <br>\n";
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
  	$DB->SQL($query);
    $startKoers=$DB->lookupRecord();
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($query);
    $eindKoers=$DB->lookupRecord();
    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
    
    */
    $waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);
    
    
    $tmp= array('perf'=>$perf,
                'bijdrage'=>$perf*$fondsData['Percentage'],
                'datum'=>$tot,
                'percentage'=>($this->normData[$categorie]/100),//$fondsData['Percentage']
                'categorie'=>$categorie,
                'koersVan'=>$startKoers['Koers'],
                'koersEind'=>$eindKoers['Koers']);

    return $tmp;
  }
  
  
function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
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

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'C');

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
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      if($i > $YPage)
      {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      }
      /*
      $yGetal=$offset-($n*$stapgrootte)+$minVal;
      if($yGetal>=$minVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      }
*/

      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      /*
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
*/
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
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)*2/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+9,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      
      if ($i>0)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
//        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      }
//      if ($i==count($data)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        

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
//          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        }
//        if ($i==count($data1)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

         $yval = $yval2;
      }
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));


  //   $XPage
   // $YPage


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