<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/04/07 15:21:44 $
File Versie					: $Revision: 1.4 $

$Log: RapportEND_L73.php,v $
Revision 1.4  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.3  2017/10/07 16:54:34  rvv
*** empty log message ***

Revision 1.2  2017/07/01 17:03:24  rvv
*** empty log message ***

Revision 1.1  2017/05/24 08:47:34  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportEND_L73
{
	function RapportEND_L73($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');


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

  $counter=0;
  
  $this->pdf->SetWidths(array(110,35,105));
  $this->pdf->SetAligns(array('L','L','L','L'));
  $this->pdf->AddPage();
  $yPage=$this->pdf->getY();
  $this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
  $this->pdf->templateVarsOmschrijving['ENDPaginas']=$this->pdf->rapport_titel;

  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2);
  $this->pdf->ln();
  $this->pdf->setTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
  $this->pdf->Row(array(vertaalTekst('Disclaimer en juridische kennisgeving',$this->pdf->rapport_taal)));
  $this->pdf->setTextColor(0);

  $this->pdf->ln(2);
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->Row(array(vertaalTekst('Deze rapportage betreffende uw beleggingsportefeuille is strikt persoonlijk en vertrouwelijk. De rapportage is met de nodige zorg samengesteld en beoogt een feitelijke weergave te geven van uw beleggingsportefeuille. Bij constatering van een onjuistheid of onvolledigheid in deze rapportage, verzoeken wij u vriendelijk uw contactpersoon bij uw vermogensbeheerder/-adviseur zo spoedig mogelijk in kennis te stellen. De posities van de portefeuille worden gewaardeerd tegen de bij Alpha Capital Asset Management B.V. laatst bekende koersen op de datum van opmaak van de rapportage. Met name van niet-beursgenoteerde effecten is daarom niet altijd de actualiteit te garanderen. Beleggingen zijn omgeven met risico. In het verleden behaalde rendementen vormen derhalve geen garantie voor toekomstige resultaten. U kunt geen rechten ontlenen aan de inhoud van deze rapportage.',$this->pdf->rapport_taal)));

  $this->pdf->ln();
  $this->toonZorgplicht();


  $this->pdf->AddPage();
  $this->pdf->rowHeight=2.7;
  $this->pdf->SetWidths(array(30,110,35,105));
  $this->pdf->SetAligns(array('L','L','L','L'));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);
  foreach($data as $row)
  {
    if($counter==$helft)
      $this->pdf->SetY($yPage);
    if($counter>=$helft)
      $this->pdf->Row(array('','',vertaalTekst($row[0],$this->pdf->rapport_taal),vertaalTekst($row[1],$this->pdf->rapport_taal)));
    else
      $this->pdf->Row(array(vertaalTekst($row[0],$this->pdf->rapport_taal),vertaalTekst($row[1],$this->pdf->rapport_taal)));
    $counter++;
  }
  $this->pdf->rowHeight=$rh;
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
	}

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }



  function toonZorgplicht()
  {
    global $__appvar;
    $DB=new DB();

    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];

    $this->totaalWaarde=$totaalWaarde;
    $categorieWaarden=array();
    if(round($this->totaalWaarde,2) <> 0)
    {
     // listarray();exit;
      $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / " . $this->totaalWaarde . ") as percentage,
Zorgplichtcategorien.Omschrijving,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
INNER JOIN Zorgplichtcategorien ON ZorgplichtPerBeleggingscategorie.Zorgplicht = Zorgplichtcategorien.Zorgplicht AND Zorgplichtcategorien.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE TijdelijkeRapportage.Portefeuille =  '" . $this->portefeuille . "' AND
 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY ZorgplichtPerBeleggingscategorie.Zorgplicht";
      $DB->SQL($query); //echo $query;exit;
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $categorieWaarden[$data['Zorgplicht']] = $data['percentage'] * 100;
        $categorieOmschrijving[$data['Zorgplicht']] = $data['Omschrijving'];
      }
    }
    $zorgplicht = new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

//listarray($zpwaarde['conclusie']);
    //listarray($tmp);exit;

    $this->pdf->SetAligns(array('L','R','R','R','R'));
    $beginY=$this->pdf->getY();

    $this->pdf->SetWidths(array(40,20,20,20,20));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('zorgplichtcategorie',$this->pdf->rapport_taal),vertaalTekst('minimaal',$this->pdf->rapport_taal),
                      vertaalTekst('maximaal',$this->pdf->rapport_taal),vertaalTekst("werkelijk",$this->pdf->rapport_taal),vertaalTekst("conclusie",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','R','R','R','R'));
    //foreach ($tmp as $index=>$regelData)


    //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen='';
      //if($zpwaarde['categorien'][$cat]['Minimum'])
      $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      //else
      //   $min='';
      //if($zpwaarde['categorien'][$cat]['Maximum'])
      $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
      // else
      //   $max='';

      if($tmp[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,120,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,120,$this->pdf->getY(),3.9,3.9);


      $this->pdf->row(array($categorieOmschrijving[$cat],$min,$max,$this->formatGetal($categorieWaarden[$cat],1)."%"));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge,$beginY,120,count($categorieWaarden)*4+4);
  }
}
?>
