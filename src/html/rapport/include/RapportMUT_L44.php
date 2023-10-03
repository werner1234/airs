<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/03/04 10:14:13 $
File Versie					: $Revision: 1.4 $

$Log: RapportMUT_L44.php,v $
Revision 1.4  2018/03/04 10:14:13  rvv
*** empty log message ***

Revision 1.3  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.2  2014/08/16 15:31:50  rvv
*** empty log message ***

Revision 1.1  2013/03/13 17:01:08  rvv
*** empty log message ***

Revision 1.2  2010/07/04 15:24:39  rvv
*** empty log message ***

Revision 1.1  2009/09/27 12:54:02  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportMUT_L44
{
	function RapportMUT_L44($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT2";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatieoverzicht";

		if ($this->pdf->rapportageValuta != 'EUR' || $this->pdf->rapportageValuta == '')
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
    $query="SELECT
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Omschrijving,
ABS(Rekeningmutaties.Aantal) AS Aantal,
Rekeningmutaties.Debet $koersQuery AS Debet,
Rekeningmutaties.Credit $koersQuery AS Credit,
Rekeningmutaties.Valutakoers,
Rekeningmutaties.Rekening,
Rekeningmutaties.Grootboekrekening,
Rekeningmutaties.Afschriftnummer,
Grootboekrekeningen.Omschrijving AS gbOmschrijving,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Afdrukvolgorde,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
if(Rekeningmutaties.Grootboekrekening='DIV',Beleggingscategorien.Afdrukvolgorde,'') as divVolgorde
FROM
Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
LEFT JOIN BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = 'TOP'
LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE  Rekeningen.Portefeuille = '".$this->portefeuille."'  AND 
Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND 
(Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')
ORDER BY Grootboekrekeningen.Afdrukvolgorde,divVolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id
";

    $DB = new DB();
		$DB->SQL($query); //echo $query;exit;
		$DB->Query();
		while($mutaties = $DB->nextRecord())
		{
			$skip = false;
			// skip bankkosten en Belasting records die al verrekend zijn in DIV overzicht.
			if($mutaties['Grootboekrekening'] == "KNBA" ||$mutaties['Grootboekrekening'] == "DIVBE")
			{
				$knbae = array();
				$query = "SELECT ".
					"SUM(Rekeningmutaties.Debet) $koersQuery AS Debet ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
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
        
                
        if($mutaties['Grootboekrekening'] == "DIV" || isset($belCalTotalen[$lastBelCat]))
        {
          if($mutaties['categorieOmschrijving'] <> $lastBelCat)
          {
            if(isset($belCalTotalen[$lastBelCat]))
            		$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." dividend ".vertaalTekst($lastBelCat,$this->pdf->rapport_taal),
		                               $belCalTotalen[$lastBelCat]['debet'],
		                               $belCalTotalen[$lastBelCat]['credit'],
		                               $belCalTotalen[$lastBelCat]['knba'],
		                               $belCalTotalen[$lastBelCat]['kost'],
		                               $belCalTotalen[$lastBelCat]['divbe'],
		                               $belCalTotalen[$lastBelCat]['netto']);
            unset($belCalTotalen[$lastBelCat]);   
            $this->pdf->Ln();                     
          }
       }   
                                   
                                   
				if($lastCategorie <> $mutaties['gbOmschrijving'] && !empty($lastCategorie) )
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

				if($lastCategorie <> $mutaties['gbOmschrijving'])
				{
					$this->printKop(vertaalTekst($mutaties['gbOmschrijving'],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
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
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) $koersQuery AS Debet ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening = 'KNBA' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$knba = $DBx->nextRecord();

				$divbe = array();
				$query = "SELECT ".
					"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) $koersQuery AS Debet ".
					"FROM Rekeningmutaties, Rekeningen ".
					"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
					"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
					"AND Rekeningen.Rekening = '".$mutaties['Rekening']."' ".
					"AND Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' ".
					"AND Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' ".
					"AND Rekeningmutaties.Omschrijving = '".mysql_escape_string($mutaties['Omschrijving'])."' ".
					"AND Rekeningmutaties.Verwerkt = '1' ".
					"AND Rekeningmutaties.Grootboekrekening = 'DIVBE' ".
					"GROUP BY Rekeningmutaties.Grootboekrekening ";

				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$divbe = $DBx->nextRecord();

				$knba['Debet']	= abs($knba['Debet']) ;
				$divbe['Debet']	= abs($divbe['Debet']);

				$netto = $credit - $knba[Debet] - $divbe[Debet];

				if($mutaties['Grootboekrekening'] == "DIV")
				{
						$nettotxt = 	$this->formatGetal($netto,2);
						$knbatxt = $this->formatGetal($knba[Debet],2);
						$beltxt = $this->formatGetal($divbe[Debet],2);
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
					$beltxt = $this->formatGetal($divbe[Debet],2);
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
        
        if($mutaties['Grootboekrekening'] == "DIV")
        {
          if($mutaties['categorieOmschrijving'] <> $lastBelCat)
          {
        
	          $this->printKop(vertaalTekst("Dividend",$this->pdf->rapport_taal).' '.vertaalTekst($mutaties['categorieOmschrijving'],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
            $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          
          }
          
          $lastBelCat=$mutaties['categorieOmschrijving'];
          $belCalTotalen[$mutaties['categorieOmschrijving']]['debet']+=$debet;
          $belCalTotalen[$mutaties['categorieOmschrijving']]['credit']+=$credit;
          $belCalTotalen[$mutaties['categorieOmschrijving']]['knba']+=$knba['Debet'];;
          $belCalTotalen[$mutaties['categorieOmschrijving']]['kost']+=$kost;
          $belCalTotalen[$mutaties['categorieOmschrijving']]['divbe']+=$divbe['Debet'];
          $belCalTotalen[$mutaties['categorieOmschrijving']]['netto']+=$netto;
          
        }


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