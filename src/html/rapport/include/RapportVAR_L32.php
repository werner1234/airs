<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/10/24 16:00:59 $
File Versie					: $Revision: 1.3 $

$Log: RapportVAR_L32.php,v $
Revision 1.3  2018/10/24 16:00:59  rvv
*** empty log message ***

Revision 1.2  2018/10/04 06:02:19  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:43:35  rvv
*** empty log message ***

Revision 1.8  2017/10/25 15:59:31  rvv
*** empty log message ***

Revision 1.7  2017/07/19 19:30:24  rvv
*** empty log message ***

Revision 1.6  2017/07/05 16:06:40  rvv
*** empty log message ***

Revision 1.5  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.4  2017/06/10 18:09:58  rvv
*** empty log message ***

Revision 1.3  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.2  2017/05/17 15:57:50  rvv
*** empty log message ***

Revision 1.1  2017/05/13 16:27:35  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.8  2013/11/23 17:23:24  rvv
*** empty log message ***

Revision 1.7  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.6  2012/10/21 12:44:08  rvv
*** empty log message ***

Revision 1.5  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.4  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.3  2012/09/19 16:53:18  rvv
*** empty log message ***

Revision 1.2  2012/09/13 15:58:37  rvv
*** empty log message ***

Revision 1.5  2012/08/11 13:17:53  rvv
*** empty log message ***

Revision 1.4  2012/07/11 11:33:23  rvv
*** empty log message ***

Revision 1.3  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.2  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportVAR_L32
{
  function RapportVAR_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "VAR";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Resultaat lopend jaar";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    
    $this->rapportageDatum = $rapportageDatum;
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $RapStopJaar = date("Y", db2jul($this->rapportageDatum));
    
    $this->tweedeStart();
    
    
    $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
    
    if ($RapStartJaar != $RapStopJaar)
    {
      echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
      exit;
    }
  }
  
  function tweedeStart()
  {
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
    {
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    }
    else
    {
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
      if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
      {
        $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
        vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
        $this->extraVulling = true;
      }
    }
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


//listarray($this->categorieVolgorde);
    // voor data
    $this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
    $this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');
    
    
    $this->pdf->widthB = array(1,95,30,10,30,115);
    $this->pdf->alignB = array('L','L','R','R','R');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->AddPage();
    $this->pdf->templateVars['VARPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['VARPaginas']=$this->pdf->rapport_titel;
    
    $posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
    $posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    
    
    $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

//  $indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
//listarray($indexData);
//exit;
    $i=0;
    foreach ($indexData as $index=>$data)
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
    }
    
    
    $q="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving as Omschrijving,beleggingscategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND Beleggingscategorie <>'' GROUP BY Beleggingscategorie  ORDER BY beleggingscategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
    $DB->SQL($q);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
      $this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
    }
    $this->categorieVolgorde['Liquiditeiten']='Liquiditeiten';
    $this->categorieOmschrijving['Liquiditeiten']=vertaalTekst('Liquiditeiten',$this->pdf->rapport_taal);
    
    $grafiekData['Datum'][]="$RapStartJaar-12-01";
    
    if(count($rendamentWaarden) > 0)
    {
      $n=1;
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
      $this->pdf->underlinePercentage=0.8;
      
      $this->pdf->SetFillColor(230,230,230);
      //$this->pdf->SetFillColor(200,240,255);
      
      // $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);
      
      
      $totaalRendament=100;
      $totaalRendamentIndex=100;
      foreach ($rendamentWaarden as $row)
      {
        //listarray($row);
        $resultaat = $row['Opbrengsten']-$row['Kosten'];
        $datum = db2jul($row['datum']);
        
        if($fill==true)
        {
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
          $fill=false;
        }
        else
        {
          $this->pdf->fillCell=array();
          $fill=true;
        }
        
        $this->pdf->CellBorders = array();
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
                          $this->formatGetal($row['waardeBegin'],0),'',
                          $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),'',
                          $this->formatGetal($row['resultaatVerslagperiode'],0),'',
                          $this->formatGetal($row['waardeHuidige'],0)));
        
        
        
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
        
        $n++;
        $i++;
      }
      $this->pdf->fillCell=array();
      
      
      
      $this->pdf->CellBorders = array('','TS','','TS','','TS','','TS');
      $this->pdf->row(array('','','','','','','',''));
      $this->pdf->SetY($this->pdf->GetY()-4);
      
      
      $this->pdf->ln(3);
      
      //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array();
      $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
                        $this->formatGetal($waardeBegin,0),'',
                        $this->formatGetal($totaalStortingenOntrekkingen,0),'',
                        $this->formatGetal($totaalResultaat,0),'',
                        $this->formatGetal($totaalWaarde,0)
                      ));//$this->formatGetal($totaalRendamentIndex-100,2)
      $this->pdf->CellBorders = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
    }
    


    $this->pdf->fillCell = array();
    
    
    if($this->extraVulling)
    {
      // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
    }
    
  }
  
  
  function formatGetalLength ($getal,$decimaal,$gewensteLengte)
  {
    $lengte = strlen(round($getal));
    if($getal < 0)
      $lengte --;
    $mogelijkeDecimalen = $gewensteLengte - $lengte;
    if($lengte >$gewensteLengte)
      $decimaal = 0;
    elseif ($decimaal > $mogelijkeDecimalen)
      $decimaal = $mogelijkeDecimalen;
    return number_format($getal,$decimaal,',','');
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
      
      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['OBL'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
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
    
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
    $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging[totaal2]) / $gemiddelde) * 100;
    
    
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
  
  
}
?>