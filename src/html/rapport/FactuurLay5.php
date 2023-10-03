<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2007/07/03 11:50:23 $
File Versie					: $Revision: 1.8 $

$Log: FactuurLay5.php,v $
Revision 1.8  2007/07/03 11:50:23  rvv
*** empty log message ***

Revision 1.7  2007/03/22 07:35:54  rvv
*** empty log message ***

Revision 1.6  2007/01/16 14:57:43  rvv
*** empty log message ***

Revision 1.5  2007/01/15 09:45:32  rvv
facnr >100

Revision 1.4  2007/01/12 16:06:46  rvv
*** empty log message ***

Revision 1.3  2007/01/12 12:53:46  rvv
*** empty log message ***

Revision 1.2  2006/12/14 13:59:09  rvv
Geen afronding op 0.1 percent meer

Revision 1.1  2006/12/05 12:15:35  rvv
Toevoeging layout5 factuur

Revision 1.22  2006/11/03 11:24:04  rvv
Na user update

Revision 1.21  2006/10/31 11:57:14  rvv
Voor user update

Revision 1.20  2006/07/26 13:58:34  cvs
*** empty log message ***


Revision 1.18  2006/01/31 08:07:25  jwellner
factuur aanpassingen

Revision 1.17  2006/01/25 09:00:28  jwellner
bufix

Revision 1.10  2005/08/10 11:48:32  cvs
debuginfo verwijderd

Revision 1.8  2005/08/10 10:00:18  cvs
Rekenfouten en huisfonds exclude


Revision 1.3  2005/08/03 14:15:44  jwellner
- FrontOffice aanpassingen
- BackOffice toegevoegd
- Facturatie Bugfix.
- Managementoverzicht

Revision 1.2  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0
 

*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class FactuurLay5
{
	var $exceldata;

	function FactuurLay5($pdf, $portefeuille, $vandatum, $tmdatum, $extrastart)
	{
		$this->excelData 	= array();

		$this->pdf = &$pdf;
		$this->portefeuille = $portefeuille;
		$this->vandatum = $vandatum;
		$this->tmdatum = $tmdatum;
		$this->extrastart = $extrastart;
		$this->pdf->marge = 30;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",12);
		$this->pdf->rapport_type = "FACTUUR";
		
		if (!isset($pdf->excelData))
		{
		$pdf->excelData[] = array(	"Client",
									"Naam",
									"Naam1",
									"Adres",
									"Woonplaats",
									"Telefoon",
									"Fax",
									"Email",
									"Account manager",
									"Depotbank",
									"DatumVan",
									"DatumTot",
									"Factuurnummer",
									"Portefeuille",
									"RapportageValuta",
									"Beginwaarde",
									"Eindwaarde",
									"GemiddeldeWaarde" ,
									"BeheerfeePerJaar",
									"BeheerfeeBedrag",
									"BeheerfeeTeruggaveHuisfondsPercentage",
									"BeheerfeeRemisiervergoedingsPercentage",
									"BetaaldeProvisie",
									"TebetalenBeheerfee",
									"BTW",
									"TeBetalenBeheerfee+BTW",
									"TotaalStortingen",
									"NettoVermogenstoename",
									"PerformancePeriode",
									"PerformanceJaar");
		}
//listarray($this);
//exit;
		// get fee methode
		// get periode data
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	
	
	function writeRapport()
	{
// ***************************** ophalen data voor afdruk ************************ //

		include('FactuurBerekening.php');

		$this->pdf->AddPage('P');

		
		$this->pdf->SetY($this->pdf->getY() +30);
		// start eerste block

		
		$kwartaal = ceil(date("n",db2jul($this->tmdatum))/3);
	//	$kwartaal = ceil(date("n",db2jul('2006-03-20'))/3);

		$kwartalen[1] = 'eerste';
		$kwartalen[2] = 'tweede';
		$kwartalen[3] = 'derde';
		$kwartalen[4] = 'vierde';

		$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));
		$this->pdf->row(array($clientdata['Naam']));
		if ($clientdata['Naam1'] !='')
		  $this->pdf->row(array($clientdata['Naam1']));
		$this->pdf->row(array($clientdata['Adres']));
		$this->pdf->row(array($clientdata['Woonplaats']));
	
		$this->pdf->SetY($this->pdf->getY() +20);

		$this->pdf->ln();
		if ($this->factuurnummer < 10)
		$this->factuurnummer = "$jaar-".$kwartaal.'-00'.$this->factuurnummer;
		elseif  ($this->factuurnummer < 100)
		$this->factuurnummer = "$jaar-".$kwartaal.'-0'.$this->factuurnummer;
		else //toevoeging voor nummers >100
		$this->factuurnummer = "$jaar-".$kwartaal.'-'.$this->factuurnummer;
		
		$this->pdf->SetFont("Times","I",12);
		$this->pdf->SetWidths(array(30,100));	
		$this->pdf->SetAligns(array("L","L"));
		if ($portefeuilledata['SoortOvereenkomst'] == 'Beheer')
		   $this->pdf->row(array("Betreft:", 'Beheersvergoeding inzake portefeuille '.$this->portefeuille));
		elseif ($portefeuilledata['SoortOvereenkomst'] == 'Advies')   
		   $this->pdf->row(array("Betreft:", 'Adviesvergoeding inzake portefeuille '.$this->portefeuille));
		if ($portefeuilledata['BeheerfeeAantalFacturen'] == 4)
		 $this->pdf->row(array("Periode:", $kwartaal.'e kwartaal '.$jaar));
		if ($portefeuilledata['BeheerfeeAantalFacturen'] == 1)
		 $this->pdf->row(array("Periode:", 'Jaar '.$jaar)); 
		 
		$this->pdf->row(array("Factuur:", $this->factuurnummer));
		$this->pdf->row(array("Datum:", date("j",db2jul($this->tmdatum))." ".$this->__appvar["Maanden"][date("n",db2jul($this->tmdatum))]." ".date("Y",db2jul($this->tmdatum))));
		
		$this->pdf->ln();
		$this->pdf->SetY($this->pdf->getY() +15);
		$this->pdf->SetFont("Times","",12);


	if ($portefeuilledata['SoortOvereenkomst'] == 'Beheer')
	{
	  if ($portefeuilledata['BeheerfeeAantalFacturen'] == 4)
		$introTekst = 	"Conform de vermogensbeheersovereenkomst zullen wij opdracht geven uw rekening een dezer dagen te ".
						"belasten voor de beheersvergoeding over het ".$kwartaal."e kwartaal van " . $jaar ."." ;	
	  if ($portefeuilledata['BeheerfeeAantalFacturen'] == 1)		
		$introTekst = 	"Conform de vermogensbeheersovereenkomst zullen wij opdracht geven uw rekening een dezer dagen te ".
						"belasten voor de beheersvergoeding over het jaar " . $jaar ."." ;			
	}
	elseif ($portefeuilledata['SoortOvereenkomst'] == 'Advies')
	{
	   if ($portefeuilledata['BeheerfeeAantalFacturen'] == 4)
		$introTekst = 	"Conform de effectenadviesovereenkomst zullen wij uw rekening een dezer dagen ".
						"belasten voor de adviesvergoeding over het ".$kwartaal."e kwartaal van " . $jaar ."." ;	
	   if ($portefeuilledata['BeheerfeeAantalFacturen'] == 1)
		$introTekst = 	"Conform de effectenadviesovereenkomst zullen wij uw rekening een dezer dagen ".
						"belasten voor de adviesvergoeding over het jaar " . $jaar ."." ;						
	}
	else 
	{
		$introTekst="Geen beheerovereenkomst.";
	}
	$this->pdf->SetWidths(array(160));
	$this->pdf->row(array($introTekst));
	
	$this->pdf->ln(6);$this->pdf->ln(6);
	
	//BeheerfeeAantalFacturen
	if ($portefeuilledata['BeheerfeePercentageVermogen'] != 0  || $portefeuilledata['BeheerfeeAantalFacturen'] != 0)
	{
	$beheerfeePercentagePeriode = $portefeuilledata['BeheerfeePercentageVermogen'] / $portefeuilledata['BeheerfeeAantalFacturen'];
	}
	else 
	$beheerfeePercentagePeriode = 0;
	
	if (strlen($beheerfeePercentagePeriode) > 9)
	  $beheerfeePercentagePeriode  = $this->formatGetal($beheerfeePercentagePeriode,8);

	
	$this->pdf->SetWidths(array(90,25,30));
	$this->pdf->SetAligns(array("L","R","R"));
	
	if ($portefeuilledata["BeheerfeeBasisberekening"] == 2 )	
		$this->pdf->row(array("Totaal vermogen per ".date("j",db2jul($this->tmdatum))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->tmdatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->tmdatum)), "€", $this->formatGetal($totaalWaarde[totaal],2) ));
	$this->pdf->ln();
	if ($portefeuilledata['SoortOvereenkomst'] == 'Beheer')
	{	
	  if ($portefeuilledata['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array("De berekende fee bedraagt ".$beheerfeePercentagePeriode."% over het beheerde vermogen per kwartaal, derhalve ","\n€","\n".$this->formatGetal($beheerfeePerPeriode,2) ));
	  if ($portefeuilledata['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array("De berekende fee bedraagt ".$beheerfeePercentagePeriode."% over het beheerde vermogen per jaar, derhalve ","\n€","\n".$this->formatGetal($beheerfeePerPeriode,2) ));
	}
	if ($portefeuilledata['SoortOvereenkomst'] == 'Advies')
	{	
	  if ($portefeuilledata['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array("De berekende fee bedraagt ".$beheerfeePercentagePeriode."% over het geadviseerde vermogen per kwartaal, derhalve ","\n€","\n".$this->formatGetal($beheerfeePerPeriode,2) ));
	  if ($portefeuilledata['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array("De berekende fee bedraagt ".$beheerfeePercentagePeriode."% over het geadviseerde vermogen per jaar, derhalve ","\n€","\n".$this->formatGetal($beheerfeePerPeriode,2) ));
	}	
	$this->pdf->row(array("BTW ".$this->formatGetal($portefeuilledata['BeheerfeeBTW'],0) ."%","€",$this->formatGetal($btw,2)));
	$this->pdf->ln();
	$this->pdf->Line($this->pdf->marge + 110 ,$this->pdf->GetY(),$this->pdf->marge +115 + 30 ,$this->pdf->GetY());
	$this->pdf->ln();

	if ($beheerfeePerPeriode < $portefeuilledata['BeheerfeeMinPeriodeBedrag'])
	{
	$this->pdf->row(array("Berekende fee","€",$this->formatGetal($beheerfeeBetalenIncl,2)));	
	$beheerfeeBetalen = $portefeuilledata['BeheerfeeMinPeriodeBedrag'] - $remisierBedrag - $huisfondsKorting;
	$btw = round(($beheerfeeBetalen/100) * $btwTarief,2);
	$beheerfeeBetalen = round($beheerfeeBetalen,2);
	$beheerfeeBetalenIncl = $beheerfeeBetalen + $btw ;

	$this->pdf->SetY($this->pdf->getY() +12);
	$this->pdf->SetWidths(array(100,15,30));	
	if ($portefeuilledata['BeheerfeeAantalFacturen'] == 4)	
		$this->pdf->row(array("Minimum kwartaal fee zoals in vermogensbeheer- overeenkomst beschreven bedraagt","\n€","\n". $this->formatGetal($portefeuilledata['BeheerfeeMinPeriodeBedrag'],2)));
	if ($portefeuilledata['BeheerfeeAantalFacturen'] == 1)	
		$this->pdf->row(array("Minimum jaar fee zoals in vermogensbeheer- overeenkomst beschreven bedraagt","\n€","\n". $this->formatGetal($portefeuilledata['BeheerfeeMinPeriodeBedrag'],2)));
	$this->pdf->ln();
	$this->pdf->row(array("BTW ".$this->formatGetal($portefeuilledata['BeheerfeeBTW'],0) ."%","€",$this->formatGetal($btw,2)));
	$this->pdf->ln();
	$this->pdf->Line($this->pdf->marge + 110 ,$this->pdf->GetY(),$this->pdf->marge +115 + 30 ,$this->pdf->GetY());
	$this->pdf->ln();
	}
	$this->pdf->row(array("Totaal te verrekenen","€",$this->formatGetal($beheerfeeBetalenIncl,2)));
	
		$this->pdf->excelData[] = array(	$clientdata['Client'],
															$clientdata['Naam'],
															$clientdata['Naam1'],
															$clientdata['Adres'],
															$clientdata['Woonplaats'],
															$clientdata['Telefoon'],
															$clientdata['Fax'],
															$clientdata['Email'],
															$portefeuilledata['Accountmanager'],
															$portefeuilledata['Depotbank'],
															date("j",db2jul($this->vandatum))." ".$this->__appvar["Maanden"][date("n",db2jul($this->vandatum))]." ".date("Y",db2jul($this->vandatum)),
															date("j",db2jul($this->tmdatum))." ".$this->__appvar["Maanden"][date("n",db2jul($this->tmdatum))]." ".date("Y",db2jul($this->tmdatum)),
															$this->factuurnummer,
															$this->portefeuille,
															"EUR",
															round($totaalWaardeVanaf[totaal],2),
															round($totaalWaarde[totaal],2),
															round($rekenvermogen,2),
															round($beheerfeeOpJaarbasis,2),
															round($administratieBedrag,2),
															round($beheerfeePerPeriode,2),
															round($huisfondsKortingPercentage,2),
															$totaalTransactie[totaal],
															$beheerfeeBetalen,
															round($btw,2),
															round($beheerfeeBetalenIncl,2),
															round($totaalStortingen,2),
															round($resultaat,2),
															round($performancePeriode,2),
															round($performanceJaar,2));
	}
}
?>