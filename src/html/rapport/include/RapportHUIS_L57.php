<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/17 15:38:53 $
File Versie					: $Revision: 1.3 $

$Log: RapportHUIS_L57.php,v $
Revision 1.3  2020/06/17 15:38:53  rvv
*** empty log message ***

Revision 1.2  2020/03/01 09:53:26  rvv
*** empty log message ***

Revision 1.1  2020/02/29 16:24:09  rvv
*** empty log message ***

Revision 1.5  2016/09/21 16:09:23  rvv
*** empty log message ***

Revision 1.4  2016/09/04 14:42:06  rvv
*** empty log message ***

Revision 1.3  2016/08/31 16:18:01  rvv
*** empty log message ***

Revision 1.2  2016/08/13 16:55:26  rvv
*** empty log message ***

Revision 1.1  2016/07/02 09:36:54  rvv
*** empty log message ***

Revision 1.3  2016/06/30 06:28:24  rvv
*** empty log message ***

Revision 1.2  2016/06/29 16:04:07  rvv
*** empty log message ***

Revision 1.1  2016/05/29 10:19:26  rvv
*** empty log message ***

Revision 1.1  2016/05/15 17:15:00  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHUIS_L57
{
	function RapportHUIS_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul = db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul = db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;

    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(280,4, 'Geschiktheidsrapportage', 0, "L");
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

    $text='Deze geschiktheidsrapportage heeft tot doel vast te stellen of het door ons verrichte vermogensbeheer (nog) geschikt voor u is. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang.

In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw doelrisicoprofiel. Uw doelrisicoprofiel is gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is o.a. vastgelegd in het inventarisatieformulier cliënt- en doelrisicoprofiel, een bijlage bij de vermogensbeheerovereenkomst, gespreksverslagen naar aanleiding van periodieke evaluatie van uw persoonlijke en financiële omstandigheden en uitgangspunten, en/of (eventueel) overige bij ons beschikbare informatie.

Wij hebben met betrekking tot uw beleggingsportefeuille de volgende aspecten beoordeeld:
';

    $this->pdf->MultiCell(280,4, $text, 0, "J");

    $this->pdf->ln();
    $this->pdf->SetWidths(array(1.5,50, 100));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->CellBorders = array('',array('L', 'R', 'U', 'T'),array('L', 'R', 'U', 'T'));
    $this->pdf->Row(array('','Beoordelingsaspect','Toelichting'));
    $this->pdf->Row(array('','',''));
    $this->pdf->Row(array('','Doelrisicoprofiel','bezien op portefeuilleniveau, de beleggingen vallen binnen de met u overeengekomen bandbreedtes voor aandelen, vastgoed beleggingen, obligaties en liquiditeiten en alternatieve beleggingen'));
    $this->pdf->Row(array('','Doelstellingen','bezien op portefeuilleniveau, de beleggingen passen bij uw beleggingsdoelstellingen'));
    $this->pdf->Row(array('','Beleggingshorizon','bezien op portefeuilleniveau, de beleggingen zijn in lijn met uw beleggingshorizon ofwel gewenste looptijd'));
    $this->pdf->Row(array('','Verliescapaciteit / financiële positie','bezien op portefeuilleniveau, u kunt eventuele verliezen met de beleggingen financieel dragen en ze zijn geschikt in relatie tot uw financiële positie'));
    $this->pdf->Row(array('','Risicobereidheid','bezien op portefeuilleniveau, u bent bereid (emotioneel gezien) de risico’s met de beleggingen te accepteren'));
    $this->pdf->Row(array('','Kennis en ervaring','u heeft voor zover nodig voldoende kennis en ervaring om kenmerken en risico’s van de beleggingen te doorgronden'));
    $this->pdf->Row(array('','Spreiding','bezien op portefeuilleniveau, de beleggingen leiden tot voldoende spreiding (% weging, geografisch, sectoren)'));
    $this->pdf->Row(array('','Eventuele specifieke voorkeuren en andere kenmerken','bezien op portefeuilleniveau, wij hebben rekening gehouden met uw eventuele specifieke voorkeuren en overige uitgangspunten'));


    $this->pdf->ln();




    $text="Wij zijn van mening dat onze vermogensbeheerdienstverlening en de door ons voor u beheerde beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn. 

Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw doelrisicoprofiel en het beheer van uw beleggingsportefeuille.

Wij zullen verder ten minste jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer. Hierbij zullen wij ook ingaan op de haalbaarheid van uw beleggingsdoelstelling(en).
";
    $this->pdf->MultiCell(280,4, $text, 0, "J");
    
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = null;
	}
}
?>
