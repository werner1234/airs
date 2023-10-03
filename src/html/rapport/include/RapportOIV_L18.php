<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/06/29 16:52:23 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportOIV_L18.php,v $
 		Revision 1.3  2011/06/29 16:52:23  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/05/16 08:13:26  rvv
 		*** empty log message ***

 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIV_L18
{
	function RapportOIV_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

			$this->pdf->rapport_header = array('','Munt',"Bedrag","Marktwaarde\nin EUR","% Vermogen");

			$this->pdf->rapport_titel = "Liquiditeiten & Geldmarkt Beleggingen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;


	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;


		$this->pdf->AddPage();
		$this->pdf->templateVars['OIVPaginas']=$this->pdf->customPageNo+$this->pdf->extraPage;

		$this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;

		$DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];


		$query = "SELECT
    TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving,
    TijdelijkeRapportage.valuta,
    TijdelijkeRapportage.actueleValuta,
    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,
    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel,
    TijdelijkeRapportage.type
    FROM TijdelijkeRapportage
    WHERE
    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
    TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND ( type = 'rekening')
    ".$__appvar['TijdelijkeRapportageMaakUniek']."
    GROUP BY
    TijdelijkeRapportage.valuta
    ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
		// type = 'rente' OR
		$this->pdf->switchFont('fonds');

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		//  listarray($data);
		    $this->pdf->Row(array('',$data['valuta'],$this->formatGetal($data['subtotaalactueelvaluta']),$this->formatGetal($data['subtotaalactueel']),$this->formatGetal($data['subtotaalactueel']/$totaalWaarde*100,1)."%"));
        $totalen['subtotaalactueel'] += $data['subtotaalactueel'];
        $totalen['subtotaalactueelvaluta'] += $data['subtotaalactueelvaluta'];

		}
				$this->pdf->switchFont('rodelijn');
	      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		    $this->pdf->Row(array('','Totaal Liquiditeiten',$this->formatGetal($totalen['subtotaalactueelvaluta']),$this->formatGetal($totalen['subtotaalactueel']),$this->formatGetal($totalen['subtotaalactueel']/$totaalWaarde*100,1)."%"));



	}
}
?>