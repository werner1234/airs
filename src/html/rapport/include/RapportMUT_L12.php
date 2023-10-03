<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.12 $

$Log: RapportMUT_L12.php,v $
Revision 1.12  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.11  2020/02/05 17:12:14  rvv
*** empty log message ***

Revision 1.10  2019/07/13 17:48:46  rvv
*** empty log message ***

Revision 1.9  2019/07/07 12:24:34  rvv
*** empty log message ***

Revision 1.8  2019/07/06 15:43:47  rvv
*** empty log message ***

Revision 1.7  2019/06/12 15:23:21  rvv
*** empty log message ***

Revision 1.6  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.5  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.4  2012/04/04 16:08:40  rvv
*** empty log message ***

Revision 1.3  2012/04/01 07:40:26  rvv
*** empty log message ***

Revision 1.2  2011/08/13 11:33:18  rvv
*** empty log message ***

Revision 1.1  2011/01/29 15:57:33  rvv
*** empty log message ***

Revision 1.21  2010/06/12 08:38:22  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportMUT_L12
{
	function RapportMUT_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";

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
    $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
		$totaal2 = $totaal1 + $this->pdf->widthA[6];
	  $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
	 	$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetal($totaalA,2);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,2);

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
    $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
		$totaal2 = $totaal1 + $this->pdf->widthA[6];

//		$this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
//		$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());

		if($totaalA > -1)
		{
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if($totaalB > -1)
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}



		$this->pdf->SetX($totaal1 - $this->pdf->widthA[5]- $this->pdf->widthA[4]);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[4],4,'', 0,0, "R");
    $this->pdf->Cell($this->pdf->widthA[5],4,$title, 0,0, "R");
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[7],4,$totaalBtxt, 0,0, "R");

		//$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		if($grandtotal)
		{
		  if($totaalA > -1)
		  {
		    $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
		 	  $this->pdf->Line($totaal1+2,$this->pdf->GetY()+1,$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY()+1);
		  }
		  if($totaalB > -1)
		  {
		    $this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
		  	$this->pdf->Line($totaal2+2,$this->pdf->GetY()+1,$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY()+1);
		  }
		  $this->pdf->ln();
		}
		else
		{
		  /*
			$this->pdf->setDash(1,1);
			if($totaalA > -1)
			  $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
			if($totaalB > -1)
		  	$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
			$this->pdf->setDash();
		  */
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

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
		$this->pdf->line($this->pdf->marge,$this->pdf->getY(),50,$this->pdf->getY());
    $this->pdf->setDrawColor(0,0,0);
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
		$this->pdf->widthA = array(20,21,85,30,30,35,30,30,0);
		$this->pdf->alignA = array('R','R','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = $this->pdf->widthA;
		$this->pdf->alignB = $this->pdf->alignA;

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
    $this->pdf->CellFontColor=array($this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_fontcolor);
		while($mutaties = $DB->nextRecord())
		{
			// print totaal op hele categorie.
			if($lastCategorie <> $mutaties[gbOmschrijving] && !empty($lastCategorie) )
			{
				$this->printSubTotaal(vertaalTekst("Saldo",$this->pdf->rapport_taal),$subdebet, $subcredit);

				$totaal=$subcredit-$subdebet;
				if($totaal < 0)
					$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), abs($totaal), -1);
				else
				 	$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), -1, abs($totaal));
				$subdebet = 0;
				$subcredit = 0;
			}

			if($lastCategorie <> $mutaties[gbOmschrijving])
			{
				$this->printKop(vertaalTekst($mutaties[gbOmschrijving],$this->pdf->rapport_taal), '');
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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


			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

			$this->pdf->row(array('','',
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

		$this->printSubTotaal(vertaalTekst("Saldo",$this->pdf->rapport_taal),$subdebet, $subcredit);

		$totaal=$subcredit-$subdebet;
  	if($totaal < 0)
		  $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), abs($totaal), -1);
		else
		 	$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), -1, abs($totaal));
    
    
    
    unset($this->pdf->CellFontColor);
	}
}
?>