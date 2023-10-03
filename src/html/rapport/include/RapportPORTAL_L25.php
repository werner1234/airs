<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPORTAL_L25
{
  function RapportPORTAL_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "PORTAL";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }

  function writeRapport()
  {

    $db=new DB();
    $query="SELECT CRM_naw.*, Portefeuilles.* FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.Portefeuille='".mysql_real_escape_string($this->portefeuille)."'";
    $db->SQL($query);
    $clientData=$db->lookupRecord();
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+1);
    $this->pdf->addPage('P');
    $this->pdf->ln(20);
  
    $this->pdf->SetWidths(array(10,180));
    $this->pdf->SetAligns(array('L','L'));
  
    $this->pdf->ln();
    $this->pdf->row(array('',$clientData['naam']));
    if($clientData['naam1']<>'')
      $this->pdf->row(array('',$clientData['naam1']));
    $this->pdf->row(array('',$clientData['verzendAdres']));
    $this->pdf->row(array('',$clientData['verzendPc'].' '.$clientData['verzendAdres']));
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->row(array('','Maastricht, '.date('d-m-Y')));
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->row(array('','Geachte, '.$clientData['verzendAanhef'].','));
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->row(array('',"In het Financial Investment Plan met u afgestemd op ".$clientData['CRMReviewDatum']." (hierna: FIP) is de vermogensbeheer relatie tussen u en Auréus vastgelegd. Tijdens de opmaak van het FIP heeft een inventarisatie plaatsgevonden met als doel uw beleggingsportefeuille aan te sluiten bij uw persoonlijke situatie en wensen. In het kader van onze dienstverlening, en de bijhorende zorgplicht, willen wij u met dit schrijven informeren over de ontwikkeling van uw beleggingsportefeuille ten opzichte van de in het FIP vastgestelde geschiktheid en doelstellingen.
Wij tonen u de door u afgegeven doelstellingen alsmede de kans om die doelstelling op termijn te realiseren. In dit document vatten wij, door middel van een aantal belangrijke parameters, de conclusie van het FIP nog eens voor u samen:

In het FIP en het Beleggingsvoorstel is de vermogensbeheer relatie tussen u en Auréus vastgesteld. Op basis van

-> Grondige inventarisatie van uw persoonlijke en financiële situatie;
-> De risico’s die u wenst te lopen bij beleggen;
-> De risico’s die u, op basis van een kwantitatieve analyse van een aantal inputvariabelen,
    kunt lopen

is voor u het beleggingsprofiel vastgesteld. De portefeuille die wij voor u beleggen wordt conform dit vastgestelde beleggingsprofiel belegd.

"));
  
    $this->pdf->row(array('','Huidig beleggingsprofiel: '.$clientData['Risicoklasse'].''));
    $this->pdf->row(array('','Huidig beleggingsfilosofie: '.str_replace($clientData['Risicoklasse'],'',$clientData['ModelPortefeuille']).''));
    $this->pdf->ln();
    $this->pdf->ln();
    $this->pdf->row(array('','In het FIP is een scenario-analyse opgenomen. Hieronder treft u een update aan van de scenario-analyse op basis van uw huidige portefeuille en beleggingsprofiel.'));
    
  //  $this->pdf->rapport_type = "SCENARIO";
    $this->pdf->ln();
    $this->pdf->ln();
    $this->scenarioData();
  //  $this->pdf->rapport_type = "PORTAL";
    $this->pdf->ln();
    $this->pdf->row(array('',$this->pdf->portefeuilledata['AccountmanagerNaam']));
    
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  function formatGetalNegatief($waarde, $dec=0)
  {
    if($waarde<0)
      return 'Negatief!';
    else
      return number_format($waarde,$dec,",",".");
  }
  
  function scenarioData()
  {
    $DB = new DB();
    global $__appvar;
    
    $this->pdf->widthA = array(40,30,20);
    $this->pdf->alignA = array('L','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
//    $this->pdf->AddPage('P');
//    $this->pdf->setY(50);
    $y=$this->pdf->getY();
    
    $query="SELECT max(check_module_SCENARIO) as check_module_SCENARIO FROM Vermogensbeheerders";
    $DB->SQL($query);
    $DB->Query();
    $check_module_SCENARIO = $DB->nextRecord();
    if($check_module_SCENARIO['check_module_SCENARIO'] < 1)
    {
      echo "Scenario-analyse module niet geactiveerd.";
      exit;
    }
    
    if(!isset($this->crmId))
    {
      $query="SELECT id FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
      $DB->SQL($query);
      $DB->Query();
      $crmId = $DB->nextRecord();
    }
    else
      $crmId['id']=$this->crmId;
    
    $sc= new scenarioBerekening($crmId['id']);
    //if($this->pdf->lastPOST['scenario_portefeuilleWaardeGebruik']==1 )
    //{
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    if($totaalWaarde==0 && $this->totaalWaarde <> 0)
    {
      $totaalWaarde = $this->totaalWaarde;
    }
    
    $sc->CRMdata['startvermogen']=$totaalWaarde;
    $sc->CRMdata['startdatum']=$this->rapportageDatum;
    //}
    
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $sc->ophalenHistorie($this->portefeuille);
    }
    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
    //
    
 //   $this->pdf->setY(75);
//    if($this->pdf->portefeuilledata['Layout']==5)
//    {
    $sc->overigeRisicoklassen();
    /*
    $this->pdf->widthA = array(175,30);
    $this->pdf->widthB = array(267-130,30,25,25,25,25);
    $this->pdf->alignB = array('L','L','R','R','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',
                      vertaalTekst('Risicoprofiel',$this->pdf->rapport_taal),
                      vertaalTekst('Kans op doel',$this->pdf->rapport_taal),
                      vertaalTekst('Pessimistisch',$this->pdf->rapport_taal),
                      vertaalTekst('Normaal',$this->pdf->rapport_taal),
                      vertaalTekst('Optimistisch',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    */
    $negatiefAdvies=true;
    $maxKansTmp=0;
    $kansData=$sc->berekenKansBijOpgehaaldeRisicoklassen();
    /*
    foreach($kansData['risicoklassen'] as $risicoklasse=>$klasseData)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->row(array('',"(".$klasseData['risicoklasseData']['afkorting'].")"));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Ln(-4);
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->row(array('',vertaalTekst($risicoklasse,$this->pdf->rapport_taal),
                        $this->formatGetal($klasseData['uitkomstKans']['kans'],0).'%',
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Pessimistisch']),
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Normaal']),
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Optimistisch'])));
      
    }
    */
    $grafiekData=$kansData['grafiekData'];
    //listarray($kansData);
    if(count($kansData['beste'])>0)
    {
      $besteProfiel=$kansData['beste'];
      $negatiefAdvies=false;
    }
    else
    {
      $besteProfiel=$kansData['maxKans'];
    }
    
    $gewenstCrmRisicoprofiel=$this->pdf->portefeuilledata['Risicoklasse'];//$sc->CRMdata['gewenstRisicoprofiel'];
    if($sc->profieldata['ScenarioGewenstProfiel']==1 && $gewenstCrmRisicoprofiel <> '')
    {
      $besteProfiel=array('risicoklasse'=>$gewenstCrmRisicoprofiel,'scenario'=>$gewenstCrmRisicoprofiel);
    }
    if($this->gewenstRisicoprofiel<>'')
      $besteProfiel['risicoklasse']=$this->gewenstRisicoprofiel;
    
    /*
    $this->pdf->setXY(160,120);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(120,0,vertaalTekst('Kans op behalen doelstelling bij diverse profielen',$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->setXY(160,125);
    $this->scatterplot(120,50,$grafiekData,$sc->profieldata['maximaalRisicoprofielStdev'],$besteProfiel);
    */
    $sc= new scenarioBerekening($crmId['id'],$besteProfiel['risicoklasse']);
    //if($this->pdf->lastPOST['scenario_portefeuilleWaardeGebruik']==1 )
    //{
    $sc->CRMdata['startvermogen']=$totaalWaarde;
    $sc->CRMdata['startdatum']=$this->rapportageDatum;
    //}
    
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $sc->ophalenHistorie($this->portefeuille);
    }
    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
//
    $this->pdf->widthA = array(10,40,30,20);
    $this->pdf->alignA = array('L','L','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  //  $this->pdf->setY(50);
    $this->scenarioKleur=$sc->scenarioKleur;
    $aantalSimulaties=10000;
    $sc->berekenSimulaties(0,$aantalSimulaties);
    $sc->berekenDoelKans();
    $sc->berekenVerdeling();
    
    
    
    /*
    $this->pdf->setXY(20,30);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    if($negatiefAdvies==true)
      $this->pdf->Cell(297-40,5,vertaalTekst('Geen voorgesteld profiel: minimale kans op doelrealisatie moet groter dan',$this->pdf->rapport_taal).' '.round($sc->profieldata['ScenarioMinimaleKans'],2).'% '.vertaalTekst('zijn. Best mogelijk voor te stellen profiel',$this->pdf->rapport_taal).': '.vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal),0,0,'C');
    else
      $this->pdf->Cell(297-40,5,vertaalTekst('Voorgesteld profiel:',$this->pdf->rapport_taal).' '.vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal),0,0,'C');
    */
    //$this->pdf->setXY($this->pdf->marge,40);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Uitgangswaarden',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Beginwaarde',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['startvermogen'])));
    $this->pdf->SetWidths(array(10,50,20,20));
    $this->pdf->row(array('',vertaalTekst('Comfortabel scenariovermogen',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->row(array('',vertaalTekst('Startjaar',$this->pdf->rapport_taal),substr($sc->CRMdata['startdatum'],0,4)));
    $this->pdf->row(array('',vertaalTekst('Doeljaar',$this->pdf->rapport_taal),substr($sc->CRMdata['doeldatum'],0,4)));
    //$this->pdf->row(array(vertaalTekst('Berekend profiel',$this->pdf->rapport_taal),vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal)));
    
    if(($sc->profieldata['ScenarioGewenstProfiel']==1 && $gewenstCrmRisicoprofiel <> '') || $sc->gebruikHandmatigeOpties )
      $this->pdf->row(array('',vertaalTekst('Gewenst profiel',$this->pdf->rapport_taal),vertaalTekst($gewenstCrmRisicoprofiel,$this->pdf->rapport_taal)));
    else
      $this->pdf->row(array('',vertaalTekst('Berekend profiel',$this->pdf->rapport_taal),vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal)));
    
    $this->pdf->row(array('',vertaalTekst('Maximaal risicoprofiel',$this->pdf->rapport_taal),vertaalTekst($sc->CRMdata['maximaalRisicoprofiel'],$this->pdf->rapport_taal)));
    $this->pdf->row(array('',vertaalTekst('Verwacht rendement',$this->pdf->rapport_taal),$this->formatGetal(($sc->profieldata['verwachtRendement']-1)*100,1).'%'));
    $this->pdf->row(array('',vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal),$this->formatGetal($sc->profieldata['klasseStd']*100,1).'%'));
    $this->pdf->Ln();
    $doelkans=$sc->doelKans;
    $eindvermogenNormaal=$kansData['risicoklassen'][$sc->CRMdata['gewenstRisicoprofiel']]['uitkomstKans']['scenarioEindwaarden']['Normaal'];
    

    $this->startJaar=$sc->CRMdata['startdatum'];
    

    /*
    if($sc->vrhMethode >0)
    {
      $this->pdf->setY(40);
      $this->pdf->widthB = array(225,25,10,20);
      $this->pdf->alignB = array('L','L','R','R');
      
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',vertaalTekst('VRH',$this->pdf->rapport_taal),'%',vertaalTekst('Vrijstelling',$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
      $rendementsheffingWaarden=$sc->rendementsheffing[substr($sc->CRMdata['doeldatum'],0,4)];
      if($sc->vrhMethode==1)
        $this->pdf->row(array('',vertaalTekst('Volledig',$this->pdf->rapport_taal),$this->formatGetal($rendementsheffingWaarden['percentage'],1),'0'));
      elseif($sc->vrhMethode==2)
        $this->pdf->row(array('',vertaalTekst('Vrijstelling 1P',$this->pdf->rapport_taal),$this->formatGetal($rendementsheffingWaarden['percentage'],1),$this->formatGetal($rendementsheffingWaarden['vrijstellingEenP'],0)));
      elseif($sc->vrhMethode==3)
        $this->pdf->row(array('',vertaalTekst('Vrijstelling 2P',$this->pdf->rapport_taal),$this->formatGetal($rendementsheffingWaarden['percentage'],1),$this->formatGetal($rendementsheffingWaarden['vrijstellingTweeP'],0)));
    }
    */
    foreach($sc->cashflow as $jaar=>$waarde)
      $cashflow[$jaar]['scenario']=$waarde;
    
    /*
    $this->pdf->setXY(25,160);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(125,0,vertaalTekst('Scenario-analyse',$this->pdf->rapport_taal),0,0,'C');
    */
   // $this->pdf->setXY(25,165);
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $scenarios = array_keys($sc->scenarioGemiddelde);
      $i = 0;
      $laatsteJaar = 2000;
      unset($this->startJaar);
      foreach ($sc->werkelijkVerloop as $jaar => $data)
      {
        $cashflow[$jaar]['werkelijk'] = $data['stortingen'];
        if (!isset($this->startJaar))
        {
          $this->startJaar = $jaar;
        }
        if ($jaar < $sc->CRMdata['startdatum'])
        {
          foreach ($scenarios as $scenario)
          {
            $grafiek[$scenario][] = $sc->CRMdata['startvermogen'];
          }
        }
        else
        {
          foreach ($scenarios as $scenario)
          {
            $grafiek[$scenario][] = $sc->scenarioGemiddelde[$scenario][$i];
          }
          $i++;
        }
        $laatsteJaar = $jaar;
      }
  
      foreach ($sc->scenarioGemiddelde as $scenario => $waarden)
      {
        foreach ($waarden as $index => $waarde)
        {
          if ($sc->CRMdata['startdatum'] + $index > $laatsteJaar)
          {
            $grafiek[$scenario][] = $sc->scenarioGemiddelde[$scenario][$index];
          }
        }
      }
    }

    $this->pdf->setY($y);
    $n=0;
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      ksort($cashflow);
      $this->pdf->widthB = array(100,18,20,20);
      $this->pdf->alignB = array('L','L','L','R','R');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',vertaalTekst('Cashflow',$this->pdf->rapport_taal)));//vertaalTekst('Scenario-analyse',$this->pdf->rapport_taal)
      $this->pdf->row(array('',
                        vertaalTekst('Jaar',$this->pdf->rapport_taal),
                        vertaalTekst('scenario €',$this->pdf->rapport_taal),
                        vertaalTekst('werkelijk €',$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
      foreach($cashflow as $jaar=>$bedragen)
      {
        if($n > 5)
        {
          $cashflowOverig['werkelijk']+=$bedragen['werkelijk'];
          $cashflowOverig['scenario']+=$bedragen['scenario'];
        }
        else
          $this->pdf->row(array('',$jaar,$this->formatGetal($bedragen['scenario']),$this->formatGetal($bedragen['werkelijk'])));
        $n++;
      }
      if(isset($cashflowOverig))
        $this->pdf->row(array('',vertaalTekst('Restant',$this->pdf->rapport_taal),$this->formatGetal($cashflowOverig['scenario']),$this->formatGetal($cashflowOverig['werkelijk'])));
    }
    else
    {
      $indexatie=false;
      foreach($sc->cashflowText as $bedragData)
        if($bedragData[2] <> '')
          $indexatie=true;
      
      if($indexatie)
        $this->pdf->widthB = array(95,18,25,15);
      else
        $this->pdf->widthB = array(95,18,25,2);
      $this->pdf->alignB = array('L','L','R','R','L');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',vertaalTekst('Cashflow',$this->pdf->rapport_taal)));
      
      if($indexatie)
        $this->pdf->row(array('',vertaalTekst('Jaar',$this->pdf->rapport_taal),
                          vertaalTekst('Bedrag in',$this->pdf->rapport_taal).' €'));
      
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($sc->cashflowText as $bedragData)
      {
        $this->pdf->row(array('',$bedragData[0],$this->formatGetal($bedragData[1],0)));
      }
    }

  
    $this->pdf->setY($y+45);
    $this->pdf->widthB = array(10,35,30,40);
    $this->pdf->alignB = array('L','L','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Scenario',$this->pdf->rapport_taal).' '.vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal),
                      vertaalTekst('Kans',$this->pdf->rapport_taal),
                      vertaalTekst('Minimaal eindvermogen',$this->pdf->rapport_taal)));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($sc->verwachteWaarden as $scenario=>$eindvermogen)
    {
      $kleur=$this->scenarioKleur[$scenario];
      $this->pdf->Rect($this->pdf->getX()+50-3,$this->pdf->GetY()+1, 2, 2 ,'F','',$kleur);
      $this->pdf->row(array('',vertaalTekst($scenario,$this->pdf->rapport_taal),$this->formatGetal( round((100-$sc->scenarios[$scenario])/5)*5,0).'%',$this->formatGetalNegatief($eindvermogen)));
    }
  
  
    $this->pdf->addPage('P');
  
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setY(35);
    $this->pdf->widthB = array(10,40,40,10);
    $this->pdf->alignB = array('L','L','L','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
  
    $this->pdf->row(array('',vertaalTekst('Conclusies',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(10,60,20,10));
    $this->pdf->row(array('',vertaalTekst('Doelvermogen',$this->pdf->rapport_taal),$this->formatGetal($doelkans,0).'%'));
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->row(array('',vertaalTekst('Gemiddeld eindvermogen',$this->pdf->rapport_taal),"€ ".$this->formatGetalNegatief($eindvermogenNormaal)));
  
    
    $this->pdf->setXY(30,50);
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
      $this->LineDiagram(110,45,$grafiek,$sc->werkelijkVerloop,$sc->CRMdata['doelvermogen']);
    else
      $this->LineDiagram(110,45,$sc->scenarioGemiddelde,'',$sc->CRMdata['doelvermogen']);
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+1);
    $this->pdf->setY(120);
    $this->pdf->ln();
    $this->pdf->SetWidths(array(10,180));
    $text="Op basis van uw belegde vermogen en uw beleggingspofiel, wordt de kans op het behalen van uw beleggingsdoelstelling van €".$this->formatGetal($sc->CRMdata['doelvermogen'],0).", ingeschat op ".$this->formatGetal($doelkans,0)."%. Het gemiddeld te verwachten vermogen op einddatum bedraagt €".$this->formatGetalNegatief($eindvermogenNormaal).".

Het is van groot belang dat, indien er iets wijzigt in uw persoonlijke situatie of in de situatie van uw juridische entiteit (zogenaamde life events), u ons hiervan op de hoogte stelt, aangezien dit consequenties kan hebben voor de manier waarop wij voor u optimaal beleggen.
Indien uw situatie verandert, of gaat veranderen, neemt u dan tijdig contact op met uw adviseur, om samen te beoordelen of dit gevolgen heeft voor uw beleggingsdoelstellingen of beleggingsprofiel.
Hierbij kunt u denken aan significant anders dan in de scenario analyse weergegeven voornemen om vermogen te storten of te onttrekken. Maar ook veranderingen in uw beleggingsdoelstellingen, beleggingshorizon, risicobereidheid of uw persoonlijke situatie. Wij kunnen dan met u bespreken of onze dienstverlening nog passend is bij uw gewijzigde situatie en wensen.

U ontvangt per kwartaal in uw periodieke rapportage een rapport genaamd Vergelijkende kostenmaatstaf. Dit rapport toont gedetailleerd de kosten van de dienstverlening, zowel cijfermatig als grafisch, gebaseerd op uw portefeuille.

In aanvulling op de eerdere afspraken zoals vastgelegd in het FIP, informeren wij u over het feit dat een tijdelijk debetstand, die kortstondig kan ontstaan als gevolg van een ruiltransactie in de portefeuille, niet wordt gezien als beleggen met geleend geld. Daarnaast kan het ook zijn dat uw beleggingsprofiel tijdelijk wordt overschreven als gevolg van een ruiltransactie, wij beschouwen dit niet als mandaatoverschrijding. Transacties zijn uitgevoerd met inachtneming van de afspraken die zijn vastgelegd in het FIP. Indien gewenst en op uw eerste verzoek zullen wij u eventuele bijzonderheden omtrent uw transacties overleggen.

Mocht u naar aanleiding van dit schrijven meer informatie wensen, dan nodigen wij u van harte uit om in contact te treden met uw adviseur.
";
    $this->pdf->row(array('',$text));
  
  }
  
  
  
  function scatterplot($w, $h, $data,$maxStdev=25,$beste)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=0; $horDiv=4; $verDiv=4;$jaar=0;
    
    $minXVal=0; $maxXVal=25;
    $minYVal=0; $maxYVal=100;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = $h;//floor($h - $margin * 1);
    $XDiag = $XPage;// + $margin * 1 ;
    $lDiag = $w;//floor($w);
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(0,0,0);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    $procentWhiteSpace = 0.10;
    $xband=($maxXVal - $minXVal);
    $yband=($maxYVal - $minYVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / $yband;
    $Xunit = $lDiag / $xband;
    $Yunit = $hDiag / $yband *-1;
    
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    
    $rood=array(180,50,50);
    $groen=array(50,180,50);
    $steps=100;
    $kleurenStap=array(($rood[0]-$groen[0])/$steps,
      ($rood[1]-$groen[1])/$steps,
      ($rood[2]-$groen[2])/$steps);
    
    
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    $factor=0.5;
    for($i=0; $i<= $maxYVal; $i+= 10)
    {
      $kleur=array($rood[0]-($i*$kleurenStap[0]),
        $rood[1]-($i*$kleurenStap[1]),
        $rood[2]-($i*$kleurenStap[2]));
      
      $kleur2=array(($rood[0]-($i*$kleurenStap[0]))*$factor+100,
        ($rood[1]-($i*$kleurenStap[1]))*$factor+100,
        ($rood[2]-($i*$kleurenStap[2]))*$factor+100);
      
      if($i < 100)
      {
        if($maxStdev >0)
        {
          $this->pdf->Rect($XDiag                 , $bodem+$i*$Yunit,$Xunit*$maxStdev    ,$Yunit*10,'F','',$kleur);
          $this->pdf->Rect($XDiag+$Xunit*$maxStdev, $bodem+$i*$Yunit,$w-$Xunit*$maxStdev ,$Yunit*10,'F','',$kleur2);
        }
        else
        {
          $this->pdf->Rect($XDiag, $bodem+$i*$Yunit,$w ,$Yunit*10,'F','',$kleur);
        }
      }
      
      if($maxStdev >0)
      {
        $tekstWidth=($w-$Xunit*$maxStdev);
        if($tekstWidth > 5 && $i==$maxYVal)
        {
          $this->pdf->setXY(($XDiag+$Xunit*$maxStdev),$bodem-$hDiag/2);
          $this->pdf->MultiCell($tekstWidth,2.5,vertaalTekst("Buiten risicotolerantie",$this->pdf->rapport_taal), 0,"C");
        }
      }
      
      //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5, 1, 1 ,'F','',$color);
      $skipNull = true;
      $this->pdf->Line($XDiag, $bodem+$i*$Yunit, $XPage+$w ,$bodem+$i*$Yunit,array('dash' => 1,'color'=>array(0,0,0)));
      
      $this->pdf->setXY($XDiag-20, $bodem+$i*$Yunit);
      $this->pdf->Cell(20,0, $i." %", 0,0, "R");
      //$this->pdf->Text($XDiag-7, $bodem+$i*$Yunit, $i." %");
      $n++;
      if($n >20)
        break;
    }
    $this->pdf->Text($XDiag-7, $bodem+$maxYVal*$Yunit-3, "Kans");
    
    for($i=0; $i<= $maxXVal; $i+= 5)
    {
      $xplot=$XDiag+$i*$Xunit;
      $skipNull = true;
      $this->pdf->Line($xplot, $YDiag, $xplot,$bodem,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($xplot-2, $bodem+3, $i." %");
      $n++;
      if($n >20)
        break;
    }
    $this->pdf->Text($XDiag+$maxXVal/2*$Xunit-8, $bodem+6, vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal));
    
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    
    foreach($data as $reeks=>$waarden)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
      
      if($this->pdf->portefeuilledata['Layout']==70 && $reeks==$beste['scenario'])
        $this->pdf->MemImage(base64_decode($this->vuurtorenIMG), $XDiag+$waarden['x']*$Xunit-1.0,$bodem+$waarden['y']*$Yunit-8.3,2 );
      
      $this->pdf->Rect($XDiag+$waarden['x']*$Xunit-0.5,$bodem+$waarden['y']*$Yunit-0.5, 1, 1 ,'F','',$color);
      $this->pdf->setXY($XDiag+$waarden['x']*$Xunit-5,$bodem+$waarden['y']*$Yunit+2.5);
      $this->pdf->Cell(10,0,$reeks, 0,0, "C");
      
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    return $beste;
  }
  
  
  
  function LineDiagram($w, $h, $data,$werkelijkVerloop,$doelVermogen)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=10000000; $horDiv=5; $verDiv=4;$jaar=0;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage  ;
    $lDiag = $w;
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(116,95,71);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    $aantalPunten=array();
    foreach($data as $reeks=>$waarden)
    {
      $tmp=ceil(max($waarden));
      if($tmp > $maxVal)
        $maxVal = $tmp;
      
      $tmp = floor(min($waarden));
      if($tmp < $minVal)
        $minVal=$tmp;
      
      foreach($waarden as $index=>$waarde)
        $aantalPunten[$index]=$index;
    }
    
    foreach($werkelijkVerloop as $jaar=>$waarden)
    {
      if($waarden['waarde'] > $maxVal)
        $maxVal = $waarden['waarde'];
      
      if($waarden['waarde'] < $minVal)
        $minVal=$waarden['waarde'];
    }
    
    if($minVal < 0)
      $minVal=0;
    
    if ($maxVal < 0)
      $maxVal = 1;
    
    
    $procentWhiteSpace = 0.1;
    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    //echo $band;exit;
    
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))*5))*pow(10,strlen($stepSize))/5;
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - (0.3))/$stepSize)*$stepSize;
    
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / (count($aantalPunten)-1);
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
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
      //$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ."");
      
      $this->pdf->setXY($XDiag-20, $i);
      if($n==0)
        $waarde=$minVal;
      else
        $waarde=0-($n*$stapgrootte);
      
      $this->pdf->Cell(20,0, $this->formatGetal($waarde,0)."", 0,0, "R");
      
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      if($n*$stapgrootte >= $minVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          //  $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ."");
          $this->pdf->setXY($XDiag-20, $i);
          $this->pdf->Cell(20,0, $this->formatGetal($n*$stapgrootte,0)."", 0,0, "R");
        }
      }
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
    $lineStyle = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $circleStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,255,255));
    
    // $color=array(200,0,0);
    $datumPrinted=array();
    $xcorrectie=$unit;
    $data=array_reverse($data);
    $reeksCount=0;
    $lastReeks=count($data)-1;
    $polly=array();
    $pollyReverse=array();
    foreach($data as $reeks=>$waarden)
    {
      $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
      
      $lines[$reeks]=array();
      $marks[$reeks]=array();
      
      //$polly[]=$XDiag;
      //$polly[]=$bodem;
      if(count($waarden)> 20)
        $modi=2;
      else
        $modi=1;
      
      for ($i=0; $i<count($waarden); $i++)
      {
        if($waarden[$i] < 0)
          $waarden[$i]=0;
        
        if(!isset($datumPrinted[$i]))
        {
          if($i%$modi==0)
            $this->pdf->TextWithRotation($XDiag+($i*$unit)-2,$YDiag+$hDiag+8,$this->startJaar+$i,25);
          $datumPrinted[$i]=1;
        }
        
        $yval2 = $YDiag + (($maxVal-$waarden[$i]) * $waardeCorrectie) ;
        
        if($i==0)
        {
          $yval = $bodem ;
        }
        else
        {
          
          //$this->pdf->line($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2,$lineStyle );
          $lines[$reeks][]=array($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2);
          $marks[$reeks][]=array($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5);
          //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5, 1, 1 ,'F','',$color);
          if($reeksCount==0)
          {
            $polly[]=$XDiag+$i*$unit-$xcorrectie;
            $polly[]=$yval;
            $polly[]=$XDiag+($i+1)*$unit-$xcorrectie;
            $polly[]=$yval2;
          }
          elseif($reeksCount==$lastReeks)
          {
            $pollyReverse[]=$yval;
            $pollyReverse[]=$XDiag+$i*$unit-$xcorrectie;
            $pollyReverse[]=$yval2;
            $pollyReverse[]=$XDiag+($i+1)*$unit-$xcorrectie;
            
          }
          
        }
        $yval = $yval2;
      }
      
      $reeksCount++;
      //$polly[]=$XDiag+$w;
      // $polly[]=$bodem;
      //  $this->pdf->Polygon($polly, 'F', null, $color) ;
    }
    $pollyReverse=array_reverse($pollyReverse);
    // listarray($polly);
    foreach($pollyReverse as $value)
      $polly[]=$value;
    // listarray($polly);
    $this->pdf->Polygon($polly, 'F', null, array(200,200,200)) ;
    
    
    foreach($lines as $reeks=>$lineData)
    {
      $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
      $lineStyle = array('width' => 0.8, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($lineData as $line)
      {
        $this->pdf->line($line[0],$line[1],$line[2],$line[3],$lineStyle);
      }
    }
    
    
    
    foreach($marks as $reeks=>$markData)
    {
      foreach($markData as $mark)
      {
        $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
        $r=0.5;
        $this->pdf->Circle($mark[0]+$r,$mark[1]+$r, $r, 0,360, $style = 'DF', $circleStyle, $color);
      }
    }
    
    
    
    
    
    $yval = $YDiag + (($maxVal-$doelVermogen) * $waardeCorrectie) ;
    $xval=$XDiag+(count($waarden))*$unit-0.5-$xcorrectie+$r;
    $circleStyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $this->pdf->Circle($xval,$yval, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    
    $this->pdf->Circle($XDiag,$YDiag+$h+10, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    $this->pdf->TextWithRotation($XDiag+2,$YDiag+$h+10+1,vertaalTekst('Comfortabel scenariovermogen',$this->pdf->rapport_taal),0);
    
    $lineStyle = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $i=0;
    foreach($werkelijkVerloop as $jaar=>$waarden)
    {
      $yval2 = $YDiag + (($maxVal-$waarden['waarde']) * $waardeCorrectie) ;
      if($i==0)
      {
        $yval = $bodem ;
      }
      else
      {
        $this->pdf->line($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2,$lineStyle );
      }
      $yval = $yval2;
      $i++;
    }
    
    
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

}
