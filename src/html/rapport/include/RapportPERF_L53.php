<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2014/11/17 05:17:44 $
File Versie					: $Revision: 1.6 $

$Log: RapportPERF_L53.php,v $
Revision 1.6  2014/11/17 05:17:44  rvv
*** empty log message ***

Revision 1.5  2014/11/17 05:11:44  rvv
*** empty log message ***

Revision 1.4  2014/11/15 18:30:24  rvv
*** empty log message ***

Revision 1.3  2014/04/30 16:03:17  rvv
*** empty log message ***

Revision 1.2  2014/04/23 16:18:44  rvv
*** empty log message ***

Revision 1.1  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.48  2013/10/26 15:42:06  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L53
{

	function RapportPERF_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht resultaat (in ".$this->pdf->rapportageValuta.")";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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
    $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;

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
	//echo " $query ".$ongerealiseerdeKoersResultaat." = ".$totaal[totaalB]." - ".$totaal[totaalA].";<br>\n";
//rvv 	Extra query die het mogelijk maakt om een startdatum na 1-1-jaar te kiezen. Het resultaat binnen het lopende jaar tot de start
//		datum wordt van het totaal afgehaald.
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersStart." ) AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 . " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
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
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";

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

		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
			  if($this->pdf->rapport_layout == 17 OR $this->pdf->rapport_layout == 10)
			   $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
			  else
				  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else if($kosten['Grootboekrekening'] == "KOBU" && $this->pdf->rapport_layout != 14 && $this->pdf->rapport_layout != 10)
			{
				//$kostenPerGrootboek['KOST'][Omschrijving] = "Bankkosten en provisie";
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

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

		$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();

		if($this->pdf->rapport_PERF_displayType == 1 || $this->pdf->lastPOST['perfBm'])
		{
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
			if($this->pdf->rapport_PERF_jaarRendement)
			  $this->pdf->row(array("",vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));

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
		}
		else
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($waardeBegin,2,true),""));
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));

			// subtotaal
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),$this->formatGetal($waardeMutatie,2),""));
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);
			$this->pdf->ln();



			$this->pdf->row(array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
			if($this->pdf->rapport_PERF_jaarRendement)
			  $this->pdf->row(array("",vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));

			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY()+1 ,$posSubtotaalEnd ,$this->pdf->GetY()+1);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$ypos = $this->pdf->GetY();
		}

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
			if(round($koersResulaatValutas,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
      if($this->pdf->rapport_layout == 5)
			  $this->pdf->row(array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
			else
			  $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
		}

		if($this->pdf->rapport_layout == 5 )
		{
     // $opbrengstenLayout5['Rente obligaties']     =  $opbrengstenPerGrootboek['Rente obligaties'];
     // $opbrengstenLayout5['Meegekochte rente']    =  $opbrengstenPerGrootboek['Meegekochte rente'];
    //  $opbrengstenLayout5['Dividend']             =  $opbrengstenPerGrootboek['Dividend'];
    //  $opbrengstenLayout5['Dividendbelasting']    =  $opbrengstenPerGrootboek['Dividendbelasting'];
    //  $opbrengstenLayout5['Creditrente']          =  $opbrengstenPerGrootboek['Creditrente'];
      $opbrengstenPerGrootboek['Fractieverrekeningen'] =  $opbrengstenPerGrootboek['Stockdividend'];
      unset($opbrengstenPerGrootboek['Stockdividend']);
		 // $opbrengstenPerGrootboek = $opbrengstenLayout5;
		}

		while (list($key, $value) = each($opbrengstenPerGrootboek))
		{
		  if(round($value,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,2),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,2)));
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		while (list($key, $value) = each($kostenPerGrootboek))
		{
		  if(round($kostenPerGrootboek[$key]['Bedrag'],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'],2),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalKosten,2)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];

		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());

		if($this->pdf->rapport_layout == 17)
		{


    $this->pdf->SetWidths(array(130,50,60,5,30,120));
	//	  $this->pdf->row(array("","","","Resultaat over verslagperiode",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
		  $this->pdf->row(array("","","Resultaat over verslagperiode","",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
		  $this->pdf->SetWidths($this->pdf->widthA);
		}
		else
		  $this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));

		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_PERF_rendement == 1)
		{

		  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul( "$RapStartJaar-01-01"))
		    $startDatum =  $this->pdf->PortefeuilleStartdatum;
		  else
		    $startDatum = "$RapStartJaar-01-01";

	    if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$startDatum,true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$startDatum);
	    }

	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);

	    $performanceJaar = performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		  $performancePeriode = performanceMeting($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
//		  $performanceBegin  = performanceMeting($this->portefeuille,$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,1,$this->pdf->rapportageValuta);

		  $this->pdf->SetY($this->pdf->GetY()+30);
		  $extraMarge = 140;
		  $this->pdf->SetX($this->pdf->marge );
		  $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
      $min = 6;
		  $this->pdf->Rect($this->pdf->marge + $extraMarge,$this->pdf->getY(),110,(20-$min),'F');
		  $this->pdf->SetFillColor(0);
		  $this->pdf->Rect($this->pdf->marge + $extraMarge,$this->pdf->getY(),110,(20-$min));
		  $this->pdf->ln(2);

      $this->pdf->SetX($this->pdf->marge  +$extraMarge +10);
			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
			$this->pdf->Cell(60,4, vertaalTekst("Resultaat over verslagperiode",$this->rapport_taal), 0,0, "L");
			$this->pdf->Cell(30,4, $this->formatGetal($performancePeriode,2)."%", 0,1, "R");
			$this->pdf->ln(2);

			$this->pdf->SetX($this->pdf->marge  +$extraMarge +10);
	    $this->pdf->Cell(60,4, vertaalTekst("Resultaat lopende kalenderjaar",$this->rapport_taal), 0,0, "L");
		  $this->pdf->Cell(30,4, $this->formatGetal($performanceJaar,2)."%", 0,1, "R");
			$this->pdf->ln(2);

//			$this->pdf->SetX($this->pdf->marge  +$extraMarge +10);
//	    $this->pdf->Cell(60,4, vertaalTekst("Resultaat vanaf begin beheer / ".jul2form(db2jul($this->pdf->PortefeuilleStartdatum)),$this->rapport_taal), 0,0, "L");
//		  $this->pdf->Cell(30,4, $this->formatGetal($performanceBegin,2)."%", 0,1, "R");
//		  $this->pdf->ln(2);
		}



$query ="SELECT Portefeuilles.SpecifiekeIndex , Fondsen.Omschrijving
 FROM Portefeuilles JOIN Fondsen on Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
  WHERE Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
$DB->SQL($query);
$data = $DB->lookupRecord();
$specifiekeIndex = $data['SpecifiekeIndex'];
$specifiekeIndexOmschrijving = $data['Omschrijving'];

 	  if($this->pdf->rapport_PERF_portefeuilleIndex == 1)
		{
		  $grafiekData = array();
		  $query = "SELECT indexWaarde, Datum ,
		      (SELECT Koers  FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND MONTH(Datum) = MONTH(HistorischePortefeuilleIndex.Datum) ORDER BY Datum DESC limit 1) as specifiekeIndexWaarde

		           FROM HistorischePortefeuilleIndex WHERE periode='m' AND portefeuille = '".$this->portefeuille."' AND Datum < '".$this->rapportageDatum."'";
		  $DB->SQL($query);
		  $DB->Query();
			  $n=0;
		  while ($data = $DB->nextRecord())
		  {
        $grafiekData['Datum'][] = $data['Datum'];
        $grafiekData['Index'][] = ($data['indexWaarde']);
        $specifiekeIndexWaarde[$n]=$data['specifiekeIndexWaarde'];
		    if($n==0)
		    {

		      $db2=new DB();
		      $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum > '".$grafiekData['Datum'][0]."' LIMIT 1";
		      $db2->sql($query);
		      $db2->Query();
		      $indexStart = $db2->lookupRecord();
         $grafiekData['Index1'][$n] = ($data['specifiekeIndexWaarde']/$indexStart['Koers']*100);
		    }
		    else
		    {
 		      $grafiekData['Index1'][$n] = ($data['specifiekeIndexWaarde']/$indexStart['Koers']*100);
 		    }
		    $n++;
		  }
//		  listarray($indexStart);
//listarray($specifiekeIndexWaarde);
//listarray($grafiekData);
		  if (count($grafiekData) > 1)
		  {
		  $color= array(30,23,96);
		  $color1 = array(167,26,32);
	    $this->pdf->SetXY(10,108)		;
		  $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		  $this->pdf->Cell(0, 5, 'Vermogensontwikkeling', 0, 1);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->SetX(15,$this->pdf->GetY()+2);
      $valX = $this->pdf->GetX();
      $valY = $this->pdf->GetY();
      $this->pdf->LineDiagram(108, 60, $grafiekData,array($color,$color1),0,0,4,4);

      $this->pdf->Rect($valX, $valY+70, 3, 3 ,'F','',$color);
      $this->pdf->SetXY($valX+4, $valY + 70);
      $this->pdf->Cell(0, 4, 'Portefeuille', 0, 0);

      $this->pdf->Rect($valX+30, $valY+70, 3, 3 ,'F','',$color1);
      $this->pdf->SetXY($valX+4+30, $valY + 70);
      $this->pdf->Cell(0, 4, $specifiekeIndexOmschrijving, 0, 0);

      $this->pdf->SetXY($valX, $valY + 80);
      }
		}

		if($this->pdf->rapport_PERF_liquiditeiten == 1)
		{
		  $this->pdf->ln();

		  $this->pdf->SetWidths($this->pdf->widthB);
		  $this->pdf->SetAligns($this->pdf->alignB);
		  $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		  $this->pdf->row(array("",vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"",""));
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 		  $this->pdf->SetWidths($this->pdf->widthA);
	 	  $this->pdf->SetAligns($this->pdf->alignA);

		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
						   "FROM TijdelijkeRapportage WHERE ".
						   " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						   " portefeuille = '".$this->portefeuille."' AND
						     type <> 'fondsen' AND type = 'rekening' "
						   .$__appvar['TijdelijkeRapportageMaakUniek'];
		           debugSpecial($query,__FILE__,__LINE__);

		  $DB->SQL($query);
		  $DB->Query();
		  $totaalWaardeLiquiditeitenVanaf = $DB->nextRecord();
		 	$this->pdf->row(array("",vertaalTekst("Saldo liquiditeiten per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($totaalWaardeLiquiditeitenVanaf['totaal'],2),""));


		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
			    		 "FROM TijdelijkeRapportage WHERE ".
					  	 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						   " portefeuille = '".$this->portefeuille."' AND
						     type <> 'fondsen' AND type = 'rekening' "
						   .$__appvar['TijdelijkeRapportageMaakUniek'];
		  debugSpecial($query,__FILE__,__LINE__);
      $DB->SQL($query);
     	$DB->Query();
     	$totaalWaardeLiquiditeiten = $DB->nextRecord();
		  $this->pdf->row(array("",vertaalTekst("Saldo liquiditeiten per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($totaalWaardeLiquiditeiten['totaal'],2),""));

		  if($this->pdf->lastPOST['perfBm'])
		  {
		    $this->pdf->ln();
		    $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		  }
		}
    
 //   $gebruikteCategorie=$this->addZorgBar();
 //   $this->plotZorgBar2(4,50,$gebruikteCategorie);

   $this->toonBenchmark();
	}
 
  function toonBenchmark()
  {
    $db=new DB();
    $nieuweKop=true;
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query="SELECT
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds,
IndexPerBeleggingscategorie.Vermogensbeheerder,
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Omschrijving as hCatOmschrijving,
Fondsen.Omschrijving as fondsOmschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
WHERE IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."'
ORDER BY Beleggingscategorien.Afdrukvolgorde";
    $db->SQL($query);
    $db->Query();
    
    if($db->records()==0)
    {
    $query="SELECT
    Fondsen.Fonds,
Portefeuilles.SpecifiekeIndex,
Portefeuilles.Portefeuille,
Fondsen.Omschrijving as fondsOmschrijving
FROM
Portefeuilles
INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    }
    $db->SQL($query);
    $db->Query();
    
    while($data=$db->nextRecord())
    {
      $hoofdcategorien[]=$data;
    }
    $extraX=160;
    $widths=array($extraX,70,25,25);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetAligns(array('L','L','R','R','R'));
    $this->pdf->SetY(40);
    
    
    foreach($hoofdcategorien as $categorie)
    {
      if($nieuweKop==true)
      {
       	$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
        $this->pdf->Rect($this->pdf->marge+$extraX,$this->pdf->GetY(),array_sum($widths)-$extraX, 4, 'F');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('','Benchmark','','%Ytd'));
        $this->pdf->Line($this->pdf->marge+$extraX,$this->pdf->GetY(),array_sum($widths)+$this->pdf->marge,$this->pdf->GetY());
        $nieuweKop=false;
      }
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $perf=$this->fondsPerformance($categorie['Fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
      $this->pdf->row(array('',$categorie['fondsOmschrijving'],'',$this->formatGetal($perf['periode'],2)));
                           
      $query="SELECT
benchmarkverdeling.fonds,
benchmarkverdeling.percentage,
benchmarkverdeling.benchmark,
Fondsen.Omschrijving as fondsOmschrijving,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as BeleggingscategorieOmschrijving
FROM
benchmarkverdeling
INNER JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
LEFT JOIN BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='$beheerder'
LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie=Beleggingscategorien.Beleggingscategorie
WHERE 
benchmarkverdeling.benchmark='".$categorie['Fonds']."'
ORDER BY benchmarkverdeling.fonds ";
     $db->SQL($query);
     $db->Query();
     if($db->records()>0)
     {
       $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
       $this->pdf->row(array('',$categorie['fondsOmschrijving'].' bestaande uit'));

         $this->pdf->row(array('','        Index/Fonds','Weging','%Ytd'));
         $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       while($data=$db->nextRecord())
       {
          $perf=$this->fondsPerformance($data['fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
          $this->pdf->row(array('','        '.$data['fondsOmschrijving'],$this->formatGetal($data['percentage'],2).'%',$this->formatGetal($perf['periode'],2)));

       }
       $nieuweKop=true;
     }
   }
 }
  
  
  	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

function fondsPerformance($fonds,$vanaf,$tot,$startdatumCheck=false)
{
  $januari=substr($tot,0,4)."-01-01";
  if($startdatumCheck==true && db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($januari))
    $januari=substr($this->pdf->PortefeuilleStartdatum,0,10);
    
  $totalPerf=0;
  $indexData=array('fondsKoers_eind'=>$this->getFondsKoers($fonds,$tot),
                    'fondsKoers_begin'=>$this->getFondsKoers($fonds,$vanaf),
                    'fondsKoers_jan'=>$this->getFondsKoers($fonds,$januari));
                    
   $jaarPerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );   
   $periodePerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_begin']) / ($indexData['fondsKoers_begin']/100 );                

  return array('periode'=>$periodePerf,'jaar'=>$jaarPerf);
}
}
?>