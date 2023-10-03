<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/06/15 15:58:41 $
 		File Versie					: $Revision: 1.6 $

 		$Log: RapportVOLK_L19.php,v $
 		Revision 1.6  2016/06/15 15:58:41  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/03/16 14:24:20  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2009/07/12 09:32:42  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2009/02/11 11:49:32  cvs
 		opgelopen rente niet in totaaltelling
 		totaal stond in verkeerde kolom
 		
 		Revision 1.2  2008/12/17 11:14:00  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/03 09:47:45  rvv
 		*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L19
{
	function RapportVOLK_L19($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

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
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthB[$tel];
		}
		$writerow = $this->pdf->widthB[($tel)];
		$end = $start + $writerow;
	  $this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
	  $this->pdf->Cell($writerow,4,$data, 0,0, "R");

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


	function printSubTotaal($title, $WaardeEur)
	{
		$hoogte = 16;
		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($WaardeEur <>0)
				$this->printCol(6,$this->formatGetal($WaardeEur,$this->pdf->rapport_VOLK_decimaal),"subtotaal");

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

	}

	function printTotaal($title, $waardeEur,$procentResultaat,$fondsResultaatGewogenprocent,$valutaResultaatGewogenprocent,$procentResultaatGewogen )
	{
	  //$totaalactueel,$procentResultaat,$totaalfondsResultaatGewogenprocent,$totaalvalutaResultaatprocent,$totaalprocentResultaatGewogen)
		$hoogte = 20;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($waardeEur <>0)
				$this->printCol(6,$this->formatGetal($waardeEur,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($procentResultaat <>0)
				$this->printCol(7,$this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($fondsResultaatGewogenprocent <>0)
				$this->printCol(10,$this->formatGetal($fondsResultaatGewogenprocent,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($valutaResultaatGewogenprocent <>0)
				$this->printCol(12,$this->formatGetal($valutaResultaatGewogenprocent,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($procentResultaatGewogen <>0)
				$this->printCol(14,$this->formatGetal($procentResultaatGewogen,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

		$this->pdf->ln();
		return $waardeEur;
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

  	$fondsresultwidth = 5;
		$omschrijvingExtra = 10;

  	$this->pdf->widthB = array(10,50+$omschrijvingExtra,18,15,21,21,1,15,21,21,15,22,$fondsresultwidth,22,15);

  	$this->pdf->widthB = array(10,60,17,17,17,17,17,17,5,17,17,17,17,17,17);

		$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

	  // voor kopjes
		$this->pdf->widthA = array(60+$omschrijvingExtra,18,15,21,21,1,15,21,21,15,22,$fondsresultwidth,22,15);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');

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


			$query = "SELECT ".
			" TijdelijkeRapportage.beleggingscategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " AS subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

        $procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalactueel,$totaalpercentage,$totaalfondsResultaatGewogenprocent,$totaalvalutaResultaatGewogenprocent,$totaalprocentResultaatGewogen);


/*
			$totaalpercentage      += $subtotaal[percentageVanTotaal];

			$totaalfondsResultaatGewogenprocent =	$subtotaal['fondsResultaatGewogenprocent'];;
			$totaalvalutaResultaatprocent = $subtotaal['valutaResultaatprocent'];
			$totaalprocentResultaatGewogen =$subtotaal['procentResultaatGewogen'];
*/
				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
//				$totaalResultaat = 0;
//				$totaalBijdrage = 0;
$totaalfondsResultaatGewogenprocent= 0;
$totaalvalutaResultaatGewogenprocent= 0;
$totaalprocentResultaatGewogen= 0;
			}

			if($lastCategorie <> $categorien[Omschrijving])
			{
					$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal), "bi");
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
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro,
				TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
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
			$percentageVanTotaaltxt='';
			$fondsResultaatprocenttxt='';
			$fondsResultaatGewogenprocenttxt='';
			$valutaResultaatprocenttxt='';
			$valutaResultaatGewogenprocenttxt='';
			$procentResultaattxt='';
			$procentResultaatGewogentxt='';

			$percentageVanTotaal=0;
			$fondsResultaatprocent=0;
			$fondsResultaatGewogenprocent=0;
			$valutaResultaatprocent=0;
			$valutaResultaatGewogenprocent=0;
			$procentResultaat=0;
			$procentResultaatGewogen=0;


			  $percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			  $fondsResultaattxt = "";
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				//
				$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
				$fondsResultaatGewogenprocent = $fondsResultaatprocent*($percentageVanTotaal/100);
				if($fondsResultaatprocent <> 0)
				{
					$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);
					$fondsResultaatGewogenprocenttxt = $this->formatGetal($fondsResultaatGewogenprocent,$this->pdf->rapport_VOLK_decimaal_proc);
				}
				//

  			$valutaResultaattxt = "";
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);
				//

				$valutaResultaatprocent = ($valutaResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
				$valutaResultaatGewogenprocent = $valutaResultaatprocent*($percentageVanTotaal/100);
				if($valutaResultaatprocent <> 0)
				{
					$valutaResultaatprocenttxt = $this->formatGetal($valutaResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);
					$valutaResultaatGewogenprocenttxt = $this->formatGetal($valutaResultaatGewogenprocent,$this->pdf->rapport_VOLK_decimaal_proc);
				}
				//

				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
				if($subdata['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

					$procentResultaatGewogen = $procentResultaat *($percentageVanTotaal/100);
					if($procentResultaatGewogen <> 0)
					  $procentResultaatGewogentxt = $this->formatGetal($procentResultaatGewogen,$this->pdf->rapport_VOLK_decimaal_proc);





				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);

				$this->pdf->setX($this->pdf->marge);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
												"",
												$this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$subdata['Valuta'],
												$this->formatGetal($subdata[actueleFonds],2),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$percentageVanTotaaltxt,
												"",//Resultaat

												$fondsResultaatprocenttxt, //koers result %
												$fondsResultaatGewogenprocenttxt, //koers contrib. %
												$valutaResultaatprocenttxt, //valuta result %
												$valutaResultaatGewogenprocenttxt, //valuta contrib. %
												$procentResultaattxt, //Totaal result %
												$procentResultaatGewogentxt	)//Totaal contrib %
												);

				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];
				$subtotaal[fondsResultaat] +=$fondsResultaat;
				$subtotaal[valutaResultaat] +=$valutaResultaat;

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['fondsResultaatGewogenprocent'] += $fondsResultaatGewogenprocent;
				$subtotaal['valutaResultaatGewogenprocent'] += $valutaResultaatGewogenprocent;
				$subtotaal['procentResultaatGewogen'] += $procentResultaatGewogen;

				$totalen['percentageVanTotaal'] +=$percentageVanTotaal;
				$totalen['fondsResultaatGewogenprocent'] += $fondsResultaatGewogenprocent;
				$totalen['valutaResultaatGewogenprocent'] += $valutaResultaatGewogenprocent;
				$totalen['procentResultaatGewogen'] += $procentResultaatGewogen;

			}


				$procentResultaat = (($categorien[subtotaalactueel]  - $categorien[subtotaalbegin] ) / ($categorien[subtotaalbegin]  /100));
				if($categorien[subtotaalbegin] < 0)
					$procentResultaat = -1 * $procentResultaat;

			// totaal op categorie tellen
			$totaalbegin   += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];

			$totaalfondsresultaat  += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage      += $subtotaal[percentageVanTotaal];

			$totaalfondsResultaatGewogenprocent =	$subtotaal['fondsResultaatGewogenprocent'];;
			$totaalvalutaResultaatGewogenprocent = $subtotaal['valutaResultaatGewogenprocent'];
			$totaalprocentResultaatGewogen =$subtotaal['procentResultaatGewogen'];

			$lastCategorie = $categorien[Omschrijving];


			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

		$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
	$actueleWaardePortefeuille += $this->printTotaal($title, $totaalactueel,$totaalpercentage,$totaalfondsResultaatGewogenprocent,$totaalvalutaResultaatGewogenprocent,$totaalprocentResultaatGewogen);


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
				$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");
			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{

					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

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
					while($subdata = $DB2->NextRecord())
					{

						if($this->pdf->rapport_HSE_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata[rentedatum]));
							if($subdata[renteperiode] <> 12 && $subdata[renteperiode] <> 0)
								$rentePeriodetxt .= " / ".$subdata[renteperiode];
						}

						$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
						$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
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
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

            $this->pdf->row(array("","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
														"","","", "",
														$percentageVanTotaaltxt));


					}
					$subtotaalPercentageVanTotaal = ($subtotaalRenteInValuta) / ($totaalWaarde/100);
					$totaalRenteInValuta += $subtotaalRenteInValuta;


			}

			// totaal op rente
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal),
			                                                                  $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
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

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			//$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");

			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");


			while($data = $DB1->NextRecord())
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

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$this->pdf->row(array("",
												"",
												"",
												"",'',
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt));


									$totalen['percentageVanTotaal'] +=$subtotaalPercentageVanTotaal;
				$totalen['fondsResultaatGewogenprocent'] += $fondsResultaatGewogenprocent; //null
				$totalen['valutaResultaatGewogenprocent'] += $valutaResultaatGewogenprocent;//null
				$totalen['procentResultaatGewogen'] += $procentResultaatGewogen;	//null
			}

			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);


			$actueleWaardePortefeuille += $this->printTotaal("",$totaalLiquiditeitenEuro,$subtotaalPercentageVanTotaal,"","");
		} // einde liquide

		// check op totaalwaarde!

		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}


		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal),$actueleWaardePortefeuille,$totalen['percentageVanTotaal'],$totalen['fondsResultaatGewogenprocent'],$totalen['valutaResultaatGewogenprocent'],$totalen['procentResultaatGewogen']);
					/*
							$totalen['percentageVanTotaal'] +=$percentageVanTotaal;
				$totalen['fondsResultaatGewogenprocent'] += $fondsResultaatGewogenprocent;
				$totalen['valutaResultaatGewogenprocent'] += $valutaResultaatGewogenprocent;
				$totalen['procentResultaatGewogen'] += $procentResultaatGewogen;
				*/

		$this->pdf->ln();
	  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;

		if($this->pdf->portefeuilledata[AEXVergelijking] > 0)
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
	}
}
?>