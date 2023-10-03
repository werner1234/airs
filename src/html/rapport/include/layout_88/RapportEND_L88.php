<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/21 12:35:10 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportEND_L88.php,v $
 		Revision 1.1  2020/03/21 12:35:10  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/04/06 16:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/01/06 10:09:57  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/12/30 14:27:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportEND_L88
{
	function RapportEND_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Disclaimer";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
    $this->vkm->pdf->templateVarsOmschrijving['ENDPaginas']=$this->pdf->rapport_titel;
	  $this->pdf->SetWidths(array(80,150));
		$this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Deze rapportage wordt u op strikt persoonlijke en vertrouwelijke basis toegezonden door
Mpartners B.V. (“Mpartners”). Dit schrijven is uitsluitend bestemd voor de geadresseerde.
Indien de inhoud hiervan ongeautoriseerd of oneigenlijk gebruikt wordt, kan dit een inbreuk
op intellectuele eigendomsrechten, regelgeving inzake privacy, publicatie en/of
communicatie in de breedste zin van het woord opleveren. Het is niet toegestaan om
gebruikte maken van de aangeboden informatie dan wel deze op enigerlei wijze te kopiëren,
te verspreiden of anderszins openbaar te maken zonder uitdrukkelijke schriftelijke
toestemming van Mpartners.
Deze rapportage is uitsluitend bedoeld ter informatie voor de cliënt en vormt in geen geval
een aanbieding van diensten in een land waarin een dergelijke aanbieding wettelijk niet
toegestaan is zonder aanvullende eisen (zoals een vergunning). Hoewel alle redelijke zorg is
betracht ten aanzien van de juistheid van gegevens, kan Mpartners niet aansprakelijk
worden gesteld voor de inhoud van deze productie.
De waarde van uw beleggingen kan fluctueren. In het verleden behaalde resultaten bieden
geen garantie voor de toekomst.
Mpartners is een beleggingsonderneming met een vergunning als bedoeld in artikel 2:96
Wet op het financieel toezicht (“Wft”) op basis waarvan Mpartners beleggingsdiensten als
bedoeld in artikel 1:1 Wft, onderdeel a, c en d van de definitie van het \'verlenen van een
beleggingsdienst\' mag verlenen. Mpartners staat aldus onder toezicht van en is
geregistreerd bij de Autoriteit Financiële Markten en De Nederlandsche Bank.
Mpartners heeft haar statutaire zetel te Amsterdam en is ingeschreven in het
Handelsregister van de Kamer van Koophandel te Amsterdam onder nummer 34389387
0000.'));


  }
}
?>