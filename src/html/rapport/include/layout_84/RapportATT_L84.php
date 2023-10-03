<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/27 18:01:41 $
File Versie					: $Revision: 1.5 $

$Log: RapportATT_L84.php,v $
Revision 1.5  2019/07/27 18:01:41  rvv
*** empty log message ***

Revision 1.4  2019/07/13 17:51:11  rvv
*** empty log message ***

Revision 1.3  2019/07/10 15:38:33  rvv
*** empty log message ***

Revision 1.2  2019/07/05 16:47:00  rvv
*** empty log message ***

Revision 1.1  2019/06/05 16:40:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportATT_L84
{
  function RapportATT_L84($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "ATT";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    
    $this->rapportageDatum = $rapportageDatum;
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $RapStopJaar = date("Y", db2jul($this->rapportageDatum));
    
    $this->tweedeStart();
    
    
    // $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
    /*
       if ($RapStartJaar != $RapStopJaar)
       {
         echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
         exit;
       }
    */
  }
  
  function tweedeStart()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
    {
      if(db2jul($this->pdf->PortefeuilleStartdatum) >  db2jul("$RapStartJaar-01-01"))
      {
        $this->tweedePerformanceStart=substr($this->pdf->PortefeuilleStartdatum,0,10);
      }
      else
      {
        $this->tweedePerformanceStart = "$RapStartJaar-01-01";
      }
      if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01")
      {
        $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
        $this->extraVulling = true;
      }
    }
    
  }
  
  function derdeStart()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $RapJaar = date("Y", db2jul($this->rapportageDatum));
    if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
    {
      $this->derdePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
    {
      if(db2jul($this->rapportageDatumVanaf) <  db2jul(($RapJaar-1).'-'.substr($this->rapportageDatum,5,5)))
      {
        $this->derdePerformanceStart=$this->rapportageDatumVanaf;
      }
      elseif(db2jul($this->pdf->PortefeuilleStartdatum) <  db2jul(($RapJaar-1).'-'.substr($this->rapportageDatum,5,5)))
      {
        $dagMaand=substr($this->rapportageDatumVanaf,5,5);
        if($dagMaand=='12-31')
          $this->derdePerformanceStart=date('Y-m-d',db2jul(($RapJaar-1).'-'.substr($this->rapportageDatum,5,5))+3600*24);
        else
          $this->derdePerformanceStart=($RapJaar-1).'-'.substr($this->rapportageDatum,5,5);
      }
      elseif(db2jul($this->pdf->PortefeuilleStartdatum) >  db2jul("$RapStartJaar-01-01"))
      {
        $this->derdePerformanceStart=substr($this->pdf->PortefeuilleStartdatum,0,10);
      }
      else
      {
        $this->derdePerformanceStart = "$RapStartJaar-01-01";
      }
    }

//echo $this->derdePerformanceStart ;exit;
  }
  
  function formatGetalKoers($waarde, $dec , $start = false)
  {
    if ($start == false)
      $waarde = $waarde / $this->pdf->ValutaKoersEind;
    else
      $waarde = $waarde / $this->pdf->ValutaKoersBegin;
    
    return number_format($waarde,$dec,",",".");
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function printSubTotaal($title, $totaalA, $totaalB)
  {
    // geen subtotaal!
    return true;
  }
  
  
  
  function writeRapport()
  {
    global $__appvar;
    
    $this->tweedeStart();
    $this->derdeStart();
    
    if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    
    
    //Kleuren instellen
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    
    $q="SELECT beleggingscategorie,omschrijving FROM Beleggingscategorien";
    $DB->SQL($q);
    $DB->Query();
    while($cat=$DB->nextRecord())
      $this->categorieOmschrijving[$cat['beleggingscategorie']]=$cat['omschrijving'];
    
    // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');



//listarray($this->categorieOmschrijving);
//listarray($this->categorieVolgorde);
    // voor data
    
    
    $this->pdf->widthB = array(1,95,30,10,30,115);
    $this->pdf->alignB = array('L','L','R','R','R');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->AddPage();
    
    $this->pdf->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
    $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    /*
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widthA), 8, 'F');
    $this->pdf->row(array(vertaalTekst("Maand",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Begin-\nvermogen",$this->pdf->rapport_taal),
                      vertaalTekst("Stortingen en \nonttrekkingen",$this->pdf->rapport_taal),
                      vertaalTekst("Koersresultaat",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Inkomsten",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Kosten",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Opgelopen\nrente",$this->pdf->rapport_taal),
                      vertaalTekst("Beleggings-\nresultaat",$this->pdf->rapport_taal),
                      vertaalTekst("Eind-\nvermogen",$this->pdf->rapport_taal),
                      vertaalTekst("Rendement",$this->pdf->rapport_taal)." %\n(".vertaalTekst("maand",$this->pdf->rapport_taal).")",
                      vertaalTekst("Rendement",$this->pdf->rapport_taal)." %\n(".vertaalTekst("cumulatief",$this->pdf->rapport_taal).")"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $sumWidth = array_sum($this->pdf->widthA);
    $this->pdf->Line($this->pdf->marge+$this->pdf->widthB[0],$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());
    */
    
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
    
    $posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
    $posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    //$this->tweedePerformanceStart='2017-01-31';
    
    $indexData = $this->getWaarden($this->tweedePerformanceStart ,$this->rapportageDatum ,$this->portefeuille);
    $indexDataGrafiek = $this->getWaarden($this->derdePerformanceStart ,$this->rapportageDatum ,$this->portefeuille);


//exit;
    $categorien=array();
    foreach ($indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
        $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
        //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
        foreach ($data['extra']['cat'] as $categorie=>$waarde)
        {
        //  if($categorie=='LIQ')
         //   $categorie='Liquiditeiten';
          
          if($waarde <> 0)
            $categorien[$categorie]=$categorie;
        }
      }
    }
    
    
    foreach ($indexDataGrafiek as $index=>$data)
    {
    //  listarray($data['extra']['cat']);
      if($data['datum'] != '0000-00-00')
      {
        $barGraph['Index'][$data['datum']]['leeg']=0;
        foreach ($data['extra']['cat'] as $categorie=>$waarde)
        {
         // if($categorie=='LIQ')
        //    $categorie='Liquiditeiten';
          if($categorie=='')
            $categorie='leeg';
          
          $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
        }
      }
    }
    
    $i=0;
    $q="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde
FROM
CategorienPerHoofdcategorie
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE
CategorienPerHoofdcategorie.Vermogensbeheerder='$beheerder' AND Beleggingscategorien.Beleggingscategorie IN('".implode("','",$categorien) ."')
GROUP BY Beleggingscategorien.Omschrijving
ORDER BY Beleggingscategorien.Afdrukvolgorde"; //WHERE
    
    $DB->SQL($q);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
      $this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
    }
    
    foreach($categorien as $categorie)
    {
      if(!isset($this->categorieVolgorde[$categorie]))
      {
        $this->categorieVolgorde[$categorie]=$categorie;
        $this->categorieOmschrijving[$categorie]=vertaalTekst($categorie,$this->pdf->rapport_taal);
      }
    }

    $grafiekData['Datum'][]="$RapStartJaar-12-01";
    
    if(count($rendamentWaarden) > 0)
    {
      $n=0;
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);
      
      $totaalRendament=100;
      $totaalRendamentIndex=100;
      foreach ($rendamentWaarden as $row)
      {
        $resultaat = $row['Opbrengsten']-$row['Kosten'];
        $datum = db2jul($row['datum']);
        
        $this->pdf->CellBorders = array();
        $n=fillLine($this->pdf,$n);
        $this->pdf->row(array(ucfirst(vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal)).date(" Y",$datum),
                          $this->formatGetal($row['waardeBegin'],0),
                          $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
                          $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],0),
                          $this->formatGetal($row['opbrengsten'],0),
                          $this->formatGetal($row['kosten'],0),
                          $this->formatGetal($row['rente'],0),
                          $this->formatGetal($row['resultaatVerslagperiode'],0),
                          $this->formatGetal($row['waardeHuidige'],0),
                          $this->formatGetal($row['performance'],2),
                          $this->formatGetal($row['index']-100,2)));
        
        if(!isset($waardeBegin))
          $waardeBegin=$row['waardeBegin'];
        $totaalWaarde = $row['waardeHuidige'];
        $totaalResultaat += $row['resultaatVerslagperiode'];
        $totaalGerealiseerd += $row['gerealiseerd'];
        $totaalOngerealiseerd += $row['ongerealiseerd'];
        $totaalOpbrengsten += $row['opbrengsten'];
        $totaalKosten += $row['kosten'];
        $totaalRente += $row['rente'];
        $totaalStortingenOntrekkingen += $row['stortingen']-$row['onttrekkingen'];
        $totaalRendament = $row['index'];
        
        $i++;
      }
      $this->pdf->fillCell=array();
      $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS');
      $this->pdf->row(array('','','','','','','','','','','',''));
      $this->pdf->SetY($this->pdf->GetY()-4);
      $this->pdf->ln(3);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array();
      $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
                        $this->formatGetal($waardeBegin,0),
                        $this->formatGetal($totaalStortingenOntrekkingen,0),
                        $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd,0),
                        $this->formatGetal($totaalOpbrengsten,0),
                        $this->formatGetal($totaalKosten,0),
                        $this->formatGetal($totaalRente,0),
                        $this->formatGetal($totaalResultaat,0),
                        $this->formatGetal($totaalWaarde,0),
                        '',
                        $this->formatGetal($totaalRendament-100,2)
                      ));//$this->formatGetal($totaalRendamentIndex-100,2)
      $this->pdf->CellBorders = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
    }
    
    $yBegin=100;
    if($this->pdf->GetY() > 102)
    {
      $this->pdf->AddPage();
      //   $yBegin=$this->pdf->GetY()+10;
    }

    if (count($barGraph) > 0)
    {
      $this->pdf->SetXY(160,$yBegin+2)		;//112
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling per maandultimo',$this->pdf->rapport_taal), 0, 1);
      $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
      $this->pdf->SetXY(160,$yBegin+80)		;//112
      $this->VBarDiagram(100, 70, $barGraph['Index'],'');
      //$this->areaDiagram(100, 70, $barGraph['Index']);
      
    }

    if (count($grafiekData) > 1)
    {
      $this->pdf->SetXY(10,$yBegin+2);//104
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(0, 5, vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.
                        vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.
                        vertaalTekst('in',$this->pdf->rapport_taal).' %)', 0, 1);
      $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
      $this->pdf->SetXY(14,$yBegin+10)		;//112
      //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)

      $this->LineDiagram(125, 70, $grafiekData,$this->pdf->rapport_grafiek_color,0,0,6,5,1);//50
    }
    $this->pdf->SetXY(8, 155);//165
    
    $this->pdf->ln(10);
    $this->pdf->SetX(108);
    $this->pdf->MultiCell(170,4,$titel,0,'L');
    $this->pdf->SetX(108);
    $this->pdf->fillCell = array();
    
    
    
  }
  
 
  
  
  function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='')
  {
    $julBegin = db2jul($datumBegin);
    $julEind = db2jul($datumEind);
    
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    
    $ready = false;
    $i=0;
    $vorigeIndex = 100;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    $datum == array();
    
    while ($ready == false)
    {
      if (mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar) > $stop)
      {
        $ready = true;
      }
      else
      {
        if($i==0)
          $datum[$i]['start']=$datumBegin;
        else
        {
          $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
        }
        $datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
        $i++;
      }
    }
    if($i==0)
      $datum[$i]['start']=$datumBegin;
    else
      $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
    $datum[$i]['stop']=$datumEind;
    
    $i=1;
    $indexData['index']=100;
    $db=new DB();
    foreach ($datum as $periode)
    {
      /*
      if(db2jul($periode['start'])<db2jul($this->pdf->PortefeuilleStartdatum) && db2jul($periode['stop'])>db2jul($this->pdf->PortefeuilleStartdatum))
        $periode['start']=date('Y-m-d',db2jul($this->pdf->PortefeuilleStartdatum)+86400);
      
      if(db2jul($periode['start'])==db2jul($this->pdf->PortefeuilleStartdatum))
        $periode['start']=date('Y-m-d',db2jul($this->pdf->PortefeuilleStartdatum)+86400);
      listarray($periode);
      */
      $indexData = array_merge($indexData,$this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille));
      $indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
      $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
      $data[$i] = $indexData;
      $i++;
    }
    return $data;
  }
  
  
  
  function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
  {
    $totaalWaarde =array();
    $db = new DB();
    
    if(db2jul($beginDatum) < db2jul($this->pdf->PortefeuilleStartdatum))
      $wegingsDatum=$this->pdf->PortefeuilleStartdatum;
    else
      $wegingsDatum=$beginDatum;
    
    $startjaar=substr($beginDatum,0,4);
    if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
      $beginjaar = true;
    else
      $beginjaar = false;
    
    $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);
    
    $fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);
    
    foreach ($fondswaarden['beginmaand'] as $regel)
    {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    
    
    
    $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;
    
    foreach ($fondswaarden['eindmaand'] as $regel)
    {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
  
      if($regel['hoofdcategorie']=='')
        $regel['hoofdcategorie']='geen-hcat';
      $categorieVerdeling[$regel['hoofdcategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
      
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
    }
    
    $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
    $DB=new DB();
    
    $query = "SELECT ".
      "SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
      "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
      "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
      "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
      "FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
      "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();
    
    if($totaalWaarde['begin']==0)
      $gemiddelde = $totaalWaarde['begin'] + $weging['totaal2'];
    else
      $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
    $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
    //echo "ATT $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100; <br>\n";
    
    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
    $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
    $onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    
    $query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();
    
    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;
    
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']=array('cat'=>$categorieVerdeling);
    return $data;
    
  }
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $legendDatum= $data['Datum'];
    $data1 = $data['Index1'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );
    
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }
    
    $this->pdf->Rect($XPage, $YPage, $w, $h,'FD','',$this->pdf->grafiekAchtergrondKleur);
    
    if($color == null)
      $color=array(140,178,209);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }
    
    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $aantalData=count($data);
    $unit = $lDiag / $aantalData;
    
    if($jaar && count($data)<12)
      $unit = $lDiag / 12;
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = round(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
      
      $n++;
      if($n >20)
        break;
    }
    
    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
    // $color=array(200,0,0);
    
    $printLabel=array();
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      
      $maand=date("n",db2jul($legendDatum[$i]));
      if($aantalData <= 13 || $maand==3 || $maand==6 || $maand==9 || $maand==12)
      {
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-3+$unit,$YDiag+$hDiag+8,vertaalTekst($__appvar["Maanden"][$maand],$this->pdf->rapport_taal) ,25);
        $printLabel[$i]=1;
      }
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      $this->pdf->Circle($XDiag+($i+1)*$unit+$extrax, $yval2, 1,0,360,'F','',$color);
      $yval = $yval2;
    }
    
    $this->pdf->setTextColor($color[0],$color[1],$color[2]);
    $yTekstStap=2.5;
    for ($i=0; $i<count($data); $i++)
    {
      if($data[$i]>$data1[$i])
        $yOffset=$yTekstStap*-1;
      else
        $yOffset=3+$yTekstStap;
      
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 9);
      if($data[$i] <> 0 && $printLabel[$i])
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2+$yOffset,$this->formatGetal($data[$i],1));
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      
    }
    $this->pdf->setTextColor(0);
    
    
    if(is_array($data1))
    {
      $this->pdf->setTextColor($color1[0],$color1[1],$color1[2]);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 1.0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        if($data1[$i]>$data[$i])
          $yOffset=$yTekstStap*-1;
        else
          $yOffset=3+$yTekstStap;
        
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        
        $this->pdf->SetFont($this->pdf->rapport_font, '', 9);
        
        if($data1[$i] <> 0 && $printLabel[$i])
          $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2+$yOffset,$this->formatGetal($data1[$i],1));
        $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
        
        $yval = $yval2;
      }
      $this->pdf->setTextColor(0);
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.1, ));
    $this->pdf->SetFillColor(0,0,0);
  }
  
  function VBarDiagram($w, $h, $data)
  {
    global $__appvar;
    $legendaWidth = 00;
    $grafiekPunt = array();
    $verwijder=array();
    $laatsteWaarden=array();
    foreach ($data as $datum=>$waarden)
    {
      $legenda[$datum] = jul2form(db2jul($datum));
      $n=0;
      $minVal=0;
      $maxVal=100;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        //foreach ($waarden as $categorie=>$waarde)
        //{
        
        $laatsteWaarden[$categorie]=$waarden[$categorie];
        
        if($categorie=='LIQ')
          $categorie='Liquiditeiten';
        $grafiek[$datum][$categorie]=$waarden[$categorie];
        $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
        $categorien[$categorie] = $n;
        $categorieId[$n]=$categorie ;
        
        $maxVal=max(array($maxVal,$waarden[$categorie]));
        $minVal=min(array($minVal,$waarden[$categorie]));
        
        if($waarden[$categorie] < 0)
        {
          unset($grafiek[$datum][$categorie]);
          $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
        }
        else
          $grafiekNegatief[$datum][$categorie]=0;
        
        
        if(!isset($colors[$categorie]))
          $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        $n++;
      }
    }
    
    
    
    $numBars = count($legenda);
    $numBars=10;
    
    if($color == null)
    {
      $color=array(155,155,155);
    }
    
    
    if($maxVal <= 100)
      $maxVal=100;
    elseif($maxVal < 125)
      $maxVal=125;
    
    if($minVal >= 0)
      $minVal = 0;
    elseif($minVal > -25)
      $minVal=-25;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda
    
    $n=0;
    foreach (($this->categorieVolgorde) as $categorie)//array_reverse
    {
      if(is_array($grafiekCategorie[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
        $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie]." (".$this->formatGetal($laatsteWaarden[$categorie],1)."%)",0,'L');
        $n++;
      }
    }
    
    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / $maxVal * -1;
      $nulYpos =0;
    }
    
    
    $horDiv = 5;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
    $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
    $eBaton = ($vBar * 50 / 100);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    
    foreach ($grafiek as $datum=>$data)
    {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        /*
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
       $this->pdf->SetTextColor(0,0,0);
*/
        
        
        if($legendaPrinted[$datum] != 1)
          $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,date('d-y',db2jul($legenda[$datum])),0);
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$datum] = 1;
      }
      $i++;
    }
    
    $i=0;
    $YstartGrafiekLast=array();
    foreach ($grafiekNegatief as $datum=>$data)
    {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
      }
      $i++;
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
}
?>