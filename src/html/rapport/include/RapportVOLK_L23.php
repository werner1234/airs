<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
File Versie					: $Revision: 1.4 $

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L23
{
	function RapportVOLK_L23($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$this->pdf->rapport_titel2="waarde,resultaat,rendement,risico,motivatie";

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
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
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
		if ($type == 'tekst' && $this->pdf->rapport_layout == 8)
		{
		  $this->pdf->Cell($writerow,4,$data, 0,0, "L");
		}
		else
		{
		  $this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		  $this->pdf->Cell($writerow,4,$data, 0,0, "R");
		}
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "grandtotaal")
			{
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
	}


	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF, $TotaalG = 0, $totaalH = 0)
	{
		$hoogte = 16;

		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->printCol(1,$title,"tekst");
		if($totaalB <>0)
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(3,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
		if($totaalA <>0)
			$this->printCol(7,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
		if($totaalC <>0)
			$this->printCol(4,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
		if($totaalD <>0)
			$this->printCol(9,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
		if($totaalE <>0)
			$this->printCol(11,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
		if($totaalF <>0)
			$this->printCol(12,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
	  //$this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);

		$hoogte = 20;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		// lege regel
		if($this->pdf->rapport_layout != 8)
			$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->printCol(1,$title,"tekst");
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		if($totaalB <>0)
			$this->printCol(5,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
		if($totaalA <>0)
			$this->printCol(0,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
		if($totaalC <>0)
			$this->printCol(6,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
		if($totaalD <>0)
			$this->printCol(8,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
		if($totaalE <>0)
			$this->printCol(9,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
		if($totaalF <>0)
			$this->printCol(10,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

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

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$this->pdf->widthB = array(20,  28,60,     12,18,21,21,    10,   21,20,20,     5,   20);
		$this->pdf->alignB = array('L', 'R','L',  'R','R','R','R', 'R',  'R','R','R', 'R', 'R',);

		//$this->pdf->CellDot=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,1);

		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
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

		$query="SELECT Omschrijving, CategorienPerHoofdcategorie.Hoofdcategorie FROM Beleggingscategorien
		        Inner Join CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
            WHERE CategorienPerHoofdcategorie.Vermogensbeheerder= '".$this->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($query);
		$DB->Query();
		while($regel = $DB->NextRecord())
		  $hoofdCategorieOmschrijvingen[$regel['Hoofdcategorie']]=$regel['Omschrijving'];

			$query = "SELECT Beleggingscategorien.Omschrijving, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" (TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel,
			CategorienPerHoofdcategorie.Hoofdcategorie,
			(SELECT Beleggingscategorien.Afdrukvolgorde FROM Beleggingscategorien WHERE Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie ) as hoofdVolgorde,
			TijdelijkeRapportage.valuta
			FROM TijdelijkeRapportage
			LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
			LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
			LEFT Join CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND  CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			"ORDER BY hoofdVolgorde, Beleggingscategorien.Afdrukvolgorde asc ";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($regel = $DB->NextRecord())
		{
		  if($regel['Hoofdcategorie']=='')
		    $regel['Hoofdcategorie']='geen';
		  if($regel['valuta']=='')
		    $regel['valuta']='geen';
		  $hoofdCategorieWaarde[$regel['Hoofdcategorie']]+=$regel['subtotaalactueel'];
		  $categoriePerHoofdcategorie[$regel['beleggingscategorie']]=$regel['Hoofdcategorie'];
		  $valutaWaarde[$regel['valuta']]+=$regel['subtotaalactueel'];
		}
//		echo $query."<br>\n";
//    listarray($valutaWaarde);
//		listarray($hoofdCategorieWaarde);exit;

			$query = "SELECT ".
			" Beleggingscategorien.Omschrijving, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
      " IF ('EUR' <> '".$this->pdf->rapportageValuta."',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as subtotaalbegin,
       SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel,
			CategorienPerHoofdcategorie.Hoofdcategorie,
			(SELECT Beleggingscategorien.Afdrukvolgorde FROM Beleggingscategorien WHERE Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie ) as hoofdVolgorde,
			TijdelijkeRapportage.valuta FROM ".
			" TijdelijkeRapportage
			LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
			LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
			LEFT Join CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND  CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
			" ORDER BY hoofdVolgorde, Beleggingscategorien.Afdrukvolgorde asc";
				$DB->SQL($query);
		$DB->Query();
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal);

        $procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, '', $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat);

				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if($categoriePerHoofdcategorie[$categorien['beleggingscategorie']] != $lastHoofdCategorie)
			{
			  $percentage = $this->formatGetal((($hoofdCategorieWaarde[$categoriePerHoofdcategorie[$categorien['beleggingscategorie']]] / $totaalWaarde) * 100),1)."%";
			  $this->pdf->setX($this->pdf->marge);
				$this->pdf->Cell(100,4,$percentage." ".$hoofdCategorieOmschrijvingen[$categoriePerHoofdcategorie[$categorien['beleggingscategorie']]],0,1);
			}

			$lastHoofdCategorie=$categoriePerHoofdcategorie[$categorien['beleggingscategorie']];

			if($lastCategorie <> $categorien[Omschrijving])
			{

					$this->pdf->SetWidths($this->pdf->widthB);
					$this->pdf->SetAligns(array('L','L','L'));

					$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
					$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
					$this->pdf->row(array("",vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal)));
					$this->pdf->SetAligns($this->pdf->alignB);

			}
			// subkop (valuta)
			if($categorien['valuta'] == $this->pdf->rapportageValuta)
			  $beginQuery = 'beginwaardeValutaLopendeJaar';
			else
			  $beginQuery = $this->pdf->ValutaKoersBegin;


				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
         (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
         (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ,BeleggingscategoriePerFonds.grafiekKleur, Valutas.Valutateken ,BeleggingscategoriePerFonds.RisicoPercentageFonds ".
				" FROM TijdelijkeRapportage
					LEFT Join BeleggingscategoriePerFonds ON TijdelijkeRapportage.fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
					LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien[beleggingscategorie]."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";



			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

			while($subdata = $DB2->NextRecord())
			{
				$fondsResultaat = ($subdata[actuelePortefeuilleWaardeInValuta] - $subdata[beginPortefeuilleWaardeInValuta]) * $subdata[actueleValuta] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata[beginPortefeuilleWaardeEuro]) * 100;
				$valutaResultaat = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro] - $fondsResultaat;
				$procentResultaat = (($subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro]) / ($subdata[beginPortefeuilleWaardeEuro] /100));
				if($subdata[beginPortefeuilleWaardeEuro] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);

				$this->pdf->setX($this->pdf->marge);
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,"");
				$this->pdf->Cell($this->pdf->widthB[2],4,$subdata['fondsOmschrijving'],null,null,'L',null);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2)
				{
				  $kleur=unserialize($subdata['grafiekKleur']);

	        $this->pdf->SetFillColor($kleur['R']['value'], $kleur['G']['value'], $kleur['B']['value']);

	        //array(20,  28,60,     12,18,21,21,    10,   21,20,20,     5,   20);
					$this->pdf->row(array("",
					             $this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												"",
												$subdata['Valutateken'],
												$this->formatGetal($subdata[actueleFonds],2),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc).'%',
												"",
												$fondsResultaattxt,
												$valutaResultaattxt,
												$procentResultaattxt.'%',
												"",
												$subdata['RisicoPercentageFonds'].'%'));
				}


				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];

				$subtotaal[percentageVanTotaal] +=$percentageVanTotaal;
				$subtotaal[fondsResultaat] +=$fondsResultaat;
				$subtotaal[valutaResultaat] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;

			}


			// totaal op categorie tellen
			$totaalbegin   += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];

			$totaalfondsresultaat  += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage      += $subtotaal[percentageVanTotaal];

			$lastCategorie = $categorien[Omschrijving];

			$grandtotaalvaluta += $subtotaal[valutaResultaat];
			$grandtotaalfonds  += $subtotaal[fondsResultaat];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

		// totaal voor de laatste categorie

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), '', $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
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
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
				$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal)));
  			$totaalRenteInValuta = 0 ;

	  		while($categorien = $DB->NextRecord())
	  		{
	  			$totaalRenteInValuta += $categorien[subtotaalactueel];
	  		}

			// totaal op rente
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal,Valutas.Valutateken".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
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

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			//$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");
			if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2 )
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
				$this->pdf->row(array("",vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal)));
			}
			else
			{
				$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");
			}

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


  			$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc);


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

				$this->pdf->row(array("","","",
												$data['Valutateken'],"",
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt,
												"",
												"",
												"",
												"",
												""));




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


		if(($this->pdf->GetY() + 4) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$y = $this->pdf->getY();
		$this->pdf->setY(($y+4));
		//$this->printCol(7,vertaalTekst("Totaal ongerealiseerd resultaat",$this->pdf->rapport_taal),"tekst");
		$this->pdf->setY($y);
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,"",$grandtotaalfonds,$grandtotaalvaluta,"",true);


		$this->pdf->ln();

		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}


		if($this->pdf->rapport_VOLK_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		elseif ($this->pdf->rapport_VOLK_rendement == 2)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;

//		if($this->pdf->rapport_layout == 8)
//		  include_once('indexGrafiek.php');
		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

		$y=$this->pdf->getY();
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIV'];
		$q = "SELECT Valuta, omschrijving FROM Valutas";
		$DB->SQL($q);
		$DB->Query();

		$dbValutacategorien = array();
		while($valta = $DB->NextRecord())
		{
			$dbValutacategorien[$valta['Valuta']] = $valta['omschrijving'];
		}
    $query = "SELECT  SUM((TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. ") AS actuelePortefeuilleWaardeEuro, TijdelijkeRapportage.valuta, Valutas.Afdrukvolgorde
			    FROM TijdelijkeRapportage Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
				  WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		    	.$__appvar['TijdelijkeRapportageMaakUniek'].
		    	"GROUP BY valuta ORDER BY Afdrukvolgorde ";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($regel = $DB->NextRecord())
		{
		  $valutaWaarde[$regel['valuta']]=$regel['actuelePortefeuilleWaardeEuro'];
		}

		$percentage=array();
		$kleur=array();
		$omschrijvingen=array();
		$rest=100;
		foreach ($valutaWaarde as $valuta=>$waarde)
		{
		  $valutaPercentage=$waarde/$totaalWaarde*100;
		  $rest -= $valutaPercentage;

		  $percentage[]=$valutaPercentage;
		  $kleur[]=array($kleuren[$valuta]['R']['value'],$kleuren[$valuta]['G']['value'],$kleuren[$valuta]['B']['value']);
      $omschrijvingen[]=$dbValutacategorien[$valuta]." ".$this->formatGetal($valutaPercentage,1)."%" ;
		}
		if(round($rest,1) <> 0.0)
		{
		  $percentage[]=$rest;
		  $kleur[]=array(200,100,100);
		  $omschrijvingen[]="Restpercentage"." ".$this->formatGetal($rest,1)."%" ;
		}
    $y=$y-40;
		$this->pdf->set3dLabels($omschrijvingen,210,$y-5,$kleur);
    $this->pdf->Pie3D($percentage,$kleur,200,$y,30,20,6,"Valutaverdeling");

    //$this->pdf->set3dLabels($grafiekData['OIS2']['Omschrijving'],$Xas+135,$yas+80,$grafiekData['OIS2']['Kleur']);
    //$this->pdf->Pie3D($grafiekData['OIS2']['Percentage'],$grafiekData['OIS2']['Kleur'],$Xas+135,$yas+80,$diameter,$hoek,$dikte,"Sector");
	}
}
?>