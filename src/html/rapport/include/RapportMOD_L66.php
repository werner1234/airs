<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/03/29 15:57:04 $
File Versie					: $Revision: 1.3 $

$Log: RapportMOD_L66.php,v $
Revision 1.3  2017/03/29 15:57:04  rvv
*** empty log message ***

Revision 1.2  2017/03/25 16:01:09  rvv
*** empty log message ***

Revision 1.1  2017/03/22 16:53:22  rvv
*** empty log message ***

Revision 1.14  2016/11/19 19:02:09  rvv
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

class RapportMOD_L66
{
	function RapportMOD_L66($pdf, $portefeuille, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MOD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_naam1 = str_replace("Modelportefeuille ","",$this->pdf->rapport_naam1);
  	$this->pdf->rapport_koptext = "Portefeuille voorstel ".$this->pdf->rapport_naam1."\n".$this->pdf->selectData['mutatieportefeuille_customNaam'];
		$this->pdf->rapport_titel = "";
		$this->portefeuille = $portefeuille;
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

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4]+ $this->pdf->widthB[5];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_decimaal);

		$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[6],$this->pdf->GetY());
		if(!empty($totaalA))
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[10],$this->pdf->GetY());

		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_omschr_fontcolor['r'],$this->pdf->rapport_subtotaal_omschr_fontcolor['g'],$this->pdf->rapport_subtotaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($begin,4, $title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_fontcolor['r'],$this->pdf->rapport_subtotaal_fontcolor['g'],$this->pdf->rapport_subtotaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[6],4,$totaalBtxt, 0,0, "R");
		if($this->pdf->rapport_inprocent == 1)
			$procenttxt = $this->formatGetal($procent,2)." %";
		$this->pdf->Cell($this->pdf->widthB[7],4,$procenttxt, 0,1, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
	}

	function printTotaal($title, $totaalA, $totaalB, $procent, $grandtotaal = false)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4]+ $this->pdf->widthB[5];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

		// lege regel
		$this->pdf->ln();

		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_decimaal);

		$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[6],$this->pdf->GetY());

		//if(!empty($totaalA))
		//		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());

		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($begin-$this->pdf->widthB[5],4, $title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[5],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,$totaalBtxt, 0,0, "R");
		if($this->pdf->rapport_inprocent == 1)
			$procenttxt = $this->formatGetal($procent,2)." %";
		$this->pdf->Cell($this->pdf->widthB[7],4,$procenttxt, 0,1, "R");


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
			if(!empty($totaalA))
			{
			//	$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			//	$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
			}
			$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[6],$this->pdf->GetY());
			$this->pdf->Line($begin,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[6],$this->pdf->GetY()+1);
		}
		else
		{
			$this->pdf->setDash(1,1);
	//		if(!empty($totaalA))
	//			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[4],$this->pdf->GetY());
			$this->pdf->setDash();
		}

		$this->pdf->ln();


		return $totaalB;
	}

	function printKop($title, $type="default")
	{
		if($type=='bi')
			$hoogte=3;
		else
			$hoogte=2;
		if(($this->pdf->GetY() + $hoogte*$this->pdf->rowHeight) >= $this->pdf->pagebreak)
		{
			$this->pdf->addPage();
		}
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

		$this->pdf->excelData[] = array($title);
	}

	function printVOLK()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->ValutaKoersEind=1;
		$this->pdf->ValutaKoersBegin=1;
		$this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;

		$DB=new DB();
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
			" FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;


		$query = "SELECT TijdelijkeRapportage.hoofdcategorieOmschrijving, TijdelijkeRapportage.hoofdcategorie, 
      TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.beleggingssector, TijdelijkeRapportage.beleggingssectorOmschrijving,  ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.hoofdcategorie, TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.beleggingssector ".
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde, TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.beleggingssectorVolgorde asc";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$regel=0;
		$this->pdf->SetFillColor($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]);
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

				$procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
				if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , false);

				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaaldividend = 0;
				$totaaldividendCorrected = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if($lastHoofdcategorie <> $categorien['hoofdcategorieOmschrijving'] && !empty($lastHoofdcategorie) )
			{

				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHoofdcategorie,$this->pdf->rapport_taal);
				$procentResultaat = (($hoofdtotaal['subtotaalactueel'] - $hoofdtotaal['subtotaalbegin'] + $hoofdtotaal['totaalDividendCorrected']) / ($hoofdtotaal['subtotaalbegin'] /100));
				if($hoofdtotaal['subtotaalbegin'] < 0)
					$procentResultaat = -1 * $procentResultaat;
				$this->printTotaal($title, $hoofdtotaal['subtotaalbegin'], $hoofdtotaal['subtotaalactueel'], $hoofdtotaal['percentageVanTotaal'] , false);
				$hoofdtotaal=array();
			}

			if($lastHoofdcategorie <> $categorien['hoofdcategorieOmschrijving'])
				$this->printKop(vertaalTekst($categorien['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");


			if($lastCategorie <> $categorien['Omschrijving'])
				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");


			// subkop (valuta)

			$regel=0;

			if($categorien['beleggingscategorie']=='AAND')
				$this->printKop(" ".$categorien['beleggingssectorOmschrijving'], "i");
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, 
          TijdelijkeRapportage.beginwaardeValutaLopendeJaar,".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro as beginPortefeuilleWaardeEuro, ".
				//" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingssector =  '".$categorien['beleggingssector']."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.hoofdcategorie =  '".$categorien['hoofdcategorie']."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;

			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
//echo $subquery."<br><br>";exit;
			while($subdata = $DB2->NextRecord())
			{

				if($subdata['Valuta'] == $this->pdf->rapportageValuta)
					$subdata['beginPortefeuilleWaardeEuro']=$subdata['beginPortefeuilleWaardeEuro']/$subdata['beginwaardeValutaLopendeJaar'];
				else
					$subdata['beginPortefeuilleWaardeEuro']=$subdata['beginPortefeuilleWaardeEuro']/$this->pdf->ValutaKoersBegin;


				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;

				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
				if($subdata['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro'] / $totaalWaarde) * 100;


				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

				if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
					$fondsResultaatprocenttxt = "p.m.";
				else
					$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				$dividendtxt='';

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($dividend['totaal'] <> 0)
					$dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);


				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

				$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);

				$this->pdf->row(array("",
													"",
													$this->formatGetal($subdata['totaalAantal'],0),
													$subdata['valuta'],
													$this->formatGetal($subdata['actueleFonds'],2),
													$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
													$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),
													($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));


				$this->pdf->excelData[] = array("",
					$subdata['fondsOmschrijving'],
					$subdata['totaalAantal'],
					round($subdata['actueleFonds'],2),
					round($subdata['actuelePortefeuilleWaardeInValuta'],2),
					round($subdata['actuelePortefeuilleWaardeEuro'],2),
					round($percentageVanTotaal,2));
				$regel++;


				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
				$subtotaal['totaalDividend'] += $dividend['totaal'];
				$subtotaal['totaalDividendCorrected'] += $dividend['corrected'];

				$hoofdtotaal['subtotaalbegin'] +=$subdata['beginPortefeuilleWaardeEuro'];
				$hoofdtotaal['subtotaalactueel'] +=$subdata['actuelePortefeuilleWaardeEuro'];
				$hoofdtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$hoofdtotaal['fondsResultaat'] +=$fondsResultaat;
				$hoofdtotaal['valutaResultaat'] +=$valutaResultaat;
				$hoofdtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$hoofdtotaal['totaalBijdrage'] += $subTotaalBijdrage;
				$hoofdtotaal['totaalDividend'] += $dividend['totaal'];
				$hoofdtotaal['totaalDividendCorrected'] += $dividend['corrected'];
			}

			// print categorie footers
			$procentResultaat = (($categorien['subtotaalactueel']  - $categorien['subtotaalbegin'] + $subtotaal['totaalDividendCorrected'] ) / ($categorien['subtotaalbegin']  /100));
			if($categorien['subtotaalbegin'] < 0)
				$procentResultaat = -1 * $procentResultaat;

			if($categorien['beleggingscategorie']=='AAND')
				$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien['subtotaalbegin'],$categorien['subtotaalactueel'],$subtotaal['percentageVanTotaal']);

			// totaal op categorie tellen
			$totaalbegin   += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];

			$totaalfondsresultaat  += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage      += $subtotaal[percentageVanTotaal];
			$totaaldividend        += $subtotaal['totaalDividend'];
			$totaaldividendCorrected        += $subtotaal['totaalDividendCorrected'];

			$lastCategorie = $categorien[Omschrijving];
			$lastHoofdcategorie=$categorien['hoofdcategorieOmschrijving'];

			$grandtotaalvaluta += $subtotaal[valutaResultaat];
			$grandtotaalfonds  += $subtotaal[fondsResultaat];
			$grandtotaaldividend  += $subtotaal['totaalDividend'];
			$grandtotaaldividendCorrected  += $subtotaal['totaalDividendCorrected'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$subtotaal = array();


		}

		$procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rente'  AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.valutaVolgorde ";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);//echo $query;exit;
		$DB->Query();

		if($DB->records() > 0)
		{
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");


			//$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_taal));
			//$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien[valuta],"");

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
						" TijdelijkeRapportage.actueleValuta , ".
						" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
						" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
						" TijdelijkeRapportage.rentedatum, ".
						" TijdelijkeRapportage.renteperiode, ".
						" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
						" FROM TijdelijkeRapportage WHERE ".
						" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
						" TijdelijkeRapportage.type = 'rente'  AND ".
						" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
						" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
						.$__appvar['TijdelijkeRapportageMaakUniek'].
						" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);

					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					$regel=0;
					while($subdata = $DB2->NextRecord())
					{
						if($this->pdf->rapport_HSE_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata['rentedatum']));
							if($subdata['renteperiode'] <> 12 && $subdata['renteperiode'] <> 0)
								$rentePeriodetxt .= " / ".$subdata[renteperiode];
						}

						$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
						$hoofdtotaal['subtotaalactueel'] +=$subdata['actuelePortefeuilleWaardeEuro'];
						$hoofdtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
						$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";


						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						$this->pdf->row(array("",$subdata['fondsOmschrijving'].$rentePeriodetxt,'',$subdata['valuta'],'',
															$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
															$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
															$percentageVanTotaaltxt));
					}

					$subtotaalPercentageVanTotaal = ($subtotaalRenteInValuta) / ($totaalWaarde/100);
					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, $subtotaalPercentageVanTotaal);
					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}
			}

			// totaal op rente
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}


		$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHoofdcategorie,$this->pdf->rapport_taal);
		$procentResultaat = (($hoofdtotaal['subtotaalactueel'] - $hoofdtotaal['subtotaalbegin'] + $hoofdtotaal['totaalDividendCorrected']) / ($hoofdtotaal['subtotaalbegin'] /100));
		if($hoofdtotaal['subtotaalbegin'] < 0)
			$procentResultaat = -1 * $procentResultaat;
		$this->printTotaal($title, $hoofdtotaal['subtotaalbegin'], $hoofdtotaal['subtotaalactueel'], $hoofdtotaal['percentageVanTotaal'] ,false);
		$hoofdtotaal=array();
		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		$regel=0;

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");

			while($data = $DB1->NextRecord())
			{
				$liqiteitenBuffer[] = $data;
			}


			foreach($liqiteitenBuffer as $data)
			{
				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];
				$subtotaalPercentageVanTotaal  = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
				$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);


				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				if($regel%2!=0)
					$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				else
					unset($this->pdf->fillCell);
				$regel++;
				$this->pdf->row(array("",
													$omschrijving,
													"",
													$data['valuta'],
													"",
													$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$subtotaalPercentageVanTotaaltxt,'','','','',''));
			}

			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
			$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,$subtotaalPercentageVanTotaal,"","");
		} // einde liquide

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,true);
		unset($this->pdf->fillCell);

		$this->pdf->ln();
	}

	function writeRapport()
	{
		global $__appvar;
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(10,55,18,12,25,25,25,20);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(65,18,12,25,25,25,20);
		$this->pdf->alignA = array('L','R','R','R','R','R','R');

		$this->pdf->excelData[] = array("",
			"Fondsomschrijving",
			"Aantal",
			"Fondskoers",
			"Fondstotaal",
			"Fondstotaal EUR",
			"Perc. %");

		$this->printVOLK();
		return '';

	}
}
?>