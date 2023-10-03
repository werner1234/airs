<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_112/ATTberekening_L112.php");
include_once("rapport/include/layout_112/RapportPERFG_L112.php");
include_once("rapport/include/layout_112/RapportOIV_L112.php");
include_once("rapport/include/layout_112/RapportAFM_L112.php");
include_once("rapport/RapportVKM.php");

class RapportOIS_L112
{
  
  function RapportOIS_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->perfg=new RapportPERFG_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->oiv=new RapportOIV_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->afm=new RapportAFM_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->vkm=new RapportVKM(null,$portefeuille,$rapportageDatumVanaf,$rapportageDatum);
    $this->vkm->writeRapport();
    $this->pdf->rapport_type = "OIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    if($this->pdf->rapport_PERF_titel)
      $this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
    else
      $this->pdf->rapport_titel = "Resultaat- en rendementsberekening ".date("j",$this->pdf->rapport_datumvanaf)." ".
        vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
        date("Y",$this->pdf->rapport_datumvanaf)." ".
        vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".
        date("j",$this->pdf->rapport_datum)." ".
        vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
        date("Y",$this->pdf->rapport_datum);
    
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L112($this);

  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function formatGetalKoers($waarde, $dec , $start = false)
  {
    if ($start == false)
      $waarde = $waarde / $this->pdf->ValutaKoersEind;
    else
      $waarde = $waarde / $this->pdf->ValutaKoersStart;
    
    return number_format($waarde,$dec,",",".");
  }
  
  
  
  function writeRapport()
  {
    global $__appvar;
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
  
    $y=$this->pdf->getY();
    $this->addResultaat();
    $this->addPortefeuilleverdeling();
    $this->pdf->setY($y+4);
    $this->indexVergelijking();
    $this->VKMBlok();
    $this->addVerdeling();
    $this->PERFGGrafiek();
    $this->toonTopTien();
    $this->addAFM();
 }
 
 function addPortefeuilleverdeling()
 {
   if(is_array($this->pdf->portefeuilles) && count($this->pdf->portefeuilles) > 1)
   {
     $DB=new DB();
     $q="SELECT Portefeuilles.Portefeuille,Portefeuilles.Depotbank,Depotbanken.Omschrijving,Portefeuilles.kleurcode FROM Portefeuilles JOIN Depotbanken ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')";
     $DB->SQL($q);
     $DB->Query();
     $portefeuilleDetails=array();
     while($data=$DB->NextRecord())
     {
       $portefeuilleDetails[$data['Portefeuille']] = $data;
     }
     $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
     $DB->SQL($q);
     $DB->Query();
     $kleuren = $DB->LookupRecord();
     $kleuren = unserialize($kleuren['grafiek_kleur']);
     $randomKleuren=array();
     $alAanwqezigeKleur=array();
     foreach($kleuren as $type=>$kleurdata)
     {
       foreach($kleurdata as $cat=>$kleur)
       {
         if($kleur['R']['value']<>0 || $kleur['G']['value']<>0 || $kleur['B']['value']<>0)
         {
           if(!isset($alAanwqezigeKleur[$kleur['R']['value'].$kleur['G']['value'].$kleur['B']['value']]))
           {
             $randomKleuren[] = array($kleur['R']['value'], $kleur['G']['value'], $kleur['B']['value']);
             $alAanwqezigeKleur[$kleur['R']['value'] . $kleur['G']['value'] . $kleur['B']['value']] = 1;
           }
         }
       }
     }
     
     $waardeVerdeling=array();
     $totaleWaarde=0;
     $i=0;
     foreach($this->pdf->portefeuilles as $portefeuille)
     {
       $gegevens = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum);
       foreach($gegevens as $regel)
       {
         $waardeVerdeling[$portefeuilleDetails[$portefeuille]['Omschrijving']]+=$regel['actuelePortefeuilleWaardeEuro'];
         $totaleWaarde+=$regel['actuelePortefeuilleWaardeEuro'];
       }
     }
     foreach($waardeVerdeling as $omschrijving=>$waarde)
     {
       $portefeuilleAandeel[$omschrijving] = $waarde / $totaleWaarde * 100;
       $portefeuilleKleur[] = $randomKleuren[$i];
       $i++;
     }
  
     $grafiekY=113;
     $this->pdf->setXY(108,$grafiekY);
     $this->PieChart2(35, 35, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleur);
     
   }

 }
  
  function PieChart2( $w, $h, $data, $format, $colors = null)
  {
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data, $format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < $this->pdf->NbVal; $i++)
      {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
 //   $this->pdf->setDrawColor(255,255,255);
    foreach ($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    $this->pdf->setDrawColor(0,0,0);
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w + $radius * .5;
   // $x1 = $XPage + $w + $radius ;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - ($radius) + $margin;
    
    for ($i = 0; $i < $this->pdf->NbVal; $i++)
    {
      $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      $this->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2, $y1);
      if(strpos($this->pdf->legends[$i],'||')>0)
      {
        $parts=explode("||",$this->pdf->legends[$i]);
        $this->pdf->Cell(0, $hLegend, $parts[1]);
      }
      else
      {
        $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
      }
      $y1 += $hLegend + $margin;
    }
  }
 
  function addAFM()
  {
    $this->afm->createPDF=false;
    $this->afm->writeRapport();
    $x=208;
    $y=140;
    $h=35;
    $this->afm->LineDiagram($x, $y, 75, $h,  $this->afm->maandTotalenCumulatief, 'Attributie-analyse');
    $xval = $x;
    $yval = $y+$h+15;
    $colors=array('allocate effect'=>array(108,31,128),'selectie effect'=>array(234,105,11),'attributie effect'=>array(0,52,121)); //
    foreach ($colors as $effect => $color)
    {
      $this->pdf->Rect($xval, $yval, 3, 3, 'DF', null, $color);
      $this->pdf->SetTextColor(0);
      $this->pdf->SetXY($xval + 5, $yval);
      $this->pdf->Cell(5, 3, vertaalTekst($effect, $this->pdf->rapport_taal), 0, 0, 'L');
      $xval += 30;
    }
  }
  
  
  function getDataHuidigejaar($portefeuille)
  {
    global $__appvar;
    
    
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    $this->pdf->SetDrawColor(0,0,0);
    // haal totaalwaarde op om % te berekenen
    
    $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
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
				 TijdelijkeRapportage.type,
				 Fondsen.isinCode as isinCode,
				 TijdelijkeRapportage.historischeWaarde,
				 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.rekening ".
      " FROM TijdelijkeRapportage
				  LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
      " TijdelijkeRapportage.type IN('fondsen') AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
    
    // print detail (select from tijdelijkeRapportage)
    debugSpecial($subquery,__FILE__,__LINE__);
    $DB2 = new DB();
    $DB2->SQL($subquery);
    $DB2->Query();
    $resulaten=array();
    $fondsGegevens=array();
    while($subdata = $DB2->NextRecord())
    {
      
      $dividend=$this->getDividend($subdata['fonds'],$portefeuille);
      $resultaatEur=($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']);
      $subdata['resultaatEur']=$resultaatEur;
      $procentResultaat = ($resultaatEur / ($subdata['beginPortefeuilleWaardeEuro'] /100));
      $aandeel=$subdata['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
      $procentResultaatBijdrage=$procentResultaat*$aandeel;
      
      if($subdata['beginPortefeuilleWaardeEuro'] < 0)
        $procentResultaat = -1 * $procentResultaat;
      
      
      if($procentResultaat < 1000 || $procentResultaat > -1000)
      {
        $resulaten[$subdata['fonds']]=$procentResultaatBijdrage;
        $subdata['rendement']=$procentResultaat;
        $subdata['rendementBijdrage']=$procentResultaatBijdrage;
      }
      $fondsGegevens[$subdata['fonds']]=$subdata;
      
    }
    asort($resulaten);
    $i=0;
    $negatief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $negatief[$fonds]=$fondsGegevens[$fonds];
      if($i==5)
        break;
      $i++;
    }
    $resulaten=array_reverse($resulaten,true);
    $i=0;
    $positief=array();
    foreach($resulaten as $fonds=>$rendment)
    {
      $positief[$fonds]=$fondsGegevens[$fonds];
      if($i==5)
        break;
      $i++;
    }
    
    return array('positief'=>$positief,'negatief'=>$negatief);
    
  }
  
  
  function getDividend($fonds,$portefeuille)
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
       portefeuille = '".$portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
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
     WHERE Rekeningen.Portefeuille='".$portefeuille."' AND
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
        $fondsAantal=fondsAantalOpdatum($portefeuille,$fonds,$data['Boekdatum']);
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
  
  function toonTabel($xmarge,$tabeldata,$titel)
  {
    $this->pdf->setY(160);
    $this->pdf->SetWidths(array($xmarge,6,48,20,12));
    $this->pdf->SetAligns(array('L','L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('',array('L','T','U'),array('T','U','L'),array('T','U','L'),array('T','U','L','R'));
    $this->pdf->row(array('','',$titel,'in EUR','in %'));
    $this->pdf->CellBorders=array('',array('L'),array('L'),array('L'),array('L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $n=1;
    $aantal=count($tabeldata);
    foreach($tabeldata as $fonds=>$fondsData)
    {
      //$this->pdf->row(array('',$n,$fondsData['fondsOmschrijving'],round($fondsData['rendementBijdrage']*100)));
      $omschrijving=$this->testTxtLength($fondsData['fondsOmschrijving'],2);
      if($n==$aantal)
      {
        $this->pdf->CellBorders=array('',array('L','U'),array('U','L'),array('U','L'),array('U','L','R'));
      }
      $this->pdf->row(array('',$n,$omschrijving,$this->formatGetal($fondsData['resultaatEur'],0,false,true),$this->formatGetal($fondsData['rendementBijdrage'],2,false,true)));//
      $n++;
    }
    
  }
  
  function testTxtLength($txt,$cell=1)
  {
    $stringWidth=$this->pdf->GetStringWidth($txt."   ");
    if($stringWidth < $this->pdf->widths[$cell])
    {
      return $txt;
    }
    else
    {
      $tmpTxt=$txt;
      for($i=strlen($txt); $i > 0; $i--)
      {
        if($this->pdf->GetStringWidth($tmpTxt."...   ")>$this->pdf->widths[$cell])
          $tmpTxt=substr($txt,0,$i);
        else
          return $tmpTxt.'...';
      }
      return $tmpTxt;
    }
  }
  
  
  
  function toonTopTien()
  {
    //  $this->pdf->ln(10);
    $data = $this->getDataHuidigejaar($this->portefeuille);

    $this->toonTabel(1,$data['positief'],"Grootste bijdrage");
    $this->toonTabel(95,$data['negatief'],"Kleinste bijdrage");
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
  }
  
  function VKMBlok()
  {
    //listarray($this->vkm->vkmWaarde);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(190,45,20,25));
    $this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->row(array('','Kosten (op jaarbasis)','EUR',"%"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Indirecte (fonds)kosten',$this->formatGetal($this->vkm->vkmWaarde['totaalDoorlopendekosten'],0),$this->formatGetal($this->vkm->vkmWaarde['totaalDoorlopendekosten']/$this->vkm->vkmWaarde['gemiddeldeWaarde']*100,2)."%"));
    $this->pdf->row(array('','Totaal directe kosten',$this->formatGetal($this->vkm->vkmWaarde['totaalDirecteKosten'],0),$this->formatGetal($this->vkm->vkmWaarde['totaalDirecteKosten']/$this->vkm->vkmWaarde['gemiddeldeWaarde']*100,2)."%"));
    $this->pdf->row(array('','Vergelijkende kostenmaatstaf',$this->formatGetal($this->vkm->vkmWaarde['totaalDoorlopendekosten']+$this->vkm->vkmWaarde['totaalDirecteKosten'],0),$this->formatGetal($this->vkm->vkmWaarde['vkmWaarde'],2)."%"));

  
  }
  
  function addVerdeling()
  {
    $this->oiv->getData();
    $ystart=$this->pdf->getY();//65;
    $x=215;
    $this->pdf->setXY($x, $ystart);
    if(min($this->oiv->grafiekdata['beleggingscategorie']['percentage']) >=0)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->MultiCell(30, 10, 'Categorieverdeling', 0, "C");
      $this->pdf->setXY($x, $ystart + 7);
      $this->pdf->PieChart(100, 30, $this->oiv->grafiekdata['beleggingscategorie']['percentage'], '%l (%p)', array_values($this->oiv->grafiekdata['beleggingscategorie']['kleuren']));
    }
    $x=260;
    $this->pdf->setXY($x,$ystart);
  
    $maxValuta=5;
    $tmp=array();
    $n=0;

    arsort($this->oiv->grafiekdata['valuta']['percentage']);
    foreach($this->oiv->grafiekdata['valuta']['percentage'] as $valuta=>$percentage)
    {
      $kleur=$this->oiv->grafiekdata['valuta']['kleuren'][$valuta];
      if($n>=$maxValuta)
      {
        $valuta='Overige';
        $tmp['percentage'][$valuta]+=$percentage;
      }
      else
      {
        $tmp['percentage'][$valuta]=$percentage;
      }
      $tmp['kleuren'][$valuta]=$kleur;
      $n++;
    }
    $this->oiv->grafiekdata['valuta']=$tmp;
    
    if(min($this->oiv->grafiekdata['valuta']['percentage']) >=0)
    {
      
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->MultiCell(30, 10, 'Valutaverdeling', 0, "C");
      $this->pdf->setXY($x, $ystart + 7);
      $this->pdf->PieChart(100, 30, $this->oiv->grafiekdata['valuta']['percentage'], '%l (%p)', array_values($this->oiv->grafiekdata['valuta']['kleuren']));
    }
  }
  
  function PERFGGrafiek()
  {
    $index=new indexHerberekening();
    $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
    $laatsteDatum='leeg';
    $huidigeJaarGrafiek=array();
    foreach ($indexData as $i=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
        $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
        $barGraph['Index'][$data['datum']]['leeg']=0;
        foreach ($data['extra']['cat'] as $categorie=>$waarde)
        {
          if($categorie=='LIQ'||$categorie=='H-Liq')
            $categorie='Liquiditeiten';
        
          $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
          if($waarde <> 0)
            $categorien[$categorie]=$categorie;
        }
      }
      $huidigeJaarGrafiek[$data['datum']]['performance']=$data['performance'];
      $huidigeJaarGrafiek[$data['datum']]['performanceCumu']=((1+$huidigeJaarGrafiek[$laatsteDatum]['performanceCumu']/100) * (1+$data['performance']/100)-1) * 100;
      $laatsteDatum=$data['datum'];
    }
    $stdev=getFondsPerformanceGestappeld2($this->pdf->portefeuilledata['SpecifiekeIndex'],$this->portefeuille,$this->rapportageDatumVanaf , $this->rapportageDatum,'maanden',false,true,true);
    $laatsteDatum='leeg';
    foreach($stdev->reeksen['benchmark'] as $datum=>$rendementDetails)
    {
      $huidigeJaarGrafiek[$datum]['benchmark'] = $rendementDetails['perf'];
      $huidigeJaarGrafiek[$datum]['benchmarkCumu'] = ((1 + $huidigeJaarGrafiek[$laatsteDatum]['benchmarkCumu'] / 100) * (1 + $rendementDetails['perf'] / 100) - 1) * 100;
      $laatsteDatum=$datum;
    }
  
    $q="SELECT Omschrijving FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $this->perfg->benchmarkOmschrijving = $DB->LookupRecord();

    if (count($huidigeJaarGrafiek) > 0)
    {
      $this->pdf->SetXY($this->pdf->marge,99)		;//112
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(138, 5, vertaalTekst('Rendement lopend jaar',$this->pdf->rapport_taal), 0, 1);
      $this->pdf->SetXY(15,135)		;//112
      $this->perfg->VBarDiagram2(90,30,$huidigeJaarGrafiek,true);
    }
    
  }
  
  
  function getGrootboeken()
  {
    $vertaling=array();
    $db=new DB();
    $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      if($data['Grootboekrekening']=='RENTE')
        $data['Omschrijving']="Rente (spaar)rekeningen";
      
      $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
    }
    return $vertaling;
  }
  
  
  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  
  function indexVergelijking()
  {
    $DB=new DB();
    
    
    $perioden=array('begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    
    $query="SELECT Portefeuilles.specifiekeIndex as Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta
FROM Portefeuilles
Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds
WHERE Portefeuilles.Portefeuille = '$this->portefeuille'";
    $DB->SQL($query);
    $DB->Query();
    $specifiekeIndex = $DB->nextRecord();
    
    $query="SELECT
'samenstelling' as samenstelling,
benchmarkverdeling.benchmark,
benchmarkverdeling.fonds as Beursindex,
benchmarkverdeling.percentage,
Fondsen.Omschrijving,
Fondsen.Valuta
FROM
benchmarkverdeling
Inner Join Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
WHERE benchmarkverdeling.benchmark='".mysql_real_escape_string($specifiekeIndex['Beursindex'])."'";

    $DB->SQL($query);
    $DB->Query();
    $indices=array();
    while($index = $DB->nextRecord())
      $indices[]=$index;
    
    $query="SELECT max(benchmarkverdelingVanaf.vanaf) as datum FROM
benchmarkverdelingVanaf WHERE vanaf<='".$this->rapportageDatum."' AND
benchmarkverdelingVanaf.benchmark ='".mysql_real_escape_string($specifiekeIndex['Beursindex'])."'";
    $DB->SQL($query);
    $datum=$DB->lookupRecord();
    $query="SELECT
	benchmarkverdelingVanaf.benchmark,
	benchmarkverdelingVanaf.fonds AS Beursindex,
	benchmarkverdelingVanaf.percentage,
	Fondsen.Omschrijving,
	Fondsen.Valuta
FROM
	benchmarkverdelingVanaf
INNER JOIN Fondsen ON benchmarkverdelingVanaf.fonds = Fondsen.Fonds
WHERE
	benchmarkverdelingVanaf.benchmark ='".mysql_real_escape_string($specifiekeIndex['Beursindex'])."' AND benchmarkverdelingVanaf.vanaf='".$datum['datum']."'";
    $DB->SQL($query);
    $DB->Query();

    while($index = $DB->nextRecord())
      $indices[]=$index;
  
    $specifiekeIndex['totaal']=true;
    $indices[]=$specifiekeIndex;
  
    $totalen=array();
    foreach($indices as $index)
    {
      if($index['specialeIndex']==1)
      {
        $specialeBenchmarks[]=$index['Beursindex'];
        $specialeIndexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
          $specialeIndexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
        $specialeIndexData[$index['Beursindex']]['performance'] =     ($specialeIndexData[$index['Beursindex']]['fondsKoers_eind'] - $specialeIndexData[$index['Beursindex']]['fondsKoers_begin']) / ($specialeIndexData[$index['Beursindex']]['fondsKoers_begin']/100 );
      }
      else
      {
        $benchmarks[]=$index['Beursindex'];
        $indexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
          $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
        $indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
      }
      $indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
      
      if($index['samenstelling']=='samenstelling')
      {
        $totalen['performance'] += $indexData[$index['Beursindex']]['performance'] * $index['percentage'] / 100;
        $totalen['performanceEur'] += $indexData[$index['Beursindex']]['performanceEur'] * $index['percentage'] / 100;
        $totalen['percentage']+=$index['percentage'];
      }
    }
//listarray($totalen);exit;
   // listarray($indices);
  //listarray($benchmarks);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(190,45,20,25));
    $this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->Rect($this->pdf->marge+190,$this->pdf->getY(),90,count($benchmarks)*4+4);
    $this->pdf->row(array("","Vergelijkingsmaatstaven","Rendement","Rendement EUR"));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    foreach ($benchmarks as $fonds)
    {
      $fondsData=$indexData[$fonds];
      $omschrijving=$this->testTxtLength($fondsData['Omschrijving'],1);
      if($fondsData['performance']==0 && $fondsData['totaal']==true)
      {
        $fondsData['performance'] = $totalen['performance'];
        $fondsData['performanceEur'] = $totalen['performanceEur'];
      }
      if($fondsData['Omschrijving']=='')
        $this->pdf->row(array(''));
      else
        $this->pdf->row(array('',$omschrijving,
                          $this->formatGetal($fondsData['performance'],2)."%",
                          $this->formatGetal($fondsData['performanceEur'],2)."%"));
    }
    
    
    if(count($specialeBenchmarks) > 0)
    {
      $this->pdf->SetY(150);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->SetWidths(array(150,60,20,20,20));
      $this->pdf->SetAligns(array('L','L','R','R','R'));
      $this->pdf->Rect($this->pdf->marge+150,150,120,count($specialeBenchmarks)*4+4);
      $this->pdf->row(array("","Overige marktindices ter informatie","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
      unset($this->pdf->CellBorders);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
      foreach ($specialeBenchmarks as $fonds)
      {
        $fondsData=$specialeIndexData[$fonds];
        if($fondsData['Omschrijving']=='')
          $this->pdf->row(array(''));
        else
          $this->pdf->row(array('',$fondsData['Omschrijving'],
                            $this->formatGetal($fondsData['performance'],2)."%"));
      }
    }
    
  }
  
  function addResultaat()
  {
  
    // voor data
    $this->pdf->widthA = array(5,80,30,5,30,5,30,120);
    $this->pdf->alignA = array('L','L','R','L','R');
  
    // voor kopjes
    $this->pdf->widthB = array(0,85,30,5,30,5,30,120);
    $this->pdf->alignB = array('L','L','R','L','R');
  
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
  
    // $this->getKleuren();

  
    if(!isset($this->pdf->__appvar['consolidatie']))
    {
      $this->pdf->__appvar['consolidatie']=1;
      $this->pdf->portefeuilles=array($this->portefeuille);
    }
    
    $vetralingGrootboek=$this->getGrootboeken();
    
    // $att=new ATTberekening_L112($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'totaal');
    $tmp=array_keys($this->waarden['Periode']);
    $categorien=array('totaal');
    foreach($tmp as $categorie)
    {
      if($categorie<>'totaal')
        $categorien[]=$categorie;
    }
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $fillArray=array(0,1);
    $subOnder=array('','');
    $volOnder=array('U','U');
    $subBoven=array('','');
    $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
    $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
    
    
    foreach($categorien as $categorie)
    {
      $volOnder[]='U';
      $volOnder[]='U';
      $subOnder[]='U';
      $subOnder[]='';
      $subBoven[]='T';
      $subBoven[]='';
      $fillArray[]=1;
      $fillArray[]=1;
      $header[]=$this->att->categorien[$categorie];
      $header[]='';
      $samenstelling[]='';
      $samenstelling[]='';
      // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    }

    $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $stortingen=array("",vertaalTekst("Stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $onttrekking=array("",vertaalTekst("Onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $effectenmutaties=array("",vertaalTekst("Mutaties gedurende verslagperiode",$this->pdf->rapport_taal));
    
    
    $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
    $ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    
    $gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
    $gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    $valutaResultaat=array("",vertaalTekst("Resultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
    $rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
    $totaalOpbrengst=array("","");//totaalOpbrengst
    
    $totaalKosten=array("","");   //totaalKosten
    $totaal=array("","");   //totaalOpbrengst-totaalKosten
  
    $perbeginXls=$perbegin;
    $waardeRapdatumXls=$waardeRapdatum;
    $mutwaardeXls=$mutwaarde;
    $stortingenXls=$stortingen;
    $onttrekkingXls=$onttrekking;
    $effectenmutatiesXls=$effectenmutaties;
    $resultaatXls=$resultaat;
    $rendementXls=$rendement;
    $ongerealiseerdFondsXls=$ongerealiseerdFonds;
    $ongerealiseerdValutaXls=$ongerealiseerdValuta;
    $gerealiseerdFondsXls=$gerealiseerdFonds;
    $gerealiseerdValutaXls=$gerealiseerdValuta;
    $valutaResultaatXls=$valutaResultaat;
    $renteXls=$rente;
    $totaalOpbrengstXls=$totaalOpbrengst;
    $totaalKostenXls=$totaalKosten;
    $totaalXls=$totaal;
    foreach($categorien as $categorie)
    {
      unset($this->waarden['Periode'][$categorie]['perfWaarden']);
    }
  

   
    foreach($categorien as $categorie)
    {
      $perfWaarden=$this->waarden['Periode'][$categorie];
      $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
      $perbegin[]='';
      $perbeginXls[]=round($perfWaarden['beginwaarde'],0);
      $perbeginXls[]='';
      $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
      $waardeRapdatum[]='';
      $waardeRapdatumXls[]=round($perfWaarden['eindwaarde'],0);
      $waardeRapdatumXls[]='';
      $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
      $mutwaarde[]='';
      $mutwaardeXls[]=round($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0);
      $mutwaardeXls[]='';
      
      if($categorie=='totaal')
      {
        $effectenmutaties[]='';
        $effectenmutaties[]='';
        $effectenmutatiesXls[]='';
        $effectenmutatiesXls[]='';
        $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
        $stortingen[]='';
        $stortingenXls[]=round($perfWaarden['storting'],0);
        $stortingenXls[]='';
        $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
        $onttrekking[]='';
        $onttrekkingXls[]=round($perfWaarden['onttrekking'],0);
        $onttrekkingXls[]='';
      }
      else
      {
        $effectenmutaties[]=$this->formatGetal($perfWaarden['stort'],0);
        $effectenmutaties[]='';
        $effectenmutatiesXls[]=round($perfWaarden['stort'],0);
        $effectenmutatiesXls[]='';
        $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
        $stortingen[]='';
        $stortingenXls[]='';
        $stortingenXls[]='';
        $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
        $onttrekking[]='';
        $onttrekkingXls[]='';
        $onttrekkingXls[]='';
      }
      
      $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
        $perfWaarden['ongerealiseerdFondsResultaat']+
        $perfWaarden['ongerealiseerdValutaResultaat']+
        $perfWaarden['gerealiseerdFondsResultaat']+
        $perfWaarden['gerealiseerdValutaResultaat']+
        $perfWaarden['opgelopenrente'];
      
      $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR+$perfWaarden['kosten']);
      $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
      
      $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
      $resultaat[]='';
      $resultaatXls[]=round($perfWaarden['resultaat'],0);
      $resultaatXls[]='';
      if($categorie=='H-Liq')
      {
        $rendement[] = '';
        $rendement[] = '';
        $rendementXls[] = '';
        $rendementXls[] = '';
      }
      else
      {
        $rendement[] = $this->formatGetal($perfWaarden['procent'], 2) . ' %';
        $rendement[] = '';
        $rendementXls[] = round($perfWaarden['procent'], 2) ;
        $rendementXls[] = '';
      }
      $ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0);
      $ongerealiseerdFonds[]='';
      $ongerealiseerdFondsXls[]=round($perfWaarden['ongerealiseerdFondsResultaat'],0);
      $ongerealiseerdFondsXls[]='';
      $ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
      $ongerealiseerdValuta[]='';
      $ongerealiseerdValutaXls[]=round($perfWaarden['ongerealiseerdValutaResultaat'],0);
      $ongerealiseerdValutaXls[]='';
      $gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0);
      $gerealiseerdFonds[]='';
      $gerealiseerdFondsXls[]=round($perfWaarden['gerealiseerdFondsResultaat'],0);
      $gerealiseerdFondsXls[]='';
      $gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
      $gerealiseerdValuta[]='';
      $gerealiseerdValutaXls[]=round($perfWaarden['gerealiseerdValutaResultaat'],0);
      $gerealiseerdValutaXls[]='';
      $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
      $valutaResultaat[]='';
      $valutaResultaatXls[]=round($perfWaarden['resultaatValuta'],0);
      $valutaResultaatXls[]='';
      $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
      $rente[]='';
      $renteXls[]=round($perfWaarden['opgelopenrente'],0);
      $renteXls[]='';
      $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0);
      $totaalOpbrengst[]='';
      $totaalOpbrengstXls[]=round($totaalOpbrengstEUR,0);
      $totaalOpbrengstXls[]='';
      $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
      $totaalKosten[]='';
      $totaalKostenXls[]=round($perfWaarden['kosten'],0);
      $totaalKostenXls[]='';
      $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
      $totaal[]='';
      $totaalXls[]=round($perfWaarden['resultaat'],0);
      $totaalXls[]='';
  
  
  
      foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $opbrengstCategorien[$categorie]=$categorie;
      foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $kostenCategorien[$categorie]=$categorie;
      
    }
    
    $cellWidth=27;
    $cellWidthP=2;
    $this->pdf->widthB = array(0,62,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP);
    $this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,5,30,5,30,5,30,5,30,5,30,5);
    $this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');


//listarray($perfWaarden);
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->ln();
    $this->headerTop=$this->pdf->GetY();
    $this->pdf->row($header);
    $this->pdf->excelData[]=$header;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->row($perbegin);
    $this->pdf->excelData[]=$perbeginXls;
    //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->excelData[]=$waardeRapdatumXls;
    $this->pdf->CellBorders = array();
    // subtotaal
    $this->pdf->ln();
    $this->pdf->excelData[]=array();
    $this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
    $this->pdf->excelData[]=$mutwaardeXls;
    $this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->excelData[]=$stortingenXls;
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->excelData[]=$onttrekkingXls;
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($effectenmutaties);
    $this->pdf->excelData[]=$effectenmutatiesXls;
    $this->pdf->ln();
    $this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
    $this->pdf->CellBorders = array();
    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=$resultaatXls;
    $this->pdf->ln();
    
  //  $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
  
    
    $benchmarkProcent=getFondsPerformanceGestappeld2($this->pdf->portefeuilledata['SpecifiekeIndex'],$this->portefeuille,$this->rapportageDatumVanaf , $this->rapportageDatum,'maanden',false,true,false);
    $portefeuilleProcent=$this->waarden['Periode']['totaal']['procent'];
    $this->pdf->CellBorders = array();
    $this->pdf->row(array('','Rendement benchmark',$this->formatGetal($benchmarkProcent,2).'%'));
    $this->pdf->row(array('','Verschil',$this->formatGetal($portefeuilleProcent-$benchmarkProcent,2).'%'));
    
    //echo "$portefeuilleProcent $benchmarkProcent <br>\n";exit;
    
    $this->pdf->excelData[]=$rendementXls;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $ypos = $this->pdf->GetY();
    
    
    $this->pdf->SetY($this->headerTop);
    $this->pdf->widthA = array(98,55,30,5,30,5,30,120);
    $this->pdf->alignA = array('L','L','R','L','R');
    // voor kopjes
    $this->pdf->widthB = array(98,55,30,5,30,5,30,120);
    $this->pdf->alignB = array('L','L','R','L','R');
  
    
    $this->pdf->SetWidths(array(98,140));
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
   // $YSamenstelling=$this->pdf->GetY();
   // $this->pdf->row($samenstelling);//,"","","",""));
    $this->pdf->excelData[]=$samenstelling;
    $this->pdf->SetWidths($this->pdf->widthB);
    //$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    //$this->pdf->ln();
    $this->hoogteBeleggingsresultaat=$this->pdf->getY();
    $this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
    $this->pdf->excelData[]=array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"","");
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->row($ongerealiseerdFonds);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
    $this->pdf->excelData[]=$ongerealiseerdFondsXls;
    $this->pdf->row($ongerealiseerdValuta);
    $this->pdf->excelData[]=$ongerealiseerdValutaXls;
    $this->pdf->row($gerealiseerdFonds);
    $this->pdf->excelData[]=$gerealiseerdFondsXls;
    $this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
    $this->pdf->excelData[]=$gerealiseerdValutaXls;
    //	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
    $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
    $this->pdf->excelData[]=$valutaResultaatXls;
    $this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
    $this->pdf->excelData[]=$renteXls;
    $keys=array();
    //foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
    //  $keys[]=$key;
    
    
    
    foreach ($opbrengstCategorien as $grootboek)
    {
      $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
      // foreach($perfWaarden as $port=>$waarden)
      $tmpXls=$tmp;
      foreach($categorien as $categorie)
      {
        $perfWaarden=$this->waarden['Periode'][$categorie];
        $tmp[]=$this->formatGetal($perfWaarden['grootboekOpbrengsten'][$grootboek],0);
        $tmp[]='';
        $tmpXls[]=round($perfWaarden['grootboekOpbrengsten'][$grootboek],0);
        $tmpXls[]='';
      }
      //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
      $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
      $this->pdf->excelData[]=$tmpXls;
    }
    
    $this->pdf->CellBorders = $subBoven;
    $this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
    //$this->pdf->ln();
    $this->pdf->CellBorders = array();
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
    $this->pdf->excelData[]=array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"","");
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    foreach ($kostenCategorien as $grootboek)
    {
      $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
      $tmpXls=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
      foreach($categorien as $categorie)
      {
        $perfWaarden=$this->waarden['Periode'][$categorie];
        
        $tmp[]=$this->formatGetal($perfWaarden['grootboekKosten'][$grootboek],0);
        $tmp[]='';
        $tmpXls[]=round($perfWaarden['grootboekKosten'][$grootboek],0);
        $tmpXls[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
      $this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
      $this->pdf->excelData[]=$tmpXls;
    }
    $this->pdf->CellBorders = $subBoven;
    $this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
    $this->pdf->excelData[]=$totaalKostenXls;
    $posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = array();
    //$this->pdf->CellBorders = $volOnder;
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
    $this->pdf->excelData[]=$totaalXls;
    $actueleWaardePortefeuille = 0;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    
  }
  
  
  
  function SetLegends($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.2f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
  
  function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
  {
    
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
    $this->SetLegends($data,$format);
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $legendWidth=50;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 2);
    $XDiag = $XPage + $margin * 2 + $legendWidth;
    $lDiag = floor($w - $margin * 3 - $legendWidth);
    if($color == null)
      $color=array(155,155,155);
    if ($maxVal == 0) {
      $maxVal = max($data)*1.1;
    }
    if ($minVal == 0) {
      $minVal = min($data)*1.1;
    }
    if($minVal >0)
      $minVal=0;
    
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    $hBar = ($hDiag / ($this->pdf->NbVal + 1));
    $hDiag = $hBar * ($this->pdf->NbVal + 1);
    $eBaton = floor($hBar * 80 / 100);
    $legendaStep=$unit;
    
    $legendaStep=$unit/$nbDiv*$bandBreedte;
    //echo "$bandBreedte / $legendaStep = ".$bandBreedte/$legendaStep." ".$nbDiv;exit;
    //if($bandBreedte/$legendaStep > $nbDiv)
    
    if($bandBreedte/$legendaStep > $nbDiv)
      $legendaStep=$legendaStep*5;
    if($bandBreedte/$legendaStep > $nbDiv)
      $legendaStep=$legendaStep*2;
    if($bandBreedte/$legendaStep > $nbDiv)
      $legendaStep=$legendaStep/2*5;
    $valIndRepere=round($valIndRepere/$unit/5)*5;
    
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    $nullijn=$XDiag - ($offset * $unit) +$margin;
    
    $i=0;
    $nbDiv=10;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    if(round($legendaStep,5) <> 0.0)
    {
      for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
      }
      
      for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $i=0;
    
    //$this->pdf->SetXY(0, $YDiag);
    //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
    //$this->pdf->SetXY($nullijn, $YDiag);
    //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
    $this->pdf->SetXY($XDiag, $YDiag);
    $this->pdf->Cell($lDiag, $hval-4, 'Contributie rendement',0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
    foreach($data as $key=>$val)
    {
      $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
      //Bar
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $hval = $eBaton;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
      //Legend
      $this->pdf->SetXY($XPage, $yval);
      $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
      $i++;
    }
    
    //Scales
    $minPos=($minVal * $unit);
    $maxPos=($maxVal * $unit);
    
    $unit=($maxPos-$minPos)/$nbDiv;
    // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
    
    for ($i = $nullijn+$XDiag; $i <= $maxVal; $i=$i+$unit)
    {
      $xpos = $XDiag +  $i;
      $this->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
      $val = $i * $valIndRepere;
      $xpos = $XDiag +  $i - $this->pdf->GetStringWidth($val) / 2;
      $ypos = $YDiag + $hDiag - $margin;
      $this->pdf->Text($xpos, $ypos, $val);
    }
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $hLegend = 2;
    $radius = min($w - $margin * 4  , $h - $margin * 2); //
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
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    foreach($data as $val) {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0) {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360) {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
  
    $x1 = $XPage - $radius - 22 ;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - $radius + $hLegend*2;
    
    for($i=0; $i<$this->pdf->NbVal; $i++) {
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1);
      $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
      $y1+=$hLegend + $hLegend;
    }
    
  }
  
  
  
}
?>