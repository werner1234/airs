<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/07/27 15:50:38 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIB_L69.php,v $
Revision 1.2  2016/07/27 15:50:38  rvv
*** empty log message ***

Revision 1.1  2016/04/23 15:33:07  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L69
{
	function RapportOIB_L69($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
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

	function printTotaal($title, $totaalA, $procent, $grandtotaal)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2];

		if(!empty($totaalA))
		{
			if($this->pdf->rapport_OIB_specificatie == 1)
				$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIB_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_layout == 14)
		{
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1];
		$this->pdf->SetX($actueel);
		$this->pdf->Cell($this->pdf->widthB[2],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[3],4,$totaalprtxt, 0,1, "R");
		}
		else
		{
		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalprtxt, 0,1, "R");
		}

		if($grandtotaal)
		{
		  if($this->pdf->rapport_layout == 14)
		  {
      $actueel  = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1];
		  }

			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[3],$this->pdf->GetY()+1);
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln(2);

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

		if(($this->pdf->GetY() + 12) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();


		if($this->pdf->rapport_layout == 14)
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0]+$this->pdf->widthB[1],4, $title, 0, "L");
		}
		else
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");
		}

	  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetY($y);
	}


	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(40,35,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,35,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');

		if($this->pdf->rapport_layout == 8)
		{
			$this->pdf->widthA = array(40,35,25,25,25,15,116);
			$this->pdf->widthB = array(40,35,25,25,25,15,116);
		}

		$this->pdf->AddPage();

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
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
		{
			$query = "SELECT TijdelijkeRapportage.hoofdcategorieOmschrijving as Omschrijving, ".
				" TijdelijkeRapportage.valutaOmschrijving as ValutaOmschrijving, ".
				" TijdelijkeRapportage.valuta, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.beleggingscategorie, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
				" FROM (TijdelijkeRapportage)".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.hoofdcategorie, TijdelijkeRapportage.valuta ".
				" ORDER BY TijdelijkeRapportage.hoofdsectorVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";
			debugSpecial($query,__FILE__,__LINE__);
		}


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			if($this->pdf->rapport_OIB_rentebijobligaties && strtolower($categorien[Omschrijving]) == "obligaties")
			{
				// selecteer rente
				$query = "SELECT TijdelijkeRapportage.valuta, ".
					" TijdelijkeRapportage.valutaOmschrijving as ValutaOmschrijving, ".
					" TijdelijkeRapportage.beleggingscategorie, ".
					" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalactueelvaluta, ".
					" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
					" TijdelijkeRapportage ".
					" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.valuta = '".$categorien['valuta']."' AND ".
					" TijdelijkeRapportage.type = 'rente'  ".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" GROUP BY TijdelijkeRapportage.valuta ".
					" ORDER BY TijdelijkeRapportage.valutaVolgorde asc , TijdelijkeRapportage.Lossingsdatum asc";
				debugSpecial($query,__FILE__,__LINE__);
				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$rentedata = $DBx->nextRecord();
				$categorien['subtotaalactueelvaluta'] = $categorien['subtotaalactueelvaluta'] + $rentedata['subtotaalactueelvaluta'];
				$categorien['subtotaalactueel'] = $categorien['subtotaalactueel'] + $rentedata['subtotaalactueel'];
			}

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
				$totaalbegin = 0;
				$totaalactueel = 0;
				// voor Pie
				$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.
			}

			if($lastCategorie != $categorien['Omschrijving'])
			{
				if($this->pdf->rapport_layout == 14 && empty($lastCategorie))
				{
					$this->pdf->row(array(""));
				}
				$categorieTekst = $categorien[Omschrijving];
				$this->printKop(vertaalTekst($categorieTekst,$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
			}
			$lastCategorie = $categorien[Omschrijving];

			$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);
			if($this->pdf->rapport_layout != 14)
			{
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal));
			}
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			// print categorie footers

			if($this->pdf->rapport_OIB_specificatie == 1)
			{
				if($this->pdf->rapport_layout == 10)
				{
					$this->pdf->row(array("",
														"",
														"",
														$this->formatGetalKoers($categorien['subtotaalactueel'],$this->pdf->rapport_OIB_decimaal),
														"",
														$this->formatGetal($percentageVanTotaal,1).""));
				}
				else
				{
					$this->pdf->row(array("",
														"",
														$this->formatGetal($categorien['subtotaalactueelvaluta'],$this->pdf->rapport_OIB_decimaal),
														$this->formatGetalKoers($categorien['subtotaalactueel'],$this->pdf->rapport_OIB_decimaal),
														"",
														$this->formatGetal($percentageVanTotaal,1).""));
				}
			}
			else
			{

				if($this->pdf->rapport_layout != 14)
				{
					$this->pdf->row(array("",
														"",
														"",
														"",
														"",
														$this->formatGetal($percentageVanTotaal,1).""));
				}
			}


			// totaal op categorie tellen
			$totaalinvaluta += $categorien['subtotaalactueelvaluta'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$lastCat       = $categorien['beleggingscategorie'];
			$lastCategorie = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie


		$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
		// voor Pie
		$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
		$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.

		if(!$this->pdf->rapport_OIB_rentebijobligaties)
		{
			// selecteer rente
			$query = "SELECT TijdelijkeRapportage.valuta, ".
				" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
				" TijdelijkeRapportage.beleggingscategorie, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalactueelvaluta, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
				" TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rente'  ".
				" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.valuta ".
				" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
			debugSpecial($query,__FILE__,__LINE__);
			$DB = new DB();
			$DB->SQL($query);
			$DB->Query();

			if($DB->records() > 0)
			{
				$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);

				$totaalRenteInValuta = 0 ;

				while($categorien = $DB->NextRecord())
				{

					$subtotaalRenteInValuta = 0;

					$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);

					// print valutaomschrijving appart ivm met apparte fontkleur
					$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
					$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
					$this->pdf->setX($this->pdf->marge);

					$this->pdf->Cell($this->pdf->widthB[0],4,"");

					if($this->pdf->rapport_layout != 14)
					{
						$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($categorien[ValutaOmschrijving],$this->pdf->rapport_taal));
					}
					$this->pdf->setX($this->pdf->marge);

					$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
					if($this->pdf->rapport_OIB_specificatie == 1)
					{
						if($this->pdf->rapport_layout == 10)
						{
							$this->pdf->row(array("",
																"",
																"",
																$this->formatGetalKoers($categorien[subtotaalactueel],$this->pdf->rapport_OIB_decimaal),
																"",
																$this->formatGetal($percentageVanTotaal,1).""));
						}
						else
						{
							$this->pdf->row(array("",
																"",
																$this->formatGetal($categorien[subtotaalactueelvaluta],$this->pdf->rapport_OIB_decimaal),
																$this->formatGetal($categorien[subtotaalactueel],$this->pdf->rapport_OIB_decimaal),
																"",
																$this->formatGetal($percentageVanTotaal,1).""));
						}
					}
					else
					{

						if($this->pdf->rapport_layout != 14)
						{
							$this->pdf->row(array("",
																"",
																"",
																"",
																"",
																$this->formatGetal($percentageVanTotaal,1).""));
						}
					}

					// print subtotaal
					//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
					$totaalRente += $categorien[subtotaalactueel];
				}

				// totaal op rente
				$percentageVanTotaal = $totaalRente / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal(" ", $totaalRente, $percentageVanTotaal);
				$this->pdf->pieData[vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien['Opgelopen Rente']=$percentageVanTotaal; //toevoeging voor kleuren.
			}
		}


		// Liquiditeiten
		$liqtitel = "Liquiditeiten";

		if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
			$liqtitel = strtoupper($liqtitel);

		$this->printKop(vertaalTekst($liqtitel,$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage, Valutas WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  AND ".
			" TijdelijkeRapportage.valuta = Valutas.valuta AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		while($data = $DB1->NextRecord())
		{
			$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];

			$percentageVanTotaal = $data[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->Cell($this->pdf->widthB[0],4,"");
			if($this->pdf->rapport_layout != 14)
			{
				$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($data[ValutaOmschrijving],$this->pdf->rapport_taal));
			}
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			if($this->pdf->rapport_OIB_specificatie == 1)
			{
				if($this->pdf->rapport_layout == 10)
				{
					$this->pdf->row(array("",
														"",
														"",
														$this->formatGetalKoers($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIB_decimaal),
														"",
														$this->formatGetal($percentageVanTotaal,1).""));
				}
				else
				{
					$this->pdf->row(array("",
														"",
														$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_OIB_decimaal),
														$this->formatGetalKoers($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIB_decimaal),
														"",
														$this->formatGetal($percentageVanTotaal,1).""));
				}
			}
			else
			{
				if($this->pdf->rapport_layout != 14)
				{
					$this->pdf->row(array("",
														"",
														"",
														"",
														"",
														$this->formatGetal($percentageVanTotaal,1).""));
				}
			}

		}
		// totaal liquiditeiten
		$percentageVanTotaal = $totaalLiquiditeitenEuro / ($totaalWaarde/100);
		$grafiekCategorien['Liquiditeiten']=$percentageVanTotaal; //toevoeging voor kleuren.
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalLiquiditeitenEuro, $percentageVanTotaal);
		$this->pdf->pieData[vertaalTekst($liqtitel,$this->pdf->rapport_taal)] = $percentageVanTotaal;


		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3];
		$proc = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		if($this->pdf->rapport_layout == 14)
		{
			$actueel -= 50;
			$proc -= 50;
			$extra = 10;
		}
		else
			$extra =0;


		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());

		$this->pdf->setX($this->pdf->marge);

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);



		if($this->pdf->rapport_layout == "7" || $this->pdf->rapport_layout == "1")
		{
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);
		}

		if($this->pdf->rapport_layout == 14)
		{
			$this->pdf->Cell($this->pdf->widthB[0]+$this->pdf->widthB[1],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "L");
		}
		else
		{
			$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
			$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "L");
			$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[3],4,"", 0,0, "L");
		}




		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		if($this->pdf->rapport_layout == 14)
		{
			$this->pdf->Cell($this->pdf->widthB[2],4,$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIB_decimaal), 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[3],4,$this->formatGetal(100,1), 0,1, "R");
		}
		else
		{
			$this->pdf->Cell($this->pdf->widthB[4],4,$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIB_decimaal), 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[5],4,$this->formatGetal(100,1), 0,1, "R");
		}

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[4],$this->pdf->GetY()+1);
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY()+1,$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_OIB_valutaoverzicht == 1)
		{
			$this->pdf->ln(2);
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIB_valutaoverzicht == 2)
		{
			$this->pdf->ln(2);
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_OIB_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIB_rendementKort);
		}

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];
		$q = "SELECT Beleggingscategorie, omschrijving FROM Beleggingscategorien";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbBeleggingscategorien = array();
		$dbBeleggingscategorien['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		while($categorie = $DB->NextRecord())
		{
			$dbBeleggingscategorien[$categorie['Beleggingscategorie']] = $categorie['omschrijving'];
		}

		foreach ($grafiekCategorien as $cat=>$percentage)
		{
			$groep=$dbBeleggingscategorien[$cat];
			if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
				$groep = strtoupper($groep);
			$groep=	vertaalTekst($groep,$this->pdf->rapport_taal);
			$kleurdata[$groep]['kleur'] = $kleuren[$cat];
			$kleurdata[$groep]['percentage'] = $percentage;
		}

		//		if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
		//		  $this->pdf->pieData[strtoupper(vertaalTekst($lastCategorie,$this->pdf->rapport_taal))] = $percentageVanTotaal;
		//		else z
		$this->pdf->printPie($this->pdf->pieData,$kleurdata);

	}
}


?>