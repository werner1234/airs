<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/29 15:45:16 $
File Versie					: $Revision: 1.3 $

$Log: RapportHSE_L44.php,v $
Revision 1.3  2019/05/29 15:45:16  rvv
*** empty log message ***

Revision 1.2  2018/06/13 15:26:14  rvv
*** empty log message ***

Revision 1.1  2018/06/09 15:58:54  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSE_L44
{
	function RapportHSE_L44($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Portefeuilleoverzicht";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
    $this->aandeel=1;
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
	  if($this->aandeel <> 1)
	    $waarde=round($waarde,0);
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

		/*
		echo $this->pdf->pagebreak;
		echo "<br>";
		echo $this->pdf->GetY();
		echo "<br>";
		*/
		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2)
		{
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
		else if ($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}
		else
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(5,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(9,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			if($totaalG <>0)
				$this->printCol(15,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			if($totaalH <>0)
			  $this->printCol(16,$this->formatGetal($totaalH,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");


			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}
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


		//	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");

			if($totaalB <>0)
				$this->printCol(4,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(8,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(5,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
      if($totaalG <>0)
		    $this->printCol(10,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//dividend
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//koersen
			if($totaalE <>0)
				$this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//valuta
			if($totaalF <>0)
				$this->printCol(13,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
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
  
 function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
     while($data = $DB->nextRecord())
     { 
       if($data['type']=='rente')
         $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
       elseif($data['type']=='fondsen')  
         $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
     }
     
     $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
     $totaalCorrected=$totaal;

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query); 
		$DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    { 
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      } 
     // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }

    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
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
		$omschrijvingExtra = 10;
    if(substr($this->rapportageDatum,0,4)<2016)
      $this->pdf->portefeuilledata['PerformanceBerekening']=4;
    
		$query="SELECT Vermogensbeheerders.VerouderdeKoersDagen FROM Vermogensbeheerders Inner Join Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$dagen = $DB->nextRecord();
    $maxDagenOud=$dagen['VerouderdeKoersDagen'];

			$this->pdf->widthB = array(50+$omschrijvingExtra,21,18,18,21,21,5,23,25,5,21,$fondsresultwidth,15,12,15);
			$this->pdf->alignB = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
			$this->pdf->widthA = array(18,68+$omschrijvingExtra,15,21,21,15,20,20,15,22,21,$fondsresultwidth,15,15);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');



		$this->pdf->AddPage();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;


			$query = "SELECT 
      TijdelijkeRapportage.beleggingscategorie as beleggingscategorie,
TijdelijkeRapportage.afmCategorieVolgorde,
TijdelijkeRapportage.beleggingscategorieOmschrijving as Omschrijving,
       IF ('EUR' <> '".$this->pdf->rapportageValuta."',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar * ".$this->aandeel."),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." * ".$this->aandeel. ") as subtotaalbegin,
       SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." AS subtotaalactueel FROM ".
			" TijdelijkeRapportage 
      LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  
      LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) 
       WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
			" ORDER BY beleggingscategorieVolgorde asc, TijdelijkeRapportage.beleggingscategorie ";

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
				$title = vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

        $procentResultaat = (($totaalactueel - $totaalbegin + $totaalDividendCorrected) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat,false, $totaalDividend);

				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalDividend=0;
        $totaalDividendCorrected=0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if($lastCategorie <> $categorien['Omschrijving'])
			{
				if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2 )
				{
					$this->pdf->SetWidths($this->pdf->widthB);
					$this->pdf->SetAligns($this->pdf->alignB);
					$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
					$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
					$this->pdf->row(array(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal)));
				}
				else
				{
					$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal), "bi");
				}
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
				" TijdelijkeRapportage.totaalAantal * ".$this->aandeel." as totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".

				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel.") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
        round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage 
           WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

		
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
//echo $subquery."<br><br>";exit;
			while($subdata = $DB2->NextRecord())
			{
				$fondsResultaat = ($subdata[actuelePortefeuilleWaardeInValuta] - $subdata[beginPortefeuilleWaardeInValuta]) * $subdata[actueleValuta] / $this->pdf->ValutaKoersEind;
				$valutaResultaat = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro] - $fondsResultaat ;
        $dividend=$this->getDividend($subdata['fonds']);
        //$fondsResultaat+=$dividend;
				$fondsResultaatprocent = ($fondsResultaat / $subdata[beginPortefeuilleWaardeEuro]) * 100;

				$procentResultaat = (($subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro] + $dividend['corrected']) / ($subdata[beginPortefeuilleWaardeEuro] /100));
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

				//$this->pdf->Cell($this->pdf->widthB[0],4,"");

				$this->pdf->Cell($this->pdf->widthB[0],4,$subdata[fondsOmschrijving],null,null,null,null,null);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				if($subdata['koersLeeftijd'] > $maxDagenOud)
				  $markering="*";
				else
				  $markering="";
          
        if($dividend['totaal'] <> 0)  
          $dividendTxt=$this->formatGetal($dividend['totaal'],0);
	      else
          $dividendTxt='';
				$this->pdf->row(array('',$this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$subdata[Valuta],
												$this->formatGetal($subdata[actueleFonds],2).$markering,
												$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
												"",
												$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
												$this->formatGetal($subdata[beginPortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												"",
                        $dividendTxt,
												$fondsResultaattxt,
												$valutaResultaattxt,
												$procentResultaattxt	)	);
	
				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
        $subtotaal['dividend'] +=$dividend['totaal'];
        $subtotaal['dividendCorrected'] +=$dividend['corrected'];
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;

			}


			// totaal op categorie tellen
			$totaalbegin   += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];

      $totaalDividend  += $subtotaal['dividend'];
      $totaalDividendCorrected  += $subtotaal['dividendCorrected'];
			$totaalfondsresultaat  += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage      += $subtotaal[percentageVanTotaal];

			$lastCategorie = $categorien[Omschrijving];

      $grandtotaalDividend  += $subtotaal['dividend'];
      $grandtotaalDividendCorrected  += $subtotaal['dividendCorrected'];
			$grandtotaalvaluta += $subtotaal[valutaResultaat];
			$grandtotaalfonds  += $subtotaal[fondsResultaat];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin + $totaalDividendCorrected) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat,false,$totaalDividend);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) * ".$this->aandeel." as subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel." as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as subtotaalactueel FROM ".
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
				$this->pdf->row(array(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal)));
	

			//$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_taal));
			//$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro, ".
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

						//$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[0],4,$subdata[fondsOmschrijving].$rentePeriodetxt);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						$this->pdf->row(array("","","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
														"","","", "",
														$percentageVanTotaaltxt));
						

					}

					// print subtotaal
					//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
					$subtotaalPercentageVanTotaal = ($subtotaalRenteInValuta) / ($totaalWaarde/100);
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

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel."  as actuelePortefeuilleWaardeEuro, ".
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
			//$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
				$this->pdf->row(array(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal)));


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
				if($this->pdf->rapport_layout == 8 && $data['Deposito'] == 1)
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

				//$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[0],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			

						$this->pdf->row(array("",
												"",
															$data['valuta'],"",
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

		// print grandtotaal
	
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true,$grandtotaalDividend);
	
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->MultiCell(200,4,"Koersen met een * zijn meer dan $maxDagenOud dagen oud.", 0, "L");
		$this->pdf->skipRapportHeader=true;
		$this->pdf->ln();

		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
   		$this->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,false,'Valutakoers');
    }


		if($this->pdf->rapport_VOLK_rendement == 1 && $this->aandeel == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		elseif ($this->pdf->rapport_VOLK_rendement == 2)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;

//		if($this->pdf->rapport_layout == 8)
//		  include_once('indexGrafiek.php');
		// index vergelijking afdrukken


		if($this->pdf->portefeuilledata['AEXVergelijking'] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
		  {
			//  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
					  $DB  = new DB();
		    $DB2 = new DB();

			    $query = "SELECT Indices.Beursindex, Indices.specialeIndex, Fondsen.Omschrijving,BeleggingscategoriePerFonds.Beleggingscategorie,Beleggingscategorien.Omschrijving as CategorieOmschrijving
            FROM Indices
            Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
            LEFT Join BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds  AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
            LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
            WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
            ORDER BY Beleggingscategorien.Afdrukvolgorde, Indices.Afdrukvolgorde ";
  $DB->SQL($query);
 //echo $query;
	$DB->Query();
	while($data = $DB->NextRecord())
	{
	  $data['Beleggingscategorie']='';
      $indexen[$data['Beursindex']]['Omschrijving']=$data['Omschrijving'];
      $indexen[$data['Beursindex']]['Categorie']=$data['Beleggingscategorie'];
      $indexen[$data['Beursindex']]['CategorieOmschrijving']=$data['CategorieOmschrijving'];
      if($data['Beleggingscategorie'] !='')
      $indexCategorien[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];

	}
		    $hoogte = (count($indexen) * 4) + 8 + (count($indexCategorien)*8);
		    if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		    {
			    $this->pdf->AddPage();
			    $this->pdf->ln();
		    }

		  	$this->pdf->ln();
		    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		    $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),230,$hoogte,'F');
		    $this->pdf->SetFillColor(0);
		    $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),230,$hoogte);
		    $this->pdf->SetX($this->pdf->marge);

	    	$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		    $this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
		    $this->pdf->Cell(50,4, vertaalTekst("Indexvergelijking",$this->pdf->rapport_taal), 0,0, "L");

		    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);

		    $this->pdf->SetX($this->pdf->marge);
		    $this->pdf->SetWidths(array(80,33,23,23,23,23,23));
				$this->pdf->SetAligns(array('L','R','R','R','R','R','R'));
		    $this->pdf->row(array("",'Koers '.date("d-m-Y",db2jul($this->rapportageDatumVanaf)),"Perf. YTD","Perf. 3MND","Perf. 1Y","Perf. 3Y","Perf. 5Y"));

		    $startJul=db2jul($this->rapportageDatumVanaf);
		    $rapJul=db2jul($this->rapportageDatum);
		    $dates=array('start'=>$startJul,
		                 'rap'=>$rapJul,
		                 '3MND'=>mktime(0,0,0,date('m',$rapJul)-3,date('d',$rapJul),date('Y',$rapJul)),
		                 '1Y'=>mktime(0,0,0,date('m',$rapJul),date('d',$rapJul),date('Y',$rapJul)-1),
		                 '3Y'=>mktime(0,0,0,date('m',$rapJul),date('d',$rapJul),date('Y',$rapJul)-3),
		                 '5Y'=>mktime(0,0,0,date('m',$rapJul),date('d',$rapJul),date('Y',$rapJul)-5));

		    foreach($indexen as $indexFonds=>$indexData)
		    {
		      foreach ($dates as $periode=>$dateJul)
            $koers[$periode]=$this->getFondsKoers($indexFonds,jul2sql($dateJul));

          if($indexData['Categorie'] != $lastCat && $indexData['Categorie']!='')
          {
            $this->pdf->ln();
            $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
            $this->pdf->row(array($indexData['CategorieOmschrijving']));
          }
            $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

          $waarden=array();
          $waarden[0]= $this->pdf->formatGetal($koers['start'],2);
          if($indexData['Categorie'] != 'Liquiditeiten')
          {
            if($koers['rap'])
              $waarden[1]=$this->pdf->formatGetal(($koers['rap']/$koers['start']*100-100),2);
            if($koers['3MND'])
              $waarden[2]=$this->pdf->formatGetal(($koers['rap']/$koers['3MND']*100-100),2);
            if($koers['1Y'])
              $waarden[3]= $this->pdf->formatGetal(($koers['rap']/$koers['1Y']*100-100),2);
            if($koers['3Y'])
              $waarden[4]= $this->pdf->formatGetal(($koers['rap']/$koers['3Y']*100-100),2);
            if($koers['5Y'])
              $waarden[5]= $this->pdf->formatGetal(($koers['rap']/$koers['5Y']*100-100),2);
          }
          else
          {
             $waarden[1]=$this->pdf->formatGetal($koers['rap'],2);
             $waarden[2]=$this->pdf->formatGetal($koers['3MND'],2);
             $waarden[3]=$this->pdf->formatGetal($koers['1Y'],2);
             $waarden[4]=$this->pdf->formatGetal($koers['3Y'],2);
             $waarden[5]=$this->pdf->formatGetal($koers['5Y'],2);
          }
          $this->pdf->row(array($indexData['Omschrijving'], $waarden[0], $waarden[1], $waarden[2], $waarden[3], $waarden[4], $waarden[5]));
          $lastCat=$indexData['Categorie'];
		    }

		  }
		}
    if(isset(	$this->pdf->skipRapportHeader))
			unset(	$this->pdf->skipRapportHeader);
	}

	function getFondsKoers($fonds,$datum)
	{
	    $DB2=new DB();
	  	$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$datum."' AND Fonds = '".$fonds."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers = $DB2->LookupRecord();
			return $koers['Koers'];
	}
  
  
  function printValutaPerformanceOverzicht($portefeuille, $rapportageDatum, $rapportageDatumVanaf,$omkeren=false,$kop='Valuta')
  {
    global $__appvar;
    $this->pdf->ln();
    
    $metJanuari = $this->pdf->rapport_valutaPerformanceJanuari;
    
    if($metJanuari == true)
    {
      $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
      $rapJaar = date('Y',$julRapDatumVanaf);
      $dagMaand = date('d-m',$julRapDatumVanaf);
      $januariDatum = $rapJaar.'-01-01';
      if($dagMaand =='01-01')
        $metJanuari = false;
    }
    
    if($this->pdf->printValutaPerformanceOverzichtProcentTeken)
      $teken = '%';
    else
      $teken = '';
    
    // selecteer distinct valuta.
    $q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta,  TijdelijkeRapportage.rapportageDatum".
      " FROM TijdelijkeRapportage, Valutas ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ". //OR TijdelijkeRapportage.rapportageDatum = '".$rapportageDatumVanaf."' )
      " TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' AND ".
      " TijdelijkeRapportage.valuta = Valutas.Valuta "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY Valutas.Afdrukvolgorde asc, TijdelijkeRapportage.rapportageDatum";
    debugSpecial($q,__FILE__,__LINE__);
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    
    if(1)
    {
      while ($valuta = $DB->NextRecord())
      {
        $valutas[$valuta['val']][$valuta['rapportageDatum']]['omschrijving'] = $valuta['ValutaOmschrijving'];
        $valutas[$valuta['val']][$valuta['rapportageDatum']]['koers'] = $valuta['actueleValuta'] / $this->pdf->ValutaKoersEind;
      }
      
      $valutaKeys = array_keys($valutas);
      if($this->pdf->rapportageValuta == 'USD' && !in_array('EUR',$valutaKeys))
      {
        $valutaKeys[] = 'EUR';
      }
      else
      {
        if(!in_array('USD',$valutaKeys))
          $valutaKeys[] = 'USD';
      }
      
      
      foreach ($valutaKeys as $valuta)
      {
        $query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
               FROM Valutas ,Valutakoersen
               WHERE Valutas.valuta = Valutakoersen.valuta AND
               Valutakoersen.datum <= date '".$rapportageDatumVanaf."' AND
               Valutas.valuta = '".$valuta."'
               ORDER BY Valutakoersen.datum desc LIMIT 1";
        $DB->SQL($query);
        $DB->Query();
        $valutawaarden = $DB->NextRecord();
        
        $valutas[$valuta][$rapportageDatumVanaf]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
        $valutas[$valuta][$rapportageDatumVanaf]['koers'] = $valutawaarden['Koers'] / $this->pdf->ValutaKoersBegin;
        
        $query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
               FROM Valutas ,Valutakoersen
               WHERE Valutas.valuta = Valutakoersen.valuta AND
               Valutakoersen.datum <= date '".$rapportageDatum."' AND
               Valutas.valuta = '".$valuta."'
               ORDER BY Valutakoersen.datum desc LIMIT 1";
        $DB->SQL($query);
        $DB->Query();
        $valutawaarden = $DB->NextRecord();
        
        $valutas[$valuta][$rapportageDatum]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
        $valutas[$valuta][$rapportageDatum]['koers'] = $valutawaarden['Koers'] / $this->pdf->ValutaKoersEind;
        
        if($metJanuari == true)
        {
          $query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
                 FROM Valutas ,Valutakoersen
                 WHERE Valutas.valuta = Valutakoersen.valuta AND
                 Valutakoersen.datum <= date '$januariDatum' AND
                 Valutas.valuta = '".$valuta."'
                 ORDER BY Valutakoersen.datum desc LIMIT 1";
          $DB->SQL($query);
          $DB->Query();
          $valutawaarden = $DB->NextRecord();
          
          $valutas[$valuta][$januariDatum]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
          $valutas[$valuta][$januariDatum]['koers'] = $valutawaarden['Koers'] / $this->pdf->ValutaKoersStart;
          $extraBreedte = 50;
        }
      }
      //listarray($valutas);
      //$kop = "Valuta";
      
      $regels = count($valutas);
      $hoogte = ($regels * 4) + 8;
      if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
      {
        $this->pdf->AddPage();
        $this->pdf->ln();
      }
      
      $this->pdf->ln();
      $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
      $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110+$extraBreedte,$hoogte,'F');
      $this->pdf->SetFillColor(0);
      $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110+$extraBreedte,$hoogte);
      $this->pdf->SetX($this->pdf->marge);
      
      // kopfontcolor
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
      $this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
      $this->pdf->Cell(40,4, vertaalTekst($kop,$this->pdf->rapport_taal), 0,0, "L");
      
      $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
      if($metJanuari == true)
        $this->pdf->Cell(23,4, date("d-m-Y",db2jul($januariDatum)), 0,0, "R");
      $this->pdf->Cell(23,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), 0,0, "R");
      $this->pdf->Cell(23,4, date("d-m-Y",db2jul($rapportageDatum)), 0,0, "R");
      if($metJanuari == true)
      {
        $this->pdf->Cell(23,4, vertaalTekst("Performance",$this->pdf->rapport_taal), 0,0, "R");
        $this->pdf->Cell(23,4, vertaalTekst("Jaar Perf.",$this->pdf->rapport_taal), 0,1, "R");
      }
      else
        $this->pdf->Cell(23,4, vertaalTekst("Performance",$this->pdf->rapport_taal), 0,1, "R");
      
      
      foreach($valutas as $key=>$data)
      {
        $performance = ($data[$rapportageDatum]['koers'] - $data[$rapportageDatumVanaf]['koers']) / ($data[$rapportageDatumVanaf]['koers']/100 );
//echo 		"	$performance = (".$data[$rapportageDatum]['koers']." - ".$data[$rapportageDatumVanaf]['koers'].") / (".$data[$rapportageDatumVanaf]['koers']."/100 );";
        $this->pdf->Cell(40,4, vertaalTekst($data[$rapportageDatumVanaf]['omschrijving'],$this->pdf->rapport_taal), 0,0, "L");
        if($metJanuari == true)
        {
          if($omkeren==true)
            $this->pdf->Cell(23,4, $this->pdf->formatGetal(1/$data[$januariDatum]['koers'],4), 0,0, "R");
          else
            $this->pdf->Cell(23,4, $this->pdf->formatGetal($data[$januariDatum]['koers'],4), 0,0, "R");
        }
        if($omkeren==true)
          $this->pdf->Cell(23,4, $this->pdf->formatGetal(1/$data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
        else
          $this->pdf->Cell(23,4, $this->pdf->formatGetal($data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
        if($omkeren==true)
          $this->pdf->Cell(23,4, $this->pdf->formatGetal(1/$data[$rapportageDatum]['koers'],4), 0,0, "R");
        else
          $this->pdf->Cell(23,4, $this->pdf->formatGetal($data[$rapportageDatum]['koers'],4), 0,0, "R");
        if($metJanuari == true)
        {
          $this->pdf->Cell(23,4, $this->pdf->formatGetal($performance,2).$teken, 0,0, "R");
          $performanceJaar = ($data[$rapportageDatum]['koers'] - $data[$januariDatum]['koers']) / ($data[$januariDatum]['koers']/100 );
          $this->pdf->Cell(23,4, $this->pdf->formatGetal($performanceJaar,2).$teken, 0,1, "R");
        }
        else
          $this->pdf->Cell(23,4, $this->pdf->formatGetal($performance,2).$teken, 0,1, "R");
      }
      $this->pdf->ln();
      $this->pdf->ln();
    }
  }
}
?>
