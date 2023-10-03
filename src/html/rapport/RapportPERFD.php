<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.7 $

$Log: RapportPERFD.php,v $
Revision 1.7  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.6  2009/03/14 13:24:27  rvv
*** empty log message ***

Revision 1.5  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.4  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.3  2007/12/14 14:12:19  rvv
*** empty log message ***

Revision 1.2  2007/11/16 11:22:27  rvv
*** empty log message ***

Revision 1.1  2007/10/04 12:01:30  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/portefeuilleVerdieptClass.php");

class RapportPERFD
{
	function RapportPERFD($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		
		      
		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";
			

		  
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = array();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);		
		$this->kleuren= array();
		$this->kleuren['hoofdsectoren'] = $allekleuren['OIS'];
		$this->kleuren['regio'] = $allekleuren['OIR'];
		//$this->pdf->rapport_PERF_displayType
		    

		//$this->pdf->rapport_PERF_displayType

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		
		$this->verdiept = new portefeuilleVerdiept($this->pdf,$this->portefeuille,$this->rapportageDatum);

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

  
	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		
		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else 
	    $koersQuery = "";

		if($this->pdf->rapport_layout == 1)
		{
			$kopStyle = "";
		}
		else
		{
			$kopStyle = "u";
		}

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,10,30,120);
		$this->pdf->alignA = array('L','L','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(1,95,30,10,30,120);
		$this->pdf->alignB = array('L','L','R','R','R');

		$this->pdf->AddPage();

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

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

		$waardeEind				= $totaalWaarde[totaal];
		$waardeBegin 			 	= $totaalWaardeVanaf[totaal];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

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
		$ongerealiseerdeKoersResultaat = $totaal[totaalB] - $totaal[totaalA]; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.
		
		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;

		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
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

		$opgelopenRente = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
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
			// loopje over Grootboekrekeningen Opbrengsten = 1
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
			"Rekeningmutaties.Grootboekrekening = '".$gb[Grootboekrekening]."' ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			if($this->pdf->rapport_layout == 7)
			{
				switch($gb['Omschrijving'])
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

			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}

		// loopje over Grootboekrekeningen Kosten = 1
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
			if($kosten[Grootboekrekening] == "KNBA")
			{
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else if($kosten[Grootboekrekening] == "KOBU")
			{
				//$kostenPerGrootboek['KOST'][Omschrijving] = "Bankkosten en provisie";
				$kostenPerGrootboek['KOST'][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
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
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
		
		$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();



			$ypos = $this->pdf->GetY();

			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($waardeBegin,2,true),""));
			$this->pdf->ln(2);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());

			$this->pdf->ln();
			$this->pdf->ln();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

			$this->pdf->row(array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));

			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY(),$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->widthA = array(130,80,30,5,30,120);
			$this->pdf->alignA = array('L','L','R','R','R');

			$this->pdf->widthB = array(125,95,30,5,30,120);
			$this->pdf->alignB = array('L','L','R','R','R');

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
			$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		if($this->pdf->rapport_layout == 7)
		{
			$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koers- en valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Gerealiseerde koers- en valutaresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Resultaat op V.V.-rekeningen",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
			$this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente obligaties",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
		}
		else
		{
			$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
      if($this->pdf->rapport_layout == 5)
			  $this->pdf->row(array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
			else
			  $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
		}

		if($this->pdf->rapport_layout == 5 )
		{
      $opbrengstenLayout5['Rente obligaties']     =  $opbrengstenPerGrootboek['Rente obligaties'];
      $opbrengstenLayout5['Meegekochte rente']    =  $opbrengstenPerGrootboek['Meegekochte rente'];
      $opbrengstenLayout5['Dividend']             =  $opbrengstenPerGrootboek['Dividend'];
      $opbrengstenLayout5['Dividendbelasting']    =  $opbrengstenPerGrootboek['Dividendbelasting'];
      $opbrengstenLayout5['Creditrente']          =  $opbrengstenPerGrootboek['Creditrente'];
      $opbrengstenLayout5['Fractieverrekeningen'] =  $opbrengstenPerGrootboek['Stockdividend'];
		  $opbrengstenPerGrootboek = $opbrengstenLayout5;
		}
		
		while (list($key, $value) = each($opbrengstenPerGrootboek))
		{
		  if(round($value,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,2),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,2)));


		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		while (list($key, $value) = each($kostenPerGrootboek))
		{
		  if(round($value,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($kostenPerGrootboek[$key][Omschrijving],$this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key][Bedrag],2),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalKosten,2)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];

		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		
		
    $toonGrafiek = true;
		$typen = array('sector','regio');
		
    if($toonGrafiek == true)		
    {

      $verdieptePortefeuilles = $this->verdiept->getVerdieptePortefeuilles();
		  $restwaarde = $totaalWaarde['totaal'];
		  foreach ($verdieptePortefeuilles as $data)
		  {
		    if(!is_array($pdf->fondsPortefeuille[$data['fonds']]))
		    {
		      $this->pdf->fondsPortefeuille[$data['fonds']]['fondsNaam']=$data['fonds'];
		      $this->verdiept->bepaalVerdeling($data['fonds'],$data['portefeuille'],$typen,$this->rapportageDatum);
		    }  
		    $aandeelInPortefeuille = $data['aandeelWaarde'] ;
        $verdelingHuisfonds[$data['fonds']]['aandeelInPortefeuille']=$aandeelInPortefeuille;
        $restwaarde -= $aandeelInPortefeuille;
		  }
		  
	$this->verdiept->bepaalVerdeling($this->portefeuille,$this->portefeuille,$typen,$this->rapportageDatum);	 
	$this->pdf->fondsPortefeuille[$this->portefeuille]['fondsNaam']='basisPortefeuille'; 
	$verdelingHuisfonds[$this->portefeuille]['aandeelInPortefeuille']=$restwaarde;
	
	

		  //Bepalen gecombineerde percentages
		  foreach ($verdelingHuisfonds as $fonds=>$aandeel)
		  {
		    $aandeelTotaal=$aandeel['aandeelInPortefeuille'];
		    foreach ($this->pdf->fondsPortefeuille[$fonds]['verdeling'] as $type=>$waarden)
		    {
		      $totaalCategorieAandeel = 0;
		      $totaalCategorieWaarde = 0;
		    
		      foreach ($waarden as $categorie=>$verdeling)
		      {
	          $verdelingPercentages[$type][$categorie][$fonds]=array('percentage'=>$verdeling['percentage'],
	                                                                 'waardeTotaalFonds'=>$aandeelTotaal,
	                                                                 'waardeAandeel'=>$aandeelTotaal*$verdeling['percentage']);
	          $totaalCategorieAandeel +=  $aandeelTotaal*$verdeling['percentage']   ;
	          $totaalCategorieWaarde += $aandeelTotaal;
		      }
		    }
		  }
//listarray($verdelingPercentages);
//listarray($this->pdf->fondsPortefeuille);
		  
		  foreach ($verdelingPercentages as $type=>$categorien)
		  {
		    foreach ($categorien as $categorie=>$waarden)
		    {
		       foreach ($waarden as $portefeuille=>$aandeel)
		       {
			      $verdelingKort[$type][$categorie] += $aandeel['waardeAandeel'];
			      $TotaalWaarde[$type] += $aandeel['waardeAandeel'];
		       }
		    }
		    arsort($verdelingKort[$type]); 
		  }
//listarray($verdelingKort);



$standaardKleuren=array(array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
						array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
						,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155),
						array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
						array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
						,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155));	

$typen = array('regio','hoofdsectoren');

foreach ($typen as $grafiek)
{
$n=0;
  foreach ($verdelingKort[$grafiek] as $key=>$value)
  {
$n++; //listarray($this->pdf->sector); echo $key.'  '.$value.' <br>';
    if($this->pdf->sector[$key])
      $omschrijving = $this->pdf->sector[$key];
    else 
      $omschrijving = "Geen $grafiek" ;
     	$grafiekData[$grafiek]['omschrijving'][] =  $omschrijving. " (" . round(( $value / $TotaalWaarde[$grafiek]) * 100 ,1) ." %)" ;
  
     	$kleurdata = $this->kleuren[$grafiek][$key];

     	if($kleurdata['R']['value'] > 0 || $kleurdata['G']['value'] > 0 || $kleurdata['B']['value'] > 0 )
     	{
      	$grafiekData[$grafiek]['kleur'][] = array($kleurdata['R']['value'],$kleurdata['G']['value'],$kleurdata['B']['value']);
     	}
     	else 
       $grafiekData[$grafiek]['kleur'][]=$standaardKleuren[$n] ;
     	
     //$this->kleuren[$grafiek][$key];
      
        			if ($value < 0)
  				$value = $value * -1;
      $grafiekData[$grafiek]['percentage'][]=$value/$TotaalWaarde[$grafiek]; 
  }
}



    // listarray($grafiekData); 


      
    $diameter = 35;
    $hoek = 30;
    $dikte = 10;
    $Xas= 80;
    $yas= 140;	

    $this->pdf->set3dLabels($grafiekData['hoofdsectoren']['omschrijving'],$Xas,$yas,$grafiekData['hoofdsectoren']['kleur']);
    $this->pdf->Pie3D($grafiekData['hoofdsectoren']['percentage'],$grafiekData['hoofdsectoren']['kleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Hoofd Sector");

    $this->pdf->set3dLabels($grafiekData['regio']['omschrijving'],$Xas+135,$yas,$grafiekData['regio']['kleur']);
    $this->pdf->Pie3D($grafiekData['regio']['percentage'],$grafiekData['regio']['kleur'],$Xas+135,$yas,$diameter,$hoek,$dikte,"Regio"); 
    }
	}
	
	

	
}
?>