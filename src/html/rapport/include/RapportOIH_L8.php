<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/06/29 16:54:20 $
File Versie					: $Revision: 1.5 $

$Log: RapportOIH_L8.php,v $
Revision 1.5  2011/06/29 16:54:20  rvv
*** empty log message ***

Revision 1.4  2011/03/25 10:47:37  rvv
*** empty log message ***

Revision 1.3  2011/03/23 17:01:48  rvv
*** empty log message ***

Revision 1.2  2011/02/06 14:36:38  rvv
*** empty log message ***

Revision 1.1  2011/02/02 18:49:00  rvv
*** empty log message ***

Revision 1.55  2010/11/14 10:41:01  rvv
Liquiditetien L32 verschoven

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIH_L8
{
	function RapportOIH_L8($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;


		include_once("rapport/rapportATTberekening.php");

		$tmp= new RapportATTberekening($portefeuille);
		$categorien=$tmp->getAttributieCategorien();

	 $index=new indexHerberekening();
//$indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille,$specifiekeIndex);
$this->indexData = $index->getWaardenATT($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille,$categorien);

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


	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
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
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(11,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(6,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(8,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(12,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(14,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(15,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($totaalG <>0)
				$this->printCol(16,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($totaalH <>0)
			  $this->printCol(17,$this->formatGetal($totaalH,2),$grandtotaal);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

		$this->pdf->ln();
		return $totaalB;
	}

	function printKop($title, $type="default",$line=0)
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
			case "BI" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize+2;
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
		if($line)
		  $this->pdf->line($this->pdf->getX(),$this->pdf->getY(),$this->pdf->getX()+$line,$this->pdf->getY());
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$fondsresultwidth = 15;
		$omschrijvingExtra = 0;
		$this->pdf->widthB = array(2,45,20,8,20,21,21,1,20,21,21,21,20,0,20,0,0,20);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');

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
			"  ".
			"ifnull(Beleggingscategorien.Omschrijving,'Liquiditeiten') as Omschrijving,
			ifnull(TijdelijkeRapportage.beleggingscategorie,'geen') as beleggingscategorie,
			  AttributieCategorien.Omschrijving as AttributieCategorie,
			  max(TijdelijkeRapportage.AttributieCategorie) as AttributieCat, ".
     " IF ('EUR' <> '".$this->pdf->rapportageValuta."',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as subtotaalbegin,".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel ,
			ifnull(Beleggingscategorien.Afdrukvolgorde,100) as BeleggingscategorieVolgorde,
			TijdelijkeRapportage.type
			FROM TijdelijkeRapportage
LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
LEFT Join AttributieCategorien ON TijdelijkeRapportage.AttributieCategorie = AttributieCategorien.AttributieCategorie  ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'
			 AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.AttributieCategorie, TijdelijkeRapportage.beleggingscategorie
ORDER BY AttributieCategorien.Afdrukvolgorde, BeleggingscategorieVolgorde asc";
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
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
        $categorieTotaal['procentResultaat'] = (($categorieTotaal['totaalactueel'] - $categorieTotaal['totaalbegin']) / ($categorieTotaal['totaalbegin'] /100));
		    if($categorieTotaal['totaalbegin'] < 0)
					$categorieTotaal['procentResultaat'] = -1 * $categorieTotaal['procentResultaat'];

			  $actueleWaardePortefeuille += $categorieTotaal['totaalactueel'];
        $this->printTotaal($title,$categorieTotaal['totaalactueel'] , 0, $categorieTotaal['totaalpercentage'] , 0, 0, 0,false,0,0);
			  $categorieTotaal=array();			}

			if($lastAttCategorie <> $categorien['AttributieCategorie'] && !empty($lastAttCategorieKort) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastAttCategorie,$this->pdf->rapport_taal);
        $attCategorieTotaal['procentResultaat'] = (($attCategorieTotaal['totaalactueel'] - $attCategorieTotaal['totaalbegin']) / ($attCategorieTotaal['totaalbegin'] /100));
		    if($attCategorieTotaal['totaalbegin'] < 0)
					$attCategorieTotaal['procentResultaat'] = -1 * $attCategorieTotaal['procentResultaat'];

        $this->printTotaal($title,$attCategorieTotaal['totaalactueel'] , 0, $attCategorieTotaal['totaalpercentage'] , 0, 0, 0,false,0,0);
        $attCategorieTotaal=array();
        $this->addPerfBox($lastAttCategorieKort,$lastAttCategorie);
        $this->pdf->addPage();
			}

  		if($lastAttCategorie <> $categorien['AttributieCategorie'])
					$this->printKop(vertaalTekst($categorien['AttributieCategorie'],$this->pdf->rapport_taal), "BI",strlen(vertaalTekst($categorien['AttributieCategorie'],$this->pdf->rapport_taal))*1.70);

			if($lastCategorie <> $categorien['Omschrijving'])
					$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");
			// subkop (valuta)
			if($categorien['valuta'] == $this->pdf->rapportageValuta)
			  $beginQuery = 'beginwaardeValutaLopendeJaar';
			else
			  $beginQuery = $this->pdf->ValutaKoersBegin;


			if($categorien['type']=='rekening')
			{
			$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
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

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
		    if($data['Termijnrekening'] >0)
			      $termijnTotaalEur += $data['actuelePortefeuilleWaardeEuro'];
			}
			foreach($liqiteitenBuffer as $data)
			{
			    if($data['valuta'] == 'EUR' && $data['Memoriaal'] < 1 && $data['Deposito'] < 1 && $data['Termijnrekening'] < 1 && $eurRekeningFound == false)
			    {
			      $eurRekeningFound = true;
			      $data['actuelePortefeuilleWaardeEuro'] = $data['actuelePortefeuilleWaardeEuro'] +  $termijnTotaalEur;
			      $data['actuelePortefeuilleWaardeInValuta'] = $data['actuelePortefeuilleWaardeInValuta'] + $termijnTotaalEur;
			    }
			    $tmp[] = $data;
			}
			$liqiteitenBuffer = $tmp;
			$liqiteitenBuffer[] = array('fondsOmschrijving'=>'Reservering Termijncontracten','actuelePortefeuilleWaardeEuro'=>$termijnTotaalEur*-1,'actuelePortefeuilleWaardeInValuta'=>$termijnTotaalEur*-1);


			foreach($liqiteitenBuffer as $data)
			{
				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];
	      $subtotaalPercentageVanTotaal  = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
		  	$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($data['Deposito'] == 1)
				{
				  $DB2 = new DB();
			  	$query= "SELECT  DATE_FORMAT(Rekeningmutaties.Boekdatum,\"%d-%m-%Y\") as Boekdatum FROM Rekeningmutaties WHERE Rekeningmutaties.Rekening = '".$data['rekening']."' ORDER BY Rekeningmutaties.Boekdatum DESC LIMIT 1";
			  	$DB2->SQL($query);
			  	$DB2->Query();
			  	$boekdatum = $DB2->nextRecord();
			  	$omschrijving .=' aanvangsdatum '.$boekdatum['Boekdatum'];
				}

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

					   $this->pdf->row(array("","","","","",
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												"",
												$subtotaalPercentageVanTotaaltxt)
												);

				$subtotaal['totaalactueel'] += $data['actuelePortefeuilleWaardeEuro'];
				$subtotaal['percentageVanTotaal'] +=$subtotaalPercentageVanTotaal;

			}
			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);


			// totaal op categorie tellen
			$categorieTotaal['totaalbegin']   += $categorien['subtotaalbegin'];
			$categorieTotaal['totaalactueel'] += $categorien['subtotaalactueel'];
			$categorieTotaal['totaalfondsresultaat']  += $subtotaal['fondsResultaat'];
			$categorieTotaal['totaalvalutaresultaat'] += $subtotaal['valutaResultaat'];
			$categorieTotaal['totaalpercentage']      += $subtotaal['percentageVanTotaal'];

			$attCategorieTotaal['totaalbegin']   += $categorien['subtotaalbegin'];
			$attCategorieTotaal['totaalactueel'] += $categorien['subtotaalactueel'];
			$attCategorieTotaal['totaalfondsresultaat']  += $subtotaal['fondsResultaat'];
			$attCategorieTotaal['totaalvalutaresultaat'] += $subtotaal['valutaResultaat'];
			$attCategorieTotaal['totaalpercentage']      += $subtotaal['percentageVanTotaal'];

			$lastCategorie = $categorien['Omschrijving'];
			$lastAttCategorie = $categorien['AttributieCategorie'];
			$lastAttCategorieKort = $categorien['AttributieCat'];

			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];

			$categorieTotaal['totaalResultaat'] +=	$subtotaal['totaalResultaat'] ;
			$categorieTotaal['totaalBijdrage']  += $subtotaal['totaalBijdrage'] ;

			$attCategorieTotaal['totaalResultaat'] +=	$subtotaal['totaalResultaat'] ;
			$attCategorieTotaal['totaalBijdrage']  += $subtotaal['totaalBijdrage'] ;

			$grandcategorieTotaal['totaalResultaat']  +=	$subtotaal['totaalResultaat'] ;
			$grandcategorieTotaal['totaalBijdrage']   += $subtotaal['totaalBijdrage'] ;

		 // $this->printTotaal("", $totaalLiquiditeitenEuro, '',$subtotaalPercentageVanTotaal,"","");
		  } // einde liquide

			}
			else
			{


				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar as beginwaardeLopendeJaar, ".
				" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeInValuta) as beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       SUM((TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar)),
       SUM((TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ")) as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
				SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as actuelePortefeuilleWaardeInValuta,
				 SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. ") as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.type IN('fondsen','rente') AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.fondsOmschrijving
				  ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

			while($subdata = $DB2->NextRecord())
			{
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;

				$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;

				$categorieTotaal['procentResultaat'] = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
				if($subdata['beginPortefeuilleWaardeEuro'] < 0)
					$categorieTotaal['procentResultaat'] = -1 * $categorieTotaal['procentResultaat'];

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($categorieTotaal['procentResultaat'] > 1000 || $categorieTotaal['procentResultaat'] < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($categorieTotaal['procentResultaat'],$this->pdf->rapport_VOLK_decimaal_proc);

				if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
					$fondsResultaatprocenttxt = "p.m.";
				else
					$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);

	  		$subcategorieTotaal['totaalBijdrage'] = $categorieTotaal['procentResultaat'] * $percentageVanTotaal / 100;

	  		$valutaResultaatprocent = ($valutaResultaat / $subdata[beginPortefeuilleWaardeEuro]) * 100;

	  		if($valutaResultaatprocent > 1000 || $valutaResultaatprocent < -1000)
          $valutaResultaatprocentTxt = "p.m.";
	  		else
          $valutaResultaatprocentTxt = $this->formatGetal($valutaResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);

	  		if($subcategorieTotaal['totaalBijdrage'] > 1000 || $subcategorieTotaal['totaalBijdrage'] < -1000)
	  		{
 		     $totaalBijdrageTxt = "p.m.";
		     $subcategorieTotaal['totaalBijdrage'] =0;
	  		}
		    else
		     $totaalBijdrageTxt = $this->formatGetal($subcategorieTotaal['totaalBijdrage'],2);

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
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");

				//$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving],null,null,null,null,"http://url?code=");

				if($this->pdf->rapport_VOLK_link == 1)
				{
					// getStroevecode.
					$DBx = new DB();
					$DBx->SQL("SELECT stroeveCode FROM Fondsen Where Fonds = '".$subdata['fonds']."'");
					$DBx->Query();
					$fdata = $DBx->nextRecord();

					$url = str_replace("[stroevecode]",$fdata['stroeveCode'],$this->pdf->rapport_VOLK_url);
					$url = str_replace("[fonds]",$subdata['fonds'],$url);

					$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,$url);
				}
				else
					$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,null);

				$this->pdf->setX($this->pdf->marge);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("","",
 			                      	$this->formatAantal($subdata['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
                              $subdata['Valuta'],
				                      $this->formatGetal($subdata['actueleFonds'],2),
															$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
  														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
	                            "",
                          		$percentageVanTotaaltxt,
												      $this->formatGetal($subdata['beginwaardeLopendeJaar'],2),
               								$this->formatGetal($subdata['beginPortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
												      $this->formatGetal($subdata['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
											      	$fondsResultaattxt,
												      '',
												      $valutaResultaattxt,
												      '',
												      '',
												      $totaalBijdrageTxt));

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subcategorieTotaal['totaalResultaat'];
				$subtotaal['totaalBijdrage'] += $subcategorieTotaal['totaalBijdrage'];
			}



			// totaal op categorie tellen
			$categorieTotaal['totaalbegin']   += $categorien['subtotaalbegin'];
			$categorieTotaal['totaalactueel'] += $categorien['subtotaalactueel'];
			$categorieTotaal['totaalfondsresultaat']  += $subtotaal['fondsResultaat'];
			$categorieTotaal['totaalvalutaresultaat'] += $subtotaal['valutaResultaat'];
			$categorieTotaal['totaalpercentage']      += $subtotaal['percentageVanTotaal'];

			$attCategorieTotaal['totaalbegin']   += $categorien['subtotaalbegin'];
			$attCategorieTotaal['totaalactueel'] += $categorien['subtotaalactueel'];
			$attCategorieTotaal['totaalfondsresultaat']  += $subtotaal['fondsResultaat'];
			$attCategorieTotaal['totaalvalutaresultaat'] += $subtotaal['valutaResultaat'];
			$attCategorieTotaal['totaalpercentage']      += $subtotaal['percentageVanTotaal'];

			$lastCategorie = $categorien['Omschrijving'];
			$lastAttCategorie = $categorien['AttributieCategorie'];
			$lastAttCategorieKort = $categorien['AttributieCat'];

			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];

			$categorieTotaal['totaalResultaat'] +=	$subtotaal['totaalResultaat'] ;
			$categorieTotaal['totaalBijdrage']  += $subtotaal['totaalBijdrage'] ;

			$attCategorieTotaal['totaalResultaat'] +=	$subtotaal['totaalResultaat'] ;
			$attCategorieTotaal['totaalBijdrage']  += $subtotaal['totaalBijdrage'] ;

			$grandcategorieTotaal['totaalResultaat']  +=	$subtotaal['totaalResultaat'] ;
			$grandcategorieTotaal['totaalBijdrage']   += $subtotaal['totaalBijdrage'] ;
	}
			$subtotaal = array();
		}

		$categorieTotaal['procentResultaat'] = (($categorieTotaal['totaalactueel'] - $categorieTotaal['totaalbegin']) / ($categorieTotaal['totaalbegin'] /100));
		if($categorieTotaal['totaalbegin'] < 0)
			$categorieTotaal['procentResultaat'] = -1 * $categorieTotaal['procentResultaat'];

    $actueleWaardePortefeuille +=	$categorieTotaal['totaalactueel'];	    $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),$categorieTotaal['totaalactueel'] , $categorieTotaal['totaalbegin'], $categorieTotaal['totaalpercentage'],$categorieTotaal['totaalfondsresultaat'],$categorieTotaal['totaalvalutaresultaat'],0,false,0,$categorieTotaal['totaalBijdrage']);

		$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastAttCategorie,$this->pdf->rapport_taal);
    $attCategorieTotaal['procentResultaat'] = (($attCategorieTotaal['totaalactueel'] - $attCategorieTotaal['totaalbegin']) / ($attCategorieTotaal['totaalbegin'] /100));
    if($attCategorieTotaal['totaalbegin'] < 0)
			$attCategorieTotaal['procentResultaat'] = -1 * $attCategorieTotaal['procentResultaat'];

//    $this->printTotaal($title,$attCategorieTotaal['totaalactueel'] , 0, $attCategorieTotaal['totaalpercentage'] , 0, 0, 0,false,0,0);
   //    $this->addPerfBox($lastAttCategorieKort,$lastAttCategorie);
		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}



		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 )
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

$this->pdf->ln(10);
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), $actueleWaardePortefeuille, '',100,0,0,0,true,0,0);
		$this->pdf->ln();


	  //$this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;
	  $this->addPerfBox('Totaal','totale portefeuille',true);
	}

	function addPerfBox($categorie,$omschrijving,$valuta=false)
	{
	  //echo $categorie." $omschrijving ||<br>\n";
	 // listarray($this->indexData);
	  foreach ($this->indexData[$categorie] as $data)
	  {
	    $perf['index']=$data['index'];
	    $perf['resultaatVerslagperiode']+=$data['resultaatVerslagperiode'];
	  }


 		if(($this->pdf->GetY() + 22 - $min) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
			$begin = $this->pdf->GetY();

		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
		//$this->SetX($this->marge + $this->widthB[0]);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110,(16-$min),'F');
		$this->pdf->SetFillColor(0);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110,(16-$min));
		$this->pdf->ln(2);
		//$this->pdf->SetX($this->pdf->marge);
		$this->pdf->SetX($this->pdf->marge);


		// kopfontcolor
		if(!$kort)
		{
			$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
			$this->pdf->Cell(80,4, vertaalTekst("Resultaat $omschrijving over verslagperiode",$this->pdf->rapport_taal), 0,0, "L");
			$this->pdf->Cell(30,4, $this->pdf->formatGetal($perf['resultaatVerslagperiode'],2), 0,1, "R");
			$this->pdf->ln();
		}
		$this->pdf->SetX($this->pdf->marge);
		if ($this->pdf->rapport_rendementText)
		  $this->pdf->Cell(80,4, vertaalTekst($this->pdf->rapport_rendementText,$this->pdf->rapport_taal), 0,0, "L");
		else
		  $this->pdf->Cell(80,4, vertaalTekst("Rendement $omschrijving over verslagperiode",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(30,4, $this->pdf->formatGetal($perf['index']-100,2)."%", 0,1, "R");
		$this->pdf->ln(2);
    $eind = $this->pdf->GetY();
	 // echo "$categorie ";
	//  listarray($perf);
	//  exit;
	  //listarray($indexData);exit;
	  if($valuta==true)
	  {

    $valutas = $this->pdf->bepaalValutaKoersen($this->portefeuille,$this->rapportageDatum,'EUR');
	  $this->pdf->setY($begin);
		if(count($valutas) > 4)
		{
			$regels = ceil((count($valutas) / 2));
		}
		else
		  $regels = count($valutas);


		$hoogte = ($regels * 5) + 5;
		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$tweedeCol = 170;
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
		$this->pdf->Rect($this->pdf->marge+$tweedeCol,$this->pdf->getY(),110,$hoogte,'F');
		$this->pdf->SetFillColor(0);
		$this->pdf->Rect($this->pdf->marge+$tweedeCol,$this->pdf->getY(),110,$hoogte);

			$kop = "Actuele koersen";

					$this->pdf->ln(2);
		$this->pdf->SetX($this->pdf->marge+$tweedeCol);

		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
			$this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
			$this->pdf->Cell(100,4, vertaalTekst($kop,$this->pdf->rapport_taal), 0,1, "L");

			$plusmarge = 0;
			$y = $this->pdf->getY();
			$start = false;
			for($a=0; $a < count($valutas); $a++)
			{
				if(count($valutas) > 4)
				{
					if($a >= $regels && $start == false)
					{
						$y2 = $this->pdf->getY();
						$this->pdf->setY($y);
						$plusmarge = 60;
						$start = true;
					}
				}
				$this->pdf->SetX($this->pdf->marge+$tweedeCol+$plusmarge);
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

				if($this->pdf->portefeuilledata['Layout'] == 8)
				  $celWidth=30;
				else
			    $celWidth=35;

				$this->pdf->Cell($celWidth,4, vertaalTekst($valutas[$a][ValutaOmschrijving],$this->pdf->rapport_taal), 0,0, "L");
  			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->Cell(20,4, $this->pdf->formatGetal($valutas[$a][actueleValuta],4), 0,1, "R");
			}

		$this->pdf->SetY($eind);
	  }
	}
}
?>
