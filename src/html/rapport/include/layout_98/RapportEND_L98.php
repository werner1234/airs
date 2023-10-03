<?php
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L98
{
  function RapportEND_L98($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    
    $this->pdf->SetWidths(array(10,125));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Geschiktheidsrapportage'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    
    if(strtolower($this->pdf->portefeuilledata['SoortOvereenkomst'])=='beheer')
    {
      $this->pdf->Row(array('','Deze geschiktheidsrapportage heeft tot doel vast te stellen of het door ons verrichte vermogensbeheer (nog) geschikt voor u is. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang.

In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel. Uw beleggingsprofiel is gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is vastgelegd in de aanvaardingsformulieren, het beleggingsvoorstel en (eventueel) overige bij ons beschikbare informatie.

Wij zijn van mening dat onze vermogensbeheerdienstverlening en de door ons voor u beheerde beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn. Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggingsprofiel en het beheer over uw beleggingsportefeuille.

Wij zullen verder jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer. Hierbij zullen wij ook ingaan op de haalbaarheid van uw beleggingsdoelstelling.
'));
    }
    else
    {
      $this->pdf->Row(array('', "Deze geschiktheidsrapportage heeft tot doel vast te stellen of het door ons verrichte beleggingsadvies (nog) geschikt voor u is. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang.

In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel. Uw beleggingsprofiel is gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is vastgelegd in de aanvaardingsformulieren, het beleggingsvoorstel en (eventueel) overige bij ons beschikbare informatie.

Wij zijn van mening dat onze adviesdienstverlening en de door ons voor u geadviseerde beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn. Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggingsprofiel en het advies over uw beleggingsportefeuille.

Wij zullen verder jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer. Hierbij zullen wij ook ingaan op de haalbaarheid van uw beleggingsdoelstelling."));
    }

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
  }
}
?>
