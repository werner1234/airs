<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/15 16:38:19 $
File Versie					: $Revision: 1.3 $

$Log: RapportMOD_L70.php,v $
Revision 1.3  2020/07/15 16:38:19  rvv
*** empty log message ***

Revision 1.2  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.1  2016/10/02 12:38:59  rvv
*** empty log message ***

Revision 1.13  2014/01/11 15:50:39  rvv
*** empty log message ***

Revision 1.12  2011/02/24 17:39:12  rvv
*** empty log message ***

Revision 1.11  2009/08/26 12:04:57  rvv
*** empty log message ***

Revision 1.10  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.9  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.8  2006/11/03 11:24:04  rvv
Na user update

Revision 1.7  2006/10/31 12:04:41  rvv
Voor user update

Revision 1.6  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.5  2006/01/25 10:29:32  jwellner
bugfix Modelcontrole

Revision 1.4  2006/01/23 14:13:43  jwellner
no message

Revision 1.3  2005/12/09 12:16:51  jwellner
ajax lib toegevoegd.

Revision 1.2  2005/12/08 14:05:29  jwellner
Modelcontrole rapport

Revision 1.1  2005/12/08 13:57:05  jwellner
Modelcontrole rapport
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportMOD_L70
{
	function RapportMOD_L70($pdf, $portefeuille, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MOD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_naam1 = str_replace("Modelportefeuille ","",$this->pdf->rapport_naam1);

		if($this->pdf->rapport_layout == 5)
		{
		  $db=new DB();
		  $query="SELECT Portefeuille, Omschrijving FROM ModelPortefeuilles WHERE Portefeuille='".$this->pdf->selectData['mutatieportefeuille_portefeuille']."'";
		  $db->SQL($query);
		  $modelNaam=$db->lookupRecord();
		  if($modelNaam['Omschrijving'] <> '')
		    $modelNaam=$modelNaam['Omschrijving'];
		  else
		    $modelNaam=$this->pdf->selectData['mutatieportefeuille_portefeuille'];

		  $this->pdf->rapport_koptext = "Portefeuille voorstel\n$modelNaam\n".$this->pdf->selectData[mutatieportefeuille_customNaam];
		}
		else
	  	$this->pdf->rapport_koptext = $this->pdf->selectData[mutatieportefeuille_customNaam];


		$this->pdf->rapport_titel = "Portefeuille voorstel";
		//$this->pdf->rapport_logo = "";

		$this->portefeuille = $portefeuille;

		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB, $procent)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_decimaal);

		$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
		if(!empty($totaalA))
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());

		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_omschr_fontcolor[r],$this->pdf->rapport_subtotaal_omschr_fontcolor[g],$this->pdf->rapport_subtotaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($begin,4, $title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_fontcolor[r],$this->pdf->rapport_subtotaal_fontcolor[g],$this->pdf->rapport_subtotaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
		if($this->pdf->rapport_inprocent == 1)
			$procenttxt = $this->formatGetal($procent,2)." %";
		$this->pdf->Cell($this->pdf->widthB[6],4,$procenttxt, 0,1, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
	}

	function printTotaal($title, $totaalA, $totaalB, $procent, $grandtotaal = false)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

		// lege regel
		$this->pdf->ln();

		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_decimaal);

		$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());

		if(!empty($totaalA))
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());

		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
		if($this->pdf->rapport_inprocent == 1)
			$procenttxt = $this->formatGetal($procent,2)." %";
		$this->pdf->Cell($this->pdf->widthB[6],4,$procenttxt, 0,1, "R");


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
			if(!empty($totaalA))
			{
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
			}
			$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			$this->pdf->Line($begin,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
		}
		else
		{
			$this->pdf->setDash(1,1);
			if(!empty($totaalA))
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);

		$this->pdf->excelData[] = array($title);
	}

	function writeRapport()
	{
		global $__appvar;
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(10,55,20,25,30,30,20);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(65,20,25,30,30,20);
		$this->pdf->alignA = array('L','R','R','R','R','R');

		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$DB = new DB();
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

		$query = "SELECT Beleggingscategorien.Omschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, ".
		" Valutas.Omschrijving AS ValutaOmschrijving".
		" FROM TijdelijkeRapportage ".
		" LEFT JOIN Valutas ON (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
		" LEFT JOIN Beleggingscategorien ON (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$this->pdf->excelData[] = array("",
														 "Fondsomschrijving",
														 "Aantal",
														 "Fondskoers",
														 "Fondstotaal",
														 "Fondstotaal EUR",
														 "Perc. %");

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie) )
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);

				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $percentageVanTotaal);
				$totaalbegin = 0;
				$totaalactueel = 0;
			}

			if($lastCategorie <> $categorien[Omschrijving])
			{
				$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
			}
			// subkop (valuta)
			$this->printKop(vertaalTekst($this->pdf->rapport_valuta_voorzet,$this->pdf->rapport_taal)." ".$categorien[valuta], $this->pdf->rapport_kop4_fontstyle);

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.fondsEenheid, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.actueleFonds, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			 .$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
			while($subdata = $DB2->NextRecord())
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving]);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

				$percentageVanTotaal = $subdata[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);

				$this->pdf->row(array("",
													"",
													$this->formatGetal($subdata[totaalAantal],0),
													$this->formatGetal($subdata[actueleFonds],2),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_decimaal),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_decimaal),
													($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));


				$this->pdf->excelData[] = array("",
																 $subdata[fondsOmschrijving],
																 $subdata[totaalAantal],
																 round($subdata[actueleFonds],2),
																 round($subdata[actuelePortefeuilleWaardeInValuta],2),
																 round($subdata[actuelePortefeuilleWaardeEuro],2),
																 round($percentageVanTotaal,2));

				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];
				$valutaOmschrijving[$categorien[valuta]] = $categorien[ValutaOmschrijving];
			}

			// print categorie footers
			$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);

			$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), "", $categorien[subtotaalactueel], $percentageVanTotaal);

			// totaal op categorie tellen
			$totaalbegin += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];

			$lastCategorie = $categorien[Omschrijving];
		}

		// totaal voor de laatste categorie
		$percentageVanTotaal 				 = $totaalactueel / ($totaalWaarde/100);

		$actueleWaardePortefeuille  += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), "", $totaalactueel,$percentageVanTotaal);



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
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
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
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$this->printKop("Waarden ".$categorien[valuta],$this->pdf->rapport_kop4_fontstyle);

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
						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving].$rentePeriodetxt);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

						$percentageVanTotaal = $subdata[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);

						$this->pdf->row(array("","","","",
																	$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_decimaal),
																	$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_decimaal),
																	($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));

						$this->pdf->excelData[] = array("",
																 $subdata[fondsOmschrijving],
																 "",
																 "",
																 round($subdata[actuelePortefeuilleWaardeInValuta],2),
																 round($subdata[actuelePortefeuilleWaardeEuro],2),
																 round($percentageVanTotaal,2));
					}

					// print subtotaal
					$percentageVanTotaal = $subtotaalRenteInValuta / ($totaalWaarde/100);
					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), "", $subtotaalRenteInValuta, $percentageVanTotaal);

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}

			}

			// totaal op rente
			$percentageVanTotaal = $totaalRenteInValuta / ($totaalWaarde/100);
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$percentageVanTotaal);
		}

		// Liquiditeiten
		$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
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

		$totaalLiquiditeitenInValuta = 0;

		while($data = $DB1->NextRecord())
		{
			$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
			$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
			$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);

			$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);

			$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];

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

			$percentageVanTotaal = $data[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);

			$this->pdf->row(array("",
										"",
										"",
										"",
										$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_decimaal),
										$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_decimaal),
										($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));

			$this->pdf->excelData[] = array("",
													 $omschrijving,
													 "",
													 "",
													 round($data[actuelePortefeuilleWaardeInValuta],2),
													 round($data[actuelePortefeuilleWaardeEuro],2),
													 round($percentageVanTotaal,2));

		}
		// totaal liquiditeiten
		$percentageVanTotaal = $totaalLiquiditeitenEuro / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro, $percentageVanTotaal);

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
	  		alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,true);

		$this->pdf->ln();

		if($this->pdf->rapport_MOD_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_MOD_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		if($this->pdf->selectData['mutatieportefeuille_afm'])
		{
			$this->pdf->ln();
			$afm=AFMstd($this->portefeuille, $this->rapportageDatum);
			$this->pdf->Cell(150,4,"De AFM Standaarddeviatie voor deze portefeuille bedraagt ".$this->formatGetal($afm['std'],2).'%');

		}
	}
}
?>