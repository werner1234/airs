<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L110
{
	function RapportVOLK_L110($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "VOLK";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Positie-overzicht";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
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
    $cellWidth=$this->pdf->widthB[$row];

    
    $writerow = $this->pdf->widthB[($tel)];
    $end = $start + $writerow;
  
    if($cellWidth>20)
      $startLine=$start+($cellWidth-20);
    else
      $startLine=$start;
    
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
      $this->pdf->Line($startLine+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
      $this->pdf->ln();
      if($type == "grandtotaal")
      {
        $this->pdf->Line($startLine+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
        $this->pdf->Line($startLine+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
      }
      else if($type == "totaal")
      {
        $this->pdf->setDash(1,1);
        $this->pdf->Line($startLine+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
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
    
    //title "Subtotaal:",$this->pdf->rapport_taal),
    //A $categorien['subtotaalbegin'],
    //B $categorien['subtotaalactueel'],
    //C $subtotaal['percentageVanTotaal'],
    //D $subtotaal['fondsResultaat'],
    //E $subtotaal['valutaResultaat'],
    //F $procentResultaat);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->printCol(3,$title,"tekst");
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
 //   if($totaalB <>0)
 //     $this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
  //  if($totaalA <>0)
  //    $this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
    if($totaalC <>0)
      $this->printCol(10,$this->formatGetal($totaalC,0)." %","subtotaal");
    if($totaalD <>0) //fondsResultaat
      $this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
   // if($totaalE <>0) //valutaResultaat
  //    $this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($totaalG <>0) //divident
      $this->printCol(13,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($totaalF <>0) //$procentResultaat
      $this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    
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
    
    if($grandtotaal)
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    else
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    if($grandtotaal == true)
      $grandtotaal = "grandtotaal";
    else
      $grandtotaal = "totaal";
    
    $this->printCol(3,$title,"tekst");
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // if($totaalB <>0)
   //   $this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
   // if($totaalA <>0)
   //   $this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
    if($totaalC <>0)
      $this->printCol(10,$this->formatGetal($totaalC,0)." %",$grandtotaal);
    if($totaalD <>0)
      $this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
  //  if($totaalE <>0)
  //    $this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
    if($totaalG <>0) //divident
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
    
    $this->pdf->SetFont($font,$fonttype,$fontsize+3);
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
    
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->ln(1);
    $this->pdf->MultiCell(90,4, $title, 0, "L");
    $this->pdf->ln(1);
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    
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
    $w=26;
                              #      x  x  x     x  x  x     x
    $this->pdf->widthB = array(5,75,$w,$w,$w,8,$w,$w,$w,8,$w);
    $this->pdf->widthA = array(  80,$w,$w,$w,8,$w,$w,$w,8,$w);
    $this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    
    // voor kopjes
    //$this->pdf->widthA = array(  60,18,88,10,21,1,17,21,1,28,28,1,18,18);
    //$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    
    
    $this->pdf->AddPage();
    $this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;
    
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
    
    $actueleWaardePortefeuille = 0;
    $grandtotaalfonds=0;
    $grandtotaaldividend=0;
    
    
    
    debugSpecial($query,__FILE__,__LINE__);
      $subquery = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving,TijdelijkeRapportage.Beleggingscategorie,
         TijdelijkeRapportage.fondsOmschrijving, ".
        " TijdelijkeRapportage.fonds, ".
        " TijdelijkeRapportage.actueleValuta, ".
        " TijdelijkeRapportage.totaalAantal, ".
        " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
        " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
        " TijdelijkeRapportage.Valuta, ".
        " TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
        " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
        " FROM TijdelijkeRapportage WHERE ".
        " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
        " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'].
        " ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
      
      // print detail (select from tijdelijkeRapportage)
      debugSpecial($subquery,__FILE__,__LINE__);
      $DB2 = new DB();
      $DB2->SQL($subquery);
      $DB2->Query();
//echo $subquery."<br><br>";exit;
      $n=0;
      $kopPrinted=false;
    $lastCategorie='';
    $bmResultaat=0;
      while($subdata = $DB2->NextRecord())
      {
        if($subdata['BeleggingscategorieOmschrijving'] <> $lastCategorie)
        {
          $this->printKop($subdata['BeleggingscategorieOmschrijving']);
          $lastCategorie=$subdata['BeleggingscategorieOmschrijving'];
          $query="SELECT Fonds FROM IndexPerBeleggingscategorie WHERE Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."' AND Beleggingscategorie='".$subdata['Beleggingscategorie']."'";
          $DB->SQL($query);
          $DB->Query();
          $bm = $DB->nextRecord();
          $bmResultaat=getFondsPerformance($bm['Fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum);
          
        }
        if($this->pdf->GetY()>160)//
        {
          $this->pdf->AddPage();
        }
        if($kopPrinted==false)
        {
          $kopPrinted=true;
        }
        $dividend=$this->getDividend($subdata['fonds']);
        
        $fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
        
        $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
        if($subdata['beginPortefeuilleWaardeEuro'] < 0)
          $procentResultaat = -1 * $procentResultaat;
        
        $percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro'] / $totaalWaarde) * 100;
        
        
        $percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";
        
        if($procentResultaat > 1000 || $procentResultaat < -1000)
          $procentResultaattxt = "p.m.";
        else
          $procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc).'%';
        
        
        $fondsResultaattxt = "";
        $dividendtxt='';
        
        if($fondsResultaat <> 0)
          $fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);
        
        if($dividend['totaal'] <> 0)
          $dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);
        
        $this->pdf->SetWidths($this->pdf->widthB);
        $this->pdf->SetAligns($this->pdf->alignB);
        
        // print fondsomschrijving appart ivm met apparte fontkleur
        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
        $this->pdf->setX($this->pdf->marge);
        $this->pdf->Cell($this->pdf->widthB[0],4,"");
        $this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,null);
        $this->pdf->setX($this->pdf->marge);
      //  $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        
        $this->pdf->row(array("",'',
                          $this->formatAantal($subdata['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
                          $this->formatGetal($subdata['actueleFonds'],2),
                          $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                          '',
                          $fondsResultaattxt,
                          $dividendtxt,
                          $procentResultaattxt,
                          '',
                          $this->formatGetal($bmResultaat,2).'%'));

        $n++;
        $actueleWaardePortefeuille+=$subdata['actuelePortefeuilleWaardeEuro'];
        $grandtotaalfonds+=$fondsResultaat;
        $grandtotaaldividend+=$dividend['totaal'];
      }
      
      
    // check op totaalwaarde!
    if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
    {
      echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."  Verschil (".round($actueleWaardePortefeuille-$totaalWaarde,2).") ');
			</script>";
      ob_flush();
      
    }
    $this->pdf->ln(4);
  
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->Cell($this->pdf->widthB[0],4,"");
    $this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal),null,null,null,null,null);
    $this->pdf->setX($this->pdf->marge);
  
  
    
    
    
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaardeVanaf = $DB->nextRecord();
    $totaalKosten       = 0;
    $waardeEind				  = $totaalWaarde['totaal'];
    $waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
    $waardeMutatie 	   	= $waardeEind - $waardeBegin;
    $stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
    $onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    $rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    $brutoResultaat=$resultaatVerslagperiode+$totaalKosten;
    $gemiddeldeVermogen=$resultaatVerslagperiode/($rendementProcent/100);
    $brutoRendement=$brutoResultaat/$gemiddeldeVermogen*100;
    
    $benchmark=getFondsPerformance($this->pdf->portefeuilledata['SpecifiekeIndex'],$this->rapportageDatumVanaf, $this->rapportageDatum);

    $this->pdf->row(array("",'',
                      '','',
                      $this->formatGetal($actueleWaardePortefeuille,2),'',
                      $this->formatGetal($grandtotaalfonds,2),
                      ($grandtotaaldividend<>0?$this->formatGetal($grandtotaaldividend,2):''),
                      $this->formatGetal($brutoRendement,2).'%',
                      '',
                      $this->formatGetal($benchmark,2).'%'));
  
    $positieLijn=$this->pdf->marge;
    $resultaatLijn=$this->pdf->marge;
    foreach($this->pdf->widthB as $i=>$width)
    {
      if($i<5)
      {
        $positieLijn += $width;
      }
      elseif($i==5)
      {
        $positieLijn += $width / 2;
        $resultaatLijn=$positieLijn+$width;
      }
      elseif($i<9)
      {
        $resultaatLijn += $width;
      }
      elseif($i==9)
      {
        $resultaatLijn += $width / 2;
      }
    }
    $this->pdf->SetLineStyle(array('dash' =>"2,2",'color'=>array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b'])));
    $this->pdf->Line($positieLijn,30,$positieLijn,$this->pdf->getY());
    $this->pdf->Line($resultaatLijn,30,$resultaatLijn,$this->pdf->getY());
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
  
    $this->pdf->setWidths(array($this->pdf->w-$this->pdf->marge*2));
    $this->pdf->setAligns(array('L'));
    $this->pdf->ln(8);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
    $this->pdf->row(array("Uitleg benchmark vergelijking"));
    $this->pdf->ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->setTextColor(0);
    $this->pdf->row(array("Ter vergelijking van jouw rendement met een benchmark gebruiken wij twee wereldwijd gespreide indices, de MSCI World Index en de FTSE World Government Bond Index. De categorieen ‘Duurzame aandelen’ en Vastgoed worden vergeleken met de MSCI World Index en de categorieen ‘Bedrijfsleningen’ en ‘Overheidsleningen’ worden vergeleken met de FTSE World Government Bond Index."));
    
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
  }
}
?>