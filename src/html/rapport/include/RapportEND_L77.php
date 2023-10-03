<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/12/22 16:15:52 $
File Versie					: $Revision: 1.6 $

$Log: RapportEND_L77.php,v $
Revision 1.6  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.5  2018/10/24 16:00:59  rvv
*** empty log message ***

Revision 1.4  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.3  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.2  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportEND_L77
{
	function RapportEND_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->portefeuille=$portefeuille;
		$this->rapportageDatumVanaf=$rapportageDatumVanaf;
		$this->rapportageDatum=$rapportageDatum;

		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Legenda";

	}

	function writeRapport()
	{
	  global $__appvar;
      $this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$this->pdf->setY(35);
		$this->pdf->setAligns(array('L','L'));
		$this->pdf->setWidths(array(20,200));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',vertaalTekst("Belangrijke informatie",$this->pdf->rapport_taal)."\n\n"));
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    if($this->pdf->rapport_taal==1)
    {
      $this->pdf->row(array('',"This report is based on the bank statements of the custodian(s) involved. In case of discrepancies, the bank reporting is leading. The ratings, prices and other details showed are based on regular data sources. These are to be considered as guidelines and are not binding to Andreas Capital.

Since these calculations may differ from those required for official tax returns, this report is not suitable for tax purposes. The cost price is based on the market price at the moment of data entry. In certain cases, deviation from the actual cost price is possible.

We request you to check this report, and report any differences within four weeks of receipt, in writing.

This report is not signed."));
    }
    else
    {
      $this->pdf->row(array('',"Deze rapportage is gebaseerd op de afschriften van de betreffende depotbanken en bij verschillen is de rapportage van de bank leidend. De ratings,koersen en andere details zijn weergegeven op basis van gebruikelijke bronnen van informatie. Ze worden beschouwd als richtlijnen en zijn niet bindend voor Andreas Capital.

Aangezien deze berekeningen kunnen afwijken van de benodigde officiële belastingopgaven, is deze rapportage niet geschikt voor fiscale doeleinden. Als kostprijs wordt de marktwaarde genomen op het moment van invoer. In bepaalde gevallen is afwijking van de werkelijke kostprijs mogelijk.

Wij verzoeken u om deze rapportage te controleren en ons eventuele verschillen, binnen vier weken na ontvangst schriftelijk te melden.

Deze rapportage is niet ondertekend."));
    }
   
    
		$this->printValutaoverzicht($this->portefeuille,$this->rapportageDatum);

	}

	function printValutaoverzicht($portefeuille, $rapportageDatum,$omkeren=false)
	{
		global $__appvar;
		// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS valuta, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta".
			" FROM TijdelijkeRapportage, Valutas ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ".
			" TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' AND ".
			" TijdelijkeRapportage.valuta = Valutas.Valuta "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($q,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		if($DB->records() > 0)
		{
			$valutas=array();
			$this->pdf->ln();
			$this->pdf->ln();

			while ($valuta = $DB->NextRecord())
			{
				$valutas[] = $valuta;
			}

			$regels = ceil((count($valutas)));
			if(count($valutas) > 4)
			{
				$regels = ceil((count($valutas) / 2));
			}
			$hoogte = ($regels * 4) + 4;
			if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
			{
				$this->pdf->AddPage();
				$this->pdf->ln();
			}
			$plusmarge = 20;
			$kop = "Wisselkoersen";

			$widths=array($plusmarge,20,20,30);
			$this->pdf->setWidths($widths);
			$this->pdf->setAligns(array('L','L','L','R'));
			$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
			$this->pdf->Rect($this->pdf->marge+$plusmarge, $this->pdf->getY(), array_sum($widths)-$plusmarge, 6 , 'F');
			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
      $this->pdf->ln(1);
			$this->pdf->SetX($this->pdf->marge+$plusmarge);
			$this->pdf->Cell(100,4, vertaalTekst($kop,$this->pdf->rapport_taal), 0,1, "L");
			$this->pdf->ln(2);
			$this->pdf->SetTextColor(0);

			$y = $this->pdf->getY();
			$start = false;
			//while ($valuta = $DB->NextRecord())
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			for($a=0; $a < count($valutas); $a++)
			{

				if($this->pdf->ValutaKoersEind > 0)
					$valutas[$a]['actueleValuta'] = $valutas[$a]['actueleValuta'] / $this->pdf->ValutaKoersEind ;


				$this->pdf->row(array('',$this->pdf->rapportageValuta,'1  =  '.$valutas[$a]['valuta'],$this->pdf->formatGetal(1/$valutas[$a]['actueleValuta'],4)));




			}

		}

	}
}
?>