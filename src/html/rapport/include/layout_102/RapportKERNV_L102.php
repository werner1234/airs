<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportKERNV_L102
{
	function RapportKERNV_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
  	$this->pdf->rapport_titel = "Resultaat posities";

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


		$this->pdf->widthB = array(70,30);
		$this->pdf->alignB = array('L','R');
    $this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->setAligns($this->pdf->alignB);
    
    
    include_once("rapport/include/layout_102/ATTberekening_L102.php");
    $att=new ATTberekening_L102($this);
    $att->indexPerformance=false;
    $this->waarden['Periode']=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'fondsen');
    $fondsResultaten=array();
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
        //listarray($perf);
        $categorieData[$fondsDetails['catOmschrijving']][$fondsDetails['fondsOmschrijving']] = $fondsData['resultaat'];
      }
    }
    //echo array_sum($fondsResultaten);exit;
		/*
			$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
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
				 TijdelijkeRapportage.beleggingscategorieOmschrijving,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
			
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($query,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

      $n=0;
      $kopPrinted=false;
      $categorieData=array();
			while($subdata = $DB2->NextRecord())
      {
        $dividend = $this->getDividend($subdata['fonds']);
        
        //$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
        //$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
        
        $resultaat=$subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected'];
        $categorieData[$subdata['beleggingscategorieOmschrijving']][$subdata['fondsOmschrijving']]=$resultaat;
        
      }
    */
    
    
			$kleur=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      foreach($categorieData as $categorie=>$fondsen)
			{
        
        $this->pdf->addPage();
        if(!isset($this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']))
        {
          $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
          $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
        }
        
        
        arsort($fondsen);
        //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        //$this->pdf->Row(array($categorie));
        //$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
        
        $totaal=0;
        //($waarde<0)?$rood:$blauw)
        $grafiekData=array();
        
        foreach ($fondsen as $fonds=>$waarde)
        {
       //   $this->pdf->Row(array($fonds, $this->formatGetal($waarde,0)));
          $totaal+=$waarde;
          $grafiekData[]=array('waarde'=>$waarde,'omschrijving'=>$fonds,'kleur'=>$kleur,'nieuweWaarde'=>$totaal);
          if(count($grafiekData)>26)
          {
            $this->pdf->setXY(130,50);
            $this->VBarDiagram(150,80,$grafiekData,$categorie);
            $this->pdf->addPage();
            $grafiekData=array();
            $totaal=0;
          }
        }
        
        $this->pdf->setXY(130,50);
        $this->VBarDiagram(150,80,$grafiekData,$categorie);
       // listarray($grafiekData);
       
      }
    

      
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
	}
  
  
  function VBarDiagram($w, $h, $data,$categorie)
  {
    $tekstKleur=array(89,89,89);
    $xPositie=$this->pdf->getX();
    $yPositie=$this->pdf->getY();
    $this->pdf->SetTextColor($tekstKleur[0],$tekstKleur[1],$tekstKleur[2]);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->setXY($xPositie,$yPositie-8);
    $this->pdf->Multicell($w,5,'Resultaat '.$categorie ,'','C');
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $h=(count($data)+2)*5;
    
    $this->pdf->TextWithRotation($xPositie,$yPositie-4,'x Duizend',0);
    $this->pdf->setXY($xPositie,$yPositie);
    
    $numBars = count($data);
    $max=0;
    
    foreach($data as $waarden)
    {
      if($waarden['waarde']>$max)
        $max=$waarden['waarde'];
      if($waarden['nieuweWaarde']>$max)
        $max=$waarden['nieuweWaarde'];
    }
    $min=$max;
    foreach($data as $waarden)
    {
      
      //if($waarden['nieuweWaarde']>0)
     // {
        if ($waarden['nieuweWaarde'] < $min)
        {
          $min = $waarden['nieuweWaarde'];
        }
      //}
			//elseif($waarden['waarde']>0 && $waarden['waarde']<$min)
     // {
     //   $min = $waarden['waarde'];
     // }
    }
    
    $max=ceil($max/1000)*1000;
    $min=floor($min/1000)*1000;
    if($min>0)
      $min=0;
    $ruimte=$max-$min;
    //echo "$ruimte=$max-$min; <br>\n";exit;
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    
    $YstartGrafiek = $YPage;
    $hGrafiek = $h;
    $XstartGrafiek = $XPage;
    $bGrafiek = $w; // - legenda
    
    $unit = $bGrafiek / $ruimte ;
  
    if($min<0)
    {
      $nulPuntExtraX = $min * $unit * -1;
      $this->pdf->Line($XstartGrafiek+$nulPuntExtraX, $YstartGrafiek, $XstartGrafiek+$nulPuntExtraX ,$YstartGrafiek+$h,array('dash' => 0,'color'=>array(0,0,0)));
    }
    else
      $nulPuntExtraX=0;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor($tekstKleur[0],$tekstKleur[1],$tekstKleur[2]);
    
    $rechts = $XstartGrafiek+$bGrafiek;
    
    $absUnit =abs($unit);
    $horDiv=5;
    $stapgrootte = (abs($ruimte)/$horDiv);
    
    
    $nulpunt = $YstartGrafiek ;
    
    $this->pdf->Rect($XstartGrafiek, $YstartGrafiek, $bGrafiek, $hGrafiek,'D','',array(250,250,250));
    
    $n=0;
    
    for($i=$XstartGrafiek; $i <= $rechts; $i+= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($i,$YstartGrafiek ,$i, $YstartGrafiek+$h ,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($i,$YstartGrafiek-3.5);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      $this->pdf->Cell(1, 3, $this->formatGetal(($min+$n*$stapgrootte)/1000,1)."",0,0,'C');
      $n++;
      if($n>20)
        break;
    }
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
  //  $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
   // $eBaton = ($vBar * 50 / 100);
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    //	$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    if(count($data)>10)
    	$decimaal=0;
    else
    	$decimaal=1;
  
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    foreach ($data as $i=>$barData)
    {
      if(!isset($XstartGrafiekLast))
        $XstartGrafiekLast = $xPositie+$nulPuntExtraX;
      //Bar
      $yval = $YstartGrafiek + (1 + $i ) * 5;
      
      $xval = $XstartGrafiekLast ;
      if(isset($barData['nieuweWaarde']))
      {
        $val = $barData['waarde'];
      }
      else
      {
        if($i>0)
        {
          $xval=$XstartGrafiekLast;
          $val = $barData['waarde'] - $min;
        }
        else
          $val = $barData['waarde'] - $min;
      }
      $hval=4;
      $lval = ($val * $unit);
      
      $this->pdf->setXY($this->pdf->marge,$yval);
      $this->pdf->SetTextColor(0,0,0);
      $this->pdf->Row(array($barData['omschrijving'],$this->formatGetal($barData['waarde'],0)));
      $this->pdf->Rect($xval,$yval,$lval, $hval, 'DF',null,$barData['kleur']);
      $XstartGrafiekLast= $XstartGrafiekLast+$lval;
      
      
      /*
      $this->pdf->SetTextColor(255,255,255);
      if(abs($hval) > 3 && abs($lval)>3)
      {
        $this->pdf->SetXY($xval+($lval/2)-5, $yval);
        $this->pdf->Cell(10, 4, $this->formatGetal($barData['waarde']/1000,$decimaal,0),0,0,'C');
      }
      $this->pdf->SetTextColor($tekstKleur[0],$tekstKleur[1],$tekstKleur[2]);
        */
      
      if($barData['waarde']>0 || 1)
      {
        if(($XstartGrafiekLast-$xPositie) > $w/2)
        {
          if($barData['waarde']>0)
            $this->pdf->SetXY($xval -1 , $yval);
          else
            $this->pdf->SetXY($xval -1 + $lval , $yval);
          $align='R';
        }
        else
        {
          if($barData['waarde']>0)
            $this->pdf->SetXY($xval + $lval, $yval);
          else
            $this->pdf->SetXY($xval , $yval);
          $align='L';
        }
        $this->pdf->Cell(1, 4, $this->formatGetal($barData['waarde'],0),0,0,$align);
        
      }
      
      
      

      /*    */
      
      
      /*
      $tw=$this->pdf->GetStringWidth($barData['omschrijving']);
      $text=$barData['omschrijving'];
      if($tw<45)
      {
        
  
        $dots = round((45 - $tw) / ($this->pdf->CurrentFont['cw']['.'] * $this->pdf->FontSize / 1000));
        $text .= str_repeat('.', $dots - 3);
      }
      $this->pdf->TextWithRotation($xval-30,$YstartGrafiek+35,$text,45);
      */
      
    }
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
  }
}
?>
