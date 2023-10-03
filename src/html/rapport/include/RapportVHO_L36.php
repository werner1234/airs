<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/01/17 18:32:01 $
File Versie					: $Revision: 1.1 $

$Log: RapportVHO_L36.php,v $
Revision 1.1  2015/01/17 18:32:01  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L36
{
	function RapportVHO_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_VHO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VHO_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend historisch overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
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

	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		//$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10]+ $this->pdf->widthB[11];
		//$totaal5 = $totaal4 + $this->pdf->widthB[12];

		$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10];
		$totaal5 = $totaal4 + $this->pdf->widthB[11] + $this->pdf->widthB[12];


		if($this->pdf->rapport_layout == 14)
		{
		 $totaalD2 = $totaalC;
		  $totaalC = null;
		  $extra = $this->pdf->rapport_VHO_lijnenKorter;
	  }

		$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
		if(!empty($totaalA))
		{
			$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
		}

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_VHO_decimaal);

		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,$this->pdf->rapport_VHO_decimaal_proc)."%";

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
		}

		if(!empty($totaalD2))
		{
			$totaalD2txt = $this->formatGetal($totaalD2,$this->pdf->rapport_VHO_decimaal_proc)."%";

		}

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
		}

		if(!empty($totaalF))
		{
			$totaalFtxt = $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);
		}



		$this->pdf->SetX(0);
		$this->pdf->Cell($begin,4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[12],4,$totaalD2txt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[13],4,$totaalEtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,1, "R");
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF=0, $grandtotaal=false)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		// lege regel
		if($this->pdf->rapport_layout != 8)
			$this->pdf->ln();

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10];
		$totaal5 = $totaal4 + $this->pdf->widthB[11] + $this->pdf->widthB[12];

		if($this->pdf->rapport_layout == 14)
		{
		  $extra = $this->pdf->rapport_VHO_lijnenKorter;
	  }

		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
			if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
			{
				$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			}
			else
			{
				$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			}
		}
		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_VHO_decimaal);

			if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
				$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			else
				$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
		}


		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,$this->pdf->rapport_VHO_decimaal_proc)."%";

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);
					if($this->pdf->rapport_layout != 14)
			$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
		}

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
		}

		if(!empty($totaalF))
		{
			$totaalFtxt = $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);
		}


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

    if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
		{
			$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[12],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[13],4,$totaalEtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,1, "R");
		}
		else
		{
			$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[12],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[13],4,$totaalEtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,1, "R");
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



		if($grandtotaal)
		{
			if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
			{
				if(!empty($totaalB))
				{
					$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
					$this->pdf->Line($actueel+$extra,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
				}
				if(!empty($totaalA))
				{
					$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
					$this->pdf->Line($begin+$extra,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
				}
				if(!empty($totaalE))
				{
					$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
					$this->pdf->Line($totaal5+$extra,$this->pdf->GetY()+1,$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY()+1);
				}
				if(!empty($totaalD))
				{
					$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
					$this->pdf->Line($totaal4+$extra,$this->pdf->GetY()+1,$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY()+1);
				}

			}
			else
			{
				if(!empty($totaalA))
				{
					$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
					$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
				}
				if(!empty($totaalB))
				{
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
					$this->pdf->Line($begin,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
				}
				if(!empty($totaalE))
				{
					$this->pdf->Line($totaal5,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
					$this->pdf->Line($totaal5,$this->pdf->GetY()+1,$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY()+1);
				}
				if(!empty($totaalD))
				{
					$this->pdf->Line($totaal4,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
					$this->pdf->Line($totaal4,$this->pdf->GetY()+1,$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY()+1);
				}

			}
		}
		else
		{
			$this->pdf->setDash(1,1);
      if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
			{
				if(!empty($totaalB))
					$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				if(!empty($totaalA))
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
				if(!empty($totaalE))
					$this->pdf->Line($totaal5,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
				if(!empty($totaalD))
					$this->pdf->Line($totaal4,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
			}
			else
			{
				if(!empty($totaalA))
					$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				if(!empty($totaalB))
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
				if(!empty($totaalE))
					$this->pdf->Line($totaal5,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
				if(!empty($totaalD))
					$this->pdf->Line($totaal4,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());

			}
			$this->pdf->setDash();
		}

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
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();


		$this->pdf->widthB = array(10,48,18,15,23,22,1,15,23,22,15,22,15,22,15);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(60,18,15,23,22,1,15,25,22,12,22,15,22,15);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');


		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
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

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers / TijdelijkeRapportage.historischeRapportageValutakoers) AS subtotaalhistorisch, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'" .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie) )
			{
				if($this->pdf->rapport_VHO_percentageTotaal == 1)
				{
					$percentageVanTotaal = ($totaalactueel) / ($totaalWaarde/100);
				}
				else
				{
					$percentageVanTotaal = "";
				}

				$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				if($totaalhistorisch < 0)
					$procentResultaat = -1 * $procentResultaat;
				// attica ?
				//$procentResultaat = ($totaalvalutaresultaat / $totaalhistorisch) *100;

				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
				//function $this->printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)

				if($this->pdf->rapport_layout == 14)
				  $actueleWaardePortefeuille += $this->printTotaal($title, $totaalhistorisch, $totaalactueel,$percentageVanTotaal, 0, $totaalGecombeneerdResultaat, $procentResultaat);
				else
				  $actueleWaardePortefeuille += $this->printTotaal($title, $totaalhistorisch, $totaalactueel,$percentageVanTotaal, $totaalfondsresultaat , $totaalvalutaresultaat, $procentResultaat);

				$totaalhistorisch = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$procentResultaat = 0 ;
				$totaalGecombeneerdResultaat =0;
			}

			if($lastCategorie <> $categorien[Omschrijving])
			{
			  if($this->pdf->rapport_layout == 14)
			  	$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal), "b");
			  else
				$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal), "bi");
			}
			// subkop (valuta)
			if($this->pdf->rapport_VHO_geenvaluta == 1)
			{
			}
			else
			{
				$tekst = vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien[valuta];
				$this->printKop($tekst, "");
			}

			// print detail (select from tijdelijkeRapportage)
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien[beleggingscategorie]."' AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery); //echo $subquery.'<br><br>';exit();
			$DB2->Query();

			while($subdata = $DB2->NextRecord())
			{
				$fondsResultaat = ($subdata[actuelePortefeuilleWaardeInValuta] - $subdata[historischeWaardeTotaal]) * $subdata[actueleValuta] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata[historischeWaardeTotaal]) * 100;

				if($subdata[historischeWaardeTotaal] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;

				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				$valutaResultaat = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				$procentResultaat = (($subdata[actuelePortefeuilleWaardeEuro] - $subdata[historischeWaardeTotaalValuta]) / ($subdata[historischeWaardeTotaalValuta] /100));
        $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;

				if($subdata[historischeWaardeTotaalValuta] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VHO_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VHO_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);

				if($this->pdf->rapport_layout == 8 || $this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 14)
				{
					if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
						$fondsResultaatprocenttxt = "p.m.";
					else
						$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				}

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);

			  $fondsOmschrijving=$subdata['fondsOmschrijving'];
        $stringWidth=$this->pdf->GetStringWidth($fondsOmschrijving);
        if($stringWidth>$this->pdf->widthB[1])
        {
          $widthCount=$this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000*3;
          $newFondsOmschrijving='';
          for($i=0; $i<strlen($fondsOmschrijving); $i++) 
          { 
            $char=$fondsOmschrijving[$i];
            $charWidth=$this->pdf->CurrentFont['cw'][$char]*$this->pdf->FontSize/1000;
            $widthCount+=$charWidth;
            if($widthCount < $this->pdf->widthB[1])
              $newFondsOmschrijving.=$char;
          }  
          $fondsOmschrijving=$newFondsOmschrijving."...";
        }
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$fondsOmschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);

				if($this->pdf->rapport_VHO_percentageTotaal == 1)
					$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";
				else
					$percentageTotaalTekst = "";


				  $this->pdf->row(array("",
												"",
												$this->formatAantal($subdata[totaalAantal],0,$this->pdf->rapport_VHO_aantalVierDecimaal),
												$this->formatGetal($subdata[historischeWaarde],2),
												$this->formatGetal($subdata[historischeWaardeTotaal],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($subdata[historischeWaardeTotaalValuta],$this->pdf->rapport_VHO_decimaal),
												"",
												$this->formatGetal($subdata[actueleFonds],2),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
												$percentageTotaalTekst,
												$fondsResultaattxt,
												$fondsResultaatprocenttxt,
												$valutaResultaattxt,
												$procentResultaattxt	)
												);



				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];

				$subtotaal[fondsResultaat] = $subtotaal[fondsResultaat] + $fondsResultaat;
				$subtotaal[valutaResultaat] = $subtotaal[valutaResultaat] + $valutaResultaat;
				$subtotaal['gecombeneerdResultaat'] += $gecombeneerdResultaat;
			}

			if($this->pdf->rapport_VHO_percentageTotaal == 1)
			{
				$percentageVanTotaal = ($categorien[subtotaalactueel]) / ($totaalWaarde/100);
			}
			else {
				$percentageVanTotaal = "";
			}

			$procentResultaat = (($categorien[subtotaalactueel] - $categorien[subtotaalhistorisch]) / ($categorien[subtotaalhistorisch] /100));
			if($categorien[subtotaalhistorisch] < 0)
				$procentResultaat = -1 * $procentResultaat;

			// attica?
			//$procentResultaat = ($subtotaal[valutaResultaat] / $categorien[subtotaalhistorisch]) *100;

			// print categorie footers
			if($this->pdf->rapport_VHO_geensubtotaal == 1)
			{
			}
			else
			{
					if($this->pdf->rapport_layout == 14)
				   $this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),$categorien[subtotaalhistorisch], $categorien[subtotaalactueel],$percentageVanTotaal,0, $subtotaal['gecombeneerdResultaat'], $procentResultaat);
          else
			     $this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalhistorisch], $categorien[subtotaalactueel],$percentageVanTotaal, $subtotaal[fondsResultaat], $subtotaal[valutaResultaat], $procentResultaat);
			}

			// totaal op categorie tellen
			$totaalhistorisch += $categorien[subtotaalhistorisch];
			$totaalactueel += $categorien[subtotaalactueel];

			$totaalfondsresultaat += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];

		  $totaalGecombeneerdResultaat += $subtotaal['gecombeneerdResultaat'];


			$lastCategorie = $categorien[Omschrijving];
			$subtotaal = array();
		}

		if($this->pdf->rapport_VHO_percentageTotaal == 1)
		{
			$percentageVanTotaal = ($totaalactueel) / ($totaalWaarde/100);
		}
		else {
			$percentageVanTotaal = "";
		}

		// totaal voor de laatste categorie
		$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
		if($totaalhistorisch < 0)
			$procentResultaat = -1 * $procentResultaat;
//echo $totaalGecombeneerdResultaat ."<br>";
	if($this->pdf->rapport_layout == 14)
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalhistorisch, $totaalactueel,$percentageVanTotaal ,$totaalGecombeneerdResultaat,$totaalGecombeneerdResultaat, $procentResultaat);
  else
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalhistorisch, $totaalactueel,$percentageVanTotaal ,$totaalfondsresultaat,$totaalvalutaresultaat, $procentResultaat);



		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersStart." subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." subtotaalactueel FROM ".
		" TijdelijkeRapportage  ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					if($this->pdf->rapport_VHO_geenvaluta == 1) {
					}
					else
						$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien[valuta],"");

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{

						if($this->pdf->rapport_HSE_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata[rentedatum]));
							if($subdata[renteperiode] <> 12 && $subdata[renteperiode] <> 0)
								$rentePeriodetxt .= " / ".$subdata[renteperiode];
						}

						$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);

						if($this->pdf->rapport_VHO_percentageTotaal == 1)
							$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";
						else
							$percentageTotaalTekst = "";



						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving].$rentePeriodetxt );

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
						{
								$this->pdf->row(array("","","","","","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
														$percentageTotaalTekst));
						}
						else
						{
							$this->pdf->row(array("","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
														"","","", "",
														$percentageTotaalTekst));
						}
					}

					// print subtotaal
					//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
					if($this->pdf->rapport_VHO_percentageTotaal ==1)
					{
						$percentageVanTotaal = ($subtotaalRenteInValuta) / ($totaalWaarde/100);
					}
					else
						$percentageVanTotaal = 0;

					if($this->pdf->rapport_VHO_geensubtotaal == 1)
					{
					}
					else
						$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, $percentageVanTotaal, "", "");

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}
			}

			// totaal op rente
			if($this->pdf->rapport_VHO_percentageTotaal ==1)
			{
				$percentageVanTotaal = ($totaalRenteInValuta) / ($totaalWaarde/100);
			}
			else
				$percentageVanTotaal = 0;

			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal Opgelopen rente:",$this->pdf->rapport_taal),"", $totaalRenteInValuta, $percentageVanTotaal,"");
		}

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro , ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() >0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");
			$totaalLiquiditeitenInValuta = 0;

			while($data = $DB1->NextRecord())
			{

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];

				if($this->pdf->rapport_VHO_percentageTotaal ==1)
				{
					$percentageVanTotaal  = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
					$percentageVanTotaalTekst = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VHO_decimaal_proc)."%";
				}
				else
					$percentageVanTotaalTekst = "";

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

				if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
				{
				  if($this->pdf->rapport_layout == 14)
					  $this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												'',
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
												'','',$percentageVanTotaalTekst));
					else
										  $this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
												$percentageVanTotaalTekst));
				}
				else
				{
					$this->pdf->row(array("",
												"",
												"",
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
												"",
												"",
												"",
												"",
												$percentageVanTotaalTekst));
				}

			}
		}


		if($this->pdf->rapport_VHO_percentageTotaal ==1)
		{
			$percentageVanTotaal = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
		}
		else
			$percentageVanTotaal = 0;

		// totaal liquiditeiten
		$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,$percentageVanTotaal,"","");


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		if($this->pdf->rapport_VHO_percentageTotaal ==1)
		{
			$percentageVanTotaal = 100;
		}
		else
			$percentageVanTotaal = 0;


		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille, $percentageVanTotaal,"","","",true);


		$this->pdf->ln();

		if($this->pdf->rapport_VHO_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VHO_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}


		if($this->pdf->rapport_VHO_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 && $this->pdf->rapport_VHO_indexUit == 0)
		{
		  if(!$this->pdf->rapport_VHO_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

	}
}
?>