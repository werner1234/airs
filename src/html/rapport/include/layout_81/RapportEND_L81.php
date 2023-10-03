<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/12/29 13:58:00 $
File Versie					: $Revision: 1.2 $

$Log: RapportEND_L81.php,v $
Revision 1.2  2018/12/29 13:58:00  rvv
*** empty log message ***

Revision 1.1  2018/12/27 15:11:17  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L81
{
	function RapportEND_L81($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Begrippen en verklaringen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}



	function writeRapport()
	{
    //Begrippen en verklaringen
    $data=array(
array("Aan- en verkoopprovisie","In rekening gebrachte kosten voor aan- en verkooptransacties."),
array("Aan- en verkopen","Gekochte en verkochte beleggingen gedurende de rapportageperiode, waaronder stortingen in en onttrekkingen uit deposito's."),
array("Adviesvergoeding","Kosten voor advisering, bewaring en administratie van de portefeuille. Kosten worden eenmaal per kwartaal in rekening gebracht."),
array("Alternative equity/fixed","Niet-traditioneel financieel instrument met (onderliggende) karakteristieken van zakelijke resp. vastrentende waarden, zoals een hedge fund of een private equity beleggingsvorm. Onderdeel van zakelijke resp. vastrentende waarden."),
array("Banking fee","Bancaire kosten zoals kosten voor het verlenen van bankgaranties, bankverklaringen en andere bancaire diensten."),
array("Beginvermogen","Waarde vermogen aan het begin van een periode."),
array("Beheervergoeding","Kosten voor beheer, bewaring en administratie van de portefeuille. In geval van all-in vermogensbeheer tevens inclusief aan- en verkoopprovisie. Kosten worden eenmaal per kwartaal in rekening gebracht."),
array("Belasting","Ingehouden bronbelasting op uitkeringen van dividend."),
array("Beleggingsdoelstelling","De afgesproken doelstelling die ten grondslag ligt aan de invulling van de portefeuille."),
array("Beleggingshorizon","De afgesproken periode waarin het vermogen beschikbaar is voor beleggingsdoeleinden en waarin getracht wordt de beleggingsdoelstelling te realiseren."),
array("Beleggingsrestricties","De afgesproken beperkingen en bijzondere afspraken voor de beleggingen in de portefeuille. Alleen van toepassing bij vermogensbeheer."),
array("Beleggingsresultaat","Gerealiseerd resultaat + ongerealiseerd resultaat + inkomsten + opgelopen rente + kosten."),
array("Benchmarkvergelijking","Vergelijking van de portefeuille met (een) benchmark(s). Een benchmark is een maatstaf waarmee (delen van) een portefeuille vergeleken kunnen worden. Het gaat hier meestal om een index. Deze maakt niet standaard onderdeel uit van de rapportage."),
array("Bewaarloon","In rekening gebrachte kosten voor het bewaren en administreren van effecten. Kosten worden eenmalig vooraf aan het begin van een jaar in rekening gebracht."),
array("Bruto transactie","Waarde transactie exclusief provisie."),
array("Bruto-inkomsten","Inkomsten voor aftrek van provisie en belasting."),
array("BTW","Af te dragen belasting toegevoegde waarde."),
array("Contributie","Dat deel van het totale rendement van de portefeuille dat kan worden toegerekend aan het rendement van een vermogenscategorie. Tijdens een rapportageperiode of over het lopende jaar (contributie cumulatief)."),
array("Coupon","De rentevergoeding die met regelmaat op rentedragende vastrentende waarden wordt betaald."),
array("Coupondatum","Datum waarop coupon wordt uitgekeerd."),
array("Derivaat","Opties, termijncontracten en warrants. Dit zijn afgeleide producten van een onderliggende waarde zoals aandelen, indices, valuta's of commodities. Derivaten kunnen worden onderscheiden binnen ieder van de drie vermogenscategorieën."),
array("Dividendbelasting","Belasting op ontvangen dividenden."),
array("Duration","Risicomaatstaf voor rentegevoeligheid. Hoe langer de resterende looptijd, des te hoger de duration. Hoe hoger de duration, des te sterker reageert de koers op een renteverandering. Stijgt of daalt de rente met 1%, dan fluctueert de waarde van de obligatie met 1% maal de duration."),
array("Eindvermogen","Waarde vermogen aan het einde van een periode."),
array("Europa","België, Denemarken, Duitsland, Finland, Frankrijk, Griekenland, Ierland, Italië, Luxemburg, Nederland, Noorwegen, Oostenrijk, Portugal, Spanje, Verenigd Koninkrijk, Zweden, Zwitserland."),
array("FX","Zie definitie 'valutakoers'."),
array("Geldmarktfonds","Beleggingsfonds dat (hoofdzakelijk) belegt in (kortlopende) deposito's en schuldpapier. Onderdeel van liquiditeiten."),
array("Gerealiseerd resultaat","Gerealiseerde winst of verlies door verkoop of onttrekking van een belegging, afgezet tegen kostprijs."),
array("Inkomsten","Coupon, dividend en overige inkomsten."),
array("Inningskosten","Provisie op coupon-/dividenduitkeringen."),
array("Kasstroomprojectie","Projectie van toekomstige inkomsten uit coupon, rente en lossingen."),
array("Koersdatum","De datum waarop de koers is vastgesteld. De koersdatum kan afwijken van een rapportagedatum, met name bij niet ter beurze genoteerde beleggingsfondsen."),
array("Kosten en belastingen","In rekening gebrachte kosten zoals: adviesvergoeding, beheervergoeding, belastingen en provisie. Negatieve kosten zijn mogelijk als gevolg van correcties op kostenboekingen waarbij de correctie hoger is dan de initiële kostenpost."),
array("Kostprijs","Historische koers waartegen een belegging is gekocht of gestort, gecorrigeerd voor tussentijdse aankopen, stortingen en bepaalde corporate actions."),
array("Kostprijs YTD","Koers van een belegging aan het begin van een kalenderjaar dan wel op het moment van aankoop/storting in dat jaar, gecorrigeerd voor tussentijdse aankopen, stortingen en bepaalde corporate actions."),
array("Liquiditeiten","Een van de drie vermogenscategorieën. Onder te verdelen in: geldmarktfondsen, rekeningcourant, termijndeposito's en valutatermijncontracten."),
array("Looptijd","Resterende looptijd van een vastrentende waarde."),
array("Lossing","Vrijgekomen vastrentende waarde."),
array("Lossingskosten","Provisie op lossingen van vastrentende waarden."),
array("Netto transactie","Waarde transactie inclusief provisie."),
array("Netto-inkomsten","Inkomsten na aftrek van provisie en belasting."),
array("Niet-westerse markten","Alle markten die niet worden genoemd onder 'Europa' en 'Noord-Amerika'."),
array("Noord-Amerika","Canada, Verenigde Staten."),
array("Ongerealiseerd resultaat","Het verschil tussen de waarde van een belegging op een bepaald moment in een kalenderjaar en de waarde gebaseerd op kostprijs (standaard in positie-overzichten) of kostprijs YTD (in het onderdeel 'Vermogensontwikkeling'). Als er in het laatste geval sprake is van gerealiseerd resultaat in een eerder kalenderjaar, wordt ongerealiseerd resultaat hiervoor om boekhoudkundige redenen gecorrigeerd."),
array("Opgelopen rente","Nog niet uitgekeerde rente op bepaalde vastrentende waarden. In het onderdeel 'Vermogensontwikkeling' betreft het om boekhoudkundige redenen een saldering t.o.v. de voorgaande maand."),
array("Overige kosten","Kosten die niet vallen onder een van de andere kostenposten. Overige transacties Wijzigingen in de portefeuille uit hoofde van corporate actions (acties van een uitgevende instelling die invloed hebben op het door haar uitgegeven effect)."),
array("Portefeuilleprofiel","Afgesproken profiel op basis waarvan de portefeuille is verdeeld over vermogenscategorieën, volgens vastgestelde normen en bandbreedtes."),
array("Portefeuilleweging","Procentuele waarde van een vermogenscategorie binnen de totale portefeuille."),
array("Provisie","Bij aan- en verkopen: aan- en verkoopprovisie, verwerkingskosten buitenland en lossingskosten. Bij inkomsten: inningskosten dividend en inningskosten coupon."),
array("Rendement","Procentuele, tijdgewogen waardeontwikkeling van een belegging. Maandelijks, over de rapportageperiode of over het lopende jaar (cumulatief rendement)."),
array("Resultaat","Opbrengst van een belegging. Onder te verdelen in gerealiseerd en ongerealiseerd resultaat."),
array("Rating","Een uitgevende instelling of een obligatielening kan een rating krijgen. Deze rating zegt iets over de kredietwaardigheid van de uitgevende instelling. Ratings worden uitgedrukt in letters. Een 'triple A'-rating (AAA) is de hoogst mogelijke rating. Hoe hoger de rating, des te lager het kredietrisico voor de belegger."),
array("Stortingen en onttrekkingen","Toevoegingen en onttrekkingen van gelden of stukken aan de portefeuille."),
array("Strategie","Beleggingsstrategie die is afgestemd op het afgesproken portefeuilleprofiel."),
array("Structured product","Gestructureerd financieel instrument. Vermogenssubcategorie bij zakelijke waarden."),
array("Tax Reclaim","(Vergoeding voor) terugvordering van ingehouden bronbelasting."),
array("Transactiedatum","Datum waarop transactie heeft plaatsgevonden."),
array("Transferkosten","Kosten voor het overboeken van effecten naar rekeningen elders. Inclusief BTW."),
array("Valutadatum","Datum waarop een bedrag dat is bijgeschreven rentedragend wordt of een bedrag dat is afgeschreven niet meer rentedragend is."),
array("Valutakoers","Geldkoers uitgedrukt in valuta waarin rapportage is opgemaakt."),
array("Valutatermijncontract","Termijncontract (derivatenvorm) met valuta als onderliggende waarde. Onderdeel van de vermogenscategorie liquiditeiten."),
array("Vastrentende waarden","Een van de drie vermogenscategorieën. Onder te verdelen in: alternative fixed income, derivaten en obligaties."),
array("Vergelijkingsmaatstaven","Vaste selectie van referentie-indices."),
array("Vermogenscategorie","Groepering van beleggingsinstrumenten. Er is een onderscheid in drie categorieën: liquiditeiten, vastrentende waarden en zakelijke waarden."),
array("Vermogenssubcategorie","Onderverdeling binnen de vermogenscategorieën liquiditeiten, vastrentende waarden en zakelijke waarden."),
array("Verwerkingskosten buitenland","Door externe brokers in rekening gebrachte kosten voor aan- en verkooptransacties."),
array("W/V","Winst-verliesratio: koers/kostprijs (YTD)."),
array("Weging","Procentuele omvang van een financieel instrument binnen de portefeuille."),
array("Wereld","Een combinatie van de regio's Europa, Noord-Amerika en niet-westerse markten."),
array("Yield","Rendement op een vastrentende waarde (inclusief de lossingen en coupons) tegen de huidige koers."),
array("Zakelijke waarden","Een van de drie vermogenscategorieën. Onder te verdelen in: aandelen en aandelenbeleggingsfondsen, alternative equity, derivaten en onroerend goed."));

  $aantal=count($data);
  $helft=floor($aantal/2);

  $rh=$this->pdf->rowHeight;
  $this->pdf->rowHeight=2.5;
  $counter=0;
  
  $this->pdf->SetWidths(array(30,110,35,105));
  $this->pdf->SetAligns(array('L','L','L','L'));
  $this->pdf->AddPage();
  $yPage=$this->pdf->getY();
  $this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
  $this->pdf->templateVarsOmschrijving['ENDPaginas']=$this->pdf->rapport_titel;
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);
  foreach($data as $row)
  {
    if($counter==$helft)
      $this->pdf->SetY($yPage);
    if($counter>=$helft)
      $row=array_merge(array('',''),$row);
    $this->pdf->Row($row);
    $counter++;
  }
  $this->pdf->rowHeight=$rh;
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
	}
}
?>
