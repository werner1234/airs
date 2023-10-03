<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L122
{

	function RapportPERF_L122($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";


		//$this->pdf->rapport_PERF_displayType

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

   
    if($rapportageDatumVanaf==$rapportageDatum && substr($rapportageDatumVanaf,5,5)=='01-01')
      $this->rapportageDatumVanaf=(substr($rapportageDatumVanaf,0,4)-1).'-12-31';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function printTotaal($title, $totaalA, $totaalB, $procent)
	{
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$extra = $this->pdf->rapport_PERF_lijnenKorter;

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2];

		$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		if(!empty($totaalA))
		{
			$this->pdf->Line($actueel+2+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthA[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[3],4,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[5],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalprtxt, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		return $totaalA;
	}

	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}


		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
  {
    global $__appvar;
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    
    if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
    {
      $koersQuery = " / (SELECT Koers FROM Valutakoersen WHERE Valuta='" . $this->pdf->rapportageValuta . "' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "";
    }
    
    if ($this->pdf->rapport_layout == 1)
    {
      $kopStyle = "";
    }
    else
    {
      $kopStyle = "u";
    }
    
    $DB = new DB();
    
    // voor data
    $this->pdf->widthA = array(5, 80, 30, 10, 30, 120);
    $this->pdf->alignA = array('L', 'L', 'R', 'R', 'R');
    
    // voor kopjes
    $this->pdf->widthB = array(1, 95, 30, 10, 30, 120);
    $this->pdf->alignB = array('L', 'L', 'R', 'R', 'R');
    
    $this->pdf->AddPage();
  
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r], $this->pdf->rapport_fontcolor[g], $this->pdf->rapport_fontcolor[b]);
    
    // ***************************** ophalen data voor afdruk ************************ //
    
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " AS totaal " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatum . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' "
      . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersBegin . " ) AS totaal " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatumVanaf . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' "
      . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    
    $DB->SQL($query);
    $DB->Query();
    $totaalWaardeVanaf = $DB->nextRecord();
    
    $waardeEind = $totaalWaarde[totaal];
    $waardeBegin = $totaalWaardeVanaf[totaal];
    $waardeMutatie = $waardeEind - $waardeBegin;
    $stortingen = getStortingen($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta);
    $onttrekkingen = getOnttrekkingen($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta);
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    $rendementProcent = performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
    //echo "$rendementProcent  	= performanceMeting(".$this->portefeuille.", ".$this->rapportageDatumVanaf.", ".$this->rapportageDatum.", ".$this->pdf->portefeuilledata['PerformanceBerekening'].",".$this->pdf->rapportageValuta." ";exit;
    if ($this->pdf->rapport_PERF_jaarRendement)
    {
      $RapStartJaar = date("Y", db2jul($this->rapportageDatum));
      if (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
      {
        $startDatum = $this->pdf->PortefeuilleStartdatum;
        $fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $this->pdf->PortefeuilleStartdatum, true);
        vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $this->pdf->PortefeuilleStartdatum);
      }
      else
      {
        $startDatum = "$RapStartJaar-01-01";
      }
      
      if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
      {
        $fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $startDatum, true);
        vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $startDatum);
      }
      $rendementProcentJaar = performanceMeting($this->portefeuille, $startDatum, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
    }
    
    // ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . "  AS totaalB, " .
      "SUM(beginPortefeuilleWaardeEuro)/ " . $this->pdf->ValutaKoersStart . "  AS totaalA " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatum . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' AND "
      . " type = 'fondsen' " . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaal = $DB->nextRecord();
    $ongerealiseerdeKoersResultaat = $totaal[totaalB] - $totaal[totaalA]; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.
    //echo " $query ".$ongerealiseerdeKoersResultaat." = ".$totaal[totaalB]." - ".$totaal[totaalA].";<br>\n";
//rvv 	Extra query die het mogelijk maakt om een startdatum na 1-1-jaar te kiezen. Het resultaat binnen het lopende jaar tot de start
//		datum wordt van het totaal afgehaald.
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersBegin . " AS totaalB, " .
      "SUM(beginPortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersStart . " ) AS totaalA " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatumVanaf . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' AND "
      . " type = 'fondsen' " . $__appvar['TijdelijkeRapportageMaakUniek'];
//		debugSpecial($query,__FILE__,__LINE__);
//		$DB->SQL($query);
//		$DB->Query();
//		$totaalWaardeVanaf = $DB->nextRecord();
//		$ongerealiseerdeKoersResultaatTotStart = $totaalWaardeVanaf[totaalB] - $totaalWaardeVanaf[totaalA];
//		$ongerealiseerdeKoersResultaat -= $ongerealiseerdeKoersResultaatTotStart;
    
    $RapJaar = date("Y", db2jul($this->rapportageDatum));
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    
    /*
    if ($RapJaar != $RapStartJaar) //Wanneer we startdatum in het afgelopen jaar kiezen moeten we de resultaten van dat jaar ook ophalen.
    {
          $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".getValutaKoers($this->pdf->rapportageValuta,$RapStartJaar."-12-31")."  AS totaalB, ".
                  "SUM(beginPortefeuilleWaardeEuro) / ".getValutaKoers($this->pdf->rapportageValuta,$RapStartJaar."-01-01")." AS totaalA ".
                 "FROM TijdelijkeRapportage WHERE ".
                 " rapportageDatum ='".$RapStartJaar."-12-31' AND ".
                 " portefeuille = '".$this->portefeuille."' AND ".
                 " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
        debugSpecial($query,__FILE__,__LINE__);
        $DB->SQL($query);
        $DB->Query();
        $totaalVorigeJaar = $DB->nextRecord();
        $ongerealiseerdeKoersResultaatVorigJaar = ($totaalVorigeJaar[totaalB] - $totaalVorigeJaar[totaalA]);
        $ongerealiseerdeKoersResultaat += $ongerealiseerdeKoersResultaatVorigJaar  ;
    }
    */
//rvv end
    $totaalOpbrengst += $ongerealiseerdeKoersResultaat;
    
    $gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta, true);
    $totaalOpbrengst += $gerealiseerdeKoersResultaat;
    
    // ophalen van rente totaal A en rentetotaal B
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatum . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' AND " .
      " type = 'rente' " . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalA = $DB->nextRecord();
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatumVanaf . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' AND " .
      " type = 'rente' " . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalB = $DB->nextRecord();
    
    $opgelopenRente = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
    $totaalOpbrengst += $opgelopenRente;
    
    if ($this->pdf->GrootboekPerVermogensbeheerder)
    {
      $query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
    }
    else
    {
      $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving" .
        " FROM Grootboekrekeningen " .
        " WHERE Grootboekrekeningen.Opbrengst = '1'  " .
        " ORDER BY Grootboekrekeningen.Afdrukvolgorde";
    }
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    while ($gb = $DB->nextRecord())
    {
      $query = "SELECT  " .
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles " .
        "WHERE " .
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
        "Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
        "Rekeningmutaties.Verwerkt = '1' AND " .
        "Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
        "Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' AND " .
        "Rekeningmutaties.Grootboekrekening = '" . $gb[Grootboekrekening] . "' ";
      
      $DB2 = new DB();
      $DB2->SQL($query);
      $DB2->Query();
      
      if ($this->pdf->rapport_layout == 7)
      {
        switch ($gb['Omschrijving'])
        {
          case "Creditrente" :
            $gb['Omschrijving'] = "Rente Bankrekeningen";
            break;
          case "Rente obligaties" :
            $gb['Omschrijving'] = "Ontvangen rente obligaties";
            break;
          case "Meegekochte rente" :
            $gb['Omschrijving'] = "Gekochte en verkochte couponrente";
            break;
        }
      }
      
      while ($opbrengst = $DB2->nextRecord())
      {
        $opbrengstenPerGrootboek[$gb['Omschrijving']] = ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
        $totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
      }
    }
    
    // loopje over Grootboekrekeningen Kosten = 1
    if ($this->pdf->GrootboekPerVermogensbeheerder)
    {
      $query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, " .
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder " .
        "WHERE " .
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
        "Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
        "Rekeningmutaties.Verwerkt = '1' AND " .
        "Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
        "Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' AND " .
        "GrootboekPerVermogensbeheerder.Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND " .
        "Rekeningmutaties.Grootboekrekening = GrootboekPerVermogensbeheerder.GrootboekRekening AND " .
        "GrootboekPerVermogensbeheerder.Kosten = '1' " .
        "GROUP BY Rekeningmutaties.Grootboekrekening " .
        "ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde ";
    }
    else
    {
      $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, " .
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, " .
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet " .
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen " .
        "WHERE " .
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
        "Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
        "Rekeningmutaties.Verwerkt = '1' AND " .
        "Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' AND " .
        "Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' AND " .
        "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND " .
        "Grootboekrekeningen.Kosten = '1' " .
        "GROUP BY Rekeningmutaties.Grootboekrekening " .
        "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
    }
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    
    $kostenPerGrootboek = array();
    
    while ($kosten = $DB->nextRecord())
    {
      if ($kosten[Grootboekrekening] == "KNBA")
      {
        if ($this->pdf->rapport_layout == 17 OR $this->pdf->rapport_layout == 10)
        {
          $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
        }
        else
        {
          $kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = "Bankkosten en provisie";
        }
        $kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      }
      else if ($kosten[Grootboekrekening] == "KOBU" && $this->pdf->rapport_layout != 14 && $this->pdf->rapport_layout != 10)
      {
        //$kostenPerGrootboek['KOST'][Omschrijving] = "Bankkosten en provisie";
        $kostenPerGrootboek['KOST'][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
        $kostenPerGrootboek['KOST']['omschrijving'] = "Transactiekosten";
      }
      else
      {
        $kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = $kosten['Omschrijving'];
        $kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      }
      
      $totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
    }
    
    $kostenProcent = ($totaalKosten / $waardeEind) * 100;
    
    
    // het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
    $koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst - $totaalKosten);
    $totaalOpbrengst += $koersResulaatValutas;
    // ***************************** einde ophalen data voor afdruk ************************ //
    
    $posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
    $posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    
    $extraLengte = $this->pdf->rapport_PERF_lijnenKorter;
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->ln();
    
    if ($this->pdf->rapport_PERF_displayType == 1 || $this->pdf->lastPOST['perfBm'])
    {
      $ypos = $this->pdf->GetY();
      
      $this->pdf->SetFont($this->pdf->rapport_font, 'b' . $kopStyle, $this->pdf->rapport_fontsize);
      $this->pdf->row(array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", ""));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
      
      $this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", $this->pdf->rapport_datumvanaf) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", $this->pdf->rapport_datumvanaf)], $this->pdf->rapport_taal) . " " . date("Y", $this->pdf->rapport_datumvanaf), $this->formatGetal($waardeBegin, 2, true), ""));
      $this->pdf->ln(2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->row(array("", vertaalTekst("Resultaat over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($resultaatVerslagperiode, 2), ""));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->ln(2);
      $this->pdf->row(array("", vertaalTekst("Totaal stortingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($stortingen, 2), ""));
      $this->pdf->ln(2);
      $this->pdf->row(array("", vertaalTekst("Totaal onttrekkingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($onttrekkingen, 2), ""));
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      $this->pdf->ln(2);
      $this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatum)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatum))], $this->pdf->rapport_taal) . " " . date("Y", db2jul($this->rapportageDatum)), $this->formatGetal($waardeEind, 2), ""));
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      
      $this->pdf->ln();
      $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      
      $this->pdf->row(array("", vertaalTekst("Rendement over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($rendementProcent, 2), "%"));
      if ($this->pdf->rapport_PERF_jaarRendement)
      {
        $this->pdf->row(array("", vertaalTekst("Rendement lopende kalenderjaar", $this->pdf->rapport_taal), $this->formatGetal($rendementProcentJaar, 2), "%", ""));
      }
      
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY() + 1, $posSubtotaalEnd, $this->pdf->GetY() + 1);
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $this->pdf->widthA = array(130, 80, 30, 5, 30, 120);
      $this->pdf->alignA = array('L', 'L', 'R', 'R', 'R');
      
      $this->pdf->widthB = array(125, 95, 30, 5, 30, 120);
      $this->pdf->alignB = array('L', 'L', 'R', 'R', 'R');
      
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
      
      $posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
      $posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    }
    else
    {
      $this->pdf->SetFont($this->pdf->rapport_font, 'b' . $kopStyle, $this->pdf->rapport_fontsize);
      $this->pdf->row(array("", vertaalTekst("Resultaat verslagperiode", $this->pdf->rapport_taal), "", ""));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
      
      $this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", $this->pdf->rapport_datumvanaf) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", $this->pdf->rapport_datumvanaf)], $this->pdf->rapport_taal) . " " . date("Y", $this->pdf->rapport_datumvanaf), $this->formatGetal($waardeBegin, 2, true), ""));
      $this->pdf->row(array("", vertaalTekst("Waarde portefeuille per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatum)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatum))], $this->pdf->rapport_taal) . " " . date("Y", db2jul($this->rapportageDatum)), $this->formatGetal($waardeEind, 2), ""));
      // subtotaal
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      $this->pdf->ln();
      $this->pdf->row(array("", vertaalTekst("Mutatie waarde portefeuille", $this->pdf->rapport_taal), $this->formatGetal($waardeMutatie, 2), ""));
      $this->pdf->row(array("", vertaalTekst("Totaal stortingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($stortingen, 2), ""));
      $this->pdf->row(array("", vertaalTekst("Totaal onttrekkingen gedurende verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($onttrekkingen, 2), ""));
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      $this->pdf->ln();
      $this->pdf->row(array("", vertaalTekst("Resultaat over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($resultaatVerslagperiode, 2), ""));
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY() + 1, $posSubtotaalEnd, $this->pdf->GetY() + 1);
      $this->pdf->ln();
      
      
      $this->pdf->row(array("", vertaalTekst("Rendement over verslagperiode", $this->pdf->rapport_taal), $this->formatGetal($rendementProcent, 2), "%"));
      if ($this->pdf->rapport_PERF_jaarRendement)
      {
        $this->pdf->row(array("", vertaalTekst("Rendement lopende kalenderjaar", $this->pdf->rapport_taal), $this->formatGetal($rendementProcentJaar, 2), "%", ""));
      }
      
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY() + 1, $posSubtotaalEnd, $this->pdf->GetY() + 1);
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
      $ypos = $this->pdf->GetY();
    }
    
    $this->pdf->SetY($ypos);
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b' . $kopStyle, $this->pdf->rapport_fontsize);
    $this->pdf->row(array("", vertaalTekst("Samenstelling resultaat over verslagperiode", $this->pdf->rapport_taal), "", ""));
    $this->pdf->SetFont($this->pdf->rapport_font, $kopStyle, $this->pdf->rapport_fontsize);
    $this->pdf->row(array("", vertaalTekst("Beleggingsresultaat", $this->pdf->rapport_taal), "", ""));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->row(array("", vertaalTekst("Ongerealiseerde koersresultaten", $this->pdf->rapport_taal), $this->formatGetal($ongerealiseerdeKoersResultaat, 2), ""));
    $this->pdf->row(array("", vertaalTekst("Gerealiseerde koersresultaten", $this->pdf->rapport_taal), $this->formatGetal($gerealiseerdeKoersResultaat, 2), ""));
    if (round($koersResulaatValutas, 2) != 0.00)
    {
      $this->pdf->row(array("", vertaalTekst("Koersresultaten valuta's", $this->pdf->rapport_taal), $this->formatGetal($koersResulaatValutas, 2), ""));
    }
    if ($this->pdf->rapport_layout == 5)
    {
      $this->pdf->row(array("", vertaalTekst("Mutatie opgelopen rente", $this->pdf->rapport_taal), $this->formatGetal($opgelopenRente, 2), ""));
    }
    else
    {
      $this->pdf->row(array("", vertaalTekst("Resultaat opgelopen rente", $this->pdf->rapport_taal), $this->formatGetal($opgelopenRente, 2), ""));
    }
    
    while (list($key, $value) = each($opbrengstenPerGrootboek))
    {
      if (round($value, 2) != 0.00)
      {
        $this->pdf->row(array("", vertaalTekst($key, $this->pdf->rapport_taal), $this->formatGetal($value, 2), ""));
      }
    }
    
    $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
    $this->pdf->row(array("", "", $this->formatGetal($totaalOpbrengst, 2)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    
    if(count($kostenPerGrootboek)>0)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, $kopStyle, $this->pdf->rapport_fontsize);
      $this->pdf->row(array("", vertaalTekst("Kosten", $this->pdf->rapport_taal), "", ""));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
  
      foreach ($kostenPerGrootboek as $key => $value)
      {
        if (round($kostenPerGrootboek[$key]['Bedrag'], 2) != 0.00)
        {
          $this->pdf->row(array("", vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'], $this->pdf->rapport_taal), $this->formatGetal($kostenPerGrootboek[$key]['Bedrag'], 2), ""));
        }
      }
  
      $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
      $this->pdf->row(array("", "", $this->formatGetal($totaalKosten, 2)));
    }
    else
    {
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
    }
    $posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    
    
    $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
    
    
    $this->pdf->row(array("", "", $this->formatGetal($totaalOpbrengst - $totaalKosten, 2)));
    
    //$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
    $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY(), $posSubtotaalEnd, $this->pdf->GetY());
    $this->pdf->Line($posSubtotaal + $extraLengte, $this->pdf->GetY() + 1, $posSubtotaalEnd, $this->pdf->GetY() + 1);
    
    
    $actueleWaardePortefeuille = 0;
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    if ($this->pdf->rapport_PERF_rendement == 1)
    {
      
      $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
      if (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
      {
        $startDatum = $this->pdf->PortefeuilleStartdatum;
      }
      else
      {
        $startDatum = "$RapStartJaar-01-01";
      }
      
      // if($rapportageDatum['a']==$rapportageDatum['b'] && substr($rapportageDatum['a'],5,5)=='01-01')
      //   vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,substr($rapportageDatum['a'],0,4).'-12-31');
      
      if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
      {
        $fondswaarden = berekenPortefeuilleWaarde($this->portefeuille, $startDatum, true);
        vulTijdelijkeTabel($fondswaarden, $this->portefeuille, $startDatum);
      }

//	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
//      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);
      
      $performanceJaar = performanceMeting($this->portefeuille, $startDatum, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
      $performancePeriode = performanceMeting($this->portefeuille, date('Y-m-d', $this->pdf->rapport_datumvanaf), $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
//		  $performanceBegin  = performanceMeting($this->portefeuille,$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,1,$this->pdf->rapportageValuta);
      
      $this->pdf->SetY($this->pdf->GetY() + 30);
      $extraMarge = 140;
      $this->pdf->SetX($this->pdf->marge);
      $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r], $this->pdf->rapport_kop_bgcolor[g], $this->pdf->rapport_kop_bgcolor[b]);
      $min = 6;
      $this->pdf->Rect($this->pdf->marge + $extraMarge, $this->pdf->getY(), 110, (20 - $min), 'F');
      $this->pdf->SetFillColor(0);
      $this->pdf->Rect($this->pdf->marge + $extraMarge, $this->pdf->getY(), 110, (20 - $min));
      $this->pdf->ln(2);
      
      $this->pdf->SetX($this->pdf->marge + $extraMarge + 10);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r], $this->pdf->rapport_kop_fontcolor[g], $this->pdf->rapport_kop_fontcolor[b]);
      $this->pdf->Cell(60, 4, vertaalTekst("Resultaat over verslagperiode", $this->rapport_taal), 0, 0, "L");
      $this->pdf->Cell(30, 4, $this->formatGetal($performancePeriode, 2) . "%", 0, 1, "R");
      $this->pdf->ln(2);
      
      $this->pdf->SetX($this->pdf->marge + $extraMarge + 10);
      $this->pdf->Cell(60, 4, vertaalTekst("Resultaat lopende kalenderjaar", $this->rapport_taal), 0, 0, "L");
      $this->pdf->Cell(30, 4, $this->formatGetal($performanceJaar, 2) . "%", 0, 1, "R");
      $this->pdf->ln(2);

//			$this->pdf->SetX($this->pdf->marge  +$extraMarge +10);
//	    $this->pdf->Cell(60,4, vertaalTekst("Resultaat vanaf begin beheer / ".jul2form(db2jul($this->pdf->PortefeuilleStartdatum)),$this->rapport_taal), 0,0, "L");
//		  $this->pdf->Cell(30,4, $this->formatGetal($performanceBegin,2)."%", 0,1, "R");
//		  $this->pdf->ln(2);
    }
    
    
    $query = "SELECT Portefeuilles.SpecifiekeIndex , Fondsen.Omschrijving
 FROM Portefeuilles JOIN Fondsen on Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
  WHERE Portefeuilles.Portefeuille = '" . $this->portefeuille . "' ";
    $DB->SQL($query);
    $data = $DB->lookupRecord();
    $specifiekeIndex = $data['SpecifiekeIndex'];
    $specifiekeIndexOmschrijving = $data['Omschrijving'];
    
    if ($this->pdf->rapport_PERF_portefeuilleIndex == 1)
    {
      $grafiekData = array();
      $query = "SELECT indexWaarde, Datum ,
		      (SELECT Koers  FROM Fondskoersen WHERE fonds = '" . $specifiekeIndex . "' AND MONTH(Datum) = MONTH(HistorischePortefeuilleIndex.Datum) ORDER BY Datum DESC limit 1) as specifiekeIndexWaarde

		           FROM HistorischePortefeuilleIndex WHERE periode='m' AND portefeuille = '" . $this->portefeuille . "' AND Datum < '" . $this->rapportageDatum . "'";
      $DB->SQL($query);
      $DB->Query();
      $n = 0;
      while ($data = $DB->nextRecord())
      {
        $grafiekData['Datum'][] = $data['Datum'];
        $grafiekData['Index'][] = ($data['indexWaarde']);
        $specifiekeIndexWaarde[$n] = $data['specifiekeIndexWaarde'];
        if ($n == 0)
        {
          
          $db2 = new DB();
          $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '" . $specifiekeIndex . "' AND Datum > '" . $grafiekData['Datum'][0] . "' LIMIT 1";
          $db2->sql($query);
          $db2->Query();
          $indexStart = $db2->lookupRecord();
          $grafiekData['Index1'][$n] = ($data['specifiekeIndexWaarde'] / $indexStart['Koers'] * 100);
        }
        else
        {
          $grafiekData['Index1'][$n] = ($data['specifiekeIndexWaarde'] / $indexStart['Koers'] * 100);
        }
        $n++;
      }
//		  listarray($indexStart);
//listarray($specifiekeIndexWaarde);
//listarray($grafiekData);
      if (count($grafiekData) > 1)
      {
        $color = array(30, 23, 96);
        $color1 = array(167, 26, 32);
        $this->pdf->SetXY(10, 108);
        $this->pdf->SetFont($this->pdf->rapport_font, 'b' . $kopStyle, $this->pdf->rapport_fontsize);
        $this->pdf->Cell(0, 5, 'Vermogensontwikkeling', 0, 1);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->SetX(15, $this->pdf->GetY() + 2);
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        $this->pdf->LineDiagram(108, 60, $grafiekData, array($color, $color1), 0, 0, 4, 4);
        
        $this->pdf->Rect($valX, $valY + 70, 3, 3, 'F', '', $color);
        $this->pdf->SetXY($valX + 4, $valY + 70);
        $this->pdf->Cell(0, 4, 'Portefeuille', 0, 0);
        
        $this->pdf->Rect($valX + 30, $valY + 70, 3, 3, 'F', '', $color1);
        $this->pdf->SetXY($valX + 4 + 30, $valY + 70);
        $this->pdf->Cell(0, 4, $specifiekeIndexOmschrijving, 0, 0);
        
        $this->pdf->SetXY($valX, $valY + 80);
      }
    }
    
    if ($this->pdf->rapport_PERF_liquiditeiten == 1)
    {
      $this->pdf->ln();
      
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font, 'b' . $kopStyle, $this->pdf->rapport_fontsize);
      $this->pdf->row(array("", vertaalTekst("Liquiditeiten", $this->pdf->rapport_taal), "", ""));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
      
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersBegin . " ) AS totaal " .
        "FROM TijdelijkeRapportage WHERE " .
        " rapportageDatum ='" . $this->rapportageDatumVanaf . "' AND " .
        " portefeuille = '" . $this->portefeuille . "' AND
						     type <> 'fondsen' AND type = 'rekening' "
        . $__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query, __FILE__, __LINE__);
      
      $DB->SQL($query);
      $DB->Query();
      $totaalWaardeLiquiditeitenVanaf = $DB->nextRecord();
      $this->pdf->row(array("", vertaalTekst("Saldo liquiditeiten per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatumVanaf)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatumVanaf))], $this->pdf->taal) . " " . date("Y", db2jul($this->rapportageDatumVanaf)), $this->formatGetal($totaalWaardeLiquiditeitenVanaf['totaal'], 2), ""));
      
      
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " AS totaal " .
        "FROM TijdelijkeRapportage WHERE " .
        " rapportageDatum ='" . $this->rapportageDatum . "' AND " .
        " portefeuille = '" . $this->portefeuille . "' AND
						     type <> 'fondsen' AND type = 'rekening' "
        . $__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query, __FILE__, __LINE__);
      $DB->SQL($query);
      $DB->Query();
      $totaalWaardeLiquiditeiten = $DB->nextRecord();
      $this->pdf->row(array("", vertaalTekst("Saldo liquiditeiten per", $this->pdf->rapport_taal) . " " . date("j", db2jul($this->rapportageDatum)) . " " . vertaalTekst($this->pdf->__appvar["Maanden"][date("n", db2jul($this->rapportageDatum))], $this->pdf->taal) . " " . date("Y", db2jul($this->rapportageDatum)), $this->formatGetal($totaalWaardeLiquiditeiten['totaal'], 2), ""));
      
      if ($this->pdf->lastPOST['perfBm'])
      {
        $this->pdf->ln();
        $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
      }
    }
    
    $this->perfG(140,50,135,70,vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal));
  }
  
  function perfG($xPositie,$yPositie,$width,$height,$title='')
  {
    $this->pdf->setXY($xPositie,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell(0,5,$title,'','C');
    $this->pdf->setXY($xPositie,$yPositie-5);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $this->pdf->Multicell(0,5,vertaalTekst('inclusief stortingen en onttrekkingen',$this->pdf->rapport_taal),'','C');
    
  //  $this->pdf->setXY($XDiag+$w+2,$yPositie-10);
  //  $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
  //  $this->pdf->Multicell($w,5,'X 1.000','','R');
    
    $this->pdf->setXY($xPositie,$yPositie);
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
    $DB = new DB();
    if(isset($this->pdf->portefeuilles))
      $port= "IN('".implode("','",$this->pdf->portefeuilles)."') ";
    else
      $port= "= '".$this->portefeuille."'";
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille $port AND Categorie = 'Totaal'  ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();
    
    if($datum['id'] > 0 )//&& $this->pdf->lastPOST['perfPstart'] == 1
    {
      if($datum['month'] <10)
        $datum['month'] = "0".$datum['month'];
      $start = $datum['year'].'-'.$datum['month'].'-01';
    }
    else
      $start = $this->pdf->portefeuilledata['Startdatum'];
    
    
    $eind = $this->rapportageDatum;
    

    
    $index = new indexHerberekening();
    $indexWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles));
    $aantalWaarden = count($indexWaarden);
    //echo $aantalWaarden;exit;
    $n=0;
    if($aantalWaarden < 13) // < dan een jaar gebruik maanden
    {
      $maandFilter=array(1,2,3,4,5,6,7,8,9,10,11,12);
    }
    elseif ($aantalWaarden < 49) // < 4 jaar gebruik kwartalen
    {
      $maandFilter=array(3,6,9,12);
    }
    else // gebruik jaren
    {
      $maandFilter=array(12);
    }
    
    foreach ($indexWaarden as $id=>$data)
    {
      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
        $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
      else
        $koers=1;
      $grafiekData['portefeuille'][$n]=$data['waardeHuidige']/$koers;
      $grafiekData['storingen'][$n]+=($data['stortingen']-$data['onttrekkingen'])/$koers;
      $datumArray[$n]=$data['datum'];
      $maand=date('m',db2jul($data['datum']));
      if(in_array($maand,$maandFilter))
        $n++;
    }
    
    
    $minVal = -1;
    $maxVal = 1;
    
    
    foreach ($grafiekData as $type=>$maxData)
    {
      foreach ($maxData as $waarde)
      {
        $maxVal=max($maxVal,$waarde);
        $minVal=min($minVal,$waarde);
      }
    }
    
    $w=$width;
    $h=$height;
    $horDiv = 10;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 0.10;
    
    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));
    
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    $horDiv=($maxVal - $minVal)/$stepSize*2;
    if($horDiv > 10)
      $horDiv=($maxVal - $minVal)/$stepSize;
    
    $legendYstep = round(($maxVal - $minVal) / $horDiv);
    $vBar = ($lDiag / (count($grafiekData['portefeuille'])+ 1));
    $bGrafiek = $vBar * (count($grafiekData['portefeuille']) + 1);
    $eBaton = ($vBar * .5);
    
    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'FD','',array(245,245,245));
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    $nulpunt = $YDiag + ($maxVal * $unith);
    $n=0;
    
    $this->pdf->Line($XDiag, $nulpunt, $XPage+$w ,$nulpunt,array('dash' => 1,'color'=>array(128,128,128)));
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$legendYstep)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
      $this->pdf->Text($XDiag+$w+2, $i, '€'.$this->formatGetal(0-($n*$legendYstep),0));
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$legendYstep)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag+$w+2, $i, '€'.$this->formatGetal($n*$legendYstep,0));
      $n++;
      if($n >20)
        break;
    }
    $n=0;
    $laatsteI = count($datumArray)-1;
    $lijnenAantal = count($grafiekData);
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.1));
    foreach ($grafiekData['storingen'] as $i=>$waarde)
    {
      $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
      $yval = $yval2;
      $xval = $XDiag + (1 + $i ) * $unitw - ($eBaton / 2);
      $lval = $eBaton;
      $hval = ($waarde * $unit);
      $hval =$nulpunt-$yval;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,array(145,182,215)); //  //0,176,88
    }
    unset($yval);
    
    $lineStyle = array('width' => 0.75, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    foreach ($grafiekData['portefeuille'] as $i=>$waarde)
    {
      if(!isset($datumPrinted[$i]))
      {
        $datumPrinted[$i] = 1;
        //if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
        $julDatum=db2jul($datumArray[$i]);
        $this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);
      }
      if($waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
        if($yval)
        {
          $markerSize=0.5;
          $this->pdf->line($XDiag+$i*$unitw, $yval, $XDiag+($i+1)*$unitw, $yval2,$lineStyle );
          $this->pdf->Rect($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,array(0,176,88));
        }
        $yval = $yval2;
      }
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->CellBorders = array();
  }
  
}
?>