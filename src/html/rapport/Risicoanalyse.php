<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.15 $

$Log: Risicoanalyse.php,v $
Revision 1.15  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.14  2010/11/17 17:16:33  rvv
*** empty log message ***

Revision 1.13  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.12  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.11  2007/04/03 13:26:33  rvv
*** empty log message ***

Revision 1.10  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.9  2006/11/03 11:24:04  rvv
Na user update

Revision 1.8  2006/10/31 12:12:15  rvv
Voor user update

Revision 1.7  2006/06/09 13:50:38  jwellner
*** empty log message ***

Revision 1.6  2005/11/07 10:29:17  jwellner
no message

Revision 1.5  2005/11/01 11:20:08  jwellner
diverse aanpassingen

Revision 1.4  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.3  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.2  2005/09/07 11:09:42  jwellner
oplevering 07 08 2005

Revision 1.1  2005/09/07 07:33:23  jwellner
no message


*/

include_once("rapportRekenClass.php");

class Risicoanalyse
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;


	function Risicoanalyse( $selectData) {

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFRapport('L','mm');
		$this->pdf->rapport_type = "geaggregeerd";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->rapport_type = "Risico";
		$this->pdf->rapport_datum = $this->selectData[datumTm];
		$this->pdf->rapport_titel = "Risico-analyse";

		$this->orderby  = " Portefeuilles.Portefeuille ";

		$this->pdf->excelData = array();
	}

	function printTotaal($title, $totaalA, $procent)
	{

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3];

		$proc = $actueel + $this->pdf->widthB[4] + $this->pdf->widthB[5]+ $this->pdf->widthB[6];

		$this->pdf->Line($proc+2,$this->pdf->GetY(),$proc + $this->pdf->widthB[7],$this->pdf->GetY());


		if(!empty($totaalA))
		{
			if($this->pdf->rapport_OIB_specificatie == 1)
				$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_OIB_decimaal);
		}

		$totaalprtxt = $this->formatGetal($procent,2);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,$totaalprtxt, 0,1, "R");

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

		if(($this->pdf->GetY() + 12) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();
		$this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
		$this->pdf->SetY($y);
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writePage($portefeuille, $einddatum)
	{
		global $__appvar;

		if($this->pdf->rapport_riscoPerfonds)
			$this->selectData[risicoMethode] = "perFonds";

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(30,40,25,25,25,25,15,25,77);
		$this->pdf->alignB = array('L','L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(30,40,25,25,25,25,15,25,75);
		$this->pdf->alignA = array('L','L','L','R','R','R','R','R');

		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 " FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$einddatum."' AND ".
						 " portefeuille = '".$portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;



		if($this->selectData[risicoMethode] == "perFonds")
		{
			$query = "SELECT Beleggingscategorien.Omschrijving, ".
				" BeleggingscategoriePerFonds.RisicoPercentageFonds, ".
				" Valutas.Omschrijving AS ValutaOmschrijving, ".
				" Fondsen.Omschrijving AS FondsOmschrijving, ".
				" TijdelijkeRapportage.valuta, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.beleggingscategorie, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
				" FROM (TijdelijkeRapportage, Portefeuilles, BeleggingscategoriePerFonds)  ".
				" LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
				" LEFT JOIN Fondsen on (TijdelijkeRapportage.fonds = Fondsen.Fonds)  ".
				" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
				" WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
				" Portefeuilles.Portefeuille = TijdelijkeRapportage.portefeuille AND ".
				" Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder AND ".
				" BeleggingscategoriePerFonds.Fonds = TijdelijkeRapportage.fonds  AND ".
				" TijdelijkeRapportage.type = 'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$einddatum."'"
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.fonds, TijdelijkeRapportage.valuta ".
				" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		}
		else
		{
			$query = "SELECT Beleggingscategorien.Omschrijving, ".
				" Beleggingscategorien.RisicoEUR, ".
				" Beleggingscategorien.RisicoVV, ".
				" Valutas.Omschrijving AS ValutaOmschrijving, ".
				" TijdelijkeRapportage.valuta, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.beleggingscategorie, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
				" FROM TijdelijkeRapportage ".
				" LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
				" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
				" WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$einddatum."'"
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
				" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		}
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
				$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $risicoSubtotaal);
				$totaalbegin = 0;
				$totaalactueel = 0;
				$risicoSubtotaal = 0;
			}

			if($lastCategorie <> $categorien[Omschrijving])
			{
				$this->printKop($categorien[Omschrijving], $this->pdf->rapport_kop3_fontstyle);
			}

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->Cell($this->pdf->widthB[0],4,"");
			$this->pdf->Cell($this->pdf->widthB[1],4,$categorien[FondsOmschrijving]);
			$this->pdf->Cell($this->pdf->widthB[2],4,$categorien[ValutaOmschrijving]);

			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			// print categorie footers

			if($this->selectData[risicoMethode] == "perFonds")
			{
				$risico = $categorien[RisicoPercentageFonds];
			}
			else
			{
				if($categorien[valuta] == "EUR")
					$risico = $categorien[RisicoEUR];
				else
					$risico = $categorien[RisicoVV];
			}

			$risicoBedrag = (ABS($categorien[subtotaalactueel]) / 100) * $risico;

			$this->pdf->row(array("",
										"",
										"",
										$this->formatGetal($categorien[subtotaalactueelvaluta],$this->pdf->rapport_OIB_decimaal),
										$this->formatGetal($categorien[subtotaalactueel],$this->pdf->rapport_OIB_decimaal),
										"",
										$this->formatGetal($risico,0)."",
										$this->formatGetal($risicoBedrag,2)));

			// totaal op categorie tellen
			$totaalinvaluta 	+= $categorien[subtotaalactueelvaluta];
			$totaalactueel 		+= $categorien[subtotaalactueel];
			$risicoSubtotaal 	+= $risicoBedrag;
			$risicoTotaal 		+= $risicoBedrag;
			$lastCategorie 		 = $categorien[Omschrijving];
		}

		// totaal voor de laatste categorie
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $risicoSubtotaal);
		// voor Pie

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" Valutas.Omschrijving AS ValutaOmschrijving, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalactueelvaluta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  ".
		" AND TijdelijkeRapportage.rapportageDatum = '".$einddatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{
			$this->printKop("Opgelopen Rente",$this->pdf->rapport_kop3_fontstyle);

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{

				$subtotaalRenteInValuta = 0;
				// print valutaomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,"");
				$this->pdf->Cell($this->pdf->widthB[2],4,$categorien[ValutaOmschrijving]);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
											"",
											"",
											$this->formatGetal($categorien[subtotaalactueelvaluta],$this->pdf->rapport_OIB_decimaal),
											$this->formatGetal($categorien[subtotaalactueel],$this->pdf->rapport_OIB_decimaal),
											"",
											$this->formatGetal(0,0),
											$this->formatGetal(0,0)));

				// print subtotaal
				// $this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
				$totaalRente += $categorien[subtotaalactueel];
			}

			// totaal op rente
			$actueleWaardePortefeuille += $this->printTotaal(" ", $totaalRente, 0);
		}


		// Liquiditeiten
		$this->printKop("Liquiditeiten",$this->pdf->rapport_kop3_fontstyle);


		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" Valutas.Omschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage, Valutas WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  AND ".
			" TijdelijkeRapportage.valuta = Valutas.valuta AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$einddatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.valuta ".
			" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		while($data = $DB1->NextRecord())
		{
			$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->Cell($this->pdf->widthB[0],4,"");
			$this->pdf->Cell($this->pdf->widthB[1],4,"");
			$this->pdf->Cell($this->pdf->widthB[2],4,$data[ValutaOmschrijving]);

			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


			$this->pdf->row(array("",
										"",
										"",
										$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_OIB_decimaal),
										$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIB_decimaal),
										"",
										$this->formatGetal(0,0),
										$this->formatGetal(0,2)));


		}
		// totaal liquiditeiten
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalLiquiditeitenEuro, 0);

		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3]+ $this->pdf->widthB[4];
		$proc = $actueel + $this->pdf->widthB[5]  + $this->pdf->widthB[6];

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[5],$this->pdf->GetY());
		$this->pdf->Line($proc+2,$this->pdf->GetY(),$proc + $this->pdf->widthB[7],$this->pdf->GetY());

		$this->pdf->setX($this->pdf->marge);

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[1],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[3],4,"Totale actuele waarde portefeuille", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "L");
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[5],4,$this->formatGetal($actueleWaardePortefeuille,$this->pdf->rapport_OIB_decimaal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[7],4,$this->formatGetal($risicoTotaal,2), 0,0, "R");


		// print risico score
		$risicoScore = $risicoTotaal / ($actueleWaardePortefeuille/100);

		$this->pdf->ln(12);
		$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[1],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[3],4,"Risico-score portefeuille", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "L");
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[5],4,$this->formatGetal($risicoScore,1), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,"", 0,0, "R");


		// print risico klasse portefeuille.
		$query = "SELECT  ".
		" Risicoklassen.Risicoklasse, ".
		" Risicoklassen.Minimaal, ".
		" Risicoklassen.Maximaal ".
		" FROM Risicoklassen, Portefeuilles WHERE ".
		" Risicoklassen.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
		" Portefeuilles.Portefeuille = '".$portefeuille."' AND ".
		" Portefeuilles.Risicoklasse = Risicoklassen.Risicoklasse " ;

		$DB3 = new DB();
		$DB3->SQL($query);
		$DB3->Query();
		$risicodata = $DB3->nextRecord();

		$this->pdf->ln(12);
		$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[1],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[3],4,"Risicoklasse portefeuille", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "L");
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[5],4,$risicodata[Risicoklasse], 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,"Min. ".$risicodata[Minimaal], 0,1, "R");

		$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[1],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[3],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[5],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[6],4,"Max. ".$risicodata[Maximaal], 0,1, "R");


		$this->pdf->ln();
	}

	function writeRapport()
	{

		$einddatum = jul2sql($this->selectData[datumTm]);

		$this->pdf->__appvar = $this->__appvar;

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


		foreach ($portefeuilles as $pdata)
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			// set portefeuillenr
			// load settings.
			$portefeuille = $pdata['Portefeuille'];
			$this->pdf->portefeuille = $pdata['Portefeuille'];
			$fondswaarden =  berekenPortefeuilleWaardeQuick($portefeuille,  $einddatum);
			vulTijdelijkeTabel($fondswaarden ,$portefeuille, $einddatum);

			loadLayoutSettings($this->pdf, $pdata['Portefeuille']);

			$this->writePage($pdata['Portefeuille'],$einddatum);

			verwijderTijdelijkeTabel($portefeuille, $this->selectData['datumTm']);
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}

}
?>