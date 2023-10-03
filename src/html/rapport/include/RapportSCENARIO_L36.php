<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/09/27 15:59:00 $
File Versie					: $Revision: 1.8 $

$Log: RapportSCENARIO_L36.php,v $
Revision 1.8  2017/09/27 15:59:00  rvv
*** empty log message ***

Revision 1.7  2016/11/09 17:05:19  rvv
*** empty log message ***

Revision 1.6  2016/11/02 16:34:11  rvv
*** empty log message ***

Revision 1.5  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.4  2015/09/30 15:53:11  rvv
*** empty log message ***

Revision 1.3  2014/12/20 22:25:16  rvv
*** empty log message ***

Revision 1.2  2014/09/17 15:16:31  rvv
*** empty log message ***

Revision 1.1  2014/09/14 15:15:29  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/scenarioBerekening.php");
//ini_set('max_execution_time', 20);

class RapportSCENARIO_L36
{
	function RapportSCENARIO_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "SCENARIO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_SCENARIO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_SCENARIO_titel;
		else
			$this->pdf->rapport_titel = "Scenario-analyse";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->gewenstRisicoprofiel = '';
    $this->vuurtorenIMG="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";

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
    $this->pdf->setY(50);
    $this->pdf->alternativeFooterTxt="Aan deze opgave kunnen geen rechten worden ontleend. Er wordt onder meer gebruikt gemaakt van veronderstellingen ten aanzien van het verwachte rendement en de standaarddeviatie. De standaarddeviatie is een indicator voor risico en is gebaseerd op historische gegevens. Prognoses zijn geen betrouwbare indicator voor toekomstige resultaten. De waarde van beleggingen kan fluctueren. In het verleden behaalde resultaten bieden geen garantie voor de toekomst.";
	 
    

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
    
     $this->pdf->setY(80);
//    if($this->pdf->portefeuilledata['Layout']==5)
//    {
      $sc->overigeRisicoklassen();
      $this->pdf->widthA = array(175,30);
		  $this->pdf->widthB = array(150,30,25,25,25,25);
		  $this->pdf->alignB = array('L','L','R','R','R','R','R');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('','Risicoprofiel','Kans op doel','Pessimistisch','Normaal','Optimistisch'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $negatiefAdvies=true;
      $maxKansTmp=0;
      $kansData=$sc->berekenKansBijOpgehaaldeRisicoklassen();
      foreach($kansData['risicoklassen'] as $risicoklasse=>$klasseData)
      {
        //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);  
        //$this->pdf->SetWidths($this->pdf->widthA);
        //$this->pdf->row(array('',"(".$klasseData['risicoklasseData']['afkorting'].")"));
        //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        //$this->pdf->Ln(-4);
        $this->pdf->SetWidths($this->pdf->widthB);
        $this->pdf->row(array('',$klasseData['risicoklasseData']['afkorting'],$this->formatGetal($klasseData['uitkomstKans']['kans'],0).'%',
                            $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Pessimistisch']),
                            $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Normaal']),
                            $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Optimistisch'])));
 
      }
      $grafiekData=$kansData['grafiekData'];
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
      
    $this->pdf->setXY(160,125);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(130,0,'Kans op behalen doelstelling bij diverse profielen',0,0,'C');
    $this->pdf->setXY(160,130);
    $this->scatterplot(130,50,$grafiekData,$sc->profieldata['maximaalRisicoprofielStdev'],$besteProfiel);  

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
    $this->pdf->setY(50);
    $this->scenarioKleur=$sc->scenarioKleur;
    $aantalSimulaties=10000;
    $sc->berekenSimulaties(0,$aantalSimulaties);
    $sc->berekenDoelKans();
    $sc->berekenVerdeling();
    

      
    $this->pdf->setXY($this->pdf->marge,50);  
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Uitgangswaarden'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);  
    $this->pdf->row(array('Beginwaarde',"€ ".$this->formatGetal($sc->CRMdata['startvermogen'])));
    $this->pdf->row(array('Doelvermogen',"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    $this->pdf->row(array('Startjaar',substr($sc->CRMdata['startdatum'],0,4)));
    $this->pdf->row(array('Doeljaar',substr($sc->CRMdata['doeldatum'],0,4)));
    $this->pdf->row(array('Berekend profiel',$sc->CRMdata['gewenstRisicoprofiel']));
    $this->pdf->row(array('Maximaal risicoprofiel',$sc->CRMdata['maximaalRisicoprofiel']));
    $this->pdf->row(array('Verwacht rendement',$this->formatGetal(($sc->profieldata['verwachtRendement']-1)*100,1).'%'));
    $this->pdf->row(array('Standaarddeviatie',$this->formatGetal($sc->profieldata['klasseStd']*100,1).'%'));
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setY(50);
    $this->pdf->widthB = array(150,40,30,20);
		$this->pdf->alignB = array('L','L','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->row(array('','Conclusies'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Kans op doelvermogen',$this->formatGetal($sc->doelKans,0).'%'));
    $this->pdf->row(array('','Risicoprofiel:',$sc->CRMdata['gewenstRisicoprofiel']));
    $this->pdf->row(array('','Doelvermogen:',"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    //$this->pdf->row(array('','Gemiddeld eindvermogen',"€ ".$this->formatGetalNegatief($kansData['risicoklassen'][$sc->CRMdata['gewenstRisicoprofiel']]['uitkomstKans']['scenarioEindwaarden']['Normaal'])));
    $this->startJaar=$sc->CRMdata['startdatum'];
    

    foreach($sc->cashflow as $jaar=>$waarde)
      $cashflow[$jaar]['scenario']=$waarde;
    
    $this->pdf->setXY(20,125);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(125,0,'Scenario-analyse',0,0,'C');
    $this->pdf->setXY(20,130);
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
      $this->LineDiagram(125,50,$grafiek,$sc->werkelijkVerloop,$sc->CRMdata['doelvermogen']);
    }
    else
      $this->LineDiagram(125,50,$sc->scenarioGemiddelde,'',$sc->CRMdata['doelvermogen']);


     $this->pdf->setY(50);
    $n=0; 
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    { 
      ksort($cashflow);
      $this->pdf->widthB = array(80,18,20,20);
		  $this->pdf->alignB = array('L','L','R','R');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('','Cashflow'));
      $this->pdf->row(array('','Jaar','werkelijk €','scenario €'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
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
      if(isset($cashflowOverig))
        $this->pdf->row(array('','Restant',$this->formatGetal($cashflowOverig['werkelijk']),$this->formatGetal($cashflowOverig['scenario'])));
    }
    else
    {
      $this->pdf->widthB = array(90,18,25);
		  $this->pdf->alignB = array('L','L','R','R');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('','Cashflow'));
      $this->pdf->row(array('','Jaar','Bedrag in €'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($sc->cashflowText as $bedragData)
      {
          $this->pdf->row(array('',$bedragData[0],$this->formatGetal($bedragData[1],0)));
      }
    }
    
    
    
    $this->pdf->setY(90);
		$this->pdf->widthB = array(5,35,30,30,30);
		$this->pdf->alignB = array('L','L','R','R','L');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',"\nScenario ".$sc->CRMdata['gewenstRisicoprofiel'],"\nKans","Eindvermogen\ntenminste",''));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($sc->verwachteWaarden as $scenario=>$eindvermogen)
    {
      $kleur=$this->scenarioKleur[$scenario];
      $this->pdf->Rect($this->pdf->getX()+5-3,$this->pdf->GetY()+1, 2, 2 ,'F','',$kleur); 
      $this->pdf->row(array('',$scenario,$this->formatGetal( round((100-$sc->scenarios[$scenario])/5)*5,0).'%',$this->formatGetalNegatief($eindvermogen)));
    }
    
 
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
        if($tekstWidth > 5)
        {
          $this->pdf->setXY(($XDiag+$Xunit*$maxStdev),$bodem-$hDiag/2);
          $this->pdf->MultiCell($tekstWidth,2.5,"Buiten risicotolerantie", 0,"C");
        }
      }
      //echo $tekstWidth;exit;
      //Buiten risicotolerantie

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
    $this->pdf->Text($XDiag+$maxXVal/2*$Xunit-8, $bodem+6, "Standaarddeviatie");
    
   $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
     
   foreach($data as $reeks=>$waarden)
   {
     $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
 
     if($this->pdf->portefeuilledata['Layout']==5 && $reeks==$beste['scenario'])
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
    $this->pdf->TextWithRotation($XDiag+2,$YDiag+$h+10+1,"Doelvermogen",0);
    
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