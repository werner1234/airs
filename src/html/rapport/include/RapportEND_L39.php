<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/10 15:50:36 $
File Versie					: $Revision: 1.5 $

$Log: RapportEND_L39.php,v $
Revision 1.5  2019/04/10 15:50:36  rvv
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

class RapportEND_L39
{
	function RapportEND_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
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

   	$this->pdf->rapport_type = "END";
		$this->pdf->rapport_titel = "Disclaimer";
		$this->pdf->addPage();
		$this->pdf->templateVars['DEFPaginas'] = $this->pdf->customPageNo;
		//$this->pdf->rowHeight = 3.5;
		//$fontSizeCorrectie=-1;
    if($this->pdf->rapport_taal==1)
      $txt="This reporting of your investment portfolio is strictly personal and confidential. The report has been compiled with care and aims to provide you with a complete and actual view of your investment portfolio.
The security prices used in this report were obtained from sources considered reliable by us, and are subject to errors and omissions.
The positions of the portfolio are valued at the most recent security prices as reported to Capitael at the date of preparation of this report.

Given the nature of some (unlisted) investments, there may be security prices or rates which were published some time ago whereas the actuality can not always be guaranteed.

Should you ascertain any inaccurate or incomplete information in this report, please inform us soon as possible. No rights can be derived from the contents of this report .
Capitael B.V. is in possession of a license to act as an authorized investment firm pursuant to Article 2:96 and 2:99 of the Act on Financial Supervision and is as such regulated by the Financial Markets Authority and the Dutch Central Bank. Capitael B.V. is registered at the Chamber of Commerce in The Hague under number 53890418.";
    else
      $txt="Deze rapportage van uw beleggingsportefeuille is strikt persoonlijk en vertrouwelijk. De rapportage is met de nodige zorg samengesteld en beoogt een feitelijke en volledige weergave te geven van uw beleggingsportefeuille
 
Het door u gekozen portefeuilleprofiel is ".$this->pdf->portefeuilledata['Risicoklasse'].". Er van uitgaande dat uw voorkeuren, doelstellingen en persoonlijke situatie in de afgelopen periode niet zijn veranderd, voldoen uw beleggingen nog steeds aan uw profiel.
 
Het beleggingsbeleid van Capitael hanteert strikte normen bij de selectie van haar beleggingen op het gebied van sociaal maatschappelijke onderwerpen zoals o.a. corruptie, milieu, gezondheid, wapenhandel en kinderarbeid. Capitael houdt zoveel mogelijk rekening met de ondersteuning van positieve trends ten aanzien van een betere wereld. Wij hanteren hiertoe de regelementen van de \"Council of Ethics of the Norwegian Pension Fund\". https://etikkradet.no/en/

De in deze rapportage gehanteerde koersen zijn verkregen uit door ons betrouwbaar geachte bronnen, onder voorbehoud van fouten en omissies.

De posities van de portefeuille worden gewaardeerd tegen de bij Capitael laatst bekende koersen op de datum van opmaak van deze rapportage. Gezien de aard van sommige (niet-beursgenoteerde) beleggingen kan er sprake zijn van een koers die enige tijd geleden is gepubliceerd en is de actualiteit niet altijd te garanderen. Bij constatering van onjuiste of onvolledige gegevens in deze rapportage verzoeken wij u vriendelijk ons hierover zo spoedig mogelijk te informeren. U kunt geen rechten ontlenen aan de inhoud van deze rapportage.

De waarde van uw belegging kan fluctueren. In het verleden behaalde resultaten bieden geen garantie voor de toekomst.

Capitael B.V. is in het bezit van een vergunning als beleggingsonderneming op grond van artikel 2:96 en 2:99 van de Wet op het financieel toezicht en staat als zodanig onder toezicht van de Autoriteit Financiële Markten en De Nederlandsche Bank.

Capitael B.V. staat ingeschreven bij de Kamer van Koophandel te 's Gravenhage onder nummer 53890418.";

		$this->pdf->ln();
		$y=$this->pdf->getY();
		$this->pdf->SetAligns(array('L','L','L'));
    $this->pdf->SetWidths(array(30,210));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+$fontSizeCorrectie);
		$this->pdf->Row(array('',$txt));




    	}
}
?>
