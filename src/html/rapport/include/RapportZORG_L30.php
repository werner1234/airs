<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/06/08 15:42:01 $
File Versie					: $Revision: 1.1 $

$Log: RapportZORG_L30.php,v $
Revision 1.1  2016/06/08 15:42:01  rvv
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

class RapportZORG_L30
{
	function RapportZORG_L30($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ZORG";
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
	  global $__appvar;
	  $this->pdf->addPage();
	  $this->pdf->templateVars['ZORGPaginas'] = $this->pdf->customPageNo;

    $velden=array();    
    $checkVelden=array('ClientInfoDatum','ClientInfoMemo','ClientInfoErvaring','ClientInfoFinancieel','ClientInfoDoelstelling','ClientInfoRisicohouding');
    $query = "desc CRM_naw";
    $this->DB->SQL($query);
    $this->DB->query();
    while($data=$this->DB->nextRecord('num'))
      $velden[]=$data[0];
    $extraVeld='';  
    foreach($checkVelden as $check)  
     if(in_array($check,$velden))
       $extraVeld.=','.$check;
 
 	  $query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();

	//	$this->pdf->SetY(40);
//    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+4);
    $this->pdf->SetWidths(array(280));
		$this->pdf->SetAligns(array('L','L'));



		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->Ln();
		$this->pdf->row(array(vertaalTekst("Bij de bepaling van het risicoprofiel voor uw portefeuille op ".$crmData['ClientInfoDatum']." hebben wij ons gebaseerd op de door u verstrekte gegevens op het gebied van uw kennis en ervaring op beleggingsgebied, uw toenmalige financiële situatie en de doelstellingen voor het portefeuillevermogen evenals  uw houding met betrekking tot het risico dat u loopt bij het beleggen. Deze gegevens zijn opgenomen in de beheerbrief als onderdeel van de beheerovereenkomst en zijn hieronder gereproduceerd. Eventuele latere wijzigingen zijn hierin verwerkt.

Mochten deze gegevens inmiddels (substantieel) zijn gewijzigd, zodat mogelijk de bepaling van het risicoprofiel van de portefeuille anders kan uitvallen, dan gelieve u ons daarvan op de hoogte te stellen. Indien u akkoord gaat met de weergave van onderstaande gegevens, dan blijven wij deze als basis gebruiken voor het risicoprofiel in de komende periode.

Castanje Vermogensbeheer",$this->pdf->rapport_taal)));
		$this->pdf->Ln();
		$crmObject=new NAW();
		for($i=1;$i<count($checkVelden); $i++)
		{
			//echo $i." ".$checkVelden[$i].' ';
			if($crmData[$checkVelden[$i]] <> '')
			{
				$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
				$this->pdf->row(array(vertaalTekst($crmObject->data['fields'][$checkVelden[$i]]['description'],$this->pdf->rapport_taal)));
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array(vertaalTekst($crmData[$checkVelden[$i]],$this->pdf->rapport_taal)));
				$this->pdf->Ln();
			}
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


	}


}
?>