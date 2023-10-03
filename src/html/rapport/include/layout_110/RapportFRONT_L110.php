<?php

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L110
{
	function RapportFRONT_L110($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();

	}

	


	function writeRapport()
	{
		global $__appvar;

		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');
		if(is_file($this->pdf->rapport_logo))
		{
      $xSize=30;
      //$logopos=($this->pdf->w/2)-($xSize/2);
      $logopos=($this->pdf->w)-($xSize)-$this->pdf->marge;
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 10, $xSize);
		}

   	$this->pdf->widthA = array($this->pdf->w-$this->pdf->marge*2);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = 16; //$this->pdf->rapport_fontsize
		$kwartalen=array(1=>'eerste',2=>'tweede',3=>'derde',4=>'vierde');
		$kwartaalNum=intval(ceil(date("n",$this->rapportageDatumJul)/3));

		$onderwerp=vertaalTekst('Kwartaaloverzicht',$this->pdf->rapport_taal).' '.vertaalTekst($kwartalen[$kwartaalNum],$this->pdf->rapport_taal).' '.vertaalTekst('kwartaal',$this->pdf->rapport_taal).' '.date("Y",$this->rapportageDatumJul);
    $this->pdf->SetWidths($this->pdf->widthA);

    $this->pdf->SetY(15);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->setAligns(array('C'));
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->row(array($onderwerp));
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
    $this->pdf->setAligns($this->pdf->alignA);
    $fontsize = $this->pdf->rapport_fontsize;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array($this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array($this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(1);
    $this->pdf->row(array($this->pdf->portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array($this->pdf->portefeuilledata['Woonplaats']));
    $this->pdf->ln(1);
    $this->pdf->row(array(vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal).': '.formatPortefeuille($this->pdf->portefeuilledata['Portefeuille'])));
    

		$this->pdf->ln(1);
		$this->pdf->row(array(vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(1);
    $this->pdf->row(array(vertaalTekst('Onderwerp',$this->pdf->rapport_taal).' '.$onderwerp));
    
    $this->pdf->ln(2);
		
		$this->PerfData();

	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   
   // $this->pdf->rapport_type = "INHOUD";
	 // $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	//  $this->pdf->addPage('L');
	//  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
	function PerfData()
	{
    global $__appvar;
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    
    if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $DB = new DB();
    
    // voor data
    $this->pdf->widthA = array(80,30,5,30,120);
    $this->pdf->alignA = array('L','R','R','R');
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    // ***************************** ophalen data voor afdruk ************************ //
    
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    
    $DB->SQL($query);
    $DB->Query();
    $totaalWaardeVanaf = $DB->nextRecord();
    $totaalOpbrengst    = 0;
    $totaalKosten       = 0;
    $waardeEind				  = $totaalWaarde['totaal'];
    $waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
    $waardeMutatie 	   	= $waardeEind - $waardeBegin;
    $stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
    $onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    $rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    //echo "$rendementProcent  	= performanceMeting(".$this->portefeuille.", ".$this->rapportageDatumVanaf.", ".$this->rapportageDatum.", ".$this->pdf->portefeuilledata['PerformanceBerekening'].",".$this->pdf->rapportageValuta." ";exit;

    // ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind."  AS totaalB, ".
      "SUM(beginPortefeuilleWaardeEuro)/ ".$this->pdf->ValutaKoersStart."  AS totaalA ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' AND "
      ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaal = $DB->nextRecord();
    $ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA']; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.


    $totaalOpbrengst += $ongerealiseerdeKoersResultaat;
    $gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta,true);
    $totaalOpbrengst += $gerealiseerdeKoersResultaat;
    
    // ophalen van rente totaal A en rentetotaal B
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' AND ".
      " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalA = $DB->nextRecord();
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
      " portefeuille = '".$this->portefeuille."' AND ".
      " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalB = $DB->nextRecord();
    
    $opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $this->pdf->ValutaKoersEind;
    $totaalOpbrengst += $opgelopenRente;
    

      $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving".
        " FROM Grootboekrekeningen ".
        " WHERE Grootboekrekeningen.Opbrengst = '1'  ".
        " ORDER BY Grootboekrekeningen.Afdrukvolgorde";

    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    while($gb = $DB->nextRecord())
    {
      $query = "SELECT  ".
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
        "WHERE ".
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
        "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";
      
      $DB2 = new DB();
      $DB2->SQL($query);
      $DB2->Query();

      while($opbrengst = $DB2->nextRecord())
      {
        $opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
        $totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
      }
    }
    

      $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
        "WHERE ".
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
        "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
        "Grootboekrekeningen.Kosten = '1' ".
        "GROUP BY Rekeningmutaties.Grootboekrekening ".
        "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    
    $kostenPerGrootboek = array();
    
    while($kosten = $DB->nextRecord())
    {
      if($kosten['Grootboekrekening'] == "KNBA")
      {
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      }
      else if($kosten['Grootboekrekening'] == "KOBU")
      {
        //$kostenPerGrootboek['KOST']['Omschrijving'] = "Bankkosten en provisie";
        $kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
        $kostenPerGrootboek['KOST']['omschrijving'] = "Transactiekosten";
      }
      else
      {
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      }
      
      $totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
    }
    
    $kostenProcent = ($totaalKosten / $waardeEind) * 100;
    
    // het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
    $koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
    $totaalOpbrengst += $koersResulaatValutas;
    // ***************************** einde ophalen data voor afdruk ************************ //
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->ln();
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',16);
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->row(array(vertaalTekst("Overzicht beleggingen",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetTextColor($this->pdf->rapport_bold_fontcolor[0],$this->pdf->rapport_bold_fontcolor[1],$this->pdf->rapport_fonds_fontcolor[2]);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'bu',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->row(array(vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,2,true),""));
    $this->pdf->row(array(vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
    $this->pdf->row(array(vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
    $this->pdf->CellBorders=array('','U');
    $this->pdf->row(array(vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
    unset($this->pdf->CellBorders);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'bu',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_bold_fontcolor[0],$this->pdf->rapport_bold_fontcolor[1],$this->pdf->rapport_fonds_fontcolor[2]);
    $this->pdf->row(array(vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    
    $this->pdf->row(array(vertaalTekst("Koersresultaat",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat+$gerealiseerdeKoersResultaat+$koersResulaatValutas,2),""));
    if(round($opgelopenRente,2) != 0.00)
      $this->pdf->row(array(vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
 
    foreach($opbrengstenPerGrootboek as $key=>$value)
    {
      if(round($value,2) != 0.00)
        $this->pdf->row(array(vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,2),""));
    }
    $this->pdf->CellBorders=array('','T');
    $this->pdf->row(array("",$this->formatGetal($totaalOpbrengst,2)));
    unset($this->pdf->CellBorders);
    $this->pdf->ln();
    
    $brutoResultaat=$resultaatVerslagperiode+$totaalKosten;
    $gemiddeldeVermogen=$resultaatVerslagperiode/($rendementProcent/100);
    $brutoRendement=$brutoResultaat/$gemiddeldeVermogen*100;
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Bruto resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($brutoResultaat,2),""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Bruto rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($brutoRendement,2),"%"));
    $this->pdf->ln();
    
    $this->pdf->SetFont($this->pdf->rapport_font,'bu',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_bold_fontcolor[0],$this->pdf->rapport_bold_fontcolor[1],$this->pdf->rapport_fonds_fontcolor[2]);
    $this->pdf->row(array(vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    foreach($kostenPerGrootboek as $key=>$value)
    {
      if(round($kostenPerGrootboek[$key]['Bedrag'],2) != 0.00)
        $this->pdf->row(array(vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'],2),""));
    }
    $this->pdf->CellBorders=array('','T');
    $this->pdf->row(array("",$this->formatGetal($totaalKosten,2)));
    unset($this->pdf->CellBorders);
    //$this->pdf->row(array("",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Netto resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Netto rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
    $this->pdf->ln();
    
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    //$this->pdf->ln();
    //$this->pdf->SetWidths($this->pdf->widthB);
    //$this->pdf->SetAligns($this->pdf->alignB);
    //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    //$this->pdf->row(array(vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
    
    
    
    
  }
}
?>
