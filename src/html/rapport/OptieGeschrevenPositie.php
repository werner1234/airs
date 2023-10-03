<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/11/14 10:41:30 $
 		File Versie					: $Revision: 1.9 $

 		$Log: OptieGeschrevenPositie.php,v $
 		Revision 1.9  2010/11/14 10:41:30  rvv
 		Opties via selectieclass
 		
 		Revision 1.8  2010/07/28 17:18:43  rvv
 		*** empty log message ***

 		Revision 1.7  2008/06/30 07:58:44  rvv
 		*** empty log message ***

 		Revision 1.6  2008/05/16 08:12:57  rvv
 		*** empty log message ***

 		Revision 1.5  2007/08/02 14:46:01  rvv
 		*** empty log message ***

 		Revision 1.4  2007/04/20 12:21:16  rvv
 		*** empty log message ***

 		Revision 1.3  2007/04/03 13:26:33  rvv
 		*** empty log message ***

 		Revision 1.2  2007/02/21 11:04:26  rvv
 		Client toevoeging

 		Revision 1.1  2006/12/05 12:12:24  rvv
 		Optie toevoeging


*/

include_once("rapportRekenClass.php");

class OptieGeschrevenPositie
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function OptieGeschrevenPositie(  $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOptieOverzicht('L','mm');
		$this->pdf->rapport_type = "OptieGeschrevenPositie";
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
			if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
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
	 				Fondsen.OptieType = 'C' AND
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
  		    if(round(round($fondswaarden['optie'][$i]['totaalAantal'],4) < 0 ))
		  	{
			  $optieData[$portefeuille][$aandeel]['aandeel'] = $fondswaarden['aandeel'];
			  $optieData[$portefeuille][$aandeel]['optie'][$i] = $fondswaarden['optie'][$i] ;
		  	}
  		  }
		}
		$portefeuilleData[$portefeuille] = $pdata;
		}

		$this->pdf->AddPage();
		$this->pdf->excelData[] = array("Portefeuille", "Client", "Fonds", "Aantal", "Optie", "Aantal", "% geschreven calls");
		$this->pdf->SetFont("Times","",10);

		// nog een keer een loop over de portefeuilles!
		$tel = 0;
		while (list($portefeuille, $data) = each($optieData))
		{
			$pRegel=0;
			foreach ($data as $aandeel)
			{
			  $aantalopties = 0;
			  $aRegel = 0;
			  foreach ($aandeel['optie'] as $optie)
			  {

				$aantal = $optie['totaalAantal'] * $optie['fondsEenheid'];
				$aantalopties += $aantal;
				if($aandeel['aandeel']['totaalAantal'] <> 0)
				{
				  $percentage = $this->formatGetal($aantal / $aandeel['aandeel']['totaalAantal'] * -100 ,1);

				}
				else
				  $percentage = '-';

			    if ($pRegel == 0) // Toon voor de eerste regel client en aandeel informatie
			    {
				  $this->pdf->row(array("$portefeuille",
									   $portefeuilleData[$portefeuille]['Client'],
									   $aandeel['aandeel']['fondsOmschrijving'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantal'],0),
									   "",
   	   								   $optie['fondsOmschrijving'],
					  			   		$this->formatGetal($aantal,0),
				  				   		"$percentage"." %"));

				  $pRegel ++;
				  $aRegel ++;
			    }
			    elseif ($aRegel == 0 ) // Toon voor de eerste regel client en aandeel informatie
			    {
				  $this->pdf->row(array("",
									   "",
									   $aandeel['aandeel']['fondsOmschrijving'],
									   $this->formatGetal($aandeel['aandeel']['totaalAantal'],0),
									   "",
   	   								   $optie['fondsOmschrijving'],
					  			   		$this->formatGetal($aantal,0),
				  				   		"$percentage"." %"));
				  $aRegel ++;
			    }
			    else //Voor de overige regels alleen de optie gegevens.
			    {
					$this->pdf->row(array("",
								   "",
								   "",
								   "",
								   "",
   								   $optie['fondsOmschrijving'],
								   $this->formatGetal($aantal,0),
								   "$percentage"." %"));
			    }

			    $this->pdf->excelData[] = array("$portefeuille",
									   $portefeuilleData[$portefeuille]['Client'],
									   $aandeel['aandeel']['fondsOmschrijving'],
									   round($aandeel['aandeel']['totaalAantal'],0),
   	   								   $optie['fondsOmschrijving'],
					  			   		round($aantal,0),
				  				   		"$percentage");
			  }
			  //Toon optie totalen per aandeel.
				if($aandeel['aandeel']['totaalAantal'] <> 0)
				  $percentageTotaal = $this->formatGetal($aantalopties / $aandeel['aandeel']['totaalAantal'] * -100 ,1) ." %";
				else
				  $percentageTotaal = '-';

				$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
								   "",
								   "",
								   "",
								   "",
   								   "Totaal",
								   $this->formatGetal($aantalopties,0),
								   "$percentageTotaal"));
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->ln();
			}
		}
		$this->pdf->SetFont("Times","b",10);
		$this->pdf->ln();
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
}
?>