<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
 		File Versie					: $Revision: 1.6 $

 		$Log: ModelWaardecontrole.php,v $
 		Revision 1.6  2019/11/16 17:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/08/28 16:02:00  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2011/04/30 16:27:12  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2009/03/25 17:47:01  rvv
 		*** empty log message ***

 		Revision 1.2  2009/03/14 13:24:27  rvv
 		*** empty log message ***

 		Revision 1.1  2007/09/21 13:26:32  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once("../classes/portefeuilleSelectieClass.php");

class ModelWaardeControle
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function ModelWaardeControle(  $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "managementoverzicht";
		$this->pdf->title = "Model controle";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];

		$this->pdf->selectData = $this->selectData;

		$this->orderby = " Client ";
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
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
		$this->pdf->AddPage();

		$this->pdf->SetFont("Times","bu",10);

		$this->pdf->Cell(185 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(65 , 4 , "Totaal vermogen in EURO" , 0,1, "L");

		$this->pdf->SetFont("Times","b",10);

		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(35 , 4 , "Client" , 0, 0, "L");
		$this->pdf->Cell(100 , 4 , "Naam" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "Portefeuille" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "Depotbank" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , "Absoluut" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "in %", 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "Performance", 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "Model %", 0, 1, "R");

		$this->pdf->excelData[] = array("",
															"Client",
															"Naam",
															"Portefeuille",
                              "Accountmanager",
                              'Soort overeenkomst',
                              'Risicoprofiel',
															"Depotbank",
															"Absoluut",
															"in %",
															"Performance",
															"Model %");

		$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(), $this->pdf->marge + 270,$this->pdf->GetY());

		$this->pdf->SetFont("Times","",10);

		//verwijderTijdelijkeTabel("m".$this->selectData['modelcontrole_portefeuille'],$einddatum);
		$portefeuilleData = berekenPortefeuilleWaardeQuick($this->selectData['modelcontrole_portefeuille'], $einddatum);
		vulTijdelijkeTabel($portefeuilleData,"m".$this->selectData['modelcontrole_portefeuille'],$einddatum);

		// bereken totaal waarde model
		$query = "SELECT IFNULL(SUM(actuelePortefeuilleWaardeEuro),0) AS totaal FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '"."m".$this->selectData['modelcontrole_portefeuille']."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB3 = new DB();
		$DB3->SQL($query);
		$DB3->Query();
		$modelwaarde = $DB3->nextRecord();
		$modelTotaal = $modelwaarde['totaal'];


		$tel = 0;
		foreach($portefeuilles as $pdata)
		{
		  $crmNaam=getCrmNaam($pdata['Portefeuille']);
      if($crmNaam)
      {
        $pdata['Naam'] = $crmNaam['naam'];
        $pdata['Naam1'] = $crmNaam['naam1'];
      }

			$tel ++;
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

			if($totaalWaarde == "")
		    $totaalWaarde = 0;

			if($grandtotaalWaarde <> 0)
				$percentage = $totaalWaarde / ($grandtotaalWaarde/100);

			$performance 			  = performanceMeting($portefeuille, $startdatum, $einddatum,  $pdata['PerformanceBerekening']);
			$performanceTotaal += $totaalWaarde * $performance;

			if($this->selectData["modelcontrole_rapport"] == "vastbedrag")
			{
				$portefTotaal = $this->selectData["modelcontrole_vastbedrag"];
			}

			if($this->selectData[modelcontrole_percentage] > 0)
			{
				$afwijking = " HAVING ABS(afwijking) > ".$this->selectData[modelcontrole_percentage]." ";
			}

			if($this->selectData[modelcontrole_uitvoer] == "afwijkingen")
			{
				$afwijking = " HAVING afwijking <> 0 ";
			}



$factor = $totaalWaarde/$modelTotaal;

			$query = "SELECT
      IFNULL(portef.actuelePortefeuilleWaardeEuro,0) as portefeuilleWaarde,
			(
			 IFNULL((IFNULL(model.actuelePortefeuilleWaardeEuro,0) / ".$modelTotaal." * 100),0) -
			 IFNULL((IFNULL(portef.actuelePortefeuilleWaardeEuro,0) / ".$totaalWaarde." * 100),0)
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid
			FROM TijdelijkeRapportage
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds AND model.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\" AND model.type = 'fondsen' AND model.rapportageDatum = '".$einddatum."'
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds AND portef.portefeuille = \"".$portefeuille."\" AND portef.type = 'fondsen'  AND portef.rapportageDatum = '".$einddatum."'
			WHERE
			TijdelijkeRapportage.type = 'fondsen' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.fonds ".$afwijking."
			ORDER BY afwijking DESC ";

//echo "<br>\n $query <br>\n";
			$waardeInModel =0;

			$DB2->SQL($query);
			$DB2->Query();
			while($data = $DB2->nextRecord())
			{
			 $waardeInModel += $data['portefeuilleWaarde'];
			}

//	echo "$waardeInModel voor liquide ";
/*
					$query = "SELECT
			(IFNULL(model.actuelePortefeuilleWaardeEuro,0) / ".$modelTotaal." * 100) AS percentageModel,

			(
			 IFNULL((IFNULL(model.actuelePortefeuilleWaardeEuro,0) / ".$modelTotaal." * 100),0) -
			 IFNULL((IFNULL(portef.actuelePortefeuilleWaardeEuro,0) / ".$totaalWaarde." * 100),0)
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid
			FROM TijdelijkeRapportage
			LEFT JOIN TijdelijkeRapportage AS model ON model.fondsOmschrijving = TijdelijkeRapportage.fondsOmschrijving AND model.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\" AND model.type = 'rekening' AND model.rapportageDatum = '".$einddatum."'
			LEFT JOIN TijdelijkeRapportage AS portef ON portef.fondsOmschrijving = TijdelijkeRapportage.fondsOmschrijving AND portef.portefeuille = \"".$portefeuille."\" AND portef.type = 'rekening'  AND portef.rapportageDatum = '".$einddatum."'
			WHERE
			TijdelijkeRapportage.type = 'rekening' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"0\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.fondsOmschrijving ".$afwijking."
			ORDER BY afwijking DESC ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
//echo $query; exit;
			while($data = $DB2->nextRecord())
			{
			 $waardeInModel += $data['portefeuillewaarde'];
			}
*/



			$overeenkomst = $waardeInModel / $totaalWaarde *100;

//			echo "$overeenkomst = $waardeInModel / $totaalWaarde *100  ";
//			echo "overeenkomst ".$overeenkomst." <- \n <br>" ;

			// schrijf data !
			$this->pdf->Cell(10 , 4 , $tel , 0, 0, "R");
			$this->pdf->Cell(35 , 4 , $pdata[Client] , 0, 0, "L");
			$this->pdf->Cell(100, 4 , $pdata[Naam] , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , $pdata[Portefeuille] , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , $pdata[Depotbank] , 0, 0, "L");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($percentage,2), 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($performance,2), 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($overeenkomst,2), 0, 1, "R");

			$this->pdf->excelData[] = array($tel,
																$pdata['Client'],
																$pdata['Naam'],
																$pdata['Portefeuille'],
                                $pdata['Accountmanager'],
                                $pdata['SoortOvereenkomst'], 
                                $pdata['Risicoklasse'],
																$pdata['Depotbank'],
																round($totaalWaarde,2),
																round($percentage,2),
																round($performance,2),
																round($overeenkomst,2));

			//verwijderTijdelijkeTabel($portefeuille,$einddatum);
			verwijderTijdelijkeTabel($portefeuille,$startdatum);
		}

    
		// subtotaal vermogensbeheerder & accountmanager




		$this->pdf->SetFont("Times","b",10);

		if($tel > 0)
			$performanceTotaal = $performanceTotaal / $grandtotaalWaarde;

		$this->pdf->ln();

		$this->pdf->Line($this->pdf->marge + 187,$this->pdf->GetY(), $this->pdf->marge + 210,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 212,$this->pdf->GetY(), $this->pdf->marge + 230,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 232,$this->pdf->GetY(), $this->pdf->marge + 250,$this->pdf->GetY());

		// druk totaal af
		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(35 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(120 , 4 , "Totaal" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , $this->formatGetal($grandtotaalWaarde,2) , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal(100,2), 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($performanceTotaal,2), 0, 1, "R");

		$this->pdf->SetFont("Times","",10);
		verwijderTijdelijkeTabel("m".$this->selectData['modelcontrole_portefeuille'],$einddatum);
		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$excelData = generateCSV($this->pdf->excelData);
			fwrite($fp,$excelData);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}
}
?>