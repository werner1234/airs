<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/07/11 16:16:40 $
File Versie					: $Revision: 1.1 $

$Log: RapportHUIS_L75.php,v $
Revision 1.1  2018/07/11 16:16:40  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportHUIS_L75
{
	function RapportHUIS_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->portefeuille=$portefeuille;
		$this->rapportageDatumVanaf=$rapportageDatumVanaf;
		$this->rapportageDatum=$rapportageDatum;

		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Disclaimer";

	}

	function writeRapport()
	{
	  global $__appvar;
      $this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$txtArray=array(array('body'=>'De inhoud van deze vermogensrapportage is samengesteld door Valyoux Vermogens-management B.V., hierna genoemd VALYOUX. VALYOUX is geen vermogensbeheerder en is slechts geregistreerd als Financieel Dienstverlener onder nummer 12016510 bij de AFM. Gebruik van deze vermogensrapportage is onderworpen aan de onderstaande voorwaarden en beperkingen. Gebruik van deze vermogensrapportage betekent dat de gebruiker instemt met het onderstaande en de Algemene Voorwaarden van VALYOUX.'),
			array('kop'=>'Gebruik vermogensrapportage','body'=>'Niets uit deze vermogensrapportage dient te worden opgevat als uitnodiging, aanbod of aanbeveling om financiële instrumenten te kopen of te verkopen, noch dient de informatie als juridisch, fiscaal of administratief advies of als beleggingsadvies. De vermogensrapportage wordt uitsluitend voor informatieve doeleinden en onafhankelijke performance meting ter beschikking gesteld en kan zonder kennisgeving worden gewijzigd. Hoewel wij alle redelijke zorg hebben betracht om ervoor te zorgen dat de informatie in deze vermogensrapportage juist is op het moment van publicatie, geven wij geen enkele garantie, impliciet dan wel expliciet, over de juistheid, betrouwbaarheid, rechtmatigheid, volledigheid of actualiteit van deze informatie.'),
			array('kop'=>'Rendementen','body'=>'Voor alle in deze vermogensrapportage genoemde beleggingsrendementen geldt dat de waarde van beleggingen kan fluctueren en dat rendementen uit het verleden geen garantie bieden voor toekomstig te behalen rendementen. Evenmin is rekening gehouden met de invloed van persoonlijke belastingposities, tenzij expliciet aangegeven. '),
			array('kop'=>'Uitsluiten aansprakelijkheid','body'=>'De informatie en meningen die in deze vermogensrapportage staan worden gegeven zonder enige garantie, expliciet of impliciet, voor zover dit mogelijk is op basis van de toepasselijke wetgeving. Wij aanvaarden geen enkele aansprakelijkheid voor schade (waaronder directe of indirecte, bijzondere, incidentele schade of gevolgschade), fiscale effecten, geleden verliezen of gemaakte kosten in verband met of als gevolg van het gebruik van deze vermogensrapportage.'),
			array('kop'=>'Informatie van derden','body'=>'Deze vermogensrapportage is mede samengesteld uit bronnen die door anderen dan VALYOUX worden beheerd, zoals bijvoorbeeld depotbanken en andere externe (financiële) instellingen. Wij zijn niet verantwoordelijk voor en onderschrijven en aanvaarden geen enkele verantwoordelijkheid voor de inhoud of het gebruik van deze externe bronnen.'),
			array('kop'=>'Intellectuele eigendomsrechten','body'=>'De rechten op deze vermogensrapportage en de daarin aangeboden informatie, behoren toe aan VALYOUX of haar toeleveranciers en zijn beschermd op basis van het auteursrecht en andere intellectuele eigendomsrechten. Behoudens voor persoonlijk en niet-commercieel gebruik, mag de aangeboden informatie en/of andere delen van deze vermogensrapportage niet worden verveelvoudigd, opgeslagen in een geautomatiseerd gegevensbestand, of openbaar gemaakt, in enige vorm of op enige wijze, hetzij elektronisch, hetzij mechanisch, door fotokopieën, opnamen of enig andere manier, zonder voorafgaande schriftelijke toestemming van VALYOUX. In het bijzonder is het de bezitter uitdrukkelijk niet toegestaan informatie van deze vermogensrapportage geheel of gedeeltelijk door te zenden naar derden of publiek te maken via nieuws groepen, mailinglijsten, elektronische prikborden, chat boxen of daarmee vergelijkbare discussiefora zonder voorafgaande schriftelijke toestemming van VALYOUX.'),
			array('kop'=>'Toepasselijk recht','body'=>'Alle zaken die betrekking hebben op gebruik van en de inhoud van deze vermogensrapportage wordt beheerst door Nederlands recht.'),
		);
		$this->pdf->setY(20);
		$this->pdf->setAligns(array('L','L'));
		$this->pdf->setWidths(array(297-$this->pdf->marge*2));
    foreach($txtArray as $blok)
		{


				$this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
				$this->pdf->row(array($blok['kop']));
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
				$this->pdf->row(array($blok['body']));
			$this->pdf->ln();

		}



	}


}
?>