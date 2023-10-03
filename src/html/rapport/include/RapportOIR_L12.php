<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/02/17 15:00:01 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportOIR_L12.php,v $
 		Revision 1.1  2019/02/17 15:00:01  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2011/06/25 16:51:45  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2009/01/20 17:44:09  rvv
 		*** empty log message ***

 		Revision 1.6  2008/06/30 07:58:44  rvv
 		*** empty log message ***

 		Revision 1.5  2007/03/27 14:58:20  rvv
 		VreemdeValutaRapportage

 		Revision 1.4  2007/01/31 16:20:27  rvv
 		*** empty log message ***

 		Revision 1.3  2006/11/03 11:24:04  rvv
 		Na user update

 		Revision 1.2  2006/10/31 12:15:25  rvv
 		Voor user update

 		Revision 1.1  2006/10/20 14:55:53  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIR_L12
{
	function RapportOIR_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIR_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIR_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in regio.";


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

	function printSubTotaal($title, $totaalA, $totaalB)
	{

		$this->pdf->SetLineWidth($this->pdf->lineWidth);


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[6],$this->pdf->GetY());


		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_OIR_decimaal);

		$this->pdf->SetX($actueel-$this->pdf->widthB[4]);
		$this->pdf->Cell($this->pdf->widthB[4],4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,1, "R");
		$this->pdf->ln();
	}

	function printTotaal($title, $totaalA, $totaalB, $grandtotaal = false)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);


		// lege regel
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] ;

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_OIR_decimaal);

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[5],$this->pdf->GetY());

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($actueel-$this->pdf->widthB[5],4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5] ,4, "", 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		//$this->pdf->Cell($this->pdf->widthB[6],4, "", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,$totaalBtxt, 0,1, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[5],$this->pdf->GetY());
			$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[5],$this->pdf->GetY()+1);
		}


		$this->pdf->ln();

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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);



			if($procent <> 0)
				$procenttxt = $this->formatGetal($procent,0)." %";
			else
				$procenttxt = "";


		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell($this->pdf->widthA[0],4, $procenttxt.' '.$title, 0,0, "L");
    $this->pdf->line($this->pdf->marge,$this->pdf->getY()+4,$this->pdf->widthA[0],$this->pdf->getY()+4);
		
	}

	function writeRapport()
	{
		global $__appvar;
		// voor data

    $this->pdf->widthA = array(70,5,86,30,30,30,30);
    $this->pdf->widthB = $this->pdf->widthA;
		$this->pdf->alignA = array('L','L','L','R','R','R','R','R');
    $this->pdf->alignB = $this->pdf->alignA ;

		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

$query = "SELECT
TijdelijkeRapportage.regioOmschrijving as Omschrijving,
TijdelijkeRapportage.Regio,
SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel
FROM (TijdelijkeRapportage,Valutas)
WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
TijdelijkeRapportage.valuta = Valutas.Valuta AND
TijdelijkeRapportage.type = 'fondsen' AND
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
.$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY TijdelijkeRapportage.Regio
ORDER BY TijdelijkeRapportage.Regio asc";

		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $lastCategorie2='';
    $vorigeOmschrijving='';
		while($categorien = $DB->NextRecord())
		{


if ($categorien['Omschrijving'] == "")
 $categorien['Omschrijving'] = "Nog geen regio";

			if($lastCategorie2 <> $categorien['Omschrijving'])
			{
				$percentageVanTotaal = $categorien['subtotaalactueel']/ ($totaalWaarde/100);

				if($hoofdcategorie['Omschrijving'] <> $vorigeOmschrijving)
				{
					$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
					$this->pdf->SetX($this->pdf->marge);
					$this->pdf->Cell($this->pdf->widthB[0],4, vertaalTekst($hoofdcategorie['Omschrijving'],$this->pdf->rapport_taal) , 0,1, "L");
					
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				}

				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal),$percentageVanTotaal, "b");

				$vorigeOmschrijving = $hoofdcategorie['Omschrijving'];
			}


			// subkop (valuta)

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.beleggingssector, ".
			" TijdelijkeRapportage.hoofdsector as Hoofdsector, ".
			" TijdelijkeRapportage.beleggingssectorOmschrijving AS secOmschrijving, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.actueleFonds, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage ".
			" WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.Regio =  '".$categorien['Regio']."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.id ".
			" ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();


			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

			while($subdata = $DB2->NextRecord())
			{
			  $percentageVanTotaal=$subdata['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100;
        $this->pdf->setX($this->pdf->marge);
				$this->pdf->row(array("",
												"",
												$subdata['fondsOmschrijving'],
												$this->formatGetal($subdata['totaalAantal'],0),
												$this->formatGetal($subdata['actueleFonds'],2),
                          $this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIR_decimaal),
                          $this->formatGetalKoers($percentageVanTotaal,$this->pdf->rapport_OIR_decimaal)));

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$lastCategorie = $subdata['secOmschrijving'];
			}



			// print categorie footers
			$this->printSubTotaal("Subtotaal:", $categorien['subtotaalbegin'], $categorien['subtotaalactueel']);

			// totaal op categorie tellen
			$totaalbegin += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$lastCategorie2 = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.RenteBerekenen ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

			$q = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS rentetotaal FROM TijdelijkeRapportage ".
					" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($q,__FILE__,__LINE__);

			$DB3 = new DB();
			$DB3->SQL($q);
			$DB3->Query();
			$subtotaal = $DB3->nextRecord();
			$subtotaal = $subtotaal['rentetotaal'];


			if($this->pdf->rapport_OIR_onderverdelingAandeel)
			{
				$percentageVanTotaal = 0;
				$percentageVanTotaaltxt = "";
			}
			else
			{
				$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";
			}

			//$percentageVanTotaal = $categorien[subtotaalactueel]/ ($totaalWaarde/100);
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$percentageVanTotaal ,"b");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{

				if(!$this->pdf->rapport_OIR_geenrentespec)
				{

					$subtotaalRenteInValuta = 0;
					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);

					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();


					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

					while($subdata = $DB2->NextRecord())
					{
						if($this->pdf->rapport_OIR_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata[rentedatum]));
							if($subdata[renteperiode] <> 12 && $subdata[renteperiode] <> 0)
								$rentePeriodetxt .= " / ".$subdata[renteperiode];
						}

						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];
						$this->pdf->row(array("","",$subdata[fondsOmschrijving].$rentePeriodetxt,"","","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIR_decimaal)));
					}

					// print subtotaal
					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}
			}

			// totaal op rente
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("",$this->pdf->rapport_taal), "", $totaalRenteInValuta);
		}

		// Liquiditeiten
		$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS liqtotaal FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rekening'  AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($q,__FILE__,__LINE__);

		$DB3 = new DB();
		$DB3->SQL($q);
		$DB3->Query();
		$subtotaal = $DB3->nextRecord();
		$subtotaal = $subtotaal['liqtotaal'];

		if($this->pdf->rapport_OIR_onderverdelingAandeel)
		{
			$percentageVanTotaal = 0;
			$percentageVanTotaaltxt = "";
		}
		else
		{
			$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";
		}

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.valuta  asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		if($DB1->records() > 0)
		{
			//$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			//$this->pdf->SetX($this->pdf->marge);
			//$this->pdf->Cell($this->pdf->widthB[0],4, vertaalTekst("LIQUIDITEITEN",$this->pdf->rapport_taal), 0,1, "L");
			//$this->pdf->SetFont($font,$fonttype,$fontsize);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$percentageVanTotaal,"b");

			while($data = $DB1->NextRecord())
			{
				if($this->pdf->rapport_OIR_liquiditeiten_omschr)
					$this->pdf->rapport_liquiditeiten_omschr = $this->pdf->rapport_OIR_liquiditeiten_omschr;

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = str_replace("{Rekening}",$data[rekening],$omschrijving);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = str_replace("{Valuta}",$data[valuta],$omschrijving);

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];


				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->row(array("",
												"",
												$omschrijving,
												"",
												"",
												$this->formatGetalKoers($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIR_decimaal)));

			}
			// totaal liquiditeiten
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("",$this->pdf->rapport_taal), "", $totaalLiquiditeitenEuro);
		}

		// check op totaalwaarde!

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		$actueleWaardePortefeuille = $totaalWaarde;
		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,true);

		$this->pdf->ln();

/*
		if($this->pdf->rapport_OIR_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIR_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}


		if($this->pdf->rapport_OIR_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
*/
	}
}
?>