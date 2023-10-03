<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANS_L110
{
	function RapportTRANS_L110($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "TRANS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Transacties";
    
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
    global $__appvar;
    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
  
    $this->pdf->widthB = array(26,50,25,65,35,28,25,25);
    $this->pdf->alignB = array('L','L','L','L','L','R','R','R');
    $this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->setAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
  
  
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
  
  
    if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
  
    // voor data
  
    // loopje over Grootboekrekeningen Opbrengsten = 1
    $query = "SELECT Fondsen.Omschrijving, ".
      "Fondsen.Fondseenheid, ".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.id,
		Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
      "Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Debet as Debet, ".
      "Rekeningmutaties.Credit as Credit,
       Rekeningmutaties.Bedrag as Bedrag,".
      "Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers ,
		 Fondsen.beurs,
		 Fondsen.ISINcode,
Beurzen.omschrijving as beursOmschrijving ".
      "FROM
       Rekeningmutaties
       JOIN Fondsen on Rekeningmutaties.Fonds = Fondsen.Fonds
       JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
       JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
       JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
       LEFT JOIN Beurzen ON Fondsen.beurs = Beurzen.beurs ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Transactietype <> 'B' AND ".
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
  
    $transactietypenOmschrijving= array('A'=>'Aankoop',
                                        'A/O'=>'Aankoop / openen',
                                        'A/S'=>'Aankoop / sluiten',
                                        'D'=>'Deponering',
                                        'L'=>'Lichting',
                                        'V'=>'Verkoop',
                                        'V/O'=>'Verkoop / openen',
                                        'V/S'=>'Verkoop / sluiten',);
  
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize+2);
    $this->pdf->setWidths(array(100));
    $this->pdf->ln();
    $this->pdf->row(array('Aan- en verkopen'));
    $this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row(array('Datum','Beurs','Type','Product','ISIN','Aantal','Koers','Waarde'));
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
  
    while($mutaties = $DB->nextRecord())
    {
      $this->pdf->row(array(date('d-m-Y',db2jul($mutaties['Boekdatum'])),$mutaties['beursOmschrijving'],$transactietypenOmschrijving[$mutaties['Transactietype']],$mutaties['Omschrijving'],$mutaties['ISINcode'],
                        $this->formatGetal($mutaties['Aantal'],4),$this->formatGetal($mutaties['Fondskoers'],4),$this->formatGetal($mutaties['Bedrag'],1)));
    
    }
  
  
    $query = "SELECT Grootboekrekeningen.Omschrijving, ".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.id,
		Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
      "Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Debet as Debet, ".
      "Rekeningmutaties.Credit as Credit,
       Rekeningmutaties.Bedrag as Bedrag,".
      "Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers".
      " FROM
       Rekeningmutaties
       JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
       JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
       JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Transactietype <> 'B' AND ".
      "Grootboekrekeningen.Grootboekrekening IN('STORT','ONTTR','DIV') AND ".
      "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize+2);
    $this->pdf->setWidths(array(150));
    $this->pdf->ln();
    $this->pdf->row(array('Stortingen, onttrekkingen en dividenden'));
    $this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row(array('Datum','Type','Waarde'));
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
  
    while($mutaties = $DB->nextRecord())
    {
      $this->pdf->row(array(date('d-m-Y',db2jul($mutaties['Boekdatum'])),$mutaties['Omschrijving'],$this->formatGetal($mutaties['Bedrag'],1)));
    
    }
    

  }
}
?>