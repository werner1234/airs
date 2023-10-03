<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/01/13 19:10:29 $
File Versie					: $Revision: 1.8 $

$Log: RapportOIS_L35.php,v $
Revision 1.8  2018/01/13 19:10:29  rvv
*** empty log message ***

Revision 1.7  2012/11/10 15:42:19  rvv
*** empty log message ***

Revision 1.6  2012/10/31 16:59:18  rvv
*** empty log message ***

Revision 1.5  2012/09/16 17:56:17  rvv
*** empty log message ***

Revision 1.4  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.3  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.2  2012/03/04 11:39:58  rvv
*** empty log message ***

Revision 1.1  2012/02/29 16:52:49  rvv
*** empty log message ***

Revision 1.1  2012/02/26 15:17:43  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingValutaLayout.php");

class RapportOIS_L35
{
	function RapportOIS_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Verdeling van de zakelijke waarden over de verschillende sectoren";

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
		$this->pdf->underlinePercentage=0.8;

		// voor data
		$this->pdf->widthA = array(25,15,50,25,25,25,15,110);
		$this->pdf->alignA = array('L','R','L','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(40,50,25,25,25,15,110);
		$this->pdf->alignB = array('L','L','R','R','R','R');

		$this->pdf->AddPage();
		$this->pdf->templateVars['OISPaginas']=$this->pdf->page;

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '$beheerder'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);

		$kleuren = $kleuren['OIS'];

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

		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.beleggingssectorOmschrijving AS beleggingssectorOmschrijving, ".
		" TijdelijkeRapportage.beleggingssector, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.hoofdcategorie='ZAK' AND".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingssector ".
		" ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde asc, TijdelijkeRapportage.beleggingssector ";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$this->pdf->SetWidths(array(10,80,30,30));
		$this->pdf->SetAligns(array('L','L','R','R'));
		$this->pdf->setY(65);
    $grafiekTonen=True;
		while($categorien = $DB->NextRecord())
		{ 
			// print categorie headers
			if($categorien['beleggingssectorOmschrijving']=='')
			  $categorien['beleggingssectorOmschrijving'] = 'Geen sector';

			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);
      if($percentageVanTotaal < 0)
        $grafiekTonen=False;
      
		 	$this->pdf->pieData[vertaalTekst($categorien['beleggingssectorOmschrijving'], $this->pdf->rapport_taal)] = $percentageVanTotaal;
			$this->pdf->pieDataKleur[] = array($kleuren[$categorien['beleggingssector']]['R']['value'],$kleuren[$categorien['beleggingssector']]['G']['value'],$kleuren[$categorien['beleggingssector']]['B']['value']);
			$GrafiekValuta[$categorien['ValutaOmschrijving']]=$percentageVanTotaal;//toevoeging kleuren.
      
			$this->pdf->row(array('',vertaalTekst($categorien['beleggingssectorOmschrijving'],$this->pdf->rapport_taal),
												$this->formatGetalKoers($categorien['subtotaalactueel'],$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal($percentageVanTotaal,1)." %"));
		}

      $this->pdf->ln(2);
       $this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),'','');
			$this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),
												$this->formatGetalKoers($totaalWaarde,$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal(100,1)." %"));
			 $this->pdf->CellBorders = array();


		$this->pdf->setXY(210,60);
    if($grafiekTonen==true)
	    $this->pdf->PieChart(50, 50, $this->pdf->pieData, '%l (%p)',$this->pdf->pieDataKleur);

	}
}
?>