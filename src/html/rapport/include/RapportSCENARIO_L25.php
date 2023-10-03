<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/scenarioBerekening.php");
//ini_set('max_execution_time', 20);

class RapportSCENARIO_L25
{
	function RapportSCENARIO_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "SCENARIO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_SCENARIO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_SCENARIO_titel;
		else
			$this->pdf->rapport_titel = "Scenario-analyse";
        $this->pdf->rapport_titel=vertaalTekst($this->pdf->rapport_titel ,$this->pdf->rapport_taal);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->gewenstRisicoprofiel = '';
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
 		return number_format($waarde,$dec,",",".");
	}
  
	function formatGetalNegatief($waarde, $dec)
	{
	  if($waarde<0)
      return 'Negatief!';
    else  
 		  return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;

		$this->pdf->widthA = array(40,30,20);
		$this->pdf->alignA = array('L','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->baseY=30;
    $this->pdf->setY($this->baseY);
 
    
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

    $sc= new scenarioBerekening($crmId['id'],$this->gewenstRisicoprofiel);

    if($this->pdf->lastPOST['scenario_portefeuilleWaardeGebruik']==1 )
    {
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
    }

    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $sc->ophalenHistorie($this->portefeuille);
    }
    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
    //
    
     $this->pdf->setY($this->baseY+30);
//    if($this->pdf->portefeuilledata['Layout']==5)
//    {
      $sc->overigeRisicoklassen();
      $this->pdf->widthA = array(185,30);
		  $this->pdf->widthB = array(150,30,25,25,25,25);
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
      $negatiefAdvies=true;
      $maxKansTmp=0;
      $kansData=$sc->berekenKansBijOpgehaaldeRisicoklassen();
      foreach($kansData['risicoklassen'] as $risicoklasse=>$klasseData)
      {
        if($risicoklasse=='Gematigd Neutraal Beheer' || $risicoklasse=='Gematigd Offensief Beheer')
          $ster='*';
        else
          $ster='';
        /*
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->SetWidths($this->pdf->widthA);
        $this->pdf->row(array('', "(" . $klasseData['risicoklasseData']['afkorting'] . ")"));
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->Ln(-4);
        $this->pdf->SetWidths($this->pdf->widthB);
        */
        $this->pdf->row(array('', vertaalTekst(str_replace(' Beheer', '', $risicoklasse), $this->pdf->rapport_taal) . $ster,
                          $this->formatGetal($klasseData['uitkomstKans']['kans'], 0) . '%',
                          $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Pessimistisch']),
                          $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Normaal']),
                          $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Optimistisch'])));
      }
      $this->pdf->SetWidths(array($this->pdf->widthB[0],100));
      $this->pdf->row(array('','* Deze profielen zijn alleen bij de markt/factor benadering mogelijk'));
      
      //$grafiekData=$kansData['grafiekData'];
     
      $grafiekData=array();
      $grafiekScenarios=array('Pessimistisch','Normaal','Optimistisch');
      $kleuren=array();
      foreach($kansData['risicoklassen'] as $risicoKlasse=>$klasseData)
      {
        foreach($klasseData['uitkomstKans']['scenarioEindwaarden'] as $scen=>$waarde)
        {
          if(in_array($scen,$grafiekScenarios))
          {
            $grafiekData[$risicoKlasse]['staven'][$scen] = $waarde;
            $kleuren[$scen]=$sc->scenarioKleur[$scen];
          }
        }
        $grafiekData[$risicoKlasse]['kans']=$klasseData['uitkomstKans']['kans'];
      }
    
      if(count($kansData['beste'])>0)
      {
        $besteProfiel=$kansData['beste'];
        $negatiefAdvies=false;
      }
      else
      {
        $besteProfiel=$kansData['maxKans'];
      }

    $gewenstCrmRisicoprofiel=$sc->CRMdata['gewenstRisicoprofiel'];
    if($sc->profieldata['ScenarioGewenstProfiel']==1 && $gewenstCrmRisicoprofiel <> '')
    {
      $besteProfiel=array('risicoklasse'=>$gewenstCrmRisicoprofiel,'scenario'=>$gewenstCrmRisicoprofiel);
    }
      
    $this->pdf->setXY(155,$this->baseY+85);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(130,0,vertaalTekst('Kans op behalen doelstelling bij diverse profielen',$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->setXY(155,$this->baseY+140);
    $this->pdf->AutoPageBreak=false;
    $this->VBarDiagram(125,50,$grafiekData,'',$kleuren,$sc->CRMdata['doelvermogen']);
    $this->pdf->AutoPageBreak=true;
    if($this->gewenstRisicoprofiel<>'')
       $besteProfiel['risicoklasse']=$this->gewenstRisicoprofiel;

    $sc= new scenarioBerekening($crmId['id'],$besteProfiel['risicoklasse']);
    if($this->pdf->lastPOST['scenario_portefeuilleWaardeGebruik']==1 )
    {
      $sc->CRMdata['startvermogen']=$totaalWaarde;
      $sc->CRMdata['startdatum']=$this->rapportageDatum;
    }
    
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $sc->ophalenHistorie($this->portefeuille);
    }
    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
//
   	$this->pdf->widthA = array(40,30,20);
		$this->pdf->alignA = array('L','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setY($this->baseY);
    $this->scenarioKleur=$sc->scenarioKleur;
    $aantalSimulaties=10000;
    $sc->berekenSimulaties(0,$aantalSimulaties);
    $sc->berekenDoelKans();
    $sc->berekenVerdeling();
    
    
    $this->pdf->setXY($this->pdf->marge,$this->baseY);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Uitgangswaarden',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);  
    $this->pdf->row(array(vertaalTekst('Beginwaarde',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['startvermogen'])));
    $this->pdf->row(array(vertaalTekst('Doelvermogen',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    $this->pdf->row(array(vertaalTekst('Startjaar',$this->pdf->rapport_taal),substr($sc->CRMdata['startdatum'],0,4)));
    $this->pdf->row(array(vertaalTekst('Doeljaar',$this->pdf->rapport_taal),substr($sc->CRMdata['doeldatum'],0,4)));
    $this->pdf->row(array(vertaalTekst('Maximaal risicoprofiel',$this->pdf->rapport_taal),vertaalTekst(str_replace(' Beheer','',$sc->CRMdata['maximaalRisicoprofiel']),$this->pdf->rapport_taal)));
    if(($sc->profieldata['ScenarioGewenstProfiel']==1 && $gewenstCrmRisicoprofiel <> '') || $sc->gebruikHandmatigeOpties )
      $this->pdf->row(array(vertaalTekst('Gewenst risicoprofiel',$this->pdf->rapport_taal),vertaalTekst(str_replace(' Beheer','',$gewenstCrmRisicoprofiel),$this->pdf->rapport_taal)));
    else
      $this->pdf->row(array(vertaalTekst('Berekend risicoprofiel',$this->pdf->rapport_taal),vertaalTekst(str_replace(' Beheer','',$sc->CRMdata['gewenstRisicoprofiel']),$this->pdf->rapport_taal)));
    $this->pdf->row(array(vertaalTekst('Verwacht netto rendement',$this->pdf->rapport_taal),$this->formatGetal(($sc->profieldata['verwachtRendement']-1)*100,1).'%'));
    $this->pdf->row(array(vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal),$this->formatGetal($sc->profieldata['klasseStd']*100,1).'%'));
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setY($this->baseY);
    $this->pdf->widthB = array(150,60,30,20);
		$this->pdf->alignB = array('L','L','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->row(array('',vertaalTekst('Conclusies',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Kans op doelvermogen bij',$this->pdf->rapport_taal).' '.str_replace('','',$sc->CRMdata['gewenstRisicoprofiel']),$this->formatGetal($sc->doelKans,0).'%'));
    $verwachtEindvermogen=$kansData['risicoklassen'][$sc->CRMdata['gewenstRisicoprofiel']]['uitkomstKans']['scenarioEindwaarden']['Normaal'];
    $this->pdf->row(array('',vertaalTekst('Gemiddeld eindvermogen',$this->pdf->rapport_taal),"€ ".$this->formatGetalNegatief($verwachtEindvermogen)));

    if($this->pdf->lastPOST['scenario_inflatie']==1)
    {
      $verwachtVermogenMetInflatie = $verwachtEindvermogen / $sc->inflatieCorrectie;
      $this->pdf->row(array('', vertaalTekst('Met inflatiecorrectie', $this->pdf->rapport_taal), "€ " . $this->formatGetalNegatief($verwachtVermogenMetInflatie)));
    }
    if(isset($sc->rendementsverloopTxt[$sc->CRMdata['gewenstRisicoprofiel']]) && count($sc->rendementsverloopTxt[$sc->CRMdata['gewenstRisicoprofiel']])>0)
    {
      $this->pdf->SetWidths(array(150,20,40,40));
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('','Periode','Verwacht rendement','Standaarddeviatie'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach ($sc->rendementsverloopTxt[$sc->CRMdata['gewenstRisicoprofiel']] as $regel)
        $this->pdf->row(array('',$regel['txt'],$this->formatGetal($regel['verwachtRendement'],1).'%',$this->formatGetal($regel['standaarddeviatie'],1).'%'));
    }
    $this->startJaar=$sc->CRMdata['startdatum'];
    
    if($sc->vrhMethode >0)
    {
      $this->pdf->setY($this->baseY);
      $this->pdf->widthB = array(225,30,25);
		  $this->pdf->alignB = array('L','L','R');

      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',vertaalTekst('VRH',$this->pdf->rapport_taal),vertaalTekst('Vrijstelling',$this->pdf->rapport_taal)));//'%'
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      $doelJaar=substr($sc->CRMdata['doeldatum'],0,4);
      $rendementsheffingWaarden=$sc->rendementsheffing[$doelJaar];
      if($sc->vrhMethode==1)
        $this->pdf->row(array('',vertaalTekst('Volledig',$this->pdf->rapport_taal),'0'));
      elseif($sc->vrhMethode==2)
        $this->pdf->row(array('',vertaalTekst('Vrijstelling 1P',$this->pdf->rapport_taal)." $doelJaar",$this->formatGetal($rendementsheffingWaarden['vrijstellingEenP'],0)));
      elseif($sc->vrhMethode==3)
        $this->pdf->row(array('',vertaalTekst('Vrijstelling 2P',$this->pdf->rapport_taal)." $doelJaar",$this->formatGetal($rendementsheffingWaarden['vrijstellingTweeP'],0)));
    }

    foreach($sc->cashflow as $jaar=>$waarde)
      $cashflow[$jaar]['scenario']=$waarde;
    
    $this->pdf->setXY(20,$this->baseY+85);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(110,0,vertaalTekst('Scenario-analyse',$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->setXY(20,$this->baseY+90);
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $scenarios=array_keys($sc->scenarioGemiddelde);
      $i=0;
      $laatsteJaar=2000;
      unset($this->startJaar);
      foreach($sc->werkelijkVerloop as $jaar=>$data)
      {
        $cashflow[$jaar]['werkelijk']=$data['stortingen'];
        if(!isset($this->startJaar))
          $this->startJaar=$jaar;
        if($jaar<$sc->CRMdata['startdatum'])
        {
          foreach($scenarios as $scenario)
            $grafiek[$scenario][]=$sc->CRMdata['startvermogen'];
        }
        else
        {
          foreach($scenarios as $scenario)
            $grafiek[$scenario][]=$sc->scenarioGemiddelde[$scenario][$i];
          $i++;
        }
        $laatsteJaar=$jaar;
      }

      foreach($sc->scenarioGemiddelde as $scenario=>$waarden)
      {
        foreach($waarden as $index=>$waarde)
        {
          if($sc->CRMdata['startdatum']+$index > $laatsteJaar)
            $grafiek[$scenario][]=$sc->scenarioGemiddelde[$scenario][$index];
        }
      }
      $this->LineDiagram(110,50,$grafiek,$sc->werkelijkVerloop,$sc->CRMdata['doelvermogen']);
    }
    else
      $this->LineDiagram(110,50,$sc->scenarioGemiddelde,'',$sc->CRMdata['doelvermogen']);


     $this->pdf->setY($this->baseY);
    $n=0; 
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    { 
      ksort($cashflow);
      $this->pdf->widthB = array(80,20,20,20);
		  $this->pdf->alignB = array('L','L','R','R');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',vertaalTekst('Cashflow',$this->pdf->rapport_taal)));//vertaalTekst('Scenario-analyse',$this->pdf->rapport_taal)
      $this->pdf->row(array('',
                            vertaalTekst('Jaar',$this->pdf->rapport_taal),
                            vertaalTekst('werkelijk €',$this->pdf->rapport_taal),
                            vertaalTekst('scenario €',$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $cashflowOverig=array();
      foreach($cashflow as $jaar=>$bedragen)
      {
        if($n > 5)
        {
          $cashflowOverig['werkelijk']+=$bedragen['werkelijk'];
          $cashflowOverig['scenario']+=$bedragen['scenario'];
        }
        else
          $this->pdf->row(array('',$jaar,$this->formatGetal($bedragen['werkelijk']),$this->formatGetal($bedragen['scenario'])));
        $n++;
      }
      if(isset($cashflowOverig['werkelijk']))
        $this->pdf->row(array('',vertaalTekst('Restant',$this->pdf->rapport_taal),$this->formatGetal($cashflowOverig['werkelijk']),$this->formatGetal($cashflowOverig['scenario'])));
    }
    else
    {
      $indexatie=false;
      foreach($sc->cashflowText as $bedragData)
        if($bedragData[2] <> '')
          $indexatie=true;
          
      if($indexatie)    
        $this->pdf->widthB = array(75,20,25,15);
      else
        $this->pdf->widthB = array(90,20,25,2);  
		  $this->pdf->alignB = array('L','L','R','R','L');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',vertaalTekst('Cashflow',$this->pdf->rapport_taal)));
     
      if($indexatie)
        $this->pdf->row(array('',vertaalTekst('Jaar',$this->pdf->rapport_taal),
                                 vertaalTekst('Bedrag in',$this->pdf->rapport_taal).' €',
                                 vertaalTekst('Index%',$this->pdf->rapport_taal)));
        
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($sc->cashflowText as $bedragData)
      {
          $this->pdf->row(array('',$bedragData[0],$this->formatGetal($bedragData[1],0),$bedragData[2]));
      }
    }
    
  
    $this->pdf->setY($this->baseY+40);
		$this->pdf->widthB = array(40,30,30);
		$this->pdf->alignB = array('L','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Scenario',$this->pdf->rapport_taal).' '.vertaalTekst(str_replace(' Beheer','',$sc->CRMdata['gewenstRisicoprofiel']),$this->pdf->rapport_taal),
                             vertaalTekst('Kans',$this->pdf->rapport_taal),
                             vertaalTekst('Minimaal eindvermogen',$this->pdf->rapport_taal)));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($sc->verwachteWaarden as $scenario=>$eindvermogen)
    {
      $kleur=$this->scenarioKleur[$scenario];
      $this->pdf->Rect($this->pdf->getX()+40-3,$this->pdf->GetY()+1, 2, 2 ,'F','',$kleur);
      $this->pdf->row(array(vertaalTekst($scenario,$this->pdf->rapport_taal),$this->formatGetal(100-$sc->scenarios[$scenario],0).'%',$this->formatGetalNegatief($eindvermogen)));
    }
    
 
	}
  
  
  function VBarDiagram($w, $h, $data,$titel,$kleurdata,$doelvermogen)
  {
    global $__appvar;
    $legendaWidth = 0;
    $grafiekPunt = array();
    
    
    $xPositie=$this->pdf->getX();
    $yPositie=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->setXY($xPositie-20,$yPositie-$h-8);
    $this->pdf->Multicell($w,5,$titel,'','C');
    $this->pdf->setXY($xPositie+110,$yPositie-$h-8);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
  //  $this->pdf->Multicell(20,5,'X 1.000','','L');
    $this->pdf->setXY($xPositie,$yPositie);
  
    $maxVal=0;
    $legenda=array();
    foreach ($data as $categorie=>$waarden)
    {
      $legenda[$categorie] = $categorie;
      foreach ($waarden['staven'] as $scenario=>$waarde)
      {
        if($waarde>$maxVal)
          $maxVal=$waarde;
      }
    }

    $numBars = count($legenda);
    
    $color=array(155,155,155);
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $YstartGrafiek = $YPage;
    $hGrafiek = $h;
    $XstartGrafiek = $XPage;
    $bGrafiek = $w; // - legenda
    
    
    $maxmaxVal=ceil($maxVal/(pow(10,strlen(round($maxVal)))))*pow(10,strlen(round($maxVal)));
  
    //echo "$maxVal $maxmaxVal";exit;
    
    if($maxmaxVal/8 > $maxVal)
      $maxVal=$maxmaxVal/8;
    elseif($maxmaxVal/4 > $maxVal)
      $maxVal=$maxmaxVal/4;
    elseif($maxmaxVal/2 > $maxVal)
      $maxVal=$maxmaxVal/2;
    else
      $maxVal=$maxmaxVal;
    
    $unit = $hGrafiek / $maxVal * -1;
    
    $nulYpos =0;
    $horDiv = 5;
    $bereik = $hGrafiek/$unit;
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = (abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $absUnit =abs($unit);
    $nulpunt = $YstartGrafiek + $nulYpos;
   // $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));
    
    $n=0;
    if($absUnit > 0 && $stapgrootte >0)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      for ($i = $nulpunt; round($i) >= $top; $i -= $absUnit * $stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
        $this->pdf->SetXY($XstartGrafiek - 1, $i - 1.5);
        $this->pdf->Cell(1, 3, $this->formatGetal($n * $stapgrootte) . "", 0, 0, 'R');
        
        $this->pdf->SetXY($XstartGrafiek+$w +0.5, $i - 1.5);
        
        $this->pdf->Cell(1, 3, $this->formatGetal($n * 20) . "%", 0, 0, 'L');
        $n++;
      }
    }
  
    $yval = $YstartGrafiek - ($doelvermogen * $absUnit);
    $this->pdf->Line($XstartGrafiek, $yval, $XstartGrafiek + $bGrafiek, $yval, array('width' => 0.5,'dash' => 0, 'color' => array(184,134,17)));
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
    
    $eBaton = ($vBar * 50 / 100);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    $legendaPrinted=array();
    foreach ($data as $risicoKlasse=>$risicoData)
    {
      arsort($risicoData['staven']);
      foreach($risicoData['staven'] as $scenario=>$val)
      {
       // listarray("$scenario=>$val <br>\n");
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $nulpunt ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$kleurdata[$scenario]);
       // echo "$xval, $yval, $lval, $hval, <br>\n";
     
        $this->pdf->SetTextColor(0,0,0);

      }
  
  
      if($legendaPrinted[$risicoKlasse] != 1)
      {
        //$this->pdf->TextWithRotation($xval-0.75,$YstartGrafiek+7.0,$risicoKlasse,45);
        $this->pdf->setXY($xval-($vBar/4),$YstartGrafiek+0.5);
        $this->pdf->Multicell($vBar,2.5,str_replace(' Beheer', '', $risicoKlasse),'','C');
  
        //$this->pdf->setXY($xval+($vBar/4),$nulpunt+$risicoData['kans']*$hGrafiek*-0.01);
        $this->pdf->Circle($xval+($vBar/4),$nulpunt+$risicoData['kans']*$hGrafiek*-0.01,1,0,360,'DF','',array(0,0,0));
  
      }
      //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);
  
  
      $legendaPrinted[$risicoKlasse] = 1;
      $i++;
    }
    
    
    $x1=$xPositie;
    $y1=$nulpunt+8;
    $hLegend=3;
    $legendaMarge=2;
    
    foreach ($kleurdata as $categorie=>$color)
    {
      $this->pdf->SetFont($this->rapport_font, '', 6);
      //$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['R'],$this->pdf->rapport_fonds_fontcolor['G'],$this->pdf->rapport_fonds_fontcolor['B']);
      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
      
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $this->pdf->Rect($x1-3, $y1+.5, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x1  ,$y1);
      $this->pdf->Cell(0,4,$categorie);
      // $y1+= $hLegend + $legendaMarge;
      $x1+=22;
      $i++;
      
    }
    $x1=$xPositie+$w-2;
    $this->pdf->Circle($x1,$y1+2,1,0,360,'DF','',array(0,0,0));
    $this->pdf->SetXY($x1+1  ,$y1);
    $this->pdf->Cell(0,4,'Kans');
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
  }
  
  
function LineDiagram($w, $h, $data,$werkelijkVerloop,$doelVermogen)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=10000000; $horDiv=5; $verDiv=4;$jaar=0;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
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

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
      
      $this->pdf->setXY($XDiag-17, $i);
      if($n==0)
        $waarde=$minVal;
      else
        $waarde=0-($n*$stapgrootte);
   
      $this->pdf->Cell(17,0, $this->formatGetal($waarde,0)."", 0,0, "R");
      
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
           $this->pdf->setXY($XDiag-17, $i);
           $this->pdf->Cell(17,0, $this->formatGetal($n*$stapgrootte,0)."", 0,0, "R");
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
     $aantalWaarden=count($waarden);
   if($aantalWaarden> 20)
     $modi=2;
   else
     $modi=1; 
     
    for ($i=0; $i<$aantalWaarden; $i++)
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
      
      if($i==($aantalWaarden-1))
      {
        $this->pdf->setXY($XDiag+$w+1, $yval2);
        $this->pdf->Cell(17,0, $this->formatGetal($waarden[$i],0)."", 0,0, "L");
      }
      
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
  
  
  
  
    $r=1;
    $yval = $YDiag + (($maxVal-$doelVermogen) * $waardeCorrectie) ;
    $xval=$XDiag+(count($waarden))*$unit-$xcorrectie;
    $circleStyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $this->pdf->Circle($xval,$yval, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    
    $this->pdf->Circle($XDiag,$YDiag+$h+12, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    $this->pdf->TextWithRotation($XDiag+2,$YDiag+$h+13,vertaalTekst('Doelvermogen',$this->pdf->rapport_taal),0);
    
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
?>