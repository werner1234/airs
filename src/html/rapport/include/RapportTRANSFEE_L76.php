<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANSFEE_L76
{
	function RapportTRANSFEE_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANSFEE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Geschiktheidsverklaring";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}



	function writeRapport()
	{
    global $__appvar,$USR;
    $this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->templateVars['TRANSFEEPaginas'] = $this->pdf->page;
    $txt = '
Ambassador Vermogensbeheer is een beleggingsonderneming die diensten op het gebied van vermogensbeheer verleent aan niet-professionele beleggers. Vermogensbeheer bij Ambassador Vermogensbeheer is meer dan het beheren van uw portefeuille alleen. Het is een continu streven naar toegevoegde waarde met als missie het behalen van uw doelen op basis van onze beleggingsfilosofie die is gebaseerd op value investing.

Het doel van de portefeuilleconstructie is om de kans zo groot mogelijk te maken om uw doelstelling(en) te behalen. Uw financiële doelstelling(en) staan hierbij centraal, naast uw financiële positie, risicobereidheid en beleggingshorizon. Tevens wordt, voor zover relevant, rekening gehouden met uw kennis en ervaring ten aanzien van beleggen en beleggingsproducten. 

Op basis van de hiervoor bedoelde door u verstrekte en bij ons bekende gegevens heeft Ambassador Vermogensbeheer u ingedeeld in een risicoprofiel en dit risicoprofiel aan u kenbaar gemaakt. Per risicoprofiel bepaalt Ambassador Vermogensbeheer de gewenste verdeling over diverse beleggingscategorieën (zoals aandelen en obligaties). Hierbij speelt de mate van risico waaraan dit risicoprofiel en daarmee ook uw portefeuille bloot mag staan een bepalende rol. De mate van risico wordt gemeten aan de hand van de standaarddeviatie. De standaarddeviatie is een, op historische gegevens gebaseerde, statistische maatstaf waarmee een inschatting gegeven kan worden over de mate waarin een effectenportefeuille in een bepaald jaar kan bewegen als gevolg van marktbewegingen. Hoe hoger de standaarddeviatie, oftewel de beweeglijkheid, hoe meer de waarde van de portefeuille kan fluctueren en dus hoe hoger het risico. De uiteindelijke verdeling over de verschillende beleggingscategorieën mag niet resulteren in een hogere standaarddeviatie dan de bij het risicoprofiel behorende maximum standaarddeviatie. Dit wordt op dagelijkse basis door Ambassador Vermogensbeheer gemonitord en waar nodig kan dit leiden tot aanpassingen aan de in uw portefeuille opgenomen effectenposities. 

De gedurende de rapportageperiode in uw portefeuille opgenomen effectenposities zijn geselecteerd door het analistenteam van Ambassador Vermogensbeheer. Bij de selectie van de effectenposities is rekening gehouden met een breed scala aan aspecten waaronder, voor zover relevant, de kwaliteit en soliditeit van het bedrijf, toereikende solvabiliteit en voldoende liquiditeit en een aantrekkelijke waardering. In het geval van beleggingsfondsen wordt tevens rekening gehouden met de doelgroep van het betreffende beleggingsfonds. Tevens is zorg gedragen voor een voldoende mate van spreiding in de portefeuille. Bij het selecteren van de onderliggende beleggingen van de portefeuille en de aan- en verkopen hiervan is geen rekening gehouden met de EU-criteria voor ecologisch duurzame economische activiteiten.

Als gevolg van de hiervoor beschreven systematiek ten aanzien van portefeuille-inrichting en de selectie van beleggingen was gedurende de rapportageperiode geborgd dat de effectenposities in uw portefeuille aansloten bij de bij Ambassador Vermogensbeheer bekende uitgangspunten met betrekking tot uw beleggingsdoelstelling(en), beleggingshorizon, het risico dat u wilt en kunt nemen, waaronder uw verliescapaciteit, en uw kennis en ervaring. 
    ';

    $this->pdf->MultiCell(280,4, $txt, 0, "L");
	}
}
?>