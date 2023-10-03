<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportRISK_L87
{

	function RapportRISK_L87($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		//$this->pdf->rapport_titel = "Performancemeting";
		$this->pdf->rapport_titel = "Rendements- en risicomaatstaven";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
		$this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

		$this->getKleuren();

		$DB = new DB();
		$query="SELECT SpecifiekeIndex,Omschrijving FROM Portefeuilles JOIN Fondsen ON Portefeuilles.SpecifiekeIndex=Fondsen.Fonds 
            WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
		$DB->SQL($query);
		$this->index=$DB->lookupRecord();

		$grafiekData=array();
		$stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
		$stdev->addReeks('totaal');
		$stdev->addReeks('benchmark',$this->index['SpecifiekeIndex']);
		$stdev->addReeks('afm');
		$stdev->berekenWaarden();

		$riskData=$stdev->riskAnalyze();

		$riskBenchmark=$stdev->riskAnalyze('benchmark','totaal',false);
		$this->linksOnder($riskData,$riskBenchmark);
		$this->addStdevGrafieken($stdev);
		$this->addPerfGrafiek($stdev);
    $this->rechtsOnder();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array();

		if($_POST['extra']=='xls')
		{
			$this->pdf->excelData[] = array('performance');
			foreach ($stdev->reeksen as $reeks => $reeksData)
			{
				$this->pdf->excelData[] = array($reeks);
				foreach ($reeksData as $datum => $data)
				{
					$this->pdf->excelData[] = array($datum, $data['perf']);
				}
			}
			$this->pdf->excelData[] = array('standaardDeviatieReeksen');
			foreach ($stdev->standaardDeviatieReeksen as $reeks => $reeksData)
			{
				$this->pdf->excelData[] = array($reeks);
				foreach ($reeksData as $datum => $data)
				{
					$this->pdf->excelData[] = array($datum, $data['stdev']);
				}
			}
		}


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

	function linksOnder($riskData,$riskBenchmark)
	{
		global $__appvar;
		$DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum ."' AND ".
			" portefeuille = '". $this->portefeuille ."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];


		$this->pdf->Ln(2);
		$this->pdf->setXY($this->pdf->marge,120);
		$this->pdf->SetWidths(array(45,20,20));
		$this->pdf->SetAligns(array('L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Portefeuille','Benchmark'));
		$this->pdf->ln(2);
		$this->pdf->row(array('Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%'));
		$this->pdf->ln(2);
		$this->pdf->row(array('AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%'));
		$this->pdf->ln(2);
		$riskData['valueAtRisk']=(100-$riskData['valueAtRisk'])/100*$totaalWaarde;
		$riskBenchmark['valueAtRisk']=(100-$riskBenchmark['valueAtRisk'])/100*$totaalWaarde;
		$this->pdf->row(array('Value at Risk','� '.$this->formatGetal($riskData['valueAtRisk'],0),'� '.$this->formatGetal($riskBenchmark['valueAtRisk'],0)));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
		$this->pdf->ln(2);
		$this->pdf->row(array('Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',$this->formatGetal($riskBenchmark['maxDrawdown'],1).'%'));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
		$this->pdf->ln(2);
		$this->pdf->row(array('Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
		$this->pdf->ln(2);
		$this->pdf->row(array('Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',$this->formatGetal($riskBenchmark['sharpeRatio'],1)));
	}


	function addPerfGrafiek($stdev)
	{
		$portIndex=1;
		$indexIndex=1;
		foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
		{
			if(db2jul($datum) >= 1)//$this->pdf->rapport_datumvanaf)
			{
				$benchmarkData = $stdev->reeksen['benchmark'][$datum];
				$juldate = db2jul($datum);
				$portIndex = (1 + $perfData['perf'] / 100) * $portIndex;
				$indexIndex = (1 + $benchmarkData['perf'] / 100) * $indexIndex;
				$perfGrafiek['portefeuille'][] = ($portIndex - 1) * 100;
				$perfGrafiek['specifiekeIndex'][] = ($indexIndex - 1) * 100;
				$perfGrafiek['datum'][] = date("d-m-Y", $juldate);
				//echo "$datum ".(($portIndex-1)*100)."<br>\n";
				//echo "$datum gebruikt. <br>\n";
			}
		//	else
				//echo "$datum overgeslagen. <br>\n";
		}

		$perfGrafiek['legenda']=array('Portefeuille',$this->index['Omschrijving']);

		$this->pdf->setXY(20,37);
		$portKleur=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $indexKleur=$this->pdf->rapport_grafiek2;
		$perfGrafiek['titel']='Portefeuille rendement';
		$this->LineDiagram(120, 55, $perfGrafiek,array($portKleur,$indexKleur),0,0,6,5,true);//50


	}

	function addStdevGrafieken($stdev)
	{
		foreach($stdev->standaardDeviatieReeksen['totaal'] as $datum=>$devData)
		{
			$benchmarkData=$stdev->standaardDeviatieReeksen['benchmark'][$datum];
			$afmData=$stdev->standaardDeviatieReeksen['afm'][$datum];

			$grafiekData['totaal']['datum'][]= date("d-m-Y",db2jul($datum));
			$grafiekData['totaal']['portefeuille'][]= $devData['stdev'];
			$grafiekData['totaal']['specifiekeIndex'][]= $benchmarkData['stdev'];
			$grafiekData['totaal']['afm'][]= $afmData['stdev'];

			$grafiekData['afm']['datum'][]= date("d-m-Y",db2jul($datum));
			$grafiekData['afm']['portefeuille'][]= $afmData['stdev'];
		}
		$grafiekData['totaal']['titel']='Standaarddeviatie';
		$grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';

		$grafiekData['totaal']['legenda']=array('Portefeuille',$this->index['Omschrijving'],'AFM');

		$this->pdf->setXY(160,37);

		$portKleur=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$afmkleur=array(122,153,172);
		$indexKleur=$this->pdf->rapport_grafiek2;
		
		$this->LineDiagram(120, 55, $grafiekData['totaal'],array($portKleur,$indexKleur,$afmkleur),0,0,6,5,false);//50
		//$this->pdf->setXY(160,35);
		//$this->LineDiagram(120, 55, $grafiekData['afm'],array($portKleur,$indexKleur),0,0,6,5,1);//50

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
		$YPage = $this->pdf->GetY();
		$margin = 0;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1 ;
		$lDiag = floor($w - $margin * 1 );

		$this->pdf->setXY($XPage,$YPage-3);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($w,0,$titel,0,0,'L');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

	//	$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]));
  
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
		for($i=$nulpunt; $i<= round($bodem,1); $i+= round($absUnit*$stapgrootte,1))
		{
			$skipNull = true;
			$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
			$n++;
			if($n >20)
				break;
		}

		$n=0;
		for($i=$nulpunt; $i >= round($top,1); $i-= round($absUnit*$stapgrootte,1))
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
		for ($i=0; $i<count($data); $i++)
		{
			if($i%$jaren==0)
				$this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
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
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
			$this->pdf->Rect($XPage+$step, $YPage+$h+12, 3, 3, 'DF','',$kleur);
			$this->pdf->SetXY($XPage+3+$step,$YPage+$h+12);
			$this->pdf->Cell(0,3,$item);

			if($aantal==3)
				$step+=$this->pdf->GetStringWidth($item)+15;
			else
		  	$step+=($w/count($legendaItems));
		}
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetFillColor(0,0,0);
	}

	function getNorm()
	{
		$DB=new DB();
		$normData=array();
		$normData['totaal']=1;
		$q="SELECT ZorgplichtPerBeleggingscategorie.Beleggingscategorie,ZorgplichtPerRisicoklasse.norm,ZorgplichtPerRisicoklasse.Zorgplicht,CategorienPerHoofdcategorie.Hoofdcategorie
       FROM
       ZorgplichtPerRisicoklasse
       Inner Join ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoklasse.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
       Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
       WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
       ZorgplichtPerRisicoklasse.Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."' 
       ORDER by CategorienPerHoofdcategorie.Hoofdcategorie";
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
			$normData[$data['Hoofdcategorie']] = $data['norm']/100;
		}

		$q="SELECT ZorgplichtPerBeleggingscategorie.Beleggingscategorie,
     CategorienPerHoofdcategorie.Hoofdcategorie,
     ZorgplichtPerPortefeuille.Zorgplicht,
     ZorgplichtPerPortefeuille.norm
     FROM ZorgplichtPerPortefeuille
     JOIN ZorgplichtPerBeleggingscategorie  ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
     Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
     WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
     ORDER by CategorienPerHoofdcategorie.Hoofdcategorie  ";
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
			$normData[$data['Hoofdcategorie']] = $data['norm']/100;
		}

		return $normData;
	}

	function rechtsOnder()
	{

		$this->pdf->setY(117);
		$this->pdf->Ln();
		$this->pdf->SetWidths(array(100,40,140));
		$this->pdf->SetAligns(array('L','L','L','L'));

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Toelichting'));
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$body="Standaarddeviatie wordt gebruikt als een maatstaf voor de risicograad van beleggingen. Het geeft de mate van afwijking van een gemiddelde weer. Risico bij beleggen is te omschrijven als de kans dat het werkelijke rendement afwijkt van het gemiddelde rendement. Dit kan dus zowel een lager als een hoger rendement betekenen. Een hogere standaarddeviatie geeft aan dat sprake is van een hoger risico. Ander woorden voor standaarddeviatie zijn volatiliteit en beweeglijkheid.";
		$this->pdf->row(array('','Standaarddeviatie',$body));
	//	$this->pdf->ln();
		//$kop="Verschil AFM standaarddeviatie en de standaarddeviatie van uw portefeuille. Aur�us Vermogen & Advies presenteert twee verschillende standaarddeviaties.";
		$body="Hierbij is de standaarddeviatie niet berekend op basis van eigen historische cijfers, maar wordt er gebruik gemaakt van door de AFM voorgeschreven gegevens die voor de gehele markt hetzelfde zijn.";
		$this->pdf->row(array('','AFM-Standaarddeviatie',$body));
//		$this->pdf->ln();
		$this->pdf->row(array('','Value at Risk','Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 97,7%. De historische VaR is bepaald aan de hand van de werkelijke maandelijkse rendementsverdeling van de afgelopen 36 maanden.'));
	//	$this->pdf->ln();
		$this->pdf->row(array('','Maximum Draw Down','Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten de volledige periode vanaf de startdatum van de portefeuille.'));
	//	$this->pdf->ln();
		$this->pdf->row(array('','Tracking Error','De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark over de afgelopen 36 maanden.'));
	//	$this->pdf->ln();
		$this->pdf->row(array('','Sharpe Ratio','De Sharpe-ratio is een meting van het naar risico gecorrigeerde rendement van de portefeuille. Hierbij wordt gekeken naar de afgelopen 36 maanden.'));



	}


}
?>