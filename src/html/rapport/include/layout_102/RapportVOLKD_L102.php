<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLKD_L102
{
	function RapportVOLKD_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLKD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
  	$this->pdf->rapport_titel = "Relatieve bijdrage tot het resultaat";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
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
     $aantal=array();
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

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$this->pdf->widthB = array(70,30,25,20);
		$this->pdf->alignB = array('L','R','R','R');
    $this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->setAligns($this->pdf->alignB);
    
    
    include_once("rapport/include/layout_102/ATTberekening_L102.php");
    $att=new ATTberekening_L102($this);
    $att->indexPerformance=false;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'fondsen');
    $fondsResultaten=array();
    $categorieDataContributie=array();
    //listarray($this->waarden['Periode']);
    foreach($this->waarden['Periode'] as $fonds=>$fondsData)
    {
   //   listarray($fondsData);
      $query="SELECT Rekeningmutaties.id FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     WHERE
     Rekeningen.Portefeuille='".$this->portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.fonds='" . mysql_real_escape_string($fonds) . "'";
      $aantal=$DB->QRecords($query);
      if($aantal>0)
      {
       // $perf = $att->fondsPerformance(array('fondsen' => array($fonds)), $this->rapportageDatumVanaf, $this->rapportageDatum,false,$fonds);
        $query="SELECT
Beleggingscategorien.Omschrijving AS catOmschrijving,
Fondsen.Omschrijving AS fondsOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie
FROM
BeleggingscategoriePerFonds
JOIN Fondsen ON BeleggingscategoriePerFonds.Fonds = Fondsen.Fonds
JOIN CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='" . $this->portefeuilledata['Vermogensbeheerder'] . "'
JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE
	BeleggingscategoriePerFonds.fonds = '" . mysql_real_escape_string($fonds) . "'
AND BeleggingscategoriePerFonds.vermogensbeheerder = '" . $this->portefeuilledata['Vermogensbeheerder'] . "'";
        $DB->SQL($query);
        $DB->Query();
        $fondsDetails = $DB->nextRecord();
        //listarray($fondsDetails);
        $fondsResultaten[$fonds] = $fondsData['resultaat'];
        unset($fondsData['perfWaarden']);
        //listarray($perf);
        $categorieData[$fondsDetails['catOmschrijving']][$fondsDetails['fondsOmschrijving']] = $fondsData;
        $categorieDataContributie[$fondsDetails['catOmschrijving']][$fondsDetails['fondsOmschrijving']] = $fondsData['bijdrage'];
      }
    }

    
			$kleur=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);

      foreach($categorieDataContributie as $categorie=>$fondsen)
			{
        
        arsort($fondsen);
        $grafiekData=array();
        $pagina=1;
        $this->pdf->addPage();
        if(!isset($this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']))
        {
          $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
          $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
        }
        
        arsort($fondsen);
        //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
       // $this->pdf->Row(array($categorie));
       // $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
        

        //($waarde<0)?$rood:$blauw)
        $teller=0;
        
        foreach ($fondsen as $fonds=>$contributie)
        {
          $details=$categorieData[$categorie][$fonds];
          if($teller>15)
          {
            $pagina++;
            $teller=0;
          }
  
          $grafiekData[$pagina]['bijdrage']['kleurData'][$fonds]=array(111,111,111);
          $grafiekData[$pagina]['bijdrage']['kleurData'][$fonds]['percentage']=$details['bijdrage'];
          $grafiekData[$pagina]['bijdrage']['data'][$fonds]=$details['bijdrage'];
  
          $grafiekData[$pagina]['weging']['kleurData'][$fonds]=$kleur;
          $grafiekData[$pagina]['weging']['kleurData'][$fonds]['percentage']=$details['weging']*100;
          $grafiekData[$pagina]['weging']['data'][$fonds]=$details['weging']*100;

          $teller++;
        }
        
        foreach($grafiekData as $pagina=>$data)
        {
          
          if($pagina>1)
            $this->pdf->addPage();
          $this->pdf->setXY(110, 50);
          $h=8*count($data['weging']['data']);
          if($h<10)
            $h=10;
          
          $this->BarDiagram(150, $h, array($data['bijdrage']['data'], $data['weging']['data']), array( $data['bijdrage']['kleurData'], $data['weging']['kleurData']),$categorie);
          $legenda = array('contributie' => array(150, 150, 150), 'weging' => array($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b']));
          $x = 120;
          $startY = $this->pdf->getY() + 12;
          foreach ($legenda as $titel => $kleur)
          {
            $this->pdf->rect($x, $startY, 2, 2, 'DF', null, $kleur);
            $this->pdf->setXY($x + 4, $startY - 1.5);
            $this->pdf->MultiCell(100, 5, vertaalTekst($titel,$this->pdf->rapport_taal), 0, "L");
            $x += 50;
          }
          
        }
       
      }
    
    //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->Row(array('Totaal', $this->formatGetal($totaal['weging']*100,1),$this->formatGetal($totaal['resultaat'],0),$this->formatGetal($totaal['bijdrage'],1)));
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    //$this->pdf->headerOnderdrukken=true;
    //$this->pdf->addPage();

   // unset($this->pdf->headerOnderdrukken);

      
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
	}
  
  
  function BarDiagram($w, $h, $data, $colorArray=null, $categorie='', $nbDiv=4)
  {
    $reeksenPerCategorie=1;
    $newData=array();
    $newColors=array();
    $minVal=0;
    $maxVal=0;
    
    if(count($data) > 1)
    {
      $reeksenPerCategorie=count($data) ;
      foreach($data as $set=>$waarden)
      {
        foreach($waarden as $groep=>$percentage)
        {
          $newData[$groep][$set] = $percentage;
          if ($percentage * 1.1 > $maxVal)
          {
            $maxVal = $percentage * 1.1;
          }
          if ($percentage * 1.1 < $minVal)
          {
            $minVal = $percentage * 1.1;
          }
        }
      }
      
      foreach($colorArray as $set=>$waarden)
      {
        foreach($waarden as $groep=>$kleuren)
          $newColors[$groep]=array($kleuren[0],$kleuren[1],$kleuren[2]);
      }
      $data=$newData;
      $colorArray=$newColors;
    }

    // $this->pdf->SetLegends($data,$format);
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
  
    $tekstKleur=array(89,89,89);
    $this->pdf->SetTextColor($tekstKleur[0],$tekstKleur[1],$tekstKleur[2]);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->setXY($XPage,$YPage-8);
    $this->pdf->Multicell($w,5,$categorie ,'','C');
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $YDiag = $YPage;
    $hDiag = floor($h);
    $XDiag = $XPage;
    $lDiag = floor($w);
    if(!isset($color))
      $color=array(155,155,155);
    // listarray($colorArray);
    $aantalRegels=count($colorArray);
    $aantalStaven=$aantalRegels*$reeksenPerCategorie+1;
    
    //$minVal=0;
    $offset=$minVal;
    $valIndRepere = ceil(($maxVal-$minVal) / $nbDiv);
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    if($aantalStaven*3*$reeksenPerCategorie < $h)
      $hDiag=$aantalStaven*3*$reeksenPerCategorie;
    
    $hBar = floor($hDiag / ($aantalStaven));
    //  $hDiag = $hBar * ($aantalStaven + 1 +$reeksenPerCategorie*1);
    $eBaton = floor($hBar * 80 / 100);
    $legendaStep=$unit;
    if($bandBreedte/$legendaStep > $nbDiv)
      $legendaStep=$legendaStep*5;
    if($bandBreedte/$legendaStep > $nbDiv)
      $legendaStep=$legendaStep*2;
    if($bandBreedte/$legendaStep > $nbDiv)
      $legendaStep=$legendaStep/2*5;
    $valIndRepere=round($valIndRepere/$unit/5)*5;
    
    
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    $nullijn=$XDiag - ($offset * $unit);
    
    $i=0;
    $nbDiv=10;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    if(round($legendaStep,5) <> 0.0)
    {
      for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
      }
      
      for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $i=0;
    
    //$this->pdf->SetXY(0, $YDiag);
    //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
    //$this->pdf->SetXY($nullijn, $YDiag);
    //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
    $this->pdf->SetXY($XDiag, $YDiag);
    // $this->pdf->Cell($lDiag, $hval-4, 'Verdeling',0,0,'C');
    $extraY=0;
    foreach($data as $categorie=>$staven)
    {
      $yval=0;
      foreach($staven as $index=>$val)
      {
        if($index==0)
          $this->pdf->SetFillColor(150,150,150);
        else
          $this->pdf->SetFillColor($colorArray[$categorie][0],$colorArray[$categorie][1],$colorArray[$categorie][2]);
        //Bar
        $xval = $nullijn;
        $lval = ($val * $unit);
        $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2+$extraY;
        $hval = $eBaton;
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
        //Legend
        $this->pdf->SetXY($this->pdf->marge, $yval);
        $this->pdf->Cell(100, $hval, $this->formatGetal($val,2,true), 0, 0, 'R');
        $i++;
      }
      $this->pdf->SetXY($this->pdf->marge, $yval-$hBar/2);
      $this->pdf->Cell(75, $hval, $categorie, 0, 0, 'L');
      $extraY=$extraY+2;
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
  
  
}
?>
