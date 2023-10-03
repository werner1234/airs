<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.3 $

$Log: Rendementsverdeling.php,v $
Revision 1.3  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.2  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.1  2013/07/24 15:48:04  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");

class Rendementsverdeling
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Rendementsverdeling(  $selectData )
	{
	  global $USR;

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rowHeight = 5;
		if(!isset($this->pdf->fonts['verdana']))
		{
			$this->pdf->AddFont('Verdana');
			$this->pdf->AddFont('Verdana','B','verdanab.php');
			$this->pdf->AddFont('Verdana','BI','verdanaib.php');
		}
		$this->pdf->rapport_type = "Rendementsverdeling";
		$this->pdf->title = $selectData['title'];
		$this->pdf->userLayout = $selectData['userLayout'];

		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Verdana","",10);


		$this->pdf->vandatum = $this->selectData[datumVan];
		$this->pdf->tmdatum = $this->selectData[datumTm];

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

	if ($this->pdf->userLayout == 12 && file_exists("./rapport/include/ManagementoverzichtHAR_L".$this->pdf->userLayout.".php"))
	{
    	include("./rapport/include/ManagementoverzichtHAR_L".$this->pdf->userLayout.".php");



	}
	else
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
															"client",
															"naam",
															"rekening",
															"depotbank",
															"vermogen",
															"perf.",
															"afw. R.",
															"aand.",
															"o.g.",
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

			$performance 			  = performanceMeting($portefeuille, $startdatum, $einddatum, $pdata['PerformanceBerekening']);
			$performanceTotaal += ($totaalWaarde * $performance);

			// schrijf data !
			$this->pdf->Cell(10 , $this->pdf->rowHeight , $tel , 0, 0, "R");
			$this->pdf->Cell(25 , $this->pdf->rowHeight , $pdata[Client] , 0, 0, "L");
			$this->pdf->Cell(60 , $this->pdf->rowHeight , rclip($pdata[Naam],30) , 0, 0, "L");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $pdata[Portefeuille] , 0, 0, "L");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $pdata[Depotbank] , 0, 0, "L");
			$this->pdf->Cell(25 , $this->pdf->rowHeight , $this->formatGetal($totaalWaarde,2) , 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($performance,2), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($verschilPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($aandPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($ogPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($oblPercentage,1), 0, 0, "R");
			$this->pdf->Cell(20 , $this->pdf->rowHeight , $this->formatGetal($liqPercentage,1), 0, 1, "R");

			$this->pdf->excelData[] = array($tel,
																$pdata[Risicoprofiel],
																$pdata[Client],
																$pdata[Naam],
																$pdata[Portefeuille],
																$pdata[Depotbank],
																round($totaalWaarde,2),
																round($performance,2),
																round($verschilPercentage,1),
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
				        'verschilPercentage'=>round($verschilPercentage,2),
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
	}
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

	function OutputDatabase()
	{
	  global $USR;
	  $db=new DB();
	  $table="reportbuilder_$USR";
	  $query="SHOW TABLES like '$table'";
	  if($db->QRecords($query) > 0)
	  {
	    $db->SQL("DROP table $table");
	    $db->Query();
	  }

    if($this->dbTable)
    {
      $db->SQL($this->dbTable);
	    $db->Query();
	    $query="show variables like 'character_set_database'";
      $db->SQL($query);
      $db->Query();
      $charset=$db->lookupRecord();
      $charset=$charset['Value'];
      $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
      $db->SQL($query);
      $db->Query();
    }

    if(is_array($this->dbWaarden))
    {
      foreach ($this->dbWaarden as $rege=>$waarden)
      {
        $query="INSERT INTO $table SET add_date=now() ";
        //listarray($waarden);
        foreach ($waarden as $key=>$value)
        {
          $query.=",$key='".addslashes($value)."' ";
        }
        $db->SQL($query);
	      $db->Query();
      }
    }

	}
}
?>