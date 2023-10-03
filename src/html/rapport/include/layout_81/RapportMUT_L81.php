<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:46:04 $
File Versie					: $Revision: 1.2 $

$Log: RapportMUT_L81.php,v $
Revision 1.2  2020/03/18 17:46:04  rvv
*** empty log message ***

Revision 1.1  2018/12/27 15:11:17  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportMutatieoverzichtLayout.php");

class RapportMUT_L81
{
	function RapportMUT_L81($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
		$totaal2 = $totaal1 + $this->pdf->widthA[6];
	  $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
	 	$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());

		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		$this->pdf->SetX($totaal1 - $this->pdf->widthA[5]);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[5],4,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[7],4,$totaalBtxt, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		return true;
	}

	function printTotaal($title, $totaalA, $totaalB, $grandtotal=false)
	{

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
		$totaal2 = $totaal1 + $this->pdf->widthA[6];

//		$this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
//		$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());

		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		$this->pdf->SetX($totaal1 - $this->pdf->widthA[5]- $this->pdf->widthA[4]);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[4],4,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[5],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[7],4,$totaalBtxt, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		if($grandtotal)
		{
		  if(!empty($totaalA))
		  {
		    $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
		 	  $this->pdf->Line($totaal1+2,$this->pdf->GetY()+1,$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY()+1);
		  }
		  if(!empty($totaalB))
		  {
		    $this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
		  	$this->pdf->Line($totaal2+2,$this->pdf->GetY()+1,$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY()+1);
		  }
		  $this->pdf->ln();
		}
		else
		{
			$this->pdf->setDash(1,1);
			if(!empty($totaalA))
			  $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
			if(!empty($totaalB))
		  	$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
			$this->pdf->setDash();
			$this->pdf->ln();
		}
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
    if($this->pdf->GetY()+9 > $this->pdf->PageBreakTrigger )
        $this->pdf->AddPage();
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
		$this->pdf->widthA = array(20,5,110,25,30,20,25,25,21);
		$this->pdf->alignA = array('R','R','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(20,5,110,25,30,20,25,25,21);
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
		$this->pdf->templateVars['MUTPaginas']=$this->pdf->page;

		// loopje over Grootboekrekeningen Opbrengsten = 1

		if($this->pdf->selectData[GrootboekTm])
			$extraquery .= " AND (Rekeningmutaties.Grootboekrekening >= '".$this->pdf->selectData[GrootboekVan]."' AND Rekeningmutaties.Grootboekrekening  <= '".$this->pdf->selectData[GrootboekTm]."') ";


		foreach ($this->pdf->lastPOST as $key=>$value)
		{
		  if(substr($key,0,4)=='MUT_' && $value==1)
		  {
		    $grootboeken[]=substr($key,4);
		    $filter = 1;
		  }
		}

		if($filter == 1)
		{
		 $grootboekSelectie = implode('\',\'',$grootboeken);
	   $extraquery .= "AND Rekeningmutaties.Grootboekrekening IN('$grootboekSelectie')  ";
		}

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

		$DB->SQL($query);
		$DB->Query();
		while($mutaties = $DB->nextRecord())
		{
			// print totaal op hele categorie.
			if($lastCategorie <> $mutaties['gbOmschrijving'] && !empty($lastCategorie) )
			{


				$this->printSubTotaal(vertaalTekst("Saldo",$this->pdf->rapport_taal),$subdebet, $subcredit);

				$totaal=$subcredit-$subdebet;
				if($totaal < 0)
					$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), abs($totaal), 0);
				else
				 	$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), 0, abs($totaal));
				$subdebet = 0;
				$subcredit = 0;
			}

			if($lastCategorie <> $mutaties['gbOmschrijving'])
			{
				$this->printKop(vertaalTekst($mutaties['gbOmschrijving'],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			}

			$debet	= abs($mutaties['Debet']) * $mutaties['Valutakoers'];
			$credit	= abs($mutaties['Credit']) * $mutaties['Valutakoers'];

			$subdebet += round($debet,2);
			$subcredit += round($credit,2);

			if($debet <> 0)
				$debettxt = $this->formatGetal($debet,2);
			else
				$debettxt = "";

			if($credit <> 0)
				$credittxt = $this->formatGetal($credit,2);
			else
				$credittxt = "";


			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

			$this->pdf->row(array(date("n",db2jul($mutaties['Boekdatum'])),
											'',
											vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal),
											date("d-m-Y",db2jul($mutaties['Boekdatum'])),
											'',
											"",
											$debettxt,
											$credittxt));

			$totaalcredit += $credit;
			$totaaldebet += $debet;
			$lastCategorie = $mutaties[gbOmschrijving];
		}

		$this->printSubTotaal(vertaalTekst("Saldo",$this->pdf->rapport_taal),$subdebet, $subcredit);

		$totaal=$subcredit-$subdebet;
  	if($totaal < 0)
		  $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), abs($totaal), 0);
		else
		 	$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), 0, abs($totaal));




	}
}
?>