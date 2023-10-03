<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/11/14 10:41:30 $
 		File Versie					: $Revision: 1.8 $

 		$Log: OptieVrijePositie.php,v $
 		Revision 1.8  2010/11/14 10:41:30  rvv
 		Opties via selectieclass
 		
 		Revision 1.7  2010/07/28 17:18:43  rvv
 		*** empty log message ***

 		Revision 1.6  2008/06/30 07:58:44  rvv
 		*** empty log message ***

 		Revision 1.5  2008/05/16 07:52:52  rvv
 		*** empty log message ***

 		Revision 1.4  2007/08/02 14:46:01  rvv
 		*** empty log message ***

 		Revision 1.3  2007/04/03 13:26:33  rvv
 		*** empty log message ***

 		Revision 1.2  2007/02/21 11:04:26  rvv
 		Client toevoeging

 		Revision 1.1  2006/12/05 12:12:24  rvv
 		Optie toevoeging


*/

include_once("rapportRekenClass.php");

class OptieVrijePositie
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $csvData;

	function OptieVrijePositie(  $selectData )
	{

		$this->selectData = $selectData;
		$this->excelData = array();

		$this->pdf = new PDFOptieOverzicht('L','mm');
		$this->pdf->rapport_type = "OptieVrijePositie";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->SetFont("Times","",10);


		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];

		$this->pdf->Fonds = $this->selectData['fonds'];
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	global $__appvar;
		$this->pdf->__appvar = $this->__appvar;

		$jaar = date("Y",$this->selectData['datumTm']);
		$einddatum = jul2db($this->selectData['datumTm']);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
		$portefeuilleList=array_keys($portefeuilles);
		$extraquery="Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') AND ";
		//
		if($this->selectData['geaccordeerd'] == 1)
		  $geaccordeerd = " Portefeuilles.OptieToestaan = 1 AND ";

		// selecteer alleen portefeuilles waar het fonds voorkomt!
		$query = "SELECT ".
				" Portefeuilles.ClientVermogensbeheerder, ".
				" Portefeuilles.Portefeuille, ".
				" Portefeuilles.Depotbank, ".
				" Portefeuilles.Accountmanager, ".
				" Clienten.Client, ".
				" Clienten.Naam, ".
				" Clienten.Naam1 ".
				" FROM (Rekeningmutaties, Rekeningen, Portefeuilles, Clienten)  ".$join.
				" WHERE  ".
				" Portefeuilles.Client = Clienten.Client AND".
				$geaccordeerd.
				" Rekeningmutaties.Fonds = '".$this->selectData['fonds']."' AND ".
				" Rekeningmutaties.Rekening = Rekeningen.Rekening AND  ".
				" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  ".$extraquery."  ".
				" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
				" Rekeningmutaties.Verwerkt = '1' AND ".
				" Rekeningmutaties.Boekdatum <= '".$einddatum."' AND ".
				" Rekeningmutaties.GrootboekRekening = 'FONDS' ".
				" GROUP BY Portefeuilles.Portefeuille "	;
	  //echo $query."\n\n";exit;
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$records = $DB->records();
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


		while($pdata = $DB->nextRecord())
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

		$query =	"SELECT Fondsen.OptieBovenliggendFonds as aandeel,
	 				Rekeningmutaties.Fonds as optie
	 				FROM (Rekeningmutaties, Rekeningen, Portefeuilles)
					JOIN Fondsen on Fondsen.Fonds = Rekeningmutaties.Fonds
					WHERE
	 				Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	 				Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Portefeuilles.Portefeuille = '$portefeuille' AND
	 				Fondsen.OptieBovenliggendFonds = '".$this->selectData['fonds']."' AND
	 				YEAR(Rekeningmutaties.Boekdatum) = '$jaar' AND
	 				Rekeningmutaties.Verwerkt = '1' AND
	 				Rekeningmutaties.Boekdatum <= '$einddatum' AND
	 				Rekeningmutaties.GrootboekRekening = 'FONDS'
	 				GROUP BY Rekeningmutaties.Fonds ";

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();

		$records = $DB2->records();

		$fondsen = array();
		while($fonds = $DB2->NextRecord())
		{
		  $fondsen[$fonds['aandeel']][] = $fonds['optie'];
		}

		$fondswaarden = array();
		while (list($aandeel, $optie) = each($fondsen))
  		{
	  	$fondswaarden['aandeel'] = fondsAantalOpdatum($portefeuille, $aandeel, $einddatum);
	  	  if ($fondswaarden['aandeel']['fondsOmschrijving'] == '')
		  {
		    $query = "SELECT Omschrijving FROM Fondsen where Fonds = '".$aandeel."'";
		    $DB3 = new DB();
		    $DB3->SQL($query);
		    $DB3->Query();
		    $fondsOmschijving = $DB3->lookupRecord();
		    $fondswaarden['aandeel']['fondsOmschrijving'] = $fondsOmschijving['Omschrijving'];
		  }
	  	  for($i=0; $i<count($optie); $i++)
  		  {
  		    $fondswaarden['optie'][$i] 		= optieAantalOpdatum($portefeuille, $optie[$i], $einddatum);
  		    if(round($fondswaarden['optie'][$i]['totaalAantal'],4) <> 0 )
		  	{
//			  $optieData[$portefeuille][$aandeel]['aandeel'] = $fondswaarden['aandeel'];
			  $optieData[$portefeuille][$aandeel]['optie'][$i] = $fondswaarden['optie'][$i] ;
		  	}
  		  }
		}

		$optieData[$portefeuille][$this->selectData[fonds]]['aandeel'] = fondsAantalOpdatum($portefeuille,$this->selectData[fonds], $einddatum);

		$portefeuilleData[$portefeuille] = $pdata;
		}

		$this->pdf->AddPage();
		$this->csvData[] = array("Portefeuille", "Client", "Aantal", "Optie", "Absoluut geschreven", "% geschreven", "Absoluut vrij", "% vrij");
		$this->pdf->SetFont("Times","",10);


		$tel = 0;

		while (list($portefeuille, $data) = each($optieData))
		{
			$pRegel=0;
			$aantalopties = 0;
			foreach ($data as $aandeel)
			{
			  $aantalopties = 0;
			  $aRegel = 0;
			  $percentageTotaal= 0 ;
			  $vrijAantal = 0;
			  $gechrevenCalls = array();
			  $geschrevenPuts = array();
			  $gekochteCalls = array();
			  $gekochtePuts = array();
			  $printFonds = true;
			  foreach ($aandeel['optie'] as $optie)
			  {

			  	if($optie['totaalAantal'] < 0 && $optie['optieType'] == 'C')
				{
					$geschrevenCallPercentage += round($aantal / $aandeel['aandeel']['totaalAantal'] * -100 ,1);
					$gechrevenCalls[]=$optie;
					$printFonds = true;
				}
				elseif($optie['totaalAantal'] < 0 && $optie['optieType'] == 'P')
				{
					$geschrevenPuts[]=$optie;
				}
				elseif($optie['totaalAantal'] > 0 && $optie['optieType'] == 'C')
				{
					$gekochteCalls[]=$optie;
				}
				elseif($optie['totaalAantal'] > 0 && $optie['optieType'] == 'P')
				{
					$gekochtePuts[]=$optie;
				}
			  }

			  if ($aandeel['aandeel']['totaalAantal'] == 0)
			  {
	//			$printFonds = true;
			  }
					  $this->pdf->row(array("$portefeuille",
									   $portefeuilleData[$portefeuille]['Client'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantal'],0),
									   "",
   	   								   "",
					  			   		"",
				  				   		""));

			  foreach ($aandeel['optie'] as $optie)
			  {
			  	$percentage=0;
				$aantal = $optie['totaalAantal'] * $optie['fondsEenheid'];
				$aantalopties += $aantal;

				if($aandeel['aandeel']['totaalAantal'] <> 0 && $optie['optieType'] == 'C' && $optie['totaalAantal'] < 0)
				{
					$percentage = $this->formatGetal($aantal / $aandeel['aandeel']['totaalAantal'] * -100 ,1) ." %";
					$percentageTotaal += $percentage;
					$vrijAantal += $aantal;
				}
				elseif ($aandeel['aandeel']['totaalAantal'] <> 0 && $optie['optieType'] == 'P' && $optie['totaalAantal'] < 0 )
				{

					for($i=0; $i<count($gechrevenCalls); $i++)
					{

						if(	$optie['optieExpDatum'] <= $gechrevenCalls[$i]['optieExpDatum'])
						{
							if ($optie['optieUitoefenPrijs'] < $gechrevenCalls[$i]['optieUitoefenPrijs'])
							{
							$percentage = $this->formatGetal(($optie['totaalAantal'] * $optie['fondsEenheid'] )/ $aandeel['aandeel']['totaalAantal'] * 100 ,1) ." %";
							$percentageTotaal += $percentage;
							$vrijAantal += $aantal;
							}
							else
							$percentage = 'Uitoefenprijs >';

						}
						else
						$percentage = 'expDatum >';
					}
					if (count($gechrevenCalls) == 0)
					$percentage = '<> - calls';

				}
				elseif ($aandeel['aandeel']['totaalAantal'] <> 0 && $optie['optieType'] == 'C' && $optie['totaalAantal'] > 0 )
				{

					for($i=0; $i<count($gechrevenCalls); $i++)
					{
						if(	$optie['optieExpDatum'] <= $gechrevenCalls[$i]['optieExpDatum'])
						{
							if ($optie['optieUitoefenPrijs'] < $gechrevenCalls[$i]['optieUitoefenPrijs'])
							{
							$percentage = $this->formatGetal(($optie['totaalAantal'] * $optie['fondsEenheid'] )/ $aandeel['aandeel']['totaalAantal'] * 100 ,1) ." %";
							$percentageTotaal += $percentage;
							$vrijAantal += $percentageTotaal;
							}
							else
							$percentage = 'Uitoefenprijs >';

						}
						else
						$percentage = 'expDatum >';
					}
					if (count($gechrevenCalls) == 0)
					$percentage = '<> - calls';

				}
				elseif ($aandeel['aandeel']['totaalAantal'] == 0)
				{
				  $percentage = 'Geen aandelen.';
				}
			    else
				  $percentage = '-';

				//afdruk gedeelte
				if($printFonds == true)
				{
					$this->pdf->row(array("",
								   "",

								   "",
								   "",
   								   $optie['fondsOmschrijving'],
								   $this->formatGetal($aantal,0),
								   "$percentage"));

					$this->csvData[] = array($portefeuille,
									   $portefeuilleData[$portefeuille]['Client'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantal'],0),
									    $optie['fondsOmschrijving'],
								   		$this->formatGetal($aantal,0),
								   		$percentage, "", "");
				}
//end afdruk
			  }
				if($printFonds == true)
				{
				$percentageTotaal = $this->formatGetal(100 - $percentageTotaal,1) ." %";
				$vrijAantal = $aandeel['aandeel']['totaalAantal'] + $vrijAantal ;
				$this->pdf->row(array(	"",
								   		"",
										"",
								   		"",
   								   		"",
								   		"",
								   		"",
								   		$vrijAantal,
								   		$percentageTotaal));

				$this->csvData[] = array($portefeuille,
									   $portefeuilleData[$portefeuille]['Client'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantal'],0)
									    ,"" ,"" ,"",
								   		 $vrijAantal,
								   		$percentageTotaal);
				}
			}
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}

}
?>