<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/06 15:48:23 $
File Versie					: $Revision: 1.8 $

$Log: RapportVHO_L77.php,v $
Revision 1.8  2020/06/06 15:48:23  rvv
*** empty log message ***

Revision 1.7  2019/07/05 16:42:29  rvv
*** empty log message ***

Revision 1.6  2019/06/19 15:59:09  rvv
*** empty log message ***

Revision 1.5  2019/06/08 16:06:01  rvv
*** empty log message ***

Revision 1.4  2019/06/02 10:03:42  rvv
*** empty log message ***

Revision 1.3  2019/04/07 11:06:41  rvv
*** empty log message ***

Revision 1.2  2019/04/06 17:11:28  rvv
*** empty log message ***

Revision 1.1  2019/04/03 15:52:48  rvv
*** empty log message ***

Revision 1.3  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.2  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.1  2018/09/29 16:19:30  rvv
*** empty log message ***

Revision 1.2  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportVHO_L77
{
	function RapportVHO_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->verdeling='hoofdcategorie';
    $this->pdf->rapport_titel = "Directe opbrengst & bronheffing";
    $this->pdf->underlinePercentage=0.8;
    $this->pdf->rapport_VOLK_decimaal=2;
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
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->printCol(3,$title,"tekst");
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    if($totaalB <>0)
      $this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($totaalA <>0)
      $this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
    if($totaalC <>0)
      $this->printCol(10,$this->formatGetal($totaalC,1)." %","subtotaal");
    if($totaalD <>0) //fondsResultaat
      $this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
    if($totaalE <>0) //valutaResultaat
      $this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
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
    if($this->pdf->modelRapport==true)
    {
      if ($totaalB <> 0)
      {
        $this->printCol(9, $this->formatGetal($totaalB, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
      }
      
      if ($totaalC <> 0)
      {
        $this->printCol(10, $this->formatGetal($totaalC, 1) . " %", $grandtotaal);
      }
      
      
    }
    else
    {
      if ($totaalB <> 0)
      {
        $this->printCol(9, $this->formatGetal($totaalB, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
      }
      if ($totaalA <> 0)
      {
        $this->printCol(5, $this->formatGetal($totaalA, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
      }
      if ($totaalC <> 0)
      {
        $this->printCol(10, $this->formatGetal($totaalC, 1) . " %", $grandtotaal);
      }
      if ($totaalD <> 0)
      {
        $this->printCol(11, $this->formatGetal($totaalD, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
      }
      if ($totaalE <> 0)
      {
        $this->printCol(12, $this->formatGetal($totaalE, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
      }
      if ($totaalG <> 0) //divident
      {
        $this->printCol(13, $this->formatGetal($totaalG, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
      }
      if ($totaalF <> 0)
      {
        $this->printCol(14, $this->formatGetal($totaalF, 1), $grandtotaal);
      }
    }
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
  
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
  
  
    $this->pdf->alignB = array('R', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
    $fillArray = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
    $this->pdf->alignA = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
    $this->pdf->widthB = array(12, 87, 10, 17, 17, 21, 10, 20, 20, 15, 30, 20);
    $this->pdf->widthA = array(89);
  
  
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
    $this->pdf->SetDrawColor(0, 0, 0);
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / " . $this->pdf->ValutaKoersEind . " AS totaal " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatum . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' "
      . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
  
    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.totaalAantal, " .
      " TijdelijkeRapportage.fonds, TijdelijkeRapportage.rapportageDatum," .
      " TijdelijkeRapportage.Valuta, " .
      " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / " . $this->pdf->ValutaKoersEind . " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.type,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.rekening,
				  TijdelijkeRapportage.Beleggingscategorie, TijdelijkeRapportage.BeleggingscategorieOmschrijving, TijdelijkeRapportage.BeleggingscategorieVolgorde,
				  TijdelijkeRapportage.Regio, TijdelijkeRapportage.RegioOmschrijving, TijdelijkeRapportage.RegioVolgorde,
				  TijdelijkeRapportage.Valuta, TijdelijkeRapportage.ValutaOmschrijving, TijdelijkeRapportage.ValutaVolgorde" .
      " FROM TijdelijkeRapportage WHERE " .
      " TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND " .
      " TijdelijkeRapportage.rapportageDatum IN('" . $this->rapportageDatumVanaf . "','" . $this->rapportageDatum . "') "
      . $__appvar['TijdelijkeRapportageMaakUniek'] .
      " ORDER BY TijdelijkeRapportage.rapportageDatum desc,TijdelijkeRapportage.fondsOmschrijving asc";//exit;
    $DB->SQL($query);
    $DB->Query();
    $portefeuilleWaarden = array();
    $sortering = array();
  
    $totalenValuta = array();
    $totalenRegio = array();
		$totalenCategorie= array();
    $totaalAlles = array();
    while ($data = $DB->nextRecord())
    {
      $portefeuilleWaarden[$data['rapportageDatum']][$data['type']][$data['fonds']] = $data;
      if ($data['type'] == 'rekening')
      {
        if(!isset($sortering[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']][$data['ValutaVolgorde']][$data['rekening']]))
          $sortering[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']][$data['ValutaVolgorde']][$data['rekening']] = $data;
  
        
				
        if ($data['rapportageDatum'] == $this->rapportageDatumVanaf)
        {
          $totalenValuta[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']][$data['ValutaVolgorde']]['beginPortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
          $totalenRegio[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']]['beginPortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
          $totalenCategorie[$data['BeleggingscategorieVolgorde']]['beginPortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
          
          $totaalAlles['beginPortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
          $sortering[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']][$data['ValutaVolgorde']][$data['rekening']]['beginPortefeuilleWaardeEuro'] = $data['actuelePortefeuilleWaardeEuro'];

        }
        if ($data['rapportageDatum'] == $this->rapportageDatum)
        {
          $sortering[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']][$data['ValutaVolgorde']][$data['rekening']]['actuelePortefeuilleWaardeEuro'] = $data['actuelePortefeuilleWaardeEuro'];
          $totalenValuta[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']][$data['ValutaVolgorde']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
          $totalenRegio[$data['BeleggingscategorieVolgorde']][$data['RegioVolgorde']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
          $totalenCategorie[$data['BeleggingscategorieVolgorde']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
          $totaalAlles['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
        }
      
      }
    }

  
    $query = "SELECT
Rekeningmutaties.Fonds,
Fondsen.Omschrijving as fondsOmschrijving,
Fondsen.Valuta,
  (SELECT  Regio FROM  BeleggingssectorPerFonds WHERE BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '" . $this->rapportageDatum . "' ORDER BY Vanaf DESC LIMIT 1) as Regio,
  (SELECT  Beleggingscategorie FROM  BeleggingscategoriePerFonds WHERE BeleggingscategoriePerFonds.Fonds = Rekeningmutaties.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '" . $this->rapportageDatum . "'  ORDER BY Vanaf  DESC LIMIT 1) as Beleggingscategorie
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
JOIN Fondsen ON Rekeningmutaties.Fonds=Fondsen.Fonds
WHERE Rekeningen.Portefeuille='" . $this->portefeuille . "' AND
Rekeningmutaties.Boekdatum>='" . $this->rapportageDatumVanaf . "' AND
Rekeningmutaties.Boekdatum<='" . $this->rapportageDatum . "' AND
Rekeningmutaties.Grootboekrekening='FONDS'
GROUP BY Rekeningmutaties.Fonds
ORDER BY fondsOmschrijving";
    $DB->SQL($query);
    $DB->Query();
    $fondsen = array();
    while ($data = $DB->nextRecord())
    {
      $fondsen[$data['Fonds']] = $data;
    }


    $query="SELECT
KeuzePerVermogensbeheerder.waarde as Beleggingscategorie,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Beleggingscategorien.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder = '" . $this->portefeuilledata['Vermogensbeheerder'] . "'  AND KeuzePerVermogensbeheerder.categorie = 'Beleggingscategorien'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde
";
    $DB->SQL($query);
    $DB->Query();
    $hoofdcategoriePerCategorie = array();
    $hoofdcategorieOmschrijving = array();
    $omschrijvingPerHoofdcategorieVolgorde=array();
    $omschrijvingPerRegioVolgorde=array();
    $omschrijvingPerValutaVolgorde=array();
    while ($data = $DB->NextRecord())
    {
      $BeleggingscategorieOmschrijving[$data['Beleggingscategorie']] = $data['Omschrijving'];
      $BeleggingscategoriePerCategorie[$data['Beleggingscategorie']] = $data['Beleggingscategorie'];
  
      $afdrukvolgorde['Beleggingscategorien'][$data['Beleggingscategorie']] = $data['Afdrukvolgorde'];
      $omschrijving['Beleggingscategorien'][$data['Beleggingscategorie']] = $data['Omschrijving'];
  
    }
  

    $afdrukvolgorde['Beleggingscategorien'][''] = 127;
  
    $query = "SELECT Regio,Afdrukvolgorde,Omschrijving FROM Regios";
    $DB->SQL($query);
    $DB->Query();
    while ($record = $DB->NextRecord())
    {
      $afdrukvolgorde['Regios'][$record['Regio']] = $record['Afdrukvolgorde'];
      $omschrijving['Regios'][$record['Regio']] = $record['Omschrijving'];
      
    }
  
    $query = "SELECT Valuta,Afdrukvolgorde,Omschrijving FROM Valutas";
    $DB->SQL($query);
    $DB->Query();
    while ($record = $DB->NextRecord())
    {
      $afdrukvolgorde['Valutas'][$record['Valuta']] = $record['Afdrukvolgorde'];
      $omschrijving['Valutas'][$record['Valuta']] = $record['Omschrijving'];
    
    }
  
    $query = "SELECT categorie,waarde,Afdrukvolgorde FROM KeuzePerVermogensbeheerder WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='" . $this->portefeuilledata['Vermogensbeheerder'] . "' AND Afdrukvolgorde > 0";
    $DB->SQL($query);
    $DB->Query();
    while ($data = $DB->NextRecord())
    {
      $afdrukvolgorde[$data['categorie']][$data['waarde']] = $data['Afdrukvolgorde'];
    }
    
  
    foreach ($fondsen as $fonds => $fondsDetails)
    {
      $query = "SELECT
Rekeningen.valuta,
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
sum((Rekeningmutaties.Credit-Rekeningmutaties.Debet)) as bedrag,
sum((Rekeningmutaties.Credit-Rekeningmutaties.Debet)*Rekeningmutaties.ValutaKoers) as bedragEUR,
Rekeningmutaties.Grootboekrekening
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='" . $this->portefeuille . "' AND
Rekeningmutaties.Boekdatum>='" . $this->rapportageDatumVanaf . "' AND
Rekeningmutaties.Boekdatum<='" . $this->rapportageDatum . "' AND
Rekeningmutaties.Fonds='" . mysql_real_escape_string($fonds) . "'
GROUP BY Rekeningmutaties.Fonds,Rekeningmutaties.Grootboekrekening";
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->NextRecord())
      {
        if ($data['Grootboekrekening'] == 'DIV' || $data['Grootboekrekening'] == 'RENOB')
        {
          $fondsen[$fonds]['directeOpbrengst'] += $data['bedrag'];
          $fondsen[$fonds]['directeOpbrengstEUR'] += $data['bedragEUR'];
        }
        if ($data['Grootboekrekening'] == 'DIVBE' || $data['Grootboekrekening'] == 'BTLBR')
        {
          $fondsen[$fonds]['bronheffing'] += $data['bedrag'];
          $fondsen[$fonds]['bronheffingEUR'] += $data['bedragEUR'];
        }
      }
      // listarray($portefeuilleWaarden[$this->rapportageDatum]['rente'][$fonds]);
  
      
			$fondsen[$fonds]['actueleFonds'] = $portefeuilleWaarden[$this->rapportageDatum]['fondsen'][$fonds]['actueleFonds'];
      $fondsen[$fonds]['actueleFondsBegin'] = $portefeuilleWaarden[$this->rapportageDatumVanaf]['fondsen'][$fonds]['actueleFonds'];
      
      $fondsen[$fonds]['beginPortefeuilleWaardeEuro'] = $portefeuilleWaarden[$this->rapportageDatumVanaf]['fondsen'][$fonds]['actuelePortefeuilleWaardeEuro'];
      $fondsen[$fonds]['actuelePortefeuilleWaardeEuro'] = $portefeuilleWaarden[$this->rapportageDatum]['fondsen'][$fonds]['actuelePortefeuilleWaardeEuro'];
      $fondsen[$fonds]['totaalAantal'] = $portefeuilleWaarden[$this->rapportageDatum]['fondsen'][$fonds]['totaalAantal'];
      $fondsen[$fonds]['beginTotaalAantal'] = $portefeuilleWaarden[$this->rapportageDatumVanaf]['fondsen'][$fonds]['totaalAantal'];
      $fondsen[$fonds]['opgelopenrente'] = $portefeuilleWaarden[$this->rapportageDatum]['rente'][$fonds]['actuelePortefeuilleWaardeEuro'];
    
      $fondsen[$fonds]['RegioOmschrijving'] = $omschrijving['Regios'][$fondsen[$fonds]['Regio']];
      $fondsen[$fonds]['RegioVolgorde'] = $afdrukvolgorde['Regios'][$fondsen[$fonds]['Regio']];
  
      $fondsen[$fonds]['ValutaOmschrijving'] = $omschrijving['Valutas'][$fondsen[$fonds]['Valuta']];
      $fondsen[$fonds]['ValutaVolgorde'] = $afdrukvolgorde['Valutas'][$fondsen[$fonds]['Valuta']];
    
      $fondsen[$fonds]['BeleggingscategorieOmschrijving'] = $omschrijving['Beleggingscategorien'][$fondsen[$fonds]['Beleggingscategorie']];
      $fondsen[$fonds]['BeleggingscategorieVolgorde'] = $afdrukvolgorde['Beleggingscategorien'][$fondsen[$fonds]['Beleggingscategorie']];
    
      $fondsen[$fonds]['Beleggingscategorie'] = $BeleggingscategoriePerCategorie[$fondsDetails['Beleggingscategorie']];
     // $fondsen[$fonds]['BeleggingscategorieOmschrijving'] = $BeleggingscategorieOmschrijving[$fondsen[$fonds]['Beleggingscategorie']];
   //   $fondsen[$fonds]['BeleggingscategorieVolgorde'] = $afdrukvolgorde['Beleggingscategorien'][$fondsen[$fonds]['Beleggingscategorie']];
      $fondsen[$fonds]['type']='fondsen';
    
      $sortering[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']][$fonds] = $fondsen[$fonds];
  
      $totalenValuta[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']]['beginPortefeuilleWaardeEuro'] += $fondsen[$fonds]['beginPortefeuilleWaardeEuro'];
      $totalenValuta[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']]['actuelePortefeuilleWaardeEuro'] += $fondsen[$fonds]['actuelePortefeuilleWaardeEuro'];
      $totalenValuta[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']]['opgelopenrente'] += $fondsen[$fonds]['opgelopenrente'];
      $totalenValuta[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']]['directeOpbrengst'] += $fondsen[$fonds]['directeOpbrengst'];
      $totalenValuta[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']]['bronheffing'] += $fondsen[$fonds]['bronheffing'];
      $totalenValuta[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']]['directeOpbrengstEUR'] += $fondsen[$fonds]['directeOpbrengstEUR'];
      $totalenValuta[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']][$fondsen[$fonds]['ValutaVolgorde']]['bronheffingEUR'] += $fondsen[$fonds]['bronheffingEUR'];
    
      $totalenRegio[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']]['beginPortefeuilleWaardeEuro'] += $fondsen[$fonds]['beginPortefeuilleWaardeEuro'];
      $totalenRegio[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']]['actuelePortefeuilleWaardeEuro'] += $fondsen[$fonds]['actuelePortefeuilleWaardeEuro'];
      $totalenRegio[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']]['opgelopenrente'] += $fondsen[$fonds]['opgelopenrente'];
      $totalenRegio[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']]['directeOpbrengstEUR'] += $fondsen[$fonds]['directeOpbrengstEUR'];
      $totalenRegio[$fondsen[$fonds]['BeleggingscategorieVolgorde']][$fondsen[$fonds]['RegioVolgorde']]['bronheffingEUR'] += $fondsen[$fonds]['bronheffingEUR'];

      $totalenCategorie[$fondsen[$fonds]['BeleggingscategorieVolgorde']]['beginPortefeuilleWaardeEuro'] += $fondsen[$fonds]['beginPortefeuilleWaardeEuro'];
      $totalenCategorie[$fondsen[$fonds]['BeleggingscategorieVolgorde']]['actuelePortefeuilleWaardeEuro'] += $fondsen[$fonds]['actuelePortefeuilleWaardeEuro'];
      $totalenCategorie[$fondsen[$fonds]['BeleggingscategorieVolgorde']]['opgelopenrente'] += $fondsen[$fonds]['opgelopenrente'];
      $totalenCategorie[$fondsen[$fonds]['BeleggingscategorieVolgorde']]['directeOpbrengstEUR'] += $fondsen[$fonds]['directeOpbrengstEUR'];
      $totalenCategorie[$fondsen[$fonds]['BeleggingscategorieVolgorde']]['bronheffingEUR'] += $fondsen[$fonds]['bronheffingEUR'];
  
      $totaalAlles['beginPortefeuilleWaardeEuro'] += $fondsen[$fonds]['beginPortefeuilleWaardeEuro'];
      $totaalAlles['actuelePortefeuilleWaardeEuro'] += $fondsen[$fonds]['actuelePortefeuilleWaardeEuro'];
      $totaalAlles['opgelopenrente'] += $fondsen[$fonds]['opgelopenrente'];
      $totaalAlles['directeOpbrengstEUR'] += $fondsen[$fonds]['directeOpbrengstEUR'];
      $totaalAlles['bronheffingEUR'] += $fondsen[$fonds]['bronheffingEUR'];
  
      $omschrijvingPerBeleggingscategorieVolgorde[$fondsen[$fonds]['BeleggingscategorieVolgorde']]=$fondsen[$fonds]['BeleggingscategorieOmschrijving'];
      $omschrijvingPerRegioVolgorde[$fondsen[$fonds]['RegioVolgorde']]=$fondsen[$fonds]['RegioOmschrijving'];
      $omschrijvingPerValutaVolgorde[$fondsen[$fonds]['ValutaVolgorde']]=$fondsen[$fonds]['Valuta'];

    }
    $omschrijvingPerBeleggingscategorieVolgorde[127]='Liquiditeiten';
    $omschrijvingPerRegioVolgorde[127]='Geldrekeningen';

	//listarray($fondsen);exit;
    ksort($sortering);
    foreach ($sortering as $key => $data)
    {
      ksort($data);
      $sortering[$key] = $data;
    }

    $this->pdf->SetFillColor(230);
  
    $i=0;
   //listarray($sortering);
  // listarray($omschrijvingPerBeleggingscategorieVolgorde);
    foreach ($sortering as $CatVolgorde => $regioCategorien)
    {
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',vertaalTekst($omschrijvingPerBeleggingscategorieVolgorde[$CatVolgorde],$this->pdf->rapport_taal)));
  
      foreach ($regioCategorien as $regioCatVolgorde => $valutaCategorien)
      {
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('',vertaalTekst($omschrijvingPerRegioVolgorde[$regioCatVolgorde],$this->pdf->rapport_taal)));
       
    
    
        foreach ($valutaCategorien as $valutaCatVolgorde => $regels)
        {
          //	listarray($regels);
          $this->pdf->fillCell = array();
 //         $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
 //         $this->pdf->row(array('','    '.$omschrijvingPerValutaVolgorde[$valutaCatVolgorde]));
          foreach ($regels as $key => $regel)
          {
            $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
            $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
            if ($i % 2 == 0)
            {
              $this->pdf->fillCell = $fillArray;
            }
            else
            {
              $this->pdf->fillCell = array();
            }
            $i++;
            if ($regel['type'] == 'fondsen')
            {
      
              $this->pdf->row(array($regel['Valuta'],
                                $regel['fondsOmschrijving'],
                                '',
       
                                $this->formatAantal($regel['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                                $this->formatGetal($regel['actueleFonds'], 2),
                                $this->formatGetal($regel['actuelePortefeuilleWaardeEuro'] + $regel['opgelopenrente'], $this->pdf->rapport_VOLK_decimaal),
                                '',
                                $this->formatGetal($regel['directeOpbrengst'], $this->pdf->rapport_VOLK_decimaal),
                                $this->formatGetal($regel['bronheffing'], $this->pdf->rapport_VOLK_decimaal),
                                '',
                                $this->formatGetal($regel['directeOpbrengstEUR'], $this->pdf->rapport_VOLK_decimaal),
                                $this->formatGetal($regel['bronheffingEUR'], $this->pdf->rapport_VOLK_decimaal),));
      
            }
            else
            {
      
              $this->pdf->row(array($regel['Valuta'],
                                $regel['fondsOmschrijving'],
                                '',
                                '',
                                '',
                                $this->formatGetal($regel['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                                '',
                                '',
                                '',
                                '','', ''));
            }
            $this->pdf->fillCell = array();
          }
          $this->checkNewPage();
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $totaal=$totalenValuta[$hoofdCatVolgorde][$regioCatVolgorde][$valutaCatVolgorde];
          $this->pdf->CellBorders = array('','','','','','TS','','TS','TS','','TS','TS');
          $this->pdf->row(array('',
                            vertaalTekst('Totaal',$this->pdf->rapport_taal).' '.vertaalTekst($omschrijvingPerValutaVolgorde[$valutaCatVolgorde],$this->pdf->rapport_taal),
                            "",
                            '',
                            '',
                            $this->formatGetal($totaal['actuelePortefeuilleWaardeEuro']+$totaal['opgelopenrente'], $this->pdf->rapport_VOLK_decimaal),
                            '',
                            $this->formatGetal($totaal['directeOpbrengst'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($totaal['bronheffing'], $this->pdf->rapport_VOLK_decimaal),
                            '',
                            $this->formatGetal($totaal['directeOpbrengstEUR'], $this->pdf->rapport_VOLK_decimaal),
                            $this->formatGetal($totaal['bronheffingEUR'], $this->pdf->rapport_VOLK_decimaal),''));
          //$this->pdf->ln();
          unset($this->pdf->CellBorders);
          $this->pdf->ln();
  
        }
        $this->checkNewPage();
        $this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
        $totaal=$totalenRegio[$hoofdCatVolgorde][$regioCatVolgorde];
        $this->pdf->CellBorders = array('','','','','','TS','','','','','TS','TS');
        $this->pdf->row(array('',
                          vertaalTekst('Totaal',$this->pdf->rapport_taal).' '.vertaalTekst($omschrijvingPerRegioVolgorde[$regioCatVolgorde],$this->pdf->rapport_taal),
                           "",
                          '',
                          '',
                          $this->formatGetal($totaal['actuelePortefeuilleWaardeEuro']+$totaal['opgelopenrente'], $this->pdf->rapport_VOLK_decimaal),
                          '',
                          '',  '','',
                     $this->formatGetal($totaal['directeOpbrengstEUR'], $this->pdf->rapport_VOLK_decimaal),
                     $this->formatGetal($totaal['bronheffingEUR'], $this->pdf->rapport_VOLK_decimaal),
                          '',''));
        $this->pdf->ln();
        unset($this->pdf->CellBorders);
        
      }
      $this->checkNewPage();
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $totaal=$totalenCategorie[$hoofdCatVolgorde];
      $this->pdf->CellBorders = array('','','','','','TS','','','','','TS','TS');
      $this->pdf->row(array('',
                        vertaalTekst('Totaal',$this->pdf->rapport_taal).' '.vertaalTekst($omschrijvingPerBeleggingscategorieVolgorde[$hoofdCatVolgorde],$this->pdf->rapport_taal),
                        "",
                        '',
                        '',
                        $this->formatGetal($totaal['actuelePortefeuilleWaardeEuro']+$totaal['opgelopenrente'], $this->pdf->rapport_VOLK_decimaal),
                        '','','','',
                       $this->formatGetal($totaal['directeOpbrengstEUR'], $this->pdf->rapport_VOLK_decimaal),
                       $this->formatGetal($totaal['bronheffingEUR'], $this->pdf->rapport_VOLK_decimaal),
                        '',''));
      $this->pdf->ln();
      unset($this->pdf->CellBorders);
    }
  
    $this->pdf->row(array('',
                      vertaalTekst('Totaal',$this->pdf->rapport_taal),
                      "",
                      '',
                      '',
                      $this->formatGetal($totaalAlles['actuelePortefeuilleWaardeEuro']+$totaalAlles['opgelopenrente'], $this->pdf->rapport_VOLK_decimaal),
                      '',
                      '','','',
                     $this->formatGetal($totaalAlles['directeOpbrengstEUR'], $this->pdf->rapport_VOLK_decimaal),
                     $this->formatGetal($totaalAlles['bronheffingEUR'], $this->pdf->rapport_VOLK_decimaal),
                      ));
  
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0, 0, 0);
  }
  
  function checkNewPage()
  {
    if($this->pdf->getY()+5>$this->pdf->PageBreakTrigger)
    {
      $this->pdf->addPage();
    }
  }

}
?>