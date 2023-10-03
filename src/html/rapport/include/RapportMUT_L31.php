<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/28 09:58:52 $
File Versie					: $Revision: 1.2 $

$Log: RapportMUT_L31.php,v $
Revision 1.2  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.1  2010/09/15 16:29:10  rvv
*** empty log message ***

Revision 1.7  2010/06/09 18:46:35  rvv
*** empty log message ***

Revision 1.6  2010/01/13 11:05:20  rvv
*** empty log message ***

Revision 1.5  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.4  2005/12/28 07:40:57  jwellner
no message

Revision 1.3  2005/11/17 07:25:02  jwellner
no message

Revision 1.2  2005/11/11 16:13:50  jwellner
bufix in MUT2 , PERF en Rekenclass

Revision 1.1  2005/09/30 14:05:13  jwellner
- rapport OIH
- rapport MUT2
- Layout 5
- selectieschermen

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

// alleen voor layout 2
class RapportMUT_L31
{
	function RapportMUT_L31($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT2";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' || $this->pdf->rapportageValuta != '')
		 $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
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
		$this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		$this->pdf->Cell($writerow,4,$data, 0,0, "R");

		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "totaal")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}
			else if($type == "grandtotaal")
			{
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
		}
		$this->pdf->setY($y);
	}

	//function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)
	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE,  $totaalF, $grandtotal=false)
	{
		$hoogte = 16;

		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		if(!$grandtotal)
			$totType = "totaal";
		else
			$totType = "grandtotaal";


		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->printCol(1,$title,"tekst");
		//if($totaalA <>0)
		$this->printCol(2,$this->formatGetal($totaalA,$this->pdf->rapport_MUT2_decimaal),$totType);
		//if($totaalB <>0)
		$this->printCol(3,$this->formatGetal($totaalB,$this->pdf->rapport_MUT2_decimaal),$totType);

		if($totaalC <>0)
			$this->printCol(4,$this->formatGetal($totaalC,$this->pdf->rapport_MUT2_decimaal),$totType);
		if($totaalD <>0)
			$this->printCol(5,$this->formatGetal($totaalD,$this->pdf->rapport_MUT2_decimaal),$totType);
		if($totaalE <>0)
			$this->printCol(6,$this->formatGetal($totaalE,$this->pdf->rapport_MUT2_decimaal),$totType);
		if($totaalF <>0)
			$this->printCol(7,$this->formatGetal($totaalF,$this->pdf->rapport_MUT2_decimaal),$totType);

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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
	{
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		// voor data
		$this->pdf->widthA = array(25,100,25,25,25,25,25,25);
		$this->pdf->alignA = array('R','L','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(25,100,25,25,25,25,25,25);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R');

		$this->pdf->AddPage();

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT ".
			"Rekeningmutaties.Boekdatum, ".
			"Rekeningmutaties.Omschrijving ,".
			"ABS(Rekeningmutaties.Aantal) AS Aantal, ".
			"Rekeningmutaties.Debet $koersQuery as Debet, ".
			"Rekeningmutaties.Credit $koersQuery as Credit, ".
			"Rekeningmutaties.Valutakoers, ".
			"Rekeningmutaties.Rekening, ".
			"Rekeningmutaties.Grootboekrekening, ".
			"Rekeningmutaties.Afschriftnummer, ".
			"Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
			"Grootboekrekeningen.Opbrengst, ".
			"Grootboekrekeningen.Kosten, ".
			"Grootboekrekeningen.Afdrukvolgorde ".
			"FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen ".
			"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Onttrekking='1' OR Grootboekrekeningen.Kruispost = '1') ".
			"ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
			//"AND Grootboekrekeningen.Grootboekrekening <> 'KNBA' ".
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($mutaties = $DB->nextRecord())
		{
			$skip = false;
			// skip bankkosten en Belasting records die al verrekend zijn in DIV overzicht.
			if($mutaties['Grootboekrekening'] == "KNBA" ||$mutaties['Grootboekrekening'] == "DIVBE")
			{
				$knbae = array();
				$query = "SELECT ".
					"SUM(Rekeningmutaties.Debet) $koersQuery AS Debet, ".
					"SUM(Rekeningmutaties.Credit) $koersQuery as Credit ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties[Rekening]."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties[Afschriftnummer]."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties[Boekdatum]."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties[Omschrijving])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening = 'DIV' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				if($DBx->records() > 0)
					$skip = true;
				else
					$skip = false;
			}



			if(!$skip)
			{
				// print totaal op hele categorie.
				if($lastCategorie <> $mutaties[gbOmschrijving] && !empty($lastCategorie) )
				{


					$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),
											$subdebet, $subcredit, $subknba, $subkost, $subbel, $subnetto);


					$subdebet = 0;
					$subcredit = 0;
					$subknba = 0;
					$subkost = 0;
					$subbel = 0;
					$subnetto = 0;
				}

				if($lastCategorie <> $mutaties[gbOmschrijving])
				{
					$this->printKop(vertaalTekst($mutaties[gbOmschrijving],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				}


				if($mutaties['Kosten'] > 0)
				{
					if($mutaties['Credit'])
						$debet	= abs($mutaties[Credit]) * $mutaties[Valutakoers] * -1;
					else
						$debet	= abs($mutaties[Debet]) * $mutaties[Valutakoers];
					$credit = 0;
				}
				else if($mutaties['Opbrengs'] > 0)
				{
					if($mutaties['Debet'])
						$credit	= abs($mutaties[Debet]) * $mutaties[Valutakoers] * -1;
					else
						$credit	= abs($mutaties[Credit]) * $mutaties[Valutakoers];
					$debet = 0;
				}
				else
				{
					if($mutaties['Grootboekrekening'] == "ONTTR")
					{
						if($mutaties['Credit'])
							$debet	= abs($mutaties[Credit]) * $mutaties[Valutakoers] * -1;
						else
							$debet	= abs($mutaties[Debet]) * $mutaties[Valutakoers];
						$credit = 0;
					}
					else
					{
						if($mutaties['Debet'])
							$credit	= abs($mutaties[Debet]) * $mutaties[Valutakoers] * -1;
						else
							$credit	= abs($mutaties[Credit]) * $mutaties[Valutakoers];
						$debet = 0;
					}

				}

				// als grootboek is Transactie kosten, zet alles onder Debet .

				if($debet <> 0)
					$debettxt = $this->formatGetal($debet,2);
				else
					$debettxt = "";

				if($credit <> 0)
					$credittxt = $this->formatGetal($credit,2);
				else
					$credittxt = "";

				$knbae = array();
				$query = "SELECT ".
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) $koersQuery AS Debet, ".
					"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) $koersQuery AS Credit ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties[Rekening]."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties[Afschriftnummer]."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties[Boekdatum]."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties[Omschrijving])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening = 'KNBA' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$knba = $DBx->nextRecord();

				$divbe = array();
				$query = "SELECT ".
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) $koersQuery AS Debet, ".
					"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) $koersQuery AS Credit ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties[Rekening]."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties[Afschriftnummer]."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties[Boekdatum]."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties[Omschrijving])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening = 'DIVBE' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$divbe = $DBx->nextRecord();

				$knba['Debet']	= abs($knba['Debet']) ;
				$divbe['Debet']	= abs($divbe['Debet']);
				$knba['Credit']	= abs($knba['Credit']) ;
				$divbe['Credit']	= abs($divbe['Credit']);

				$netto = $credit - $knba['Debet'] - $divbe['Debet'] + $knba['Credit'] + $divbe['Credit'];

				if($mutaties['Grootboekrekening'] == "DIV")
				{
						$nettotxt = 	$this->formatGetal($netto,2);
						$knbatxt = $this->formatGetal($knba['Debet']-$knba['Credit'],2);
						$beltxt = $this->formatGetal($divbe['Debet']-$divbe['Credit'],2);
						$kosttxt = $this->formatGetal(0,2);
						$kosttxt = $this->formatGetal(0,2);
						$omschrijving = str_replace("Dividend","",$mutaties['Omschrijving']);
				}
				else if($mutaties['Grootboekrekening'] == "KNBA")
				{
					// bankkosten optellen bij kosten
					$knba[Debet]	= 0;
					$divbe[Debet] = 0;
					$netto = 0;
					$kost = $debet;
					$debet = 0;

					$debettxt = "";
					$knbatxt = "";
					$kosttxt = $this->formatGetal($kost,2);
					$beltxt = "";
					$nettotxt = "";
					$omschrijving = $mutaties['Omschrijving'];
				}
				else if($mutaties['Grootboekrekening'] == "DIVBE")
				{
					// bankkosten optellen bij belasting
					$knba[Debet]	= 0;
					$divbe[Debet] = $credit * -1;
					$netto = 0;
					$kost = 0;
					$credit = 0;

					$credittxt = "";
					$knbatxt = "";
					$kosttxt = "";
					$beltxt = $this->formatGetal($divbe['Debet'],2);
					$nettotxt = "";
					$omschrijving = $mutaties['Omschrijving'];
				}
				else
				{
					$knba[Debet]	= 0;
					$divbe[Debet] = 0;
					$netto = 0;

					$knbatxt = "";
					$kosttxt = "";
					$beltxt = "";
					$nettotxt = "";
					$omschrijving = $mutaties['Omschrijving'];
				}
					// selecteer KNBA
					// selecteer DIVBE

				$subdebet += $debet;
				$subcredit += $credit;
				$subknba += $knba['Debet'];
				$subkost += $kost;
				$subbel += $divbe['Debet'];
				$subnetto += $netto;
				//echo $divbe[Debet]." ".$subbel."<br>";


				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

				$this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
												vertaalTekst($omschrijving,$this->pdf->rapport_taal),
												$debettxt,
												$credittxt,
												$knbatxt,
												$kosttxt,
												$beltxt,
												$nettotxt));

				$totaalcredit += $credit;
				$totaaldebet += $debet;
				$totaalknba += $knba[Debet];
				$totaalkost += $kost;
				$totaalbel += $divbe[Debet];
				$totaalnetto += $netto;
				$lastCategorie = $mutaties[gbOmschrijving];
			}
		}


		$actueleWaardePortefeuille +=
		$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),
		$subdebet,
		$subcredit,
		$subknba,
		$subkost,
		$subbel,
		$subnetto);

		$this->pdf->ln();

		$totaal = $actueleWaardePortefeuille;
		$actueleWaardePortefeuille +=
		$this->printTotaal(vertaalTekst("Totaal Generaal",$this->pdf->rapport_taal),
		$totaaldebet,
		$totaalcredit,
		$totaalknba,
		$totaalkost,
		$totaalbel,
		$totaalnetto,true);
	}
}
?>