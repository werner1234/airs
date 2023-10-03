<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportCASH.php");

class RapportVOLK_L76
{
	function RapportVOLK_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		$this->pdf->rapport_titel =	"Overzicht portefeuille";
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;

		$this->portefeuille = $portefeuille;
		$startjaar=substr($rapportageDatum,0,4).'-01-01';
		if(db2jul($pdf->portefeuilledata['Startdatum']) > db2jul($startjaar))
      $this->rapportageDatumVanafAangepast = substr($pdf->portefeuilledata['Startdatum'],0,10);
		else
      $this->rapportageDatumVanafAangepast = $startjaar;
    
		if($this->rapportageDatumVanaf <> $this->rapportageDatumVanafAangepast)
		{
			$this->vulTijdelijkeRaportage($this->rapportageDatumVanafAangepast,$this->rapportageDatum);
		}
    $this->db = new DB();
	}
	
	
	function vulTijdelijkeRaportage($vanafDatum,$rapportagedatum)
	{
		
     $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,$rapportagedatum,(substr($rapportagedatum, 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$vanafDatum);
     vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,$rapportagedatum);
	}

	function formatGetal($waarde, $dec,$percentage=false)
	{
	  if($waarde==0)
      return '';
		if($percentage==true)
			$extraTeken='%';
		else
			$extraTeken='';
		return number_format($waarde,$dec,",",".").$extraTeken;
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
			$start += $this->pdf->widthA[$tel];
		}

		$writerow = $this->pdf->widthA[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		if ($type == 'tekst')
		{
	    $y = $this->pdf->getY();
      $this->pdf->setY($y);
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



	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		$hoogte = 20;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";


			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(0,$title,"tekst");
      if($totaalB <>0)
				$this->printCol(7,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(4,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(8,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(10,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(11,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(13,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc).'%',$grandtotaal);
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
				$fontsize = $this->pdf->rapport_fontsize+1;
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
	//	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}


	function getDividend($fonds,$ytd=false)
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

		if($ytd==true)
	  	$totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanafAangepast]);
		else
			$totaal+=$rente[$this->rapportageDatum];
		$totaalCorrected=$totaal;

		if($ytd==true)
			$periodeFilter="Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanafAangepast."' AND";
		else
			$periodeFilter='';


		$query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND $periodeFilter
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query);
		$DB->Query();
		//echo "$query <br>\n";  //
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


			$fondsresultwidth = 5;
			$omschrijvingExtra = 9;

		// voor kopjes
			$this->pdf->widthA = array(70,25,15,19,0,0,19,22,16,18,23,18,18,18);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
      $this->pdf->SetFillColor(230,230,230);
      $fill=true;

		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas'] = $this->pdf->rapport_titel;

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
Fondsen.variabeleCoupon,
Fondsen.OptieBovenliggendFonds,
if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.fondsOmschrijving ,optie.Omschrijving) as onderliggendFonds, 
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
				   TijdelijkeRapportage.portefeuille,
           (TijdelijkeRapportage.historischeWaarde ) AS historischeKoers,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers) AS historischeWaardeEuro
      FROM ".
			" TijdelijkeRapportage 
Left Join Fondsen ON TijdelijkeRapportage.Fonds = Fondsen.Fonds 
Left Join Fondsen as optie ON Fondsen.OptieBovenliggendFonds = optie.Fonds   
      ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND 
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND 
      TijdelijkeRapportage.type <> 'rente'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, 
      TijdelijkeRapportage.beleggingscategorieVolgorde asc, onderliggendFonds,
      TijdelijkeRapportage.fondsOmschrijving asc, 
      TijdelijkeRapportage.type asc";



		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query); 
		$DB->Query();
 //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);

		while($fonds = $DB->NextRecord())
		{
		  
      $q="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE type = 'rente' AND 
      fonds='".$fonds['fonds']."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"  
			.$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB2->SQL($q);
      $rente=$DB2->lookupRecord();
      $fonds['rente']=$rente['actuelePortefeuilleWaardeEuro'];


		  if( $fonds['hoofdcategorieOmschrijving'] == '')
		    $fonds['hoofdcategorieOmschrijving'] ='Geen hoofdcategorie';
		  if($fonds['Omschrijving']=='')
		    $fonds['Omschrijving']='Geen categorie';
		  if($fonds['beleggingssectorOmschrijving']=='')
		    $fonds['beleggingssectorOmschrijving']='Geen sector';

      if($fonds['beleggingscategorie'] <> 'AAND')
        $fonds['beleggingssectorOmschrijving']='';
        
 
    $ytm='';        
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $fonds['Omschrijving'] && !empty($lastCategorie) )
			{
			 
        if($totaalactueelRente <> 0)
        {
          if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,0,0,1,1,1,1,1,1,1,1);
            $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		        $fill=true;
		      }
          $aandeel=$totaalactueelRente/$totaalWaarde*100;
	        $this->pdf->row(array("Opgelopen rente",'','','','',"",'',$this->formatGetal($totaalactueelRente,$this->pdf->rapport_VOLK_decimaal),
          $this->formatGetal($aandeel,1).'%','','','','',''));
        }
        $totaalactueel+=$totaalactueelRente;
        $totaalpercentage+=$aandeel;
        $totaalbegin=$fonds['historischeWaardeEuro'];
       
				$title = '';
        $procentResultaat=$totaalBijdrage/$totaalpercentage*100;

        $actueleWaardePortefeuille += $this->printTotaal($title, '', $totaalactueel, $totaalpercentage , $totaalResultaat, $totaalDividend, ($totaalResultaat+$totaalDividendCorrected)/($totaalactueel-($totaalResultaat+$totaalDividendCorrected))*100);

				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalactueelRente =0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;

				$totaalDividend=0;
				$totaalDividendCorrected =0;
			}

			if($lastHCategorie <> $fonds['hoofdcategorieOmschrijving'])
			{// echo $this->pdf->GetY()." ".$fonds['hoofdcategorieOmschrijving']."<br>\n";
			  if($this->pdf->GetY() > 156)
          $this->pdf->AddPage();
				$this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
			}

			if($lastCategorie <> $fonds['Omschrijving'])
			{
					$this->printKop(vertaalTekst($fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
			}
			if($lastSector <> $fonds['beleggingssectorOmschrijving'] && $fonds['beleggingssectorOmschrijving'] <> '')
			{
					$this->printKop(vertaalTekst($fonds['beleggingssectorOmschrijving'],$this->pdf->rapport_taal), "b");
			}





			$dividend=$this->getDividend($fonds['fonds']);
			if($dividend['totaal'] <> 0)
				$dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);
			else
				$dividendtxt='';




      $resultaat=$fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeEuro'];
  		$procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeEuro']  + $dividend['corrected'] ) / ($fonds['historischeWaardeEuro'] /100));

			$dividendYTD=$this->getDividend($fonds['fonds'],true);
			$procentResultaatYTD = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro'] + $dividendYTD['corrected']) / ($fonds['beginPortefeuilleWaardeEuro'] /100));
			if($fonds['beginPortefeuilleWaardeEuro'] < 0)
				$procentResultaatYTD = -1 * $procentResultaatYTD;


			if($fonds['historischeWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

			$percentageVanTotaal = ($fonds['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
      {
				$procentResultaattxt = "p.m.";
        $procentResultaat=0;
      }
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc,true);
         
         $bijdrage=$procentResultaat*$percentageVanTotaal/100;
          
if($fonds['type']=='rekening')
{
  $resultaat=0;
  $fondsResultaat=0;
  $fondsResultaatprocent=0;
  $valutaResultaat=0;
  $procentResultaat=0;
  $procentResultaattxt='';
  $fonds['totaalAantal']=0;
  $fonds['actueleFonds']=0;
  $fonds['historischeKoers']=0;
}




				$resultaattxt = "";
        $resultaattxt=$this->formatGetal($resultaat); 
        
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

					 if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,0,0,1,1,1,1,1,1,1,1);
            //listarray($this->pdf->widths);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }
          
				$this->pdf->row(array(
													$fonds['fondsOmschrijving'],
													$this->formatAantal($fonds['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$fonds['valuta'],
                          $this->formatGetal($fonds['historischeKoers'],2),
													'',
													"",
													$this->formatGetal($fonds['actueleFonds'],2),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$percentageVanTotaaltxt,
                          $this->formatGetal($fonds['rente']),
													$resultaattxt,
													$dividendtxt,
													$this->formatGetal($procentResultaatYTD,1,true),
													$procentResultaattxt));



				$valutaWaarden[$categorien['valuta']] = $fonds['actueleValuta'];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['totaalResultaat'] +=$resultaat;
				$subtotaal['totaalBijdrage'] += $bijdrage;
        $subtotaal['rente'] += $fonds['rente'];
			  $subtotaal['totaalDividend'] += $dividend['totaal'];
			  $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];

        $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$hcatTotaal['totaalactueel'] += $fonds['actuelePortefeuilleWaardeEuro'];
		  	$hcatTotaal['totaalDividend'] += $dividend['totaal'];
		  	$hcatTotaal['totaalDividendCorrected'] += $dividend['corrected'];

	  	$totaalactueel += $fonds['actuelePortefeuilleWaardeEuro'];
      $totaalactueelRente+=$fonds['rente'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];
			$totaalDividend += $dividend['totaal'];
			$totaalDividendCorrected += $dividend['corrected'];
			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastSector = $fonds['beleggingssectorOmschrijving'];


			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $bijdrage;

			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;
			$grandtotaalDividend += $dividend['totaal'];
			$grandtotaalDividendCorrected += $dividend['corrected'];
			
      
$ongerealiseerdResultaat += $subtotaal['totaalResultaat'] ;
$inProcent += $subtotaal['totaalBijdrage'] ;   

$subtotaal = array();  
		}
    
//    listarray($totaalBijdrage);


      //vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal)
		$actueleWaardePortefeuille += $this->printTotaal('', $totaalbegin, $totaalactueel,$totaalpercentage,$totaalResultaat,$totaalDividend ,$totaalResultaat+$totaalDividendCorrected/($totaalactueel-$totaalResultaat+$totaalDividendCorrected)*100);
		
    $aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100;
    //echo "$aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100; <br>\n ".($actueleWaardePortefeuille-$totaalWaarde);exit;
    $this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,$aandeelOpTotaal,$ongerealiseerdResultaat,$grandtotaalDividend,($ongerealiseerdResultaat+$grandtotaalDividendCorrected)/($actueleWaardePortefeuille-$ongerealiseerdResultaat+$grandtotaalDividendCorrected)*100,true);
    $this->pdf->Ln();
	//		   $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanafAangepast,$omkeren);
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // printRendement($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanafAangepast,false,$this->pdf->rapportageValuta);
   // printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanafAangepast, $this->rapportageDatum);
    $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);

$this->pdf->SetFillColor(0);
 unset($this->pdf->fillCell);
    
    if($this->rapportageDatumVanaf <> $this->rapportageDatumVanafAangepast)
    {
      $this->vulTijdelijkeRaportage($this->rapportageDatumVanafAangepast,$this->rapportageDatum);
    }
	}
}
?>
