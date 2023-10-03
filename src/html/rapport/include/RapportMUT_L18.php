<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/28 09:58:52 $
File Versie					: $Revision: 1.3 $

$Log: RapportMUT_L18.php,v $
Revision 1.3  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.2  2010/07/04 15:24:39  rvv
*** empty log message ***

Revision 1.1  2008/09/15 08:04:05  rvv
*** empty log message ***

Revision 1.15  2007/11/22 11:36:49  rvv
att aanpassing backoffice

Revision 1.14  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.13  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.12  2006/02/07 11:06:28  jwellner
- bugfix valuta in mutatievoorstel fondsen
- bugfix in MUT / TRANS layout 8

Revision 1.11  2006/02/03 08:00:27  jwellner
diverse bugfixen

Revision 1.10  2006/01/23 14:13:43  jwellner
no message

Revision 1.9  2006/01/03 15:41:53  cvs
kolom van 15 naar 19 breed

Revision 1.8  2005/11/17 07:25:02  jwellner
no message

Revision 1.7  2005/10/26 11:47:39  jwellner
no message

Revision 1.6  2005/10/21 08:08:56  jwellner
lock file bij complete database updates

Revision 1.5  2005/10/19 11:18:23  jwellner
focus op 1e veld in formulier bij editForm

Revision 1.4  2005/09/29 15:00:18  jwellner
no message

Revision 1.3  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.2  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.2  2005/07/12 07:09:50  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportMutatieoverzichtLayout.php");

class RapportMUT_L18
{
	function RapportMUT_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
			$this->pdf->rapport_header = array('','Periode',"Bank Afschrift","Omschrijving","Boekdatum","Rekening","","Debet","Credit");

			  $this->pdf->SetTextColor($this->pdf->rapport_style['fonds']['fontcolor']['r'],$this->pdf->rapport_style['fonds']['fontcolor']['g'],$this->pdf->rapport_style['fonds']['fontcolor']['b']);


	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{


		$totaal1 = $this->pdf->marge + $this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] + $this->pdf->widths[3] + $this->pdf->widths[4] + $this->pdf->widths[5]+ $this->pdf->widths[6];
		$totaal2 = $totaal1 + $this->pdf->widths[7];

		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		$this->pdf->SetX($totaal1 - $this->pdf->widths[6]);

		//$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widths[6],$this->pdf->rowHeight,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widths[7],$this->pdf->rowHeight,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widths[8],$this->pdf->rowHeight,$totaalBtxt, 0,0, "R");

		//$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		return true;
	}

	function printTotaal($title, $totaalA, $totaalB, $grandtotal=false)
	{

		$totaal1 = $this->pdf->marge + $this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] + $this->pdf->widths[3] + $this->pdf->widths[4] + $this->pdf->widths[5]+ $this->pdf->widths[6];
		$totaal2 = $totaal1 + $this->pdf->widthA[7];

		$this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widths[7],$this->pdf->GetY());
		$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widths[8],$this->pdf->GetY());

		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}
		else
		{
			$totaalAtxt = $this->formatGetal($totaalB,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}
		else
		{
			$totaalBtxt = $this->formatGetal($totaalA,2);
		}

		$this->pdf->SetX($totaal1 - $this->pdf->widths[6]- $this->pdf->widths[5]);

	//	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widths[5],$this->pdf->rowHeight,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widths[6],$this->pdf->rowHeight,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widths[7],$this->pdf->rowHeight,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widths[8],$this->pdf->rowHeight,$totaalBtxt, 0,0, "R");

	//	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		if($grandtotal)
		{
			$this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widths[7],$this->pdf->GetY());
			$this->pdf->Line($totaal1+2,$this->pdf->GetY()+1,$totaal1 + $this->pdf->widths[7],$this->pdf->GetY()+1);
			$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widths[8],$this->pdf->GetY());
			$this->pdf->Line($totaal2+2,$this->pdf->GetY()+1,$totaal2 + $this->pdf->widths[8],$this->pdf->GetY()+1);

		}
		else
		{
			$this->pdf->setDash(1,1);
			$this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widths[7],$this->pdf->GetY());
			$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widths[8],$this->pdf->GetY());
			$this->pdf->setDash();

		}
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

	//	$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetX($this->pdf->marge+$this->pdf->widths[0]);
		$this->pdf->MultiCell(90,4, $title, 0, "L");

	}

	function writeRapport()
	{

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		// voor data
		$this->pdf->widthA = array(15,19,60,20,30,20,25,25,70);
		$this->pdf->alignA = array('R','R','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(15,19,60,20,30,20,25,25,70);
		$this->pdf->alignB = array('R','R','L','R','R','R','R','R');

		if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData['maandrapportage'] == 1 || $this->pdf->selectData['kwartaalrapportage'] == 1) )
		{
			$maand = date("n",db2jul($this->rapportageDatum));
			$kwartaal = floor(($maand / 4)+1);
			switch($kwartaal)
			{
				case 1 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-01-01";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 2 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-03-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 3 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-06-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 4 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-09-30";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
			}
		}

		$this->pdf->AddPage();
		//$this->pdf->switchFont('fonds');

		// loopje over Grootboekrekeningen Opbrengsten = 1

		if($this->pdf->selectData[GrootboekTm])
			$extraquery .= " AND (Rekeningmutaties.Grootboekrekening >= '".$this->pdf->selectData[GrootboekVan]."' AND Rekeningmutaties.Grootboekrekening  <= '".$this->pdf->selectData[GrootboekTm]."') ";


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
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".$extraquery.
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') ".
			"ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($mutaties = $DB->nextRecord())
		{
			// print totaal op hele categorie.
			if($lastCategorie <> $mutaties[gbOmschrijving] && !empty($lastCategorie) )
			{
				if($subcredit > $subdebet)
				{
					$verschildebet = $subcredit - $subdebet;
					$verschilcredit = 0;
				}
				else
				{
					$verschilcredit = $subdebet - $subcredit;
					$verschildebet = 0;
				}

				$this->printSubTotaal(vertaalTekst("Saldo",$this->pdf->rapport_taal),$verschildebet, $verschilcredit);
				$subdebet  += $verschildebet ;
				$subcredit += $verschilcredit;

				$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $subdebet, $subcredit);
				$subdebet = 0;
				$subcredit = 0;
			}

			if($lastCategorie <> $mutaties[gbOmschrijving])
			{
				$this->printKop(vertaalTekst($mutaties[gbOmschrijving],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
			//	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			}

			$debet	= abs($mutaties[Debet]) * $mutaties[Valutakoers];
			$credit	= abs($mutaties[Credit]) * $mutaties[Valutakoers];

			$subdebet += $debet;
			$subcredit += $credit;

			if($debet <> 0)
				$debettxt = $this->formatGetal($debet,2);
			else
				$debettxt = "";

			if($credit <> 0)
				$credittxt = $this->formatGetal($credit,2);
			else
				$credittxt = "";



			$this->pdf->row(array('',date("n",db2jul($mutaties['Boekdatum'])),
											$mutaties['Afschriftnummer'],
											vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal),
											date("d-m-Y",db2jul($mutaties['Boekdatum'])),
											$mutaties[Rekening],
											"",
											$debettxt,
											$credittxt));

			$totaalcredit += $credit;
			$totaaldebet += $debet;
			$lastCategorie = $mutaties[gbOmschrijving];
		}

		if($subcredit > $subdebet)
		{
			$verschildebet = $subcredit - $subdebet;
			$verschilcredit = 0;
		}
		else
		{
			$verschilcredit = $subdebet - $subcredit;
			$verschildebet = 0;
		}
		$this->printSubTotaal(vertaalTekst("Saldo",$this->pdf->rapport_taal),$verschildebet, $verschilcredit);

		$subdebet  += $verschildebet ;
		$subcredit += $verschilcredit;

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $subdebet, $subcredit);


		$totaal = $actueleWaardePortefeuille;
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal Generaal",$this->pdf->rapport_taal), $totaal, $totaal,true);
	}
}
?>