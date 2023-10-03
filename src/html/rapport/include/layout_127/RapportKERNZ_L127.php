<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportKERNZ_L127
{
	function RapportKERNZ_L127($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Informatie over risicoprofielen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

	}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->setFillColor(0,0,0);
		risicoMeter_L127($this->pdf, 50, 65, 50, $this->pdf->portefeuilledata['Risicoklasse']);
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$txt="Het gebruik van de Risicometer Beleggen (voorheen: risicowijzer) kent beperkingen. Bij het benoemen van beperkingen van Risicometer Beleggen kunnen de volgende aspecten (niet limitatief) van belang zijn:

  • De Risicometer Beleggen is gebaseerd op de beweeglijkheid van de koersen in het verleden.
  • Dit is een indicatie voor de beweeglijkheid in de toekomst, maar geen garantie.
  • Hoe beweeglijker de koersen, hoe hoger het rendement kan zijn, maar ook hoe lager het rendement kan zijn. Rendement kan positief maar ook negatief zijn.
  • Er zijn meer risico’s bij beleggen. De informatie over het risicoprofiel bevat een beschrijving van deze risico’s.
  • Laag risico betekent nog steeds dat geld verloren kan worden.
  • De Risicometer Beleggen gaat over de standaard beleggingen in een profiel en niet over de daadwerkelijke beleggingen in een individueel geval.
  • De Risicometer Beleggen gaat uit van een gespreide beleggingsportefeuille. Een minder gespreide beleggingsportefeuille kent veelal een hoger risico.
  • De Risicometer Beleggen gaat uit van een lange beleggingshorizon. Hoe korter de (resterende) beleggingshorizon des te waarschijnlijker het is, dat het jaar-rendement verder afzit van het verwachte jaar-rendement.
  • Het is van belang kennis te nemen van alle relevante aspecten van de Risicometer Beleggen.

Het bepalen van het beleggingsrisico aan de hand van de standaarddeviaties (de waarden die aan de berekening ten grondslag liggen) is geen exacte wetenschap. Het is slechts het maken van een inschatting. 

Het is eveneens van belang in dit verband de volgende informatie te raadplegen:";

		$linkArray=array('http://www.1301services.nl/documenten/2017vbarisicostandaarden.pdf'=>'  • het rapport VBA Risico- Standaarden Beleggingen 2017;',
	'http://www.1301services.nl/documenten/2016AFMLeidraadrisicoprofielen.pdf'=>'  • De AFM Leidraad Informatie over risicoprofielen 2016 Aanbevelingen voor een betere aansluiting tussen beleggingen en risicoprofielen',
	'https://www.nvb.nl/thema-s/sparen-lenen-beleggen/5160/risicometer-beleggen.html'=>'  • de website van de Nederlandse Vereniging van Banken (NVB); en',
	'https://www.afm.nl/nl-nl/professionals/onderwerpen/downloadbestanden-informatieverstrekking/risicowijzer-beleggingen'=>'  • de website van de De Auroriteit Financiële Markten.');

		$this->pdf->setY(100);
		$this->pdf->setWidths(array(297-$this->pdf->marge*2));
		$this->pdf->setAligns(array('L'));
		$this->pdf->row(array($txt));
    foreach($linkArray as $link=>$txt)
		{
			$this->pdf->Cell(297 - $this->pdf->marge * 2, $this->pdf->rowHeight, $txt, null, null, null, null, $link);
			$this->pdf->ln();
		}

	}
}
?>