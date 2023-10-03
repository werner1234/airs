<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/02 15:20:30 $
File Versie					: $Revision: 1.3 $

$Log: RapportEND_L54.php,v $
Revision 1.3  2019/11/02 15:20:30  rvv
*** empty log message ***

Revision 1.2  2019/10/11 17:40:07  rvv
*** empty log message ***

Revision 1.1  2019/01/16 08:41:15  rvv
*** empty log message ***

Revision 1.6  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.5  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.4  2015/02/15 10:36:54  rvv
*** empty log message ***

Revision 1.3  2015/02/07 20:37:51  rvv
*** empty log message ***

Revision 1.2  2015/01/17 18:32:01  rvv
*** empty log message ***

Revision 1.1  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.1  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.3  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.2  2014/04/23 16:18:44  rvv
*** empty log message ***

Revision 1.1  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportEND_L54
{
	function RapportEND_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";
		$this->portefeuille=$portefeuille;
	}

	function writeRapport()
	{
	  global $__appvar;

    $this->pdf->rapport_titel = "";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
    $crm=new Naw();
    $crm->getByField('portefeuille',$this->portefeuille);
    $velden=array();
    foreach($crm->data['fields'] as $veld=>$details)
      $velden[$veld]=$details['value'];
    
    $tekst1='Wij vragen uw aandacht voor het volgende.
    
Ten minste jaarlijks verstrekken wij u een geschiktheidsrapport om vast te stellen of het vermogensbeheer dat wij verrichten nog geschikt is voor u. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te handelen in uw belang. Deze tekst dient als het geschiktheidsrapport en wij verzoeken u deze goed door te nemen.

Wij beoordelen doorlopend of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel en of beleggen geschikt is voor u. Uw beleggingsprofiel is onder andere gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is vastgelegd in diverse documenten zoals het inventarisatieformulier, gespreksverslagen en de vermogensbeheerovereenkomst en (eventueel) overige bij ons beschikbare informatie.

Uw beleggingsprofiel is {vastgesteldProfiel} en is mede gebaseerd op uw beleggingshorizon van {beleggingsHorizon}. Het netto streefrendement dat hoort bij een {vastgesteldProfiel} beleggingsprofiel is {Streefrendement}% en uw risicobereidheid (zowel emotioneel als ook financieel) is een daling van de waarde van uw portefeuille met {MentaleRisicoacceptatie}.

Wij zijn van mening dat onze dienstverlening en de beleggingsportefeuille die wij voor u beheren (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn. Zijn uw persoonlijke en/of financiële omstandigheden echter gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Dat geldt ook voor overige andere belangrijke wijzigingen. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggingsprofiel en het beheer van uw vermogen.

Jaarlijks gaan wij in overleg met u voor een evaluatie van uw omstandigheden en het door ons uitgevoerde beheer. Hierbij zullen wij ook ingaan op de kans dat uw beleggingsdoelstelling niet wordt behaald. Dit alles met het doel u optimaal van dienst te zijn.
';
    $tekst2='Als vermogensbeheerder hechten we er veel waarde aan dat wij op een gedegen manier beleggen met het vermogen dat aan ons is toevertrouwd. Uw vermogen wordt steeds meer geïnvesteerd in beleggingen die een duurzaam karakter hebben en waarbij respect voor mens, milieu en maatschappij een belangrijke rol speelt.

Concluderend moeten we op basis van geldende Europese regelgeving echter stellen dat de dienstverlening die Comfort aanbiedt en de portefeuilles die zij beheert geen duurzaamheidskenmerken promoten of duurzame beleggingen als doelstelling hebben, zoals bedoeld in de artikelen 8 en 9 van de Europese Verordening 2019/2088

Wij vertrouwen erop u hiermee voldoende te hebben geïnformeerd. Indien u nog vragen of opmerkingen heeft, dan vernemen wij dat uiteraard graag.
';
    foreach($velden as $key=>$value)
    {
      $tekst1=str_replace('{'.$key.'}',$value,$tekst1);
      $tekst2=str_replace('{'.$key.'}',$value,$tekst2);
    }
    
    
    $this->pdf->SetWidths(array((297-($this->pdf->marge*2))));
		$this->pdf->SetAligns(array("L"));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
		$this->pdf->row(array('GESCHIKTHEIDSVERKLARING'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->row(array($tekst1));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->row(array('DUURZAAMHEIDSVERKLARING'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    
    $this->pdf->row(array($tekst2));
    $this->pdf->ln();
    
    //if($this->pdf->portefeuilledata['Vermogensbeheerder']=='SHP')
   //   $this->pdf->row(array('Comfort Vermogensbeheer'));
    //else
		if($this->pdf->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen')
		{
			$this->pdf->row(array('Toekomstbeleggen.nl'));
		}
		else
		{
			$this->pdf->row(array($this->pdf->portefeuilledata['VermogensbeheerderNaam']));
		}

	}

}
?>