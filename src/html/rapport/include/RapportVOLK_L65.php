<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/11 17:30:27 $
File Versie					: $Revision: 1.17 $

$Log: RapportVOLK_L65.php,v $
Revision 1.17  2020/07/11 17:30:27  rvv
*** empty log message ***

Revision 1.16  2020/04/11 16:33:41  rvv
*** empty log message ***

Revision 1.15  2019/11/16 17:12:28  rvv
*** empty log message ***

Revision 1.14  2019/03/31 12:19:56  rvv
*** empty log message ***

Revision 1.13  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.12  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.11  2018/11/17 17:34:53  rvv
*** empty log message ***

Revision 1.10  2018/11/16 16:41:32  rvv
*** empty log message ***

Revision 1.9  2018/10/24 16:00:59  rvv
*** empty log message ***

Revision 1.8  2018/05/05 19:41:36  rvv
*** empty log message ***

Revision 1.7  2018/02/22 07:45:39  rvv
*** empty log message ***

Revision 1.6  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.5  2017/10/07 17:19:52  rvv
*** empty log message ***

Revision 1.4  2017/09/07 05:49:19  rvv
*** empty log message ***

Revision 1.3  2017/09/06 16:31:28  rvv
*** empty log message ***

Revision 1.2  2016/03/16 14:24:20  rvv
*** empty log message ***

Revision 1.1  2015/11/29 13:13:22  rvv
*** empty log message ***

Revision 1.2  2015/10/04 11:52:21  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:05  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L65
{
	function RapportVOLK_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$rapportagePeriode = date("j",$this->pdf->rapport_datumvanaf)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
			date("Y",$this->pdf->rapport_datumvanaf).
			' '.vertaalTekst('tot en met ',$this->pdf->rapport_taal).' '.
			date("j",$this->pdf->rapport_datum)." ".
			vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
			date("Y",$this->pdf->rapport_datum);

		$this->pdf->rapport_titel = vertaalTekst("Vergelijkend overzicht verslagperiode",$this->pdf->rapport_taal).' '.$rapportagePeriode.' '.vertaalTekst("inclusief geschiktheidsverklaring",$this->pdf->rapport_taal);


    if(!is_array($this->pdf->excelData))
      $this->pdf->excelData=array();
      
    $this->pdf->excelData[]=array("Categorie","Fondsomschrijving","Aantal","Koers","Waarde",
										"Waarde ".$this->pdf->rapportageValuta,"Valuta","Koers","Waarde","Waarde ".$this->pdf->rapportageValuta,"in %",
										"Fonds-\nresultaat","Valuta-\nresultaat","Direct\nresultaat","Rendement in %");

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

    $this->aandeel=1;
    $this->PERFblockTonen=true;

		if(date('d-m',$this->pdf->rapport_datumvanaf)!='01-01')
		{

			if($this->rapportageDatumVanaf<>substr($this->pdf->PortefeuilleStartdatum,0,10))
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
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if($this->aandeel <> 1)
	    $waarde=round($waarde,0);
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

	// type = totaal / subtotaal / tekst
	function printCol($row, $data, $type = "tekst")
	{
		$y = $this->pdf->getY();
		// draw lines
		// calculate positions
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthB[$tel];
		}

		$writerow = $this->pdf->widthB[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		if ($type == 'tekst' && $this->pdf->rapport_layout == 8)
		{
		  $this->pdf->Cell($writerow,4,$data, 0,0, "L");
		}
		else
		{
		  $this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		  $this->pdf->Cell($writerow,4,$data, 0,0, "R");
		}
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "grandtotaal")
			{
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
	}


	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF, $totaalG = 0, $totaalH = 0)
	{
		$hoogte = 16;

		/*
		echo $this->pdf->pagebreak;
		echo "<br>";
		echo $this->pdf->GetY();
		echo "<br>";
		*/
		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

	//title "Subtotaal:",$this->pdf->rapport_taal), 
  //A $categorien[subtotaalbegin],
  //B $categorien[subtotaalactueel],
  //C $subtotaal[percentageVanTotaal], 
  //D $subtotaal[fondsResultaat], 
  //E $subtotaal[valutaResultaat], 
  //F $procentResultaat);
		if( $this->aandeel<>1)
		{
      $totaalD=0;
      $totaalE=0;
      $totaalF=0;
      $totaalG=0;
		}

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
//      $this->pdf->SetX($this->pdf->marge);
//      $this->pdf->Cell(100,4,$title, 0,0, "L");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0) //fondsResultaat
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
	    if($this->toonYtd==true && $totaalE <>0) //valutaResultaat
	    	$this->printCol(14,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalG <>0) //divident
				$this->printCol(12,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($this->toonPeriode==true && $totaalF <>0) //$procentResultaat
				$this->printCol(13,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

	}

	function PERFblock()
	{
		global $__appvar;
		$DB=new DB();

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		else
			$koersQuery = "";

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

		$totaalOpbrengst=0;
		$totaalKosten=0;
		$waardeEind			  	= $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		if($this->toonPeriode==true)
			$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
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


		// het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];

		$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;

		$this->pdf->ln();
		$Yhoogte=5*4+$this->toonPeriode*4+$this->toonYtd*4;

		if($this->pdf->getY()+$Yhoogte>$this->pdf->PageBreakTrigger)
			$this->pdf->addPage();
		$this->pdf->rect($this->pdf->marge,$this->pdf->getY(),105,$Yhoogte);

		$this->pdf->SetWidths(array(80,20,6));
		$this->pdf->SetAligns(array('L','R','L'));


		$this->pdf->SetFont($this->pdf->rapport_font,'bu',$this->pdf->rapport_fontsize);
		$this->pdf->row(array(vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array(vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode+$totaalKosten,0),""));
		$this->pdf->row(array(vertaalTekst("Kosten",$this->pdf->rapport_taal),$this->formatGetal($totaalKosten,0),""));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->Line($this->pdf->marge+80,$this->pdf->GetY() ,$this->pdf->marge+80+20 ,$this->pdf->GetY());
	  $this->pdf->row(array(vertaalTekst("Totaal resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,0),""));
		$this->pdf->Line($this->pdf->marge+80,$this->pdf->GetY() ,$this->pdf->marge+80+20 ,$this->pdf->GetY());
    $this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		if($this->toonPeriode==true)
			$this->pdf->row(array(vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
		if($this->toonYtd==true)
			$this->pdf->row(array(vertaalTekst("Rendement lopende kalenderjaar",$this->pdf->rapport_taal),$this->formatGetal($rendementProcentJaar,2),"%",""));


return '';

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



			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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

		foreach($opbrengstenPerGrootboek as $key=>$value)
		{
			if(round($value,2) != 0.00)
				$this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,0),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,0)));
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		foreach($kostenPerGrootboek as $key=>$value)
		{
			if(round($kostenPerGrootboek[$key]['Bedrag'],2) != 0.00)
				$this->pdf->row(array("",vertaalTekst($kostenPerGrootboek[$key]['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($kostenPerGrootboek[$key]['Bedrag'],0),""));
		}

		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalKosten,0)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];

		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());

			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			//$this->pdf->SetWidths(array(130,80,30,5,30,120););
			$this->pdf->SetWidths(array(80,5,30,10,30,120));

			$this->pdf->row(array(vertaalTekst("Totaal resultaat over verslagperiode",$this->pdf->rapport_taal),"",'','',$this->formatGetal($totaalOpbrengst - $totaalKosten,0),""));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->SetWidths($this->pdf->widthA);
  		$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		//	$this->pdf->Line($posTotaal +2+$extraLengte  ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		$hoogte = 20;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);


		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";
    
    if( $this->aandeel<>1)
    {
      $totaalA=0;
      $totaalD=0;
      $totaalE=0;
      $totaalF=0;
      $totaalG=0;
    }

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			//$this->printCol(3,$title,"tekst");
      $this->pdf->SetX($this->pdf->marge);
      $this->pdf->Cell(100,4,$title, 0,0, "L");
       $this->pdf->SetX($this->pdf->marge);

			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
	    if($this->toonYtd==true && $totaalE <>0)
		    $this->printCol(14,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($totaalG <>0) //divident
				$this->printCol(12,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($this->toonPeriode==true && $totaalF <>0)
				$this->printCol(13,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
	

		$this->pdf->ln();
		return $totaalB;
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

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}
  
  
  function getDividend($fonds,$vanaf,$tot)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro * ".$this->aandeel." as actuelePortefeuilleWaardeEuro,
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
     
     $totaal+=($rente[$tot]-$rente[$vanaf]);
     $totaalCorrected=$totaal;

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$vanaf ."' AND 
     Rekeningmutaties.Boekdatum <= '".	$tot ."' AND
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
      
      if($aantal[$boekdatum] > $aantal[$tot])
      {
        $aandeel=$aantal[$tot]/$aantal[$boekdatum];
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

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$this->pdf->widthB = array(5,54,18,15,20,21,1,16,21,21,15,18,18,20,19);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(   59,18,15,20,21,1,16,21,21,15,18,18,20,19);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');


        if( $this->aandeel<>1)
				{
          $this->pdf->widthB = array(5,84,18,15,10,21,1,16,21,21,15,18,8,10,19);
          $this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
          
          // voor kopjes
          $this->pdf->widthA = array(   89,18,15,10,21,1,16,21,21,15,18,8,10,19);
          $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
          
        }

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
			$tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
		elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
			$tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
		else
			$tweedePerformanceStart = "$RapStartJaar-01-01";
		if(substr($tweedePerformanceStart,5,5)=='01-01')
			$startJaar=true;
		else
			$startJaar=false;
		$tmp=berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatum,$startJaar,'EUR',$tweedePerformanceStart);
		$ytdFonds=array();
		$ytdBeleggingscategorie=array();

		foreach($tmp as $regel)
		{

			$dividend=$this->getDividend($regel['fonds'], $tweedePerformanceStart, $this->rapportageDatum);
			if($regel['type']=='fondsen')
			{
				$ytdFonds[$regel['fonds']] = ($regel['actuelePortefeuilleWaardeEuro'] - $regel['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / $regel['beginPortefeuilleWaardeEuro'];
				$ytdBeleggingscategorie[$regel['beleggingscategorieOmschrijving']]['eind']+=($regel['actuelePortefeuilleWaardeEuro'] - $regel['beginPortefeuilleWaardeEuro'] + $dividend['corrected']);
				$ytdBeleggingscategorie[$regel['beleggingscategorieOmschrijving']]['begin']+=($regel['beginPortefeuilleWaardeEuro']);
			}

		}

		foreach($ytdBeleggingscategorie as $categorie=>$waarden)
		{
			$ytdBeleggingscategorie[$categorie]['rendement']=$waarden['eind']/$waarden['begin'];
		}


		$this->pdf->AddPage();
		if(!isset($this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']))
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;


			$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
	    "
       IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar * ".$this->aandeel."),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel.") as subtotaalbegin,
      ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.beleggingscategorie <> 'Liquiditeiten' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde asc";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

           $procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$totaalresultaatYtd=$ytdBeleggingscategorie[$lastCategorie]['rendement']*100;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalresultaatYtd, $procentResultaat,false,$totaaldividend);

				$totaalbegin = 0;
				$totaalactueel = 0;
                $totaaldividend = 0;
                $totaaldividendCorrected = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if($lastCategorie <> $categorien['Omschrijving'])
					$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");




			// subkop (valuta)
			if($categorien['valuta'] == $this->pdf->rapportageValuta)
			  $beginQuery = 'beginwaardeValutaLopendeJaar';
			else
			  $beginQuery = $this->pdf->ValutaKoersBegin;

			$this->printKop($categorien['valuta'], "b");
				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal * ".$this->aandeel." as totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery * ".$this->aandeel." as beginPortefeuilleWaardeEuro, ".
				//" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel." as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND TijdelijkeRapportage.beleggingscategorie <> 'Liquiditeiten' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
			
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
//echo $subquery."<br><br>";exit;
			while($subdata = $DB2->NextRecord())
			{
			  $dividend=$this->getDividend($subdata['fonds'],$this->rapportageDatumVanaf, $this->rapportageDatum);
        $dividend['totaal']=$dividend['totaal']*$this->aandeel;
        $dividend['corrected']=$dividend['corrected']*$this->aandeel;
       
				//$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;

				$fondsResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] ;//- $fondsResultaat;
				$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;

				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
				if($subdata['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro'] / $totaalWaarde) * 100;


					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

					if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
						$fondsResultaatprocenttxt = "p.m.";
					else
						$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
        $dividendtxt='';

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($dividend['totaal'] <> 0)
					$dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);
          
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving],null,null,null,null,null);
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				if($this->toonYtd==true)
			  	$ytdTxt=$this->formatGetal($ytdFonds[$subdata['fonds']]*100,2);
				else
					$ytdTxt='';

				if($this->toonPeriode==false)
					$procentResultaattxt='';
        
        if( $this->aandeel<>1)
				{
          $this->pdf->row(array("",
                            "",
                            $this->formatAantal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                            '',
                            '',
                            '',
                            "",
                            $this->formatGetal($subdata['actueleFonds'], 2),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            $percentageVanTotaaltxt));
				}
				else
        {
          $this->pdf->row(array("",
                            "",
                            $this->formatAantal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                            $this->formatGetal($subdata['beginwaardeLopendeJaar'], 2),
                            $this->formatGetal($subdata['beginPortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            "",
                            $this->formatGetal($subdata['actueleFonds'], 2),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            $percentageVanTotaaltxt,
                            $fondsResultaattxt,
                            $dividendtxt,
                            $procentResultaattxt,
                            $ytdTxt));
        }
	$this->pdf->excelData[]=array($categorien['Omschrijving'],
													$subdata['fondsOmschrijving'],
													round($subdata['totaalAantal'],2),
													round($subdata['beginwaardeLopendeJaar'],2),
													round($subdata['beginPortefeuilleWaardeInValuta'],2),
													round($subdata['beginPortefeuilleWaardeEuro'],2),
													$categorien['valuta'],
													round($subdata['actueleFonds'],2),
													round($subdata['actuelePortefeuilleWaardeInValuta'],2),
													round($subdata['actuelePortefeuilleWaardeEuro'],2),
													round($percentageVanTotaal,2),
													round($fondsResultaat,2),
													round($valutaResultaat,2),
                          round($dividend['totaal'],2),
                          round($procentResultaat,2));	

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
                $subtotaal['totaalDividend'] += $dividend['totaal'];
                $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];

			}

			// print categorie footers
				$procentResultaat = (($categorien['subtotaalactueel']  - $categorien['subtotaalbegin'] + $subtotaal['totaalDividendCorrected'] ) / ($categorien['subtotaalbegin']  /100));
				if($categorien['subtotaalbegin'] < 0)
					$procentResultaat = -1 * $procentResultaat;


	//				$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien['subtotaalbegin'],$categorien['subtotaalactueel'],$subtotaal['percentageVanTotaal'], $subtotaal['fondsResultaat'], $subtotaal['valutaResultaat'], $procentResultaat,$subtotaal['totaalDividend']);


	

			// totaal op categorie tellen
			$totaalbegin   += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];
      
			$totaalfondsresultaat  += $subtotaal['fondsResultaat'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];
      $totaaldividend        += $subtotaal['totaalDividend'];
      $totaaldividendCorrected        += $subtotaal['totaalDividendCorrected'];

			$lastCategorie = $categorien['Omschrijving'];

			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];
      $grandtotaaldividend  += $subtotaal['totaalDividend'];
      $grandtotaaldividendCorrected  += $subtotaal['totaalDividendCorrected'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;


		$totaalresultaatYtd=$ytdBeleggingscategorie[$lastCategorie]['rendement']*100;


		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalresultaatYtd,$procentResultaat,$totaaldividend);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) * ".$this->aandeel." as subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel." as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde ";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");
			$totaalRenteInValuta = 0 ;
      while($categorien = $DB->NextRecord())
			{
				$totaalRenteInValuta += $categorien['subtotaalactueel'];
			}
      // totaal op rente
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
     
      $this->pdf->excelData[]=array('Opgelopen rente','Opgelopen rente',"","","","","","",'',round($totaalRenteInValuta,2),round($subtotaalPercentageVanTotaal,2));	
    
    }

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage 
            LEFT JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" (TijdelijkeRapportage.type = 'rekening' OR (TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.beleggingscategorie = 'Liquiditeiten' )) ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
				$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");

			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}


			foreach($liqiteitenBuffer as $data)
			{
        if($this->aandeel <> 1)
				{
          $omschrijving='Cash positie';
				}
				else
        {
          $omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
          $omschrijving = vertaalTekst(str_replace("{Rekening}", $data[rekening], $omschrijving), $this->pdf->rapport_taal);
          $omschrijving = str_replace("{Tenaamstelling}", vertaalTekst($data[fondsOmschrijving], $this->pdf->rapport_taal), $omschrijving);
          $omschrijving = vertaalTekst(str_replace("{Valuta}", $data[valuta], $omschrijving), $this->pdf->rapport_taal);
        }

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];
				$subtotaalPercentageVanTotaal  = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
				$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt));
      $this->pdf->excelData[]=array('Liquiditeiten',$omschrijving,"","","","","","",
													round($data['actuelePortefeuilleWaardeInValuta'],2),
													round($data['actuelePortefeuilleWaardeEuro'],2),
													round($subtotaalPercentageVanTotaal,2));	
  		}

			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
			$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,$subtotaalPercentageVanTotaal,"","");
		} // einde liquide

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true,$grandtotaaldividend);
	

		$this->pdf->ln();
		if($this->PERFblockTonen==true)
		  $this->PERFblock();

		$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);

		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
    $this->pdf->SetXY(8, 180);
    $this->pdf->MultiCell(297-16,4,vertaalTekst('* Koersresultaat beslaat uitsluitend het ongerealiseerd koersresultaat.',$this->pdf->rapport_taal),0,'L');

    if($this->pdf->getY() > 185)
			$this->pdf->addPage();
		$this->pdf->setY(185);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize-2);
		$this->pdf->MultiCell(297-16,4,vertaalTekst('Geschiktheidsverklaring',$this->pdf->rapport_taal));
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
		$txt=vertaalTekst("Hierdoor bevestigen wij dat uw beleggingsportefeuille in overeenstemming is met de vastlegging van uw beleggingsdoelstelling, beleggingshorizon, kennis- en ervaringsniveau, risicobereidheid en verliescapaciteit (uw cliëntprofiel).",$this->pdf->rapport_taal);
		$this->pdf->MultiCell(297-16,4,$txt);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

	}
}
?>
