<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/20 16:19:15 $
File Versie					: $Revision: 1.29 $

$Log: RapportPERF_L35.php,v $
Revision 1.29  2019/11/20 16:19:15  rvv
*** empty log message ***

Revision 1.28  2019/10/05 17:36:53  rvv
*** empty log message ***

Revision 1.27  2019/05/08 15:11:07  rvv
*** empty log message ***

Revision 1.26  2019/04/17 14:58:31  rvv
*** empty log message ***

Revision 1.25  2019/01/26 19:33:28  rvv
*** empty log message ***

Revision 1.24  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.23  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.22  2018/05/12 15:46:42  rvv
*** empty log message ***

Revision 1.21  2018/05/09 13:22:09  rvv
*** empty log message ***

Revision 1.20  2018/03/10 18:24:22  rvv
*** empty log message ***

Revision 1.18  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.17  2018/02/19 07:16:26  rvv
*** empty log message ***

Revision 1.16  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.15  2018/01/27 17:31:22  rvv
*** empty log message ***

Revision 1.14  2018/01/13 19:10:29  rvv
*** empty log message ***

Revision 1.13  2017/11/18 18:58:17  rvv
*** empty log message ***

Revision 1.12  2017/11/04 17:40:21  rvv
*** empty log message ***

Revision 1.11  2017/10/14 17:27:54  rvv
*** empty log message ***

Revision 1.10  2014/11/23 14:13:22  rvv
*** empty log message ***

Revision 1.9  2014/10/04 15:21:52  rvv
*** empty log message ***



*/
include_once("rapport/include/RapportOIB_L35.php");
include_once("rapport/include/RapportPERFG_L35.php");
include_once("rapport/include/ATTberekening_L35.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportPERF_L35
{
	function RapportPERF_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->pdf = &$pdf;
	 	$this->oib = new RapportOIB_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->perfg = new RapportPERFG_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Kerngegevens rapportage";
  	$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
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
		// OIB grafiek rechts boven.
		// Perfg grafiek eerste pagina (totaal)

		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
		{
			$koersQuery = " / (SELECT Koers FROM Valutakoersen WHERE Valuta='" . $this->pdf->rapportageValuta . "' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		}
		else
		{
			$koersQuery = "";
		}

		if ($this->pdf->rapport_layout == 1)
		{
			$kopStyle = "";
		}
		else
		{
			$kopStyle = "u";
		}

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(0, 85, 30, 10, 30, 120);
		$this->pdf->alignA = array('L', 'L', 'R', 'L', 'R');

		// voor kopjes
		$this->pdf->widthB = array(1, 95, 30, 10, 30, 120);
		$this->pdf->alignB = array('L', 'L', 'R', 'L', 'R');

		$this->pdf->AddPage();
		$this->pdf->templateVars['PERFPaginas'] = $this->pdf->page;


		if (count($this->pdf->portefeuilles) > 0)
		{
			$this->writeGeconsolideerd();
		}
		else
		{
			$this->writeOngeconsolideerd();
		}
	}

	function getFondsKoers($fonds,$datum)
	{
		$db=new DB();
		$query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
		$db->SQL($query);
		$koers=$db->lookupRecord();
		return $koers['Koers'];
	}

	function getValutaKoers($valuta,$datum)
	{
		$db=new DB();
		$query="SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
		$db->SQL($query);
		$koers=$db->lookupRecord();
		return $koers['Koers'];
	}

	function getPerformance($fonds,$vanaf,$tot,$valuta=false,$indexdata=array())
	{
		$att=new ATTberekening_L35($this);
		$maanden=$att->getMaanden(db2jul($vanaf),db2jul($tot));
		$januari=substr($tot,0,4)."-01-01";

		$totalPerf=0;
		foreach($maanden as $maand)
		{
			if($indexdata['catOmschrijving']=='Benchmark')
			{
				$totaalIndex=$att->indexPerformance('totaal',$maand['start'],$maand['stop']);
				$totalPerf+=($totaalIndex['perf']*100);
			}
			else
			{
				if($valuta==true)
					$indexData=array('fondsKoers_eind'=>$this->getValutaKoers($fonds,$maand['stop']),
													 'fondsKoers_begin'=>$this->getValutaKoers($fonds,$maand['start']),
													 'fondsKoers_jan'=>$this->getValutaKoers($fonds,$januari));
				else
					$indexData=array('fondsKoers_eind'=>$this->getFondsKoers($fonds,$maand['stop']),
													 'fondsKoers_begin'=>$this->getFondsKoers($fonds,$maand['start']),
													 'fondsKoers_jan'=>$this->getFondsKoers($fonds,$januari));

				$jaarPerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );
				$voorPerf=($indexData['fondsKoers_begin'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );
				$totalPerf+=($jaarPerf-$voorPerf);
			}
			//echo "m $fonds ".($jaarPerf-$voorPerf)." <br>\n";
		}
		//echo "t $fonds $totalPerf  $vanaf,$tot <br>\n";
		return $totalPerf;
	}


	function AddBenchmarks($portefeuilleIndex)
	{

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
			$this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
		elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
			$this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
		else
			$this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

		$DB=new DB();

		$query="SELECT specifiekeIndex as Beursindex,
    Fondsen.Omschrijving,
Fondsen.Valuta,
'Gecombineerd' as catOmschrijving
 FROM Portefeuilles 
 Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds
 WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
		$DB->SQL($query);
		$DB->Query();
		$specifiekeIndex = $DB->nextRecord();

		$indexData[$specifiekeIndex['Beursindex']]=$specifiekeIndex;
		$indexData[$specifiekeIndex['Beursindex']]['performance']=$portefeuilleIndex[$specifiekeIndex['Beursindex']];
		/*
        $query="SELECT
    IndexPerBeleggingscategorie.Beleggingscategorie,
    IndexPerBeleggingscategorie.Fonds as Beursindex,
    IndexPerBeleggingscategorie.Vermogensbeheerder,
    Fondsen.Omschrijving,
    Beleggingscategorien.Omschrijving as catOmschrijving
    FROM
    IndexPerBeleggingscategorie
    INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
    INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
    WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
    (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille = '".$this->portefeuille."')
    ORDER BY Beleggingscategorien.Afdrukvolgorde";
             */
            $query="SELECT
 IndexPerBeleggingscategorie.Portefeuille,
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds AS Beursindex,
IndexPerBeleggingscategorie.Vermogensbeheerder,
Fondsen.Omschrijving,
Beleggingscategorien.Omschrijving AS catOmschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN CategorienPerHoofdcategorie ON IndexPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
        WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
        (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille = '".$this->portefeuille."')
        GROUP BY IndexPerBeleggingscategorie.Fonds
        ORDER BY Beleggingscategorien.Afdrukvolgorde, IndexPerBeleggingscategorie.Portefeuille desc";

		$DB->SQL($query);
		$DB->Query();

		$gebruikteCategorien=array();
		while($index = $DB->nextRecord())
		{
			if(in_array($index['Beleggingscategorie'],$gebruikteCategorien) && $index['Portefeuille']=='')
        continue;

			if($index['catOmschrijving'] == '')
				$index['catOmschrijving']='Overige';

			$benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];
			
			$gebruikteCategorien[]=$index['Beleggingscategorie'];

			$indexData[$index['Beursindex']]=$index;
			foreach ($perioden as $periode => $datum)
			{
				$indexData[$index['Beursindex']]['fondsKoers_' . $periode] = $this->getFondsKoers($index['Beursindex'], $datum);
			}
			$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);
		}


		//listarray($indexData);
		$this->pdf->setY(85);
		$this->pdf->setWidths(array(45,50,20,5));
		$this->pdf->setAligns(array('L','L','R','L'));


		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
		$this->pdf->row(array('Ontwikkeling benchmarks','','Rendement'));
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

		foreach($indexData as $fonds=>$fondsData)
		  $this->pdf->row(array($fondsData['catOmschrijving'],$fondsData['Omschrijving'],$this->formatGetal($fondsData['performance'],2),'%'));
	}

			function writeOngeconsolideerd()
			{
				global $__appvar;
			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r], $this->pdf->rapport_fontcolor[g], $this->pdf->rapport_fontcolor[b]);
			$x = $this->pdf->setX();
			$y = $this->pdf->getY();

			//grafiek rechts boven
			$DB = new DB();
			$q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
			$DB->SQL($q);
			$DB->Query();
			$kleuren = $DB->LookupRecord();
			$kleuren = unserialize($kleuren['grafiek_kleur']);
			$kleuren = $kleuren['OIB'];
			$query = "SELECT Beleggingscategorie,Omschrijving as value FROM Beleggingscategorien";
			$DB->SQL($query);
			$DB->Query();
			while ($data = $DB->nextRecord())
			{
				$omschrijving[$data['Beleggingscategorie']] = $data['value'];
			}

			$this->oib->getOIBdata();
			foreach ($this->oib->hoofdCatogorieData as $categorie => $data)
			{

				if ($data['port']['procent'] > 0)
				{
					$portefeuilleGrafiekData[ vertaalTekst($omschrijving[$categorie], $this->pdf->rapport_taal)] = round($data['port']['procent'] * 100, 1);
					$portefeuilleGrafiekKleur[] = array($kleuren[$categorie]['R']['value'], $kleuren[$categorie]['G']['value'], $kleuren[$categorie]['B']['value']);
				}
			}

			// einde grafiek rechtsboven
			//begin grafiek rechts onder
			$indexLookup['totaal'] = $this->pdf->portefeuilledata['SpecifiekeIndex'];
			$start = $this->rapportageDatumVanaf;
			$eind = $this->rapportageDatum;
			$datumStop = db2jul($eind);
			$att = new ATTberekening_L35($this);
			$hcatData = $att->bereken($start, $eind, $this->pdf->rapportageValuta, 'hoofdcategorie');

			$maandPeriode = mktime(0, 0, 0, 1, 1, date("Y", $datumStop));//-1

			$categorien = array('ZAK', 'VAR', 'totaal');
			$index=array();
			foreach ($categorien as $cat)
			{
				$perfIndexCum = 0;
				foreach ($att->waarden[$cat] as $datum => $data)
				{
					$juldate = db2jul($datum);
					if ($juldate > $maandPeriode)
					{
						$perfIndex = $this->perfg->fondsPerf($indexLookup[$cat], date("Y-m-d", mktime(0, 0, 0, substr($datum, 5, 2), 0, substr($datum, 0, 4))), $datum);

					//	$perfIndexCum = ($perfIndexCum * (1 + $perfIndex));
						$perfIndexCum+=$perfIndex*100;
						$data['specifiekeIndex'] = $perfIndexCum;// ($perfIndexCum - 1) * 100;
						$hcatWaarden['maanden'][$cat]['portefeuille'][] = $data['indexBruto'] - 100;
						$hcatWaarden['maanden'][$cat]['specifiekeIndex'][] = $data['specifiekeIndex'];
						$hcatWaarden['maanden'][$cat]['datum'][] = date("M", $juldate);
						$hcatWaarden['maanden'][$cat]['waarde'][] = $data;

						$index[$cat]=$data['specifiekeIndex'];
					}
				}
			}

			//$typen=array('procent','indexPerf'); //,'bijdrage'
			$stapelTypen = array('procent'); //,'bijdrage'
			$somTypen = array('indexPerf');
			foreach ($hcatData as $categorie => $categorieData)
			{
				$laatste = array();
				foreach ($categorieData['perfWaarden'] as $datum => $waarden)
				{

					$this->jaarTotalen[$categorie]['resultaat'] += $waarden['resultaat'];
					foreach ($stapelTypen as $type)
					{
						$this->jaarTotalen[$categorie][$type] = ((1 + $waarden[$type]) * (1 + $laatste[$type]) - 1);
						$laatste[$type] = $this->jaarTotalen[$categorie][$type];
					}
					foreach ($somTypen as $type)
					{
						$this->jaarTotalen[$categorie][$type] += $waarden[$type];
						$laatste[$type] = $this->jaarTotalen[$categorie][$type];
					}
					if ($categorie != 'totaal')
					{
						$this->jaarTotalen[$categorie]['allocateEffect'] += ($waarden['weging'] - $waarden['indexBijdrageWaarde']) * $waarden['indexPerf'];
						$this->jaarTotalen[$categorie]['selectieEffect'] += ($waarden['procent'] - $waarden['indexPerf']) * $waarden['weging'];
						$this->jaarTotalen['totaal']['allocateEffect'] += ($waarden['weging'] - $waarden['indexBijdrageWaarde']) * $waarden['indexPerf'];
					}
					$this->jaarTotalen[$categorie]['portBijdrage'] += $waarden['bijdrage'];
				}
			}
			$this->oib->hoofdcategorien['totaal'] = "Totaal";
			foreach ($this->jaarTotalen as $categorie => $waarden)
			{
				$grafiekWaarden[$this->oib->hoofdcategorien[$categorie]] = array($waarden['allocateEffect'] * 100,
					(($waarden['procent'] - $waarden['indexPerf']) - $waarden['allocateEffect']) * 100,
					($waarden['procent'] - $waarden['indexPerf']) * 100);
			}

			$this->pdf->setXY($this->pdf->marge, 125);
			$this->pdf->setWidths(array(90, 90, 90));
			$this->pdf->setAligns(array('C', 'C', 'C'));
			$this->pdf->row(array( vertaalTekst('Portefeuille verdeling', $this->pdf->rapport_taal),  vertaalTekst('Portefeuille rendement', $this->pdf->rapport_taal),  vertaalTekst('Attributie analyse', $this->pdf->rapport_taal)));

			$this->pdf->setXY(30, 135);
			$this->pdf->PieChart(50, 45, $portefeuilleGrafiekData, '%l (%p)', $portefeuilleGrafiekKleur);


			$this->pdf->setXY(110, 130);
			$this->perfg->LineDiagram(70, 45, $hcatWaarden['maanden'][$cat], array(array(87, 165, 25), array(0, 52, 121)), 0, 0, 6, 5, 1);//50
			//einde grafiek rechts onder

			$this->pdf->setXY(210, 179);
			$this->VBarDiagram(50, 47, $grafiekWaarden['Totaal'], '');

			$xstep = 25;
			$xgrafiek = 210;
			$ygrafiek = 185;
			$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
			//$legenda = array('Allocatie effect' => array(87, 165, 25), 'Selectie effect' => array(255, 0, 59), 'Totaal' => array(0, 52, 121));
			$legenda = array('Allocatie effect' => array(108,31,128), 'Selectie effect' => array(234,105,11), 'Totaal' => array(0, 52, 121));

			foreach ($legenda as $omschrijving => $color)
			{
				$this->pdf->setXY($xgrafiek, $ygrafiek);
				$this->pdf->Rect($xgrafiek - 3, $ygrafiek, 3, 3, 'DF', '', $color);
				$this->pdf->Cell(100, 4,  vertaalTekst($omschrijving,$this->pdf->rapport_taal), 0, 0, 'L');
				$xgrafiek += $xstep;
			}

			$this->pdf->SetDrawColor(0, 0, 0);
			$this->pdf->SetFillColor(0, 0, 0);


			$this->pdf->setXY($x, $y);

			// ***************************** ophalen data voor afdruk ************************ //

			// haal totaalwaarde op om % te berekenen
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " AS totaal " .
				"FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum ='" . $this->rapportageDatum . "' AND " .
				" portefeuille = '" . $this->portefeuille . "' "
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query, __FILE__, __LINE__);

			$DB->SQL($query);
			$DB->Query();
			$totaalWaarde = $DB->nextRecord();

			// haal totaalwaarde op om % te berekenen
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersBegin . " ) AS totaal " .
				"FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum ='" . $this->rapportageDatumVanaf . "' AND " .
				" portefeuille = '" . $this->portefeuille . "' "
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query, __FILE__, __LINE__);

			$DB->SQL($query);
			$DB->Query();
			$totaalWaardeVanaf = $DB->nextRecord();

			$waardeEind = $totaalWaarde[totaal];
			$waardeBegin = $totaalWaardeVanaf[totaal];
			$waardeMutatie = $waardeEind - $waardeBegin;
			$stortingen = getStortingen($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta);
			$onttrekkingen = getOnttrekkingen($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta);
			$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
			$rendementProcent = performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
			// $rendementProcent=$hcatData['totaal']['procent'];


//listarray($hcatData['totaal']['procent']);

			//echo "$rendementProcent  	= performanceMeting(".$this->portefeuille.", ".$this->rapportageDatumVanaf.", ".$this->rapportageDatum.", ".$this->pdf->portefeuilledata['PerformanceBerekening'].",".$this->pdf->rapportageValuta." ";exit;
			if ($this->pdf->rapport_PERF_jaarRendement)
			{
				$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
				if (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
				{
					$startDatum = $this->pdf->PortefeuilleStartdatum;
					$fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $this->pdf->PortefeuilleStartdatum, true);
					vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $this->pdf->PortefeuilleStartdatum);
				}
				else
				{
					$startDatum = "$RapStartJaar-01-01";
				}

				if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
				{
					$fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $startDatum, true);
					vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $startDatum);
				}
				$rendementProcentJaar = performanceMeting($this->portefeuille, $startDatum, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
			}

			// ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . "  AS totaalB, " .
				"SUM(beginPortefeuilleWaardeEuro)/ " . $this->pdf->ValutaKoersStart . "  AS totaalA " .
				"FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum ='" . $this->rapportageDatum . "' AND " .
				" portefeuille = '" . $this->portefeuille . "' AND "
				. " type = 'fondsen' " . $__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query, __FILE__, __LINE__);
			$DB->SQL($query);
			$DB->Query();
			$totaal = $DB->nextRecord();
			$ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA']; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.

//		datum wordt van het totaal afgehaald.
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersBegin . " AS totaalB, " .
				"SUM(beginPortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersStart . " ) AS totaalA " .
				"FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum ='" . $this->rapportageDatumVanaf . "' AND " .
				" portefeuille = '" . $this->portefeuille . "' AND "
				. " type = 'fondsen' " . $__appvar['TijdelijkeRapportageMaakUniek'];

			$RapJaar = date("Y", db2jul($this->rapportageDatum));
			$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));


			$totaalOpbrengst += $ongerealiseerdeKoersResultaat;

			$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta, true,'Totaal',true);
			$totaalOpbrengst += $gerealiseerdeKoersResultaat['totaal'];

			// ophalen van rente totaal A en rentetotaal B
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
				"FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum ='" . $this->rapportageDatum . "' AND " .
				" portefeuille = '" . $this->portefeuille . "' AND " .
				" type = 'rente' " . $__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query, __FILE__, __LINE__);
			$DB->SQL($query);
			$DB->Query();
			$totaalA = $DB->nextRecord();

			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
				"FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum ='" . $this->rapportageDatumVanaf . "' AND " .
				" portefeuille = '" . $this->portefeuille . "' AND " .
				" type = 'rente' " . $__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query, __FILE__, __LINE__);
			$DB->SQL($query);
			$DB->Query();
			$totaalB = $DB->nextRecord();

			$opgelopenRente = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
			$totaalOpbrengst += $opgelopenRente;

			if ($this->pdf->GrootboekPerVermogensbeheerder)
			{
				$query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
			}
			else
			{
				$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving" .
					" FROM Grootboekrekeningen " .
					" WHERE Grootboekrekeningen.Opbrengst = '1'  " .
					" ORDER BY Grootboekrekeningen.Afdrukvolgorde";
			}

				if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
					$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
				else
					$koersQuery = "";

			$DB = new DB();
			$DB->SQL($query);
			$DB->Query();
			while ($gb = $DB->nextRecord())
			{
				$query = "SELECT  " .
					"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
					"FROM Rekeningmutaties, Rekeningen, Portefeuilles " .
					"WHERE " .
					"Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
					"Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
					"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
					"Rekeningmutaties.Verwerkt = '1' AND " .
					"Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
					"Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' AND " .
					"Rekeningmutaties.Grootboekrekening = '" . $gb['Grootboekrekening'] . "' ";

				$DB2 = new DB();
				$DB2->SQL($query);
				$DB2->Query();

				if ($this->pdf->rapport_layout == 7)
				{
					switch ($gb['Omschrijving'])
					{
						case "Creditrente" :
							$gb['Omschrijving'] = "Rente Bankrekeningen";
							break;
						case "Rente obligaties" :
							$gb['Omschrijving'] = "Ontvangen rente obligaties";
							break;
						case "Meegekochte rente" :
							$gb['Omschrijving'] = "Gekochte en verkochte couponrente";
							break;
					}
				}

				while ($opbrengst = $DB2->nextRecord())
				{
					$opbrengstenPerGrootboek[$gb['Omschrijving']] = ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
					$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
				}
			}

			// loopje over Grootboekrekeningen Kosten = 1
			if ($this->pdf->GrootboekPerVermogensbeheerder)
			{
				$query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, " .
					"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
					"FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder " .
					"WHERE " .
					"Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
					"Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
					"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
					"Rekeningmutaties.Verwerkt = '1' AND " .
					"Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
					"Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' AND " .
					"GrootboekPerVermogensbeheerder.Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND " .
					"Rekeningmutaties.Grootboekrekening = GrootboekPerVermogensbeheerder.GrootboekRekening AND " .
					"GrootboekPerVermogensbeheerder.Kosten = '1' " .
					"GROUP BY Rekeningmutaties.Grootboekrekening " .
					"ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde ";
			}
			else
			{
				$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, " .
					"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
					"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen " .
					"WHERE " .
					"Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
					"Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
					"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
					"Rekeningmutaties.Verwerkt = '1' AND " .
					"Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
					"Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' AND " .
					"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND " .
					"Grootboekrekeningen.Kosten = '1' " .
					"GROUP BY Rekeningmutaties.Grootboekrekening " .
					"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
			}

			$DB = new DB();
			$DB->SQL($query);
			$DB->Query();

			$kostenPerGrootboek = array();

			while ($kosten = $DB->nextRecord())
			{
				if ($kosten['Grootboekrekening'] == "KNBA")
				{
					if ($this->pdf->rapport_layout == 17)
					{
						$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
					}
					else
					{
						$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
					}
					$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
				}
				else if ($kosten['Grootboekrekening'] == "KOBU" && $this->pdf->rapport_layout != 14)
				{
					//$kostenPerGrootboek['KOST'][Omschrijving] = "Bankkosten en provisie";
					$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
				}
				else
				{
					$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
					$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
				}

				$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}

			$kostenProcent = ($totaalKosten / $waardeEind) * 100;


			// het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
			$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst - $totaalKosten);
			$totaalOpbrengst += $koersResulaatValutas;
			// ***************************** einde ophalen data voor afdruk ************************ //

			$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
			$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

			$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;
			$this->pdf->ln();

			$ypos = $this->pdf->GetY();
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			$this->pdf->SetFont($this->pdf->rapport_font, 'b' . $kopStyle, $this->pdf->rapport_fontsize);
			$this->pdf->row(array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", ""));
			$this->pdf->excelData[]=array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", "");
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


			$this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatumVanaf)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatumVanaf))], $this->pdf->taal) . " " . date("Y", db2jul($this->rapportageDatumVanaf)), $this->formatGetal($waardeBegin, 2, true), ""));
			$this->pdf->excelData[]=array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatumVanaf)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatumVanaf))], $this->pdf->taal) . " " . date("Y", db2jul($this->rapportageDatumVanaf)), round($waardeBegin,2), "");
			$this->pdf->ln(2);
			$this->pdf->row(array("", vertaalTekst("Resultaat over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($resultaatVerslagperiode, 2), ""));
				$this->pdf->excelData[]=array("", vertaalTekst("Resultaat over verslagperiode", $this->pdf->rapport_taal), round($resultaatVerslagperiode, 2), "");
			$this->pdf->ln(2);
			$this->pdf->row(array("", vertaalTekst("Totaal stortingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($stortingen, 2), ""));
				$this->pdf->excelData[]=array("", vertaalTekst("Totaal stortingen gedurende verslagperiode", $this->pdf->rapport_taal), round($stortingen, 2), "");
			$this->pdf->ln(2);
			$this->pdf->row(array("", vertaalTekst("Totaal onttrekkingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($onttrekkingen, 2), ""));
				$this->pdf->excelData[]=array("", vertaalTekst("Totaal onttrekkingen gedurende verslagperiode", $this->pdf->rapport_taal), round($onttrekkingen, 2), "");
			$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
			$this->pdf->ln(2);
			$this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatum)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatum))], $this->pdf->taal) . " " . date("Y", db2jul($this->rapportageDatum)), $this->formatGetal($waardeEind, 2), ""));
				$this->pdf->excelData[]=array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatum)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatum))], $this->pdf->taal) . " " . date("Y", db2jul($this->rapportageDatum)), round($waardeEind, 2), "");
			$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
			$this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
			$this->pdf->ln(2);
			$this->pdf->row(array("", vertaalTekst("Netto rendement over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($rendementProcent, 2), "%"));
				$this->pdf->excelData[]=array("", vertaalTekst("Netto rendement over verslagperiode", $this->pdf->rapport_taal), round($rendementProcent, 2), "%");
			if ($this->pdf->rapport_PERF_jaarRendement)
			{
				$this->pdf->row(array("", vertaalTekst("Rendement lopende kalenderjaar", $this->pdf->rapport_taal), $this->formatGetal($rendementProcentJaar, 2), "%", ""));
				$this->pdf->excelData[]=array("", vertaalTekst("Rendement lopende kalenderjaar", $this->pdf->rapport_taal), round($rendementProcentJaar, 2), "%", "");
			}

			$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
			$this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY() + 1, $posSubtotaalEnd, $this->pdf->GetY() + 1);

			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

			$this->pdf->widthA = array(130, 70, 30, 5, 30, 120);
			$this->pdf->alignA = array('L', 'L', 'R', 'R', 'R');

			$this->pdf->widthB = array(130, 70, 30, 5, 30, 120);
			$this->pdf->alignB = array('L', 'L', 'R', 'R', 'R');

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
			$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

			$this->pdf->SetY($ypos);
			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
			$this->pdf->SetFont($this->pdf->rapport_font, 'b' . $kopStyle, $this->pdf->rapport_fontsize);
			$this->pdf->row(array("", vertaalTekst("Samenstelling resultaat over verslagperiode", $this->pdf->rapport_taal), "", ""));
			$this->pdf->excelData[]=array("", vertaalTekst("Samenstelling resultaat over verslagperiode", $this->pdf->rapport_taal), "", "");
			$this->pdf->SetFont($this->pdf->rapport_font, $kopStyle, $this->pdf->rapport_fontsize);
			$this->pdf->row(array("", vertaalTekst("Beleggingsresultaat", $this->pdf->rapport_taal), "", ""));
			$this->pdf->excelData[]=array("", vertaalTekst("Beleggingsresultaat", $this->pdf->rapport_taal), "", "");
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

			$this->pdf->row(array("", vertaalTekst("Ongerealiseerde koersresultaten", $this->pdf->rapport_taal), $this->formatGetal($ongerealiseerdeKoersResultaat, 2), ""));
				$this->pdf->excelData[]=array("", vertaalTekst("Ongerealiseerde koersresultaten", $this->pdf->rapport_taal), round($ongerealiseerdeKoersResultaat, 2), "");
        $this->pdf->row(array("",vertaalTekst("Gerealiseerde Fondsresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat['fonds'],2),""));
        $this->pdf->row(array("",vertaalTekst("Gerealiseerde Valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat['valuta'],2),""));

				$this->pdf->excelData[]=array("", vertaalTekst("Gerealiseerde Fondsresultaten", $this->pdf->rapport_taal), round($gerealiseerdeKoersResultaat['fonds'], 2), "");
        $this->pdf->excelData[]=array("", vertaalTekst("Gerealiseerde Valutaresultaten", $this->pdf->rapport_taal), round($gerealiseerdeKoersResultaat['valuta'], 2), "");
			if (round($koersResulaatValutas, 2) != 0.00)
			{
        $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's liquiditeiten",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
				$this->pdf->excelData[]=array("", vertaalTekst("Koersresultaten valuta's liquiditeiten", $this->pdf->rapport_taal), round($koersResulaatValutas, 2), "");
			}
		  $this->pdf->row(array("", vertaalTekst("Resultaat opgelopen rente", $this->pdf->rapport_taal), $this->formatGetal($opgelopenRente, 2), ""));
			$this->pdf->excelData[]=array("", vertaalTekst("Resultaat opgelopen rente", $this->pdf->rapport_taal),  round($opgelopenRente, 2), "");


			while (list($key, $value) = each($opbrengstenPerGrootboek))
			{
				if (round($value, 2) != 0.00)
				{
					$this->pdf->row(array("", vertaalTekst($key, $this->pdf->rapport_taal), $this->formatGetal($value, 2), ""));
					$this->pdf->excelData[]=array("", vertaalTekst($key, $this->pdf->rapport_taal), round($value, 2), "");
				}
			}

			$this->pdf->Line($posSubtotaal + 2 + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
			$this->pdf->row(array("", "", $this->formatGetal($totaalOpbrengst, 2)));
				$this->pdf->excelData[]=array("", "", round($totaalOpbrengst, 2));
			$this->pdf->ln();

			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);

			$this->pdf->SetFont($this->pdf->rapport_font, $kopStyle, $this->pdf->rapport_fontsize);
			$this->pdf->row(array("", vertaalTekst("Kosten", $this->pdf->rapport_taal), "", ""));
				$this->pdf->excelData[]=array("", vertaalTekst("Kosten", $this->pdf->rapport_taal), "", "");
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			while (list($key, $value) = each($kostenPerGrootboek))
			{
				if (round($kostenPerGrootboek[$key]['Bedrag'], 2) != 0.00)
				{
					$this->pdf->row(array("", vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'], $this->pdf->rapport_taal), $this->formatGetal($kostenPerGrootboek[$key]['Bedrag'], 2), ""));
					$this->pdf->excelData[]=array("", vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'], $this->pdf->rapport_taal), round($kostenPerGrootboek[$key]['Bedrag'], 2), "");
				}
			}

			$this->pdf->Line($posSubtotaal + 2 + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
			$this->pdf->row(array("", "", $this->formatGetal($totaalKosten, 2)));
				$this->pdf->excelData[]=array("", "", round($totaalKosten, 2));

			$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];

			$this->pdf->Line($posTotaal + 2 + $extraLengte, $this->pdf->GetY(), $posTotaal + $this->pdf->widthA[2], $this->pdf->GetY());


			$this->pdf->row(array("", "", $this->formatGetal($totaalOpbrengst - $totaalKosten, 2)));
				$this->pdf->excelData[]=array("", "", round($totaalOpbrengst - $totaalKosten, 2));

			$this->pdf->Line($posTotaal + 2 + $extraLengte, $this->pdf->GetY(), $posTotaal + $this->pdf->widthA[2], $this->pdf->GetY());
			$this->pdf->Line($posTotaal + 2 + $extraLengte, $this->pdf->GetY() + 1, $posTotaal + $this->pdf->widthA[2], $this->pdf->GetY() + 1);

			$actueleWaardePortefeuille = 0;
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

			if ($this->pdf->rapport_PERF_rendement == 1)
			{

				$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
				if (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
				{
					$startDatum = $this->pdf->PortefeuilleStartdatum;
				}
				else
				{
					$startDatum = "$RapStartJaar-01-01";
				}

				if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
				{
					$fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $startDatum, true);
					vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $startDatum);
				}

				$fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $this->pdf->PortefeuilleStartdatum, true);
				vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $this->pdf->PortefeuilleStartdatum);

				$performanceJaar = performanceMeting($this->portefeuille, $startDatum, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
				$performancePeriode = performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
//		  $performanceBegin  = performanceMeting($this->portefeuille,$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,1,$this->pdf->rapportageValuta);

				$this->pdf->SetY($this->pdf->GetY() + 30);
				$extraMarge = 140;
				$this->pdf->SetX($this->pdf->marge);
				$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r], $this->pdf->rapport_kop_bgcolor[g], $this->pdf->rapport_kop_bgcolor[b]);
				$min = 6;
				$this->pdf->Rect($this->pdf->marge + $extraMarge, $this->pdf->getY(), 110, (20 - $min), 'F');
				$this->pdf->SetFillColor(0);
				$this->pdf->Rect($this->pdf->marge + $extraMarge, $this->pdf->getY(), 110, (20 - $min));
				$this->pdf->ln(2);

				$this->pdf->SetX($this->pdf->marge + $extraMarge + 10);
				$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r], $this->pdf->rapport_kop_fontcolor[g], $this->pdf->rapport_kop_fontcolor[b]);
				$this->pdf->Cell(60, 4, vertaalTekst("Resultaat over verslagperiode", $this->rapport_taal), 0, 0, "L");
				$this->pdf->excelData[]=array('',vertaalTekst("Resultaat over verslagperiode", $this->rapport_taal),round($performancePeriode, 2) . "%" );
				$this->pdf->Cell(30, 4, $this->formatGetal($performancePeriode, 2) . "%", 0, 1, "R");
				$this->pdf->ln(2);

				$this->pdf->SetX($this->pdf->marge + $extraMarge + 10);
				$this->pdf->Cell(60, 4, vertaalTekst("Resultaat lopende kalenderjaar", $this->rapport_taal), 0, 0, "L");
				$this->pdf->excelData[]=array('',vertaalTekst("Resultaat lopende kalenderjaar", $this->rapport_taal),round($performanceJaar, 2) . "%");
				$this->pdf->Cell(30, 4, $this->formatGetal($performanceJaar, 2) . "%", 0, 1, "R");
				$this->pdf->ln(2);


			}
			//	$his->pdf->rowHeight=4;

				$this->AddBenchmarks(array($this->pdf->portefeuilledata['SpecifiekeIndex']=>$index['totaal']));


			}


	function writeGeconsolideerd()
	{
		$this->indexberekening=new indexHerberekening();
		$this->perioden=$this->indexberekening->getMaanden(db2jul($this->rapportageDatumVanaf),db2jul($this->rapportageDatum));


		$realCategorie=array();
		foreach($this->berekening->categorien as $categorie)
		{
			if($this->waarden['lopendeJaar']['eindWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['beginWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['stortingen'][$categorie] <> 0 || $this->waarden['lopendeJaar']['onttrekkingen'][$categorie] <> 0)
			{
				$realCategorie[]=$categorie;
			}
		}

		$tmpCat=array();
		foreach($realCategorie as $categorie)
		{
			if($categorie <> 'Totaal' && $categorie <> 'Liquiditeiten')
				$tmpCat[]=$categorie;
		}

		if(count($realCategorie) > 6)
			$x=185/count($realCategorie)-3;
		else
			$x=23;

		$this->pdf->widthA = array(0,115,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->pdf->widthB = array(0,115,30,10,30,116);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R');



		// if(is_array($this->pdf->__appvar['consolidatie']))
		// {


		$fillPortefeuilles=$this->pdf->portefeuilles;
		$fillPortefeuilles[]=$this->portefeuille;

		foreach($fillPortefeuilles as $portefeuille)
		{
			if(!isset($this->perfWaarden[$portefeuille]))
				$this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
		}

		$backup=$this->pdf->portefeuilles;
		$aantalPortefeuilles=count($this->pdf->portefeuilles);
		if($aantalPortefeuilles>6)
		{
			$n=1;
			$p=0;
			$verdeling=array();
			$tmp=array();
			foreach($this->pdf->portefeuilles as $index=>$portefeuille)
			{
				//echo "$n $p $aantalPortefeuilles $portefeuille <br>\n";
				$tmp[]=$portefeuille;
				if($n%6==0 || $n == $aantalPortefeuilles)
				{
					$verdeling[$p]=$tmp;
					$tmp=array();
					$p++;
					// $n=0;
				}

				$n++;
			}
			
			foreach($verdeling as $pagina=>$portefeuilles)
			{
			  if($pagina>0)
        {
          $this->pdf->AddPage();
        }
				$this->pdf->portefeuilles=$portefeuilles;
				$this->addconsolidatie();
			}
			$this->pdf->portefeuilles=$backup;
		}
		else
			$this->addconsolidatie();

		// }

		if($this->pdf->debug)
		{
			// listarray($this->berekening->performance);flush();
			// exit;
		}
	}

	function getWaarden($portefeuille,$vanafDatum,$totDatum)
	{
		global $__appvar;
		// ***************************** ophalen data voor afdruk ************************ //

		$waarden=array();
		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
		{
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
			$totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$vanafDatum);
			$vanRapKoers=getValutaKoers($this->pdf->rapportageValuta,$totDatum);
		}
		else
		{
			$koersQuery = "";
			$totRapKoers=1;
			$vanRapKoers=1;
		}

		if(substr($vanafDatum,5,5)=='01-01')
			$beginJaar=true;
		else
			$beginJaar=false;

		if($this->pdf->lastPOST['doorkijk']==1)
		{
			$waarden=$this->getDoorkijk($portefeuille);
			$totaalOpbrengst=$waarden['totaalOpbrengst'];
		}
		else
		{
			$fondsen=berekenPortefeuilleWaarde($portefeuille,$vanafDatum,$beginJaar,$this->pdf->rapportageValuta,$vanafDatum);
			$totaal=array();
			$totaalWaardeVanaf['totaal']=0;
			foreach($fondsen as $id=>$regel)
			{
				$totaalWaardeVanaf['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
				if($regel['type']=='rente')
				{
					$totaalB['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
				}
			}

			$totaalWaarde['totaal']=0;
			$fondsen=berekenPortefeuilleWaarde($portefeuille,$totDatum,false,$this->pdf->rapportageValuta,$vanafDatum);
			$totaal=array();
			foreach($fondsen as $id=>$regel)
			{
				$totaalWaarde['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
				if($regel['type']=='rente')
				{
					$totaalA['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
				}
				if($regel['type']=='fondsen')
				{
					$totaal['totaalB']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
					$totaal['totaalA']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
				}
			}

			$ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA'];
			$waarden['ongerealiseerdeKoersResultaat']=$ongerealiseerdeKoersResultaat;


			$DB=new DB();

			$waardeEind				  = $totaalWaarde['totaal'];
			$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
			$waardeMutatie 	   	= $waardeEind - $waardeBegin;
			$stortingen 			 	= getStortingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
			$onttrekkingen 		 	= getOnttrekkingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
			$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
			$rendementProcent  	=  performanceMeting($portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);


			$pRec['SpecifiekeIndex']=getSpecifiekeIndex($portefeuille,$this->rapportageDatum);
			if($pRec['SpecifiekeIndex']=='')
			{
				$query = "SELECT GeconsolideerdePortefeuilles.SpecifiekeIndex
    FROM GeconsolideerdePortefeuilles 
    WHERE GeconsolideerdePortefeuilles.VirtuelePortefeuille = '$portefeuille'";
				$DB->SQL($query);
				$pRec = $DB->lookupRecord();
			}
			if($pRec['SpecifiekeIndex']=='')
			{
				$query="SELECT Portefeuille,Startdatum,PerformanceBerekening,ZpMethode, TijdelijkUitsluitenZp,Portefeuilles.Vermogensbeheerder,
    Vermogensbeheerders.check_module_SCENARIO, Portefeuilles.ModelPortefeuille, Portefeuilles.SpecifiekeIndex
    FROM Portefeuilles 
    JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder
    WHERE Portefeuille = '$portefeuille'";
				$DB->SQL($query);
				$pRec=$DB->lookupRecord();
			}


			//  $this->werkelijkeBenchmarkVerdeling
			// echo $vanafDatum,$totDatum;
			// listarray($this->perioden);
			if($portefeuille==$this->portefeuille)
			{
				$benchmarkRendement=0;

				if(1)//count($this->pdf->portefeuilles) > 1)
				{
					$stdev = new rapportSDberekening($this->portefeuille, $this->rapportageDatum, 1);
					$stdev->settings['SdFrequentie'] = 'm';
					$jaar=date("Y",db2jul($this->rapportageDatum));
					if(db2jul("$jaar-01-01")<db2jul($this->pdf->PortefeuilleStartdatum))
						$beginJaar=$this->pdf->PortefeuilleStartdatum;
					else
						$beginJaar="$jaar-01-01";
					$stdev->setStartdatum($beginJaar);
					$stdev->settings['gebruikHistorischePortefeuilleIndex'] = false;
					if (count($this->pdf->portefeuilles) > 1)
					{
						$stdev->consolidatiePortefeuilles = $this->pdf->portefeuilles;
					}
					$stdev->addReeks('benchmarkTot', 'SpecifiekeIndex', false);//$this->index['SpecifiekeIndex']
					//  $stdev->berekenWaarden();
					$benchmarkRendement = $stdev->getReeksRendement('benchmarkTot');
          
          $indexdata['catOmschrijving']='Benchmark';
          $benchmarkRendement= $this->getPerformance(getSpecifiekeIndex($this->portefeuille,$this->rapportageDatum),$beginJaar, $this->rapportageDatum, false,$indexdata);
				}
				/*
        else
        {

        foreach($this->perioden as $periode)
        {
          if(count($this->pdf->portefeuilles) > 1)
          {
            $benchmarkVerdeling=array();
            $herIndex=false;
            foreach($this->pdf->portefeuilles as $cPortefeuille)
            {
              $query="SELECT aandeel*100 as aandeel,datum FROM tempVerdeling WHERE hoofdPortefeuille='" . $this->portefeuille . "' AND  portefeuille='$cPortefeuille' AND datum <='" . $periode['stop']. "' ORDER BY Datum desc limit 1";
              $DB->SQL($query);
              $DB->Query();
              $aandeel = $DB->lookupRecord();
              if($aandeel['aandeel'] > 100)
                $herIndex=true;

              $index['SpecifiekeIndex']=getSpecifiekeIndex($cPortefeuille,$this->rapportageDatum);

              if(isset($benchmarkVerdeling[$index['SpecifiekeIndex']]))
                $benchmarkVerdeling[$index['SpecifiekeIndex']] += $aandeel['aandeel'];
              else
                $benchmarkVerdeling[$index['SpecifiekeIndex']] = $aandeel['aandeel'];
              //echo $aandeel['datum']." ".$index['SpecifiekeIndex']." $cPortefeuille  += ".$aandeel['aandeel'].";<br>\n";
            }

            $benchmarkFonds=$benchmarkVerdeling;
            if($herIndex==true || array_sum($benchmarkVerdeling) <> 100)
            {
              $sum=0;
              foreach($benchmarkFonds as $fonds=>$percentage)
                $sum+=abs($percentage);
              foreach($benchmarkFonds as $fonds=>$percentage)
                $benchmarkFonds[$fonds]=abs($percentage)/$sum*100;
              // echo array_sum($benchmarkFonds);
            }
           // $fondsen=$benchmarkFonds;

            $fondsVerdeling=array();
            foreach($benchmarkFonds as $fondsDeel=>$aandeel)
            {

              $query = "SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fondsDeel'";
              $DB->SQL($query);
              $DB->Query();

              while ($data = $DB->nextRecord())
              {
                // echo  $data['percentage']."*$aandeel/100 <br>\n";
                // listarray($data);
                if(isset($fondsVerdeling[$data['fonds']]))
                  $fondsVerdeling[$data['fonds']] += $data['percentage']*$aandeel/100;
                else
                  $fondsVerdeling[$data['fonds']] = $data['percentage']*$aandeel/100;
              }
              if (count($fondsVerdeling) == 0)
                $fondsVerdeling[$fondsDeel] = $aandeel;
            }

            $fondsen=$benchmarkVerdeling;
            //listarray($fondsen);
          }
          else
          {
            $benchmark = getSpecifiekeIndex($portefeuille, $periode['stop']);
            $fondsen = getFondsverdeling($benchmark);
          }
         // echo  "$portefeuille ".$periode['start']." ".$periode['stop']."<br>\n"; ob_flush();
          //listarray($fondsen);
          $tmp = getFondsPerformance($fondsen, $periode['start'], $periode['stop']);

          $tmp=1+($tmp/100);
          $benchmarkRendement=(((1+($benchmarkRendement/100))*$tmp)-1)*100;

          //echo  "$portefeuille ".$benchmark." ".$periode['start']." -> ".$periode['stop']." $tmp -> $benchmarkRendement <br>\n";
        }

        }
  */
			}
			else
			{
				//$benchmarkRendement = getFondsPerformance($pRec['SpecifiekeIndex'], $vanafDatum, $totDatum);
				$benchmarkRendement=0;
				foreach($this->perioden as $periode)
				{
     

				 $maandPerf=$this->getFondsPerf($pRec['SpecifiekeIndex'],$periode['start'], $periode['stop']);
          $benchmarkRendement+=$maandPerf*100;
          
				//	$indexVerdeling=getFondsverdeling($pRec['SpecifiekeIndex']);
			//		$tmp = getFondsPerformance($indexVerdeling, $periode['start'], $periode['stop']);
			//		$tmp=1+($tmp/100);
			//		$benchmarkRendement=(((1+($benchmarkRendement/100))*$tmp)-1)*100;
					//  echo "$portefeuille ".$pRec['SpecifiekeIndex']." ".$periode['start']." -> ".$periode['stop']." $tmp -> $benchmarkRendement <br>\n";
				}
			}
			unset($tmp);
			$waarden['waardeEind']=$waardeEind;
			$waarden['waardeBegin']=$waardeBegin;
			$waarden['waardeMutatie']=$waardeMutatie;
			$waarden['stortingen']=$stortingen;
			$waarden['onttrekkingen']=$onttrekkingen;
			$waarden['resultaatVerslagperiode']=$resultaatVerslagperiode;
			$waarden['rendementProcent']=$rendementProcent;
			$waarden['rendementBenchmark']=$benchmarkRendement;

			$RapJaar = date("Y", db2jul($totDatum));
			$RapStartJaar = date("Y", db2jul($vanafDatum));
			$totaalOpbrengst += $ongerealiseerdeKoersResultaat;
			$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($portefeuille, $vanafDatum, $totDatum,$this->pdf->rapportageValuta,true,'Totaal',true);
			$totaalOpbrengst += $gerealiseerdeKoersResultaat['totaal'];
			$waarden['gerealiseerdeKoersResultaat']=$gerealiseerdeKoersResultaat['totaal'];
      $waarden['gerealiseerdeKoersResultaatFonds']=$gerealiseerdeKoersResultaat['fonds'];
      $waarden['gerealiseerdeKoersResultaatValuta']=$gerealiseerdeKoersResultaat['valuta'];

      
      $opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $totRapKoers;
			$totaalOpbrengst += $opgelopenRente;
			$waarden['opgelopenRente']=$opgelopenRente;

		}

		$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT Rekeningmutaties.Grootboekrekening, ".
				"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
				"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
				"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
				"WHERE ".
				"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
				"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
				"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
				"Rekeningmutaties.Verwerkt = '1' AND ".
				"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
				"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
				"Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' GROUP BY Rekeningmutaties.Grootboekrekening";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			$directeOpbrengsten=array('DIV','DIVB','RENOB','RENTE','DIVBE','ROER','RENME');
			while($opbrengst = $DB2->nextRecord())
			{
				if(in_array($gb['Grootboekrekening'],$directeOpbrengsten))
					$opbrengstenPerGrootboek['Directe opbrengsten'] +=  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				else
					$opbrengstenPerGrootboek['Indirecte opbrengsten'] +=  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}

		$waarden['opbrengstenPerGrootboek']=$opbrengstenPerGrootboek;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
			"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
			"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
			"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
			"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
			"Grootboekrekeningen.Kosten = '1' ".
			"GROUP BY Rekeningmutaties.Grootboekrekening ".
			"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();

		while($kosten = $DB->nextRecord())
		{
			// $kosten['Grootboekrekening']='kosten';
			$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'] ;//'Kosten';
			$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}
		foreach ($kostenPerGrootboek as $data)
		{
			$tmp[$data['Omschrijving']]=$data['Bedrag'];
		}

		$waarden['kostenPerGrootboek']=$tmp;
		$waarden['totaalKosten']=$totaalKosten;

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		$koersResulaatValutas = $waarden['resultaatVerslagperiode'] - ($totaalOpbrengst  -  $waarden['totaalKosten']);
		$totaalOpbrengst += $koersResulaatValutas;
		$waarden['kostenProcent']=$kostenProcent;
		$waarden['koersResulaatValutas']=$koersResulaatValutas;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		return $waarden;
	}


	function getCRMnaam($portefeuille)
	{
	  global $crm_velden;
		$db = new DB();
  
		if(count($crm_velden)==0)
    {
      $query = "desc CRM_naw";
      $db->SQL($query);
      $db->query();
      while ($data = $db->nextRecord('num'))
      {
        $crm_velden[] = $data[0];
      }
    }
    $extraVeld='';
    if(in_array('PortefeuilleNaam',$crm_velden))
    {
      $extraVeld = ',PortefeuilleNaam';
    }
    if(in_array('PortefeuilleBeheerder',$crm_velden))
    {
      $extraVeld .= ',PortefeuilleBeheerder';
    }
		$query="SELECT naam $extraVeld FROM CRM_naw WHERE portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$naam=$crmData['PortefeuilleNaam']."\n".$crmData['PortefeuilleBeheerder']."\n$portefeuille";


		return $naam;
	}
	
	function getFondsPerf($benchmark,$van,$tot)
  {
    $DB=new DB();
    $verdeling=getFondsverdeling($benchmark);
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
    return $totalPerf;
  }


	function addconsolidatie()
	{

		if(!isset($this->pdf->__appvar['consolidatie']))
		{
			$this->pdf->__appvar['consolidatie']=1;
			$this->pdf->portefeuilles=array($this->portefeuille);
		}
		//$this->pdf->doubleHeader=true;
	//	$this->pdf->addPage();
		$this->pdf->templateVars['PERFDPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['PERFDPaginas']=$this->pdf->rapport_titel;

		$startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
		$startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
		$eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

		//	$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		//  $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
		//  $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
		// listarray($this->pdf->portefeuilles);
		$fillArray=array(0,1);
		$subOnder=array('','');
		$volOnder=array('U','U');
		$subBoven=array('','');
    $crmHeader = array('','');
		$header=array("",vertaalTekst(" \nResultaat verslagperiode",$this->pdf->rapport_taal));
		$samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));

		$db=new DB();

	//	if(count($this->pdf->portefeuilles)<7)// && count($this->pdf->portefeuilles) > 1)
			$portefeuilles[]=$this->portefeuille;
	//	else
	//		$portefeuilles=array();

		foreach($this->pdf->portefeuilles as $portefeuille)
			$portefeuilles[]=$portefeuille;
		$longName=false;

		$perfWaarden=array();
		foreach($portefeuilles as $portefeuille)
		{
			$kop=$this->getCRMnaam($portefeuille);
			$query="
        SELECT Depotbanken.omschrijving,Portefeuilles.ClientVermogensbeheerder, CRM_naw.naam AS crmNaam
        FROM Depotbanken 
        JOIN Portefeuilles ON Portefeuilles.Depotbank=Depotbanken.Depotbank 
        LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.Portefeuille
        WHERE Portefeuilles.Portefeuille='".$portefeuille."'";

			$db->SQL($query);
			$depotbank=$db->lookupRecord();
			$volOnder[]='U';
			$volOnder[]='U';
			$subOnder[]='U';
			$subOnder[]='';
			$subBoven[]='T';
			$subBoven[]='';
			$fillArray[]=1;
			$fillArray[]=1;

			if($portefeuille==$this->portefeuille) {
				$header[]=vertaalTekst("Totaal",$this->pdf->rapport_taal);
        $crmHeader[] = '';
      } else {
        $crmHeader[] = substr($depotbank['crmNaam'], 0, 17);
				if($portefeuille<> $kop)
					$header[] = $kop;
				elseif($depotbank['ClientVermogensbeheerder']<>'')
					$header[] =  $depotbank['omschrijving']. "\n" .  $depotbank['ClientVermogensbeheerder'] ;
				else
					$header[] =  $depotbank['omschrijving']. "\n" .$portefeuille;
			}
			$header[]='';
      $crmHeader[] = '';
			$samenstelling[]='';
			$samenstelling[]='';
			if(!isset($this->perfWaarden[$portefeuille]))
				$this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);

			$perfWaarden[$portefeuille]=$this->perfWaarden[$portefeuille];
		}

		foreach($perfWaarden as $port=>$waarden)
		{
			foreach($waarden['opbrengstenPerGrootboek'] as $categorie=>$waarde)
				if(round($waarde,2)!=0.00)
					$opbrengstCategorien[$categorie]=$categorie;
			foreach($waarden['kostenPerGrootboek'] as $categorie=>$waarde)
				if(round($waarde,2)!=0.00)
					$kostenCategorien[$categorie]=$categorie;
		}

		$perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
		$waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
		$mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
		$stortingen=array("",vertaalTekst("Totaal stortingen verslagperiode",$this->pdf->rapport_taal));
		$onttrekking=array("",vertaalTekst("Totaal onttrekkingen verslagperiode",$this->pdf->rapport_taal));
		$resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
		$rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
		$rendementBenchmark=array("",vertaalTekst("Ontwikkeling benchmark",$this->pdf->rapport_taal));
		$rendementVerschil=array("",vertaalTekst("Verschil in rendement",$this->pdf->rapport_taal));
		$ongerealiseerd=array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
		$gerealiseerd=array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
    $gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde Fondsresultaten",$this->pdf->rapport_taal)); //
    $gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde Valutaresultaten",$this->pdf->rapport_taal)); //
		$valutaResultaat=array("",vertaalTekst("Koersresultaten valuta's liquiditeiten",$this->pdf->rapport_taal)); //
		$totaalResultaat=array("",vertaalTekst("Koersresultaat",$this->pdf->rapport_taal)); //
		$rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
		$totaalOpbrengst=array("","");//totaalOpbrengst
		$aandeel=array("",vertaalTekst("Percentage v/h belegd vermogen",$this->pdf->rapport_taal));//

		$totaalKosten=array("","");   //totaalKosten
		$totaal=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));   //totaalOpbrengst-totaalKosten

		$excelVelden=array('perbegin'=>$perbegin,
											 'waardeRapdatum'=>$waardeRapdatum,
											 'mutwaarde'=>$mutwaarde,
											 'stortingen'=>$stortingen,
											 'onttrekking'=>$onttrekking,
											 'resultaat'=>$resultaat,
											 'rendement'=>$rendement,
											 'rendementBenchmark'=>$rendementBenchmark,
											 'ongerealiseerd'=>$ongerealiseerd,
											 'gerealiseerd'=>$gerealiseerd,
											 'valutaResultaat'=>$valutaResultaat,
											 'totaalResultaat'=>$totaalResultaat,
											 'rente'=>$rente,
											 'totaalOpbrengst'=>$totaalOpbrengst,
											 'aandeel'=>$aandeel,
											 'totaalKosten'=>$totaalKosten,
											 'totaal'=>$totaal);
		foreach($perfWaarden as $portefeuille=>$waarden)
		{
			$excelVelden['perbegin'][]=round($perfWaarden[$portefeuille]['waardeBegin'],0);$excelVelden['perbegin'][]='';
			$perbegin[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeBegin'],0,true);
			$perbegin[]='';

			$excelVelden['waardeRapdatum'][]=round($perfWaarden[$portefeuille]['waardeEind'],0);$excelVelden['waardeRapdatum'][]='';
			$waardeRapdatum[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind'],0,true);
			$waardeRapdatum[]='';

			$excelVelden['mutwaarde'][]=round($perfWaarden[$portefeuille]['waardeMutatie'],0);$excelVelden['mutwaarde'][]='';
			$mutwaarde[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeMutatie'],0,true);
			$mutwaarde[]='';

			$excelVelden['stortingen'][]=round($perfWaarden[$portefeuille]['stortingen'],0);$excelVelden['stortingen'][]='';
			$stortingen[]=$this->formatGetal($perfWaarden[$portefeuille]['stortingen'],0);
			$stortingen[]='';

			$excelVelden['onttrekking'][]=round($perfWaarden[$portefeuille]['onttrekkingen']*-1,0);$excelVelden['onttrekking'][]='';
			$onttrekking[]=$this->formatGetal($perfWaarden[$portefeuille]['onttrekkingen']*-1,0);
			$onttrekking[]='';

			$excelVelden['resultaat'][]=round($perfWaarden[$portefeuille]['resultaatVerslagperiode'],0);$excelVelden['resultaat'][]='';
			$resultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['resultaatVerslagperiode'],0);
			$resultaat[]='';

			$excelVelden['rendement'][]=round($perfWaarden[$portefeuille]['rendementProcent'],2);$excelVelden['rendement'][]='%';
			$rendement[]=$this->formatGetal($perfWaarden[$portefeuille]['rendementProcent'],2);
			$rendement[]='%';

			$excelVelden['rendementBenchmark'][]=round($perfWaarden[$portefeuille]['rendementBenchmark'],2);$excelVelden['rendementBenchmark'][]='%';
			$rendementBenchmark[] = $this->formatGetal($perfWaarden[$portefeuille]['rendementBenchmark'], 2);
			$rendementBenchmark[] = '%';

			$rendementVerschil[]= $this->formatGetal($perfWaarden[$portefeuille]['rendementProcent']-$perfWaarden[$portefeuille]['rendementBenchmark'], 2);
			$rendementVerschil[]= '%';

			$excelVelden['ongerealiseerd'][]=round($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat'],0);$excelVelden['ongerealiseerd'][]='';
			$ongerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat'],0);
			$ongerealiseerd[]='';

			$excelVelden['gerealiseerd'][]=round($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat'],0);$excelVelden['gerealiseerd'][]='';
      $gerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat'],0);
      $gerealiseerd[]='';
      
      $gerealiseerdFonds[]=$this->formatGetal($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaatFonds'],0);
      $gerealiseerdFonds[]='';
      
      $gerealiseerdValuta[]=$this->formatGetal($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaatValuta'],0);
      $gerealiseerdValuta[]='';
      
			$excelVelden['valutaResultaat'][]=round($perfWaarden[$portefeuille]['koersResulaatValutas'],0);$excelVelden['valutaResultaat'][]='';
			$valutaResultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['koersResulaatValutas'],0);
			$valutaResultaat[]='';

			$excelVelden['totaalResultaat'][]=round($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat']+$perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat']+$perfWaarden[$portefeuille]['koersResulaatValutas'],0);$excelVelden['totaalResultaat'][]='';
			$totaalResultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat']+$perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat']+$perfWaarden[$portefeuille]['koersResulaatValutas'],0);
			$totaalResultaat[]='';

			$excelVelden['rente'][]=round($perfWaarden[$portefeuille]['opgelopenRente'],0);$excelVelden['rente'][]='';
			$rente[]=$this->formatGetal($perfWaarden[$portefeuille]['opgelopenRente'],0);
			$rente[]='';

			$excelVelden['totaalOpbrengst'][]=round($perfWaarden[$portefeuille]['totaalOpbrengst'],0);$excelVelden['totaalOpbrengst'][]='';
			$totaalOpbrengst[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst'],0);
			$totaalOpbrengst[]='';

			$excelVelden['totaalKosten'][]=round($perfWaarden[$portefeuille]['totaalKosten'],0);$excelVelden['totaalKosten'][]='';
			$totaalKosten[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalKosten'],0);
			$totaalKosten[]='';

			$excelVelden['totaal'][]=round($perfWaarden[$portefeuille]['totaalOpbrengst']-$perfWaarden[$portefeuille]['totaalKosten'],0);$excelVelden['totaal'][]='';
			$totaal[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst']-$perfWaarden[$portefeuille]['totaalKosten'],0);
			$totaal[]='';
			$excelVelden['aandeel'][]=round($perfWaarden[$portefeuille]['waardeEind']/$this->perfWaarden[$this->portefeuille]['waardeEind']*100,1);$excelVelden['aandeel'][]='';
			$aandeel[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind']/$this->perfWaarden[$this->portefeuille]['waardeEind']*100,1);
			$aandeel[]='%';

		}

		// if($longName==true && count($portefeuilles) < 8)
		$cols=7;
		//else
		//  $cols=9;

		$w=(297-2*8-60-(9*3))/$cols;
		$w2=4.5;
		$this->pdf->widthB = array(0,60,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

		// $this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,10);//$this->pdf->rapport_kop_fontsize
		//$this->pdf->fillCell=$fillArray;
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row($header);
		$this->pdf->excelData[]=$header;
    $this->pdf->row($crmHeader);

		$this->pdf->excelData[]=$excelVelden['perbegin'];
		$this->pdf->excelData[]=$excelVelden['waardeRapdatum'];
		$this->pdf->excelData[]=$excelVelden['mutwaarde'];
		$this->pdf->excelData[]=$excelVelden['stortingen'];
		$this->pdf->excelData[]=$excelVelden['onttrekking'];
		$this->pdf->excelData[]=$excelVelden['resultaat'];
		$this->pdf->excelData[]=$excelVelden['rendement'];
		$this->pdf->excelData[]=$excelVelden['rendementBenchmark'];
		$this->pdf->excelData[]=$excelVelden['aandeel'];

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->fillCell=array();
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
		$this->pdf->CellBorders = $subOnder;
		$this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
		$this->pdf->ln();
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->ln();
		unset( $this->pdf->CellBorders );
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
		$this->pdf->ln(-3);
		$this->pdf->row($rendementBenchmark);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
		$this->pdf->CellBorders = $volOnder;
		$this->pdf->ln(-3);
		$this->pdf->row($rendementVerschil);

		$this->pdf->ln(3);

		$this->pdf->row($aandeel);
		$this->pdf->ln(3);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array();
		//	$ypos = $this->pdf->GetY()-5;
		//	$this->pdf->SetY($ypos);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		//$this->pdf->fillCell=$fillArray;
		// $this->pdf->SetTextColor(255,255,255);
		$YSamenstelling=$this->pdf->GetY();
		//$this->pdf->row($samenstelling);//,"","","",""));
		//$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->fillCell=array();
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->excelData[]=array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"","");



		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		//$this->pdf->row($ongerealiseerd);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		//$this->pdf->row($gerealiseerd);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
		//	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
		//  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($totaalResultaat);
		$this->pdf->excelData[]=$excelVelden['totaalResultaat'];
		if(!in_array('Directe opbrengsten',$opbrengstCategorien))
		{
			$this->pdf->excelData[]=$excelVelden['rente'];
			$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
		}
		$keys=array();
		foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
			$keys[]=$key;

		foreach ($opbrengstCategorien as $categorie)
		{
			$tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
			$tmpXls=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
			foreach($perfWaarden as $port=>$waarden)
			{
				$tmp[]=$this->formatGetal($waarden['opbrengstenPerGrootboek'][$categorie],0);
				$tmp[]='';
				$tmpXls[]=round($waarden['opbrengstenPerGrootboek'][$categorie],0);
				$tmpXls[]='';
			}
			//if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
			$this->pdf->excelData[]=$tmpXls;
			if($categorie=='Directe opbrengsten')
			{
				$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
				$this->pdf->excelData[]=$excelVelden['rente'];
			}
		}

		$this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
		$this->pdf->excelData[]=$excelVelden['totaalOpbrengst'];
		$this->pdf->ln();
		$this->pdf->CellBorders = array();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->excelData[]=array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"","");
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		foreach ($kostenCategorien as $categorie)
		{

			$tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
			$tmpXls=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
			foreach($perfWaarden as $port=>$waarden)
			{
				$tmp[]=$this->formatGetal($waarden['kostenPerGrootboek'][$categorie],0);
				$tmp[]='';
				$tmpXls[]=round($waarden['kostenPerGrootboek'][$categorie],0);
				$tmpXls[]='';
			}
			//		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
			$this->pdf->excelData[]=$tmpXls;
		}
		$this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
		$this->pdf->excelData[]=$excelVelden['totaalKosten'];
		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
		$this->pdf->CellBorders = $volOnder;
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
		$actueleWaardePortefeuille = 0;
		$this->pdf->excelData[]=$excelVelden['totaal'];
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array();


		
	}



  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

          $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'FD','',array(245,245,245));

      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0)
        $maxVal = ceil(max($data));
      $minVal = floor(min($data));

      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.2;

      if ($maxVal <0)
       $maxVal=0;

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

      $horDiv = 10;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }

      if($numBars > 0)
        $this->pdf->NbVal=$numBars;


        $colors=array(array(108,31,128),array(234,105,11),array(0,52,121));

      $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
      $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      //$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $index=>$val)
      {

        $color=$colors[$index];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
      }


  }
}

?>