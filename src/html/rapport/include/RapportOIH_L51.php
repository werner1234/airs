<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/10/28 18:03:18 $
File Versie					: $Revision: 1.3 $

$Log: RapportOIH_L51.php,v $
Revision 1.3  2017/10/28 18:03:18  rvv
*** empty log message ***

Revision 1.2  2017/08/23 15:22:13  rvv
*** empty log message ***

Revision 1.2  2017/06/24 16:30:07  rvv
*** empty log message ***

Revision 1.1  2017/06/21 16:10:36  rvv
*** empty log message ***

Revision 1.19  2017/06/19 06:50:08  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIH_L51
{
	function RapportOIH_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Resultaat Vastgoedportefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
		$this->extraVoetPages=array();
		$this->extraVoet='';
		$this->extraVoet2='';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	function writeRapport()
	{
		global $__appvar;
		//$brightness=1.55;


		$this->pdf->AddPage();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->templateVars['VHOPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['VHOPaginas']=$this->pdf->rapport_titel;

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query = "SELECT TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.Valuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
			" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
          TijdelijkeRapportage.type,
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
           round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd ".
			" FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.beleggingscategorie='VAS' AND ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type IN('fondsen','rente') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.beleggingscategorie ,TijdelijkeRapportage.type, 
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB2 = new DB();
		$DB->SQL($query);
		$DB->Query();

		$totalen=array();
		while($data = $DB->NextRecord())
		{

			$query = "SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )-ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) ,0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND
           Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND Rekeningmutaties.Fonds = '".$data['fonds']."'";
			$DB2->SQL($query);
			$extra=$DB2->lookupRecord();

			$resultaat=$extra['opbrengstTotaal']-$extra['kostenTotaal'];
			$this->pdf->row(array("  ".$data['fondsOmschrijving'],
												$this->formatGetal($data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$data['Valuta'],
												$this->formatGetal($data['historischeWaardeTotaal'],0),
												$this->formatGetal($data['historischeWaardeTotaalValuta'],0),
												"",
												$this->formatGetal($extra['kostenTotaal'],0),
												$this->formatGetal($extra['opbrengstTotaal'],0),
												$this->formatGetal($resultaat,0),
												$this->formatGetal($resultaat/$data['historischeWaardeTotaalValuta']*100,2)."",
											)	);

			$totalen['historischeWaardeTotaalValuta']+=$data['historischeWaardeTotaalValuta'];
			$totalen['kostenTotaal']+=$extra['kostenTotaal'];
			$totalen['opbrengstTotaal']+=$extra['opbrengstTotaal'];
			$totalen['resultaat']+=$resultaat;
		}

		$this->pdf->CellBorders = array('','','','','SUB','','SUB','SUB','SUB');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("","","","",$this->formatGetal($totalen['historischeWaardeTotaalValuta'],0),"",
											$this->formatGetal($totalen['kostenTotaal'],0),
											$this->formatGetal($totalen['opbrengstTotaal'],0),
											$this->formatGetal($totalen['resultaat'],0),""));
		$this->pdf->CellBorders = array();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();


	}

}
?>