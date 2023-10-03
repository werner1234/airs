<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/01/27 17:31:22 $
File Versie					: $Revision: 1.8 $

$Log: RapportOIV_L35.php,v $
Revision 1.8  2018/01/27 17:31:22  rvv
*** empty log message ***

Revision 1.7  2018/01/21 09:00:44  rvv
*** empty log message ***

Revision 1.6  2013/10/19 15:57:25  rvv
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

class RapportOIV_L35
{
	function RapportOIV_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Verdeling van het vermogen over de verschillende regio's en valuta";

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
    $this->pdf->row(array('',vertaalTekst('Regioverdeling zakelijke waarden',$this->pdf->rapport_taal),vertaalTekst('Valutaverdeling totaal vermogen',$this->pdf->rapport_taal)));
    $this->pdf->setY(65);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetWidths(array(150,50,30,30));
		$this->pdf->underlinePercentage=0.8;
		$this->pdf->SetAligns(array('L','L','R','R'));
		
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers

			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);
			$this->pdf->pieData[vertaalTekst($categorien['ValutaOmschrijving'], $this->pdf->rapport_taal)] = $percentageVanTotaal;
			$this->pdf->pieDataKleur[] = array($kleuren[$categorien['valuta']]['R']['value'],$kleuren[$categorien['valuta']]['G']['value'],$kleuren[$categorien['valuta']]['B']['value']);
			$GrafiekValuta[$categorien['ValutaOmschrijving']]=$percentageVanTotaal;//toevoeging kleuren.

			$this->pdf->row(array('',vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal),
												$this->formatGetalKoers($categorien['subtotaalactueel'],$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal($percentageVanTotaal,1)." %"));
		}

      $this->pdf->ln(2);
      $this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),'','');
			$this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),
												$this->formatGetalKoers($totaalWaarde,$this->pdf->rapport_OIV_decimaal),
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
			$this->pdf->pieData2[vertaalTekst($categorien['regioOmschrijving'], $this->pdf->rapport_taal)] = $percentageVanTotaal;
			$this->pdf->pieDataKleur2[] = array($kleurenOIR[$categorien['regio']]['R']['value'],$kleurenOIR[$categorien['regio']]['G']['value'],$kleurenOIR[$categorien['regio']]['B']['value']);
			$GrafiekValuta[$categorien['regioOmschrijving']]=$percentageVanTotaal;//toevoeging kleuren.

			$this->pdf->row(array('',vertaalTekst($categorien['regioOmschrijving'],$this->pdf->rapport_taal),
												$this->formatGetalKoers($categorien['subtotaalactueel'],$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal($percentageVanTotaal,1)." %"));
		}

      $this->pdf->ln(2);
      $this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),'','');
			$this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),
												$this->formatGetalKoers($totaalWaarde,$this->pdf->rapport_OIV_decimaal),
												$this->formatGetal(100,1)." %"));

$this->pdf->CellBorders = array();

		$this->pdf->setXY(160,120);
	  $this->PieChart(50, 50, $this->pdf->pieData, '%l (%p)',$this->pdf->pieDataKleur);

	  $this->pdf->setXY(20,120);
	  $this->PieChart(50, 50, $this->pdf->pieData2, '%l (%p)',$this->pdf->pieDataKleur2);

	}
  
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($this->pdf->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $w + $radius ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag -($radius) + $margin;

      for($i=0; $i<$this->pdf->NbVal; $i++)
      {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $margin;
      }

  }
}
?>