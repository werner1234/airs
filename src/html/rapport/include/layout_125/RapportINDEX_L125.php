<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportINDEX_L125
{
	function RapportINDEX_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Vergelijking benchmark";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->aandeel=1;
	}
  
  function formatGetal($waarde, $dec, $teken='')
  {
    return formatGetal_L125($waarde, $dec, $teken);
  }

  
  function writeRapport()
  {
    global $__appvar;

    $huidigeJaar=substr($this->rapportageDatum,0,4);
    $index=new indexHerberekening();
    $perioden=$index->getJaren(db2jul($huidigeJaar - 4).'-01-01', db2jul($this->rapportageDatum));
    $header=array('','Naam index');
    $rekenPerioden=array();
    foreach(array_reverse($perioden) as $periode)
    {
      $jaar=substr($periode['stop'],0,4);
      if($jaar==$huidigeJaar)
      {
        $header[]='YTD';
        $rekenPerioden['YTD']=$periode;
      }
      else
      {
        $header[]=$jaar;
        $rekenPerioden[$jaar]=$periode;
      }
    }
    $query="SELECT
	Indices.Beursindex,
	Fondsen.Omschrijving as fondsOmschrijving,
	Fondsen.Valuta,
	Indices.toelichting,
	Beleggingscategorien.Omschrijving as beleggingscategorieOmschrijving
FROM
	Indices
JOIN Fondsen ON Indices.Beursindex = Fondsen.Fonds
INNER JOIN BeleggingscategoriePerFonds ON Indices.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
AND Indices.Beursindex = BeleggingscategoriePerFonds.Fonds
INNER JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE
	Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY
	Indices.Afdrukvolgorde";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();

    while($index = $DB->nextRecord())
    {
      foreach($rekenPerioden as $perodeTxt=>$periode)
      {
        $koers['start'] = globalGetFondsKoers($index['Beursindex'], $periode['start']);
        $koers['stop'] = globalGetFondsKoers($index['Beursindex'], $periode['stop']);
        $indexData[$index['beleggingscategorieOmschrijving']][$index['fondsOmschrijving']][$perodeTxt] = ($koers['stop'] - $koers['start']) / ($koers['start']/100);
      }
      
    }
 //listarray($indexData);
  
    $this->pdf->AddPage();
    subHeader_L125($this->pdf, 28, array(90, 280), array('Indexvergelijking', 'Resultaten'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $cellWidth=25;
    $this->pdf->setAligns(array('L','L','R','R','R','R','R','R','R','R'));
    $this->pdf->setWidths(array(20-$this->pdf->marge,80,$cellWidth,$cellWidth,$cellWidth,$cellWidth,$cellWidth,$cellWidth,$cellWidth,$cellWidth));
    $this->pdf->ln(12);
    $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->row($header);
    $this->pdf->setTextColor(0);
    foreach($indexData as $categorie=>$categorieData)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+1);
      $this->pdf->ln();
      $this->pdf->row(array('',$categorie));
      $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($categorieData as $fonds=>$rendementsWaarden)
      {
        $row=array('',$fonds);
        foreach($rendementsWaarden as $periodeTxt=>$rendement)
          $row[]=$this->formatGetal($rendement,2,'%');
        $this->pdf->Row($row);
      }
      
    }
  
  
  
    if(substr($rekenPerioden['YTD']['start'],5,5)=='12-31')
    {
      $start=(substr($rekenPerioden['YTD']['start'],0,4)+1).'-01-01';
    }
    else
    {
      $start=$rekenPerioden['YTD']['start'];
    }
    $query = "SELECT TijdelijkeRapportage.valuta,Valutas.Omschrijving,Valutas.Afdrukvolgorde,Valutas.Valutateken FROM TijdelijkeRapportage
Inner Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' GROUP BY Valuta
ORDER BY Valutas.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $valutaRendementen=array();
    $test=array(array('valuta'=>'USD','Omschrijving'=>'US Dollar','Afdrukvolgorde'=>1,'ValutaTeken'=>'$'));
    foreach($test as $valuta)
    //while($valuta = $DB->nextRecord())
    {
      $koers=$valuta;
      $koers['start'] = globalGetValutaKoers($koers['valuta'], $start);
      $koers['stop'] = globalGetValutaKoers($koers['valuta'], $this->rapportageDatum);
      $koers['rendement']=($koers['stop'] - $koers['start']) / ($koers['start']/100);
      $valutaRendementen[$valuta['Omschrijving']]=$koers;
    }
    $y=$this->pdf->getY()+8;
    subHeader_L125($this->pdf, $y, array(100, 280), array('Actuele valutakoers ', ''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(12);
   // $this->pdf->setAligns(array('L','L','L','L','L'));
    $this->pdf->setWidths(array(20-$this->pdf->marge,80,$cellWidth+$cellWidth,$cellWidth+$cellWidth,$cellWidth+$cellWidth));
    

    $beginPeriodeTxt=date("j",db2jul($start))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($start))],$this->pdf->taal)." ".date("Y",db2jul($start));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
    $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->row(array('','Wisselkoers','Koers '.$beginPeriodeTxt,'Koers '.$eindPeriodeTxt,'Verschil wisselkoers'));
    $this->pdf->ln();
    $this->pdf->setTextColor(0);
    foreach($valutaRendementen as $valuta=>$details)
    {
      $this->pdf->row(array('','€ 1 is gelijk aan',
                        $this->formatGetal($details['start'],4,(trim($details['Valutateken'])<>''?$details['Valutateken']:$details['valuta'])),
                        $this->formatGetal($details['stop'],4,(trim($details['Valutateken'])<>''?$details['Valutateken']:$details['valuta'])),
                        $this->formatGetal($details['rendement'],1,($details['rendement']>0?'+':'-')).'*'));
    }
    $this->pdf->ln();
    $this->pdf->setTextColor($this->pdf->textGrijs[0],$this->pdf->textGrijs[1],$this->pdf->textGrijs[2]);
    
    $txt="* Een positief resultaat betekent dat de dollar sterker werd ten opzichte van de euro. Als de dollar meer waard wordt, stijgen de koersen van uw
Amerikaanse aandelen mee. Omdat u voor een aanzienlijk deel belegt in dollar-genoteerde indextrackers en -fondsen, is een sterkere dollar ten opzichte
van de euro gunstig voor uw totaalrendement.";
    $this->pdf->SetX(20);
    $this->pdf->MultiCell($this->pdf->w-20*2,6,$txt,0,'L');

  }
  
  
}

