<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/12/19 08:29:17 $
 		File Versie					: $Revision: 1.4 $

 		$Log: RapportTRANS_L18.php,v $
 		Revision 1.4  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/06/02 15:05:05  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/05/16 08:13:26  rvv
 		*** empty log message ***

 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportTransactieoverzichtLayout.php");

class RapportTRANS_L18
{
	function RapportTRANS_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Transacties";
			$this->pdf->rapport_header = array('','Datum',"Transactie","Omschrijving","Munt","Aantal","Koers","Bedrag in Eur");


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde<>0)
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	  $transactietypenÓmschrijving= array('A'=>'Aankoop',
	                                      'A/O'=>'Aankoop / openen',
	                                      'A'=>'Aankoop',
	                                      'A/S'=>'Aankoop / sluiten',
	                                      'D'=>'Deponering',
	                                      'L'=>'Lichting',
	                                      'V'=>'Verkoop',
	                                      'V/O'=>'Verkoop / openen',
	                                      'V/S'=>'Verkoop / sluiten',);


    $koersQuery = "";

		$DB = new DB();
		$db2 = new DB();





		$this->pdf->AddPage();
		$this->pdf->templateVars['TRANSPaginas'] = $this->pdf->customPageNo+$this->pdf->extraPage;
		$this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$rapjaar = date('Y',db2jul($this->rapportageDatumVanaf));
		$transactietypen = array();

		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

			  $this->pdf->switchFont('fonds');
		foreach ($buffer as $mutaties)
		{
		//listarray($mutaties);
						 $this->pdf->Row(array('',date("d-m-Y",db2jul($mutaties['Boekdatum'])),
						                          $transactietypenÓmschrijving[$mutaties['Transactietype']],
						                          $mutaties['Omschrijving'],
						                          $mutaties['Valuta'],
						                          $this->formatGetal($mutaties['Aantal']),
						                          $this->formatGetal($mutaties['Fondskoers'],2),
						                          $this->formatGetal(($mutaties['Credit']-$mutaties['Debet'])*$mutaties['Valutakoers'])));

		}

		 if (($this->pdf->customPageNo+$this->pdf->extraPage) <> $this->pdf->templateVars['TRANSPaginas'])
$this->pdf->templateVars['TRANSPaginas'] .= " - " . ($this->pdf->customPageNo+$this->pdf->extraPage);

	}
}
?>