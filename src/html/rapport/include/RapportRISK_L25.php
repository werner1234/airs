<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/19 15:02:02 $
File Versie					: $Revision: 1.38 $

$Log: RapportRISK_L25.php,v $
Revision 1.38  2020/02/19 15:02:02  rvv
*** empty log message ***

Revision 1.37  2020/02/01 18:11:55  rvv
*** empty log message ***

Revision 1.36  2019/11/23 18:37:04  rvv
*** empty log message ***

Revision 1.35  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.34  2017/10/28 18:03:18  rvv
*** empty log message ***

Revision 1.33  2017/10/25 16:00:47  rvv
*** empty log message ***

Revision 1.32  2017/10/18 15:29:27  rvv
*** empty log message ***

Revision 1.31  2017/10/14 17:27:54  rvv
*** empty log message ***

Revision 1.30  2017/10/11 14:57:49  rvv
*** empty log message ***

Revision 1.29  2017/09/30 16:31:15  rvv
*** empty log message ***

Revision 1.28  2017/09/27 15:58:49  rvv
*** empty log message ***

Revision 1.27  2017/09/23 17:42:26  rvv
*** empty log message ***

Revision 1.26  2017/07/23 13:36:28  rvv
*** empty log message ***

Revision 1.25  2017/04/22 16:44:09  rvv
*** empty log message ***

Revision 1.24  2017/04/09 10:13:59  rvv
*** empty log message ***

Revision 1.23  2017/01/15 08:01:57  rvv
*** empty log message ***

Revision 1.22  2017/01/11 17:12:46  rvv
*** empty log message ***

Revision 1.1  2016/09/07 15:42:21  rvv
*** empty log message ***

Revision 1.6  2016/02/20 15:18:29  rvv
*** empty log message ***

Revision 1.5  2015/12/02 16:16:29  rvv
*** empty log message ***

Revision 1.4  2015/11/29 13:14:46  rvv
*** empty log message ***

Revision 1.3  2015/11/25 16:56:13  rvv
*** empty log message ***

Revision 1.2  2015/03/04 16:30:29  rvv
*** empty log message ***

Revision 1.22  2014/12/31 18:09:06  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportRISK_L25
{

	function RapportRISK_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		//$this->pdf->rapport_titel = "Performancemeting";
		$this->pdf->rapport_titel = "Performance en attributie-overzicht per beleggingscategorie en totaal";
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
		$this->pdf->templateVars['RISKPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['RISKPaginas']=$this->pdf->rapport_titel;

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
		$this->rechtsOnder($riskData,$riskBenchmark);
		$this->addStdevGrafieken($stdev);
		$this->addPerfGrafiek($stdev);
    $this->ATTblock();
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


	function rechtsOnder_old($riskData)
	{
    global $__appvar;
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();


		$this->pdf->Ln(2);
		$this->pdf->setXY($this->pdf->marge,120);
		$this->pdf->SetWidths(array(185,55,20));
		$this->pdf->SetAligns(array('L','L','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%',$body));
		$this->pdf->ln(2);
		$this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%',$body));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Standaarddeviatie benchmark',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%',$body));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Value at Risk','€ '.$this->formatGetal((100-$riskData['valueAtRisk'])/100*$totaalWaarde['totaal'],0).'',''));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
		$this->pdf->ln(2);
		$this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',''));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
		$this->pdf->ln(2);
		$this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
		$this->pdf->ln(2);
		$this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',''));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));

	}

	function rechtsOnder($riskData,$riskBenchmark)
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
		$this->pdf->SetWidths(array(185,45,20,20));
		$this->pdf->SetAligns(array('L','L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','','Portefeuille','Benchmark'));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatie'],1).'%',$this->formatGetal($riskData['standaarddeviatieBenchmark'],1).'%'));
		$this->pdf->ln(2);
		$this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%'));
		$this->pdf->ln(2);
		$riskData['valueAtRisk']=(100-$riskData['valueAtRisk'])/100*$totaalWaarde;
		$riskBenchmark['valueAtRisk']=(100-$riskBenchmark['valueAtRisk'])/100*$totaalWaarde;
		$this->pdf->row(array('','Value at Risk','€ '.$this->formatGetal($riskData['valueAtRisk'],0),'€ '.$this->formatGetal($riskBenchmark['valueAtRisk'],0)));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
		$this->pdf->ln(2);
		$this->pdf->row(array('','Maximum Draw Down',$this->formatGetal($riskData['maxDrawdown'],1).'%',$this->formatGetal($riskBenchmark['maxDrawdown'],1).'%'));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
		$this->pdf->ln(2);
		$this->pdf->row(array('','Tracking Error',$this->formatGetal($riskData['trackingError'],1).'%',''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
		$this->pdf->ln(2);
		$this->pdf->row(array('','Sharpe ratio',$this->formatGetal($riskData['sharpeRatio'],1).'',$this->formatGetal($riskBenchmark['sharpeRatio'],1)));
		$this->pdf->ln(2);
		$this->pdf->row(array('','Informatieratio',$this->formatGetal($riskData['informatieratio'],1).'',''));

	}


	function addPerfGrafiek($stdev)
	{
		$portIndex=1;
		$indexIndex=1;
		foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
		{
			if(db2jul($datum) >= $this->pdf->rapport_datumvanaf)
			{
				$benchmarkData = $stdev->reeksen['benchmark'][$datum];
				$juldate = db2jul($datum);
				$portIndex = (1 + $perfData['perf'] / 100) * $portIndex;
				$indexIndex = (1 + $benchmarkData['perf'] / 100) * $indexIndex;
				$perfGrafiek['portefeuille'][] = ($portIndex - 1) * 100;
				$perfGrafiek['specifiekeIndex'][] = ($indexIndex - 1) * 100;
				$perfGrafiek['datum'][] = date("M y", $juldate);
				//echo "$datum ".(($portIndex-1)*100)."<br>\n";
				//echo "$datum gebruikt. <br>\n";
			}
		//	else
				//echo "$datum overgeslagen. <br>\n";
		}

		$perfGrafiek['legenda']=array('Portefeuille',$this->index['Omschrijving']);

		$this->pdf->setXY(20,35);
		$portKleur=array($this->pdf->rapport_grafiek_color[0],$this->pdf->rapport_grafiek_color[1],$this->pdf->rapport_grafiek_color[2]);
		$indexKleur=array(0,49,60);
		$perfGrafiek['titel']='Portefeuille rendement';
		$this->LineDiagram(120, 55, $perfGrafiek,array($portKleur,$indexKleur),0,0,6,5,true);//50


	}

	function addStdevGrafieken($stdev)
	{
		foreach($stdev->standaardDeviatieReeksen['totaal'] as $datum=>$devData)
		{
			$benchmarkData=$stdev->standaardDeviatieReeksen['benchmark'][$datum];
			$afmData=$stdev->standaardDeviatieReeksen['afm'][$datum];

			$grafiekData['totaal']['datum'][]= date("M y",db2jul($datum));
			$grafiekData['totaal']['portefeuille'][]= $devData['stdev'];
			$grafiekData['totaal']['specifiekeIndex'][]= $benchmarkData['stdev'];
			$grafiekData['totaal']['afm'][]= $afmData['stdev'];

			$grafiekData['afm']['datum'][]= date("M y",db2jul($datum));
			$grafiekData['afm']['portefeuille'][]= $afmData['stdev'];
		}
		$grafiekData['totaal']['titel']='Standaarddeviatie';
		$grafiekData['afm']['titel']='AFM Standaarddeviatie portefeuille';

		$grafiekData['totaal']['legenda']=array('Portefeuille',$this->index['Omschrijving'],'AFM');

		$this->pdf->setXY(160,35);

		$portKleur=array($this->pdf->rapport_grafiek_color[0],$this->pdf->rapport_grafiek_color[1],$this->pdf->rapport_grafiek_color[2]);
		$afmkleur=array(122,153,172);
		$indexKleur=array(0,49,60);
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

		$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]));
  
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
	
	function TwrNaarMaanden($data)
  {
    $perioden=array();
  //  return $data;
    $datumBegin='';
    $datumEind='';
    $maanden=array();
    foreach($data as $datum=>$periodeData)
    {
      $start=substr($periodeData['periode'],0,10);
      $stop=substr($periodeData['periode'],11,10);
      if($datumBegin=='')
        $datumBegin=$start;
      $datumEind=$stop;

      //$perioden[substr($periodeData['periode'],11,10)]=array('start'=>substr($periodeData['periode'],0,10),'stop'=>substr($periodeData['periode'],11,10));
    }
    
    //listarray($data);
    $index=new indexHerberekening();
    $perioden=$index->getMaanden(db2jul($datumBegin),db2jul($datumEind));
    foreach($perioden as $periode)
    {
      $start=$periode['start'];
      $stop=$periode['stop'];
      $maanden[$start]=$start;
      $maanden[$stop]=$stop;
    }
  
    $conversie=array();
    $perf=0;
    $maandTotalen=array();
    $somvelden=array('storting','onttrekking','gerealiseerd','ongerealiseerd','opbrengst','kosten','rente','resultaat');
    foreach($data as $datum=>$periodeData)
    {
      //listarray($periodeData);
      if(!isset($maandTotalen['waardeBegin']))
        $maandTotalen['waardeBegin']=$data['waardeBegin'];
      $stop=substr($periodeData['periode'],11,10);
  
      foreach($somvelden as $veld)
        $maandTotalen[$veld]+=$data[$veld];
  
      $maandTotalen['procent'] = ((1 + $maandTotalen['procent'] ) * (1 + $periodeData['procent']) - 1);
      if(isset($maanden[$datum]))
      {
        foreach($somvelden as $veld)
          $periodeData[$veld]=$maandTotalen[$veld];
        $periodeData['waardeBegin']=$maandTotalen['waardeBegin'];
        $periodeData['procent']=$maandTotalen['procent'];
        $maandTotalen=array();
        $conversie[$stop]=$periodeData;

      }
    }
   
    
    return $conversie;
  }

	function ATTblock()
	{

		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
/*
		$stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
	//	$stdev->setStartdatum($this->rapportageDatumVanaf);
		$stdev->settings['julStartdatum']=db2jul($this->rapportageDatumVanaf);
		$stdev->settings['Startdatum']=date('Y-m-d',$stdev->settings['julStartdatum']);
	//	$stdev->noTotaal=false;
		$stdev->addReeks('hoofdCategorie');
		$reeksen=$stdev->reeksen;
  */
    $reeksen=array();
    include_once("rapport/include/ATTberekening_L25.php");
    $this->att=new ATTberekening_L25($this);
    $this->att->indexPerformance=false;
    if($this->pdf->portefeuilledata['PerformanceBerekening']==3 && intval(substr($this->rapportageDatum,0,4))>=2021)
    {
      $perioden='maandenTWR';
    }
    else
    {
      $perioden='maanden';
    }
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'','hoofdcategorie',$perioden);
    foreach($this->waarden['Periode'] as $hcat=>$maandData)
    {
      if($hcat=='totaal')
        continue;
      foreach($this->TwrNaarMaanden($maandData['perfWaarden']) as $maand=>$waarden)
      {
        $reeksen[$hcat][$maand]=array('perf'=>$waarden['procent']*100,
                                      'aandeelOpTotaal'=>$waarden['aandeelOpTotaal'],
                                      'resultaat'=>$waarden['resultaat'],
                                      'gemWaarde'=>$waarden['gemWaarde'],
                                      'totaalGemWaarde'=>$this->att->perfTotaal['perfWaarden'][$maand]['gemWaarde'],
                                      'datum'=>$maand);
      }
    }
   // unset($this->waarden['Periode']['totaal']);
  //  listarray($this->waarden['Periode']);
  //  listarray($stdev->reeksen);
  //  listarray($reeksen);
    /*   */
	//	listarray($stdev->reeksen);
		$totalen=array();
		$categorieOmschrijving=array('totaal'=>'Totaal');
		$query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds,Beleggingscategorien.Omschrijving FROM IndexPerBeleggingscategorie 
      LEFT JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie=Beleggingscategorien.Beleggingscategorie
      WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
            AND (IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
			$DB->SQL($query);
			$DB->Query();
	  	$benchmarkLookup=array();
			while($index=$DB->nextRecord())
			{
				$benchmarkLookup[$index['Beleggingscategorie']] = $index['Fonds'];
				$categorieOmschrijving[$index['Beleggingscategorie']] = $index['Omschrijving'];
			}
		$benchmarkLookup['totaal']=$this->pdf->portefeuilledata['SpecifiekeIndex'];

		 $normData=$this->getNorm();
	//	listarray($normData);
//listarray($benchmarkLookup);

	//	$beginDatum=$this->rapportageDatumVanaf;
		//$this->maandTotalen=array();
		$categorieRegels=array('H-Aand'=>array(),'H-AltBel'=>array(),'H-Oblig'=>array(),'H-Liq'=>array());
		//$this->maandCumulatief=array();
		$aandeelBinnenCategorie=array();

		foreach($reeksen as $hoofcategorie=>$maandWaarden)
		{
		  $cumuBenchmark=0;
			$cumuPerf=0;
			$beginDatum=$this->rapportageDatumVanaf;
			$benchmarkPerfTag='';
			foreach($maandWaarden as $datum=>$perfData)
			{
				$benchmarkPerfArray=getFondsPerformance_L25($benchmarkLookup[$hoofcategorie],$beginDatum ,$datum );
				$benchmarkPerf=$benchmarkPerfArray['perf'];

				$cumuBenchmark=((1+$cumuBenchmark/100)*(1+$benchmarkPerf/100)-1)*100;
				$cumuPerf=((1+$cumuPerf/100)*(1+$perfData['perf']/100)-1)*100;
				$categorieRegels[$hoofcategorie]['benchmarkPerf']=$cumuBenchmark;
				$categorieRegels[$hoofcategorie]['benchmarkPerfTag']=$benchmarkPerfArray['tag'];
				$categorieRegels[$hoofcategorie]['perf']=$cumuPerf;

				//echo "$beginDatum ,$datum | $benchmarkPerf | $hoofcategorie | ".$benchmarkLookup[$hoofcategorie]." | $cumuBenchmark<br>\n";
		//		echo "$hoofcategorie | ". ($perfData['perf'] )." | ".$perfData['resultaat']." | ".$perfData['gemWaarde']." | ".$perfData['totaalGemWaarde']."<br>\n";
        if($hoofcategorie <> 'totaal')
				{
					$perfData['aandeel']=$perfData['gemWaarde']/$perfData['totaalGemWaarde'];
					$totalen[$datum] += ($perfData['perf'] * $perfData['aandeel']);
					$aandeelBinnenCategorie[$hoofcategorie][] = $perfData['aandeelOpTotaal'];
					$categorieRegels[$hoofcategorie]['allocateEffect'] += ($perfData['aandeelOpTotaal'] - $normData[$hoofcategorie]) * $benchmarkPerf;
					$categorieRegels['totaal']['allocateEffect'] += ($perfData['aandeelOpTotaal'] - $normData[$hoofcategorie]) * $benchmarkPerf;//wordt gebruikt
				}
				$beginDatum=$datum;
			}
		}
//listarray($categorieRegels);
		$tmp=array();
		foreach($aandeelBinnenCategorie as $categorie=>$waarden)
		{
			$tmp[$categorie] = array_sum($waarden) / count($waarden);
		}
		//listarray($aandeelBinnenCategorie);
		//listarray($tmp);
		//listarray($stdev->reeksen);

		$aandeelBinnenCategorie=$tmp;
		$aandeelBinnenCategorie['totaal']=1;
/*
		$cumu=0;
		foreach($totalen as $datum=>$perf)
		{
			$cumu=((1+$cumu/100)*(1+$perf/100)-1)*100;
		}
		$categorieRegels['totaal']['perf']=$cumu;
  */
    $categorieRegels['totaal']['perf']=$this->waarden['Periode']['totaal']['procent'];
	//	listarray($totalen);
	//	echo "$cumu<br>\n";
		//$stdev->berekenWaarden();
//exit;

		//$this->oib = new RapportOIB_L35($this->pdf , $this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
	//	$this->oib->getOIBdata();
	//	$this->oib->hoofdcategorien['geen-Hcat']='geen-Hcat';
		//$oibData=$this->oib->hoofdCatogorieData;
	//	$oibData['totaal']['port']['procent']=1;

		$startJaar=date("Y",$this->pdf->rapport_datum);
		$this->oib->hoofdcategorien['totaal']="Totaal";
		//$this->pdf->rapport_titel = "Performance en attributie-overzicht per beleggingscategorie en totaal";
		$this->pdf->setXY($this->pdf->marge,120);
		//$this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
		$w=20;
		$this->pdf->SetWidths(array(35,$w,$w,$w,$w,$w,$w,$w));
		$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
		$this->pdf->row(array("","Gemiddelde tactische\nWeging","Strategische\nWeging","Rendement\nPortefeuille","Ontwikkeling\nbenchmark",'Attributie',"Allocatie\neffect","Selectie\neffect"));
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->ln();
		$tag=false;
		foreach ($categorieRegels as $categorie=>$waarden)
		{

			if($waarden['benchmarkPerfTag']<>'')
				$tag=true;
		//	listarray($waarden);
      if(!isset($categorieOmschrijving[$categorie]))
        $omschrijving=$categorie;
      else
        $omschrijving=$categorieOmschrijving[$categorie];
			$this->pdf->row(array($omschrijving,
												$this->formatGetal($aandeelBinnenCategorie[$categorie]*100,1), // . ' '.$this->formatGetal(($oibData[$categorie]['port']['procent'])*100,1)
												$this->formatGetal($normData[$categorie]*100,1),
												$this->formatGetal($waarden['perf'],2),
												$this->formatGetal($waarden['benchmarkPerf'],2).$waarden['benchmarkPerfTag'],
												$this->formatGetal(($waarden['perf']-$waarden['benchmarkPerf']),2),//$this->formatGetal((($waarden['weging']*$waarden['procent'])-($waarden['indexPerf']*$waarden['indexBijdrageWaarde']))*100,2),
												$this->formatGetal($waarden['allocateEffect'],2),
												$this->formatGetal((($waarden['perf']-$waarden['benchmarkPerf'])-$waarden['allocateEffect']),2)));
			$this->pdf->excelData[]=array($this->oib->hoofdcategorien[$categorie],
				$waarden['procent'],
				$waarden['weging'],
				$waarden['indexBijdrageWaarde'],
				$waarden['indexPerf'],
				$waarden['procent']-$waarden['indexPerf'],
				$waarden['allocateEffect'],
				(($waarden['procent']-$waarden['indexPerf'])-$waarden['allocateEffect']));
			$this->pdf->ln(2);


		}
		if($tag==true)
			$this->pdf->Cell(60,4,'* deze koers heeft een vertraging van één of meerdere dagen', 0,0, "L");

		$this->pdf->rapport_titel = "Performance en attributie-overzicht per beleggingscategorie en totaal";
		$this->pdf->addPage();
		$this->pdf->Ln();
		$this->pdf->SetWidths(array(10,40,200));
		$this->pdf->SetAligns(array('L','L','L','L'));

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Toelichting'));
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$body="Standaarddeviatie wordt gebruikt als een maatstaf voor de risicograad van beleggingen. Het geeft de mate van afwijking van een gemiddelde weer. Risico bij beleggen is te omschrijven als de kans dat het werkelijke rendement afwijkt van het verwachte rendement. Dit kan dus zowel een lager als een hoger rendement betekenen. Een hogere standaarddeviatie geeft aan dat sprake is van een hoger risico. Ander woorden voor standaarddeviatie zijn volatiliteit en beweeglijkheid. De rendementen van aandelen schommelen meer dan die van obligaties. Dit komt tot uitdrukking in het verschil in standaarddeviatie,die bij obligaties doorgaans lager is. Naarmate de rendementen in het verleden (voorafgaande 36 maanden) meer schommelden, is de standaarddeviatie hoger en dat geldt daarmee ook voor het risico. N.B. Dit zijn cijfers gebaseerd op rendementen in het verleden. Toekomstige marktontwikkelingen kunnen voor een heel andere uitkomst zorgen.";
		$this->pdf->row(array('','Standaarddeviatie',$body));
		$this->pdf->ln();
		//$kop="Verschil AFM standaarddeviatie en de standaarddeviatie van uw portefeuille. Auréus Vermogen & Advies presenteert twee verschillende standaarddeviaties.";
		$body="Hierbij is de standaarddeviatie niet berekend op basis van eigen historische cijfers, maar wordt er gebruik gemaakt van door de AFM voorgeschreven gegevens die voor de gehele markt hetzelfde zijn.";
		$this->pdf->row(array('','AFM-Standaarddeviatie',$body));
		$this->pdf->ln();
		$this->pdf->row(array('','Value at Risk','Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 97,7%. De historische VaR is bepaald aan de hand van de werkelijke maandelijkse rendementsverdeling van de afgelopen 36 maanden.'));
		$this->pdf->ln();
		$this->pdf->row(array('','Maximum Draw Down','Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten de volledige periode vanaf de startdatum van de portefeuille.'));
		$this->pdf->ln();
		$this->pdf->row(array('','Tracking Error','De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark over de afgelopen 36 maanden.'));
		$this->pdf->ln();
		$this->pdf->row(array('','Sharpe Ratio','De Sharpe-ratio is een meting van het naar risico gecorrigeerde rendement van de portefeuille. Hierbij wordt gekeken naar de afgelopen 36 maanden.'));
		$this->pdf->ln();
		$this->pdf->row(array('','Informatie Ratio','De Informatie ratio is een meting van het voor risico gecorrigeerde rendement van de portefeuille ten opzichte van de benchmark over de afgelopen 36 maanden.'));
		//$this->pdf->ln();
		//$this->pdf->row(array('','Active Share','De Active Share geeft een indicatie van de afwijking van de opbouw van de portefeuille ten opzichte van de opbouw van de benchmark.'));


	}


}
?>