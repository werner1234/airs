<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/12 11:49:05 $
File Versie					: $Revision: 1.3 $

$Log: RapportPERFG_L65.php,v $
Revision 1.3  2020/04/12 11:49:05  rvv
*** empty log message ***

Revision 1.2  2019/12/08 09:01:23  rvv
*** empty log message ***

Revision 1.1  2019/12/07 17:47:50  rvv
*** empty log message ***

Revision 1.13  2019/11/16 17:12:28  rvv
*** empty log message ***

Revision 1.12  2019/03/31 12:19:56  rvv
*** empty log message ***

Revision 1.11  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.10  2018/07/14 14:04:37  rvv
*** empty log message ***

Revision 1.9  2018/02/24 18:33:46  rvv
*** empty log message ***

Revision 1.8  2017/09/07 05:49:19  rvv
*** empty log message ***

Revision 1.7  2017/09/06 16:31:28  rvv
*** empty log message ***

Revision 1.6  2016/04/21 19:31:19  rvv
*** empty log message ***

Revision 1.5  2016/04/20 15:46:31  rvv
*** empty log message ***

Revision 1.4  2016/04/19 10:46:26  rvv
*** empty log message ***

Revision 1.3  2016/04/18 18:51:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERFG_L65
{

	function RapportPERFG_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		if(date('d-m',$this->pdf->rapport_datumvanaf)!='01-01')
		{
			$this->toonYtd = true;
			$this->toonPeriode=true;
		}
		else
		{
			$this->toonYtd = true;
			$this->toonPeriode=false;
		}
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

		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
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
    $this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;
    
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
		
		$waardeEind				= $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		if($this->toonPeriode==true)
			$rendementProcent  	= $this->performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		//echo "$rendementProcent  	= performanceMeting(".$this->portefeuille.", ".$this->rapportageDatumVanaf.", ".$this->rapportageDatum.", ".$this->pdf->portefeuilledata['PerformanceBerekening'].",".$this->pdf->rapportageValuta." ";exit;
		if($this->toonYtd==true)
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
			$rendementProcentJaar = $this->performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
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
			
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,0,true),""));
			$this->pdf->ln(2);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,0),""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,0),""));
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,0),""));
			$this->pdf->Line($posSubtotaal+$extraLengte ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln(2);
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,0),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			
			$this->pdf->ln();
			$this->pdf->ln();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($this->toonPeriode==true)
				$this->pdf->row(array("",vertaalTekst("Rendement zonder liquiditeiten over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
			if($this->toonYtd)
				$this->pdf->row(array("",vertaalTekst("Rendement zonder liquiditeiten lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));
			
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
			
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,0,true),""));
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,0),""));
			// subtotaal
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->row(array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),$this->formatGetal($waardeMutatie,0),""));
			$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,0),""));
			$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,0),""));
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,0),""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
			$this->pdf->ln();
			
			
			if($this->toonPeriode==true)
				$this->pdf->row(array("",vertaalTekst("Rendement zonder liquiditeiten over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
			if($this->toonYtd==true)
				$this->pdf->row(array("",vertaalTekst("Rendement zonder liquiditeiten lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));
			
			
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
		
		
		$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,0),""));
		$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,0),""));
		if(round($koersResulaatValutas,2) != 0.00)
			$this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,0),""));
		if(round($opgelopenRente) <> 0)
			$this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,0),""));
		
		if($this->pdf->rapport_layout == 5 )
		{
			$opbrengstenPerGrootboek['Fractieverrekeningen'] =  $opbrengstenPerGrootboek['Stockdividend'];
			unset($opbrengstenPerGrootboek['Stockdividend']);
		}
		
		while (list($key, $value) = each($opbrengstenPerGrootboek))
		{
			if(round($value,2) != 0.00)
				$this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,0),""));
		}
		
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,0)));
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
				$this->pdf->row(array("",vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'],0),""));
		}
		
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalKosten,0)));
		
		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
		
		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		
		if($this->pdf->rapport_layout == 17)
		{
			
			
			$this->pdf->SetWidths(array(80,5,30,10,30,120));
			//	  $this->pdf->row(array("","","","Resultaat over verslagperiode",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->pdf->row(array("","","Resultaat over verslagperiode","",$this->formatGetal($totaalOpbrengst - $totaalKosten,0)));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->SetWidths($this->pdf->widthA);
		}
		else
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			//$this->pdf->SetWidths(array(130,80,30,5,30,120););
			$this->pdf->SetWidths(array(80,5,30,10,30,120));
			
			$this->pdf->row(array(vertaalTekst("Totaal resultaat over verslagperiode",$this->pdf->rapport_taal),"",'','',$this->formatGetal($totaalOpbrengst - $totaalKosten,0),""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->SetWidths($this->pdf->widthA);
		}
		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		//	$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

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

	    $performanceJaar =  $this->performanceMeting($this->portefeuille,$startDatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		  $performancePeriode = $this->performanceMeting($this->portefeuille,date('Y-m-d',$this->pdf->rapport_datumvanaf),$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
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
		 	$this->pdf->row(array("",vertaalTekst("Saldo liquiditeiten per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)),$this->formatGetal($totaalWaardeLiquiditeitenVanaf['totaal'],0),""));


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
		  $this->pdf->row(array("",vertaalTekst("Saldo liquiditeiten per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($totaalWaardeLiquiditeiten['totaal'],0),""));

		  if($this->pdf->lastPOST['perfBm'])
		  {
		    $this->pdf->ln();
		    $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		  }

		include_once($__appvar["basedir"]."/html/indexBerekening.php");

		$index=new indexHerberekening();
		$data=$index->getWaarden($this->pdf->PortefeuilleStartdatum, $this->rapportageDatum,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);
	//	listarray($data);
		$grafiekData=array('titel'=>vertaalTekst('Portefeuille rendement vanaf' ,$this->pdf->rapport_taal).' '.date('d-m-Y',db2jul($this->pdf->PortefeuilleStartdatum)));
		$maanden=array('','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
		foreach($maanden as $key=>$value)
      $maanden[$key]=vertaalTekst($value,$this->pdf->rapport_taal);
		foreach($data as $indexData)
		{
			$grafiekData['portefeuille'][]=$indexData['index']-100;
			$julDatum=db2jul($indexData['datum']);
			$grafiekData['datum'][]=$maanden[date('n',$julDatum)].'-'.date('y',$julDatum);
		}
    $this->pdf->setXY(180,48);
		// $datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR',$output=''
		$color=array(51,102,204);
		//$this->LineDiagram(100, 50, $grafiekData, $color, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4);
	}


	function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4)
	{
		global $__appvar;

		$legendDatum= $data['datum'];
		$legendaItems= $data['legenda'];
		$titel=$data['titel'];
		$data1 = $data['specifiekeIndex'];
		$data = $data['portefeuille'];

		if(count($data1)>0)
			$bereikdata = array_merge($data,$data1);
		else
			$bereikdata =   $data;

		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 2;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1 ;
		$lDiag = floor($w - $margin * 1 );

		$this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->Cell($w,0,$titel,0,0,'C');

		$this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

		if(is_array($color[0]))
		{
			$color1= $color[1];
			$color = $color[0];
		}

		if($color == null)
			$color=array(155,155,155);
		$this->pdf->SetLineWidth(0.2);


		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);

		if ($maxVal == 0)
		{
			$maxVal = ceil(max($bereikdata));
		}
		if ($minVal == 0)
		{
			$minVal = floor(min($bereikdata));
		}

		$minVal = floor(($minVal-1) * 1.1);
		if($minVal > 0)
			$minVal=0;
		$maxVal = ceil(($maxVal+1) * 1.1);
		$legendYstep = ($maxVal - $minVal) / $horDiv;
		$verInterval = ($lDiag / $verDiv);
		$horInterval = ($hDiag / $horDiv);
		$waardeCorrectie = $hDiag / ($maxVal - $minVal);
		$unit = $lDiag / count($data);



		for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
			$xpos = $XDiag + $verInterval * $i;

		$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetDrawColor(0,0,0);

		$stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
		$unith = $hDiag / (-1 * $minVal + $maxVal);

		$top = $YPage;
		$bodem = $YDiag+$hDiag;
		$absUnit =abs($unith);

		$nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
		$n=0;
		for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
		{
			if($i > $YPage)
			{
				$skipNull = true;
				$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
				$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
			}
			/*
      $yGetal=$offset-($n*$stapgrootte)+$minVal;
      if($yGetal>=$minVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      }
*/

			$n++;
			if($n >20)
				break;
		}

		$n=0;
		for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
		{
			/*
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
*/
			$yGetal=$offset-(-1*$n*$stapgrootte)+$minVal;
			if($yGetal<=$maxVal)
			{
				$this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
				if($skipNull == true)
					$skipNull = false;
				else
					$this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
			}


			$n++;
			if($n >20)
				break;
		}
		$yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
		$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
		$jaren=ceil(count($data)*2/12);
		for ($i=0; $i<count($data); $i++)
		{
			if($i%$jaren==0)
				$this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+9,$legendDatum[$i],25);
			$yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;

			if ($i>0)
			{
				$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
//        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
			}
//      if ($i==count($data)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);


			$yval = $yval2;
		}

		if(is_array($data1))
		{
			$yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
			$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

			for ($i=0; $i<count($data1); $i++)
			{
				$yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;

				if ($i>0)
				{
					$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
//          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
				}
//        if ($i==count($data1)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

				$yval = $yval2;
			}
		}


		$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));


		//   $XPage
		// $YPage


		$step=5;
		foreach ($legendaItems as $index=>$item)
		{
			if($index==0)
				$kleur=$color;
			else
				$kleur=$color1;
			$this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
			$this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
			$this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
			$this->pdf->Cell(0,3,$item);
			$step+=($w/2);
		}
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetFillColor(0,0,0);
	}
  
  
  function performanceMeting($portefeuille, $datumBegin, $datumEind, $type = "1", $valuta = 'EUR')
  {
    global $__appvar;
    $DB = new DB();
    $query="SELECT layout FROM Vermogensbeheerders JOIN Portefeuilles on Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
    $DB->SQL($query);
    $layout=$DB->lookupRecord();
    if(file_exists($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php"))
    {
      include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php");
      $attObject="ATTberekening_L".$layout['layout'];
      $att=new $attObject();
      if(method_exists("ATTberekening_L".$layout['layout'],'getPerf'))
      {
        return $att->getPerf($portefeuille, $datumBegin, $datumEind,$valuta);
      }
    }
//echo   " $portefeuille, $datumBegin, $datumEind, $type , $valuta <br>\n";ob_flush();
    if($type == 6)//Attributie kwartaalwaardering
    {
      $index=new rapportATTberekening($portefeuille);
      $index->categorien[] = 'Totaal';
      $performance = $index->attributiePerformance($portefeuille, $datumBegin, $datumEind,'all',$valuta,'kwartaal');
      return $performance['Totaal'] -100;
    }
    elseif($type == 5)//Maandelijkse waardering realtime?
    {
      $index=new indexHerberekening();
      $indexData = $index->getWaardenATT($datumBegin, $datumEind,$portefeuille,'Totaal','maand',$valuta);
      foreach ($indexData as $data)
      {
        $performance =  $data['index'] -100;
      }
      return $performance;
    }
    elseif($type == 3)//TWR
    {
      $index=new indexHerberekening();
      $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','TWR');
      foreach ($indexData as $data)
        $performance =  $data['index'] -100;
      return $performance;
    }
    elseif($type == 4)//Maandelijkse waardering
    {
      $index=new indexHerberekening();
      $perioden=$index->getMaanden(db2jul($datumBegin),db2jul($datumEind));
  
  
     $portefeuilleVerdeling=array();
     foreach($perioden as $periode)
     {
       
       foreach($periode as $index=>$datum)
       {
         if(!isset($portefeuilleVerdeling[$datum]))
         {
           $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $datum, (substr($datum, 5, 5) == '01-01'?true:false), $valuta, $datum);
           foreach ($fondswaarden as $fondsData)
           {
             if($fondsData['type']=='rekening' || $fondsData['beleggingscategorie']=='Liquiditeiten')
               $portefeuilleVerdeling[$datum]['rekening']+=$fondsData['actuelePortefeuilleWaardeEuro'];
             $portefeuilleVerdeling[$datum]['totaal']+=$fondsData['actuelePortefeuilleWaardeEuro'];
           }
         }
       }
       
       $query = "SELECT ".
         "SUM(((TO_DAYS('".$periode['stop']."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$periode['stop']."') - TO_DAYS('".$periode['start']."')) ".
         "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
         "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
         "FROM  (Rekeningen, Portefeuilles)
	        Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
         "WHERE ".
         "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
         "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
         "Rekeningmutaties.Verwerkt = '1' AND ".
         "Rekeningmutaties.Boekdatum > '".$periode['start']."' AND ".
         "Rekeningmutaties.Boekdatum <= '".$periode['stop']."' AND ".
         "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
       $DB->SQL($query);
       $DB->Query();
       $weging = $DB->NextRecord();
       $portefeuilleVerdeling[$periode['stop']]['LiqMutatie']=$weging['totaal2'];
       $portefeuilleVerdeling[$periode['stop']]['gewogenLiqMutatie']=$weging['totaal1'];
       
     }
     //listarray($portefeuilleVerdeling);
  
      $performanceTotaal=1;
      foreach($perioden as $periode)
      {
        $beginwaarde=$portefeuilleVerdeling[$periode['start']]['totaal'];
        $eindwaarde=$portefeuilleVerdeling[$periode['stop']]['totaal'];
  
        //$gewogenLiq=0;//$portefeuilleVerdeling[$periode['stop']]['gewogenLiqMutatie'];
        $liqMutatie=$portefeuilleVerdeling[$periode['stop']]['LiqMutatie'];
        $liqWaarde=$portefeuilleVerdeling[$periode['start']]['rekening'];
        
        
        $gemiddelde = $beginwaarde - $liqWaarde;
        if($gemiddelde <> 0)
        {
          $performance = ((($eindwaarde - $beginwaarde) - $liqMutatie) / $gemiddelde);
          $performanceTotaal=($performanceTotaal)*(1+$performance);
         // echo "$performance -> $performanceTotaal <br>\n";
        }
        
      }
      return ($performanceTotaal-1)*100;

    }
    elseif($type == 7)//Dagelijkse YtD waardering
    {
      $index=new indexHerberekening();
      $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','dagYTD');
      foreach ($indexData as $data)
      {
        $performance =  $data['index'] -100;
      }
      return $performance;
      
    }
    elseif($type == 8)//Kwartaal waardering
    {
      $index=new indexHerberekening();
      $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','kwartaal',$valuta);
      foreach ($indexData as $data)
      {
        $performance =  $data['index'] -100;
      }
      return $performance;
    }
    
    if ($valuta != "EUR" )
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    if(substr($datumBegin,0,4)==substr($datumEind,0,4) || ((substr($datumBegin,5,5)=='31-12') && substr($datumEind,5,5)=='01-01') )
    {
      // haal beginwaarde op.
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
        "FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum = '".$datumBegin."' AND ".
        " portefeuille = '".$portefeuille."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
      $DB->SQL($query);
      $DB->Query();
      $beginwaarde = $DB->NextRecord();
      //echo $beginwaarde." = ".$beginwaarde[totaal]." / ".getValutaKoers($valuta,$datumBegin)."<br>";
      $beginwaarde = $beginwaarde['totaal'] / getValutaKoers($valuta,$datumBegin);
      
      // haal eindwaarde op.
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
        "FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='".$datumEind."' AND ".
        " portefeuille = '".$portefeuille."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
      $DB->SQL($query);
      $DB->Query();
      $eindwaarde = $DB->NextRecord();
      $eindwaarde = $eindwaarde['totaal']  / getValutaKoers($valuta,$datumEind);
      
      $query = "SELECT ".
        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
        "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
        "FROM  (Rekeningen, Portefeuilles)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
        "WHERE ".
        "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
      $DB->SQL($query);
      $DB->Query();
      $weging = $DB->NextRecord();
      
      $gemiddelde = $beginwaarde + $weging['totaal1'];
      if($gemiddelde <> 0)
        $performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / $gemiddelde) * 100;
    }
    else
    {
      $index=new indexHerberekening();
      $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','jaar',$valuta);
      foreach($indexData as $index)
        $performance=$index['index']-100;
    }
//echo "gemiddelde $gemiddelde = $beginwaarde + ".$weging[totaal1]."\n<br>\n";
//echo "$datumBegin - $datumEind -> performance = $performance = ((($eindwaarde - $beginwaarde) - ".$weging[totaal2].") / $gemiddelde) * 100";flush();
//echo "<br>$performance<br>";
    return $performance;
  }

}
?>