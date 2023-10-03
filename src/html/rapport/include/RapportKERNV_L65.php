<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/03/31 12:19:56 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportKERNV_L65.php,v $
 		Revision 1.3  2019/03/31 12:19:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/02/23 18:32:59  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/07/22 08:18:21  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/07/21 15:54:40  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/07/18 15:46:07  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/07/14 14:04:37  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/07/07 17:35:19  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/07/01 13:47:10  rvv
 		*** empty log message ***

 		Revision 1.8  2018/06/30 17:43:55  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/06/27 16:13:50  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/06/20 16:40:16  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/06/17 07:31:10  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/06/16 17:42:56  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/09/23 17:42:26  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportKERNV_L65
{
  function RapportKERNV_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "KERNV";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = 'Risico karakteristieken';

    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

  function getBenchmarkverdeling()
  {
    //
    $DB=new DB();

    $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."' ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    $zorgplichtcategorien=array();
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }

    $query="SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Vermogensbeheerder,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum,
ZorgplichtPerPortefeuille.norm
FROM
ZorgplichtPerPortefeuille
WHERE ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."'
 ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }

    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      $query="SELECT IndexPerBeleggingscategorie.Fonds,Fondsen.Omschrijving FROM IndexPerBeleggingscategorie 
      JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
      WHERE Categoriesoort='Zorgplichtcategorien' AND Categorie='$zorgplicht' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $DB->SQL($query);
      $DB->Query();
      $data = $DB->nextRecord();
      $zorgplichtcategorien[$zorgplicht]['fonds']=$data['Fonds'];
      $zorgplichtcategorien[$zorgplicht]['fondsOmschrijving']=$data['Omschrijving'];
    }

    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      $query="SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving 
      FROM benchmarkverdeling 
      JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmark='".$zorgplichtData['fonds']."'";
      $DB->SQL($query);
      $DB->Query();
      while($data = $DB->nextRecord())
        $zorgplichtcategorien[$zorgplicht]['fondsSamenselling'][$data['fonds']]=$data;
    }
    
    $totalen=array();
    $omschrijving=array();
    foreach($zorgplichtcategorien as $categorie=>$categorieData)
    {
      if(is_array($categorieData['fondsSamenselling']))
      {
        foreach($categorieData['fondsSamenselling'] as $fonds=>$fondsDetail)
        {
          $totalen[$fonds]+=($fondsDetail['percentage']/100)*($categorieData['norm']);
          $omschrijving[$fonds]=$fondsDetail['Omschrijving'];
        }
      }
      else
      {
        if($categorieData['fonds']<>'')
        {
          $totalen[$categorieData['fonds']] += ($categorieData['norm']);
          $omschrijving[$categorieData['fonds']]=$categorieData['fondsOmschrijving'];
        }
      }
    }

    if(array_sum($totalen)==100)
      return array('fondsen'=>$totalen,'omschrijving'=>$omschrijving);
    else
      return false;

    //
  }


  function writeRapport()
  {
    global $__appvar;


    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    unset($this->pdf->fillCell);


    $verdeling=$this->getBenchmarkverdeling();

//listarray($verdeling);
      $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
      $stdev->addReeks('totaal');
      if(is_array($verdeling))
      {
        $stdev->addReeks('benchmark', $verdeling['fondsen']);
        if($_POST['debug']==1)
        {
          $this->pdf->setXY($this->pdf->marge, 140);
          $this->pdf->SetWidths(array(165, 70, 20));
          $this->pdf->SetAligns(array('L', 'L', 'R'));
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->Row(array('', 'Benchmark opbouw'));
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          foreach ($verdeling['fondsen'] as $fonds => $percentage)
          {
            $this->pdf->Row(array('', $verdeling['omschrijving'][$fonds], $this->formatGetal($percentage, 1) . '%'));
          }
        }
      }

 //     $stdev->addReeks('benchmark',$this->pdf->portefeuilledata['SpecifiekeIndex']);
    $stdev->addReeks('afm');
    $stdev->berekenWaarden(true);

    $grafiekData=array();
    $maanden=array('','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    foreach($maanden as $key=>$value)
      $maanden[$key]=vertaalTekst($value,$this->pdf->rapport_taal);
    foreach($stdev->standaardDeviatieReeksen as $reeks=>$reeksData)
    {
        foreach($reeksData as $datum=>$meting)
        {
          if($reeks=='totaal')
          {
            $grafiekData[$reeks]['portefeuille'][] = $meting['stdev'];
            $grafiekData[$reeks]['specifiekeIndex'][] = $stdev->standaardDeviatieReeksen['benchmark'][$datum]['stdev'];
            $julDatum=db2jul($datum);
            $grafiekData[$reeks]['datum'][] = $maanden[date('n',$julDatum)].'-'.date('y',$julDatum);
          }
          elseif($reeks=='afm')
          {
            if(isset($stdev->standaardDeviatieReeksen['totaal'][$meting['datum']]))
              $grafiekData['totaal']['afm'][] = $meting['stdev'];
          }
        }
    }



    if(count($grafiekData['totaal']['datum'])==0)
      return;

//procent

    $counterBegin=count($this->pdf->excelData);
    $xlsHeader=array('datum');
    /*
    $skipIndex=array('afm','totaalInformatieratio','totaalTrackingError');
    foreach($standaardDeviatieReeksen as $cat=>$reeksData)
    {
      foreach($reeksData as $index=>$waarden)
      {



      }
    }
    */
    $this->pdf->excelData[$counterBegin]=$xlsHeader;

    $grafiekData['totaal']['titel']='';//'Verloop standaarddeviatie';
    $grafiekData['totaal']['legenda']=array('portefeuille','benchmark','afm');
    foreach($grafiekData['totaal']['legenda'] as $key=>$value)
      $grafiekData['totaal']['legenda'][$key]=vertaalTekst($value,$this->pdf->rapport_taal);


    $kleuren=array(array(151,203,227),array(203,178,203),array(31,161,46));

    $this->pdf->setXY(17,45);
    $this->LineDiagram(120, 55, $grafiekData['totaal'],$kleuren,0,0,6,5,1);//50

    $stdev->uitvoer=$stdev->getLast($stdev->standaardDeviatieReeksen);
    $riskData=$stdev->riskAnalyze('totaal');

    $this->rechtsBoven($riskData);

    //$text="De VAR (Value At Risk) geeft het maximale verlies van de portefeuille weer op jaarbasis met 95% zekerheid op basis van Monte Carlo simulatie. De standaarddeviatie is een statistische maat voor de spreiding van de getallen rondom het gemiddelde.";
    $text="De VAR (Value At Risk) geeft het maximale verlies van de portefeuille weer op jaarbasis met 95% zekerheid.\nDe standaarddeviatie is een statistische maat voor de spreiding van de getallen rondom het gemiddelde.";
    $this->pdf->setXY($this->pdf->marge,140);
    $this->pdf->SetWidths(array(297-2*$this->pdf->marge));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst($text ,$this->pdf->rapport_taal)));

}



  function rechtsBoven($riskData)
  {

    $this->pdf->Ln(2);
    $this->pdf->setXY($this->pdf->marge,45);
    $this->pdf->SetWidths(array(175,55,20));
    $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal),$this->formatGetal($riskData['standaarddeviatie'],1).'%'));
    $this->pdf->row(array('',vertaalTekst('AFM-Standaarddeviatie',$this->pdf->rapport_taal),$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%'));
    $this->pdf->row(array('',vertaalTekst('Standaarddeviatie benchmark',$this->pdf->rapport_taal),$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%'));
    $this->pdf->row(array('',vertaalTekst('Value at Risk',$this->pdf->rapport_taal),$this->formatGetal(100-$riskData['valueAtRisk'],1).'%',''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
   // $this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
   // $this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
   // $this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',''));
   // $this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));

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
    $data2 = $data['afm'];
    $data = $data['portefeuille'];

    if(count($data2)>0)
      $bereikdata = array_merge($data,$data1,$data2);
    elseif(count($data1)>0)
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

    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setXY($XPage,$YPage-3);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,4,$titel,0,1,'C');
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    //$this->pdf->line($XPage,$YPage+$this->pdf->rowHeight-6,$XPage+$w,$YPage+$this->pdf->rowHeight-6);


    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color2= $color[2];
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
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
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

    if(is_array($data2))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);

      for ($i=0; $i<count($data2); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data2[$i]) * $waardeCorrectie) ;

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
      elseif($index==1)
        $kleur=$color1;
      else
        $kleur=$color2;
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
      $this->pdf->Cell(0,3,$item);
      $step+=($w/3);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
}
?>
