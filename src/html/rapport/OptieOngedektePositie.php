<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/01/16 11:20:42 $
 		File Versie					: $Revision: 1.15 $

 		$Log: OptieOngedektePositie.php,v $
 		Revision 1.15  2011/01/16 11:20:42  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2010/11/14 10:41:30  rvv
 		Opties via selectieclass

 		Revision 1.13  2010/07/28 17:18:43  rvv
 		*** empty log message ***

 		Revision 1.12  2009/11/11 13:41:47  rvv
 		*** empty log message ***

 		Revision 1.11  2009/05/27 16:00:22  rvv
 		*** empty log message ***

 		Revision 1.10  2009/04/29 13:02:15  rvv
 		*** empty log message ***

 		Revision 1.9  2009/04/15 13:07:02  rvv
 		*** empty log message ***

 		Revision 1.8  2009/01/20 17:44:08  rvv
 		*** empty log message ***

 		Revision 1.7  2008/09/03 09:05:17  rvv
 		*** empty log message ***

 		Revision 1.6  2008/06/30 07:58:44  rvv
 		*** empty log message ***

 		Revision 1.5  2008/05/16 08:12:57  rvv
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

class OptieOngedektePositie
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function OptieOngedektePositie(  $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOptieOverzicht('L','mm');
		$this->pdf->rapport_type = "OptieOngedektePositie";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->SetFont("Times","",10);


		$this->pdf->vandatum = $this->selectData[datumVan];
		$this->pdf->tmdatum = $this->selectData[datumTm];
		$this->pdf->OptieExpJaar = $this->selectData['expiratieJaar'] ;
		$this->pdf->OptieExpMaand = $this->selectData['expiratieMaand'];
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	global $__appvar;

	$percentageLimiet = $this->selectData['ongedektePositiePercentage'];
	if($percentageLimiet == '0')
	  $percentageLimiet = $percentageLimiet - 0.00001;

		$this->pdf->__appvar = $this->__appvar;

		$jaar = date("Y",$this->selectData['datumTm']);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		if($records <= 0)
		{
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


		foreach($portefeuilles as $pdata)
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}

			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($rapportageDatum[a]) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
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

			$einddatum = jul2db($this->selectData['datumTm']);


		$query =	"SELECT Fondsen.OptieBovenliggendFonds as aandeel,
	 				Rekeningmutaties.Fonds as optie
	 				FROM (Rekeningmutaties, Rekeningen, Portefeuilles)
					JOIN Fondsen on Fondsen.Fonds = Rekeningmutaties.Fonds
					WHERE
	 				Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	 				Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Portefeuilles.Portefeuille = '$portefeuille' AND
	 				Fondsen.OptieExpDatum <> '' AND
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
//		$optieData=array();
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
			    $optieData[$portefeuille][$aandeel]['aandeel'] = $fondswaarden['aandeel'];
			    $optieData[$portefeuille][$aandeel]['optie'][$i] = $fondswaarden['optie'][$i] ;
		    }
  		}
		}

	//	listarray($fondswaarden);
	//	listarray($optieData);

		$portefeuilleData[$portefeuille] = $pdata;
		}

		$this->pdf->AddPage();
		$this->pdf->excelData[] = array("Portefeuille", "Client", "Fonds", "Aantal", "Optie", "Aantal", "% geschreven calls");
		$this->pdf->SetFont("Times","",10);


		$tel = 0;

		while (list($portefeuille, $data) = each($optieData))
		{
	    $pRegel=0;
			$aantalopties = 0;
			foreach ($data as $aandeel)
			{
			  $aandeel['aandeel']['totaalAantalZonderPuts'] = $aandeel['aandeel']['totaalAantal'];
			  $aantalopties = 0;
			  $aRegel = 0;
			  $percentageTotaal= 0 ;
			  $gechrevenCalls = array();
			  $geschrevenPuts = array();
			  $gekochteCalls = array();
			  $gekochtePuts = array();
			  $printFonds = false;
			  foreach ($aandeel['optie'] as $optie)
			  {

			  if($optie['totaalAantal'] < 0 && $optie['optieType'] == 'C')
				{
					//$geschrevenCallPercentage += round($aantal / $aandeel['aandeel']['totaalAantal'] * -100 ,2);
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
/*
			  foreach ($aandeel['optie'] as $optie)
			  {
				  if ($optie['optieType'] == 'P' && $optie['totaalAantal'] < 0 ) //geschreven put kunnen het onderliggende fondsaantal verhogen.
				  {
				    $done=false;
				  	for($i=0; $i<count($gechrevenCalls); $i++)
				  	{
					  	if(	$optie['optieExpDatum'] > $gechrevenCalls[$i]['optieExpDatum'])
						    $done = true;
					  	if ($optie['optieUitoefenPrijs'] > $gechrevenCalls[$i]['optieUitoefenPrijs'])
	  					    $done = true;
			    	}
			    	if($done == false)
			    	{
			    	  $aandeel['aandeel']['totaalAantal'] += $optie['totaalAantal'] * $optie['fondsEenheid'] * -1;
			    	}
				  }
			  }
*/
			  if ($aandeel['aandeel']['totaalAantal'] == 0)
			  {
				$printFonds = true;//rvv22-10-08
			  }

			  foreach ($aandeel['optie'] as $optie)
			  {
			  	$percentage=0;
				  $aantal = $optie['totaalAantal'] * $optie['fondsEenheid'];
				  $aantalopties += $aantal;

				if($aandeel['aandeel']['totaalAantal'] <> 0 && $optie['optieType'] == 'C' && $optie['totaalAantal'] < 0) //geschreven call
				{
					$percentage = $aantal / $aandeel['aandeel']['totaalAantal'] * -100;
					$percentageTotaal += $percentage;
					$percentage = round($percentage,2);
				}
				elseif ($aandeel['aandeel']['totaalAantal'] <> 0 && $optie['optieType'] == 'P' && $optie['totaalAantal'] < 0 ) //geschreven put
				{
					for($i=0; $i<count($gechrevenCalls); $i++)
					{
						if(	$optie['optieExpDatum'] <= $gechrevenCalls[$i]['optieExpDatum'])
						{
							if ($optie['optieUitoefenPrijs'] < $gechrevenCalls[$i]['optieUitoefenPrijs'])
							{
							$percentage =($optie['totaalAantal'] * $optie['fondsEenheid'] )/ $aandeel['aandeel']['totaalAantal'] * 100 ;
							$percentageTotaal += $percentage;
							$percentage = round($percentage,2);
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
				elseif ($aandeel['aandeel']['totaalAantal'] <> 0 && $optie['optieType'] == 'C' && $optie['totaalAantal'] > 0 ) //gekochte call
				{

					for($i=0; $i<count($gechrevenCalls); $i++)
					{
						if(	$optie['optieExpDatum'] <= $gechrevenCalls[$i]['optieExpDatum'])
						{
							if ($optie['optieUitoefenPrijs'] < $gechrevenCalls[$i]['optieUitoefenPrijs'])
							{
							$percentage = ($optie['totaalAantal'] * $optie['fondsEenheid'] )/ $aandeel['aandeel']['totaalAantal'] * 100;
							$percentageTotaal += $percentage;
							$percentage = round($percentage,2);
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
				  $percentageTotaal += 10000;
				}
			    else
				  $percentage = '-';

//afdruk gedeelte
if($printFonds == true)
{
				if ($pRegel == 0) // Toon voor de eerste regel client en aandeel informatie
			    {
				  $dataBuffer[]=(array("$portefeuille",
									   $portefeuilleData[$portefeuille]['Client'],
									   $aandeel['aandeel']['fondsOmschrijving'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantalZonderPuts'],0),
									   "",
   	   								   $optie['fondsOmschrijving'],
					  			   		$this->formatGetal($aantal,0),
				  				   		"$percentage"." %"));

				  $pRegel ++;
				  $aRegel ++;
			    }
			    elseif ($aRegel == 0 ) // Toon voor de eerste regel client en aandeel informatie
			    {
				  //$this->pdf->row
				  $dataBuffer[]=(array("",
									   "",
									   $aandeel['aandeel']['fondsOmschrijving'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantalZonderPuts'],0),
									   "",
   	   								   $optie['fondsOmschrijving'],
					  			   		$this->formatGetal($aantal,0),
				  				   		"$percentage"." %"));
				  $aRegel ++;
			    }
			    else //Voor de overige regels alleen de optie gegevens.
			    {
					//$this->pdf->row
					$dataBuffer[]=(array("",
								   "",
								   "",
								   "",
								   "",
   								   $optie['fondsOmschrijving'],
								   $this->formatGetal($aantal,0),
								   "$percentage"." %"));
			    }

			    //$this->pdf->excelData[]
			    $csvBuffer[] = array($portefeuille,
								   $portefeuilleData[$portefeuille]['Client'],
								   $aandeel['aandeel']['fondsOmschrijving'],
								   round($aandeel['aandeel']['totaalAantal'],0),
   	   							   $optie['fondsOmschrijving'],
					  			   round($aantal,0),
				  				   $percentage);

}
//end afdruk

			  }

$percentageTotaal=round($percentageTotaal,2);
if($percentageTotaal > $percentageLimiet)
{
  for ($i=0; $i<count($dataBuffer);$i++)
  {

    $this->pdf->row($dataBuffer[$i]);
  }
  for ($i=0; $i<count($csvBuffer);$i++)
  {
    $this->pdf->excelData[]= $csvBuffer[$i];
  }
  $printFonds = true;
}
else
  $pRegel=0;
$dataBuffer = array();
$csvBuffer  = array();

      if($printFonds == true && $percentageTotaal > $percentageLimiet)
      {
        if($percentageTotaal >= 1000)
          $percentageTotaal = "p.m.";
        else
				  $percentageTotaal = $this->formatGetal($percentageTotaal,2) ." %";

				$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
								   "",
								   "",
								   "",
								   "",
   								 "",
								   "Totaal",
								   "$percentageTotaal"));
									//$this->formatGetal($aantalopties,0)
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->ln();
			}
}
		}


		if($this->progressbar)
			$this->progressbar->hide();
	}


}
?>