<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/12/06 16:50:06 $
File Versie					: $Revision: 1.1 $

$Log: RapportEND_L36.php,v $
Revision 1.1  2017/12/06 16:50:06  rvv
*** empty log message ***

Revision 1.1  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.2  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L36
{
	function RapportEND_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
   $this->pdf->Row(array('',"Deze geschiktheidsrapportage heeft tot doel vast te stellen of het door ons verrichte vermogensbeheer (nog) geschikt voor u is. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang.

In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel. Uw beleggingsprofiel is gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is o.a. vastgelegd in het inventarisatieformulier cliënt- en beleggingsprofiel, het beleggingsvoorstel, gespreksverslagen naar aanleiding van periodieke evaluatie van uw persoonlijke en financiële omstandigheden en uitgangspunten, en/of (eventueel) overige bij ons beschikbare informatie.

Wij hebben met betrekking tot uw beleggingsportefeuille de volgende aspecten beoordeeld:
"));


    $this->pdf->ln();
    $this->pdf->SetWidths(array(11,50, 100));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->CellBorders = array('',array('L', 'R', 'U', 'T'),array('L', 'R', 'U', 'T'));
    $this->pdf->Row(array('','Beoordelingsaspect','Toelichting'));
    $this->pdf->Row(array('','',''));
    $this->pdf->Row(array('','Beleggingsprofiel','bezien op portefeuilleniveau, de beleggingen vallen binnen de met u overeengekomen bandbreedtes voor zakelijke en vastrentende waarden'));
    $this->pdf->Row(array('','Doelstellingen','bezien op portefeuilleniveau, de beleggingen passen bij uw beleggingsdoelstellingen'));
    $this->pdf->Row(array('','Beleggingshorizon ','bezien op portefeuilleniveau, de beleggingen zijn in lijn met uw beleggingshorizon ofwel gewenste looptijd'));
    $this->pdf->Row(array('','Verliescapaciteit / financiële positie','bezien op portefeuilleniveau, u kunt eventuele verliezen met de beleggingen financieel dragen en ze zijn geschikt in relatie tot uw financiële positie'));
    $this->pdf->Row(array('','Risicobereidheid','bezien op portefeuilleniveau, u bent bereid (emotioneel gezien) de risico’s met de beleggingen te accepteren'));
    $this->pdf->Row(array('','Kennis en ervaring','u heeft voor zover nodig voldoende kennis en ervaring om kenmerken en risico’s van de beleggingen te doorgronden'));
    $this->pdf->Row(array('','Spreiding','bezien op portefeuilleniveau, de beleggingen leiden tot voldoende spreiding (% weging, geografisch, sectoren)'));
    $this->pdf->Row(array('','Eventuele specifieke voorkeuren en andere kenmerken','bezien op portefeuilleniveau, wij hebben rekening gehouden met uw eventuele specifieke voorkeuren en overige uitgangspunten'));

    $this->pdf->ln();


    $this->pdf->CellBorders = null;
    $this->pdf->SetWidths(array(10,250));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->Row(array('',"Wij zijn van mening dat onze vermogensbeheerdienstverlening en de door ons voor u beheerde
beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn. 

Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggingsprofiel en het beheer van uw beleggingsportefeuille.

Wij zullen verder ten minste jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer. Hierbij zullen wij ook ingaan op de haalbaarheid van uw beleggingsdoelstelling(en).
"));


  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
	}
}
?>
