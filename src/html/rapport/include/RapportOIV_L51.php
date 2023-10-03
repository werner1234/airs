<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIV_L51
{
	function RapportOIV_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->volkRapport=false;
		$this->bedragDecimalen=0;

		$this->pdf->rapport_titel = "Vergelijkend historisch overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}
  
  
  function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
    $rente=array();
    $aantal=array();
    $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $totaal=0;
    while($data = $DB->nextRecord())
    {
      if($data['type']=='rente')
        $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
      elseif($data['type']=='fondsen')
        $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
    }
    
    $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
    $totaalCorrected=$totaal;
    
    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND
     Grootboekrekeningen.Opbrengst=1";
    $DB->SQL($query);
    $DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    {
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      }
      // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();


		$this->pdf->widthB = array(10,50,18,15,22,22,1,15,22,22,15,22,15,22,15);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(60,18,15,22,22,1,15,25,22,12,22,15,22,15);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');



		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
		$divPerCategorie=array();
    if($this->pdf->volkRapport==true)
    {
      $query = "SELECT TijdelijkeRapportage.fonds,TijdelijkeRapportage.Beleggingscategorie  FROM TijdelijkeRapportage ".
        " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND
		 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'" .$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);//echo $query;exit;
      $DB->Query();
      while ($fondsen = $DB->NextRecord())
      {
        $div=$this->getDividend($fondsen['fonds']);
        $divPerCategorie[$fondsen['Beleggingscategorie']]['totaal']+=$div['totaal'];
        $divPerCategorie[$fondsen['Beleggingscategorie']]['corrected']+=$div['corrected'];
      }
    }
		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.valuta,
		SUM( (actuelePortefeuilleWaardeInValuta-(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid))*actueleValuta)/".$this->pdf->ValutaKoersEind." as  fondsResultaat, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers / TijdelijkeRapportage.historischeRapportageValutakoers) AS subtotaalhistorisch,".
    " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." AS subtotaalactueel,
    
    SUM((actuelePortefeuilleWaardeInValuta - beginPortefeuilleWaardeInValuta) * actueleValuta)/".$this->pdf->ValutaKoersEind." AS VOLKfondsResultaat,
    SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersBegin." AS VOLKbeginwaarde
    
    ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type IN('fondsen','rente') AND
		 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'" .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie".
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);//echo $query;exit;
		$DB->Query();
    $regels=array();
    while($categorien = $DB->NextRecord())
    {
      $regels[]=$categorien;
    }
    if(count($regels)==0)
    {
      $this->pdf->noHeader=true;
    }
    else
    {
      $this->pdf->noHeader=false;
    }
    $this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    unset($this->pdf->noHeader);
    
    $subtotaal=array();
		foreach ($regels as $categorien)
		{
		  if($categorien['Omschrijving']=='Liquiditeiten')
        $categorien['Omschrijving']='Liquiditeiten (fondsen)';
		  if($this->pdf->volkRapport==true)
      {

        $procentResultaat = (($categorien['subtotaalactueel'] - $categorien['VOLKbeginwaarde'] + $divPerCategorie[$categorien['beleggingscategorie']]['corrected']) / ($categorien['VOLKbeginwaarde'] / 100));
        $valutaResultaat = $categorien['subtotaalactueel'] - $categorien['VOLKbeginwaarde'] - $categorien['VOLKfondsResultaat'];
      
        if ($categorien['VOLKbeginwaarde'] < 0)
        {
          $procentResultaat = -1 * $procentResultaat;
        }
        $percentageTotaalTekst = $this->formatGetal($procentResultaat, $this->pdf->rapport_VHO_decimaal_proc);
      
        $this->pdf->row(array($categorien['Omschrijving'],'',
                          $this->formatGetal($categorien['VOLKbeginwaarde'], $this->bedragDecimalen),'',
                          $this->formatGetal($categorien['subtotaalactueel'], $this->bedragDecimalen),
                          $this->formatGetal($categorien['VOLKfondsResultaat'], $this->bedragDecimalen),
                          $this->formatGetal($valutaResultaat, $this->bedragDecimalen),
                          $percentageTotaalTekst));
      }
      else //VHO rapport
      {
        $procentResultaat = (($categorien['subtotaalactueel'] - $categorien['subtotaalhistorisch']) / ($categorien['subtotaalhistorisch'] / 100));
        $valutaResultaat = $categorien['subtotaalactueel'] - $categorien['subtotaalhistorisch'] - $categorien['fondsResultaat'];
  
        if ($categorien['subtotaalhistorisch'] < 0)
        {
          $procentResultaat = -1 * $procentResultaat;
        }
        $percentageTotaalTekst = $this->formatGetal($procentResultaat, $this->pdf->rapport_VHO_decimaal_proc);
  
        $this->pdf->row(array($categorien['Omschrijving'],'',
                          $this->formatGetal($categorien['subtotaalhistorisch'], $this->bedragDecimalen),'',
                          $this->formatGetal($categorien['subtotaalactueel'], $this->bedragDecimalen),
                          $this->formatGetal($categorien['fondsResultaat'], $this->bedragDecimalen),
                          $this->formatGetal($valutaResultaat, $this->bedragDecimalen),
                          $percentageTotaalTekst));
      }
      
      
      $actueleWaardePortefeuille+=$categorien['subtotaalactueel'];
      $subtotaal['subtotaalactueel']+=$categorien['subtotaalactueel'];
      $subtotaal['VOLKbeginwaarde']+=$categorien['VOLKbeginwaarde'];
      
		}
  
		if($subtotaal['subtotaalactueel']<>0)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array('','', array('SUB'),'', array('SUB'));
      $this->pdf->row(array('Subtotaal','',
                        $this->formatGetal($subtotaal['VOLKbeginwaarde'], $this->bedragDecimalen),'',
                        $this->formatGetal($subtotaal['subtotaalactueel'], $this->bedragDecimalen),
                        '', '', ''));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->ln();
      unset($this->pdf->CellBorders);
    }
    
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), array_sum($this->pdf->widths), 8 , 'F');
    
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
//Categorie
    if($this->pdf->volkRapport==true)
    {
      $this->pdf->row(array(
                        "\n".vertaalTekst("Categorie",$this->pdf->rapport_taal),
                        vertaalTekst("Periode begin",$this->pdf->rapport_taal)."\n".vertaalTekst("waarde in valuta",$this->pdf->rapport_taal),
                        vertaalTekst("Periode begin",$this->pdf->rapport_taal)."\n".vertaalTekst("waarde in EUR",$this->pdf->rapport_taal),
                        vertaalTekst("Actuele",$this->pdf->rapport_taal)."\n".vertaalTekst("waarde in valuta",$this->pdf->rapport_taal),
                        vertaalTekst("Actuele",$this->pdf->rapport_taal)."\n".vertaalTekst("waarde in EUR",$this->pdf->rapport_taal),
                        '','','',
                      ));
      
    }
    else
    {
      $this->pdf->row(array(
                        "\n".vertaalTekst("Categorie",$this->pdf->rapport_taal),
                        '',
                        '',
                        vertaalTekst("Actuele",$this->pdf->rapport_taal)."\n".vertaalTekst("waarde in valuta",$this->pdf->rapport_taal),
                        vertaalTekst("Actuele",$this->pdf->rapport_taal)."\n".vertaalTekst("waarde in EUR",$this->pdf->rapport_taal),
                        '','',''
                      ));
      
    }
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->row(array(vertaalTekst("Liquiditeiten", $this->pdf->rapport_taal)));

		/*
		 $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, " .
          " TijdelijkeRapportage.actueleValuta , " .
          " TijdelijkeRapportage.rentedatum, " .
          " TijdelijkeRapportage.renteperiode, " .
          " TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, " .
          " TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /" . $this->pdf->ValutaKoersEind . " as actuelePortefeuilleWaardeEuro, " .
          " TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille " .
          " FROM TijdelijkeRapportage WHERE " .
          " TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
          " TijdelijkeRapportage.type = 'rente'   " .
          " AND TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
          . $__appvar['TijdelijkeRapportageMaakUniek'] .
          " ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
        debugSpecial($subquery, __FILE__, __LINE__);
        $DB2 = new DB();
        $DB2->SQL($subquery);
        $DB2->Query();
        $rente=0;
        while ($data = $DB2->NextRecord())
        {

          $actueleWaardePortefeuille+=$data['actuelePortefeuilleWaardeEuro'];
          $rente+=$data['actuelePortefeuilleWaardeEuro'];
          
        }
    
    if($rente<>0)
    {
      $this->pdf->row(array(vertaalTekst("    "."Opgelopen Rente", $this->pdf->rapport_taal),
                        '',
                        $this->formatGetal($rente, $this->bedragDecimalen),
                        '', '', ''));
    }
		*/
  
		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta ,  TijdelijkeRapportage.rekening as zoekRekening, Depotbanken.Omschrijving as depotbankOmschrijving,".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro , ".
      " (SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = zoekRekening AND type='rekening'  LIMIT 1)  / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro,".
      " (SELECT actuelePortefeuilleWaardeInValuta FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = zoekRekening AND type='rekening'  LIMIT 1) as beginPortefeuilleWaardeValuta,".
      " TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage
			JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			LEFT JOIN Depotbanken on Rekeningen.Depotbank=Depotbanken.Depotbank WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc,TijdelijkeRapportage.rekening";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
    $subtotaal=array();
		if($DB1->records() >0)
		{

			while($data = $DB1->NextRecord())
			{
        
        if($this->pdf->volkRapport==true)
        {
          $this->pdf->row(array("    ".vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal).' '.$data['zoekRekening']." ".$data['depotbankOmschrijving'],//$data['rekening']." / ".$data['valuta'],
                            $this->formatGetal($data['beginPortefeuilleWaardeValuta'], $this->bedragDecimalen),
                            $this->formatGetal($data['beginPortefeuilleWaardeEuro'], $this->bedragDecimalen),
                            $this->formatGetal($data['actuelePortefeuilleWaardeInValuta'], $this->bedragDecimalen),
                            $this->formatGetal($data['actuelePortefeuilleWaardeEuro'], $this->bedragDecimalen),
                            '', '', ''));
        }
        else
        {
          $this->pdf->row(array("    ".$data['rekening']." / ".$data['valuta'],
                            '',//$this->formatGetal($data['beginPortefeuilleWaardeValuta'], $this->bedragDecimalen),
                            '',//$this->formatGetal($data['beginPortefeuilleWaardeEuro'], $this->bedragDecimalen),
                            $this->formatGetal($data['actuelePortefeuilleWaardeInValuta'], $this->bedragDecimalen),
                            $this->formatGetal($data['actuelePortefeuilleWaardeEuro'], $this->bedragDecimalen),
                            '', '', ''));
  
        }

        $actueleWaardePortefeuille+=$data['actuelePortefeuilleWaardeEuro'];
        $subtotaal['beginPortefeuilleWaardeEuro']+=$data['beginPortefeuilleWaardeEuro'];
        $subtotaal['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
        
      }
		}
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    if($this->pdf->volkRapport==true)
    {
      $this->pdf->CellBorders = array('', '', array('SUB'), '', array('SUB'));
      $this->pdf->row(array('Subtotaal',
                        '',
                        $this->formatGetal($subtotaal['beginPortefeuilleWaardeEuro'], $this->bedragDecimalen),
                        '',
                        $this->formatGetal($subtotaal['actuelePortefeuilleWaardeEuro'], $this->bedragDecimalen),
                        '', '', ''));
    }
    else
    {
      $this->pdf->CellBorders = array('', '', '', '', array('SUB'));
      $this->pdf->row(array('Subtotaal',
                        '',
                        '',
                        '',
                        $this->formatGetal($subtotaal['actuelePortefeuilleWaardeEuro'], $this->bedragDecimalen),
                        '', '', ''));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    unset($this->pdf->CellBorders);
    
    // check op totaalwaarde!
    if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
    {
      echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
      ob_flush();
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','','','',array('SUB'));
    $this->pdf->row(array('Totale actuele waarde portefeuille',
                      '','','',
                      $this->formatGetal($actueleWaardePortefeuille, $this->bedragDecimalen),
                      '', '', ''));
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
		$this->pdf->ln();
/*
		if($this->pdf->rapport_VHO_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
*/

	}
}
?>