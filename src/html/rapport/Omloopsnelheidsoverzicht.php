<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.4 $

$Log: Omloopsnelheidsoverzicht.php,v $
Revision 1.4  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.3  2015/04/07 05:45:00  rvv
*** empty log message ***

Revision 1.2  2015/04/06 19:59:33  rvv
*** empty log message ***

Revision 1.1  2015/04/04 15:14:38  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");

class Omloopsnelheidsoverzicht
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Omloopsnelheidsoverzicht(  $selectData )
	{
    global $USR;
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "managementoverzicht";
    $this->pdf->title = "Omloopsnelheidsoverzicht";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
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

		$query="SELECT Grootboekrekeningen.Grootboekrekening FROM Grootboekrekeningen";
		$db=new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		  $grootboeken[]=$data['Grootboekrekening'];

		$grootboekDb="`".implode("` DOUBLE NOT NULL,\n`",$grootboeken)."` DOUBLE NOT NULL,";

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
`resultaat` DOUBLE NOT NULL ,
`rendement` DOUBLE NOT NULL ,
`AFMstd` DOUBLE NOT NULL ,
$grootboekDb
`liquiditeiten` DOUBLE NOT NULL ,
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
				logScherm("Portefeuille: ".$pdata['Portefeuille']." (Vullen tijdelijke rapportage)");
			}

			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata[Startdatum];
			}
			else
			{
				$startdatum = $rapportageDatum['a'];
			}
			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			if($rapportMaand == 1 && $rapportDag == 1)
				$startjaar = true;
			else
				$startjaar = false;

			$einddatum = $rapportageDatum[b];

			$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,  $startdatum, $startjaar);
			$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille,  $einddatum);

			vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startdatum);
			vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$einddatum);

			// tel totaal op!
			$DB2 = new DB();
		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			  				 "FROM TijdelijkeRapportage WHERE ".
				  			 " rapportageDatum ='".$startdatum."' AND ".
					  		 " portefeuille = '".$portefeuille."' "
						  	 .$__appvar['TijdelijkeRapportageMaakUniek'];
		  	debugSpecial($query,__FILE__,__LINE__);

		  	$DB2->SQL($query);
		  	$DB2->Query();
		  	$totaalWaarde= $DB2->nextRecord();      
      $grandtotaalWaarde += $totaalWaarde['totaal'];
      
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
     // $portefeuilles[]
		}
    $grandtotaalWaarde=$grandtotaalWaarde/2;
		$this->pdf->AddPage();

		$this->pdf->SetFont("Times","bu",10);

	//	$this->pdf->Cell(185 , 4 , "" , 0, 0, "L");
//		$this->pdf->Cell(65 , 4 , "Totaal vermogen in EURO" , 0,1, "L");

		$this->pdf->SetFont("Times","b",10);

		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(35 , 4 , "Client" , 0, 0, "L");
		$this->pdf->Cell(100 , 4 , "Naam" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "Portefeuille" , 0, 0, "L");
		$this->pdf->Cell(20 , 4 , "Depotbank" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , "Gem.Vermogen" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "Performance", 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "Omzet", 0, 0, "R");
		$this->pdf->Cell(25 , 4 , "Omzet snelheid", 0, 1, "R");


	  	$this->pdf->excelData[] = array("",
		  													"Client",
			  												"Naam",
                                "Portefeuille",
                                "Accountmanager",
                                "Depotbank",
                                "Risicoprofiel",
                                "SoortOvereenkomst",
                                'Beginvermogen',
                                'Eindvermogen',
															"Gem.Vermogen",
															"Performance",
															"Omzet",
                              "Omzet snelheid",
                              "AFM-#");
 
		$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());

		$this->pdf->SetFont("Times","",10);

		$tel = 0;
		foreach($portefeuilles as $pdata)
		{
		  if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']." (Gegevens ophalen)");
			}

			$tel ++;
			$portefeuille = $pdata['Portefeuille'];

			if(db2jul($rapportageDatum[a]) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
			}
			else
			{
				$startdatum = $rapportageDatum['a'];
			}

			$einddatum = $rapportageDatum['b'];

			if($pdata['Vermogensbeheerder'] <> $vorigeVermogensbeheerder && $this->selectData['orderbyVermogensbeheerder'] == 1)
			{
					if($tel > 1)
					{
						if($grandtotaalWaarde <> 0)
							$percentage = $vermogensbeheerderTotaal / ($grandtotaalWaarde/100);

						$perf = $vermogensbeheerderPerformanceTotaal / $vermogensbeheerderTotaal;

						$this->pdf->ln();
						$this->pdf->SetFont("Times","b",10);
						$this->pdf->Cell(165 , 4 , "Subtotaal ".$vorigeVermogensbeheerder , 0, 0, "R");
						$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
						$this->pdf->Cell(25 , 4 , $this->formatGetal($vermogensbeheerderTotaal,2) , 0, 0, "R");
						$this->pdf->Cell(20 , 4 , $this->formatGetal($percentage,2), 0, 0, "R");
						$this->pdf->Cell(20 , 4 , $this->formatGetal($perf,2), 0, 1, "R");

					}

					$this->pdf->ln();
					$this->pdf->SetFont("Times","bi",10);
					$this->pdf->Cell(30 , 4 , "Vermogensbeheerder ".$pdata['Vermogensbeheerder'] , 0, 1, "L");
					$this->pdf->ln();

					$this->pdf->excelData[] = array();
					$this->pdf->excelData[] = array("",
															"Vermogensbeheerder ".$pdata['Vermogensbeheerder']);
					$this->pdf->excelData[] = array();

					$this->pdf->SetFont("Times","",10);

					$vermogensbeheerderTotaal = 0;
					$vermogensbeheerderPerformanceTotaal = 0;
			}


			if($pdata['Accountmanager'] <> $vorigeAccountmanager && $this->selectData['orderbyAccountmanager'] == 1)
			{
					if($tel > 1)
					{
						if($grandtotaalWaarde <> 0)
							$percentage = $accountmanagerTotaal / ($grandtotaalWaarde/100);

						$perf = $accountPerformanceTotaal / $accountmanagerTotaal;

						$this->pdf->ln();
						$this->pdf->SetFont("Times","b",10);
						$this->pdf->Cell(165 , 4 , "Subtotaal ".$vorigeAccountmanager , 0, 0, "R");
						$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
						$this->pdf->Cell(25 , 4 , $this->formatGetal($accountmanagerTotaal,2) , 0, 0, "R");
						$this->pdf->Cell(20 , 4 , $this->formatGetal($percentage,2), 0, 0, "R");
						$this->pdf->Cell(20 , 4 , $this->formatGetal($perf,2), 0, 1, "R");
					}

					$this->pdf->ln();
					$this->pdf->SetFont("Times","bi",10);
					$this->pdf->Cell(30 , 4 , "Accountmanager ".$pdata['Accountmanager'] , 0, 1, "L");
					$this->pdf->ln();
					$this->pdf->excelData[] = array();
					$this->pdf->excelData[] = array("",
															"Accountmanager ".$pdata['Accountmanager']);
					$this->pdf->excelData[] = array();

					$this->pdf->SetFont("Times","",10);

					$accountmanagerTotaal = 0;
					$accountPerformanceTotaal = 0;
			}


			$DB2 = new DB();

		  	// haal beginwaarde op
		  	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			  				 "FROM TijdelijkeRapportage WHERE ".
				  			 " rapportageDatum ='".$startdatum."' AND ".
					  		 " portefeuille = '".$portefeuille."' "
						  	 .$__appvar['TijdelijkeRapportageMaakUniek'];
		  	debugSpecial($query,__FILE__,__LINE__);

		  	$DB2->SQL($query);
		  	$DB2->Query();
		  	$totaalWaarde= $DB2->nextRecord();
		  	$this->extraVelden['beginvermogen'] = $totaalWaarde['totaal'];


		  	$queryTotaal = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			  				 "FROM TijdelijkeRapportage WHERE ".
				  			 " rapportageDatum ='".$einddatum."' AND ".
					  		 " portefeuille = '".$portefeuille."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
			  debugSpecial($queryTotaal,__FILE__,__LINE__);
  			$DB2->SQL($queryTotaal);
	  		$DB2->Query();
		  	$totaalWaarde = $DB2->nextRecord();
			  $this->extraVelden['eindvermogen'] = $totaalWaarde['totaal'];
		  	$this->extraVelden['gemiddelde'] = ($this->extraVelden['eindvermogen'] + $this->extraVelden['beginvermogen'])/2;
		
		  	$query = "SELECT
SUM(abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)+abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)) as omzet
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE 
Rekeningen.Portefeuille='$portefeuille' AND Rekeningmutaties.Boekdatum > '$startdatum' AND Rekeningmutaties.Boekdatum <= '$einddatum'
AND Rekeningmutaties.Grootboekrekening='FONDS' AND
Rekeningmutaties.Transactietype IN('A','A/O','A/S','V','V/O','V/S') ";

			  debugSpecial($query,__FILE__,__LINE__);
			  $DB2->SQL($query);
			  $DB2->Query();
			  $totaal = $DB2->nextRecord();
  			$this->extraVelden['omzet'] = $totaal['omzet'];
        $this->extraVelden['omzetsnelheid'] = $totaal['omzet']/$this->extraVelden['gemiddelde']*100;

      if($pdata['RapportageValuta'] =='')
        $pdata['RapportageValuta']='EUR';
			$performance 			  = performanceMeting($portefeuille, $startdatum, $einddatum, $pdata['PerformanceBerekening'], $pdata['RapportageValuta']);
			$performanceTotaal += $this->extraVelden['eindvermogen'] * $performance;
      $omzetTotaal += $this->extraVelden['omzet'];

      $afm=AFMstd($portefeuille,$einddatum,false);

			// schrijf data !
			$this->pdf->Cell(10 , 4 , $tel , 0, 0, "R");
			$this->pdf->Cell(35 , 4 , $pdata['Client'] , 0, 0, "L");
			$this->pdf->Cell(100, 4 , $pdata['Naam'] , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , $pdata['Portefeuille'] , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , $pdata['Depotbank'] , 0, 0, "L");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($this->extraVelden['gemiddelde'],2) , 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($performance,2), 0, 0, "R");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($this->extraVelden['omzet'],2), 0, 0, "R");
			$this->pdf->Cell(20 , 4 , $this->formatGetal($this->extraVelden['omzetsnelheid'],2), 0, 1, "R");
      
	  	$this->pdf->excelData[] = array($tel,
																$pdata['Client'],
																$pdata['Naam'],
																$pdata['Portefeuille'],
                                $pdata['Accountmanager'],
																$pdata['Depotbank'],
                                $pdata['Risicoklasse'],
                                $pdata['SoortOvereenkomst'],
                                round($this->extraVelden['beginvermogen'],2),
																round($this->extraVelden['eindvermogen'],2),
                                round($this->extraVelden['gemiddelde'],2),
																round($performance,2),
																round($this->extraVelden['omzet'],2),
																round($this->extraVelden['omzetsnelheid'],2),
                                round($afm['std'],2));



			verwijderTijdelijkeTabel($portefeuille,$startdatum);

			$accountmanagerTotaal += $this->extraVelden['gemiddelde'];
      $accountmanagerOmzet += $this->extraVelden['omzet'];
			$accountPerformanceTotaal += $this->extraVelden['gemiddelde'] * $performance;
			$vorigeAccountmanager = $pdata['Accountmanager'];

			$vermogensbeheerderTotaal += $this->extraVelden['gemiddelde'];
      $vermogensbeheerderOmzet += $this->extraVelden['omzet'];
			$vermogensbeheerderPerformanceTotaal += $this->extraVelden['gemiddelde'] * $performance;
			$vorigeVermogensbeheerder = $pdata['Vermogensbeheerder'];
		}


		// subtotaal vermogensbeheerder & accountmanager

		if($vorigeAccountmanager <> "" && $this->selectData['orderbyAccountmanager'] == 1)
		{
				if($tel > 0)
				{
					if($grandtotaalWaarde <> 0)
						$percentage = $accountmanagerTotaal / ($grandtotaalWaarde/100);

					$perf = $accountPerformanceTotaal / $accountmanagerTotaal;

					$this->pdf->ln();
					$this->pdf->SetFont("Times","b",10);
					$this->pdf->Cell(165 , 4 , "Subtotaal ".$vorigeAccountmanager , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
					$this->pdf->Cell(25 , 4 , $this->formatGetal($accountmanagerTotaal,2) , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($perf,2), 0, 0, "R");
          $this->pdf->Cell(25 , 4 , $this->formatGetal($accountmanagerOmzet,2), 0, 0, "R");
          $this->pdf->Cell(20 , 4 , $this->formatGetal($accountmanagerOmzet/$accountmanagerTotaal*100,2), 0, 1, "R");
				}

				$this->pdf->ln();
				$this->pdf->SetFont("Times","",10);
		}

		if($vorigeVermogensbeheerder <> "" && $this->selectData['orderbyVermogensbeheerder'] == 1)
		{
				if($tel > 0)
				{
					if($grandtotaalWaarde <> 0)
						$percentage = $vermogensbeheerderTotaal / ($grandtotaalWaarde/100);

					$perf = $vermogensbeheerderPerformanceTotaal / $vermogensbeheerderTotaal;

					$this->pdf->ln();
					$this->pdf->SetFont("Times","b",10);
					$this->pdf->Cell(165 , 4 , "Subtotaal ".$vorigeVermogensbeheerder , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
					$this->pdf->Cell(25 , 4 , $this->formatGetal($vermogensbeheerderTotaal,2) , 0, 0, "R");
					$this->pdf->Cell(20 , 4 , $this->formatGetal($perf,2), 0, 0, "R");
          $this->pdf->Cell(25 , 4 , $this->formatGetal($vermogensbeheerderOmzet,2), 0, 0, "R");
          $this->pdf->Cell(20 , 4 , $this->formatGetal($vermogensbeheerderOmzet/$vermogensbeheerderTotaal*100,2), 0, 1, "R");
				}
				$this->pdf->ln();
				$this->pdf->SetFont("Times","",10);
		}


		$this->pdf->SetFont("Times","b",10);

		if($tel > 0)
			$performanceTotaal = $performanceTotaal / $grandtotaalWaarde;

		$this->pdf->ln();

		$this->pdf->Line($this->pdf->marge + 187,$this->pdf->GetY(), $this->pdf->marge + 210,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 212,$this->pdf->GetY(), $this->pdf->marge + 230,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge + 235,$this->pdf->GetY(), $this->pdf->marge + 255,$this->pdf->GetY());
    $this->pdf->Line($this->pdf->marge + 260,$this->pdf->GetY(), $this->pdf->marge + 275,$this->pdf->GetY());
		// druk totaal af
		$this->pdf->Cell(10 , 4 , "" , 0, 0, "R");
		$this->pdf->Cell(35 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(120 , 4 , "Totaal" , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , "" , 0, 0, "L");
		$this->pdf->Cell(25 , 4 , $this->formatGetal($grandtotaalWaarde,2) , 0, 0, "R");
		$this->pdf->Cell(20 , 4 , $this->formatGetal($performanceTotaal,2), 0, 0, "R");
    $this->pdf->Cell(25 , 4 , $this->formatGetal($omzetTotaal,2), 0, 0, "R");
    $this->pdf->Cell(20 , 4 , $this->formatGetal($omzetTotaal/$grandtotaalWaarde*100,2), 0, 1, "R");
    

		$this->pdf->SetFont("Times","",10);
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
          $query.=",`$key`='".addslashes($value)."' ";
        }
        $db->SQL($query);
	      $db->Query();
      }
    }

	}
}
?>