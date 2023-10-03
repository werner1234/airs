<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/12/09 17:54:25 $
File Versie					: $Revision: 1.1 $

$Log: RapportEND_L71.php,v $
Revision 1.1  2017/12/09 17:54:25  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L71
{
	function RapportEND_L71($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Geschiktheidsrapportage";

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

  $this->pdf->AddPage();
  $this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
  $this->pdf->templateVarsOmschrijving['ENDPaginas']=$this->pdf->rapport_titel;
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetWidths(array(10,250));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->ln();
    
    $teksten=array('Geschiktheidsrapportage'=>'Deze geschiktheidsrapportage heeft tot doel vast te stellen of het door ons verrichte vermogensbeheer (nog) geschikt voor u is. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang.

In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw beleggersprofiel. Uw beleggersprofiel is gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is o.a. vastgelegd in het inventarisatieformulier cliënt- en beleggersprofiel, het beleggingsvoorstel, vastleggingen van de periodieke evaluatie van uw persoonlijke en financiële omstandigheden en uitgangspunten, en/of (eventueel) overige bij ons beschikbare informatie.

Wij hebben met betrekking tot uw beleggingsportefeuille de volgende aspecten beoordeeld:',
'Beleggersprofiel'=>'Bezien op portefeuilleniveau, de beleggingen vallen binnen de met u overeengekomen bandbreedtes voor zakelijke en vastrentende waarden',
'Doelstellingen'=>'Bezien op portefeuilleniveau, de beleggingen passen bij uw beleggingsdoelstellingen',
'Beleggingshorizon'=>'Bezien op portefeuilleniveau, de beleggingen zijn in lijn met uw beleggingshorizon ofwel gewenste looptijd',
'Verliescapaciteit / financiële positie'=>'Bezien op portefeuilleniveau, u kunt eventuele verliezen met de beleggingen financieel dragen en ze zijn geschikt in relatie tot uw financiële positie',
'Risicobereidheid'=>'Bezien op portefeuilleniveau, u bent bereid (emotioneel gezien) de risico’s met de beleggingen te accepteren',
'Kennis en ervaring'=>'U heeft voor zover nodig voldoende kennis en ervaring om kenmerken en risico’s van de beleggingen te doorgronden',
'Spreiding'=>'Bezien op portefeuilleniveau, de beleggingen leiden tot voldoende spreiding (% weging, geografisch, sectoren)',
'Eventuele specifieke voorkeuren en andere kenmerken'=>'Bezien op portefeuilleniveau, wij hebben rekening gehouden met uw eventuele specifieke voorkeuren en overige uitgangspunten',
''=>'Wij zijn van mening dat onze vermogensbeheerdienstverlening en de door ons voor u beheerde beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn.

Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggersprofiel en het beheer van uw beleggingsportefeuille.

Wij zullen verder ten minste jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer. Hierbij zullen wij ook ingaan op de haalbaarheid van uw beleggingsdoelstelling(en).');
    foreach($teksten as $kop=>$tekst)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->Row(array('',$kop));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Row(array('',$tekst));
      $this->pdf->ln(2);
    }
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
	}
}
?>
