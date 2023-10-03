<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/30 15:30:39 $
File Versie					: $Revision: 1.3 $

$Log: RapportEND_L67.php,v $
Revision 1.3  2020/05/30 15:30:39  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L67
{
	function RapportEND_L67($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    if($this->pdf->rapport_taal==1)
    {
      $data = array(
        array('Accrued interest', 'Interest on certain fixed-income securities that has not yet been paid. In the \'Performance of Assets\' section this is offset against the previous month for accounting purposes. '),
        array('Alternative investments ', 'Non-traditional financial instruments with the (underlying) characteristics of equities & real estate, and fixed-income instruments, such as hedge funds or private equity. They are part of the asset classes equities & real estate and fixed income. '),
        array('Asset class ', 'Category of investment instruments. We distinguish three asset classes: liquid assets, fixed income and equities & real estate.'),
        array('Banking fee ', 'Fee charged for banking services such as the provision of bank guarantees and bank references.'),
        array('Benchmarking ', 'Comparing the portfolio with (a) benchmark(s). A benchmark is a standard against which all or part of the portfolio can be compared. Benchmarks are usually an index. '),
        array('Buying and selling', 'Investments bought and sold during the reporting period, including funds placed into and withdrawals from deposit accounts. '),
        array('Cash flow projection', 'A projection of future income from coupons, interest and redemption payments.'),
        array('Collection charges', 'Commission on coupon/dividend payments.'),
        array('Commission', 'For purchases and sell transactions: fees charged for purchasing and selling, processing costs of international trades and redemption fees. For income: dividend and coupon collection charges. '),
        array('Contribution', 'That part of a portfolio’s total return that can be attributed to an asset class’s return during a reporting period or for the current calendar year (cumulative contribution). '),
        array('Cost', 'Historical price for which a security is purchased or deposited, adjusted for interim purchases, deposits and corporate actions.'),
        array('Costs and taxes ', 'Fees charged, such as: advisory fees, management fees, taxes and commission.Negative costs are possible as a result of adjustments to cost items where the adjustment is higher than the initial cost.'),
        array('Coupon ', 'The interest rate paid at regular intervals on interest-bearing fixed income securities.'),
        array('Coupon date', 'Date on which coupon is paid.'),
        array('Credit rating ', 'A credit rating is an assessment by a rating agency of the creditworthiness of an issuing organisation or bond, indicated by letter grades. The highest possible rating is ‘triple A’ (AAA). The higher the rating, the lower the credit risk run by the investor. '),
        array('Date of valuation', 'The date on which a stock’s price is established. The date of valuation does not necessarily coincide with the reporting date, in particular in the case of unlisted investment funds. '),
        array('Deposits and withdrawals ', 'Additions to, and withdrawal of funds or securities from a portfolio. '),
        array('Derivatives', 'Options, futures contracts and warrants. These instruments are products derived from underlying assets such as equities, indices, currencies and commodities. Derivatives are found in each of the three asset classes.'),
        array('Dividend tax', 'Tax on dividends received.'),
        array('Duration ', 'A measure of the potential risk of interest rate sensitivity. The longer the term to maturity, the higher the duration and the more sharply prices will respond to interest rate changes. If interest rates rise or fall by 1%, the value of a bond will fluctuate by the duration times 1%.'),
        array('Emerging markets ', 'All markets not part of Europe and North America'),
        array('Equities & real estate', 'One of the three asset classes, broken down into: equities and equity investment funds, alternative equities and derivatives, and real estate.'),
        array('Exchange rate', 'Rate of exchange of the reporting currency.'),
        array('Final capital', 'Value of assets at the end of a period.'),
        array('Fixed income', 'One of the three asset classes. Includes: alternative fixed income, derivatives and bonds.'),
        array('Forward exchange contract', 'Futures contract (a type of derivative) based on a currency. Asset class: liquid assets.'),
        array('FX ', 'See definition of \'exchange rate\'.'),
        array('Gross income', 'Income including commissions and taxes.'),
        array('Gross transaction amount', 'Value of transaction including commission fees.'),
        array('Initial capital', 'Value of assets at the start of a period.'),
        array('Investment goal', 'The goal agreed with the client which forms the starting point for the portfolio composition. '),
        array('Investment horizon', 'The period agreed with the client during which the assets are available for investment and during which the investment goals are pursued. '),
        array('Investment restrictions', 'Restrictions agreed with the client and special arrangements relating to investments in the portfolio. These apply only to asset management.'),
        array('Liquid assets', 'One of the three asset classes. Liquid assets include money market funds,current account balances, time deposits and forward exchange contracts.'),
        array('Management fee', 'Fee charged for the management, custody and administration of a portfolio. In the case of all-in asset management, the fee also includes buying and selling commissions. Management fees are payable every quarter. '),
        array('Maturity', 'Residual maturity of a fixed income security.'),
        array('Measure of reference', 'A fixed selection of reference indices.'),
        array('Money market fund', 'An investment fund that invests primarily in cash, (short-term) deposits and debt instruments. Asset class: liquid assets. '),
        array('Net income', 'Income net of commissions and taxes.'),
        array('Net transaction', 'Value of the transaction including commission.'),
        array('North America ', 'Canada and the United States.'),
        array('Other costs', 'All costs not specified in any of the other cost items. '),
        array('Other transactions', 'Movements in the portfolio resulting from corporate actions (actions by an issuing entity that affect securities already issued).'),
        array('Portfolio profile', 'The agreed profile which determines the composition of the portfolio across asset classes, in accordance with set standards and bandwidths.'),
        array('Portfolio weighting', 'The proportion of an asset class in the total portfolio, in percentages.'),
        array('Processing costs international trades ', 'Costs charged by external brokers for purchase and selling transactions.'),
        array('Profit/loss ratio ', 'The ratio between market price and cost (YTD).'),
        array('Purchase and sell commission', 'Fees charged for purchase and selling transactions.'),
        array('Realised gain/loss', 'Gains or losses made on the sale or withdrawal of investments, against cost.'),
        array('Redemption', 'Redemption is the return of an investor\'s principal in a fixed-income security.'),
        array('Redemption fee', 'Fee charged for redemption of a fixed income security.'),
        array('Return', 'The percentage, time-weighted change in the value of an investment. Either monthly, during a reporting period, or for the current calendar year (cumulative return).'),
        array('Return on investment ', 'Return on an investment, comprising realised and unrealised gains/losses.'),
        array('Revenues', 'Coupon, dividend and other income.'),
        array('Strategy', 'Investment strategy aligned with the portfolio profile opted for.'),
        array('Structured product ', 'Structured financial instrument. Sub-asset class of equities & real estate.'),
        array('Sub-asset class', 'A subclass of the asset classes liquid assets, fixed income and equities & real estate.'),
        array('Tax reclaim  ', 'Fee for reclaim of withholding tax.'),
        array('Total return ', 'Realised capital gain/loss + unrealised gain/loss + income + accrued interest + costs.'),
        array('Transaction date', 'Date on which a transaction took place.'),
        array('Transfer fee', 'Fee for transferring securities from one account to an account held elsewhere. The fee includes VAT.'),
        array('Unrealised gain/loss ', 'The difference between the value of an investment at a particular point in time in a calendar year and its value based on cost (a standard item of portfolio statements) or YTD cost (in the \'Performance of Assets\' section). If, in the latter case, gains/losses have been realised in a previous calendar year, the unrealised gain/loss is adjusted for accounting purposes.'),
        array('Value date', 'Date on which an amount deposited into an account starts to bear interest, or on which an amount withdrawn from an account no longer bears interest.'),
        array('VAT', 'Value added tax due.'),
        array('Weighting', 'Percentage composition of a financial instrument in a portfolio.'),
        array('Withholding Tax', 'Taxes withheld at source on dividend payments. '),
        array('World  ', 'A combination of the regions Europe, North America and non-Western markets.'),
        array('Yield ', 'Return on fixed-income securities (including redemption and coupon payments) at current market price.'),
        array('YTD cost', 'Price of a security at the start of the calendar year or at the time of purchase/deposit in that same year, adjusted for interim purchases, deposits and corporate actions.'));
    }
    else
    {
      $data = array(
        array("Aan- en verkoopprovisie", "In rekening gebrachte kosten voor aan- en verkooptransacties."),
        array("Aan- en verkopen", "Gekochte en verkochte beleggingen gedurende de rapportageperiode, waaronder stortingen in en onttrekkingen uit deposito's."),
        array("Adviesvergoeding", "Kosten voor advisering, bewaring en administratie van de portefeuille. Kosten worden eenmaal per kwartaal in rekening gebracht."),
        array("Alternative equity/fixed", "Niet-traditioneel financieel instrument met (onderliggende) karakteristieken van zakelijke resp. vastrentende waarden, zoals een hedge fund of een private equity beleggingsvorm. Onderdeel van zakelijke resp. vastrentende waarden."),
        array("Banking fee", "Bancaire kosten zoals kosten voor het verlenen van bankgaranties, bankverklaringen en andere bancaire diensten."),
        array("Beginvermogen", "Waarde vermogen aan het begin van een periode."),
        array("Beheervergoeding", "Kosten voor beheer, bewaring en administratie van de portefeuille. In geval van all-in vermogensbeheer tevens inclusief aan- en verkoopprovisie. Kosten worden eenmaal per kwartaal in rekening gebracht."),
        array("Belasting", "Ingehouden bronbelasting op uitkeringen van dividend."),
        array("Beleggingsdoelstelling", "De afgesproken doelstelling die ten grondslag ligt aan de invulling van de portefeuille."),
        array("Beleggingshorizon", "De afgesproken periode waarin het vermogen beschikbaar is voor beleggingsdoeleinden en waarin getracht wordt de beleggingsdoelstelling te realiseren."),
        array("Beleggingsrestricties", "De afgesproken beperkingen en bijzondere afspraken voor de beleggingen in de portefeuille. Alleen van toepassing bij vermogensbeheer."),
        array("Beleggingsresultaat", "Gerealiseerd resultaat + ongerealiseerd resultaat + inkomsten + opgelopen rente + kosten."),
        array("Benchmarkvergelijking", "Vergelijking van de portefeuille met (een) benchmark(s). Een benchmark is een maatstaf waarmee (delen van) een portefeuille vergeleken kunnen worden. Het gaat hier meestal om een index. Deze maakt niet standaard onderdeel uit van de rapportage."),
        array("Bewaarloon", "In rekening gebrachte kosten voor het bewaren en administreren van effecten. Kosten worden eenmalig vooraf aan het begin van een jaar in rekening gebracht."),
        array("Bruto transactie", "Waarde transactie exclusief provisie."),
        array("Bruto-inkomsten", "Inkomsten voor aftrek van provisie en belasting."),
        array("BTW", "Af te dragen belasting toegevoegde waarde."),
        array("Contributie", "Dat deel van het totale rendement van de portefeuille dat kan worden toegerekend aan het rendement van een vermogenscategorie. Tijdens een rapportageperiode of over het lopende jaar (contributie cumulatief)."),
        array("Coupon", "De rentevergoeding die met regelmaat op rentedragende vastrentende waarden wordt betaald."),
        array("Coupondatum", "Datum waarop coupon wordt uitgekeerd."),
        array("Derivaat", "Opties, termijncontracten en warrants. Dit zijn afgeleide producten van een onderliggende waarde zoals aandelen, indices, valuta's of commodities. Derivaten kunnen worden onderscheiden binnen ieder van de drie vermogenscategorieën."),
        array("Dividendbelasting", "Belasting op ontvangen dividenden."),
        array("Duration", "Risicomaatstaf voor rentegevoeligheid. Hoe langer de resterende looptijd, des te hoger de duration. Hoe hoger de duration, des te sterker reageert de koers op een renteverandering. Stijgt of daalt de rente met 1%, dan fluctueert de waarde van de obligatie met 1% maal de duration."),
        array("Eindvermogen", "Waarde vermogen aan het einde van een periode."),
        array("Europa", "België, Denemarken, Duitsland, Finland, Frankrijk, Griekenland, Ierland, Italië, Luxemburg, Nederland, Noorwegen, Oostenrijk, Portugal, Spanje, Verenigd Koninkrijk, Zweden, Zwitserland."),
        array("FX", "Zie definitie 'valutakoers'."),
        array("Geldmarktfonds", "Beleggingsfonds dat (hoofdzakelijk) belegt in (kortlopende) deposito's en schuldpapier. Onderdeel van liquiditeiten."),
        array("Gerealiseerd resultaat", "Gerealiseerde winst of verlies door verkoop of onttrekking van een belegging, afgezet tegen kostprijs."),
        array("Inkomsten", "Coupon, dividend en overige inkomsten."),
        array("Inningskosten", "Provisie op coupon-/dividenduitkeringen."),
        array("Kasstroomprojectie", "Projectie van toekomstige inkomsten uit coupon, rente en lossingen."),
        array("Koersdatum", "De datum waarop de koers is vastgesteld. De koersdatum kan afwijken van een rapportagedatum, met name bij niet ter beurze genoteerde beleggingsfondsen."),
        array("Kosten en belastingen", "In rekening gebrachte kosten zoals: adviesvergoeding, beheervergoeding, belastingen en provisie. Negatieve kosten zijn mogelijk als gevolg van correcties op kostenboekingen waarbij de correctie hoger is dan de initiële kostenpost."),
        array("Kostprijs", "Historische koers waartegen een belegging is gekocht of gestort, gecorrigeerd voor tussentijdse aankopen, stortingen en bepaalde corporate actions."),
        array("Kostprijs YTD", "Koers van een belegging aan het begin van een kalenderjaar dan wel op het moment van aankoop/storting in dat jaar, gecorrigeerd voor tussentijdse aankopen, stortingen en bepaalde corporate actions."),
        array("Liquiditeiten", "Een van de drie vermogenscategorieën. Onder te verdelen in: geldmarktfondsen, rekeningcourant, termijndeposito's en valutatermijncontracten."),
        array("Looptijd", "Resterende looptijd van een vastrentende waarde."),
        array("Lossing", "Vrijgekomen vastrentende waarde."),
        array("Lossingskosten", "Provisie op lossingen van vastrentende waarden."),
        array("Netto transactie", "Waarde transactie inclusief provisie."),
        array("Netto-inkomsten", "Inkomsten na aftrek van provisie en belasting."),
        array("Niet-westerse markten", "Alle markten die niet worden genoemd onder 'Europa' en 'Noord-Amerika'."),
        array("Noord-Amerika", "Canada, Verenigde Staten."),
        array("Ongerealiseerd resultaat", "Het verschil tussen de waarde van een belegging op een bepaald moment in een kalenderjaar en de waarde gebaseerd op kostprijs (standaard in positie-overzichten) of kostprijs YTD (in het onderdeel 'Vermogensontwikkeling'). Als er in het laatste geval sprake is van gerealiseerd resultaat in een eerder kalenderjaar, wordt ongerealiseerd resultaat hiervoor om boekhoudkundige redenen gecorrigeerd."),
        array("Opgelopen rente", "Nog niet uitgekeerde rente op bepaalde vastrentende waarden. In het onderdeel 'Vermogensontwikkeling' betreft het om boekhoudkundige redenen een saldering t.o.v. de voorgaande maand."),
        array("Overige kosten", "Kosten die niet vallen onder een van de andere kostenposten. Overige transacties Wijzigingen in de portefeuille uit hoofde van corporate actions (acties van een uitgevende instelling die invloed hebben op het door haar uitgegeven effect)."),
        array("Portefeuilleprofiel", "Afgesproken profiel op basis waarvan de portefeuille is verdeeld over vermogenscategorieën, volgens vastgestelde normen en bandbreedtes."),
        array("Portefeuilleweging", "Procentuele waarde van een vermogenscategorie binnen de totale portefeuille."),
        array("Provisie", "Bij aan- en verkopen: aan- en verkoopprovisie, verwerkingskosten buitenland en lossingskosten. Bij inkomsten: inningskosten dividend en inningskosten coupon."),
        array("Rendement", "Procentuele, tijdgewogen waardeontwikkeling van een belegging. Maandelijks, over de rapportageperiode of over het lopende jaar (cumulatief rendement)."),
        array("Resultaat", "Opbrengst van een belegging. Onder te verdelen in gerealiseerd en ongerealiseerd resultaat."),
        array("Rating", "Een uitgevende instelling of een obligatielening kan een rating krijgen. Deze rating zegt iets over de kredietwaardigheid van de uitgevende instelling. Ratings worden uitgedrukt in letters. Een 'triple A'-rating (AAA) is de hoogst mogelijke rating. Hoe hoger de rating, des te lager het kredietrisico voor de belegger."),
        array("Stortingen en onttrekkingen", "Toevoegingen en onttrekkingen van gelden of stukken aan de portefeuille."),
        array("Strategie", "Beleggingsstrategie die is afgestemd op het afgesproken portefeuilleprofiel."),
        array("Structured product", "Gestructureerd financieel instrument. Vermogenssubcategorie bij zakelijke waarden."),
        array("Tax Reclaim", "(Vergoeding voor) terugvordering van ingehouden bronbelasting."),
        array("Transactiedatum", "Datum waarop transactie heeft plaatsgevonden."),
        array("Transferkosten", "Kosten voor het overboeken van effecten naar rekeningen elders. Inclusief BTW."),
        array("Valutadatum", "Datum waarop een bedrag dat is bijgeschreven rentedragend wordt of een bedrag dat is afgeschreven niet meer rentedragend is."),
        array("Valutakoers", "Geldkoers uitgedrukt in valuta waarin rapportage is opgemaakt."),
        array("Valutatermijncontract", "Termijncontract (derivatenvorm) met valuta als onderliggende waarde. Onderdeel van de vermogenscategorie liquiditeiten."),
        array("Vastrentende waarden", "Een van de drie vermogenscategorieën. Onder te verdelen in: alternative fixed income, derivaten en obligaties."),
        array("Vergelijkingsmaatstaven", "Vaste selectie van referentie-indices."),
        array("Vermogenscategorie", "Groepering van beleggingsinstrumenten. Er is een onderscheid in drie categorieën: liquiditeiten, vastrentende waarden en zakelijke waarden."),
        array("Vermogenssubcategorie", "Onderverdeling binnen de vermogenscategorieën liquiditeiten, vastrentende waarden en zakelijke waarden."),
        array("Verwerkingskosten buitenland", "Door externe brokers in rekening gebrachte kosten voor aan- en verkooptransacties."),
        array("W/V", "Winst-verliesratio: koers/kostprijs (YTD)."),
        array("Weging", "Procentuele omvang van een financieel instrument binnen de portefeuille."),
        array("Wereld", "Een combinatie van de regio's Europa, Noord-Amerika en niet-westerse markten."),
        array("Yield", "Rendement op een vastrentende waarde (inclusief de lossingen en coupons) tegen de huidige koers."),
        array("Zakelijke waarden", "Een van de drie vermogenscategorieën. Onder te verdelen in: aandelen en aandelenbeleggingsfondsen, alternative equity, derivaten en onroerend goed."));
    }

  $aantal=count($data);
  $helft=floor($aantal/2);

  $rh=$this->pdf->rowHeight;
  $this->pdf->rowHeight=2.7;
  $counter=0;
  
  $this->pdf->SetWidths(array(30,110,35,105));
  $this->pdf->SetAligns(array('L','L','L','L'));
  $this->pdf->AddPage();
    if($this->pdf->rapport_taal==1)
      $this->pdf->ln();
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
