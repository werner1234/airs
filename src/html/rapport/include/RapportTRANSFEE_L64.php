<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/07/23 17:29:56 $
File Versie					: $Revision: 1.2 $

$Log: RapportTRANSFEE_L64.php,v $
Revision 1.2  2018/07/23 17:29:56  rvv
*** empty log message ***

Revision 1.1  2018/07/21 15:54:40  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:17:01  rvv
*** empty log message ***

Revision 1.4  2016/08/27 16:26:45  rvv
*** empty log message ***

Revision 1.3  2016/06/09 05:49:23  rvv
*** empty log message ***

Revision 1.2  2016/06/08 15:42:01  rvv
*** empty log message ***

Revision 1.1  2016/06/05 12:37:50  rvv
*** empty log message ***

Revision 1.4  2014/10/15 16:05:25  rvv
*** empty log message ***

Revision 1.3  2014/10/08 15:42:52  rvv
*** empty log message ***

Revision 1.2  2014/10/04 15:22:54  rvv
*** empty log message ***

Revision 1.1  2014/10/01 16:06:12  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportTRANSFEE_L64
{
	function RapportTRANSFEE_L64($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANSFEE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Algemene toelichting";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();


		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	function kopEnVoet()
	{
	  if(is_file($this->pdf->rapport_factuurHeader))
		{
			$this->pdf->Image($this->pdf->rapport_factuurHeader, 0, 10, 210, 34);
		}
		if(is_file($this->pdf->rapport_factuurFooter))
		{
			$this->pdf->Image($this->pdf->rapport_factuurFooter, 5, 255, 200, 37);
		}
	}


	function writeRapport()
	{
	  $this->pdf->addPage();
	  $this->pdf->templateVars['TRANSFEEPaginas'] = $this->pdf->page;

    $velden=array();    
    $checkVelden=array('Streefrendement','Afhankelijkheid','KennisSV','ErvaringSV','risicoacceptatie');
    $query = "desc CRM_naw";
    $this->DB->SQL($query);
    $this->DB->query();
    while($data=$this->DB->nextRecord('num'))
      $velden[]=$data[0];
    $extraVeld='';  
    foreach($checkVelden as $check)  
     if(in_array($check,$velden))
       $extraVeld.=','.$check;
 
 	  $query = "SELECT verzendAanhef,beleggingsDoelstelling,beleggingsHorizon $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $this->pdf->SetWidths(array(10,260));
		$this->pdf->SetAligns(array('L','L'));

		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->Ln(8);
		$this->pdf->row(array('', 'Op de vorige pagina ziet u de uitgevoerde transacties van afgelopen kwartaal. In de nieuwsbrief treft u nadere informatie en onze beweegredenen voor deze transacties. Eventuele aan- en verkopen die verband houden met het herbalanceren van uw portefeuille worden verder niet toegelicht.'));
		$this->pdf->Ln();
		$this->pdf->row(array('', 'BeSmart heeft op basis van de eerder door u verstrekte informatie een cliëntprofiel opgesteld. Het door u opgegeven doel van de beleggingen is "'.$crmData['beleggingsDoelstelling'].'"  met daarbij een beleggingshorizon van '.$crmData['beleggingsHorizon'].'. U streeft met de belegging naar een gemiddeld rendement van '.$crmData['Streefrendement'].' op jaarbasis. U bent nu of in de toekomst '.$crmData['Afhankelijkheid'].' afhankelijk van de beleggingsresultaten. Uw kennis omtrent de werking van de financiële markten en financiële instrumenten is '.$crmData['KennisSV'].' en u heeft '.$crmData['ErvaringSV'].' ervaring met beleggen.'));
		$this->pdf->Ln();
		$this->pdf->row(array('', 'Op basis van dit cliëntprofiel is bepaald welk portefeuilleprofiel voor u het meeste geschikt is. BeSmart belegt voor u binnen de bandbreedtes van het profiel '.$this->pdf->rapport_risicoklasse.'. Een '.$this->pdf->rapport_risicoklasse.' profiel correspondeert met een '.$crmData['risicoacceptatie'].' neerwaartse risico-acceptatie.'));
		$this->pdf->Ln();
		$this->pdf->row(array('', 'Voor de selectie van beleggingsproducten hanteert BeSmart Vermogensbeheer een adequaat beleggingsproces waarbij per portefeuilleprofiel wordt beoordeeld of de producten in uw portefeuille of die BeSmart voor u zal aankopen aansluiten bij de behoeften, karakteristieken en doelstellingen van u als belegger in dit portefeuilleprofiel.'));
	}
}
?>