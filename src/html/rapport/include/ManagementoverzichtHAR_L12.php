<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/17 09:40:14 $
File Versie					: $Revision: 1.11 $

$Log: ManagementoverzichtHAR_L12.php,v $
Revision 1.11  2019/11/17 09:40:14  rvv
*** empty log message ***

Revision 1.10  2017/02/11 17:29:12  rvv
*** empty log message ***

Revision 1.9  2014/04/05 15:33:48  rvv
*** empty log message ***

Revision 1.8  2012/07/14 13:20:23  rvv
*** empty log message ***

Revision 1.7  2012/01/04 16:28:38  rvv
*** empty log message ***

Revision 1.6  2011/12/11 10:58:53  rvv
*** empty log message ***

Revision 1.5  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.4  2009/11/08 14:11:55  rvv
*** empty log message ***

Revision 1.3  2008/11/13 10:13:08  rvv
*** empty log message ***

Revision 1.2  2007/11/16 11:25:30  rvv
*** empty log message ***

Revision 1.1  2007/02/21 11:06:15  rvv
*** empty log message ***



*/

		global $__appvar;
		$this->pdf->__appvar = $this->__appvar;

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		$rapportageDatum[a] = jul2sql($this->selectData[datumVan]);
		$rapportageDatum[b] = jul2sql($this->selectData[datumTm]);
		// vul eerst de tijdelijketabel
		foreach($portefeuilles as $pdata)
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']);
			}

			$portefeuille = $pdata[Portefeuille];
			if(db2jul($rapportageDatum[a]) < db2jul($pdata[Startdatum]))
			{
				$startdatum = $pdata[Startdatum];
			}
			else
			{
				$startdatum = $rapportageDatum[a];
			}
			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			if($rapportMaand == 1 && $rapportDag == 1)
				$startjaar = true;
			else
				$startjaar = false;

			$einddatum = $rapportageDatum[b];

			$fondswaarden[a] =  berekenPortefeuilleWaardeQuick($portefeuille,  $startdatum, $startjaar);
			$fondswaarden[b] =  berekenPortefeuilleWaardeQuick($portefeuille,  $einddatum);

			vulTijdelijkeTabel($fondswaarden[a] ,$portefeuille,$startdatum);
			vulTijdelijkeTabel($fondswaarden[b] ,$portefeuille,$einddatum);

			// tel totaal op!
			$DB2 = new DB();
			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$grandtotaalWaarde += $totaalWaarde[totaal];
		}


					$this->pdf->excelData[] = array("",
															"risicoprofiel",
															"naam",
  														"soort overeenkomst",
															"rekening",
															"depotbank",
															"vermogen",
															"perf.",

															"aand.",
															"alter.",
															"obl.",
															"liq.");



		$this->pdf->SetFont("Verdana","",10);

		// nog een keer een loop over de portefeuilles!

		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			exit;
		}
		$tel = 0;

		$vorigeRisicoprofiel = "xx";

    foreach($portefeuilles as $pdata)
		{
			$tel ++;
			$tel2++;

			// add page per risicoprofiel
			if($pdata['Risicoprofiel'] <> $vorigeRisicoprofiel)
			{
				if($tel > 1)
				{
					$perf = $subtotaalPerf / $subtotaalWaarde;

					$this->pdf->ln();

					$this->pdf->Cell(95 , $this->pdf->rowHeight , "Subtotaal " , 0, 0, "R");
					$this->pdf->Cell(20 , $this->pdf->rowHeight , "" , 0, 0, "L");
					$this->pdf->Cell(20 , $this->pdf->rowHeight , "" , 0, 0, "L");
					$this->pdf->Cell(25 , $this->pdf->rowHeight , $this->formatGetal($subtotaalWaarde,2) , 0, 0, "R");
					$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($perf,2), 0, 1, "R");

					$subtotaalWaarde = 0;
					$subtotaalPerf = 0;
				}

				$this->pdf->risicoprofiel = $pdata['Risicoprofiel'];
				$this->pdf->AddPage();
				$tel = 1;

			}


			$portefeuille = $pdata[Portefeuille];

			if(db2jul($rapportageDatum[a]) < db2jul($pdata[Startdatum]))
			{
				$startdatum = $pdata[Startdatum];
			}
			else
			{
				$startdatum = $rapportageDatum[a];
			}

			$einddatum = $rapportageDatum[b];


			$DB2 = new DB();
			$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$totaalWaarde = $DB2->nextRecord();
			$totaalWaarde = $totaalWaarde[totaal];

			$queryTotaal = "SELECT  SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS totaal ".
							 " FROM TijdelijkeRapportage ".
							 " WHERE ".
							 " TijdelijkeRapportage.type = 'rente' AND ".
							 " TijdelijkeRapportage.rapportageDatum ='".$einddatum."' AND ".
							 " TijdelijkeRapportage.portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$renteTotaal = $DB2->nextRecord();
			$renteTotaal = $renteTotaal['totaal'];

			$queryTotaal = "SELECT CategorienPerHoofdcategorie.Hoofdcategorie, SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage ".
							 " LEFT JOIN CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$pdata[Vermogensbeheerder]."'".
							 "WHERE ".
							 " TijdelijkeRapportage.type = 'fondsen' AND ".
							 " TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND  ".
							 " TijdelijkeRapportage.rapportageDatum ='".$einddatum."' AND ".
							 " TijdelijkeRapportage.portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'].
							 " GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie ";
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$catWaarde = array();
			while($wrd = $DB2->nextRecord())
			{
				$catWaarde[$wrd['Hoofdcategorie']] = $wrd['totaal'];
			}

			// per categorie.
			$aandPercentage = $catWaarde['H-Aand'] / ($totaalWaarde/100);
			if ($this->pdf->userLayout == 12)
			  $ogPercentage = $catWaarde['H-Alter'] / ($totaalWaarde/100);
			else
			  $ogPercentage = $catWaarde['H-Og'] / ($totaalWaarde/100);
			$oblPercentage = ($catWaarde['H-Obl']+$renteTotaal) / ($totaalWaarde/100);

			$queryTotaal = "SELECT  SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS totaal ".
							 " FROM TijdelijkeRapportage ".
							 " WHERE ".
							 " TijdelijkeRapportage.type = 'rekening' AND ".
							 " TijdelijkeRapportage.rapportageDatum ='".$einddatum."' AND ".
							 " TijdelijkeRapportage.portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($queryTotaal,__FILE__,__LINE__);

			$DB2->SQL($queryTotaal);
			$DB2->Query();
			$liqTotaal = $DB2->nextRecord();
			$liqTotaal = $liqTotaal['totaal'];
			$liqPercentage = $liqTotaal / ($totaalWaarde/100);

			// verschil met risicoprofiel.
			$verschilPercentage = $pdata['Risicoprofiel'] - $aandPercentage;

			if($grandtotaalWaarde <> 0)
				$percentage = $totaalWaarde / ($grandtotaalWaarde/100);

			$performance 			  = performanceMeting($portefeuille, $startdatum, $einddatum,$pdata['PerformanceBerekening']);
			$performanceTotaal += ($totaalWaarde * $performance);

			// schrijf data !
			$this->pdf->Cell(10 , $this->pdf->rowHeight , $tel , 0, 0, "R");
			$this->pdf->Cell(60 , $this->pdf->rowHeight , rclip($pdata[Naam],30) , 0, 0, "L");
			$this->pdf->Cell(25 , $this->pdf->rowHeight , $pdata['SoortOvereenkomst'] , 0, 0, "L");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $pdata[Portefeuille] , 0, 0, "L");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $pdata[Depotbank] , 0, 0, "L");
			$this->pdf->Cell(25 , $this->pdf->rowHeight , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($performance,2), 0, 0, "R");
	//		$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($verschilPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($aandPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($ogPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($oblPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($liqPercentage,1), 0, 1, "R");

			$this->pdf->excelData[] = array($tel,
				                        trim($pdata['Risicoprofiel'].' '.$pdata['Risicoklasse']),
																$pdata[Naam],
																$pdata['SoortOvereenkomst'],
																$pdata[Portefeuille],
																$pdata[Depotbank],
																round($totaalWaarde,2),
																round($performance,2),
																round($aandPercentage,1),
																round($ogPercentage,1),
																round($oblPercentage,1),
																round($liqPercentage,1));
			$this->dbWaarden[]=array(
				        'Rapport'=>'Management',
				        'Client' => $pdata['Client'],
				        'Naam'=>$pdata['Naam'],
				        'Naam1'=>$pdata['Naam1'],
				        'Portefeuille'=>$pdata['Portefeuille'],
				        'Vermogensbeheerder'=>$pdata['Vermogensbeheerder'],
				        'totaalvermogen'=>round($totaalWaarde,2),
				        'inprocenttotaal'=>round($percentage,2),
				        'performance'=>round($performance,2),
				        'aand'=>round($aandPercentage,2),
				        'og'=>round($ogPercentage,2),
				        'obl'=>round($oblPercentage,2),
				        'liq'=>round($liqPercentage,2));

			//verwijderTijdelijkeTabel($portefeuille,$einddatum);
			verwijderTijdelijkeTabel($portefeuille,$startdatum);

			$subtotaalWaarde += $totaalWaarde;
			$subtotaalPerf += ($totaalWaarde * $performance);

			$vorigeRisicoprofiel = $pdata['Risicoprofiel'];
		}
		// subtotaal vermogensbeheerder & accountmanager

		$perf = $subtotaalPerf / $subtotaalWaarde;

		$this->pdf->ln();

		$this->pdf->Cell(95 , $this->pdf->rowHeight , "Subtotaal " , 0, 0, "R");
		$this->pdf->Cell(20 , $this->pdf->rowHeight , "" , 0, 0, "L");
		$this->pdf->Cell(20 , $this->pdf->rowHeight , "" , 0, 0, "L");
		$this->pdf->Cell(25 , $this->pdf->rowHeight , $this->formatGetal($subtotaalWaarde,2) , 0, 0, "R");
		$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($perf,2), 0, 1, "R");


		$this->pdf->SetFont("Verdana","b",10);

		if($tel > 0)
			$performanceTotaal = $performanceTotaal / $grandtotaalWaarde;

		$this->pdf->ln();

		$this->pdf->Line($this->pdf->marge + 125,$this->pdf->GetY()-1, $this->pdf->marge + 180,$this->pdf->GetY()-1);
		$this->pdf->Line($this->pdf->marge + 125,$this->pdf->GetY()+4, $this->pdf->marge + 180,$this->pdf->GetY()+4);

		// druk totaal af
		$this->pdf->Cell(95 , $this->pdf->rowHeight , "Totaal " , 0, 0, "R");
		$this->pdf->Cell(20 , $this->pdf->rowHeight , "" , 0, 0, "L");
		$this->pdf->Cell(20 , $this->pdf->rowHeight , "" , 0, 0, "L");
		$this->pdf->Cell(25 , $this->pdf->rowHeight , $this->formatGetal($grandtotaalWaarde,2) , 0, 0, "R");
		$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($performanceTotaal,2), 0, 1, "R");

		$this->pdf->SetFont("Verdana","",10);

		// vergelijking AEX
		$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".jul2sql($this->selectData[datumVan])."' AND Fonds = 'AEX'  ORDER BY Datum DESC LIMIT 1";
		$DB2->SQL($q);
		$DB2->Query();
		$koers1 = $DB2->LookupRecord();

		$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".jul2sql($this->selectData[datumTm])."' AND Fonds = 'AEX'  ORDER BY Datum DESC LIMIT 1";
		$DB2->SQL($q);
		$DB2->Query();
		$koers2 = $DB2->LookupRecord();

		$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );

		//$this->pdf->ln();
		//$this->pdf->Cell(200,4, "Performance AEX over zelfde periode ".$this->formatGetal($performance,2)." %", 0,1, "R");

		if($this->progressbar)
			$this->progressbar->hide();

?>