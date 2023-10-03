<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/13 15:30:11 $
File Versie					: $Revision: 1.1 $

$Log: RapportEND_L85.php,v $
Revision 1.1  2019/11/13 15:30:11  rvv
*** empty log message ***

Revision 1.1  2017/12/09 17:54:25  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L85
{
	function RapportEND_L85($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    if($this->pdf->rapport_taal==1)
		{
      $this->pdf->Row(array('', "The purpose of this suitability report is to determine whether the asset management carried out by us is (still) suitable for you. The suitability assessment enables us to be able to act (permanently) in your interest.

In this context, we assess whether your investment portfolio (still) matches your investment profile. Your investment profile is based on your investment objectives, investment horizon, financial situation, risk appetite (both emotionally and your ability to bear investment risks financially) and knowledge and experience. This information is filed in the client and investment profile inventory form, the asset management agreement and (possibly) other information available to us.

We believe that our asset management service and the investment portfolio managed by us (including related transactions) are currently still suitable for you. However, if your personal and / or financial circumstances have changed, we would like to hear from you as soon as possible. We will in that case, assess whether the changes will have consequences for your investment profile and the management of your assets.

We will consult with you on an annual basis to update your personal and financial circumstances and will evaluate our asset management services with you."));
		}
		else
    {
      $this->pdf->Row(array('', "Deze geschiktheidsrapportage heeft tot doel vast te stellen of het door ons verrichte vermogensbeheer (nog) geschikt voor u is. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang.

In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel. Uw beleggingsprofiel is gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is o.a. vastgelegd in het inventarisatieformulier cliënt- en beleggingsprofiel, gespreksverslagen naar aanleiding van periodieke evaluatie van uw persoonlijke en financiële omstandigheden en uitgangspunten, en/of (eventueel) overige bij ons beschikbare informatie.

Wij hebben met betrekking tot uw beleggingsportefeuille de volgende aspecten beoordeeld:"));
  
      $this->pdf->SetWidths(array(10,60,180));
      $this->pdf->ln();
      $this->pdf->CellBorders=array('',array('T','L','U','R'),array('T','U','R'));
      $this->pdf->Row(array('','Beoordelingsaspect','Toelichting'));
      $this->pdf->CellBorders=array('',array('L','U','R'),array('U','R'));
      $this->pdf->Row(array('','',''));
      $items=array('Beleggingsprofiel'=>'de beleggingen vallen binnen de met u overeengekomen bandbreedtes voor aandelen en obligaties',
                   'Doelstellingen'=>'bezien op portefeuilleniveau, de beleggingen passen bij uw beleggingsdoelstellingen',
                   'Beleggingshorizon'=>'bezien op portefeuilleniveau, de beleggingen zijn in lijn met uw beleggingshorizon ofwel gewenste looptijd ',
                   'Verliescapaciteit / financiële positie'=>'bezien op portefeuilleniveau, u kunt eventuele verliezen met de beleggingen financieel dragen en ze zijn geschikt in relatie tot uw financiële positie',
                   'Risicobereidheid '=>'bezien op portefeuilleniveau, u bent bereid (emotioneel gezien) de risico’s met de beleggingen te accepteren',
                   'Kennis en ervaring'=>'u heeft voor zover nodig voldoende kennis en ervaring om kenmerken en risico’s van de beleggingen te doorgronden',
                   'Spreiding '=>'bezien op portefeuilleniveau, de beleggingen leiden tot voldoende spreiding (% weging, geografisch, sectoren)',
                   'Eventuele specifieke voorkeuren en andere kenmerken'=>'er is bij de beleggingen / transacties rekening gehouden met uw eventuele specifieke voorkeuren en andere kenmerken.');
      foreach($items as $key=>$value)
			{
				$this->pdf->row(array('',$key,$value));
			}
			unset( $this->pdf->CellBorders);
			$this->pdf->ln();
      $this->pdf->SetWidths(array(10,250));
      $this->pdf->Row(array('', "Wij zijn van mening dat onze vermogensbeheerdienstverlening en de door ons voor u beheerde
beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn.

Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggingsprofiel en het beheer van uw beleggingsportefeuille.

Wij zullen verder ten minste jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer. Hierbij zullen wij ook ingaan op de haalbaarheid van uw beleggingsdoelstelling(en).
"));
    }
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
	}
}
?>
