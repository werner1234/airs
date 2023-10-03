<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERFD_L125
{

	function RapportPERFD_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

  	$this->pdf->rapport_titel = "Samengevat";

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
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";


		$DB = new DB();

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
    $this->totaleWaarde=$totaalWaarde['totaal'];

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

		$waardeEind				= $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    //echo "$rendementProcent  	= performanceMeting(".$this->portefeuille.", ".$this->rapportageDatumVanaf.", ".$this->rapportageDatum.", ".$this->pdf->portefeuilledata['PerformanceBerekening'].",".$this->pdf->rapportageValuta." ";exit;
		if($this->pdf->rapport_PERF_jaarRendement)
		{
		  $RapStartJaar = date("Y", db2jul($this->rapportageDatum));
		  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul( "$RapStartJaar-01-01"))
		  {
		    $startDatum =  $this->pdf->PortefeuilleStartdatum;
		    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);
		  }
		  else
		    $startDatum = "$RapStartJaar-01-01";

		  if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$startDatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$startDatum);
	    }
	    $rendementProcentJaar = performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		}

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



//rvv end
		$totaalOpbrengst = $ongerealiseerdeKoersResultaat;

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


			$query = "SELECT  ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, Grootboekrekeningen.Omschrijving, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM (Rekeningmutaties, Rekeningen, Portefeuilles) JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening AND  Grootboekrekeningen.Opbrengst = '1'  ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
		  	GROUP BY Rekeningmutaties.Grootboekrekening
		  	 ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
      $opbrengstenPerGrootboek=array();
			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$opbrengst['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
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
    $totaalKosten=0;
		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else if($kosten['Grootboekrekening'] == "KOBU")
			{
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
        $kostenPerGrootboek['KOST']['Omschrijving'] = "Transactiekosten";
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}

			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}
		// het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
    
    
    // voor data
    $this->pdf->widthA = array(20-$this->pdf->marge,80,30,30,60,30);
    $this->pdf->alignA = array('L','L','R','C','L','R');
    
    $this->pdf->AddPage();

    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    subHeader_L125($this->pdf,28,array(140,140),array('Resultaat over het lopende jaar','Huidige vermogensverdeling'));
    $this->pdf->setWidths($this->pdf->widthA);
    $this->pdf->setAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($this->pdf->marge,43);//$this->pdf->rapportBeginY);

    $this->pdf->row(array("",vertaalTekst("Waarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($waardeBegin,0,'€'),""));
    $this->pdf->ln();
    $this->pdf->row(array("",vertaalTekst("Stortingen",$this->pdf->rapport_taal),$this->formatGetal($stortingen,0,'€'),""));
    $this->pdf->ln();
    $this->pdf->row(array("",vertaalTekst("Onttrekkingen",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,0,'€'),""));
    $this->pdf->ln();
    $this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,0,'€'),""));
    
    $this->pdf->ln(8);
    $this->pdf->line(20,$this->pdf->getY(),20+$this->pdf->widthA[1]+$this->pdf->widthA[2],$this->pdf->getY(),array('color'=>$this->pdf->textGrijs));
    $this->pdf->ln(8);
    
    $this->pdf->SetTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->row(array("",vertaalTekst("Waarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),"Rendement"));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+6);

    $this->pdf->row(array("",$this->formatGetal($waardeEind,0,'€'),$this->formatGetal($rendementProcent,2,'%')));
    $this->pdf->ln();
    
    subHeader_L125($this->pdf,110,array(140,140),array('Resultaten','Kosten'));
    $this->pdf->setWidths($this->pdf->widthA);
    $this->pdf->setAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(12+3);
    $kostenY=$this->pdf->getY();
    $this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,0,'€')));
    $this->pdf->ln();
    $this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,0,'€')));
    $this->pdf->ln();
    if($opgelopenRente<>0)
    {
      $this->pdf->row(array("", vertaalTekst("Resultaat opgelopen rente", $this->pdf->rapport_taal),$this->formatGetal($opgelopenRente, 0,'€')));
      $this->pdf->ln();
    }
    foreach($opbrengstenPerGrootboek as $key=>$value)
    {
      if(round($value,2) != 0.00)
      {
        $this->pdf->row(array("", vertaalTekst($key, $this->pdf->rapport_taal), $this->formatGetal($value, 0,'€')));
        $this->pdf->ln();
      }
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
    $this->pdf->row(array("","",$this->formatGetal($totaalOpbrengst,0,'€')));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $maxY=$this->pdf->getY();

    $this->pdf->setY($kostenY);
    
    foreach($kostenPerGrootboek as $key=>$value)
    {
      if(round($kostenPerGrootboek[$key]['Bedrag'],2) != 0.00)
      {
        $this->pdf->row(array("", "", "", "", vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'], $this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'], 0,'€')));
        $this->pdf->ln();
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
    $this->pdf->row(array("","","","","",$this->formatGetal($totaalKosten,0,'€')));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $maxY=max($maxY,$this->pdf->getY());
    $this->pdf->setY($maxY);
    $this->pdf->ln();
    $this->pdf->line(20,$this->pdf->getY(),140+110,$this->pdf->getY());
    $this->pdf->ln();
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->setX(160);
    $this->pdf->Cell(50,5,vertaalTekst('Beleggingsresultaat',$this->pdf->rapport_taal),0,0,'L');
    $this->pdf->setTextColor(0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(40,5,$this->formatGetal($resultaatVerslagperiode,0,'€'),0,0,'R');
    
    $this->pdf->row(array("", "", "", "", vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'], $this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'], 0,'€')));
    


  $this->rechtsBoven();


	}
	
	function rechtsBoven()
  {
    global $__appvar;
    $DB=new DB();
    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
    $this->pdf->grafiekKleuren=$kleuren;
    $this->categorieKleuren=$kleuren['OIB'];
    
    $query="SELECT Risicoklasse, klasseStd FROM Risicoklassen WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND  Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."'";
    $DB->SQL($query);
    $DB->Query();
    $risicoklasse = $DB->LookupRecord();

    
    $query = "SELECT Beleggingscategorie,BeleggingscategorieOmschrijving, SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."'"
      .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY BeleggingscategorieOmschrijving ORDER BY BeleggingscategorieVolgorde";
    debugSpecial($query,__FILE__,__LINE__);
  
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data = $DB->nextRecord())
    {
      $verdeling[$data['BeleggingscategorieOmschrijving']]+=$data['totaal']/$this->totaleWaarde*100;
      $pieKleuren[$data['BeleggingscategorieOmschrijving']]=array($this->categorieKleuren[$data['Beleggingscategorie']]['R']['value'],$this->categorieKleuren[$data['Beleggingscategorie']]['G']['value'],$this->categorieKleuren[$data['Beleggingscategorie']]['B']['value']) ;
    }
  
    $this->pdf->setY(43);//$this->pdf->rapportBeginY);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->setX(160);
    $this->pdf->Cell(30,5,vertaalTekst('Portefeuilleprofiel',$this->pdf->rapport_taal).':',0,0,'L');
    $this->pdf->setTextColor(0);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,5,$risicoklasse['Risicoklasse'],0,0,'L');
    $this->pdf->ln();
    $this->pdf->ln(2);
    $this->pdf->setX(160);
    $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
    $this->pdf->Cell(30,5,vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal).':',0,0,'L');
    $this->pdf->setTextColor(0);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,5,$this->formatGetal($risicoklasse['klasseStd'],2).'%',0,0,'L');
    
    
    $this->pdf->setXY(165,60);
    printPie_L125($this->pdf,50, 50, $verdeling, '%l (%p)',$pieKleuren);
  }
  
  
  
 
}
?>