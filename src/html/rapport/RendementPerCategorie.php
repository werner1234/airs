<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.5 $

$Log: RendementPerCategorie.php,v $
Revision 1.5  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.4  2017/10/14 17:25:20  rvv
*** empty log message ***

Revision 1.3  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.2  2014/04/05 15:33:12  rvv
*** empty log message ***

Revision 1.1  2013/11/30 14:24:23  rvv
*** empty log message ***

Revision 1.1  2013/08/07 17:18:57  rvv
*** empty log message ***

Revision 1.1  2013/07/24 15:48:04  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");
include_once("rapport/rapportATTberekening.php");

class RendementPerCategorie_dummy
{
  
  function RendementPerCategorie_dummy($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    global $__appvar;
    $this->__appvar = $__appvar;
    $this->pdf = &$pdf;
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    
  }
}

class RendementPerCategorie
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RendementPerCategorie(  $selectData )
	{
	  global $USR;

		$this->selectData = $selectData;
		$this->pdf = new PDFOverzicht('L','mm');

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
    $this->pdf->userLayout=12;
		$this->pdf->rowHeight = 5;
		if(!isset($this->pdf->fonts['verdana']))
		{
			$this->pdf->AddFont('Verdana');
			$this->pdf->AddFont('Verdana','B','verdanab.php');
			$this->pdf->AddFont('Verdana','BI','verdanaib.php');
		}
		$this->pdf->rapport_type = "Rendementsverdeling";
		$this->pdf->title = $selectData['title'];
	

		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Verdana","",10);


		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];

		if($this->selectData['orderbyVermogensbeheerder'] == 1)
		{
			$this->orderby  = " Portefeuilles.Vermogensbeheerder ";
			if($this->selectData['orderbyAccountmanager'] == 1)
				$this->orderby  .= " , Portefeuilles.Accountmanager ";
		}
		else if($this->selectData['orderbyAccountmanager'] == 1)
				$this->orderby  = " Portefeuilles.Accountmanager ";
		else
		{
			$this->orderby  = " Clienten.Client ";
		}

    	$this->dbTable="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 20 ) NOT NULL ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Naam` VARCHAR( 50 ) NOT NULL ,
`Naam1` VARCHAR( 50 ) NOT NULL ,
`totaalvermogen` DOUBLE NOT NULL ,
`inprocenttotaal` DOUBLE NOT NULL ,
`performance` DOUBLE NOT NULL ,
`verschilPercentage` DOUBLE NOT NULL ,
`aand` DOUBLE NOT NULL ,
`og` DOUBLE NOT NULL ,
`obl` DOUBLE NOT NULL ,
`liq` DOUBLE NOT NULL ,
`add_date` datetime ,
PRIMARY KEY ( `id` ),
KEY `Portefeuille` (`Portefeuille`)
)";

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function writeRapport()
  {
    global $USR;

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
			$pro_multiplier = 100 / $records /2;
		}

		$rapportageDatum['a'] = jul2sql($this->selectData['datumVan']);
		$rapportageDatum['b'] = jul2sql($this->selectData['datumTm']);
		// vul eerst de tijdelijketabel
		foreach($portefeuilles as $pdata)
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']);
			}

			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
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

			$fondswaarden[a] =  berekenPortefeuilleWaarde($portefeuille,  $startdatum, $startjaar);
			$fondswaarden[b] =  berekenPortefeuilleWaarde($portefeuille,  $einddatum);

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
			$grandtotaalWaarde += $totaalWaarde['totaal'];
		}




		$this->csvData[] = array("",
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
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']);
			}

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


			$portefeuille = $pdata['Portefeuille'];

			if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
			}
			else
			{
				$startdatum = $rapportageDatum['a'];
			}

			$einddatum = $rapportageDatum['b'];


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
			$totaalWaarde = $totaalWaarde['totaal'];
			// verschil met risicoprofiel.


			if($grandtotaalWaarde <> 0)
				$percentage = $totaalWaarde / ($grandtotaalWaarde/100);



	  if($pdata['PerformanceBerekening'] == 6)
	    $periodeBlok = 'kwartaal';
	  else
	    $periodeBlok = 'maand';
      
     
	  

      
      if(is_file("rapport/include/rapportATTberekening_L".$pdata['Layout'].".php"))
      {
        if($pdata['RapportageValuta']=='')
          $pdata['RapportageValuta']='EUR';
        $pdf = new PDFRapport('L', 'mm');
        loadLayoutSettings($pdf, $portefeuille);
        $dummyRaport = new RendementPerCategorie_dummy($pdf, $portefeuille, $startdatum, $einddatum);
        include_once("rapport/include/rapportATTberekening_L".$pdata['Layout'].".php");
        $this->berekening = new rapportATTberekening_L12($dummyRaport);//rapportATTberekening_L12($this->pdata);
        $tmp = $this->berekening->bereken($startdatum, $einddatum, 'attributie', $pdata['RapportageValuta']);
        
        $rendementen=array();
        foreach($tmp as $categorie=>$waarden)
        {
          $rendementen[$categorie]=$waarden['procent'];
        }
        foreach($rendementen as $categorie=>$rendement)
        {
          $this->waarden['rapportagePeriode']['performance'][$categorie]=$rendement;
          if($categorie=='totaal')
            $this->waarden['rapportagePeriode']['performance']['Totaal']=$rendement;
        }
      }
      else
      {
        $this->berekening = new rapportATTberekening($portefeuille);
        $this->berekening->getAttributieCategorien();
        $this->berekening->pdata['pdf'] = true;
        $this->berekening->attributiePerformance($portefeuille, $startdatum, $einddatum, 'rapportagePeriode', $pdata['RapportageValuta'], $periodeBlok);
        $this->waarden['rapportagePeriode'] = $this->berekening->performance['rapportagePeriode'];
      }
      
    $performance 			  = $this->waarden['rapportagePeriode']['performance']['Totaal'];//performanceMeting($portefeuille, $startdatum, $einddatum,$pdata['PerformanceBerekening']);
		$performanceTotaal += ($totaalWaarde * $performance);


			// schrijf data !
			$this->pdf->Cell(10 , $this->pdf->rowHeight , $tel , 0, 0, "R");
			$this->pdf->Cell(60 , $this->pdf->rowHeight , rclip($pdata['Naam'],30) , 0, 0, "L");
			$this->pdf->Cell(25 , $this->pdf->rowHeight , $pdata['SoortOvereenkomst'] , 0, 0, "L");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $pdata['Portefeuille'] , 0, 0, "L");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $pdata['Depotbank'] , 0, 0, "L");
			$this->pdf->Cell(25 , $this->pdf->rowHeight , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($performance,2), 0, 0, "R");
	//		$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($verschilPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($this->waarden['rapportagePeriode']['performance']['Aandelen'],2), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($this->waarden['rapportagePeriode']['performance']['Alternatieven'],2), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($this->waarden['rapportagePeriode']['performance']['Vastrentend'],2), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($this->waarden['rapportagePeriode']['performance']['Liquiditeiten'],2), 0, 1, "R");

			$this->pdf->excelData[] = array($tel,
																$pdata['Risicoklasse'],
																$pdata['Naam'],
																$pdata['SoortOvereenkomst'],
																$pdata['Portefeuille'],
																$pdata['Depotbank'],
																round($totaalWaarde,2),
																round($performance,2),
																round($this->waarden['rapportagePeriode']['performance']['Aandelen'],2),
																round($this->waarden['rapportagePeriode']['performance']['Alternatieven'],2),
																round($this->waarden['rapportagePeriode']['performance']['Vastrentend'],2),
																round($this->waarden['rapportagePeriode']['performance']['Liquiditeiten'],2));


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