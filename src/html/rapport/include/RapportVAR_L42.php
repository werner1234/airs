<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/02/14 16:53:20 $
File Versie					: $Revision: 1.2 $

$Log: RapportVAR_L42.php,v $
Revision 1.2  2018/02/14 16:53:20  rvv
*** empty log message ***

Revision 1.1  2018/02/10 18:09:12  rvv
*** empty log message ***

Revision 1.1  2017/12/09 17:54:25  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVAR_L42
{
	function RapportVAR_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VAR";
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
  $this->pdf->templateVars['VARPaginas']=$this->pdf->page;
  $this->pdf->templateVarsOmschrijving['VARPaginas']=$this->pdf->rapport_titel;
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetWidths(array(10,250));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->ln();
   $this->pdf->Row(array('',"Periodiek stellen wij vast of het door ons verrichte vermogensbeheer (nog) geschikt voor u is. Deze geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang.

In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel. Uw beleggingsprofiel is gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring.
Deze informatie is vastgelegd in de Informatiewijzer Beleggen en uw risico, de overeenkomst vermogensbeheer en (eventueel) overige bij ons beschikbare informatie.

Wij zijn van mening dat onze vermogensbeheerdienst en de door ons voor u beheerde beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn. Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggingsprofiel en het beheer van uw vermogen.

Wij zullen verder jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer."));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
	}
}
?>
