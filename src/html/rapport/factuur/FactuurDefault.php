<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/11/07 16:46:38 $
File Versie					: $Revision: 1.3 $

$Log: FactuurDefault.php,v $
Revision 1.3  2015/11/07 16:46:38  rvv
*** empty log message ***

Revision 1.2  2009/05/05 12:38:08  cvs
*** empty log message ***

Revision 1.1  2007/08/02 14:46:59  rvv
*** empty log message ***




*/




		$this->pdf->marge = 10;
    $rowHeightBackup=$this->pdf->rowHeight;
		$this->pdf->rowHeight=6;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->pdf->rapport_type = "FACTUUR";

//listarray($this->waarden);
		$this->pdf->AddPage('P');

		$this->pdf->SetY($this->pdf->getY() +30);
		// start eerste block
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,18);

		//$title = vertaalTekst("",$this->pdf->rapport_taal);
		$this->pdf->Cell(120,6, vertaalTekst("Feenota",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumTot']))." ".$this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot'])), 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("Factuurnr.",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, date("Y",db2jul($this->waarden['datumTot']))."/".$this->factuurnummer, 0,1, "R");

		$this->pdf->Cell(120,6, $this->waarden['clientNaam'], 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("Rek.nr.",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->portefeuille, 0,1, "R");

		$this->pdf->Cell(120,6, $this->waarden['clientNaam1'], 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("Valuta",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, vertaalTekst("EUR",$this->pdf->rapport_taal), 0,1, "R");

		$this->pdf->ln(6);$this->pdf->ln(6);
		$this->pdf->ln(6);$this->pdf->ln(6);

		// start tweede block
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,48);
		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Aanvangsvermogen per ",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])).":", 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['totaalWaardeVanaf'],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Eindvermogen",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":", 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['totaalWaarde'],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Line($this->pdf->marge + 120 ,$this->pdf->GetY(),$this->pdf->marge +120 + 30 ,$this->pdf->GetY());

		$this->pdf->Cell(120,6, vertaalTekst("Gemiddeld belegd vermogen:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['rekenvermogen'],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Beheerfee op jaarbasis volgens contract:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['beheerfeeOpJaarbasis'],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Beheerfee per periode volgens contract (inclusief admin vergoeding):",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, "", 0,0, "R");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['beheerfeePerPeriode'],2), 0,1, "R");

		$this->pdf->ln(6);

		// start derde block
		$this->pdf->ln(6);$this->pdf->ln(6);

		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,66);

		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Totaal betaalde effectenprovisie:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['totaalTransactie'],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		if($this->waarden['BeheerfeeRemisiervergoedingsPercentage'])
		{
			$this->pdf->Cell(80,6, vertaalTekst("In mindering op de beheerfee:",$this->pdf->rapport_taal), 0,0, "L");
			$this->pdf->Cell(40,6, $this->waarden['BeheerfeeRemisiervergoedingsPercentage']."%", 0,0, "R");
			$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['remisierBedrag'],2), 0,0, "R");
			$this->pdf->Cell(30,6, "", 0,1, "R");
		}
		else {
			$this->pdf->ln();
		}

		if($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'])
		{
			$this->pdf->Cell(80,6, vertaalTekst("Korting i.v.m. beleggingen in huisfondsen:",$this->pdf->rapport_taal), 0,0, "L");
			$this->pdf->Cell(40,6, $this->formatGetal($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'], 2)."%", 0,0, "R");
			$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['huisfondsKorting'], 2), 0,0, "R");
			$this->pdf->Cell(30,6, "", 0,1, "R");
		}
		else
		{
			$this->pdf->ln();
		}


		$this->pdf->ln(6);


		$this->pdf->Cell(120,6, vertaalTekst("Totaal te betalen beheerfee:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, "", 0,0, "R");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['beheerfeeBetalen'],2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("BTW:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['btw'],2), 0,0, "R");
		$this->pdf->Cell(30,6, "", 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Beheerfee inclusief BTW",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6, "", 0,0, "R");
		$this->pdf->Cell(30,6, $this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2), 0,1, "R");

		$this->pdf->ln(6);$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Verschuldigde beheerfee wordt automatisch van uw rekening afgeschreven",$this->pdf->rapport_taal), 0,1, "L");

		$this->pdf->ln(6);

		// start vierde block
		$this->pdf->ln(6);$this->pdf->ln(6);

		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),185,36);

		$this->pdf->ln(6);

		$this->pdf->Cell(120,6, vertaalTekst("Totaal aan stortingen / onttrekkingen:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($this->waarden['stortingenOntrekkingen'],2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Netto vermogenstoename / afname:",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($this->waarden['resultaat'],2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Performance periode",$this->pdf->rapport_taal)." ".date("j-n-Y",db2jul($this->waarden['datumVan']))." t/m ".date("j-n-Y",db2jul($this->waarden['datumTot'])).":", 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($this->waarden['performancePeriode'],2), 0,1, "R");

		$this->pdf->Cell(120,6, vertaalTekst("Performance jaar",$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":", 0,0, "L");
		$this->pdf->Cell(30,6,  "", 0,0, "R");
		$this->pdf->Cell(30,6,  $this->formatGetal($this->waarden['performanceJaar'],2),  0,1, "R");

		$this->pdf->ln(6);
    $this->pdf->rowHeight=$rowHeightBackup;

?>