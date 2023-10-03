<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANS_L125
{
	function RapportTRANS_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Transacties";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}
  
  function formatGetal($waarde, $dec, $teken='')
  {
    return formatGetal_L125($waarde, $dec, $teken);
  }

	function writeRapport()
	{

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Rekeningmutaties.id, Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid,Grootboekrekeningen.Omschrijving as GbOmschrijving, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		 Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
    "Rekeningmutaties.Bedrag as Bedrag, ".
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



		$buffer = array();


		while($mutaties = $DB->nextRecord())
		{
		  if(substr($mutaties['Transactietype'],0,1)=='A'||substr($mutaties['Transactietype'],0,1)=='V')
      {
        $buffer['Aan- en verkopen onder het lopende jaar'][] = $mutaties;
      }
		  elseif(substr($mutaties['Transactietype'],0,1)=='D'||substr($mutaties['Transactietype'],0,1)=='L')
      {
        $buffer['Deponeringen en lichtingen'][] = $mutaties;
      }
      else
      {
        $buffer[$mutaties['Transactietype']][] = $mutaties;
      }
		}
    
    $transactietypenOmschrijving= array('A'=>'Aankoop',
                                        'A/O'=>'Aankoop / openen',
                                        'A/S'=>'Aankoop / sluiten',
                                        'D'=>'Deponering',
                                        'L'=>'Lichting',
                                        'V'=>'Verkoop',
                                        'V/O'=>'Verkoop / openen',
                                        'V/S'=>'Verkoop / sluiten');
    
    
    $this->pdf->AddPage();
    
    unset($this->pdf->fillCell);
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $header=array('','Datum','Soort transactie','Belegging','Aantal','Koers','Bedrag');
    $aanwezigeTransactietypen=array();
		foreach ($buffer as $gbOmschrijving=>$gbRegels)
		{
       subHeader_L125($this->pdf, $this->pdf->getY(), array(140), array($gbOmschrijving));
       $this->pdf->ln(12);
       $this->pdf->setWidths(array(20-$this->pdf->marge,30,30,80,30,30,30));
       $this->pdf->setAligns(array('L','L','L','L','R','R','R'));
       $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+1);
       $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
       $this->pdf->ln(2);
       $this->pdf->row($header);
       $this->pdf->ln(2);
       $this->pdf->setTextColor(0);
       $this->pdf->setFillColor($this->pdf->kopGrijs[0],$this->pdf->kopGrijs[1],$this->pdf->kopGrijs[2]);
       $totalen=array();
	     foreach($gbRegels as $mutaties)
       {
         $aanwezigeTransactietypen[$mutaties['Transactietype']]=$mutaties['Transactietype'];
         //if($mutaties['Aantal']>0)
         //  $this->pdf->fillCell=array(0,1,1,1,1,1,1);
         //else
         //  $this->pdf->fillCell=array();
         $boekJul=db2jul($mutaties['Boekdatum']);
         $maand=vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$boekJul)],$this->pdf->taal);
         
         $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
         $this->pdf->row(array('',date("d ", db2jul($boekJul)).$maand,
                           $transactietypenOmschrijving[$mutaties['Transactietype']],
                           $mutaties['Omschrijving'],
                           $this->formatGetal(abs($mutaties['Aantal']), 0),
                           $this->formatGetal($mutaties['Fondskoers'], 2),
                           $this->formatGetal($mutaties['Bedrag'], 0,'€'),
                           ''));
         $totalen['Bedrag']+=$mutaties['Bedrag'];
       }
       unset($this->pdf->fillCell);
       $this->pdf->ln(4);
       $this->pdf->line(20,$this->pdf->getY(),$this->pdf->w-20,$this->pdf->getY(),array('color'=>$this->pdf->textGrijs,'width'=>0.1));
       $this->pdf->ln(4);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+3);
      $this->pdf->row(array('','Totaal','','','','',$this->formatGetal($totalen['Bedrag'], 0,'€')));
      $this->pdf->ln(1);
      
		}
    $this->pdf->rowHeight=$rowHeightBackup;
    
/*
    if(count($aanwezigeTransactietypen)>0)
    {
      $this->pdf->ln(4);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->row(array('', 'Soort transactie', 'Omschrijving'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      foreach ($transactietypenOmschrijving as $key => $value)
      {
        if (isset($aanwezigeTransactietypen[$key]))
        {
          $this->pdf->row(array('', $key, $value));
        }
      }
    }
*/
	}
}
?>