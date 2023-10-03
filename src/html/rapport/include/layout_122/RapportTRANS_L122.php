<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANS_L122
{
	function RapportTRANS_L122($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Transactie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;


	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
 
	function toonTotaal($fondsData)
  {
    $this->pdf->CellBorders = array('T','T','T','T','T','T','T','T');
    $this->pdf->row(array('Totaal',
                      $this->formatGetal($fondsData['actueleFonds'],2),
                      $this->formatGetal($fondsData['totaalAantal'],4),
                       $this->formatGetal($fondsData['Investering'],0),
                      $this->formatGetal($fondsData['actuelePortefeuilleWaardeEuro'],0),
                      $this->formatGetal($fondsData['actuelePortefeuilleWaardeEuro']-$fondsData['Investering'],0),
                      '',//$this->formatGetal(($fondsData['actuelePortefeuilleWaardeEuro']-$fondsData['Investering'])/$fondsData['Investering']*100,2).'%',
                      ''
                    ));
    unset($this->pdf->CellBorders);
  }


	function writeRapport()
	{
    global $__appvar;
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		// voor data
		$this->pdf->widthA = array(20,25,25,25,25,25,25,25,25,25);//,19.5,19.5,19,11=69
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
	
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
		$this->pdf->setWidths($this->pdf->widthA);
    $this->pdf->setAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

    
		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.id,
		 Rekeningmutaties.Bedrag,
	   Rekeningmutaties.Transactietype,
		 Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers ".
		"FROM Rekeningmutaties
		 JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
		 JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
		 JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
		 JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
		"WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum >= '".$this->pdf->portefeuilledata['Startdatum']."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Fonds, Rekeningmutaties.Boekdatum, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();


		$buffer = array();
    $huidigeKoersen=array();
		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
      $huidigeKoersen[$mutaties['Fonds']]['Fonds']=$mutaties['Fonds'];
      if(!isset($huidigeKoersen[$mutaties['Fonds']]['Fondskoers']))
        $huidigeKoersen[$mutaties['Fonds']]['Fondskoers']=globalGetFondsKoers($mutaties['Fonds'],$this->rapportageDatum);
		}

    
    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      "IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
          " TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ".
      " FROM TijdelijkeRapportage WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.type =  'fondsen' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
    $DB->SQL($query);
    $DB->Query();
    $huidigeFonds=array();
    while($mutaties = $DB->nextRecord())
    {
      $huidigeFonds[$mutaties['fonds']]=$mutaties;
    }
    
    $lastFonds='';
		foreach ($buffer as $mutaties)
		{
		  if($lastFonds=='' || $lastFonds <> $mutaties['Fonds'])
      {
        if($lastFonds!='')
        {
          $this->toonTotaal($huidigeFonds[$lastFonds]);
          $this->pdf->ln();
        }
        $lastFonds=$mutaties['Fonds'];
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->Cell(100,$this->pdf->rowHeight,$mutaties['Omschrijving']."  (€ ".$this->formatGetal($huidigeKoersen[$mutaties['Fonds']]['Fondskoers'],2).")",0,1);
        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
      }
      
      $datum=date('d-m-Y',db2jul($mutaties['Boekdatum']));
		  $huidigeWaarde=$mutaties['Fondseenheid']*$mutaties['Aantal']*$huidigeKoersen[$mutaties['Fonds']]['Fondskoers'];
		  if($mutaties['Aantal']>0)
      {
        $huidigeWaardeTxt = $this->formatGetal($huidigeWaarde, 0);
        $meerWaardeTxt= $this->formatGetal($huidigeWaarde-$mutaties['Bedrag']*-1,0);
        $rendement=($huidigeWaarde-$mutaties['Bedrag']*-1)/($mutaties['Bedrag']*-1)*100;
        $rendementTxt=$this->formatGetal($rendement,2)."%";
        $jaarDeel=(db2jul($this->rapportageDatum)-db2jul($mutaties['Boekdatum']))/(365.25*24*3600);
        $geanualiseerd=(pow(1+($rendement/100),1/$jaarDeel)-1)*100;
        $geanualiseerdTxt=$this->formatGetal($geanualiseerd,2)."%";
      }
		  else
      {
        $huidigeWaardeTxt = '';
        $meerWaardeTxt='';
        $rendementTxt='';
        $geanualiseerdTxt='';
        $jaarDeel='';
      }
      $this->pdf->row(array($datum,
                        $this->formatGetal($mutaties['Fondskoers'],2),
                        $this->formatGetal($mutaties['Aantal'],4),
                        $this->formatGetal($mutaties['Bedrag']*-1,0),
                        $huidigeWaardeTxt,
                        $meerWaardeTxt,
                        $rendementTxt,
                        $geanualiseerdTxt //,$this->formatGetal($jaarDeel,2)
                      ));
      $huidigeFonds[$mutaties['Fonds']]['Investering']+=($mutaties['Bedrag']*-1);
    }
    if($lastFonds!='')
    {
      $this->toonTotaal($huidigeFonds[$lastFonds]);
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);



	}
}
?>