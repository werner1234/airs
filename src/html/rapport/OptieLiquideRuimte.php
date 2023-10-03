<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/09/28 14:42:51 $
 		File Versie					: $Revision: 1.12 $

 		$Log: OptieLiquideRuimte.php,v $
 		Revision 1.12  2013/09/28 14:42:51  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/09/01 13:32:39  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/08/14 15:44:43  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2010/11/14 10:41:30  rvv
 		Opties via selectieclass
 		
 		Revision 1.8  2010/07/28 17:18:43  rvv
 		*** empty log message ***

 		Revision 1.7  2009/03/25 17:47:01  rvv
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

class OptieLiquideRuimte
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function OptieLiquideRuimte(  $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOptieOverzicht('L','mm');
		$this->pdf->rapport_type = "OptieLiquideRuimte";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->SetFont("Times","",10);


		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];
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
		$this->pdf->__appvar = $this->__appvar;

		$jaar = date("Y",$this->selectData['datumTm']);
		$einddatum = jul2db($this->selectData['datumTm']);

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

	  $rekeningData = array();
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
        
      $this->pdf->soort=$this->selectData['soort'];   
      if($this->selectData['soort']=='OptieLiquideRuimte')
      {
        $filter=" AND Fondsen.OptieType <> 'C' ";
      }        

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
	 			  Rekeningmutaties.GrootboekRekening = 'FONDS' $filter
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
			  $optieData[$portefeuille][$aandeel]['aandeel'] = $fondswaarden['aandeel'];
			  $optieData[$portefeuille][$aandeel]['optie'][$i] = $fondswaarden['optie'][$i] ;
		  	}
  		  }
		}

		if (isset($optieData[$portefeuille]))
		{
		  $rekeningData[$portefeuille] = berekenRekeningWaarde($portefeuille, $einddatum);
		}
		$portefeuilleData[$portefeuille] = $pdata;
		}

		$this->pdf->AddPage();
		$this->pdf->excelData[] = array("Portefeuille", "Client", "Fonds/rekening", "Aantal", "Optie", "Aantal", "Uitgaven EUR", "Waarde EUR");
		$this->pdf->SetFont("Times","",10);

		// nog een keer een loop over de portefeuilles!
		$tel = 0;
		while (list($portefeuille, $data) = each($optieData))
		{
		  $pdfRegels=array();
      $xlsRegels=array();
			$pRegel=0;
			$aantalopties = 0;
			$totaalKosten=0;
			foreach ($data as $aandeel)
			{
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
					$gechrevenCalls[]=$optie;
				}
				elseif($optie['totaalAantal'] < 0 && $optie['optieType'] == 'P')
				{
					$geschrevenPuts[]=$optie;
					$printFonds = true;
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
        
			  foreach ($aandeel['optie'] as $optie)
			  {
			  	$kosten = 0;
				$aantal = $optie['totaalAantal'] * $optie['fondsEenheid'];
				$aantalopties += $aantal;

				if($optie['optieType'] == 'P' && $optie['totaalAantal'] < 0)
				{
					$kosten = $optie['totaalAantal'] * $optie['fondsEenheid'] * $optie['optieUitoefenPrijs'] * $optie['actueleValuta'];
					$totaalKosten += $kosten;
					$kosten = $this->formatGetal($kosten,2);
				}
				elseif($optie['optieType'] == 'C' && $optie['totaalAantal'] < 0)
				{
					for($i=0; $i<count($geschrevenPuts); $i++)
					{
						if(	$optie['optieExpDatum'] <= $geschrevenPuts[$i]['optieExpDatum'])
						{
							if ($aandeel['aandeel']['totaalAantal'] > $optie['totaalAantal'])
							{
							$kosten = -1 * $optie['totaalAantal'] * $optie['optieUitoefenPrijs'] * $optie['fondsEenheid'] * $optie['actueleValuta'];
							$totaalKosten += $kosten;
							$kosten = $this->formatGetal($kosten,2);
							}
							else
							$kosten = 'Uitoefenprijs >';

						}
						else
						$kosten = 'expDatum >';
					}				
        }
			  else
				  $kosten = 0;

        //afdruk gedeelte
      if($printFonds == true)
      {
				if ($pRegel == 0) // Toon voor de eerste regel client en aandeel informatie
			    {
				  $pdfRegels[]=array("$portefeuille",
									   $portefeuilleData[$portefeuille]['Client'],
									   $aandeel['aandeel']['fondsOmschrijving'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantal'],0),
									   "",
   	   								   $optie['fondsOmschrijving'],
					  			   		$this->formatGetal($aantal,0),
				  				   		$kosten);
				  $pRegel ++;
				  $aRegel ++;
			    }
			    elseif ($aRegel == 0 ) // Toon voor de eerste regel client en aandeel informatie
			    {
				  $pdfRegels[]=array("",
									   "",
									   $aandeel['aandeel']['fondsOmschrijving'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantal'],0),
									   "",
   	   								   $optie['fondsOmschrijving'],
					  			   		$this->formatGetal($aantal,0),
				  				   		$kosten);
				  $aRegel ++;
			    }
			    else //Voor de overige regels alleen de optie gegevens.
			    {
					$pdfRegels[]=array("",
								   "",
								   "",
								   "",
								   "",
   								   $optie['fondsOmschrijving'],
								   $this->formatGetal($aantal,0),
								   $kosten);
			    }

			    $xlsRegels[] = array($portefeuille,
									   $portefeuilleData[$portefeuille]['Client'],
									   $aandeel['aandeel']['fondsOmschrijving'],
									   $aandeel['aandeel']['totaalAantal'],
									   $optie['fondsOmschrijving'],
					  			   	   $aantal,
				  				   	   $kosten, "" );
         }
//end afdruk

			  }


  }
  if ($printFonds == true) //rekeningen en totaal aan eind portefeuille
  {
	//$rekeningData[$portefeuille]
	$rekeningTotaal = 0;
		for($r=0; $r<count($rekeningData[$portefeuille]); $r++)
		{
			if ($rekeningData[$portefeuille][$r]['valuta'] == "EUR" && round($rekeningData[$portefeuille][$r]['bedrag'],2) != 0)
			{
				$rekeningEur = $rekeningData[$portefeuille][$r]['koers'] * $rekeningData[$portefeuille][$r]['bedrag'];
				$pdfRegels[]=array("","",
								   $rekeningData[$portefeuille][$r]['rekening'],
								   "","","","","",
								   $this->formatGetal($rekeningEur,2));

				$xlsRegels[] = array($portefeuille,
									   $portefeuilleData[$portefeuille]['Client'],
									   $rekeningData[$portefeuille][$r]['rekening'],
									   "","","","",round($rekeningEur,2) );

			$rekeningTotaal += $rekeningEur;
			}
		}
		$pdfRegels[]=array("");
		$saldo = $totaalKosten + $rekeningTotaal;
		if($saldo < 0)
		{
		$melding = "Liquiditeiten tekort van ";
		}
		else
		{
		$melding = 'Liquiditeiten na transacties';
		}
    
    if($saldo < 0 || !isset($this->selectData['liquideRuimteTekort']))
    {
      foreach($pdfRegels as $regel)
        $this->pdf->row($regel);
      foreach($xlsRegels as $regel)
        $this->pdf->excelData[]=$regel;
   
  		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  	$this->pdf->row(array("","",
							  $melding,
							  "€ ".$this->formatGetal($saldo,2),
							  "","","Totalen",
							  $this->formatGetal($totaalKosten,2),
							  $this->formatGetal($rekeningTotaal,2)));
    
	   	$this->pdf->excelData[] = array($portefeuille,
							   	 $portefeuilleData[$portefeuille]['Client'],
								 $melding,
								 round($saldo,2),
							   	 "",
   								 "Totalen",
								 round($totaalKosten,2),
								 round($rekeningTotaal,2));

	  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	  	$this->pdf->ln();
    }
  }

		}
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