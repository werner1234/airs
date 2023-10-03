<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/04/10 15:46:58 $
File Versie					: $Revision: 1.6 $

$Log: RapportSCENARIO2.php,v $
Revision 1.6  2016/04/10 15:46:58  rvv
*** empty log message ***

Revision 1.5  2016/04/03 10:56:11  rvv
*** empty log message ***

Revision 1.4  2016/03/27 17:28:50  rvv
*** empty log message ***

Revision 1.3  2016/03/20 14:37:26  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/scenarioBerekening.php");
//ini_set('max_execution_time', 20);

class RapportSCENARIO2
{
	function RapportSCENARIO2($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "SCENARIO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_SCENARIO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_SCENARIO_titel;
		else
			$this->pdf->rapport_titel = "Scenario-analyse-verloop";
        $this->pdf->rapport_titel=vertaalTekst($this->pdf->rapport_titel ,$this->pdf->rapport_taal);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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
    
   
    
    
    if($this->portefeuille<>'')
      $query="SELECT check_module_SCENARIO,Vermogensbeheerders.Vermogensbeheerder FROM Vermogensbeheerders 
      JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder 
      WHERE portefeuille='".$this->portefeuille."'";
    else
      $query="SELECT max(check_module_SCENARIO) as check_module_SCENARIO FROM Vermogensbeheerders";  
 		$DB->SQL($query);
		$DB->Query();
		$check_module_SCENARIO = $DB->nextRecord(); 
    if($check_module_SCENARIO['check_module_SCENARIO'] < 1)
    {
      echo "Scenario-analyse module niet geactiveerd.";
      exit;
    }
    
    $query="SELECT datum,scenarioKansOpDoel,scenarioVerwachtVermogen FROM HistorischeScenarios 
    WHERE portefeuille='".$this->portefeuille."' ORDER BY datum";
    $DB->SQL($query);
		$DB->Query();
    $kansOpDoel=array();
		while($data = $DB->nextRecord())
    {
      $kansOpDoel['datum'][]=$data['datum'];
      $kansOpDoel['Index'][]=$data['scenarioKansOpDoel'];
      

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

    $sc= new scenarioBerekening($crmId['id'],$this->pdf->portefeuilledata['Risicoklasse']);
    
    $this->scenarioProfieldata=$sc->profieldata;
    
    
    $this->pdf->setXY(165,45);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(110,0,vertaalTekst('Kans op doel',$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->setXY(165,50);
    $this->LineDiagramOld(120, 50, $kansOpDoel,null,0,0,5,4);
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
  
    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
    
    $sc->ophalenHistorie($this->portefeuille);
    $sc->werkelijkVerloop[substr($this->rapportageDatum,0,4)]=array('waarde'=>$totaalWaarde);
 
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

 
    
    $this->pdf->setXY(20,45);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(125,0,vertaalTekst('Scenario-analyse',$this->pdf->rapport_taal),0,0,'C');
    $this->pdf->setXY(20,50);

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
            $grafiek[$scenario][]='';//$data['waarde'];//$sc->CRMdata['startvermogen'];
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
      $this->LineDiagram(120,50,$grafiek,$sc->werkelijkVerloop,$sc->CRMdata['doelvermogen']);


    $yPagina=122;
    $this->pdf->setY($yPagina);
    $xOffset=0;

    $this->pdf->SetWidths(array(5+$xOffset,40,30,30));
    $this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Uitgangswaarden',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);  
    $this->pdf->row(array('',vertaalTekst('Huidig vermogen',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['startvermogen'])));
    $this->pdf->row(array('',vertaalTekst('Doelvermogen',$this->pdf->rapport_taal),"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    $this->pdf->row(array('',vertaalTekst('Doeljaar',$this->pdf->rapport_taal),substr($sc->CRMdata['doeldatum'],0,4)));
    $this->pdf->row(array('',vertaalTekst('Verwacht rendement',$this->pdf->rapport_taal),$this->formatGetal(($sc->profieldata['verwachtRendement']-1)*100,1).'%'));
    $this->pdf->row(array('',vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal),$this->formatGetal($sc->profieldata['klasseStd']*100,1).'%'));
    $this->pdf->ln();

    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Scenario',$this->pdf->rapport_taal).' '.vertaalTekst($sc->CRMdata['gewenstRisicoprofiel'],$this->pdf->rapport_taal),
                             vertaalTekst('Kans ongeveer',$this->pdf->rapport_taal),
                             vertaalTekst('Eindvermogen',$this->pdf->rapport_taal)));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($sc->verwachteWaarden as $scenario=>$eindvermogen)
    {
      $kleur=$this->scenarioKleur[$scenario];
      $this->pdf->Rect($this->pdf->getX()+5-3+$xOffset,$this->pdf->GetY()+1, 2, 2 ,'F','',$kleur); 
      $this->pdf->row(array('',vertaalTekst($scenario,$this->pdf->rapport_taal),$this->formatGetal( round((100-$sc->scenarios[$scenario])/5)*5,0).'%',$this->formatGetalNegatief($eindvermogen)));
    }
    
    foreach($sc->cashflow as $jaar=>$waarde)
      $cashflow[$jaar]['scenario']=$waarde;
     
    $this->pdf->setY($yPagina); 
    $xOffset=110;
    $xOffset=$xOffset+30;

      ksort($cashflow);
      $this->pdf->widthB = array(80+$xOffset,18,20,20);
		  $this->pdf->alignB = array('L','L','R','R');
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
        if($n > 14)
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

    $this->pdf->Rect($this->pdf->marge    ,120,105,75);
    $this->pdf->Rect($this->pdf->marge+110,120,105 ,75);
    $this->pdf->Rect($this->pdf->marge+219,120,60 ,75);
	}


function LineDiagramOld($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $data1 = $data['Index1'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);
  //  $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);


  $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
/*
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 100)
        $maxVal = 101;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 100)
        $minVal = 99;
    }
*/
    $minVal = 0;//100 + ($minVal-100) * 2;
    $maxVal = 100;//100 + ($maxVal-100) * 2;


     $legendYstep = ($maxVal - $minVal) / $horDiv;

     $verInterval = ($lDiag / $verDiv);
     $horInterval = ($hDiag / $horDiv);

     $waardeCorrectie = $hDiag / ($maxVal - $minVal);

     $unit = $lDiag / count($data);

     if($jaar)
       $unit = $lDiag / 12;

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

$nulpunt = $YDiag + (($maxVal-100) * $waardeCorrectie);
$n=0;
//echo "$i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte ";
  
  if($this->scenarioProfieldata['ScenarioMinimaleKans'])
  {   
    $this->pdf->Rect($XDiag,$nulpunt+(100-$this->scenarioProfieldata['ScenarioMinimaleKans'])*$waardeCorrectie,
                      $lDiag,$this->scenarioProfieldata['ScenarioMinimaleKans']*$waardeCorrectie,'F','',array(200,200,200));
    $this->pdf->SetXY($XDiag,$nulpunt+(100-$this->scenarioProfieldata['ScenarioMinimaleKans']/2)*$waardeCorrectie);                 
    $this->pdf->Cell($lDiag,0,vertaalTekst("Scenario minimaal slagingspercentage",$this->pdf->rapport_taal)." ".$this->scenarioProfieldata['ScenarioMinimaleKans']."%",0,0,'C');
     
                                        
  }
  $this->pdf->Line($XDiag, $YDiag, $XDiag ,$bodem,array('dash' => 1,'color'=>array(0,0,0)));
  for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
  {
  //  echo "$XDiag, $i, $XPage+$w ,$i <br>";
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 100-($n*$stapgrootte) ." %");
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
      $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+100 ." %");

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
      $yval = $YDiag + (($maxVal-$data[0]) * $waardeCorrectie) ;
      
      
   if(count($waarden)> 20)
     $modi=2*12;
   else
     $modi=1*12; 

      for ($i=0; $i<count($data); $i++)
      {
        
        if(!isset($datumPrinted[$i]))
        {     
          if($i%$modi==0)
            $this->pdf->TextWithRotation($XDiag+($i*$unit)-2,$YDiag+$hDiag+8,substr($legendDatum[$i],0,4),25);
          $datumPrinted[$i]=1;
        }
      }       
      $lineStyle = array('width' => 0.4, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
      for ($i=1; $i<count($data); $i++)
      {
      
        $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      //  if ($i>0)
      //   $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
        $yval = $yval2;
      }

      $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
      $this->pdf->SetFillColor(0,0,0);
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
    
    
    $this->pdf->TextWithRotation($XDiag-12,$YDiag+$h/2+10,vertaalTekst('Verwacht vermogen',$this->pdf->rapport_taal),90);
    
 

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
  if($waarden[$i]<>0 && $yval <> $bodem)
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
    
    $this->pdf->line($XDiag, $yval, $xval, $yval,array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 1, 'color' => array(0,0,0)) );
    $this->pdf->Circle($XDiag,$YDiag+$h+10, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    $this->pdf->TextWithRotation($XDiag+2,$YDiag+$h+10+1,vertaalTekst('Doelvermogen',$this->pdf->rapport_taal)." = €".$this->formatGetal($doelVermogen,0),0);
    
    $lineStyle = array('width' => 0.4, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
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