<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/06/27 16:13:50 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIH_L13.php,v $
Revision 1.4  2018/06/27 16:13:50  rvv
*** empty log message ***

Revision 1.3  2018/06/23 14:21:39  rvv
*** empty log message ***

Revision 1.2  2018/06/13 15:54:31  rvv
*** empty log message ***

Revision 1.1  2018/06/11 05:14:31  rvv
*** empty log message ***

Revision 1.13  2014/03/16 11:17:35  rvv
*** empty log message ***

Revision 1.12  2014/02/22 18:43:38  rvv
*** empty log message ***

Revision 1.11  2014/02/05 16:02:14  rvv
*** empty log message ***

Revision 1.10  2014/01/15 15:03:06  rvv
*** empty log message ***

Revision 1.9  2014/01/15 14:21:32  rvv
*** empty log message ***

Revision 1.8  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.7  2012/04/08 08:13:18  rvv
*** empty log message ***

Revision 1.6  2012/04/04 16:08:40  rvv
*** empty log message ***

Revision 1.5  2012/04/01 07:40:26  rvv
*** empty log message ***

Revision 1.4  2011/11/16 19:22:09  rvv
*** empty log message ***

Revision 1.3  2011/10/02 08:37:20  rvv
*** empty log message ***

Revision 1.2  2011/07/03 06:42:47  rvv
*** empty log message ***

Revision 1.1  2011/01/29 15:57:33  rvv
*** empty log message ***

Revision 1.1  2010/11/14 10:46:23  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIH_L13
{
	function RapportOIH_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIS_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingssector";

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

/*
	function printSubTotaal($title, $totaalA, $totaalB)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4]+ $this->pdf->widthB[5]+ $this->pdf->widthB[6];

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[7],$this->pdf->GetY());


		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_OIS_decimaal);

		$this->pdf->SetX($actueel-$this->pdf->widthB[6]);
		$this->pdf->Cell($this->pdf->widthB[6],4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[7],4,$totaalBtxt, 0,1, "R");
		$this->pdf->ln();
	}
*/

	function printTotaal($title, $totaalA, $totaalB, $procent, $grandtotaal = false)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		// lege regel
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] +
			$this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7];

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_OIS_decimaal);
    else
      return 0;  

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[8],$this->pdf->GetY());

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($actueel,4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		//$this->pdf->Cell($this->pdf->widthB[6],4, "", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[8],4,$totaalBtxt, 0,0, "R");

		$this->pdf->Cell($this->pdf->widthB[9],4,$procent, 0,1, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[8],$this->pdf->GetY());
			$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[8],$this->pdf->GetY()+1);
		}
		else
		{
			$this->pdf->setDash(1,1);
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[8],$this->pdf->GetY());
			$this->pdf->setDash();
		}

		//$this->pdf->ln();

		return $totaalB;
	}

	function printKop($title, $procent, $type="default")
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);

		if($this->pdf->rapport_layout == 13)
		 $afronding=1;
		else
		 $afronding=0;

		$procenttxt = $this->formatGetal($procent,$afronding)." %";



		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell($this->pdf->widthB[0],4, $procenttxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[1],4, $title, 0,1, "L");
	}

	function writeRapport()
	{
		// voor data
		$this->pdf->widthB = array(15, 70, 15,85, 25, 25, 20, 1, 25, 0);
		$this->pdf->alignB = array('R', 'L','R', 'L', 'R', 'R', 'L', 'R', 'R', 'R');

		// voor kopjes
		$this->pdf->widthA = array(15, 70, 15,85, 25, 25, 20, 1, 25, 0);
		$this->pdf->alignA = array('R', 'L','R', 'L', 'R', 'R', 'L', 'R', 'R', 'R');


		// printIndex($this);
//		$this->ToonGrafieken();
		$this->ToonOIH();

	}

	function ToonOIH()
	{
		global $__appvar;

		$this->pdf->AddPage();

		if ($this->pdf->rapport_layout == 13)
		{
			$afronding = 1;
		}
		else
		{
			$afronding = 0;
		}


		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
			"FROM TijdelijkeRapportage WHERE " .
			" rapportageDatum ='" . $this->rapportageDatum . "' AND " .
			" portefeuille = '" . $this->portefeuille . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query, __FILE__, __LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];


		$actueleWaardePortefeuille = 0;

		$query = "SELECT Beleggingscategorien.Omschrijving, Beleggingssectoren.Omschrijving AS secOmschrijving , " .
			" TijdelijkeRapportage.beleggingssector, " .
			" TijdelijkeRapportage.valuta, " .
			" TijdelijkeRapportage.beleggingscategorie, " .
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin, " .
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel " .
			" FROM (TijdelijkeRapportage, Valutas) " .
			" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) " .
			" LEFT JOIN Beleggingssectoren on (TijdelijkeRapportage.beleggingssector = Beleggingssectoren.Beleggingssector) " .
			" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
			" TijdelijkeRapportage.valuta = Valutas.Valuta AND " .
			" TijdelijkeRapportage.type = 'fondsen' AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "'"
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" GROUP BY TijdelijkeRapportage.beleggingscategorie  " .
			" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Beleggingssectoren.Afdrukvolgorde asc";
		debugSpecial($query, __FILE__, __LINE__);
		$DB = new DB();
		$DB->SQL($query); //echo "catt $query<br>\n";exit;
		$DB->Query();

		while ($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			// print totaal op hele categorie.

			if ($lastCategorie2 <> $categorien['Omschrijving'] && !empty($lastCategorie2))
			{
				$title = vertaalTekst("Subtotaal", $this->pdf->rapport_taal);
				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel);
				$percentageVanTotaal_totaal = 0;
				$totaalbegin = 0;
				$totaalactueel = 0;
			}

			if ($lastCategorie2 <> $categorien['Omschrijving'])
			{
				$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde / 100);
				$this->printKop(vertaalTekst($categorien['Omschrijving'], $this->pdf->rapport_taal), $percentageVanTotaal, "bi");
				$secTel = 0;
			}
			// subkop (valuta)
			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, " .
				" TijdelijkeRapportage.actueleValuta, " .
				" TijdelijkeRapportage.beleggingssector, " .
				" Beleggingssectoren.Omschrijving AS secOmschrijving, " .
				" TijdelijkeRapportage.totaalAantal, " .
				" TijdelijkeRapportage.beginwaardeLopendeJaar, " .
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, " .
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, " .
				" TijdelijkeRapportage.actueleFonds, " .
				" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, " .
				" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, " .
				" TijdelijkeRapportage.beleggingscategorie, " .
				" TijdelijkeRapportage.valuta, " .
				" TijdelijkeRapportage.fonds, " .
				" TijdelijkeRapportage.portefeuille " .
				" FROM TijdelijkeRapportage " .
				" LEFT JOIN Beleggingssectoren on (TijdelijkeRapportage.beleggingssector = Beleggingssectoren.Beleggingssector) " .
				" WHERE " .
				" TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
				" TijdelijkeRapportage.beleggingscategorie =  '" . $categorien['beleggingscategorie'] . "' AND " .
				" TijdelijkeRapportage.type =  'fondsen' AND " .
				" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
				. $__appvar['TijdelijkeRapportageMaakUniek'] .
				" ORDER BY Beleggingssectoren.Afdrukvolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

			debugSpecial($subquery, __FILE__, __LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery); //echo "subq $subquery <br>\n<br>\n";
			$DB2->Query();

			$lastCategorie = "xx";
			$secTel = 0;
			while ($subdata = $DB2->NextRecord())
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				if ($lastCategorie <> $subdata['secOmschrijving'])
				{
					// selecteer sum van deze sector... en dan :

					$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS sectortotaal FROM TijdelijkeRapportage " .
						" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
						" TijdelijkeRapportage.beleggingscategorie =  '" . $subdata['beleggingscategorie'] . "' AND " .
						" TijdelijkeRapportage.beleggingssector =  '" . $subdata['beleggingssector'] . "' AND " .
						" TijdelijkeRapportage.type =  'fondsen' AND " .
						" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
						. $__appvar['TijdelijkeRapportageMaakUniek'];
					debugSpecial($q, __FILE__, __LINE__);
					$DB3 = new DB();
					$DB3->SQL($q); //echo "q $q <br>\n";
					$DB3->Query();
					$subtotaal = $DB3->nextRecord();
					$subtotaal = $subtotaal['sectortotaal'];


					$percentageVanTotaal = round($subtotaal / ($totaalWaarde/ 100), 1);
					//echo $categorien[Omschrijving]. " ".$percentageVanTotaal."<br>\n";
						$secPercentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, $afronding) . " %";

					$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
					$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
					$this->pdf->Cell($this->pdf->widthB[0], 4, $secPercentageVanTotaaltxt, 0, 0, "R");
					$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
					$this->pdf->Cell($this->pdf->widthB[1], 4, $subdata['secOmschrijving'], 0, 0, "L");
					$this->pdf->SetX($this->pdf->marge);
				}


					$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde / 100);
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, 1).' %';


				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

//$this->formatGetal(($subdata['actuelePortefeuilleWaardeEuro'] / $categorien['subtotaalactueel']) * 100, $afronding)
				$this->pdf->row(array('',
													"",$percentageVanTotaaltxt,
													$subdata['fondsOmschrijving'],
													$this->formatAantal($subdata['totaalAantal'], 0, $this->pdf->rapport_OIS_aantalVierDecimaal),
													$this->formatGetal($subdata['actueleFonds'], 2),
													$subdata['valuta'],
													"",
													$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_OIS_decimaal)));

				$percentageVanTotaal_totaal += $percentageVanTotaal;

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$lastCategorie = $subdata['secOmschrijving'];
			}

			// print categorie footers
			//$this->printSubTotaal("Subtotaal:", $categorien[subtotaalbegin], $categorien[subtotaalactueel]);

			// totaal op categorie tellen
			$totaalbegin += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$lastCategorie2 = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie
		//$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$percentageVanTotaal_totaal);

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal", $this->pdf->rapport_taal), $totaalbegin, $totaalactueel);


		$percentageVanTotaal_totaal = 0;

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, " .
			" TijdelijkeRapportage.beleggingscategorie, " .
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, " .
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, " .
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM " .
			" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) " .
			" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
			" TijdelijkeRapportage.type = 'rente'  AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" GROUP BY TijdelijkeRapportage.beleggingscategorie " .
			" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query, __FILE__, __LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if ($DB->records() > 0)
		{

			$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS rentetotaal FROM TijdelijkeRapportage " .
				" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
				" TijdelijkeRapportage.type = 'rente'  AND " .
				" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($q, __FILE__, __LINE__);
			$DB3 = new DB();
			$DB3->SQL($q);
			$DB3->Query();
			$subtotaal = $DB3->nextRecord();
			$subtotaal = $subtotaal['rentetotaal'];


			$percentageVanTotaal = $subtotaal / ($totaalWaarde / 100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, $afronding) . " %";


			//$percentageVanTotaal = $categorien[subtotaalactueel]/ ($totaalWaarde/100);
			$this->printKop(vertaalTekst("Opgelopen rente", $this->pdf->rapport_taal), $percentageVanTotaal, "bi");

			$totaalRenteInValuta = 0;

			while ($categorien = $DB->NextRecord())
			{
				if (!$this->pdf->rapport_OIS_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, " .
						" TijdelijkeRapportage.actueleValuta , " .
						" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, " .
						" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, " .
						" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille " .
						" FROM TijdelijkeRapportage WHERE " .
						" TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
						" TijdelijkeRapportage.type = 'rente'  AND " .
						" TijdelijkeRapportage.beleggingscategorie = '" . $categorien['beleggingscategorie'] . "' AND " .
						" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
						. $__appvar['TijdelijkeRapportageMaakUniek'] .
						" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery, __FILE__, __LINE__);
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while ($subdata = $DB2->NextRecord())
					{
						if ($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
						{
							$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde / 100);
							$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, 1);
						}
						else
						{
							$percentageVanTotaaltxt = "";
						}

						$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];
						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
						$this->pdf->row(array("", "", "",$subdata['fondsOmschrijving'],
															"",


															$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_OIS_decimaal),
															$subdata['valuta'],
															"",
															$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_OIS_decimaal),
															$percentageVanTotaaltxt));
					}

					// print subtotaal
					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
				}
			}

			// totaal op rente
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal", $this->pdf->rapport_taal), "",
																											 $totaalRenteInValuta);
		}

		// Liquiditeiten
		$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS liqtotaal FROM TijdelijkeRapportage " .
			" WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
			" TijdelijkeRapportage.type = 'rekening'  AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($q, __FILE__, __LINE__);
		$DB3 = new DB();
		$DB3->SQL($q);
		$DB3->Query();
		$subtotaal = $DB3->nextRecord();
		$subtotaal = $subtotaal['liqtotaal'];


		$percentageVanTotaal = $subtotaal / ($totaalWaarde / 100);
		$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, 0) . " %";


		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, " .
			" TijdelijkeRapportage.actueleValuta , " .
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, " .
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, " .
			" TijdelijkeRapportage.rekening, " .
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille " .
			" FROM TijdelijkeRapportage WHERE " .
			" TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
			" TijdelijkeRapportage.type = 'rekening'  " .
			" AND TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" ORDER BY SUBSTR(TijdelijkeRapportage.rekening,1,LENGTH(TijdelijkeRapportage.rekening)-3), TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.fondsOmschrijving";
		debugSpecial($query, __FILE__, __LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		if ($DB1->records() > 0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten", $this->pdf->rapport_taal), $percentageVanTotaal, "bi");

			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
			while ($data = $DB1->NextRecord())
			{
				if ($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
				{
					$percentageVanTotaal = $data['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde / 100);
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, 1);
				}
				else
				{
					$percentageVanTotaaltxt = "";
				}

				if ($this->pdf->rapport_OIS_liquiditeiten_omschr)
				{
					$this->pdf->rapport_liquiditeiten_omschr = $this->pdf->rapport_OIS_liquiditeiten_omschr;
				}

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}", $data['rekening'], $omschrijving), $this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}", vertaalTekst($data['fondsOmschrijving'], $this->pdf->rapport_taal), $omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}", $data['valuta'], $omschrijving), $this->pdf->rapport_taal);
        $omschrijving = vertaalTekst(str_replace("{PortefeuilleVoorzet}", $this->pdf->rapport_portefeuilleVoorzet, $omschrijving), $this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
													"",	"",

													$omschrijving,
													"",
													$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_OIS_decimaal),
													$data['valuta'],
													"",
													$this->formatGetalKoers($data['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_OIS_decimaal),
													$percentageVanTotaaltxt));

			}
			// totaal liquiditeiten
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal", $this->pdf->rapport_taal), "", $totaalLiquiditeitenEuro);
		}

		// check op totaalwaarde!
		if (round(($totaalWaarde - $actueleWaardePortefeuille), 2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport " . $this->portefeuille . ", totale waarde (" . round($totaalWaarde, 2) . ") komt niet overeen met afgedrukte totaal (" . round($actueleWaardePortefeuille, 2) . ") in rapport " . $this->pdf->rapport_type . "');
			</script>";
			ob_flush();
		}
		$actueleWaardePortefeuille = $totaalWaarde;
		// print grandtotaal

		if ($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
		{
			$totaalTxt = $this->formatGetal(100, 1);
		}
		else
		{
			$totaalTxt = "";
		}

		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille", $this->pdf->rapport_taal), "", $actueleWaardePortefeuille, $totaalTxt, true);

		$this->pdf->ln();


//		if ($this->pdf->rapport_OIS_valutaoverzicht == 1)
//		{
//			$this->pdf->ln();
//			// in PDFRapport.php
//			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
//		}
//		elseif ($this->pdf->rapport_OIS_valutaoverzicht == 2)
//		{
//			$this->pdf->ln();
//			// in PDFRapport.php
//			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
//		}
//
//
//		if ($this->pdf->rapport_OIS_rendement == 1)
//		{
//			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
//		}
	}


	function ToonGrafieken()
	{

$this->pdf->addPage();

		global $__appvar;
		$DB=new DB();
		$rapportageDatum = $this->rapportageDatum;
		$portefeuille = $this->portefeuille;



		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$rapportageDatum."' AND ".
			" portefeuille = '".$portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM
			TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND
			TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND
 			TijdelijkeRapportage.Type = 'rekening'
			" .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalLiquiditeiten = $DB->nextRecord();
		$totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];

		//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
		$allekleuren['OIS2'] = $allekleuren['OIS'];

		$this->pdf->rapport_GRAFIEK_sortering = $kleuren['grafiek_sortering'];

		if ($this->pdf->rapport_GRAFIEK_sortering == 1)
			$order = 'TijdelijkeRapportage.beleggingscategorieVolgorde ASC';
		else
			$order = 'WaardeEuro desc';


		$query="SELECT TijdelijkeRapportage.Hoofdcategorie as beleggingscategorie,
	sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
	TijdelijkeRapportage.HoofdcategorieOmschrijving as Omschrijving,
	TijdelijkeRapportage.Hoofdcategorie as Beleggingscategorie
	FROM TijdelijkeRapportage
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.Hoofdcategorie
	ORDER BY $order";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$percentagebelcat=array();
		$labelcat=array();
		while($cat = $DB->nextRecord())
		{
			if ($cat['beleggingscategorie']== "")
			{
				if (round($cat['WaardeEuro'] - $totaalLiquiditeiten,1) != 0)
				{
					if(round($totaalLiquiditeiten,2) != 0)
					{
						$data['beleggingscategorie']['Liquiditeiten']['waardeEur']=$totaalLiquiditeiten;
						$data['beleggingscategorie']['Liquiditeiten']['Omschrijving']='Liquiditeiten';
						$cat['WaardeEuro'] = $cat['WaardeEuro'] - $totaalLiquiditeiten;
					}
					$cat['Omschrijving']="Geen categorie";
					$cat['beleggingscategorie']="Geen categorie";
				}
				else
				{
					$cat['Omschrijving']="Liquiditeiten";
					$cat['Beleggingscategorie']="Liquiditeiten";
				}
			}

			if ($this->pdf->rapport_GRAFIEK_sortering == 1 && $cat['Omschrijving'] == "Liquiditeiten" ) //liquiditeiten later toevoegen
			{
				$liquididiteiten['waardeEur'] = $cat['WaardeEuro'];
				$liquididiteiten['Omschrijving'] = "Liquiditeiten";
			}
			else
			{
				$data['beleggingscategorie'][$cat['Beleggingscategorie']]['waardeEur']=$cat['WaardeEuro'];
				$data['beleggingscategorie'][$cat['Beleggingscategorie']]['Omschrijving']=$cat['Omschrijving'];
			}
		}

		if ($this->pdf->rapport_GRAFIEK_sortering == 1 && round($liquididiteiten['waardeEur'],2) != 0 ) // liquiditeiten toevoegen
		{
			$data['beleggingscategorie']['Liquiditeiten']['waardeEur']     = $liquididiteiten['waardeEur'];
			$data['beleggingscategorie']['Liquiditeiten']['Omschrijving']  = $liquididiteiten['Omschrijving'];
		}

		$query="SELECT
			TijdelijkeRapportage.Beleggingssector, TijdelijkeRapportage.beleggingssectorOmschrijving as Omschrijving,
			sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM TijdelijkeRapportage LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds
			WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
			AND TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'  AND TijdelijkeRapportage.beleggingscategorie='AAND' AND Fondsen.fondssoort<>'OPT' "
			.$__appvar['TijdelijkeRapportageMaakUniek']."
			GROUP BY TijdelijkeRapportage.Beleggingssector
			ORDER BY $order ;";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
    $totaalWaardeSec=0;
		while($sec = $DB->nextRecord())
		{
			if ($sec['Beleggingssector']== "")
			{
					$sec['Omschrijving']= 'Geen sector';
					$sec['Beleggingssector']= 'Geen sector';
			}

			if ($this->pdf->rapport_GRAFIEK_sortering == 1 && $sec['Omschrijving'] == "Liquiditeiten" ) //liquiditeiten later toevoegen
			{
				$liquididiteiten['waardeEur'] = $sec['WaardeEuro'];
				$liquididiteiten['Omschrijving'] = "Liquiditeiten";
			}
			else
			{
				$data['sectoren'][$sec['Beleggingssector']]['waardeEur']=$sec['WaardeEuro'];
				$data['sectoren'][$sec['Beleggingssector']]['Omschrijving']=$sec['Omschrijving'];
			}
      $totaalWaardeSec+=$sec['WaardeEuro'];
		}


		$grafieken = array();
		$grafieken[] = 'OIB';
	//	$grafieken[] = 'OIR';
	//	$grafieken[] = 'OIS';
		$grafieken[] = 'OIS2';

		$groepen = array();
		$groepen[]=$data['beleggingscategorie'];
	//	$groepen[]=$data['regio'];
	//	$groepen[]=$data['hoofdsectoren'];
		$groepen[]=$data['sectoren'];

		$standaardKleuren=array(array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
			array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
		,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155));


		$grafiekKleuren = array();
		for ($i=0; $i <2; $i++)
		{
		  if($i==1)
        $rekenWaarde=$totaalWaardeSec;
		  else
		    $rekenWaarde=$totaalWaarde;
			$restPercentage = 100;
			while (list($groep, $groepdata) = each($groepen[$i]))
			{
				$percentageGroep=($groepdata['waardeEur'] / $rekenWaarde) * 100 ;
				$restPercentage = $restPercentage - $percentageGroep;
				if (round($percentageGroep,1) != 0)
				{
					$kleurdata[$i][$groep]['kleur'] = $allekleuren[$grafieken[$i]][$groep];
					if ($percentageGroep < 0)
						$percentageGroep = $percentageGroep * -1;
					$grafiekData[$grafieken[$i]]['Percentage'][] = $percentageGroep ;
					$grafiekData[$grafieken[$i]]['Omschrijving'][] =  $groepdata['Omschrijving'] . " (" . round(($groepdata['waardeEur'] / $rekenWaarde) * 100 ,1) ." %)" ;
				}
			}
			if (round($restPercentage,1) >0)
			{
				$grafiekData[$grafieken[$i]]['Percentage'][] = $restPercentage;
				$grafiekData[$grafieken[$i]]['Omschrijving'][] = "Rest percentage" . " (" . round($restPercentage,1) ." %)" ;
			}


			if($kleurdata[$i])
			{
				$a=0;
				while (list($key, $value) = each($kleurdata[$i]))
				{
					if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
					{
						if ($a <15)
						{
							$grafiekKleuren[$i][]=$standaardKleuren[$a];
							$grafiekData[$grafieken[$i]]['Kleur'][] = $standaardKleuren[$a];
						}
						else
						{
							$grafiekKleuren[$i][]=$standaardKleuren[$a-15];
							$grafiekData[$grafieken[$i]]['Kleur'][] = $standaardKleuren[$a-15];
						}
					}
					else
					{
						$grafiekKleuren[$i][] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
						$grafiekData[$grafieken[$i]]['Kleur'][] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
					}
					$a++;
				}
			}
			else
			{
				$grafiekKleuren[$i] = $standaardKleuren;
				$grafiekData[$grafieken[$i]]['Kleur'] = $standaardKleuren;
			}
		}



		$diameter = 35;
		$hoek = 30;
		$dikte = 10;
		$Xas= 80;
		$yas= 80;

//($labels,$x,$y,$colors,$xcor=-55,$xcor2=5,$ycor= 27,$kort = 0,$colMaxInput=0)
		$this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas,$yas,$grafiekData['OIB']['Kleur']);
		$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$grafiekData['OIB']['Kleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie",'titel');

		$this->pdf->set3dLabels($grafiekData['OIS2']['Omschrijving'],$Xas+135,$yas,$grafiekData['OIS2']['Kleur'],-55,5,27,0,15,30);
		$this->pdf->Pie3D($grafiekData['OIS2']['Percentage'],$grafiekData['OIS2']['Kleur'],$Xas+135,$yas,$diameter,$hoek,$dikte,"Sectorverdeling aandelen (excl opties)",'titel');


	}
  
    function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}
}
?>