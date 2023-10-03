<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L12
{
	function RapportVOLK_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->totaalWaarde=0;
    $this->pdf->rapport_VOLK_decimaal=0;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function getOmschrijving($cellWidth,$omschrijving)
	{
		$omschrijvingWidth = $this->pdf->GetStringWidth($omschrijving);
		//echo "$omschrijvingWidth $cellWidth $omschrijving <br>\n";
		if ($omschrijvingWidth > $cellWidth)
		{
			$dotWidth = $this->pdf->GetStringWidth('...');
			$chars = strlen($omschrijving);
			$newOmschrijving = $omschrijving;
			for ($i = 3; $i < $chars; $i++)
			{
				$omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
				if ($cellWidth > ($omschrijvingWidth + $dotWidth))
				{
					$omschrijving = substr($newOmschrijving, 0, $chars - $i) . '...';
					break;
				}
			}
		}
		return $omschrijving;

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
		if($type == "totaal_uit" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
   
			if($type == "subtotaal")
      {
        $this->pdf->Line($start + 2, $this->pdf->GetY(), $end, $this->pdf->GetY());
      }
			elseif($type == "grandtotaal")
			{
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal_uit")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
	}


	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF, $totaalG = 0, $totaalH = 0)
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
      $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
   		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
      if($totaalG <>0)
        $this->printCol(13,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
	
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
    
    $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
    
	
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
		//	if($totaalC <>0)
		//		$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
      if($totaalG <>0)
        $this->printCol(13,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
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
      case "bu" :
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
		if($type=='bu')
      $this->pdf->line($this->pdf->marge,$this->pdf->getY(),$this->pdf->marge+$this->pdf->widthA[0]-15,$this->pdf->getY(),array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
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
    
    
    return array('totaal'=>$totaal/$this->pdf->ValutaKoersEind,'corrected'=>$totaalCorrected/$this->pdf->ValutaKoersEind);
  }
  

	function addRente($categorie)
	{
		global $__appvar;
		$actueleWaardePortefeuille=0;
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
			" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rente' AND TijdelijkeRapportage.Beleggingscategorie='$categorie' AND ".
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
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bu");


			//$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_taal));
			//$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					if($this->pdf->rapport_VOLK_geenvaluta == 1) {
					}
					else
						$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'],"");

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
						" TijdelijkeRapportage.type = 'rente' AND TijdelijkeRapportage.Beleggingscategorie='$categorie'  AND ".
						" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."'".
						" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
						.$__appvar['TijdelijkeRapportageMaakUniek'].
						" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);

					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{

						$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($this->totaalWaarde/100);
						$percentageVanTotaaltxt = "";

						$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
						{
							$this->pdf->row(array("","","","","","","","",
																$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
																$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
																$percentageVanTotaaltxt));
						}
					}

					// print subtotaal
					//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
					$subtotaalPercentageVanTotaal = ($subtotaalRenteInValuta) / ($this->totaalWaarde/100);

					$this->printSubTotaal('',"", $subtotaalRenteInValuta, "", "", "");//"Subtotaal:",$this->pdf->rapport_taal)


					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
				}
			}

			// totaal op rente

			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), "", $totaalRenteInValuta,"","","");//." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal)
		}
		return $actueleWaardePortefeuille;
	}
  
  function gemiddeldeTransactieValutaKoers($fonds)
  {
    $valutaKoers=$this->pdf->ValutaKoersBegin;
    if($fonds=='')
      return $this->pdf->ValutaKoersBegin;
      
     $query="SELECT Boekdatum,Debet,Credit,Bedrag,Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND Grootboekrekening='FONDS'";
     $DB = new DB();
		$DB->SQL($query); 
		$DB->Query();
    $totaal=0;
    $delen=0;
    while($data = $DB->nextRecord())
    { 
      $bedrag=abs($data['Bedrag']);
      $valutaKoers=getValutaKoers($this->pdf->rapportageValuta,$data['Boekdatum']);
      if($valutaKoers=='')
        $valutaKoers=$this->pdf->ValutaKoersBegin;
      $delen+=$valutaKoers*$bedrag;
      $totaal+=$bedrag;  
    }
    $gemiddelde=$delen/$totaal;

    if($gemiddelde <> 0)
      return $gemiddelde;
    else    
      return $valutaKoers;
  }

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


			$fondsresultwidth = 20;
			$omschrijvingExtra = 15;
    
    $this->pdf->widthA = array(60   +$omschrijvingExtra,18,15,21,21,1,15,23,23,1,15,$fondsresultwidth,15,14);
    $this->pdf->widthB = array(10,50+$omschrijvingExtra,18,15,21,21,1,15,23,23,1,15,$fondsresultwidth,15,14);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R');
    
    // voor kopjes

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
		$totaalWaarde = $totaalWaarde['totaal'];
		$this->totaalWaarde=$totaalWaarde;

		$actueleWaardePortefeuille = 0;
    

      $beginBoekdatumQuery='';

			$query = "SELECT $beginBoekdatumQuery".
			" Beleggingscategorien.Omschrijving, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
	//		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " AS subtotaalbegin, ".// Aanpassing ivm transacties bij geen EUR rapportages.
        //(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. ",
     "
		SUM(
			IF (
				TijdelijkeRapportage.type = 'rekening',
				TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind." ,
				0
			)
		) as liqWaarde,
       
     SUM(IF(TijdelijkeRapportage.type='rekening',
     0,
       
       IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / historischeRapportageValutakoers ))
       )) as subtotaalbegin,
      ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type IN ('fondsen','rekening') AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		
		$lastBeleggingscategorie='leeg';
    $totaalbegin = 0;
    $totaalactueel = 0;
    $totaalvalutaresultaat = 0;
    $totaalfondsresultaat = 0;
    $totaalpercentage = 0;

    $totaalResultaat = 0;
    $totaalBijdrage = 0;
    $totaaldividend=0;
    $totaalLiq=0;
    $title='';
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
      
     // echo $this->pdf->ValutaKoersBegin." ".getValutaKoers($this->pdf->rapportageValuta,$categorien['boekdatum'])." |".$categorien['valuta']."| ".$categorien['boekdatum']."<br>\n";
//      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '' && $this->pdf->rapportageValuta != $categorien['valuta'])
//        $categorien['subtotaalbegin']=$categorien['subtotaalbegin']*$this->pdf->ValutaKoersBegin/getValutaKoers($this->pdf->rapportageValuta,$categorien['boekdatum']);

			// print totaal op hele categorie.
			if(!empty($lastCategorie) && $lastCategorie <> $categorien['Omschrijving'])
			{
				if($lastCategorie<>'')
			  	$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal);//." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

        $procentResultaat = (($totaalactueel - $totaalLiq - $totaalbegin) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

					$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat,false,$totaaldividend);

				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalLiq = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;
        $totaaldividend =0;
				$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);
			}

			if($lastCategorie <> $categorien['Omschrijving'])
			{
					$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bu");
			}
			// subkop (valuta)
			//if($categorien['valuta'] == $this->pdf->rapportageValuta)
			//  $beginQuery = 'beginwaardeValutaLopendeJaar';
			//else
			//  $beginQuery = 'historischeRapportageValutakoers';

				$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'], "");
				/// $beginQuery
        $subquery = "SELECT $beginBoekdatumQuery
        TijdelijkeRapportage.fondsOmschrijving,
        TijdelijkeRapportage.beginwaardeValutaLopendeJaar, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.Bewaarder, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro  as beginPortefeuilleWaardeEuro, ".
				//" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,TijdelijkeRapportage.type,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.type,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
				" TijdelijkeRapportage.type IN ('fondsen','rekening') AND ".
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
		//	  if($categorien['valuta'] != $this->pdf->rapportageValuta)
    //    {
    //      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '' && $categorien['boekdatum'] <> '')
    //         $subdata['beginPortefeuilleWaardeEuro']=$subdata['beginPortefeuilleWaardeEuro']*$this->pdf->ValutaKoersBegin/getValutaKoers($this->pdf->rapportageValuta,$categorien['boekdatum']);
    //    }
    
      if($subdata['valuta'] == $this->pdf->rapportageValuta)
      {
			  $subdata['beginPortefeuilleWaardeEuro'] = $subdata['beginPortefeuilleWaardeEuro']  / $subdata['beginwaardeValutaLopendeJaar'];
			}
      elseif($this->pdf->rapportageValuta <> '' && $this->pdf->rapportageValuta <> 'EUR' )
      {
        $subdata['beginPortefeuilleWaardeEuro'] = $subdata['beginPortefeuilleWaardeEuro'] / $this->gemiddeldeTransactieValutaKoers($subdata['fonds']);
      }
      else
      {
			  $subdata['beginPortefeuilleWaardeEuro'] = $subdata['beginPortefeuilleWaardeEuro'] / $this->pdf->ValutaKoersBegin;
      }
        
        $dividend=$this->getDividend($subdata['fonds']);
        
        
        $fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
        $fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
        $valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
        $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
        if($subdata['type']=='rekening')
        {
          $procentResultaat = 0;
        }
				//$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				//$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
				//$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
				//$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));


				if($subdata['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

				$percentageVanTotaaltxt = "";


				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

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

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");

				//$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,"http://url?code=");

					$this->pdf->Cell($this->pdf->widthB[1],4,$this->getOmschrijving($this->pdf->widthB[1]-11,$subdata['fondsOmschrijving']),null,null,null,null,null);

					$this->pdf->setX($this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1]-10);
					$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['Bewaarder'],null,null,null,null,null);


				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


        if($subdata['type']=='rekening')
        {
          $this->pdf->row(array("",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            $percentageVanTotaaltxt,
                            "",
                            "",
                            "",
                            "")
          );
        }
        else
        {
          $this->pdf->row(array("",
                            "",
                            $this->formatAantal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                            $this->formatGetal($subdata['beginwaardeLopendeJaar'], 2),
                            $this->formatGetal($subdata['beginPortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            "",
                            $this->formatGetal($subdata['actueleFonds'], 2),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            $percentageVanTotaaltxt,
                            $fondsResultaattxt,
                            $valutaResultaattxt,
                            $dividendtxt,
                            $procentResultaattxt)
          );
          $subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
          $subtotaal['fondsResultaat'] +=$fondsResultaat;
          $subtotaal['valutaResultaat'] +=$valutaResultaat;
          $subtotaal['totaalResultaat'] +=$subTotaalResultaat;
          $subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
          $subtotaal['totaalDividend'] += $dividend['totaal'];
          $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];
        }



				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];

		

			}
			$lastBeleggingscategorie=$categorien['beleggingscategorie'];

			//	$procentResultaat = (($categorien['subtotaalactueel']  - $categorien['subtotaalbegin'] ) / ($categorien['subtotaalbegin']  /100));
      $procentResultaat = (( ($categorien['subtotaalactueel']-$categorien['liqWaarde'])  - $categorien['subtotaalbegin'] + $subtotaal['totaalDividendCorrected'] ) / ($categorien['subtotaalbegin']  /100));
				if($categorien['subtotaalbegin'] < 0)
					$procentResultaat = -1 * $procentResultaat;


			$this->printSubTotaal('', $categorien['subtotaalbegin'], $categorien['subtotaalactueel'],"", $subtotaal['fondsResultaat'], $subtotaal['valutaResultaat'], $procentResultaat,$subtotaal['totaalDividend']);//vertaalTekst("Subtotaal:",$this->pdf->rapport_taal)


			// totaal op categorie tellen
			$totaalbegin   += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];
      $totaalLiq += $categorien['liqWaarde'];

			$totaalfondsresultaat  += $subtotaal['fondsResultaat'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];
      $totaaldividend        += $subtotaal['totaalDividend'];
      $totaaldividendCorrected        += $subtotaal['totaalDividendCorrected'];

			$lastCategorie = $categorien['Omschrijving'];

			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];
      $grandtotaaldividend  += $subtotaal['totaalDividend'];
      $grandtotaaldividendCorrected  += $subtotaal['totaalDividendCorrected'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		//$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
    $procentResultaat = (($totaalactueel - $totaalLiq - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
		
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;
		if($lastCategorie<>'')
				$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,"",$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);//." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal)

		$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);
    $lastPrintedRenteCategorie=$lastBeleggingscategorie;
		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , TijdelijkeRapportage.beleggingscategorie,".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekeningUIT'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY SUBSTR(TijdelijkeRapportage.rekening,1,LENGTH(TijdelijkeRapportage.rekening)-3), TijdelijkeRapportage.valutaVolgorde,TijdelijkeRapportage.fondsOmschrijving";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			//$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");

				$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bu");


			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}

			foreach($liqiteitenBuffer as $data)
			{
				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];
				$subtotaalPercentageVanTotaal  = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

				$subtotaalPercentageVanTotaaltxt = "";



				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt));



        
			  	$lastBeleggingscategorie=$data['beleggingscategorie'];
   		}

			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);

			// totaal liquiditeiten

				$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,"","","");

				} // einde liquide

		//if($lastBeleggingscategorie=='')
		//	$lastBeleggingscategorie='Liquiditeiten';
    if($lastBeleggingscategorie<>$lastPrintedRenteCategorie)
	  	$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);

		// check op totaalwaarde!
    /*
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}
*/
		$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,"",$grandtotaalfonds,$grandtotaalvaluta,"",true,$grandtotaaldividend);


		$this->pdf->ln();

//printIndex($this);
/*
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


		if($this->pdf->portefeuilledata[AEXVergelijking] > 0)
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
    */
	}
  
  function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}
}
?>