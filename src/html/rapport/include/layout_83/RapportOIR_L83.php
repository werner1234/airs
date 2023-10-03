<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/09/18 14:53:53 $
File Versie					: $Revision: 1.1 $

$Log: RapportOIR_L83.php,v $
Revision 1.1  2019/09/18 14:53:53  rvv
*** empty log message ***

Revision 1.4  2019/05/11 16:49:13  rvv
*** empty log message ***

Revision 1.3  2019/04/24 15:23:46  rvv
*** empty log message ***

Revision 1.2  2019/04/24 14:42:25  rvv
*** empty log message ***

Revision 1.1  2019/04/10 15:47:20  rvv
*** empty log message ***

Revision 1.4  2014/02/22 18:43:38  rvv
*** empty log message ***

Revision 1.3  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.2  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.1  2012/10/07 14:57:18  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIR_L83
{
	function RapportOIR_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_END_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_END_titel;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();

		$this->rapportMaand 	= date("n",$this->rapportageDatumJul);
		$this->rapportDag 		= date("d",$this->rapportageDatumJul);
		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}



	function writeRapport()
	{
	  global $__appvar;

   	$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_titel = "Disclaimer";
		$this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
		//$this->pdf->rowHeight = 3.5;
		//$fontSizeCorrectie=-1;

      $txt="De posities van de portefeuille worden gewaardeerd tegen de bij de rapportage administrateur laatst bekende koersen op de datum van opmaak van de rapportage. Als van een effect geen koers en/of marktwaarde beschikbaar is of deze naar ons oordeel onjuist is, vermelden wij de waarde van dat effect niet. Beleggen brengt risico’s met zich mee. De waarde van beleggingen kan fluctueren. In het verleden behaalde resultaten bieden geen garantie voor de toekomst. Valutaschommelingen kunnen van invloed zijn op het rendement.

Deze rapportage is strikt persoonlijk en vertrouwelijk. De rapportage is met de nodige zorg samengesteld en beoogt een feitelijke weergave te geven van het cliëntprofiel, het portefeuilleprofiel en de portefeuille, voor zover bekend bij Van Lawick & Co. Vermogensbeheer B.V. (hierna: “Van Lawick & Co.”). Als u het niet eens bent met de inhoud van deze rapportage dan moet u dat binnen 30 dagen aan Van Lawick & Co. schriftelijk laten weten. Doet u dit niet? Dan betekent dit dat u akkoord bent met de inhoud.

Aan Van Lawick & Co. is een vergunning als beleggingsonderneming verleend als bedoeld in artikel 2:96 Wet op het Financieel Toezicht (“Wft”) en Van Lawick & Co. staat als zodanig onder toezicht van de Autoriteit Financiële Markten en De Nederlandsche Bank. Van Lawick & Co. heeft zich aangesloten bij het Kifid en heeft haar uitspraken bindend verklaard.

Van Lawick & Co. is deelnemer van het Dutch Securities Institute (DSI). Van Lawick & Co. staat ingeschreven bij de Kamer van Koophandel te Den Haag onder nummer 32083194.

Via www.vanlawick.com kunt u nadere informatie verkrijgen, o.a. de privacyverklaring, de klachtenprocedure, het cookie- en beloningsbeleid en de contactgegevens.";

		$this->pdf->ln(70);
		$this->pdf->SetAligns(array('L','L','L'));
    $this->pdf->SetWidths(array(30,210));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

    $this->pdf->Multicell(280,5,$txt,'','J');
    




    	}
}
?>
