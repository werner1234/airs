<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportPERF_L18.php,v $
 		Revision 1.5  2011/09/14 09:26:56  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/07/01 07:12:34  rvv
 		*** empty log message ***

 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***

 		Revision 1.2  2008/03/18 09:58:08  rvv
 		*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L18
{
	function RapportPERF_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Rendementsverdeling";
		$this->portefeuille = $portefeuille;
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

	  $DB = new DB();
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
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

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

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersStart." ) AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 . " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];

$RapJaar = date("Y", db2jul($this->rapportageDatum));
$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

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

		$opgelopenRente = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
		$totaalOpbrengst += $opgelopenRente;

		if($this->pdf->GrootboekPerVermogensbeheerder)
		{
		  $query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
		}
		else
		{
		$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving".
		" FROM Grootboekrekeningen ".
		" WHERE Grootboekrekeningen.Opbrengst = '1'  ".
		" ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		}

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
			  if($gb['Grootboekrekening'] == "DIV" || $gb['Grootboekrekening'] == "RENTE" || $gb['Grootboekrekening'] == "RENOB")
			  {
			    $opbrengstenPerGrootboek['Directe Opbrengsten'] += ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				  $totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			  }
			  else
			  {
				  $opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				  $totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			  }
			}
		}

		// loopje over Grootboekrekeningen Kosten = 1
		if($this->pdf->GrootboekPerVermogensbeheerder)
		{
		  $query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder ".
	   	"WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
		  "GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ".
		  "Rekeningmutaties.Grootboekrekening = GrootboekPerVermogensbeheerder.GrootboekRekening AND ".
		  "GrootboekPerVermogensbeheerder.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde ";
		}
		else
		{
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
		}

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();

		/*
  BEH   Beheervergoeding
  DIV   Dividend
  DIVBE   Dividendbelasting
  FONDS   Fonds
  KNBA   Bankkosten
  KOBU   Kosten buitenland
  KOST   Transactiekosten
  Kruis   Kruispost
  ONTTR   Onttrekking
  RENME   Meegekochte rente
  RENOB   Rente obligaties
  RENTE   Creditrente
  STORT   Storting
  VERM   Vermogen
  VKSTO   Stockdividend
*/

		while($kosten = $DB->nextRecord())
		{
      if($kosten[Grootboekrekening] == "KNBA" || $kosten[Grootboekrekening] == "KOST" || $kosten[Grootboekrekening] == "KOBU")
			{
			  $kostenPerGrootboek['KNBA'][Omschrijving] = "Bank Kosten";
				$kostenPerGrootboek['KNBA'][Bedrag] += -1*($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += -1*($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}

		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten) ;
		$totaalOpbrengst += $koersResulaatValutas;
		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


		$this->pdf->AddPage();
				$this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;
		$this->pdf->SetTextColor($this->rapport_fontcolor[r],$this->rapport_fontcolor[g],$this->rapport_fontcolor[b]);
		$add=24/2;
		$this->pdf->widthA = array(3,200+$add,55+$add);
		$this->pdf->alignA = array('L','L','R');
		$this->pdf->CellBorders = array('','U','U','U','U');
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

//		$this->pdf->ln();


	$this->pdf->switchFont('fonds');
  $this->pdf->switchFont('rodelijn');
	$this->pdf->row(array("",vertaalTekst("Totale waarde van vermogen op ",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)).' EUR '.$this->formatGetal($waardeEind,0),''));

	$this->pdf->switchFont('fonds');

	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	$this->pdf->row(array("",vertaalTekst("Waardering in EUR",$this->pdf->rapport_taal),"",""));
	$this->pdf->switchFont('fonds');

			$this->pdf->row(array("",vertaalTekst("Ongerealiseerd Resultaat",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,0),""));
			$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,0),""));
			if(round($gerealiseerdeKoersResultaat,2) != 0.00)
			$this->pdf->row(array("",vertaalTekst("Transactieresultaat",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,0),""));
			if(round($koersResulaatValutas,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,0),""));

		while (list($key, $value) = each($opbrengstenPerGrootboek))
		{
		  if(round($value,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,0),""));
		}

		while (list($key, $value) = each($kostenPerGrootboek))
		{
		  if(round($kostenPerGrootboek[$key]['Bedrag'],0) != 0)
			  $this->pdf->row(array("",vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'],0),""));
		}


  $this->pdf->switchFont('rodelijn');
	  $this->pdf->row(array("","Resultaat",$this->formatGetal($totaalOpbrengst - $totaalKosten,0)));


	  		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->templateVars['PERFPaginas']=$this->pdf->customPageNo+$this->pdf->extraPage;


	}
}
?>