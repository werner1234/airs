<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHUIS_L128
{
  function RapportHUIS_L128($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "HUIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Portefeuilledetails";
  }
  
  function writeRapport()
  {
    global $__appvar;
    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    
    
    $portefeuilles=array();
    $query = "SELECT Fondsen.Portefeuille,
              Portefeuilles.Startdatum,
              Portefeuilles.Einddatum,
              Fondsen.Omschrijving,
              TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
              FondsenBuitenBeheerfee.layoutNr
              FROM TijdelijkeRapportage
JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds
JOIN Fondsen ON FondsenBuitenBeheerfee.Fonds = Fondsen.Fonds
JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
              WHERE FondsenBuitenBeheerfee.Huisfonds = 1 AND rapportageDatum ='".$this->rapportageDatum."' AND
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      if($data['Portefeuille']<>'')
        $portefeuilles[$data['Portefeuille']]=$data;
    }
    
    $kopBackup=$this->pdf->rapport_koptext;
    
    foreach($portefeuilles as $portefeuille=>$pdata)
    {
      $pdata['Omschrijving']=vertaalTekst($pdata['Omschrijving'],$this->pdf->rapport_taal);
      $rapportageDatum['a'] = date("Y-m-d",$this->pdf->rapport_datumvanaf);
      $rapportageDatum['b'] = date("Y-m-d",$this->pdf->rapport_datum);
      
      if($this->pdf->rapport_datumvanaf < db2jul($pdata['Startdatum']))
        $rapportageDatum['a'] = $pdata['Startdatum'];
      
      if($this->pdf->rapport_datum > db2jul($pdata['Einddatum']))
      {
        echo "<b>Fout: Portefeille '$portefeuille' heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
        exit;
      }
      if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
      {
        echo "<b>Fout: '$portefeuille' Van datum kan niet groter zijn dan  T/m datum! </b>";
        exit;
      }
      
      if(substr($rapportageDatum['a'],5,2)==01 && substr($rapportageDatum['a'],8,2)==01)
        $startjaar=true;
      else
        $startjaar=false;
      
      $fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['a'],$startjaar,$pdata['RapportageValuta'],$rapportageDatum['a']);
      $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],0,$pdata['RapportageValuta'],$rapportageDatum['a']);
      vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$rapportageDatum['a']);
      vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
      $portefeuilleWaarde=0;
      foreach($fondswaarden['b'] as $fonds)
        $portefeuilleWaarde+=$fonds['actuelePortefeuilleWaardeEuro'];
      
      $aandeelVanPortefeuille=$pdata['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
      if($aandeelVanPortefeuille <>0)
      {
        
        $rapportagePeriode = date("j",$this->pdf->rapport_datumvanaf)." ".
          vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
          date("Y",$this->pdf->rapport_datumvanaf).
          ' '.vertaalTekst('tot en met',$this->pdf->rapport_taal).' '.
          date("j",$this->pdf->rapport_datum)." ".
          vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
          date("Y",$this->pdf->rapport_datum);
        
        $rapport = new RapportHUIS_VOLK_L128($this->pdf, $portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
        $this->pdf->rapport_type='HUIS';
        $this->pdf->rapport_titel = vertaalTekst('Overzicht',$this->pdf->rapport_taal).' '.$pdata['Omschrijving'].'*';
        
        
        $this->pdf->rapport_koptext ='';// "\n " . $pdata['Omschrijving'] . "\n \n";
        $this->pdf->huisfondsOmschrijving=$pdata['Omschrijving'];
        $rapport->aandeel = $aandeelVanPortefeuille;
        $rapport->PERFblockTonen=false;
        $this->pdf->huisAandeel=$aandeelVanPortefeuille;
        $rapport->writeRapport();
        unset($this->pdf->huisAandeel);
        unset($this->pdf->huisfondsOmschrijving);
        unset($this->pdf->huis3);
      }
    }
    $this->pdf->rapport_koptext=$kopBackup;
    
  }
  
}



class RapportHUIS_VOLK_L128
{
  function RapportHUIS_VOLK_L128($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    global $__appvar;
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "HUIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    if($this->pdf->rapport_VOLK_titel)
      $this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
    else
      $this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";
    
    if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
      $this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";
    
    $rapportagePeriode = date("j",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datumvanaf).
      ' '.vertaalTekst('tot en met ',$this->pdf->rapport_taal).' '.
      date("j",$this->pdf->rapport_datum)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datum);
    
    $this->pdf->rapport_titel = vertaalTekst("Vergelijkend overzicht verslagperiode",$this->pdf->rapport_taal).' '.$rapportagePeriode.' '.vertaalTekst("inclusief geschiktheidsverklaring",$this->pdf->rapport_taal);
    
    
    if(!is_array($this->pdf->excelData))
      $this->pdf->excelData=array();
    
    $this->pdf->excelData[]=array("Categorie","Fondsomschrijving","Aantal","Koers","Waarde",
      "Waarde ".$this->pdf->rapportageValuta,"Valuta","Koers","Waarde","Waarde ".$this->pdf->rapportageValuta,"in %",
      "Fonds-\nresultaat","Valuta-\nresultaat","Direct\nresultaat","Rendement in %");
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    
    $this->aandeel=1;
    $this->PERFblockTonen=true;
    
    if(date('d-m',$this->pdf->rapport_datumvanaf)!='01-01')
    {
      
      if($this->rapportageDatumVanaf<>substr($this->pdf->PortefeuilleStartdatum,0,10))
        $this->toonYtd = true;
      $this->toonPeriode=true;
    }
    else
    {
      $this->toonYtd = true;
      $this->toonPeriode=false;
    }
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
    
  }
  
  function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
  {
    if(1 || $this->aandeel<> 1)
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
    //A $categorien[subtotaalbegin],
    //B $categorien[subtotaalactueel],
    //C $subtotaal[percentageVanTotaal],
    //D $subtotaal[fondsResultaat],
    //E $subtotaal[valutaResultaat],
    //F $procentResultaat);
    if( $this->aandeel<>1)
    {
      $totaalD=0;
      $totaalE=0;
      $totaalF=0;
      $totaalG=0;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->printCol(1,$title,"tekst");
//      $this->pdf->SetX($this->pdf->marge);
//      $this->pdf->Cell(100,4,$title, 0,0, "L");
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    if($totaalB <>0)
      $this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($totaalA <>0)
      $this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
    if($totaalC <>0)
      $this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
    if($totaalD <>0) //fondsResultaat
      $this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($this->toonYtd==true && $totaalE <>0) //valutaResultaat
      $this->printCol(14,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($totaalG <>0) //divident
      $this->printCol(12,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($this->toonPeriode==true && $totaalF <>0) //$procentResultaat
      $this->printCol(13,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
    
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
    
    
    if($grandtotaal == true)
      $grandtotaal = "grandtotaal";
    else
      $grandtotaal = "totaal";
    
    if(1|| $this->aandeel<>1)
    {
      $totaalA=0;
      $totaalD=0;
      $totaalE=0;
      $totaalF=0;
      $totaalG=0;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    //$this->printCol(3,$title,"tekst");
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->Cell(100,4,$title, 0,0, "L");
    $this->pdf->SetX($this->pdf->marge);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    if($totaalB <>0)
      $this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
    if($totaalA <>0)
      $this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
    if($totaalC <>0)
      $this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
    if($totaalD <>0)
      $this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
    if($this->toonYtd==true && $totaalE <>0)
      $this->printCol(14,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
    if($totaalG <>0) //divident
      $this->printCol(12,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
    if($this->toonPeriode==true && $totaalF <>0)
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
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
    
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->MultiCell(90,4, $title, 0, "L");
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
  }
  
  
  function getDividend($fonds,$vanaf,$tot)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
    
    $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro * ".$this->aandeel." as actuelePortefeuilleWaardeEuro,
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
    
    $totaal+=($rente[$tot]-$rente[$vanaf]);
    $totaalCorrected=$totaal;
    
    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$vanaf ."' AND
     Rekeningmutaties.Boekdatum <= '".	$tot ."' AND
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
      
      if($aantal[$boekdatum] > $aantal[$tot])
      {
        $aandeel=$aantal[$tot]/$aantal[$boekdatum];
      }
      // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }
  
  function getKleuren()
  {
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $this->allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->allekleuren['Rating']['Liquiditeiten']=$this->allekleuren['OIS']['Liquiditeiten'];
  }
  
  function getGrafiekData($portefeuille,$datum,$portefeuilleWaarde)
  {
    global $__appvar;
    
    $grafiekVerdelingen=array('OIS'=>'beleggingssector','OIR'=>'regio','OIV'=>'valuta');
    $geenVertaing=array('beleggingssector'=>'Geen sector');
    $grafiekData=array();
    $DB=new DB();
    foreach($grafiekVerdelingen as $kleurShort=> $verdeling)
    {
      if($verdeling=='rating')
      {
        $query = "SELECT 	if(TijdelijkeRapportage.type='rekening','Liquiditeiten',if(ISNULL(Fondsen.rating),'NR',REPLACE(REPLACE(Fondsen.rating,'+',''),'-',''))) AS verdeling,
            if(TijdelijkeRapportage.type='rekening','Liquiditeiten',if(ISNULL(Rating.omschrijving),'Geen rating',REPLACE(REPLACE(Rating.omschrijving,'+',''),'-',''))) AS Omschrijving,
             sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS waarde
          FROM TijdelijkeRapportage LEFT JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds LEFT JOIN Rating ON Fondsen.rating = Rating.rating
          WHERE TijdelijkeRapportage.portefeuille='" . $portefeuille . "' AND rapportageDatum='" . $datum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
          GROUP BY verdeling
          ORDER BY Rating.Afdrukvolgorde, verdeling";
      }
      elseif($verdeling=='beleggingssector' || $verdeling=='regio' )
      {
        $query = "SELECT  if(TijdelijkeRapportage.type='rekening','Liquiditeiten',$verdeling) AS verdeling,
             if(TijdelijkeRapportage.type='rekening','Liquiditeiten',".$verdeling."Omschrijving) AS Omschrijving, sum(actuelePortefeuilleWaardeEuro) as waarde
          FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille='$portefeuille' AND rapportageDatum='".$datum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
          GROUP BY verdeling ORDER BY ".$verdeling."volgorde, $verdeling"; //logscherm( $query);
      }
      else
        $query = "SELECT $verdeling as verdeling, ".$verdeling." as Omschrijving, sum(actuelePortefeuilleWaardeEuro) as waarde
          FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille='$portefeuille' AND rapportageDatum='".$datum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
          GROUP BY $verdeling ORDER BY ".$verdeling."volgorde, $verdeling";
      
      $DB->SQL($query);
      $DB->Query();
      while($data = $DB->NextRecord())
      {
        
        if($data['verdeling']=='')
        {
          if(isset($geenVertaing[$verdeling]))
            $data['verdeling']=$geenVertaing[$verdeling];
          else
            $data['verdeling'] = 'Geen ' . $verdeling;
          if ($data['Omschrijving'] == '')
          {
            $data['Omschrijving'] = $data['verdeling'];
          }
        }
        
        $kleur=$this->allekleuren[$kleurShort][$data['verdeling']];
        // echo $verdeling." $kleurShort ".$data['verdeling'];listarray($kleur);
        $grafiekData[$verdeling]['data'][$data['verdeling']]['waardeEur']+=$data['waarde'];
        $grafiekData[$verdeling]['data'][$data['verdeling']]['Omschrijving']=$data['Omschrijving'];
        $grafiekData[$verdeling]['pieData'][$data['Omschrijving']]+=$data['waarde']/$portefeuilleWaarde;
        $grafiekData[$verdeling]['kleurData'][$data['Omschrijving']]=$kleur;
        $grafiekData[$verdeling]['kleurData'][$data['Omschrijving']]['percentage']+=$data['waarde']/$portefeuilleWaarde*100;
        
      }
      
      
      
    }
    return $grafiekData;
  }
  
  function toonGrafieken($grafiekData)
  {
    
    $yPos=$this->pdf->getY();
    if($yPos < 130)
    {
      $yPos += 15;
    }
    else
    {
      $this->pdf->addPage();
      $yPos=50;
    }
    
    $xyPos=array(array(10,$yPos),array(110,$yPos),array(210,$yPos));
    $vertalingen=array('beleggingssector'=>'sector');
    $n=0;
    foreach($grafiekData as $grafiekSoort=>$grafiekData)
    {
      if(isset($vertalingen[$grafiekSoort]))
        $titel=vertaalTekst('Spreiding per '.$vertalingen[$grafiekSoort], $this->pdf->rapport_taal);
      else
        $titel=vertaalTekst('Spreiding per '.$grafiekSoort, $this->pdf->rapport_taal);
      
      $this->pdf->setXY($xyPos[$n][0],$xyPos[$n][1]);
      $this->pdf->wLegend = 0;
      $this->printPie($grafiekData['pieData'], $grafiekData['kleurData'], $titel, 35, 35);
      $this->pdf->wLegend = 0;
      $n++;
    }
  }
  
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
  {
    
    $col1=array(255,0,0); // rood
    $col2=array(0,255,0); // groen
    $col3=array(255,128,0); // oranje
    $col4=array(0,0,255); // blauw
    $col5=array(255,255,0); // geel
    $col6=array(255,0,255); // paars
    $col7=array(128,128,128); // grijs
    $col8=array(128,64,64); // bruin
    $col9=array(255,255,255); // wit
    $col0=array(0,0,0); //zwart
    $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();
    
    if(isset($kleurdata))
    {
      $grafiekKleuren = array();
      $a=0;
      while (list($key, $value) = each($kleurdata))
      {
        if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
      $grafiekKleuren = $standaardKleuren;
    
    while (list($key, $value) = each($pieData))
      if ($value < 0)
        $pieData[$key] = -1 * $value;
    
    //$this->pdf->SetXY(210, $this->pdf->headerStart);
    $y = $this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($startX,$y-4);
    // $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
    $this->pdf->setXY($startX,$y);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->setX($startX);
    $this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
    $hoogte = ($this->pdf->getY() - $y) + 8;
    $this->pdf->setY($y);
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->setX($startX);
    
    //	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);
    
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 4;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
    $radius=min($w,$h);
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < $this->pdf->NbVal; $i++) {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $aantal=count($data);
    foreach($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        $factor=1.5;
        
        if($i==($aantal-1))
          $angleEnd=360;
        
        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }
    
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w +$margin +4 ;
    $x2 = $x1 + $margin/2;
    $y1 = $YDiag - ($radius) ;
    
    for($i=0; $i<$this->pdf->NbVal; $i++)
    {
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1);
      $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
      $y1+=$hLegend + 2;
    }
    
  }
  
  function SetLegends($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      $l=vertaalTekst($l ,$this->pdf->rapport_taal);
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      //$p=sprintf('%.1f',$val).'%';
      $p=$this->formatGetal($val,1).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      //$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
  
  
  function writeRapport()
  {
    global $__appvar;
    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    $this->getKleuren();
    
    
    
    $this->pdf->widthB = array(5,54,18,15,20,21,1,16,21,21,15,18,18,20,19);
    $this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    
    // voor kopjes
    $this->pdf->widthA = array(   59,18,15,20,21,1,16,21,21,15,18,18,20,19);
    $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    
    
    if(1|| $this->aandeel<>1)
    {
      $this->pdf->widthB = array(5,84,18,15,10,21,1,16,21,21,15,18,8,10,19);
      $this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
      
      // voor kopjes
      $this->pdf->widthA = array(   89,18,15,10,21,1,16,21,21,15,18,8,10,19);
      $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
      
    }
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
      $tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
      $tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    else
      $tweedePerformanceStart = "$RapStartJaar-01-01";
    if(substr($tweedePerformanceStart,5,5)=='01-01')
      $startJaar=true;
    else
      $startJaar=false;
    $tmp=berekenPortefeuilleWaarde($this->portefeuille,$this->rapportageDatum,$startJaar,'EUR',$tweedePerformanceStart);
    $ytdFonds=array();
    $ytdBeleggingscategorie=array();
    
    foreach($tmp as $regel)
    {
      
      $dividend=$this->getDividend($regel['fonds'], $tweedePerformanceStart, $this->rapportageDatum);
      if($regel['type']=='fondsen')
      {
        $ytdFonds[$regel['fonds']] = ($regel['actuelePortefeuilleWaardeEuro'] - $regel['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / $regel['beginPortefeuilleWaardeEuro'];
        $ytdBeleggingscategorie[$regel['beleggingscategorieOmschrijving']]['eind']+=($regel['actuelePortefeuilleWaardeEuro'] - $regel['beginPortefeuilleWaardeEuro'] + $dividend['corrected']);
        $ytdBeleggingscategorie[$regel['beleggingscategorieOmschrijving']]['begin']+=($regel['beginPortefeuilleWaardeEuro']);
      }
      
    }
    
    foreach($ytdBeleggingscategorie as $categorie=>$waarden)
    {
      $ytdBeleggingscategorie[$categorie]['rendement']=$waarden['eind']/$waarden['begin'];
    }
    
    
    $this->pdf->AddPage();
    if(!isset($this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']))
    {
      $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = substr($this->pdf->rapport_titel,0,-1);
    }
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
    
    $grafiekData=$this->getGrafiekdata($this->portefeuille,$this->rapportageDatum,$totaalWaarde);
    
    
    
    $query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, ".
      " TijdelijkeRapportage.valuta, ".
      " TijdelijkeRapportage.beleggingscategorie, ".
      "
       IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar * ".$this->aandeel."),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel.") as subtotaalbegin,
      ".
      " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." subtotaalactueel FROM ".
      " TijdelijkeRapportage ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.beleggingscategorie <> 'Liquiditeiten' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
      " ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde asc";
    
    
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
      if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
      {
        $title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
        
        $procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
        if($totaalbegin < 0)
          $procentResultaat = -1 * $procentResultaat;
        
        $totaalresultaatYtd=$ytdBeleggingscategorie[$lastCategorie]['rendement']*100;
        
        $actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalresultaatYtd, $procentResultaat,false,$totaaldividend);
        
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
      
      if($lastCategorie <> $categorien['Omschrijving'])
        $this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");
      
      
      
      
      // subkop (valuta)
      if($categorien['valuta'] == $this->pdf->rapportageValuta)
        $beginQuery = 'beginwaardeValutaLopendeJaar';
      else
        $beginQuery = $this->pdf->ValutaKoersBegin;
      
      $this->printKop($categorien['valuta'], "b");
      $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
        " TijdelijkeRapportage.fonds, ".
        " TijdelijkeRapportage.actueleValuta, ".
        " TijdelijkeRapportage.totaalAantal * ".$this->aandeel." as totaalAantal, ".
        " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
        " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
        " TijdelijkeRapportage.Valuta, ".
        " TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery * ".$this->aandeel." as beginPortefeuilleWaardeEuro, ".
        //" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel." as beginPortefeuilleWaardeEuro, ".
        " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
        " FROM TijdelijkeRapportage WHERE ".
        " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
        " TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
        " TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
        " TijdelijkeRapportage.type =  'fondsen' AND TijdelijkeRapportage.beleggingscategorie <> 'Liquiditeiten' AND ".
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
        $dividend=$this->getDividend($subdata['fonds'],$this->rapportageDatumVanaf, $this->rapportageDatum);
        $dividend['totaal']=$dividend['totaal']*$this->aandeel;
        $dividend['corrected']=$dividend['corrected']*$this->aandeel;
        
        //$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
        
        $fondsResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] ;//- $fondsResultaat;
        $fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
        
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
        
        // print fondsomschrijving appart ivm met apparte fontkleur
        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
        $this->pdf->setX($this->pdf->marge);
        $this->pdf->Cell($this->pdf->widthB[0],4,"");
        $this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,null);
        $this->pdf->setX($this->pdf->marge);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        
        if($this->toonYtd==true)
          $ytdTxt=$this->formatGetal($ytdFonds[$subdata['fonds']]*100,2);
        else
          $ytdTxt='';
        
        if($this->toonPeriode==false)
          $procentResultaattxt='';
        
        if(1|| $this->aandeel<>1)
        {
          $this->pdf->row(array("",
                            "",
                            $this->formatAantal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                            '',
                            '',
                            '',
                            "",
                            $this->formatGetal($subdata['actueleFonds'], 2),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                            $percentageVanTotaaltxt));
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
                            $dividendtxt,
                            $procentResultaattxt,
                            $ytdTxt));
        }
        $this->pdf->excelData[]=array($categorien['Omschrijving'],
          $subdata['fondsOmschrijving'],
          round($subdata['totaalAantal'],2),
          round($subdata['beginwaardeLopendeJaar'],2),
          round($subdata['beginPortefeuilleWaardeInValuta'],2),
          round($subdata['beginPortefeuilleWaardeEuro'],2),
          $categorien['valuta'],
          round($subdata['actueleFonds'],2),
          round($subdata['actuelePortefeuilleWaardeInValuta'],2),
          round($subdata['actuelePortefeuilleWaardeEuro'],2),
          round($percentageVanTotaal,2),
          round($fondsResultaat,2),
          round($valutaResultaat,2),
          round($dividend['totaal'],2),
          round($procentResultaat,2));
        
        $valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
        $subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
        $subtotaal['fondsResultaat'] +=$fondsResultaat;
        $subtotaal['valutaResultaat'] +=$valutaResultaat;
        $subtotaal['totaalResultaat'] +=$subTotaalResultaat;
        $subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
        $subtotaal['totaalDividend'] += $dividend['totaal'];
        $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];
        
      }
      
      // print categorie footers
      $procentResultaat = (($categorien['subtotaalactueel']  - $categorien['subtotaalbegin'] + $subtotaal['totaalDividendCorrected'] ) / ($categorien['subtotaalbegin']  /100));
      if($categorien['subtotaalbegin'] < 0)
        $procentResultaat = -1 * $procentResultaat;
      
      
      //				$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien['subtotaalbegin'],$categorien['subtotaalactueel'],$subtotaal['percentageVanTotaal'], $subtotaal['fondsResultaat'], $subtotaal['valutaResultaat'], $procentResultaat,$subtotaal['totaalDividend']);
      
      
      
      
      // totaal op categorie tellen
      $totaalbegin   += $categorien['subtotaalbegin'];
      $totaalactueel += $categorien['subtotaalactueel'];
      
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
    
    $procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
    if($totaalbegin < 0)
      $procentResultaat = -1 * $procentResultaat;
    
    
    $totaalresultaatYtd=$ytdBeleggingscategorie[$lastCategorie]['rendement']*100;
    
    
    $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalresultaatYtd,$procentResultaat,$totaaldividend);
    
    // selecteer rente
    $query = "SELECT TijdelijkeRapportage.valuta, ".
      " TijdelijkeRapportage.beleggingscategorie, ".
      " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) * ".$this->aandeel." as subtotaalValuta, ".
      " SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel." as subtotaalbegin, ".
      " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as subtotaalactueel FROM ".
      " TijdelijkeRapportage ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.type = 'rente'  AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.valuta ".
      " ORDER BY TijdelijkeRapportage.valutaVolgorde ";
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
        $totaalRenteInValuta += $categorien['subtotaalactueel'];
      }
      // totaal op rente
      $subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
      $actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
      
      $this->pdf->excelData[]=array('Opgelopen rente','Opgelopen rente',"","","","","","",'',round($totaalRenteInValuta,2),round($subtotaalPercentageVanTotaal,2));
      
    }
    
    // Liquiditeiten
    
    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.actueleValuta , ".
      " TijdelijkeRapportage.rekening , ".
      " TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta , ".
      " TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
      " FROM TijdelijkeRapportage
            LEFT JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " (TijdelijkeRapportage.type = 'rekening' OR (TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.beleggingscategorie = 'Liquiditeiten' )) ".
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
      $this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");
      
      while($data = $DB1->NextRecord())
      {
        $liqiteitenBuffer[] = $data;
      }
      
      
      foreach($liqiteitenBuffer as $data)
      {
        if(1 || $this->aandeel<> 1)
        {
          $omschrijving='Cash positie';
        }
        else
        {
          $omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
          $omschrijving = vertaalTekst(str_replace("{Rekening}", $data[rekening], $omschrijving), $this->pdf->rapport_taal);
          $omschrijving = str_replace("{Tenaamstelling}", vertaalTekst($data['fondsOmschrijving'], $this->pdf->rapport_taal), $omschrijving);
          $omschrijving = vertaalTekst(str_replace("{Valuta}", $data[valuta], $omschrijving), $this->pdf->rapport_taal);
        }
        
        $totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];
        $subtotaalPercentageVanTotaal  = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
        $subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
        
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
        $this->pdf->excelData[]=array('Liquiditeiten',$omschrijving,"","","","","","",
          round($data['actuelePortefeuilleWaardeInValuta'],2),
          round($data['actuelePortefeuilleWaardeEuro'],2),
          round($subtotaalPercentageVanTotaal,2));
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
    $this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true,$grandtotaaldividend);
    
    
    $this->pdf->ln();
    
    
    $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
    
    if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
    {
      if(!$this->pdf->rapport_VOLK_geenIndex)
        $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
    }
    
    $this->toonGrafieken($grafiekData);
    /*
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
    $this->pdf->SetXY(8, 180);
    $this->pdf->MultiCell(297-16,4,vertaalTekst('* Koersresultaat beslaat uitsluitend het ongerealiseerd koersresultaat.',$this->pdf->rapport_taal),0,'L');

    if($this->pdf->getY() > 185)
      $this->pdf->addPage();
    $this->pdf->setY(185);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize-2);
    $this->pdf->MultiCell(297-16,4,vertaalTekst('Geschiktheidsverklaring',$this->pdf->rapport_taal));
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
    $txt=vertaalTekst("Hierdoor bevestigen wij dat uw beleggingsportefeuille in overeenstemming is met de vastlegging van uw beleggingsdoelstelling, beleggingshorizon, kennis- en ervaringsniveau, risicobereidheid en verliescapaciteit (uw cliëntprofiel).",$this->pdf->rapport_taal);
    $this->pdf->MultiCell(297-16,4,$txt);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
        */
  }
}