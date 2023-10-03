<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportMUT_L125
{
	function RapportMUT_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutaties";

		if ($this->pdf->rapportageValuta != 'EUR' || $this->pdf->rapportageValuta == '')
		 $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec,$teken='',$nullOnderdrukken=false)
	{
	  if($nullOnderdrukken==true && round($waarde,2) == 0)
	    return '';
    return formatGetal_L125($waarde, $dec, $teken);
	}



	function writeRapport()
	{
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";



		
    $query="SELECT Fondsen.Omschrijving as fondsOmschrijving,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Omschrijving,
ABS(Rekeningmutaties.Aantal) AS Aantal,
Rekeningmutaties.Debet $koersQuery AS Debet,
Rekeningmutaties.Credit $koersQuery AS Credit,
Rekeningmutaties.Valutakoers,
Rekeningmutaties.Rekening,
Rekeningmutaties.Grootboekrekening,
Rekeningmutaties.Afschriftnummer,
Grootboekrekeningen.Omschrijving as gbOmschrijving,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Afdrukvolgorde,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
if(Rekeningmutaties.Grootboekrekening='DIV',Beleggingscategorien.Afdrukvolgorde,'') as divVolgorde,
if(substr(Rekeningmutaties.Grootboekrekening,1,3)='DIV',KeuzePerVermogensbeheerder.Afdrukvolgorde,200) as divCaTVolgorde
FROM
Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
LEFT JOIN BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '". $this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT JOIN Fondsen ON Rekeningmutaties.Fonds=Fondsen.Fonds
LEFT JOIN KeuzePerVermogensbeheerder ON BeleggingscategoriePerFonds.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie='Beleggingscategorien'
WHERE  Rekeningen.Portefeuille = '".$this->portefeuille."'  AND
Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND 
(Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')
ORDER BY Grootboekrekeningen.Afdrukvolgorde,divCaTVolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id
";
    

//LEFT JOIN KeuzePerVermogensbeheerder ON Rekeningmutaties.Grootboekrekening = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie='Grootboekrekeningen'
    $DB = new DB();
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();
		$mutaties=array();
		while($data = $DB->nextRecord())
    {
      if($data['categorieOmschrijving']=='')
        $data['categorieOmschrijving']='geen';
      if($data['fondsOmschrijving']<>'')
        $data['Omschrijving']=$data['fondsOmschrijving'];
      $gbOmschrijving=$data['gbOmschrijving'];
      if($data['Grootboekrekening']=='DIVBE')
      {
        $gbOmschrijving = 'Dividend';
        $mutaties[$gbOmschrijving][$data['categorieOmschrijving']][$data['Boekdatum'].$data['Omschrijving']]['belasting']+=($data['Credit']-$data['Debet'])*$data['Valutakoers'];
      }
      elseif($data['Grootboekrekening']=='DIV')
      {
        $mutaties[$gbOmschrijving][$data['categorieOmschrijving']][$data['Boekdatum'].$data['Omschrijving']]['opbrensgt']+=($data['Credit']-$data['Debet'])*$data['Valutakoers'];
      }
      else
      {
        $mutaties[$gbOmschrijving][$data['categorieOmschrijving']][$data['Boekdatum'].$data['Omschrijving']]['kosten']+=($data['Credit']-$data['Debet'])*$data['Valutakoers'];
      }
      $mutaties[$gbOmschrijving][$data['categorieOmschrijving']][$data['Boekdatum'].$data['Omschrijving']]['Boekdatum']=$data['Boekdatum'];
      $mutaties[$gbOmschrijving][$data['categorieOmschrijving']][$data['Boekdatum'].$data['Omschrijving']]['Omschrijving']=$data['Omschrijving'];
      
      
    }
    
    $this->pdf->AddPage();
		$widths=array(20-$this->pdf->marge,20,80,25,30,30,30,30);
		
		$this->pdf->setAligns(array('L','L','L','R','R','R','R'));
		$headerFonds=array('','Datum','Belegging','','Bruto opbrengst','Belasting','Netto opbrengst');
    $headerKost=array('','Datum','Omschrijvng','Kosten');
    $totalen=array();
    $prefix='€';
		foreach($mutaties as $gbOmschrijving=>$categorieData)
    {
      subHeader_L125($this->pdf, $this->pdf->getY(), array(140), array($gbOmschrijving));
      
      $this->pdf->setWidths($widths);
      $this->pdf->ln(8);
      $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->ln(3);
      if($gbOmschrijving=='Dividend')
      {

        $this->pdf->row($headerFonds);
      }
      else
      {
        $this->pdf->row($headerKost);
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $subtotalen=array();
      foreach($categorieData as $categorie=>$regels)
      {
        $this->pdf->ln(2);
        if($categorie<>'geen')
        {
          $this->pdf->setX(20);
          $this->pdf->setTextColor($this->pdf->textGroen[0], $this->pdf->textGroen[1], $this->pdf->textGroen[2]);
          $this->pdf->Cell(100, $this->pdf->rowHeight, $categorie);
          $this->pdf->ln(6);
        }
        $this->pdf->setTextColor(0);
        foreach($regels as $regel)
        {
          $boekJul=db2jul($regel['Boekdatum']);
          $maand=vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$boekJul)],$this->pdf->taal);
  
          if($gbOmschrijving=='Dividend')
          {
            $this->pdf->row(array('',date("d ", db2jul($boekJul)).$maand, $regel['Omschrijving'], '',  $this->formatGetal($regel['opbrensgt'], 2,$prefix), $this->formatGetal($regel['belasting'], 2,$prefix),  $this->formatGetal($regel['opbrensgt'] + $regel['belasting'], 2,$prefix)));
          }
          else
          {
            $this->pdf->row(array('',date("d ", db2jul($boekJul)).$maand, $regel['Omschrijving'],  $this->formatGetal($regel['kosten'], 2,$prefix)));
          }
          $subtotalen['opbrensgt']+=$regel['opbrensgt'];
          $subtotalen['belasting']+=$regel['belasting'];
          $subtotalen['netto']+=$regel['opbrensgt']+$regel['belasting'];
          $subtotalen['kosten']+=$regel['kosten'];
          $totalen['opbrensgt']+=$regel['opbrensgt'];
          $totalen['belasting']+=$regel['belasting'];
          $totalen['netto']+=$regel['opbrensgt']+$regel['belasting'];
          $totalen['kosten']+=$regel['kosten'];
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+1);
      $this->pdf->ln(2);
      $this->pdf->row(array('','','',$this->formatGetal($subtotalen['kosten'],2,$prefix,true),$this->formatGetal($subtotalen['opbrensgt'],2,$prefix,true),$this->formatGetal($subtotalen['belasting'],2,$prefix,true),$this->formatGetal($subtotalen['netto'],2,$prefix,true)));
      $this->pdf->ln(2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    }
    $this->pdf->ln();
    $this->pdf->line(20,$this->pdf->getY(),$this->pdf->w-20,$this->pdf->getY(),array('color'=>$this->pdf->textGrijs,'width'=>0.1));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+1);
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totalen['kosten'],2,$prefix),$this->formatGetal($totalen['opbrensgt'],2,$prefix),$this->formatGetal($totalen['belasting'],2,$prefix),$this->formatGetal($totalen['netto'],2,$prefix)));
    
   //
   // listarray($mutaties);
	}
}
?>