<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


//ini_set('max_execution_time',60);
class RapportEND_L123
{

	function RapportEND_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
    $this->rapportageDatum = $rapportageDatum;
    $this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapport_titel = "Grondslagen";

	}

  function headerValuta()
  {
    $this->pdf->setAligns(array("L",'L','R','R','R'));
    unset($this->pdf->fillCell);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(160,25+25+25+25));
    $this->pdf->Row(array("",vertaalTekst("Wisselkoers op einddatum rapportage", $this->pdf->rapport_taal)));
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetWidths(array(160,25,25,25,25));
    $this->pdf->Row(array("",vertaalTekst("Valuta\n \n ", $this->pdf->rapport_taal),vertaalTekst("Wisselkoers\nEUR\n ", $this->pdf->rapport_taal),vertaalTekst("Wisselkoers\nomgekeerd\n ", $this->pdf->rapport_taal),vertaalTekst("Datum\n \n ", $this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$this->pdf->rapport_highRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);

  }
  function addRegel($data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','U','U','U','U');
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->Row(array('',$data['valuta'],$this->formatGetal($data['actueleValuta'],4),$this->formatGetal(1/$data['actueleValuta'],4),date('d/m/Y',$this->rapportageDatumJul)));
  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

	function writeRapport()
	{
    global $__appvar;

    $q = "SELECT TijdelijkeRapportage.valuta, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.koersDatum ".
      " FROM TijdelijkeRapportage JOIN Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND ".
      " TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."'  ".
      $__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.valuta ORDER BY Valutas.Afdrukvolgorde asc";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();


    $valutas=array();
    while ($valuta = $DB->NextRecord())
    {
      $valutas[] = $valuta;
    }

    $this->pdf->addPage();

    $poly=array($this->pdf->marge,29,
      $this->pdf->marge+82,29,
      $this->pdf->marge+82,120,
      $this->pdf->marge+82,125,
      $this->pdf->marge,125);

    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);

    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $ystart=$this->pdf->getY();

    $curX = $this->pdf->getX();
    $this->pdf->setX($curX+3);

    if( $this->pdf->rapport_taal == 2 ) {
      $tekst = $this->pdf->portefeuilledata['VermogensbeheerderNaam'] . ", has taken all reasonable care to ensure the accuracy of this report. The valuation of the securities for tax purposes can however differ from those as know at the time op publication of this report. This report has not been made for the purposes of tax calculations.

Securities are valued on the basis of the closing price of the last trade dat of the reporting period. For som securities the (closing)price might be out of date when there was no active trading.

All prices or rates for the valuation of this report are obtained from an independent source.

Please be informed that this report is for information purposes only, that the content is nog legally binding and strictly private and confidential.";
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $tekst = $this->pdf->portefeuilledata['VermogensbeheerderNaam'] . ", a apport� le plus grand soin � la fiabilit� de ce rapport. Les cours fiscaux peuvent toutefois diff�rer des cours connus au moment de la publication. Ce rapport n'est donc pas r�dig� � des fins fiscales.

Les titres sont valoris�s sur la base des cours de cl�ture du dernier jour de bourse de la p�riode concern�e. Il se peut que le cours (de cl�ture) indiqu� ne siot pas actualis� pour certains titres en raison d'un manque de liquidit�.

Les informations utilis�es pour la valorisation des titres proviennent d'une source ind�pentante.

Le pr�sent rapport de portefeuille vous est exclusievement communiqu� � titre informatif et purement indicatif. Il ne vous conf�re aucun droit et est strictement personnel.";
    } else {
      $tekst = $this->pdf->portefeuilledata['VermogensbeheerderNaam'] . ", heeft alle redelijke zorg besteed aan de juistheid van deze rapportage. De waardering van de financi�le instrumenten voor fiscale doeleinden kan echter verschillen van die zoals bekend op het moment van publicatie van dit rapport. Deze rapportage is dan ook niet opgesteld voor fiscale doeleinden.
    
De financi�le instrumenten zijn hierin gewaardeerd op basis van de slotkoersen van de laatste handelsdag van de rapportageperiode. Door gebrek aan handel kan voor bepaalde effecten de getoonde (slot)koers verouderd zijn. In dit rapport maken we gebruik van een tijd gewogen rendementsberekening.

De informatie die de basis voor de bovenstaande waarderingsgrondslag vormt, is afkomstig van een onafhankelijke bron. Deze informatie is en blijft eigendom van de betrokken onafhankelijke bron.

Dit portefeuille overzicht wordt u uitsluitend ter informatie meegedeeld en is louter indicatief. Het is strikt persoonlijk, dient vertrouwelijk te worden behandeld en u kunt er geen rechten aan ontlenen.";
    }

    $this->pdf->MultiCell(77,4,$tekst,0,'L');
    $this->pdf->setX($curX);
    $this->pdf->setY($ystart);
    $this->headerValuta();
    foreach($valutas as $regel)
    {
      $this->addRegel($regel);
    }



  }


}
?>