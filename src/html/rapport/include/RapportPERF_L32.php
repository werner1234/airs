<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/26 19:33:28 $
File Versie					: $Revision: 1.6 $

$Log: RapportPERF_L32.php,v $
Revision 1.6  2019/01/26 19:33:28  rvv
*** empty log message ***

Revision 1.5  2017/10/25 15:59:31  rvv
*** empty log message ***

Revision 1.4  2017/06/10 18:09:58  rvv
*** empty log message ***

Revision 1.3  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.2  2017/05/20 18:16:29  rvv
*** empty log message ***

Revision 1.1  2015/11/04 16:54:21  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L32
{

	function RapportPERF_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
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
    $this->__appvar=$__appvar;
   
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
  
		if (is_array($this->pdf->__appvar['consolidatie']))
		{
			$this->writeRapportConsolidatie();
		}
		else
		{
			$this->writeRapportSingle();
		}
	}

	function writeRapportSingle()
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
		$this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;

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
		$ongerealiseerdeKoersResultaat = $totaal[totaalB] - $totaal[totaalA]; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.
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
			if($kosten[Grootboekrekening] == "KNBA")
			{
			  if($this->pdf->rapport_layout == 17 OR $this->pdf->rapport_layout == 10)
			   $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
			  else
				  $kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else if($kosten[Grootboekrekening] == "KOBU" && $this->pdf->rapport_layout != 14 && $this->pdf->rapport_layout != 10)
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

			$this->pdf->row(array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,2,true),""));
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
			$this->pdf->row(array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
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

			$this->pdf->row(array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,2,true),""));
			$this->pdf->row(array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
			// subtotaal
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Mutatie waarde vermogen",$this->pdf->rapport_taal),$this->formatGetal($waardeMutatie,2),""));
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
      $opbrengstenPerGrootboek['Fractieverrekeningen'] =  $opbrengstenPerGrootboek['Stockdividend'];
      unset($opbrengstenPerGrootboek['Stockdividend']);
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
        
 // if($rapportageDatum['a']==$rapportageDatum['b'] && substr($rapportageDatum['a'],5,5)=='01-01')
 //   vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,substr($rapportageDatum['a'],0,4).'-12-31');
    
	    if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
	    {
	      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$startDatum,true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$startDatum);
	    }

//	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$this->pdf->PortefeuilleStartdatum,true);
//      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$this->pdf->PortefeuilleStartdatum);

	    $performanceJaar = performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		  $performancePeriode = performanceMeting($this->portefeuille,date('Y-m-d',$this->pdf->rapport_datumvanaf),$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
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

	}
	
	
	
	
	
	
	function tweedeStart()
	{
		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
			$this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
		elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
			$this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
		else
			$this->tweedePerformanceStart = "$RapStartJaar-01-01";
	}
	
	function getAttributieCategorien($realCategorie)
	{
		$this->AttCategorien=array('Totaal');
		$categorieOmschrijving['Totaal'] = 'Totaal';
		$query="SELECT KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
AttributieCategorien.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN AttributieCategorien ON KeuzePerVermogensbeheerder.waarde = AttributieCategorien.AttributieCategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='AttributieCategorien'
ORDER BY Afdrukvolgorde";
		$this->db->SQL($query);
		$this->db->Query();
		while($categorie = $this->db->nextRecord())
		{
			$categorieOmschrijving[$categorie['waarde']]=$categorie['Omschrijving'];
			$this->AttCategorien[]=$categorie['waarde'];
		}
		
		$query = "SELECT  BeleggingssectorPerFonds.AttributieCategorie,  AttributieCategorien.Omschrijving
              FROM BeleggingssectorPerFonds  ,AttributieCategorien
              WHERE BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
              BeleggingssectorPerFonds.AttributieCategorie =  AttributieCategorien.AttributieCategorie
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie
              ORDER By AttributieCategorien.Afdrukvolgorde";
		$this->db->SQL($query);
		$this->db->Query();
		//$this->categorien[] = 'Totaal';
		
		while($categorie = $this->db->nextRecord())
		{
			$categorieOmschrijving[$categorie['AttributieCategorie']]=$categorie['Omschrijving'];
			//$this->categorien[]=$categorie['AttributieCategorie'];
		}
		if(!in_array('Liquiditeiten',$this->categorien))
		{
			$categorieOmschrijving['Liquiditeiten']='Liquiditeiten';
			// $this->categorien[]='Liquiditeiten';
		}
		
		$this->pdf->ln();
		$y=$this->pdf->GetY();
		$kopRegel = array();
		array_push($kopRegel,"");
		array_push($kopRegel,"");
		foreach ($realCategorie as $categorie)
		{
			array_push($kopRegel,vertaalTekst($categorieOmschrijving[$categorie],$this->pdf->rapport_taal));
			array_push($kopRegel,"");
		}
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row($kopRegel);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetY($y+8);
		return $realCategorie;
	}
	
	function bepaalCategorieWaarden()
	{

		foreach ($this->categorien as $categorie)
		{
			if ($categorie == 'Totaal')
				$attributieQuery = '';
			elseif ($categorie == 'Liquiditeiten')
				$attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '' AND ";
			else
				$attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '".$categorie."' AND";
			
			if ($categorie == 'Totaal' || $this->pdf->debug)
			{
				
				$gerealiseerdKoersresultaat[$categorie] = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,true,$categorie);
				
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaalB, ".
					"SUM(beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalA ".
					"FROM TijdelijkeRapportage WHERE ".
					" rapportageDatum ='".$this->rapportageDatum."' AND ".
					" portefeuille = '".$this->portefeuille."' AND ".
					$attributieQuery
					." type = 'fondsen' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query,__FILE__,__LINE__);
				$this->db->SQL($query);
				$this->db->Query();
				$totaal = $this->db->nextRecord();
				$ongerealiseerdeKoersResultaaten[$categorie] = ($totaal[totaalB] - $totaal[totaalA]) ;
				
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					"FROM TijdelijkeRapportage WHERE ".
					" rapportageDatum ='".$this->rapportageDatum."' AND ".
					" portefeuille = '".$this->portefeuille."' AND ".$attributieQuery.
					" type = 'rente' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query,__FILE__,__LINE__);
				$this->db->SQL($query);
				$this->db->Query();
				$totaalA = $this->db->nextRecord();
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					"FROM TijdelijkeRapportage WHERE ".
					" rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
					" portefeuille = '".$this->portefeuille."' AND ". $attributieQuery.
					" type = 'rente' ". $this->__appvar['TijdelijkeRapportageMaakUniek'] ;
				debugSpecial($query,__FILE__,__LINE__);
				$this->db->SQL($query);
				$this->db->Query();
				$totaalB = $this->db->nextRecord();
				$opgelopenRentes[$categorie] = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
			}
		}
		$waarden=array('gerealiseerdKoersresultaat'=>$gerealiseerdKoersresultaat,
									 'ongerealiseerdeKoersResultaaten'=>$ongerealiseerdeKoersResultaaten,
									 'opgelopenRentes'=>$opgelopenRentes);
		$this->waarde = $waarden;
		return $waarden;
	}
	
	
	function createRows()
	{
		$row['waardeVanaf'] = array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
		$row['waardeTot'] = array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
		$row['mutatiewaarde'] = array("",vertaalTekst("Mutatie waarde vermogen",$this->pdf->rapport_taal));
		$row['totaalStortingen'] = array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
		$row['totaalOnttrekkingen'] = array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
		$row['directeOpbrengsten'] = array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
		$row['toegerekendeKosten'] = array("",vertaalTekst("Toegerekende kosten",$this->pdf->rapport_taal));
		$row['resultaatVerslagperiode'] = array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
		$row['rendementProcent'] = array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
		$row['rendementProcentJaar'] = array("",vertaalTekst("Rendement over lopende jaar",$this->pdf->rapport_taal));
		$row['gerealiseerdKoersresultaat'] = array("",vertaalTekst("gerealiseerdKoersresultaat",$this->pdf->rapport_taal));
		$row['ongerealiseerdeKoersResultaaten'] = array("",vertaalTekst("ongerealiseerdeKoersResultaaten",$this->pdf->rapport_taal));
		$row['opgelopenRentes'] = array("",vertaalTekst("opgelopenRentes",$this->pdf->rapport_taal));
		$row['totaal'] = array("",vertaalTekst("Totaal Performance",$this->pdf->rapport_taal));
		
		
		foreach ($this->categorien as $categorie)
		{
			$resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode']['mutatie'][$categorie] - $this->waarden['rapportagePeriode']['stortingen'][$categorie] + $this->waarden['rapportagePeriode']['onttrekkingen'][$categorie] + $this->waarden['rapportagePeriode']['opbrengsten'][$categorie] - $this->waarden['rapportagePeriode']['kosten'][$categorie];
			if ($categorie == 'Totaal')
			{
				$resultaatCorrectie = $resultaatVerslagperiode['Totaal'] - $this->waarde['opgelopenRentes'][$categorie] - $this->waarde['ongerealiseerdeKoersResultaaten'][$categorie] -
					($this->waardenPerGrootboek['totaalOpbrengst'] - $this->waardenPerGrootboek['totaalKosten']);
				if(round($resultaatCorrectie,1) != round($this->waarde['gerealiseerdKoersresultaat'][$categorie],1))//correctie vreemde valuta
				{
					$this->waarde['gerealiseerdKoersresultaat'][$categorie]  = $resultaatCorrectie ;
				}
			}
			
			array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode']['beginWaarde'][$categorie],$this->bedragDecimalen,true));
			array_push($row['waardeVanaf'],"");
			
			array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode']['eindWaarde'][$categorie],$this->bedragDecimalen));
			array_push($row['waardeTot'],"");
			
			array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode']['mutatie'][$categorie],$this->bedragDecimalen));
			array_push($row['mutatiewaarde'],"");
			
			if ($categorie == 'Liquiditeiten')
			{
				array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
				array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
				array_push($row['rendementProcent'],' ');
				array_push($row['rendementProcent'],' ');
				array_push($row['rendementProcentJaar'],' ');
				array_push($row['rendementProcentJaar'],' ');
			}
			else
			{
				array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
				array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
				array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode']['performance'][$categorie],2));
				array_push($row['rendementProcent'],'%');
				array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar']['performance'][$categorie],2));
				array_push($row['rendementProcentJaar'],'%');
			}
			
			array_push($row['totaalStortingen'],"");
			array_push($row['totaalOnttrekkingen'],"");
			
			if ($categorie == 'Totaal')
			{
				array_push($row['directeOpbrengsten'],'0');
				array_push($row['toegerekendeKosten'],'0');
			}
			else
			{
				array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode']['opbrengsten'][$categorie],$this->bedragDecimalen));
				array_push($row['toegerekendeKosten'],$this->formatGetal($this->waarden['rapportagePeriode']['kosten'][$categorie],$this->bedragDecimalen));
			}
			array_push($row['directeOpbrengsten'],"");
			array_push($row['toegerekendeKosten'],"");
			
			array_push($row['resultaatVerslagperiode'],$this->formatGetal($resultaatVerslagperiode[$categorie],$this->bedragDecimalen));
			array_push($row['resultaatVerslagperiode'],"");
		}
		return $row;
	}
	
	
	function waardenPerGrootboek()
	{
		
		if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		else
			$koersQuery = "";
		
		$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening,
		Grootboekrekeningen.Kosten ,Grootboekrekeningen.Opbrengst,".
			"SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) -  ".
			"SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery )AS waarde ".
			"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
			"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
			"  (Grootboekrekeningen.Kosten = '1' || Grootboekrekeningen.Opbrengst ='1') ".
			"GROUP BY Rekeningmutaties.Grootboekrekening ".
			"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
		
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$waardenPerGrootboek = array();
		while($grootboek = $DB->nextRecord())
		{
			if($grootboek['Opbrengst']=='1')
			{
				$waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
				$waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['bedrag'] += $grootboek['waarde'];
				$waardenPerGrootboek['totaalOpbrengst'] += $grootboek['waarde'];
			}
			else
			{
				if($grootboek[Grootboekrekening] == "KNBA")
				{
					$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = "Bankkosten en provisie";
					$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
				}
				else if($grootboek[Grootboekrekening] == "KOBU")
				{
					$waardenPerGrootboek['kosten']['KOST']['bedrag'] -= $grootboek['waarde'];
					$waardenPerGrootboek['kosten']['KOST']['omschrijving'] = "Transactiekosten";
				}
				else
				{
					$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
					$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
				}
				$waardenPerGrootboek['totaalKosten'] -= $grootboek['waarde'];
			}
		}
		
		return $waardenPerGrootboek;
	}
	
	
	function writeRapportConsolidatie()
	{
		include_once("rapport/include/rapportATTberekening_L51.php");
		$this->db = new DB();
		$this->tweedeStart();
		$DB = new DB();
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$kopStyle = "u";
		
		if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		else
			$koersQuery = "";
		
		if($this->pdf->portefeuilledata['PerformanceBerekening'] == 6)
			$periodeBlok = 'kwartaal';
		else
			$periodeBlok = 'maand';
		
		$query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, ".
			" Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, ".
			" Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
			" Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
			" AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
		$DB->SQL($query);
		$pdata = $DB->lookupRecord();
		
		$this->berekening = new rapportATTberekening_L51($pdata);
		$this->berekening->getAttributieCategorien();
		$this->getAttributieCategorien();
		$this->berekening->categorien=$this->AttCategorien;
		$this->berekening->pdata['pdf']=true;
		$this->berekening->attributiePerformance($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,'rapportagePeriode',$this->pdf->rapportageValuta,$periodeBlok);
		$this->berekening->attributiePerformance($this->portefeuille,$this->tweedePerformanceStart,$this->rapportageDatum,'lopendeJaar',$this->pdf->rapportageValuta,$periodeBlok);
		
		$this->waarden['rapportagePeriode']=$this->berekening->performance['rapportagePeriode'];
		$this->waarden['lopendeJaar']=$this->berekening->performance['lopendeJaar'];
		
		$realCategorie=array();
		foreach($this->berekening->categorien as $categorie)
		{
			if($this->waarden['lopendeJaar']['eindWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['beginWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['stortingen'][$categorie] <> 0 || $this->waarden['lopendeJaar']['onttrekkingen'][$categorie] <> 0)
			{
				$realCategorie[]=$categorie;
			}
		}
		
		$tmpCat=array();
		foreach($realCategorie as $categorie)
		{
			if($categorie <> 'Totaal' && $categorie <> 'Liquiditeiten')
				$tmpCat[]=$categorie;
		}
		
		if(count($realCategorie) > 6)
			$x=185/count($realCategorie)-3;
		else
			$x=23;
		
		$this->pdf->widthA = array(0,95,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->pdf->widthB = array(0,95,30,10,30,116);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R');
		
		
		
		if(is_array($this->pdf->__appvar['consolidatie']))
		{
			$this->pdf->templateVars['PERFPaginas']=$this->pdf->page+1;
			$this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;
			
			$fillPortefeuilles=$this->pdf->portefeuilles;
			$fillPortefeuilles[]=$this->portefeuille;
			
			foreach($fillPortefeuilles as $portefeuille)
			{
				if(!isset($this->perfWaarden[$portefeuille]))
					$this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
			}
			
			$backup=$this->pdf->portefeuilles;
			$aantalPortefeuilles=count($this->pdf->portefeuilles);
			if($aantalPortefeuilles>6)
			{
				$n=1;
				$p=0;
				$verdeling=array();
				$tmp=array();
				foreach($this->pdf->portefeuilles as $index=>$portefeuille)
				{
					//echo "$n $p $aantalPortefeuilles $portefeuille <br>\n";
					$tmp[]=$portefeuille;
					if($n%6==0 || $n == $aantalPortefeuilles)
					{
						$verdeling[$p]=$tmp;
						$tmp=array();
						$p++;
						// $n=0;
					}
					
					$n++;
				}
				//listarray($verdeling);exit;
				foreach($verdeling as $pagina=>$portefeuilles)
				{
					$this->pdf->portefeuilles=$portefeuilles;
					$this->addconsolidatie();
				}
				$this->pdf->portefeuilles=$backup;
			}
			else
				$this->addconsolidatie();
			
		}
		
		if($this->pdf->debug)
		{
			// listarray($this->berekening->performance);flush();
			// exit;
		}
	}
	
	
	
	
	function addconsolidatie()
	{
		
		if(!isset($this->pdf->__appvar['consolidatie']))
		{
		//	$this->pdf->__appvar['consolidatie']=1;
			$this->pdf->portefeuilles=array($this->portefeuille);
		}
		$this->pdf->doubleHeader=true;
		$this->pdf->addPage();
		$this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;
		
		$startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
		$startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
		$eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
		
		//	$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		//  $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
		//  $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
		// listarray($this->pdf->portefeuilles);
		$fillArray=array(0,1);
		$subOnder=array('','');
		$volOnder=array('U','U');
		$subBoven=array('','');
		$header=array("","\n \n".vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
		$samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
		
		$db=new DB();
		
		//if(count($this->pdf->portefeuilles)<6)
			$portefeuilles[]=$this->portefeuille;
	//	else
		//	$portefeuilles=array();
		foreach($this->pdf->portefeuilles as $portefeuille)
			$portefeuilles[]=$portefeuille;
		$longName=false;
		
		$perfWaarden=array();
		foreach($portefeuilles as $portefeuille)
		{
			if(strlen($portefeuille)>15)
				$longName=true;
			$query="SELECT Depotbanken.omschrijving FROM Depotbanken JOIN Portefeuilles ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE Portefeuilles.Portefeuille='".$portefeuille."'";
			$db->SQL($query);
			$depotbank=$db->lookupRecord();
			$volOnder[]='U';
			$volOnder[]='U';
			$subOnder[]='U';
			$subOnder[]='';
			$subBoven[]='T';
			$subBoven[]='';
			$fillArray[]=1;
			$fillArray[]=1;
			if($portefeuille==$this->portefeuille)
				$header[]=vertaalTekst("Totaal",$this->pdf->rapport_taal);
			else
				$header[]=$portefeuille."\n".$depotbank['omschrijving'];
			$header[]='';
			$samenstelling[]='';
			$samenstelling[]='';
			if(!isset($this->perfWaarden[$portefeuille]))
				$this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
			
			$perfWaarden[$portefeuille]=$this->perfWaarden[$portefeuille];
		}
		
		foreach($perfWaarden as $port=>$waarden)
		{
			foreach($waarden['opbrengstenPerGrootboek'] as $categorie=>$waarde)
				if(round($waarde,2)!=0.00)
					$opbrengstCategorien[$categorie]=$categorie;
			foreach($waarden['kostenPerGrootboek'] as $categorie=>$waarde)
				if(round($waarde,2)!=0.00)
					$kostenCategorien[$categorie]=$categorie;
		}
		
		$perbegin=array("",vertaalTekst("Waarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
		$waardeRapdatum=array("",vertaalTekst("Waarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
		$mutwaarde=array("",vertaalTekst("Mutatie waarde vermogen",$this->pdf->rapport_taal));
		$stortingen=array("",vertaalTekst("Stortingen",$this->pdf->rapport_taal));
		$onttrekking=array("",vertaalTekst("Onttrekkingen",$this->pdf->rapport_taal));
		$resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
		$rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
		$ongerealiseerd=array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
		$gerealiseerd=array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
		$valutaResultaat=array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal)); //
		$rente=array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal));//
		$totaalOpbrengst=array("","");//totaalOpbrengst
		$aandeel=array("",vertaalTekst("Percentage v/h vermogen",$this->pdf->rapport_taal));//
		
		$totaalKosten=array("","");   //totaalKosten 
		$totaal=array("",vertaalTekst("Resultaat lopende jaar",$this->pdf->rapport_taal));   //totaalOpbrengst-totaalKosten 
		
		foreach($perfWaarden as $portefeuille=>$waarden)
		{
			$perbegin[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeBegin'],0,true);
			$perbegin[]='';
			$waardeRapdatum[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind'],0,true);
			$waardeRapdatum[]='';
			$mutwaarde[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeMutatie'],0,true);
			$mutwaarde[]='';
			$stortingen[]=$this->formatGetal($perfWaarden[$portefeuille]['stortingen'],0);
			$stortingen[]='';
			$onttrekking[]=$this->formatGetal($perfWaarden[$portefeuille]['onttrekkingen'],0);
			$onttrekking[]='';
			$resultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['resultaatVerslagperiode'],0);
			$resultaat[]='';
			$rendement[]=$this->formatGetal($perfWaarden[$portefeuille]['rendementProcent'],2);
			$rendement[]='%';
			$ongerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat'],0);
			$ongerealiseerd[]='';
			$gerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat'],0);
			$gerealiseerd[]='';
			$valutaResultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['koersResulaatValutas'],0);
			$valutaResultaat[]='';
			$rente[]=$this->formatGetal($perfWaarden[$portefeuille]['opgelopenRente'],0);
			$rente[]='';
			$totaalOpbrengst[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst'],0);
			$totaalOpbrengst[]='';
			$totaalKosten[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalKosten'],0);
			$totaalKosten[]='';
			$totaal[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst']-$perfWaarden[$portefeuille]['totaalKosten'],0);
			$totaal[]='';
			$aandeel[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind']/$this->perfWaarden[$this->portefeuille]['waardeEind']*100,1);
			$aandeel[]='%';
			
		}
		
		// if($longName==true && count($portefeuilles) < 8)
		$cols=7;
		//else
		//  $cols=9;  
		
		$w=(297-2*8-50-(9*3))/$cols;
		$w2=4.5;
		$this->pdf->widthB = array(0,50,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

		$this->pdf->ln();
//listarray($perfWaarden);
		
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		//$this->pdf->fillCell=$fillArray;
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row($header);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		
		$this->pdf->row($perbegin);
		//,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
		$this->pdf->CellBorders = $subOnder;
		$this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
		$this->pdf->CellBorders = array();
		// subtotaal
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
		$this->pdf->CellBorders = $subOnder;
		$this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
		$this->pdf->ln();
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->ln();
		
		$this->pdf->CellBorders = $volOnder;
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
		$this->pdf->ln(3);
		$this->pdf->row($aandeel);
		$this->pdf->ln(3);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array();
		//	$ypos = $this->pdf->GetY()-5;
		//	$this->pdf->SetY($ypos);
		
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		//$this->pdf->fillCell=$fillArray;
		// $this->pdf->SetTextColor(255,255,255);
		$YSamenstelling=$this->pdf->GetY();
		//$this->pdf->row($samenstelling);//,"","","",""));
		//$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->fillCell=array();
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		
		
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row($ongerealiseerd);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		$this->pdf->row($gerealiseerd);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
		//	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
		$this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
		
		$keys=array();
		foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
			$keys[]=$key;
		
		foreach ($opbrengstCategorien as $categorie)
		{
			$tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
			foreach($perfWaarden as $port=>$waarden)
			{
				$tmp[]=$this->formatGetal($waarden['opbrengstenPerGrootboek'][$categorie],0);
				$tmp[]='';
			}
			//if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
		}
		
		$this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
		$this->pdf->ln();
		$this->pdf->CellBorders = array();
		
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		foreach ($kostenCategorien as $categorie)
		{
			
			$tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
			foreach($perfWaarden as $port=>$waarden)
			{
				$tmp[]=$this->formatGetal($waarden['kostenPerGrootboek'][$categorie],0);
				$tmp[]='';
			}
			//		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}
		$this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
		$this->pdf->CellBorders = $volOnder;
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array();
		
	}
	
	function getWaarden($portefeuille,$vanafDatum,$totDatum)
	{
		global $__appvar;
		// ***************************** ophalen data voor afdruk ************************ //
		
		$waarden=array();
		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
		{
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
			$totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$vanafDatum);
			$vanRapKoers=getValutaKoers($this->pdf->rapportageValuta,$totDatum);
		}
		else
		{
			$koersQuery = "";
			$totRapKoers=1;
			$vanRapKoers=1;
		}
		
		if(substr($vanafDatum,5,5)=='01-01')
			$beginJaar=true;
		else
			$beginJaar=false;
		
		$fondsen=berekenPortefeuilleWaarde($portefeuille,$vanafDatum,$beginJaar,$this->pdf->rapportageValuta,$vanafDatum);
		$totaal=array();
		$totaalWaardeVanaf['totaal']=0;
		foreach($fondsen as $id=>$regel)
		{
			$totaalWaardeVanaf['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
			if($regel['type']=='rente')
			{
				$totaalB['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
			}
		}
		
		$totaalWaarde['totaal']=0;
		$fondsen=berekenPortefeuilleWaarde($portefeuille,$totDatum,false,$this->pdf->rapportageValuta,$vanafDatum);
		$totaal=array();
		foreach($fondsen as $id=>$regel)
		{
			$totaalWaarde['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
			if($regel['type']=='rente')
			{
				$totaalA['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
			}
			if($regel['type']=='fondsen')
			{
				$totaal['totaalB']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
				$totaal['totaalA']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
			}
		}
		
		$ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA'];
		$waarden['ongerealiseerdeKoersResultaat']=$ongerealiseerdeKoersResultaat;
		
		
		$DB=new DB();
		
		$waardeEind				  = $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		//$rendementProcent  	=  performanceMeting($portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
//echo $this->pdf->portefeuilledata['PerformanceBerekening'];exit;
		$rendementProcent = $this->berekening->performanceMeting($portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		$waarden['waardeEind']=$waardeEind;
		$waarden['waardeBegin']=$waardeBegin;
		$waarden['waardeMutatie']=$waardeMutatie;
		$waarden['stortingen']=$stortingen;
		$waarden['onttrekkingen']=$onttrekkingen;
		$waarden['resultaatVerslagperiode']=$resultaatVerslagperiode;
		$waarden['rendementProcent']=$rendementProcent;
		
		$RapJaar = date("Y", db2jul($totDatum));
		$RapStartJaar = date("Y", db2jul($vanafDatum));
		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;
		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($portefeuille, $vanafDatum, $totDatum,$this->pdf->rapportageValuta,true);
		$totaalOpbrengst += $gerealiseerdeKoersResultaat;
		$waarden['gerealiseerdeKoersResultaat']=$gerealiseerdeKoersResultaat;
		
		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $totRapKoers;
		$totaalOpbrengst += $opgelopenRente;
		$waarden['opgelopenRente']=$opgelopenRente;
		
		if($this->pdf->GrootboekPerVermogensbeheerder)
			$query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
		else
			$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		
		
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
				"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
				"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
				"Rekeningmutaties.Verwerkt = '1' AND ".
				"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
				"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
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
		$waarden['opbrengstenPerGrootboek']=$opbrengstenPerGrootboek;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;
		
		// loopje over Grootboekrekeningen Kosten = 1
		if($this->pdf->GrootboekPerVermogensbeheerder)
		{
			$query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, ".
				"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
				"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
				"FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder ".
				"WHERE ".
				"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
				"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
				"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
				"Rekeningmutaties.Verwerkt = '1' AND ".
				"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
				"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
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
				"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
				"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
				"Rekeningmutaties.Verwerkt = '1' AND ".
				"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
				"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
				"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
				"Grootboekrekeningen.Kosten = '1' ".
				"GROUP BY Rekeningmutaties.Grootboekrekening ".
				"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
		}
		
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		
		$kostenPerGrootboek = array();
    $totaalKosten=0;
		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			elseif($kosten['Grootboekrekening'] == "KOBU")
			{
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			
			
			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}
		foreach ($kostenPerGrootboek as $data)
		{
			$tmp[$data['Omschrijving']]=$data['Bedrag'];
		}
		
		$waarden['kostenPerGrootboek']=$tmp;
		$waarden['totaalKosten']=$totaalKosten;
		
		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		$waarden['kostenProcent']=$kostenProcent;
		$waarden['koersResulaatValutas']=$koersResulaatValutas;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;
		
		return $waarden;
	}
	
	
	function toonZorgplicht()
	{
		global $__appvar;
		$DB=new DB();
		
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		if($totaalWaarde['totaal'] <> 0)
		{
			$query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$totaalWaarde['totaal']." as percentage,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE TijdelijkeRapportage.Portefeuille =  '".$this->portefeuille."' AND
 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
			$DB->SQL($query); //echo $query;exit;
			$DB->Query();
			while($data= $DB->nextRecord())
			{
				$categorieWaarden[$data['Zorgplicht']]=$data['percentage']*100;
				$categorieOmschrijving[$data['Zorgplicht']]=$data['beleggingscategorieOmschrijving'];
			}
		}
		$zorgplicht = new Zorgplichtcontrole();
		$zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
		
		$tmp=array();
		foreach ($zpwaarde['conclusie'] as $index=>$regelData)
			$tmp[$regelData[0]]=$regelData;
		
		krsort($tmp);

//listarray($zpwaarde['conclusie']);
		//listarray($tmp);exit;
		
		$this->pdf->SetAligns(array('L','L','R','R','R','R'));
		$this->pdf->SetY(150);
		$beginY=$this->pdf->getY();
		$extraX=155;
		
		$this->pdf->SetWidths(array($extraX,40,16,16,16,16,16));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',
											vertaalTekst("beleggingscategorie",$this->pdf->rapport_taal),
											vertaalTekst("minimaal",$this->pdf->rapport_taal),
											vertaalTekst("norm",$this->pdf->rapport_taal),
											vertaalTekst("maximaal",$this->pdf->rapport_taal),
											vertaalTekst("werkelijk",$this->pdf->rapport_taal),
											vertaalTekst("conclusie",$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetAligns(array('L','L','R','R','R','R'));
		//foreach ($tmp as $index=>$regelData)
		
		/*
    - zorgplichtcategorie
- minimum
- neutraal (is norm)
- maximum
- werkelijk
*/
		//  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
		foreach ($categorieWaarden as $cat=>$percentage)
		{
			if($tmp[$cat][2])
				$risicogewogen=$tmp[$cat][2]."%";
			else
				$risicogewogen='';
			//if($zpwaarde['categorien'][$cat]['Minimum'])   
			$min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
			//else
			//   $min='';   
			//if($zpwaarde['categorien'][$cat]['Maximum'])  
			$max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
			// else
			//   $max='';  
			$norm=$this->formatGetal($zpwaarde['categorien'][$cat]['Norm'],0)."%";
			
			if($tmp[$cat][5]=='Voldoet')
				$this->pdf->MemImage($this->checkImg,120+$extraX,$this->pdf->getY(),3.9,3.9);
			else
				$this->pdf->MemImage($this->deleteImg,120+$extraX,$this->pdf->getY(),3.9,3.9);
			
			
			$this->pdf->row(array('',vertaalTekst($categorieOmschrijving[$cat],$this->pdf->rapport_taal),$min,$norm,$max,$this->formatGetal($categorieWaarden[$cat],1)."%"));//$risicogewogen
		}
		$this->pdf->Rect($this->pdf->marge+$extraX,$beginY,120,count($categorieWaarden)*4+4);
	}
	
	
}
?>