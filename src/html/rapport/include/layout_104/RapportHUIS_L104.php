<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHUIS_L104
{

	function RapportHUIS_L104($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->pdf = &$pdf;
    $this->portefeuille=$portefeuille;
    $this->pdf->rapport_type = "HUIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "";
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;

	}



	function writeRapport()
	{
	  global $__appvar;

    $this->pdf->AddPage('L');
    $this->pdf->templateVars['HUISPaginas']=$this->pdf->page;
    $this->pdf->widthA = array(30,180);
    $this->pdf->alignA = array('L','L','L');

    $fontsize = 10; //$this->pdf->rapport_fontsize


    $this->pdf->SetWidths($this->pdf->widthA);

    $this->pdf->SetY(40);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->row(array(' ',vertaalTekst('Disclaimer en juridische kennisgeving',$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array(' ','Groene Hart Vermogensbeheer en Groene Hart Financiele Diensten zijn handelsnamen van Moka Asset Management Europe bv. Moka Asset Management Europe staat onder toezicht van AFM en de DNB. Moka Asset Management Europe is ingeschreven bij de Kamer van Koophandel te Amsterdam, KvK- nummer 24275087, kantoorhoudende aan de Veenweg 158C (3641 SM) te Mijdrecht. Voor meer contactinformatie verwijzen wij u naar onze website www.ghfd.nl. 

Deze rapportage over uw beleggingsportefeuille is strikt persoonlijk en vertrouwelijk. De rapportage is met de nodige zorg samengesteld en beoogt een feitelijke weergave te geven van uw beleggingsportefeuille. Als u het niet eens bent met de inhoud van de verstrekte effectennota of overzichten dan moet u dat binnen één week aan ons laten weten. Doet u dit niet? Dan betekent dit dat u akkoord bent met de inhoud. 

De posities van de portefeuille worden gewaardeerd tegen de bij ons op de datum van opmaak van de rapportage laatst bekende koersen per rapportagedatum. Alle vreemde valuta’s zijn gewaardeerd tegen koersen op de laatste handelsdag van de rapportageperiode. De informatie die de basis voor de bovenstaande waarderingsgrondslagen vormt, is afkomstig van onafhankelijke bronnen.  

Beleggen brengt risico’s met zich mee. De waarde van beleggingen kan fluctueren. In het verleden behaalde resultaten bieden geen garantie voor de toekomst. Valutaschommelingen kunnen van invloed zijn op het rendement. 

Onder voorbehoud van type- en drukfouten.
     '));


  }
  

}
