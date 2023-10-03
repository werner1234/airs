<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/09/11 15:40:24 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIV_L50.php,v $
Revision 1.2  2013/09/11 15:40:24  rvv
*** empty log message ***

Revision 1.1  2013/06/30 15:07:33  rvv
*** empty log message ***

Revision 1.5  2012/12/08 14:48:08  rvv
*** empty log message ***

Revision 1.4  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.3  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.2  2012/02/29 16:52:49  rvv
*** empty log message ***

Revision 1.1  2012/02/26 15:17:43  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingValutaLayout.php");

class RapportOIV_L50
{
	function RapportOIV_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Verdeling van het vermogen over de verschillende regio's en valuta.";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;

		// voor data
		$this->pdf->widthA = array(25,15,50,25,25,25,15,110);
		$this->pdf->alignA = array('L','R','L','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(40,50,25,25,25,15,110);
		$this->pdf->alignB = array('L','L','R','R','R','R');

		$this->pdf->AddPage();
		$this->pdf->templateVars['OIVPaginas']=$this->pdf->page;

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '$beheerder'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);

		$kleurenOIR = $kleuren['OIR'];
		$kleuren = $kleuren['OIV'];

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    
    $this->pdf->setY(58);
    $this->pdf->SetWidths(array(10,140,150));
    $this->pdf->SetAligns(array('L','L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Regioverdeling zakelijke waarden','Valutaverdeling totaal vermogen'));
    $this->pdf->setY(65);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetWidths(array(150,50,30,30));
		$this->pdf->underlinePercentage=0.8;
		$this->pdf->SetAligns(array('L','L','R','R'));
		
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers

			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);
			$this->pieData[vertaalTekst($categorien['ValutaOmschrijving'], $this->pdf->rapport_taal)] = $percentageVanTotaal;
			$this->pieDataKleur[] = array($kleuren[$categorien['valuta']]['R']['value'],$kleuren[$categorien['valuta']]['G']['value'],$kleuren[$categorien['valuta']]['B']['value']);
			$GrafiekValuta[$categorien['ValutaOmschrijving']]=$percentageVanTotaal;//toevoeging kleuren.

			$this->pdf->row(array('',vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal),
												$this->formatGetal($categorien['subtotaalactueel'],$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal($percentageVanTotaal,1)." %"));
		}

      $this->pdf->ln(2);
      $this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),'','');
			$this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),
												$this->formatGetal($totaalWaarde,$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal(100,1)." %"));


		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND TijdelijkeRapportage.hoofdcategorie='ZAK' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query = "SELECT TijdelijkeRapportage.regioOmschrijving AS regioOmschrijving, ".
		" TijdelijkeRapportage.regio, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.hoofdcategorie='ZAK' AND".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.regio ".
		" ORDER BY TijdelijkeRapportage.regioVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$this->pdf->SetWidths(array(10,50,30,30));
		$this->pdf->SetAligns(array('L','L','R','R'));
		$this->pdf->setY(65);
		$this->pdf->CellBorders = array();
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			if($categorien['regioOmschrijving']=='')
			  $categorien['regioOmschrijving']='Geen regio';
			if($categorien['regio']=='')
			  $categorien['regio']='Geen regio';

			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);
			$this->pieData2[vertaalTekst($categorien['regioOmschrijving'], $this->pdf->rapport_taal)] = $percentageVanTotaal;
			$this->pieDataKleur2[] = array($kleurenOIR[$categorien['regio']]['R']['value'],$kleurenOIR[$categorien['regio']]['G']['value'],$kleurenOIR[$categorien['regio']]['B']['value']);
			$GrafiekValuta[$categorien['regioOmschrijving']]=$percentageVanTotaal;//toevoeging kleuren.

			$this->pdf->row(array('',vertaalTekst($categorien['regioOmschrijving'],$this->pdf->rapport_taal),
												$this->formatGetal($categorien['subtotaalactueel'],$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal($percentageVanTotaal,1)." %"));
		}

      $this->pdf->ln(2);
      $this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),'','');
			$this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),
												$this->formatGetal($totaalWaarde,$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal(100,1)." %"));

$this->pdf->CellBorders = array();

		$this->pdf->setXY(210,100);
	  $this->pdf->PieChart(50, 50, $this->pieData, '%l (%p)',$this->pieDataKleur);

	  $this->pdf->setXY(40,100);
	  $this->pdf->PieChart(50, 50, $this->pieData2, '%l (%p)',$this->pieDataKleur2);

	}
}
?>