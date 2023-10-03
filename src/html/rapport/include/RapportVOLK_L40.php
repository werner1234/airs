<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/29 06:28:02 $
File Versie					: $Revision: 1.2 $

$Log: RapportVOLK_L40.php,v $
Revision 1.2  2017/05/29 06:28:02  rvv
*** empty log message ***

Revision 1.1  2016/10/09 14:45:08  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L40
{
	function RapportVOLK_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
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

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
		$this->pdf->excelData[]=array('Categorie','Valuta', 'ISIN','Fondsomschrijving','Aantal','Per stuk in valuta','Portefeuille in Valuta','Portefeuille in EUR','Per stuk in valuta','Portefeuille in valuta','Portefeuille in EUR',
			'aandeel op categorie','aandeel op totaal','Fonds resultaat','valuta resultaat','in %');

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


	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF, $TotaalG = 0, $totaalH = 0)
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

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

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

		// lege regel
		if($this->pdf->rapport_layout != 8)
			$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";


			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
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

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		if ($this->pdf->rapport_layout == 8 || $this->pdf->rapport_layout == 5)
		{
			$fondsresultwidth = 15;
			$omschrijvingExtra = 0;
		}
		else
		{
			$fondsresultwidth = 5;
			$omschrijvingExtra = 10;
		}


		$this->pdf->widthB = array(10, 50 + $omschrijvingExtra, 18, 15, 21, 21, 1, 15, 21, 21, 15, 22, $fondsresultwidth, 22, 15);
		$this->pdf->alignB = array('R', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');

		// voor kopjes
		$this->pdf->widthA = array(60 + $omschrijvingExtra, 18, 15, 21, 21, 1, 15, 21, 21, 15, 22, $fondsresultwidth, 22, 15);
		$this->pdf->alignA = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');

		$this->pdf->AddPage();

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
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, " .
			" TijdelijkeRapportage.valuta, " .
			" TijdelijkeRapportage.beleggingscategorie, " .
			"
       IF (TijdelijkeRapportage.valuta = '" . $this->pdf->rapportageValuta . "',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersBegin . ") as subtotaalbegin,
      " .
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " AS subtotaalactueel FROM " .
			" TijdelijkeRapportage " .
			" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "'"
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta " .
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde asc";

		
		debugSpecial($query, __FILE__, __LINE__);
		$DB = new DB();
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();

		while ($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if ($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie))
			{
				$title = vertaalTekst("Subtotaal", $this->pdf->rapport_taal) . " " . vertaalTekst($lastCategorie, $this->pdf->rapport_taal);

				$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin / 100));
				if ($totaalbegin < 0)
				{
					$procentResultaat = -1 * $procentResultaat;
				}

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage, $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat);

				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if ($lastCategorie <> $categorien[Omschrijving])
			{

				$this->printKop(vertaalTekst($categorien[Omschrijving], $this->pdf->rapport_taal), "bi");

			}
			// subkop (valuta)
			if ($categorien['valuta'] == $this->pdf->rapportageValuta)
			{
				$beginQuery = 'beginwaardeValutaLopendeJaar';
			}
			else
			{
				$beginQuery = $this->pdf->ValutaKoersBegin;
			}


			$DB2 = new DB();
			$subquery = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersEind . ") as actuelePortefeuilleWaardeEuro 
				 FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
				" TijdelijkeRapportage.beleggingscategorie =  '" . $categorien[beleggingscategorie] . "' AND " .
				" TijdelijkeRapportage.type =  'fondsen' AND " .
				" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
				. $__appvar['TijdelijkeRapportageMaakUniek'];//exit;
			$DB2->SQL($subquery);
			$DB2->Query();
			$catWaarde = $DB2->NextRecord();

			$this->printKop(vertaalTekst("Waarden", $this->pdf->rapport_taal) . " " . $categorien[valuta], "");
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving,if(Fondsen.OptieBovenliggendFonds<>'',Fondsen.OptieBovenliggendFonds,TijdelijkeRapportage.fonds)as volgorde , " .
				" TijdelijkeRapportage.fonds, Fondsen.ISINCode, " .
				" TijdelijkeRapportage.actueleValuta, " .
				" TijdelijkeRapportage.totaalAantal, " .
				" TijdelijkeRapportage.beginwaardeLopendeJaar , " .
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, " .
				" TijdelijkeRapportage.Valuta, " .
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro, " .
				//" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersEind . " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille " .
				" FROM TijdelijkeRapportage JOIN Fondsen ON TijdelijkeRapportage.fonds=Fondsen.Fonds WHERE " .
				" TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
				" TijdelijkeRapportage.beleggingscategorie =  '" . $categorien[beleggingscategorie] . "' AND " .
				" TijdelijkeRapportage.valuta =  '" . $categorien[valuta] . "' AND " .
				" TijdelijkeRapportage.type =  'fondsen' AND " .
				" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
				. $__appvar['TijdelijkeRapportageMaakUniek'] .
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, volgorde,TijdelijkeRapportage.fondsPaar asc, Fondsen.OptieBovenliggendFonds, TijdelijkeRapportage.fondsOmschrijving asc";

			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery, __FILE__, __LINE__);

			$DB2->SQL($subquery);
			$DB2->Query();
//echo $subquery."<br><br>";exit;
			while ($subdata = $DB2->NextRecord())
			{
				$fondsResultaat = ($subdata[actuelePortefeuilleWaardeInValuta] - $subdata[beginPortefeuilleWaardeInValuta]) * $subdata[actueleValuta] / $this->pdf->ValutaKoersEind;

				$fondsResultaatprocent = ($fondsResultaat / $subdata[beginPortefeuilleWaardeEuro]) * 100;
				$valutaResultaat = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro] - $fondsResultaat;

				$procentResultaat = (($subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro]) / ($subdata[beginPortefeuilleWaardeEuro] / 100));
				if ($subdata[beginPortefeuilleWaardeEuro] < 0)
				{
					$procentResultaat = -1 * $procentResultaat;
				}

				$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde / 100);
				$percentageVanCategorie = ($subdata[actuelePortefeuilleWaardeEuro]) / ($catWaarde['actuelePortefeuilleWaardeEuro'] / 100);


				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, $this->pdf->rapport_VOLK_decimaal_proc) . " %";

				if ($procentResultaat > 1000 || $procentResultaat < -1000)
				{
					$procentResultaattxt = "p.m.";
				}
				else
				{
					$procentResultaattxt = $this->formatGetal($procentResultaat, $this->pdf->rapport_VOLK_decimaal_proc);
				}


				$fondsResultaattxt = "";
				$valutaResultaattxt = "";

				if ($fondsResultaat <> 0)
				{
					$fondsResultaattxt = $this->formatGetal($fondsResultaat, $this->pdf->rapport_VOLK_decimaal);
				}

				if ($valutaResultaat <> 0)
				{
					$valutaResultaattxt = $this->formatGetal($valutaResultaat, $this->pdf->rapport_VOLK_decimaal);
				}

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r], $this->pdf->rapport_fonds_fontcolor[g], $this->pdf->rapport_fonds_fontcolor[b]);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0], 4, "");


				$this->pdf->Cell($this->pdf->widthB[1], 4, $subdata[fondsOmschrijving], null, null, null, null, null);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r], $this->pdf->rapport_fontcolor[g], $this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


				$this->pdf->row(array("",
													"",
													$this->formatAantal($subdata[totaalAantal], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
													$this->formatGetal($subdata[beginwaardeLopendeJaar], 2),
													$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta], $this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata[beginPortefeuilleWaardeEuro], $this->pdf->rapport_VOLK_decimaal),
													"",
													$this->formatGetal($subdata[actueleFonds], 2),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta], $this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro], $this->pdf->rapport_VOLK_decimaal),
													$percentageVanTotaaltxt,
													$fondsResultaattxt,
													$fondsResultaatprocenttxt,
													$valutaResultaattxt,
													$procentResultaattxt)
				);
				$this->pdf->excelData[] = array($categorien['Omschrijving'], $subdata['Valuta'], $subdata['ISINCode'],
					$subdata['fondsOmschrijving'],
					round($subdata['totaalAantal'], 6),
					round($subdata['beginwaardeLopendeJaar'], 2),
					round($subdata['beginPortefeuilleWaardeInValuta'], 2),
					round($subdata['beginPortefeuilleWaardeEuro'], 2),
					round($subdata['actueleFonds'], 2),
					round($subdata['actuelePortefeuilleWaardeInValuta'], 2),
					round($subdata['actuelePortefeuilleWaardeEuro'], 2),
					round($percentageVanCategorie, 2),
					round($percentageVanTotaal, 2),
					round($fondsResultaat, 2),
					round($valutaResultaat, 2),
					round($procentResultaat, 2));

				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];

				$subtotaal[percentageVanTotaal] += $percentageVanTotaal;
				$subtotaal[fondsResultaat] += $fondsResultaat;
				$subtotaal[valutaResultaat] += $valutaResultaat;
				$subtotaal['totaalResultaat'] += $subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;

			}


			$this->printSubTotaal(vertaalTekst("Subtotaal:", $this->pdf->rapport_taal), $categorien[subtotaalbegin], $categorien[subtotaalactueel], $subtotaal[percentageVanTotaal], $subtotaal[fondsResultaat], $subtotaal[valutaResultaat], $procentResultaat);


			// totaal op categorie tellen
			$totaalbegin += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];

			$totaalfondsresultaat += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage += $subtotaal[percentageVanTotaal];

			$lastCategorie = $categorien[Omschrijving];

			$grandtotaalvaluta += $subtotaal[valutaResultaat];
			$grandtotaalfonds += $subtotaal[fondsResultaat];

			$totaalResultaat += $subtotaal['totaalResultaat'];
			$totaalBijdrage += $subtotaal['totaalBijdrage'];
			$grandtotaalResultaat += $subtotaal['totaalResultaat'];
			$grandtotaalBijdrage += $subtotaal['totaalBijdrage'];

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin / 100));
		if ($totaalbegin < 0)
		{
			$procentResultaat = -1 * $procentResultaat;
		}


		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal", $this->pdf->rapport_taal) . " " . vertaalTekst($lastCategorie, $this->pdf->rapport_taal), $totaalbegin, $totaalactueel, $totaalpercentage, $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat);

		$query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " as subtotaalactueel 
		   FROM TijdelijkeRapportage WHERE
		   TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
			" TijdelijkeRapportage.type = 'rente'  AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'];
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$catWaarde = $DB->NextRecord();
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, " .
			" TijdelijkeRapportage.beleggingscategorie, " .
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, " .
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersBegin . " as subtotaalbegin, " .
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " as subtotaalactueel FROM " .
			" TijdelijkeRapportage " .
			" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
			" TijdelijkeRapportage.type = 'rente'  AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" GROUP BY TijdelijkeRapportage.valuta " .
			" ORDER BY TijdelijkeRapportage.valutaVolgorde ";
		debugSpecial($query, __FILE__, __LINE__);


		$DB->SQL($query);
		$DB->Query();

		if ($DB->records() > 0)
		{
			$this->printKop(vertaalTekst("Opgelopen Rente", $this->pdf->rapport_taal), "bi");
		}
		$totaalRenteInValuta = 0;
		while ($categorien = $DB->NextRecord())
		{

			$subtotaalRenteInValuta = 0;
			$subtotaalPercentageVanTotaal = 0;

			$this->printKop(vertaalTekst("Waarden", $this->pdf->rapport_taal) . " " . $categorien[valuta], "");

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, " .
				" TijdelijkeRapportage.actueleValuta , " .
				" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, " .
				" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersEind . " as actuelePortefeuilleWaardeEuro, " .
				" TijdelijkeRapportage.rentedatum, " .
				" TijdelijkeRapportage.renteperiode, " .
				" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille " .
				" FROM TijdelijkeRapportage WHERE " .
				" TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
				" TijdelijkeRapportage.type = 'rente'  AND " .
				" TijdelijkeRapportage.valuta =  '" . $categorien[valuta] . "'" .
				" AND TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
				. $__appvar['TijdelijkeRapportageMaakUniek'] .
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery, __FILE__, __LINE__);

			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
			while ($subdata = $DB2->NextRecord())
			{

				if ($this->pdf->rapport_HSE_rentePeriode)
				{
					$rentePeriodetxt = "  " . date("d-m", db2jul($subdata[rentedatum]));
					if ($subdata[renteperiode] <> 12 && $subdata[renteperiode] <> 0)
					{
						$rentePeriodetxt .= " / " . $subdata[renteperiode];
					}
				}

				$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde / 100);
				$percentageVanCategorie = ($subdata[actuelePortefeuilleWaardeEuro]) / ($catWaarde['subtotaalactueel'] / 100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, $this->pdf->rapport_VOLK_decimaal_proc) . " %";

				$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r], $this->pdf->rapport_fonds_fontcolor[g], $this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0], 4, "");
				$this->pdf->Cell($this->pdf->widthB[1], 4, $subdata[fondsOmschrijving] . $rentePeriodetxt);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r], $this->pdf->rapport_fontcolor[g], $this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


				$this->pdf->row(array("", "", "", "", "", "", "", "",
													$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
													$percentageVanTotaaltxt));

				$this->pdf->excelData[] = array("Opgelopen Rente", $subdata['valuta'], '',$subdata[fondsOmschrijving] . $rentePeriodetxt,"","","","","",
					round($subdata['actuelePortefeuilleWaardeInValuta'], 2),
					round($subdata['actuelePortefeuilleWaardeEuro'], 2),
					round($percentageVanCategorie, 2),
					round($percentageVanTotaal, 2));

				// print subtotaal
				//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
				$subtotaalPercentageVanTotaal = ($subtotaalRenteInValuta) / ($totaalWaarde / 100);
			}
			$this->printSubTotaal(vertaalTekst("Subtotaal:", $this->pdf->rapport_taal), "", $subtotaalRenteInValuta, $subtotaalPercentageVanTotaal, "", "");
    	$totaalRenteInValuta += $subtotaalRenteInValuta;
		}
		$actueleWaardePortefeuille 	+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() > 0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");
			$liquiditeitenTotaal=0;
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
				$liquiditeitenTotaal+=$data['actuelePortefeuilleWaardeEuro'];
			}

					foreach($liqiteitenBuffer as $data)
					{
						$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
						$omschrijving = vertaalTekst(str_replace("{Rekening}", $data[rekening], $omschrijving), $this->pdf->rapport_taal);
						$omschrijving = str_replace("{Tenaamstelling}", vertaalTekst($data[fondsOmschrijving], $this->pdf->rapport_taal), $omschrijving);
						$omschrijving = vertaalTekst(str_replace("{Valuta}", $data[valuta], $omschrijving), $this->pdf->rapport_taal);

						$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];
						$subtotaalPercentageVanTotaal = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde / 100);
						$subtotaalPercentageVanCategorie = ($data[actuelePortefeuilleWaardeEuro]) / ($liquiditeitenTotaal / 100);
						$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal, $this->pdf->rapport_VOLK_decimaal_proc) . " %";

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r], $this->pdf->rapport_fonds_fontcolor[g], $this->pdf->rapport_fonds_fontcolor[b]);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0], 4, "");
						$this->pdf->Cell($this->pdf->widthB[1], 4, $omschrijving);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r], $this->pdf->rapport_fontcolor[g], $this->pdf->rapport_fontcolor[b]);
						$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

						$this->pdf->row(array("",
															"",
															"",
															"",
															"",
															"",
															"",
															"",
															$this->formatGetal($data[actuelePortefeuilleWaardeInValuta], $this->pdf->rapport_VOLK_decimaal),
															$this->formatGetal($data[actuelePortefeuilleWaardeEuro], $this->pdf->rapport_VOLK_decimaal),
															$subtotaalPercentageVanTotaaltxt));

						$this->pdf->excelData[] = array("Liquiditeiten", $data['valuta'], '',$omschrijving,"","","","","",
							round($data['actuelePortefeuilleWaardeInValuta'], 2),
							round($data['actuelePortefeuilleWaardeEuro'], 2),
							round($subtotaalPercentageVanCategorie, 2),
							round($subtotaalPercentageVanTotaal, 2));

					}


			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);

			// totaal liquiditeiten
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
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true);
		$this->pdf->ln();

		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}


		if($this->pdf->rapport_VOLK_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		elseif ($this->pdf->rapport_VOLK_rendement == 2)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;

//		if($this->pdf->rapport_layout == 8)
//		  include_once('indexGrafiek.php');
		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
	}
}
?>
