<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/04 16:40:47 $
File Versie					: $Revision: 1.7 $

$Log: RapportPERF_L57.php,v $
Revision 1.7  2020/03/04 16:40:47  rvv
*** empty log message ***

Revision 1.6  2019/10/30 16:47:58  rvv
*** empty log message ***

Revision 1.5  2015/01/02 11:46:23  rvv
*** empty log message ***

Revision 1.4  2015/01/02 10:07:51  rvv
*** empty log message ***

Revision 1.3  2015/01/01 22:02:01  rvv
*** empty log message ***

Revision 1.2  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.1  2014/12/28 14:29:08  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L57
{
	function RapportPERF_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";

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
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";
		$kopStyle = "u";

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,70,25,5,25,120);
		$this->pdf->alignA = array('L','L','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(1,85,25,5,25,297-(1+85+25+5+25+2*$this->pdf->marge));
		$this->pdf->alignB = $this->pdf->alignA;

    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

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
		$ongerealiseerdeKoersResultaat = $totaal[totaalB] - $totaal[totaalA]; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.

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
			  "Rekeningmutaties.Grootboekrekening = '".$gb[Grootboekrekening]."' ";

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
			else if($kosten['Grootboekrekening'] == "KOBU" && $this->pdf->rapport_layout != 14 && $this->pdf->rapport_layout != 10)
			{
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
        $kostenPerGrootboek['KOST']['omschrijving'] = "Transactiekosten";
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

			$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
			$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,2,true),""));
			$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
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

			$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,2),""));
			$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,2),""));
			if(round($koersResulaatValutas,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
      if($this->pdf->rapport_layout == 5)
			  $this->pdf->row(array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
			else
			  $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
		
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

		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst - $totaalKosten,2)));

		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		

   $this->toonBenchmark();
   $this->benchmarkGrafiek();
	}
  
  function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	    {
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$i]['start'],5,5)=='12-31')
	        $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
	    }

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
  }
  
  function benchmarkGrafiek()
  {
    $db=new DB();
    $query="SELECT
    Fondsen.Fonds,
    Portefeuilles.SpecifiekeIndex,
    Portefeuilles.Portefeuille,
    Fondsen.Omschrijving as fondsOmschrijving
    FROM
    Portefeuilles
    INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
    WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";

    $db->SQL($query);
    $db->Query();
    $index=$db->lookupRecord();
    if($index['Fonds'] <> '')
    {
      $maanden=$this->getMaanden(db2jul('2011-01-01'),db2jul($this->rapportageDatum));
      foreach($maanden as $periode)
      {
        $tmp=array('fondsKoers_eind'=>$this->getFondsKoers($index['Fonds'],$periode['stop']),'fondsKoers_begin'=>$this->getFondsKoers($index['Fonds'],$periode['start']));
        $periodePerf=($tmp['fondsKoers_eind'] - $tmp['fondsKoers_begin']) / ($tmp['fondsKoers_begin'] );
        $indexMaanden[$periode['stop']]=$periodePerf;
      }
      $stapeling=1;
      foreach($indexMaanden as $maand=>$maandPerf)
      {
        $stapeling=$stapeling*(1+$maandPerf);
        $indexStapeling[$maand]=($stapeling-1)*100;
      
        $grafiekData['Index'][]=($stapeling-1)*100;
     
        if(substr($maand,5,2)=='01')
          $grafiekData['Datum'][]=substr($maand,0,4);
        else
          $grafiekData['Datum'][]='';  
      
      }
  //  listarray($indexMaanden);
  //  listarray($indexStapeling);
   // listarray(array_sum($indexMaanden));
      $this->pdf->SetXY(170,120);
   
      $this->pdf->MultiCell(110,4,$index['fondsOmschrijving'],0,'C',0);
      $this->pdf->SetXY(175,130);
      $color=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      $color=array(0,0,0);
      $this->LineDiagram(110, 60, $grafiekData,$color,0,0,4,4,0);
    }
  }
 
 
 
   function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['Datum'];
   // $legendJaar= $data['jaren'];
  //  $data1 = $data['specifiekeIndex'];
    $data = $data['Index'];
  //  $legendaItems= $data['legenda'];

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

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'D','');//,array(245,245,245)

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }
    
//$maxVal=75;
//$minVal=-25;
//echo "$maxVal - $minVal = ".($maxVal-$minVal);
$bereik=($maxVal-$minVal);
//if($bereik)
//$bereik=60;
$minVal=floor($minVal*0.04)/0.04;
$maxVal=ceil($maxVal*0.04)/0.04;
$bereik=($maxVal-$minVal);

$lijnen=array(4,5,3,6,7,8);
foreach($lijnen as $optie)
{
  if($bereik%$optie==0)
  {
    $horDiv=$optie;
    break;
  }
}
//echo "$minVal $maxVal $bereik $horDiv";

    //$minVal = floor(($minVal-1) * 1.1);
    //$maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($bereik) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($bereik);
    $unit = $lDiag / count($data);
    
    if($jaar && count($data) < 12)
      $unit = $lDiag / 12;

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = (abs($bereik)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-8, $i, 100-($n*$stapgrootte) ."");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-8, $i, 100+($n*$stapgrootte)+0 ."");

      $n++;
      if($n >20)
         break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    for ($i=0; $i<count($data); $i++)
    {
      $this->pdf->line($XDiag+($i+1)*$unit, $YDiag+$hDiag+2, $XDiag+($i+1)*$unit, $YDiag+$hDiag+3,array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)) );
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+12,$legendJaar[$i],0);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }


    if(is_array($data1) && count($data1)>0)
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        
        if($i>2)
        {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        }
        
         $yval = $yval2;

      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));


  //   $XPage
   // $YPage
  
      $legendaItems=array();  
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
    $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($XPage+$step, $YPage+$h+14, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+14);
    $this->pdf->Cell(0,3,$item);
    $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
  function toonBenchmark()
  {
    $db=new DB();
    $nieuweKop=true;
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];

    

    $query="SELECT
    Fondsen.Fonds,
Portefeuilles.SpecifiekeIndex,
Portefeuilles.Portefeuille,
Fondsen.Omschrijving as fondsOmschrijving
FROM
Portefeuilles
INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";

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
      // 	$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      //  $this->pdf->Rect($this->pdf->marge+$extraX,$this->pdf->GetY(),array_sum($widths)-$extraX, 4, 'F');
        $this->pdf->SetFont($this->pdf->rapport_font,'BU',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('','Benchmark','','%Ytd'));
        //$this->pdf->Line($this->pdf->marge+$extraX,$this->pdf->GetY(),array_sum($widths)+$this->pdf->marge,$this->pdf->GetY());
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