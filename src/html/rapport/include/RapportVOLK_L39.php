<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportCASH.php");

class RapportVOLK_L39
{
	function RapportVOLK_L39($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		$this->pdf->rapport_titel =	"Overzicht portefeuille";
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
		$this->portefeuille = $portefeuille;
    //if(db2jul(date('Y',$this->pdf->rapport_datum)."-01-01")<db2jul($this->pdf->PortefeuilleStartdatum))
    //  $this->rapportageDatumVanaf = $this->pdf->PortefeuilleStartdatum;
    //else
	  //	$this->rapportageDatumVanaf = date('Y',$this->pdf->rapport_datum)."-01-01";//$rapportageDatumVanaf;
      

		$this->rapportageDatum = $rapportageDatum;
    
    	$this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
      $this->db = new DB();
	}

	function formatGetal($waarde, $dec, $nulTonen=false)
	{
	  if($waarde==0 && $nulTonen==false)
      return '';
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
    if($waarde==0)
      return '';
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
    if($waarde==0)
      return '';
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
		if ($type == 'tekst')
		{
		  $this->pdf->fillCell[] = 1;
      $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);
      $y = $this->pdf->getY();
      $this->pdf->SetWidths(array(array_sum($this->pdf->widthB)));
      $this->pdf->row(array(""));
      $this->pdf->setY($y);
      unset($this->pdf->fillCell);
    
		  //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		  $this->pdf->Cell(10,4,'', 0,0, "L");
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
		//		$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
		//		$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
		//		$this->pdf->setDash(1,1);
		//		$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
		//		$this->pdf->setDash();
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

  		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			
			if($totaalA <>0)
				$this->printCol(5 ,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
			if($totaalB <>0)
				$this->printCol(9 ,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalC <>0)
				$this->printCol(11,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0)
				$this->printCol(12,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
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

		$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";


			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
      if($totaalB <>0)
				$this->printCol(8,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(9,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
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
	//	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}
	
	function getRekeningBeginWaarde($rekening='')
	{
		global $__appvar;
		$query="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE rekening='$rekening' AND rapportageDatum ='".$this->rapportageDatumVanaf."'  AND portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $waarde = $DB->nextRecord();
    return $waarde['actuelePortefeuilleWaardeEuro'];
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
			$omschrijvingExtra = 9;


			$this->pdf->widthB = array(10,50+$omschrijvingExtra,25,13,20,25,1,19,24,19,15,22,17,14);
			$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
			$this->pdf->widthA = array(60+$omschrijvingExtra,25,13,19,25,1,20,21,15,15,22,17,14);
			$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');

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

			$query = "SELECT TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorieOmschrijving AS Omschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.valuta,
Fondsen.rating as fondsRating,
TijdelijkeRapportage.Lossingsdatum,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.type,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				   TijdelijkeRapportage.portefeuille FROM ".
			" TijdelijkeRapportage Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds      
      ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, 
      TijdelijkeRapportage.beleggingscategorieVolgorde asc, 
      TijdelijkeRapportage.beleggingssectorVolgorde asc,
      TijdelijkeRapportage.fondsOmschrijving asc,
      TijdelijkeRapportage.type asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query); 
		$DB->Query();
 //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);

		while($fonds = $DB->NextRecord())
		{
		  if( $fonds['hoofdcategorieOmschrijving'] == '')
		    $fonds['hoofdcategorieOmschrijving'] ='Geen hoofdcategorie';
		  if($fonds['Omschrijving']=='')
		    $fonds['Omschrijving']='Geen categorie';
		  if($fonds['beleggingssectorOmschrijving']=='')
		    $fonds['beleggingssectorOmschrijving']='Geen sector';

      if($fonds['beleggingscategorie'] <> 'AAND')
        $fonds['beleggingssectorOmschrijving']='';
        

     if($fonds['Lossingsdatum'] <> '')
        $lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
     else
        $lossingsJul=0;
     $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

			$koers=getRentePercentage($fonds['fonds'],$this->rapportageDatum);

     
     if($lossingsJul > 0 && $fonds['type']=='fondsen')
	   {
       $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;
       
		   $p = $fonds['actueleFonds'];
	     $r = $koers['Rentepercentage']/100;
	     $b = $this->cashfow->fondsDataKeyed[$fonds['fonds']]['lossingskoers'];// 100
	     $y = $jaar;
       
       $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
       $ytm=$this->formatGetal($ytm,2)." %";
       //echo $fonds['fonds']."$ytm = p $p,r $r,b $b,y $y <br>\n";
     }
     else
        $ytm='';
          
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorieKey <> $fonds['beleggingscategorie'] && !empty($lastCategorie) )
			{

				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
        $procentResultaat = (($totaalactueel - $totaalactueelRente - $totaalbegin) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat);

				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalactueelRente =0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if($lastHCategorieKey <> $fonds['hoofdcategorie'])
			{

			 /*
			   if(isset($hcatTotaal))
         {
           $procentResultaat = (($hcatTotaal['totaalactueel'] - $hcatTotaal['totaalbegin']) / ($hcatTotaal['totaalbegin'] /100));
           $this->printTotaal('Totaal '.$lastHCategorie, $hcatTotaal['totaalbegin'],$hcatTotaal['totaalactueel'],
           $hcatTotaal['percentageVanTotaal'],$hcatTotaal['fondsResultaat'],$hcatTotaal['valutaResultaat'],$procentResultaat);
           unset($hcatTotaal);
         }
*/
       
					$this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
			}

			if($lastCategorieKey <> $fonds['beleggingscategorie'])
			{
					$this->printKop('    '.vertaalTekst($fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
			}
			if($lastSector <> $fonds['beleggingssectorOmschrijving'] && $fonds['beleggingssectorOmschrijving'] <> '')
			{
					$this->printKop('       '.vertaalTekst($fonds['beleggingssectorOmschrijving'],$this->pdf->rapport_taal), "b");
			}



				$fondsResultaat = ($fonds['actuelePortefeuilleWaardeInValuta'] - $fonds['beginPortefeuilleWaardeInValuta']) * $fonds['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $fonds['beginPortefeuilleWaardeEuro']) * 100;
				$valutaResultaat = $fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro'] - $fondsResultaat;

				$procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro']) / ($fonds['beginPortefeuilleWaardeEuro'] /100));
				if($fonds['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($fonds['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc);
        if($percentageVanTotaaltxt <> '')
          $percentageVanTotaaltxt.=" %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);
if($fonds['type']=='rekening' || $fonds['type']=='rente')
{
  $fondsResultaat=0;
  $fondsResultaatprocent=0;
  $valutaResultaat=0;
  $procentResultaat=0;
  $procentResultaattxt='';
  $fonds['totaalAantal']=0;
  $fonds['actueleFonds']=0;
  $fonds['beginwaardeLopendeJaar']=0;
  $fonds['fondsOmschrijving']=vertaalTekst($fonds['fondsOmschrijving'],$this->pdf->rapport_taal);
}
      
      if($fonds['rekening']<>'')
      {
        $fonds['beginPortefeuilleWaardeEuro']=$this->getRekeningBeginWaarde($fonds['rekening']);
      }

if($fonds['type']=='rente')
{
  $fonds['fondsOmschrijving']=vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal);
  $percentageVanTotaaltxt='';
  $beginWaardeEur=  $this->formatGetal($fonds['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal);
}
else
  $beginWaardeEur=  $this->formatGetal($fonds['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal,true);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
			//	$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			//	$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$this->pdf->Cell($this->pdf->widthB[1],4,$fonds['fondsOmschrijving'],null,null,null,null,null);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        /*
        if($fill==true)
		    {
		      $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
		      $fill=false;
		    }
		    else
		    {
		      $this->pdf->fillCell=array();
		      $fill=true;
		    }
          */

        
				$this->pdf->row(array("",
													"",
													$this->formatAantal($fonds['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$fonds['valuta'],
                          $this->formatGetal($fonds['beginwaardeLopendeJaar'],2),
													$beginWaardeEur,
													"",
													$this->formatGetal($fonds['actueleFonds'],2),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal,true),
													$percentageVanTotaaltxt,
                          $ytm,
													$fondsResultaattxt,
													$valutaResultaattxt,
													$procentResultaattxt));



				$valutaWaarden[$categorien['valuta']] = $fonds['actueleValuta'];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
        
        $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$hcatTotaal['fondsResultaat'] +=$fondsResultaat;
				$hcatTotaal['valutaResultaat'] +=$valutaResultaat;
				$hcatTotaal['totaalbegin'] +=$fonds['beginPortefeuilleWaardeEuro'];
				$hcatTotaal['totaalactueel'] += $fonds['actuelePortefeuilleWaardeEuro'];


			// totaal op categorie tellen
			$totaalbegin   += $fonds['beginPortefeuilleWaardeEuro'];
			$totaalactueel += $fonds['actuelePortefeuilleWaardeEuro'];
      if($fonds['type']=='rente')
        $totaalactueelRente+=$fonds['actuelePortefeuilleWaardeEuro'];

			$totaalfondsresultaat  += $subtotaal['fondsResultaat'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];

			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastCategorieKey = $fonds['beleggingscategorie'];
			$lastHCategorieKey = $fonds['hoofdcategorie'];
			$lastSector = $fonds['beleggingssectorOmschrijving'];
			$lastSectorKey = $fonds['beleggingssector'];


			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;
		
		if($lastCategorie=='Liquiditeiten')
      $procentResultaat='';

		// totaal voor de laatste categorie
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin,
																											 $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);
		  /*
       if(isset($hcatTotaal))
         {
           $procentResultaat = (($hcatTotaal['totaalactueel'] - $hcatTotaal['totaalbegin']) / ($hcatTotaal['totaalbegin'] /100));
           $this->printTotaal('Totaal '.$lastHCategorie, $hcatTotaal['totaalbegin'],$hcatTotaal['totaalactueel'],
           $hcatTotaal['percentageVanTotaal'],$hcatTotaal['fondsResultaat'],$hcatTotaal['valutaResultaat'],$procentResultaat);
           unset($hcatTotaal);
         }
		*/
	  $rendementProcent  	= '';//performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,$rendementProcent,true);
    $this->pdf->Ln();
    
    
	//		   $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren);
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    printRendement($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,false,$this->pdf->rapportageValuta);
   // printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
   // $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
    
    VOLK_VHO_voet($this->pdf,$this->portefeuille);

	}
}
?>
