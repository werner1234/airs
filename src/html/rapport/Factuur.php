<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2007/04/20 12:21:16 $
File Versie					: $Revision: 1.26 $

$Log: Factuur.php,v $
Revision 1.26  2007/04/20 12:21:16  rvv
*** empty log message ***

Revision 1.25  2007/03/22 07:35:54  rvv
*** empty log message ***

Revision 1.24  2007/01/12 12:57:57  rvv
*** empty log message ***

Revision 1.23  2006/12/05 12:15:35  rvv
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

class Factuur
{
	var $exceldata;

	function Factuur($pdf, $portefeuille, $vandatum, $tmdatum, $extrastart)
	{
		$this->excelData 	= array();

		$this->pdf = &$pdf;
		$this->portefeuille = $portefeuille;
		$this->vandatum = $vandatum;
		$this->tmdatum = $tmdatum;
		$this->extrastart = $extrastart;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
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
include('FactuurBerekening.php');

		$this->pdf->AddPage('P');

		$this->pdf->SetY($this->pdf->getY() +30);
		// start eerste block
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,18);

		//$title = vertaalTekst("",$this->pdf->rapport_taal);
		$this->pdf->Cell(120,6, vertaalTekst("Feenota",$this->pdf->rapport_taal)." ".date("j",db2jul($this->tmdatum))." ".$this->__appvar["Maanden"][date("n",db2jul($this->tmdatum))]." ".date("Y",db2jul($this->tmdatum)), 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("Factuurnr.",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, date("Y",db2jul($this->tmdatum))."/".$this->factuurnummer, 0,1, "R");

		$this->pdf->Cell(120,6, $clientdata[Naam], 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("Rek.nr.",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->portefeuille, 0,1, "R");

		$this->pdf->Cell(120,6, $clientdata[Naam1], 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("Valuta",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("EUR",$this->pdf->rapport_taal), 0,1, "R");

		$this->pdf->ln(6);$this->pdf->ln(6);
		$this->pdf->ln(6);$this->pdf->ln(6);

		// start tweede block
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,48);
		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Aanvangsvermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->vandatum))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->vandatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->vandatum)).":", 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($totaalWaardeVanaf[totaal],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Eindvermogen",$this->pdf->rapport_taal)." ".date("j",db2jul($this->tmdatum))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->tmdatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->tmdatum)).":", 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($totaalWaarde[totaal],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Line($this->pdf->marge + 120 ,$this->pdf->GetY(),$this->pdf->marge +120 + 30 ,$this->pdf->GetY());

		$this->pdf->Cell(120,6, vertaalTekst("Gemiddeld belegd vermogen:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($rekenvermogen,2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Beheerfee op jaarbasis volgens contract:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($beheerfeeOpJaarbasis,2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Beheerfee per periode volgens contract (inclusief admin vergoeding):",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, "", 0,0, "R");
		$this->pdf->Cell(30,6, $this->formatGetal($beheerfeePerPeriode,2), 0,1, "R");

		$this->pdf->ln(6);

		// start derde block
		$this->pdf->ln(6);$this->pdf->ln(6);

		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,66);

		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Totaal betaalde effectenprovisie:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($totaalTransactie[totaal],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		if($portefeuilledata[BeheerfeeRemisiervergoedingsPercentage])
		{
			$this->pdf->Cell(80,6, vertaalTekst("In mindering op de beheerfee:",$this->pdf->rapport_taal), 0,0, "L");
			$this->pdf->Cell(40,6, $portefeuilledata[BeheerfeeRemisiervergoedingsPercentage]."%", 0,0, "R");
			$this->pdf->Cell(30,6, $this->formatGetal($remisierBedrag,2), 0,0, "R");
			$this->pdf->Cell(30,6, "", 0,1, "R");
		}
		else {
			$this->pdf->ln();
		}

		if($portefeuilledata[BeheerfeeTeruggaveHuisfondsenPercentage])
		{
			$this->pdf->Cell(80,6, vertaalTekst("Korting i.v.m. beleggingen in huisfondsen:",$this->pdf->rapport_taal), 0,0, "L");
			$this->pdf->Cell(40,6, $this->formatGetal($huisfondsKortingPercentage, 2)."%", 0,0, "R");
			$this->pdf->Cell(30,6, $this->formatGetal($huisfondsKorting, 2), 0,0, "R");
			$this->pdf->Cell(30,6, "", 0,1, "R");
		}
		else
		{
			$this->pdf->ln();
		}


		$this->pdf->ln(6);
		
	if ($beheerfeePerPeriode < $portefeuilledata['BeheerfeeMinPeriodeBedrag'])
	{
	$beheerfeeBetalen = $portefeuilledata['BeheerfeeMinPeriodeBedrag'] - $remisierBedrag - $huisfondsKorting;
	$btw = ($beheerfeeBetalen/100) * $btwTarief;
	$beheerfeeBetalenIncl = $beheerfeeBetalen + $btw ;
	}

		$this->pdf->Cell(120,6, vertaalTekst("Totaal te betalen beheerfee:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, "", 0,0, "R");
		$this->pdf->Cell(30,6, $this->formatGetal($beheerfeeBetalen,2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("BTW:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($btw,2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Beheerfee inclusief BTW",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, "", 0,0, "R");
		$this->pdf->Cell(30,6, $this->formatGetal($beheerfeeBetalenIncl,2), 0,1, "R");

		$this->pdf->ln(6);$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Verschuldigde beheerfee wordt automatisch van uw rekening afgeschreven",$this->pdf->rapport_taal), 0,1, "L");

		$this->pdf->ln(6);

		// start vierde block
		$this->pdf->ln(6);$this->pdf->ln(6);

		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,36);

		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Totaal aan stortingen / onttrekkingen:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($stortingenOntrekkingen,2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Netto vermogenstoename / afname:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($resultaat,2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Performance periode",$this->pdf->rapport_taal)." ".date("j-n-Y",db2jul($this->vandatum))." t/m ".date("j-n-Y",db2jul($this->tmdatum)).":", 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($performancePeriode,2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Performance jaar",$this->pdf->rapport_taal)." ".date("Y",db2jul($this->tmdatum)).":", 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($performanceJaar,2),  0,1, "R");

		$this->pdf->ln(6);


		$this->pdf->excelData[] = array(	$clientdata['Client'],
															$clientdata['Naam'],
															$clientdata['Naam1'],
															$clientdata['Adres'],
															$clientdata['Woonplaats'],
															$clientdata['Telefoon'],
															$clientdata['Fax'],
															$clientdata['Email'],
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