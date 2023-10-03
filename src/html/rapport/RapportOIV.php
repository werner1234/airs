<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingValutaLayout.php");

class RapportOIV
{
	function RapportOIV($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIV_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIV_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in valuta";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
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

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2];

		$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		if(!empty($totaalA))
		{
			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIV_decimaal);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_OIV_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,$this->pdf->rapport_OIV_decimaal_proc);

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

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function renteEnLiquiditeiten($koersPrinted=true)
	{
		global $__appvar;
		// global $totaalWaarde, $categorien, $this->lastValutaCode,$totaalactueel,$totaalactueelvaluta;
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,  ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  ".
		" AND TijdelijkeRapportage.valuta = '".$this->lastValutaCode."' ".
		" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		$rente = $DB1->NextRecord();



		//$percentageVanTotaal = $rente['subtotaalactueel'] / ($totaalWaarde/100);
		if(round($rente['subtotaalactueelvaluta'],2) <> 0)
		{
			$this->pdf->row(array("",
											"",
											vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),
											$this->formatGetal($rente['subtotaalactueelvaluta'],$this->pdf->rapport_OIV_decimaal),
											"",
											"",
											""));
		}

		$this->totaalactueel += $rente['subtotaalactueel'];
		$this->totaalactueelvaluta += $rente['subtotaalactueelvaluta'];

		// selecteer liquid
		$query = "SELECT TijdelijkeRapportage.valuta,TijdelijkeRapportage.actueleValuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,  ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rekening'  ".
		" AND TijdelijkeRapportage.valuta = '".$this->lastValutaCode."' ".
		" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		$rente = $DB1->NextRecord();

		//$percentageVanTotaal = $rente['subtotaalactueel'] / ($totaalWaarde/100);
		if(round($rente['subtotaalactueelvaluta'],2) <> 0)
		{
			if($koersPrinted==false)
			{
				$koerstxt = vertaalTekst("Koers", $this->pdf->rapport_taal);
				$koers = $this->formatGetalKoers($rente['actueleValuta'], 4);
				$koersPrinted=true;
			}
			else
			{
				$koerstxt='';
				$koers='';
			}

			$this->pdf->row(array($koerstxt,
												$koers,
										vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),
										$this->formatGetal($rente['subtotaalactueelvaluta'],$this->pdf->rapport_OIV_decimaal),
										"",
										"",
										""));
		}

		$this->totaalactueel 				+= $rente['subtotaalactueel'];
		$this->totaalactueelvaluta 	+= $rente['subtotaalactueelvaluta'];
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;

		// voor data
		$this->pdf->widthA = array(25,15,50,25,25,25,15,110);
		$this->pdf->alignA = array('L','R','L','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(40,50,25,25,25,15,102);
		$this->pdf->alignB = array('L','L','R','R','R','R');

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
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT ".
		" TijdelijkeRapportage.type, TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
		" TijdelijkeRapportage.valuta, TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.actueleValuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  TijdelijkeRapportage.type <> 'rente' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta,TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.type ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde asc, TijdelijkeRapportage.beleggingscategorieVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$lastValuta = 'eerste';
		$koersPrinted=false;
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);


			if ($categorien['valuta'] == $this->pdf->rapportageValuta)
			  $koersPrinted = true;

			// print totaal op hele categorie.
			if($lastValuta <> $categorien['ValutaOmschrijving'] && !empty($lastValuta) )
			{
				$this->renteEnLiquiditeiten($koersPrinted);

				$percentageVanTotaal = $this->totaalactueel / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal("", $this->totaalactueel, $this->totaalactueelvaluta, $percentageVanTotaal);

				$this->pdf->pieData[vertaalTekst($this->lastValutaOmschrijving, $this->pdf->rapport_taal)] = $percentageVanTotaal;
				$GrafiekValuta[$this->lastValutaOmschrijving]=$percentageVanTotaal;//toevoeging kleuren.
				$this->totaalactueel = 0;
				$this->totaalactueelvaluta = 0;
				$koersPrinted=false;
			}

			if($lastValuta <> $categorien['ValutaOmschrijving'])
			{
				$this->printKop(vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal), "bi");
				$koerstxt = vertaalTekst("Koers",$this->pdf->rapport_taal);
				$koers =  $this->formatGetalKoers($categorien['actueleValuta'],4);
			}
			elseif ($koersPrinted == true )
			{
			  $koerstxt = "";
				$koers =  "";
			}

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			if($categorien['type'] == 'fondsen' )
			{
			  if($categorien['valuta'] == $this->pdf->rapportageValuta)
			  {
			    $koerstxt = '';
			    $koers = '';
			  }

				// print categorie footers
				$this->pdf->row(array($koerstxt,
												$koers,
												vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal),
												$this->formatGetal($categorien['subtotaalactueelvaluta'],$this->pdf->rapport_OIV_decimaal),
												"",
												"",
												""));

				// totaal op categorie tellen
				$this->totaalactueel += $categorien['subtotaalactueel'];
				$this->totaalactueelvaluta += $categorien['subtotaalactueelvaluta'];
				$koersPrinted = true;
			}

			$lastValuta = $categorien['ValutaOmschrijving'];
			$this->lastValutaCode = $categorien['valuta'];
			$this->lastValutaOmschrijving = $categorien['ValutaOmschrijving'];
	//		$grafiekCategorien[$lastValuta]=$categorien[ValutaOmschrijving];
		}

		$this->renteEnLiquiditeiten($koersPrinted);


		// totaal voor de laatste categorie
		$percentageVanTotaal = $this->totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", $this->totaalactueel, $this->totaalactueelvaluta, $percentageVanTotaal);
		$this->pdf->pieData[vertaalTekst($this->lastValutaOmschrijving, $this->pdf->rapport_taal)] = $percentageVanTotaal;
		$GrafiekValuta[$this->lastValutaOmschrijving]=$percentageVanTotaal; //toevoeging kleuren.


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3]+ $this->pdf->widthA[4];
		$proc = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[5],$this->pdf->GetY());
		$this->pdf->Line($proc+2,$this->pdf->GetY(),$proc + $this->pdf->widthA[6],$this->pdf->GetY());

		$this->pdf->setX($this->pdf->marge);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($this->pdf->widthA[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[1],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[2],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[3],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[4],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[5],4,$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIV_decimaal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$this->formatGetal(100,$this->pdf->rapport_OIV_decimaal_proc), 0,1, "R");

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[5],$this->pdf->GetY());
		$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthA[5],$this->pdf->GetY()+1);
		$this->pdf->Line($proc+2,$this->pdf->GetY(),$proc + $this->pdf->widthA[6],$this->pdf->GetY());
		$this->pdf->Line($proc+2,$this->pdf->GetY()+1,$proc + $this->pdf->widthA[6],$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		if($this->pdf->rapport_OIV_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIV_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_OIV_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIV_rendementKort );
		}

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIV'];
		$q = "SELECT Valuta, omschrijving FROM Valutas";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbValutacategorien = array();
		while($valta = $DB->NextRecord())
		{
			$dbValutacategorien[$valta['Valuta']] = $valta['omschrijving'];
		}

		while (list($groep, $percentage) = each($GrafiekValuta))//$grafiekCategorien
		{
		  while (list($key, $value) = each($dbValutacategorien))
  		  {
			if ($value == $groep)
			{
			  $groepVertaling=vertaalTekst($groep,$this->pdf->rapport_taal);
  			$kleurdata[$groepVertaling]['kleur'] = $kleuren[$key];
  			$kleurdata[$groepVertaling]['percentage'] = $percentage;
			}
  		  }
		reset($dbValutacategorien);
		}

		$this->pdf->printPie($this->pdf->pieData,$kleurdata);
  //  listarray($kleurdata);
//listarray($this->pdf->pieData);
	}
}
?>