<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportRISK_L105
{
	function RapportRISK_L105($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Risico verdeling";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  
  function scatterplot($w, $h, $data)
  {
    
    
    $minXVal=0; $maxXVal=25;
    $minYVal=0; $maxYVal=10;
  
    foreach($data as $fonds=>$fondsData)
    {
      if($fondsData['x']>$maxXVal)
        $maxXVal=$fondsData['x'];
      if($fondsData['y']>$maxYVal)
        $maxYVal=$fondsData['y'];
      if($fondsData['y']<$minYVal)
        $minYVal=$fondsData['y'];
    }
  
    $minYVal=floor($minYVal/5)*5;
    $minXVal=floor($minXVal/5)*5;
    $maxYVal=ceil($maxYVal/5)*5;
    $maxXVal=ceil($maxXVal/5)*5;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = $h;
    $XDiag = $XPage;
    $lDiag = $w;
    
    $color=array(0,0,0);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  
    
    
    $xband=($maxXVal - $minXVal);
    $yband=($maxYVal - $minYVal);
    $waardeCorrectie = $hDiag / $yband;
    $Xunit = $lDiag / $xband;
    $Yunit = $hDiag / $yband *-1;
   
   // $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    
  
  
    $nulpunt = $YDiag + ($maxYVal * $waardeCorrectie);
    $bodem = $YDiag+$hDiag;
    
    $n=0;
    
    
    for($i=$minYVal; $i<= $maxYVal; $i+= 5)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $nulpunt+$i*$Yunit, $XPage+$w ,$nulpunt+$i*$Yunit,array('dash' => 1,'color'=>array(0,0,0)));
      
      $this->pdf->setXY($XDiag-20, $nulpunt+$i*$Yunit);
      $this->pdf->Cell(20,0, $i." %", 0,0, "R");
      //$this->pdf->Text($XDiag-7, $bodem+$i*$Yunit, $i." %");
      $n++;
      if($n >20)
        break;
    }
    $this->pdf->Text($XDiag-7, $nulpunt+$maxYVal*$Yunit-3, "Rendement");
    
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
    
  //  $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    
    foreach($data as $reeks=>$waarden)
    {
      $color=$waarden['kleur'];
   //   $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
      $this->pdf->Rect($XDiag+$waarden['x']*$Xunit-1,$nulpunt+$waarden['y']*$Yunit-1, 2, 2 ,'F','',$color);
      if($reeks=='Portefeuille')
      {
        $this->pdf->setXY($XDiag + $waarden['x'] * $Xunit - 5, $nulpunt + $waarden['y'] * $Yunit + 2.5);
        $this->pdf->Cell(10, 0, $reeks, 0, 0, "C");
      }
    }
    
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    
  }
	
  function printKop($kop)
{
$this->pdf->setWidths($this->pdf->widthA);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->row(array($kop));
$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
$this->pdf->setWidths($this->pdf->widthB);
}
	function writeRapport()
	{
    global $__appvar;
    include_once($__appvar["basedir"]."/html/rapport/CorrelatieStdevClass.php");
    $aantalJaar=4;
    if(isset($_POST['RISK_jaren']) && $_POST['RISK_jaren']>0)
      $aantalJaar=$_POST['RISK_jaren'];
      
    $this->pdf->AddPage();
    $stdev = new rapportSDberekening($this->portefeuille, $this->rapportageDatum);
    $dev=new correlatieStdev($this->portefeuille,$this->rapportageDatum);
    $dev->bepaalPeriode($aantalJaar);
    
    $startJul=db2jul($dev->eersteKoersDatum);
    $pstartJul=db2jul($this->pdf->portefeuilledata['Startdatum']);
    if($pstartJul>$startJul)
      $rendementStartJul=$pstartJul;
    else
      $rendementStartJul=$startJul;
    $dev->eersteKoersDatum=date('Y-m-d',$rendementStartJul);
    $maanden=$stdev->indexberekening->getMaanden($rendementStartJul,$stdev->settings['julRapportageDatum']);
    
    foreach($maanden as $maand)
    {
      $dev->bepaalPortefeuilleVerdeling($maand['stop']);
    }
    $dev->getKoersen();
   
    foreach($maanden as $maand)
    {
      $dev->bepaalPeriode($aantalJaar,$maand['stop']);
      $dev->eersteKoersDatum=date('Y-m-d',$rendementStartJul);
      $dev->getKoersen($maand['stop']);

      $dev->bepaalCorrelatieMatrix($maand['stop']);
    }
    $dev->berekenVariantie($this->rapportageDatum, '');
    
    $fondsPerf=array();
    foreach($dev->koersenPerFonds as $fonds=>$koersen)
    {
      $eersteDatum='';
      $eersteKoers='';
      $laatsteDatum='';
      $laatsteKoers='';
      foreach($koersen as $datum=>$koers)
      {
        if($eersteDatum=='')
        {
          $eersteDatum = $datum;
          $eersteKoers = $koers;
        }
        $laatsteDatum=$datum;
        $laatsteKoers=$koers;
      }
      $perf=($laatsteKoers-$eersteKoers)/$eersteKoers;
      $periode=($laatsteDatum-$eersteDatum)/86400/365.25;
//      echo "$fonds -> $perf | $periode=($laatsteDatum-$eersteDatum)/86400/365.25; <br>\n";

      $fondsPerf[$fonds]=(pow(1+$perf,1/$periode)-1)*100;
//      echo $fondsPerf[$fonds]."=(pow(1+$perf,1/$periode)-1)*100  | aantal jaren :".round($periode,2)."<br>\n<hr>" ;
    }
    
    
    //Kleuren instellen
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $randomKleur=array();
    foreach($allekleuren as $group=>$kleurdata)
    {
      foreach($kleurdata as $cat=>$kleur)
      {
        if($kleur['R']['value']<>0 && $kleur['G']['value']<>0 && $kleur['B']['value']<>0)
        {
          $randomKleur[] = $kleur;
        }
      }
    }
    $query = "SELECT TijdelijkeRapportage.fonds, TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.hoofdcategorieOmschrijving , BeleggingscategoriePerFonds.grafiekKleur".
			" FROM TijdelijkeRapportage JOIN BeleggingscategoriePerFonds ON TijdelijkeRapportage.fonds=BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='$beheerder' WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
    $this->pdf->widthA = array(55);
    $this->pdf->widthB = array(3.5,60,25,20);
    $this->pdf->alignB = array('L','L','R','R');
    $this->pdf->setAligns($this->pdf->alignB);
    $this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Fondsomschrijving','Standaarddeviatie','Rendement'));
    $lastHoofdCat='';
    $grafiekData=array();
    $i=0;
		while($data = $DB->NextRecord())
		{
		  $kleur=unserialize($data['grafiekKleur']);
		  if(!is_array($kleur) || ($kleur['R']['value']==0 && $kleur['G']['value']==0 && $kleur['B']['value']==0))
      {
        $kleur = $randomKleur[$i];
        $i++;
      }
		  if($lastHoofdCat<>$data['hoofdcategorieOmschrijving'])
      {
        $this->printKop($data['hoofdcategorieOmschrijving']);
      }
      if($data['fonds']<>'')
      {
        $grafiekData[$data['fonds']] = array('x' =>$dev->fondsStandaardDeviatie[$data['fonds']],'y'=>$fondsPerf[$data['fonds']],'kleur'=>array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']));
      }

      
      $this->pdf->SetFillColor($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
      $this->pdf->Rect($this->pdf->getX()+1,$this->pdf->getY()+1,2,2,'DF');
      $this->pdf->row(array('',$data['fondsOmschrijving'],$this->formatGetal($dev->fondsStandaardDeviatie[$data['fonds']],2),$this->formatGetal($fondsPerf[$data['fonds']],2)));
      $lastHoofdCat=$data['hoofdcategorieOmschrijving'];
    }
    
    $portefeuillestdev = $dev->std['totaal'][$this->rapportageDatum];

    $rendementProcent  	= performanceMeting($this->portefeuille, date('Y-m-d',$rendementStartJul), $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    $jaren=(db2jul($this->rapportageDatum)-$rendementStartJul)/86400/365.25;
    $pRendJaar=(pow(1+$rendementProcent/100,1/$jaren)-1)*100;
//    echo "Portefeuille= $pRendJaar=(pow(1+$rendementProcent/100,1/$jaren)-1)*100;<br>\n";
    $this->printKop('Totaal');
    $this->pdf->row(array('','Portefeuille',$this->formatGetal($portefeuillestdev,2),$this->formatGetal($pRendJaar,2)));
    
    $grafiekData['Portefeuille'] = array('x' =>$portefeuillestdev,'y'=>$pRendJaar,'kleur'=>array(0,0,0));
		
    $this->pdf->setXY(138,40);
    $this->scatterplot(150,150,$grafiekData);
  //    listarray($dev->std);exit;
  //  listarray($dev->koersenPerFonds);exit;

 
	}


}
?>
