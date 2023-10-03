<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/07/11 16:16:40 $
File Versie					: $Revision: 1.1 $

$Log: RapportEND_L75.php,v $
Revision 1.1  2018/07/11 16:16:40  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportEND_L75
{
	function RapportEND_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->portefeuille=$portefeuille;
		$this->rapportageDatumVanaf=$rapportageDatumVanaf;
		$this->rapportageDatum=$rapportageDatum;

		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Begrippenlijst bij uw vermogensrapportage";

	}

	function writeRapport()
	{
	  global $__appvar;
      $this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$txtArray=array(array('Aandelen'=>'bewijs van deelname in het kapitaal van een onderneming en geeft in principe recht op dividend. De prijs wordt uitgedrukt als aandelenkoers en komt meestal tot stand op een aandelenmarkt.'),
			array('AFM'=>'Autoriteit Financiële Markten. Maakt zich sterk voor eerlijke en transparante financiële markten en is in Nederland de onafhankelijke gedragstoezichthouder.'),
			array('Allocatie/asset allocatie'=>'hiermee wordt de spreiding van de beleggingsportefeuille over de verschillende beleggingscategorieën aangeduid.'),
			array('Allocatie effect'=>'gevolg van het tactische allocatiebeleid waarbij meer of minder in een bepaalde beleggingscategorie wordt belegd ten opzichte van de strategische allocatie.'),
			array('Attributie-overzicht'=>'hiermee wordt inzichtelijk gemaakt hoe de performance van de portefeuille is opgebouwd. Per beleggingscategorie wordt berekend hoeveel aan de totale performance wordt bijgedragen.'),
			array('Beheerkosten/beheervergoeding'=>'de kosten die veelal per kwartaal worden betaald aan de vermogensbeheerder als vergoeding voor zijn/haar diensten.'),
			array('Beleggingscategorie'=>'een type belegging met eigen karakteristieken zoals aandelen, obligaties, onroerend goed, grondstoffen en liquide middelen.'),
			array('Beleggingsportefeuille'=>'het geheel van beleggingen van bijvoorbeeld particuliere of professionele beleggers, welke uit diverse beleggingscategorieën kan bestaan.'),
			array('Beleggingsresultaat'=>'de opbrengst van een investering of belegging, zowel positief als negatief. Dit kan worden uitgedrukt in een bedrag of als percentage van het beginvermogen.'),
			array('Benchmark'=>'Engels woord voor financiële maatstaf; een ijkpunt (bijvoorbeeld een beursindex of bepaalde staatslening) waartegen de prestaties van een beleggings-portefeuille kunnen worden afgezet om te beoordelen of deze marktconform zijn.'),
			array('Beursindex'=>'indexcijfer om het koersniveau en de koersschommelingen op een bepaalde markt te kunnen beoordelen. Bij effectenindices moet onderscheid gemaakt worden tussen koersindices en herbeleggingsindices.'),
			array('Bewaarloon'=>'tarief voor het aanhouden van een effecten(bewaar)depot.'),
			array('Coupon'=>'afknipbare gedeelte van een obligatie dat tegen inlevering recht geeft op rente.'),
			array('Direct resultaat'=>'opbrengsten die direct toe te kennen zijn aan uw beleggingsportefeuille zoals dividend bij aandelen/fondsen en (coupon)rente bij obligaties.'),
			array('Directe kosten'=>'kosten die direct toe te rekenen zijn aan uw beleggingsportefeuille zoals transactiekosten, bewaarloon, beheervergoeding.'),
			array('Dividend'=>'deel van de winst van een bedrijf dat aan aandeelhouders wordt uitgekeerd.'),
			array('Financiële activa-passiva'=>'ontvangen gelden - uitgeleende gelden'),
			array('Fondsresultaat'=>'ongerealiseerde winst of verlies van de betreffende belegging, waarbij de huidige koers wordt afgezet tegen de koers per 1 januari of tegen de oorspronkelijke aankoopkoers.'),
			array('Hedging'=>'het afdekken van een financieel risico van een investering door middel van een andere investering.'),
			array('Historische waarde ontwikkeling'=>'het verloop van het rendement in de portefeuille over een bepaalde periode, veelal vanaf de startdatum.'),
			array('Illiquide vermogen'=>'beleggingen die niet of nauwelijks te kopen/verkopen zijn omdat er geen markt voor is, veelal niet beursgenoteerde beleggingen.'),
			array('Indirecte kosten'=>'de kosten van de onderliggende producten/fondsen waarin u belegt of handelt. Deze kosten vindt u normaliter niet terug op uw overzicht omdat deze worden verwerkt in de koers en in mindering worden gebracht op de waarde van de beleggingen.'),
			array('Informatieratio'=>'maatstaf voor rendementsrisico die gebruikt wordt bij het beoordelen van de prestatie van een vermogensbeheerder.'),
			array('Kifid'=>'het Klachteninstituut Financiële Dienstverlening. U kunt hier terecht met een klacht over een financieel product of dienst (www.kifid.nl).'),
			array('Kosten'=>'de naam voor de prijs die men moet betalen voor het gebruik van een product of dienst, uitgedrukt in geld.'),
			array('Kosten maatstaf'=>'deze maatstaf geeft een inschatting van de kosten van de beleggingsdienstverlening plus de geschatte kosten van de beleggingsfondsen/producten in de effectenportefeuille.'),
			array('Liquide vermogen'=>'tegenovergestelde van illiquide vermogen, beleggingen die makkelijk en snel te kopen/verkopen zijn, veelal beursgenoteerde beleggingen.'),
			array('Liquiditeiten'=>'verzamelnaam voor direct ter beschikking staande geldmiddelen zoals contant geld en spaarsaldi.'),
			array('Management fee'=>'Engelse term voor de kosten die in mindering worden gebracht op het vermogen van het fonds, als vergoeding voor de fondsbeheerder voor het managen van het fonds.'),
			array('Maximale terugval'=>'het maximale verlies van een effectenportefeuille dat gerealiseerd is binnen een bepaalde periode, meestal een jaar. Ook wel draw-down genoemd.'),
			array('Mutatie overzicht'=>'overzicht van de mutaties die hebben plaatsgevonden in uw beleggingsportefeuille gedurende de rapportage-periode, zowel opbrengsten transacties (dividend en rente) als boekingen van kosten (bewaarloon, beheervergoeding, transactiekosten).'),
			array('Obligatie'=>'een verhandelbaar schuldbewijs voor een lening die door een overheid of onderneming is aangegaan. De hoofdsom wordt terugbetaald aan de koper wanneer de obligatie afloopt. Daarnaast keert de obligatie tijdens de looptijd rente (coupon) uit.'),
			array('Onttrekkingen'=>'bedrag aan liquide middelen dat u uit uw portefeuille heeft opgenomen.'),
			array('Performance'=>'Engelse term voor beleggingsresultaat.'),
			array('Private equity'=>'beleggingen in niet-beursgenoteerde bedrijven'),
			array('Rendement'=>'andere term voor beleggingsresultaat.'),
			array('Rente'=>'de vergoeding die wordt ontvangen voor het uitlenen van geld en die betaald wordt door degene die het geld leent.'),
			array('Selectie effect'=>'wordt veroorzaakt doordat met het beleggingsbeleid van de vermogensbeheerder een rendement wordt verkregen dat afwijkt van de benchmark voor die categorie.'),
			array('Sharpe-ratio'=>'verhoudingsgetal dat het mogelijk maakt de performance van portefeuilles en benchmarks met elkaar te vergelijken. Het geeft de verhouding aan tussen het eventuele risico en de opbrengst van een belegging. Hoe hoger de ratio, hoe beter het gelukt is om bij een bepaald genomen risico een extra rendement te behalen. De Sharpe-ratio wordt veel gebruikt om de prestaties van vermogensbeheerders met elkaar te vergelijken.'),
			array('Sigma'=>'wordt vaak gebruikt voor de uitkomst van een berekening van de standaarddeviatie.'),
			array('Standaarddeviatie'=>'een maatstaf voor de risicograad van beleggingen. Via een formule worden de koersuitslagen ten opzichte van de gemiddelde koersafwijking berekend. Hoe hoger de standaarddeviatie, hoe groter het risico. De standaarddeviatie wordt als een bruikbare indicatie voor het risico/volatiliteit van een portefeuille beschouwd.'),
			array('Standaarddeviatie AFM'=>'leidraad van de AFM om financiële instellingen te ondersteunen bij het centraal stellen van het klantbelang en bij het verbeteren van de kwaliteit van de informatieverstrekking aan consumenten. De AFM beoogt met deze leidraad meer duidelijkheid te geen aan de Consument over de beleggingen en de risico’s achter risicoprofielen, zodat zij adviezen en profielen beter met elkaar kunnen vergelijken.'),
			array('Standaarddeviatie ex-ante'=>'het vooraf ingeschatte risico van de portefeuille gemeten in standaarddeviatie. Voor de berekening is uitgegaan van de gemiddelde standaarddeviaties per beleggingscategorie volgende de VBA Risicostandaarden Beleggingen.'),
			array('Standaarddeviatie ex-post'=>'het gerealiseerde risico van de portefeuille sinds aanvangsdatum gemeten in standaarddeviatie.'),
			array('Stortingen'=>'bedrag aan liquide middelen dat u aan uw portefeuille heeft toegevoegd.'),
			array('Strategische weging/allocatie'=>'spreiding van het vermogen over de verschillende beleggingscategorieën die allen een verschillend risico en rendement hebben. Op lange termijn moet een goede strategische weging/allocatie leiden tot een succesvolle portefeuille.'),
			array('Tactische weging/allocatie'=>'het afwijken van de strategische weging/allocatie om op korte termijn het rendement van de beleggingen te vergroten of het risico te verkleinen.'),
			array('TCO'=>'Het begrip ‘total cost of ownership’ (TCO) betekent dat er een maatstaf wordt gehanteerd door een vermogensbeheerder of bank, waarbij de hoogte van alle kosten van hun dienstverlening voor beleggers inzichtelijk staat weergegeven. Dit zijn zowel de directe als indirecte kosten.'),
			array('TWR'=>'Time Weighted Rate of Return c.q. Tijd Gewogen Rendement. Een methodiek voor het berekenen van het rendement van een beleggingsportefeuille. Bij deze berekening wordt het resultaat van de portefeuille in de tijd berekend, onafhankelijk van eventuele stortingen en onttrekkingen aan het oorspronkelijk geïnvesteerde bedrag.'),
			array('Tracking error'=>'een manier om de afwijking van het rendement van een portefeuille t.o.v. de waardeontwikkeling van een benchmark te meten. In statistische termen is een tracking error de standaarddeviatie van het verschil in rendement tussen de portefeuille en de gehanteerde benchmark.'),
			array('Transactie overzicht'=>'overzicht van alle aan-verkoop transacties die hebben plaatsgevonden in uw beleggingsportefeuille gedurende de rapportage-periode.'),
			array('Value at risk'=>'maatstaf waarmee men vooraf een inschatting maakt van het verlies dat maximaal op een belegging kan worden behaald.'),
			array('Valuta resultaat'=>'het gedeelte van het behaalde rendement dat wordt veroorzaakt door stijgingen/dalingen in valutakoersen.'),
			array('Valuta verdeling'=>'overzicht waarmee inzichtelijk wordt gemaakt hoe de beleggingen in uw portefeuille zijn verdeeld over verschillende valuta.'),
			array('Vastgoed'=>'niet-verplaatsbare materiele zaken, ook wel onroerend goed of onroerende zaak genoemd.'),
			array('Vermogensadvies'=>'vorm van vermogensbeheer waarbij u zelf beslist over aan- en of verkooptransacties binnen uw beleggingsportefeuille. De vermogensbeheerder heeft een adviserende rol die aansluit bij uw wensen en doelstellingen.'),
			array('Vermogensbeheer'=>'het discretionair beheer van vermogens door organisaties of personen die daarin gespecialiseerd zijn. Voor individuele transacties is uw toestemming niet meer nodig, de beheerder heeft een mandaat.'),
			array('Vermogensverdeling'=>'ander woord voor asset allocatie; de spreiding van het vermogen over verschillende beleggingscategorieën.'),
			array('Verslagperiode'=>'het tijdsbestek waarover gerapporteerd wordt, veelal per maand, kwartaal of jaar.'),
			array('Volatiliteit'=>'ander woord voor beweeglijkheid, maatstaf is vaak de standaarddeviatie.'),
			array('Winst/verlies ratio'=>'getal dat de verhouding tussen in een portefeuille gerealiseerde winsten en verliezen aangeeft.'),
			array('Yield'=>'Engelse term waarin het inkomen uit een belegging, zoals rente of dividend, in procenten wordt uitgedrukt ten opzichte van de waarde of koers van de belegging. In het geval van een uitgekeerd dividend wordt er ook gesproken over dividendrendement en bij een uitgekeerde rentecoupon over het couponrendement.'),
			array('YTD'=>'Year to Date, periode in een lopend jaar, gerekend van 1 januari tot heden.'),
			array('YTD Winst/verlies'=>'bedrag aan ongerealiseerde winst of verlies in een portefeuille, gerekend in het lopende jaar van 1 januari tot heden.'),
			array('YTD Rendement'=>'percentage aan ongerealiseerde winst of verlies in een portefeuille, gerekend in het lopende jaar van 1 januari tot heden.'));
		$this->pdf->setY(20);
		$this->pdf->setAligns(array('L','L'));
		$this->pdf->setWidths(array(297-$this->pdf->marge*2));
    foreach($txtArray as $blok)
		{
			foreach($blok as $kop=>$tekst)
			{
				$kop.=": ";
				$this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
				$kopWidth=$this->pdf->GetStringWidth($kop);
				$aantalSpaties = round($kopWidth / ($this->pdf->CurrentFont['cw'][' '] * $this->pdf->FontSize / 1000));
				$spatieVulling = str_repeat(' ', $aantalSpaties);

				$this->pdf->row(array($kop));
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
				$this->pdf->ln(-4);
				$this->pdf->row(array($spatieVulling.$tekst));
			}
		}



	}


}
?>