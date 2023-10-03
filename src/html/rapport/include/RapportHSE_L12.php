<?


include_once('../indexBerekening.php');
include_once('rapportATTberekening_L12.php');

class RapportHSE_L12
{

  function RapportHSE_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->underlinePercentage=0.8;
    
    $DB = new DB();
	  $query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, ".
		" Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, ".
		" Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
		" Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
		" AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
		$DB->SQL($query);
		$this->pdata = $DB->lookupRecord();

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function writeRapport()
	{
    global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.startDatum,
Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

    	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
		$this->alleKleuren=$allekleuren;
    $this->attKleuren=$allekleuren['ATT'];

   // echo $this->pdf->PortefeuilleStartdatum."<br>\n";//exit;
    $DB = new DB();
   
    $gewensteStart=(date('Y',$this->pdf->rapport_datum)-9).'-01-01';
    if(db2jul($this->portefeuilledata['startDatum'])>db2jul($gewensteStart))
      $start=$this->portefeuilledata['startDatum'];
    else
      $start=$gewensteStart;

    //if($this->pdf->lastPOST['perfPstart'] == 1 || $this->pdf->lastPOST['backoffice']==1)
    //  $start=   substr($this->pdf->PortefeuilleStartdatum,0,10);
    //else
    //  $start = $this->rapportageDatumVanaf;//substr($this->pdf->PortefeuilleStartdatum,0,10);
    
    $filterDatumGrafiek=0;
    if(count($this->pdf->portefeuilles)>1)
    {
      $query = "SELECT
min(Rekeningmutaties.Boekdatum) as startdatum
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening
WHERE Rekeningen.Portefeuille IN('" . implode("','", $this->pdf->portefeuilles) . "')";
      $DB->SQL($query);
      $DB->Query();
      $datum = $DB->nextRecord();
      $filterDatumGrafiek=$datum['startdatum'];
      //if($this->pdf->lastPOST['perfPstart'] == 1 || $this->pdf->lastPOST['backoffice']==1)
      //  $start=substr($datum['startdatum'],0,8).'01';
    }

$eind = $this->rapportageDatum;
$datumStart = db2jul($start);
$datumStop  = db2jul($eind);


    $startJul=db2jul($start);
    $this->pdf->rapport_titel = "";//"Ontwikkeling vermogen en beleggingsresultaat vanaf ".date("d",$startJul)." ".vertaalTekst($__appvar["Maanden"][date("n",$startJul)],$this->pdf->rapport_taal)." ".date("Y",$startJul);

    $this->pdf->AddPage();
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));

    $extraIndices=array();
    $extraIndicesPerformance=array();
    $extraIndicesTmp=array();
    foreach($this->pdf->lastPOST as $key=>$value)
    {
      if(substr($key,0,8)=='mmIndex_')
      {
        $extraIndices[]=$value;
        $extraIndicesTmp[$value]=0;
      }
    }

    /*
	  $this->berekening = new rapportATTberekening_L12($this->pdata);//rapportATTberekening_L12($this->pdata);
	  $categorien=$this->berekening->getAttributieCategorien();
    $this->berekening->pdata['pdf']=true;
    if (count($this->pdf->portefeuilles)>1)
      $this->berekening->portefeuilles=$this->pdf->portefeuilles;
    $this->berekening->attributiePerformance($this->portefeuille,$start,$this->rapportageDatum,'rapportagePeriode',$this->pdf->rapportageValuta);
    $this->tmp['rapportagePeriode']=$this->berekening->performance['rapportagePeriode'];
    */
    
    $this->berekening = new rapportATTberekening_L12($this);//rapportATTberekening_L12($this->pdata);
    $this->tmp=array();
    $this->waarden=array();
    $this->tmp['rapportagePeriode']=$this->berekening->bereken($start,$this->rapportageDatum,'attributie',$this->pdf->rapportageValuta);
    $categorien=$this->berekening->categorien;


foreach ($this->tmp['rapportagePeriode'] as $categorie=>$catData)
{
  foreach($catData['perfWaarden'] as $datum=>$data)
  {
    if ($categorie == 'totaal')
    {
      foreach ($extraIndices as $index)
      {
        $perf = getFondsPerformance($index, $data['begindatum'], $datum);
        $extraIndicesPerformance[$datum][$index] = ((1 + $extraIndicesTmp[$index] / 100) * (1 + $perf / 100) - 1) * 100;
        $extraIndicesTmp[$index] = $extraIndicesPerformance[$datum][$index];
      }
    }
    $this->waarden[$categorie][$datum]['waarde'] = $data['eindwaarde'];
    $this->waarden[$categorie][$datum]['perf'] = $data['procent']*100;
    $this->waarden[$categorie][$datum]['stortingen'] = $data['stort'];
    $this->waarden[$categorie][$datum]['resultaat'] = ($data['eindwaarde'] - $data['beginwaarde']) -$data['stort'];
  }
}

$huidigeJaar=date('Y');
$laatsteJaar='';
foreach ($this->waarden as $categorie=>$datumData)
{
  $laatstePerf=0;
  $laatstePerfCumu=0;
  foreach ($datumData as $datum=>$waarden)
  {
    $jaar=substr($datum,0,4);
    if($jaar<>$laatsteJaar)
    {
      $laatstePerf=0;
      $this->jaarWaarden[$categorie][$jaar]['stortingenCumu']=$this->jaarWaarden[$categorie][$laatsteJaar]['stortingenCumu'];
      $this->jaarWaarden[$categorie][$jaar]['resultaatCumu']=$this->jaarWaarden[$categorie][$laatsteJaar]['resultaatCumu'];
    }

    $this->jaarWaarden[$categorie][$jaar]['waarde']=$waarden['waarde'];
    $this->jaarWaarden[$categorie][$jaar]['stortingen']+=$waarden['stortingen'];
    $this->jaarWaarden[$categorie][$jaar]['resultaat']+=$waarden['resultaat'];
    $this->jaarWaarden[$categorie][$jaar]['perf']=((1+$waarden['perf']/100)*(1+$laatstePerf/100)-1)*100;
    $this->jaarWaarden[$categorie][$jaar]['perfCumu']=((1+$waarden['perf']/100)*(1+$laatstePerfCumu/100)-1)*100;
    $this->jaarWaarden[$categorie][$jaar]['stortingenCumu']+=$waarden['stortingen'];
    $this->jaarWaarden[$categorie][$jaar]['resultaatCumu']+=$waarden['resultaat'];
    $this->jaarWaarden[$categorie][$jaar]['datum']=$datum;


    $laatstePerf=$this->jaarWaarden[$categorie][$jaar]['perf'];
    $laatstePerfCumu=$this->jaarWaarden[$categorie][$jaar]['perfCumu'];
    if(!isset($eersteJaar))
      $eersteJaar=$jaar;
    $laatsteJaar=$jaar;
  }
}



$this->pdf->CellBorders=array();
$this->pdf->setY(40);

    $this->pdf->SetFillColor($this->pdf->rapport_kop_kleur[0],$this->pdf->rapport_kop_kleur[1],$this->pdf->rapport_kop_kleur[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->setDrawColor(255,255,255);

    $yStart=$this->pdf->getY();
    $this->pdf->Rect($this->pdf->marge, $yStart+.5, 125, 6, 'F');
    $this->pdf->Rect($this->pdf->marge, $yStart+80+.5, 125, 6, 'F');
    $this->pdf->Rect($this->pdf->marge+150, $yStart+.5, 125, 6, 'F');
    $this->pdf->Rect($this->pdf->marge+150, $yStart+80+.5, 125, 6, 'F');

    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(125));
    $this->pdf->setAligns(array('C'));
    $this->pdf->setXY($this->pdf->marge, $yStart);
    $this->pdf->ln(1.5);
    $this->pdf->Row(array("Ontwikkeling vanaf ".date("j",db2jul($start)).' '.
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($start))],$this->pdf->taal).' '.date("Y",db2jul($start))));
    
    
    

    $this->pdf->setXY($this->pdf->marge, $yStart+80);
    $this->pdf->setWidths(array(125));
    $this->pdf->setAligns(array('C'));
    $this->pdf->ln(1.5);
    $this->pdf->Row(array("Verhandelbaarheid"));
    
    
    $extraX=150;
    $this->pdf->setXY($this->pdf->marge, $yStart+80);
    $this->pdf->setWidths(array($extraX,125));
    $this->pdf->setAligns(array('L','C'));
    $this->pdf->ln(1.5);
    $this->pdf->Row(array('',"Ontwikkeling per beleggingscategorie"));

    $toonenVanaf=$laatsteJaar-12;
    $eersteGetoondeJaar='';
    $n=0;
    foreach($this->jaarWaarden['totaal'] as $jaar=>$waarden)
    {
      if($jaar>$toonenVanaf)
      {
        $eersteGetoondeJaar=$jaar;
        break;
      }
      $n++;
      if($n>100)
        break;

    }
   // if(substr($start,0,4) <> $eersteGetoondeJaar)
   // {
   //   $vanTxt='1 '.vertaalTekst($this->pdf->__appvar["Maanden"][1],$this->pdf->taal).' '.$eersteGetoondeJaar;
   // }
   // else
   // {
      $vanTxt=date("j",db2jul($start)).' '.vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($start))],$this->pdf->taal).' '.date("Y",db2jul($start));
   // }

    $this->pdf->setXY($this->pdf->marge, $yStart);
    $this->pdf->setWidths(array($extraX,125));
    $this->pdf->setAligns(array('L','C'));
    $this->pdf->ln(1.5);
    $this->pdf->Row(array('',"Ontwikkeling vanaf ".$vanTxt));
    $this->pdf->ln(8);

    $this->pdf->SetTextColor(0);
//$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array($extraX,20,25,28,25,27));
$this->pdf->setAligns(array('L','L','R','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->Row(array('','Jaar', 'Totaal vermogen', 'Resultaat', '%', 'Stortingen en onttrekkingen'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$cumulatiefVanaf='';
    $jaarSom=array();
foreach($this->jaarWaarden['totaal'] as $jaar=>$waarden)
{
  if($jaar>$toonenVanaf)
  {
    if(count($jaarSom)>0)
    {
      $jaarSom['waarde'] = $waarden['waarde'];
      $jaarSom['resultaat'] += $waarden['resultaat'];
      $jaarSom['perf'] = $waarden['perfCumu'];
      $jaarSom['stortingen'] += $waarden['stortingen'];
      $this->pdf->Row(array('','t/m '.$jaar,
                        $this->formatGetal($jaarSom['waarde'], 0),
                        $this->formatGetal($jaarSom['resultaat'], 0),
                        $this->formatGetal($jaarSom['perf'], 2),
                        $this->formatGetal($jaarSom['stortingen'], 0)));
      $jaarSom=array();
    }
    else
    {
      $this->pdf->Row(array('', $jaar,
                        $this->formatGetal($waarden['waarde'], 0),
                        $this->formatGetal($waarden['resultaat'], 0),
                        $this->formatGetal($waarden['perf'], 2),
                        $this->formatGetal($waarden['stortingen'], 0)));
    }
  }
  else
  {
    //$jaarSom['waarde']+=$waarden['waarde'];
    $jaarSom['resultaat']+=$waarden['resultaat'];
    $jaarSom['perf']=$waarden['perfCumu'];
    $jaarSom['stortingen']+=$waarden['stortingen'];
    $cumulatiefVanaf=" vanaf $eersteJaar";
  }

  if($jaar>=substr($filterDatumGrafiek,0,4))
  {
    //if($jaar>$toonenVanaf)
      $indexWaardenGrafiek[] = array('jaar' => $jaar, 'waarde' => $waarden['waarde'], 'perfCumu' => $waarden['perfCumu'], 'datum' => $waarden['datum'], 'extraIndices' => $extraIndicesPerformance[$waarden['datum']]);
  }
}
    $this->pdf->setDrawColor($this->pdf->rapport_grijs_kleur[0],$this->pdf->rapport_grijs_kleur[1],$this->pdf->rapport_grijs_kleur[2]);
$cumulatief=$this->jaarWaarden['totaal'][$laatsteJaar];
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array('','','',array('TS'),array('TS'),array('TS'));
$this->pdf->Row(array('','','','','','',));
//$this->pdf->CellBorders = array('','','',array('UU'),array('UU'),array('UU',));
    $this->pdf->setWidths(array($extraX,20+20,5,28,25,27));
$this->pdf->Row(array('','Cumulatief'.$cumulatiefVanaf,'',$this->formatGetal($cumulatief['resultaatCumu'],0),$this->formatGetal($cumulatief['perfCumu'],2),$this->formatGetal($cumulatief['stortingenCumu'],0)));
/*
$this->pdf->CellBorders = array();
$this->pdf->setY(34);
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
$this->pdf->setWidths(array(140,152));
$this->pdf->setAligns(array('C','C'));
$this->pdf->Row(array('',"Ontwikkeling vermogen per beleggingscategorie"));
$this->pdf->ln(8);
$this->pdf->setWidths(array(140,20,21,12,21,12,21,12,21,12));
$this->pdf->setAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
*/

$header=array('',"Jaar\n ");
$headerCat=$categorien;
unset($headerCat['totaal']);

foreach ($headerCat as $categorie=>$omschrijving)
{
 array_push($header,$omschrijving);
 if($categorie!='Liquiditeiten')
   array_push($header,'%');
}
//$this->pdf->Row($header);
//$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//$regel=array();

$col=0;
$tmp=array();
foreach ($this->jaarWaarden as $categorie=>$jaarwaarden)
{
  if($categorie<>'totaal')
  {
    foreach ($jaarwaarden as $jaar => $waarden)
    {
      $tmp[$jaar][$categorie] = array('waarde' => $waarden['waarde'], 'perf' => $waarden['perf'], 'perfCumu' => $waarden['perfCumu']);
    }
  }
}
//listarray($tmp);echo $toonenVanaf;

$this->pdf->CellBorders = array();
foreach ($tmp as $jaar=>$data)
{
  if($jaar>$toonenVanaf)
  {
    $row = array('', $jaar);
    foreach ($headerCat as $categorie=>$omschrijving)
    {
      $waarden = $data[$categorie];
      array_push($row, $this->formatGetal($waarden['waarde'], 0));
      if ($categorie != 'Liquiditeiten')
      {
        array_push($row, $this->formatGetal($waarden['perf'], 2));
      }

      $barData[$jaar][$categorie] = ($waarden['waarde'] / $this->jaarWaarden['totaal'][$jaar]['waarde'] * 100);
      //echo  $barData[$jaar][$categorie]." = (".$waarden['waarde']." / ".$this->jaarWaarden['totaal'][$jaar]['waarde']." * 100) | $jaar | $categorie <br>\n";
    }
   // $this->pdf->Row($row);
  }
}
//listarray($barData);exit;
/*
$this->pdf->CellBorders = array('','','',array('UU'),'',array('UU'),'',array('UU'),'',array('UU'));
    $this->pdf->setWidths(array(140,20+20,1,12,21,12,21,12,21,12));
$row=array('','Cumulatief'.$cumulatiefVanaf);
foreach ($headerCat as $categorie)
{
  $cumulatief=$this->jaarWaarden[$categorie][$laatsteJaar];
  array_push($row,'');
  if($categorie!='Liquiditeiten')
    array_push($row,$this->formatGetal($cumulatief['perfCumu'],2));
}
$this->pdf->ln();
$this->pdf->Row($row);
*/
$this->pdf->CellBorders = array();
  
  
  
    //if($__debug==true)
    //   $verhandelbaarheidVeld='extraVeld1';
    // $categorieen=array('Dagelijks','Wekelijks','Twee-wekelijks','Maandelijks');
  
   
  
    $mogelijkeKleuren=array(array(132,158,173),array(190,190,190),array(206,215,222));
    $aanwezigeKleuren=array('132158173','190190190','206215222');
    foreach($this->alleKleuren as $soort=>$categorieData)
    {
      foreach($categorieData as $cat=>$kleurData)
      {
        if($kleurData['R']['value']<>0 && $kleurData['G']['value']<>0 && $kleurData['B']['value']<>0)
        {
          $kleurString=$kleurData['R']['value'].$kleurData['G']['value'].$kleurData['B']['value'];
          if(in_array($kleurString,$aanwezigeKleuren))
            continue;
          $aanwezigeKleuren[]=$kleurString;
        
          $mogelijkeKleuren[]=array($kleurData['R']['value'],$kleurData['G']['value'],$kleurData['B']['value']);
        }
      }
    }
    $this->mogelijkeKleuren=$mogelijkeKleuren;



$this->LineDiagram(18,40,110,50,$indexWaardenGrafiek,'');//Totaal resultaat


$this->VBarDiagram(160,175,123,50,$barData,'');//Ontwikkeling vermogen
  global $__debug;
    $verhandelbaarheidVeld='Verhandelbaarheid';
  
  
  
  
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $db=new DB();
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();
    $pWaarde = $totaalWaarde['totaal'];
    
    
    $query="SELECT sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
FondsExtraInformatie.".$verhandelbaarheidVeld." as soort,
FondsExtraTrekvelden.volgorde
FROM TijdelijkeRapportage
LEFT JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds=FondsExtraInformatie.fonds
LEFT JOIN FondsExtraTrekvelden ON FondsExtraTrekvelden.trekveld='verhandelbaarheid' AND FondsExtraInformatie.Verhandelbaarheid = FondsExtraTrekvelden.waarde
WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'
AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP by soort ORDER BY volgorde, soort";

  /*
    $query="SELECT sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
FondsExtraInformatie.".$verhandelbaarheidVeld." as soort
FROM TijdelijkeRapportage
LEFT JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds=FondsExtraInformatie.fonds
GROUP by soort ORDER BY soort";*/
    
    $db->SQL($query);
    $db->Query();
    $totaleWaarde=0;
    $verdeling=array();
    $this->pdf->excelData[]=array('Verhandelbaarheid','WaardeEuro');
    while($data=$db->nextRecord())
    {
      if($data['soort']=='')
        $data['soort']='Dagelijks';
      $verdeling[$data['soort']]+=$data['WaardeEuro'];
      $totaleWaarde+=$data['WaardeEuro'];
  
      $this->pdf->excelData[]=array($data['soort'],$data['WaardeEuro']);
    }
    $grafiekData=array();
    $i=0;
    
    if(round($totaleWaarde)<>round($pWaarde))
    {
      echo "Portefeuillewaarde €".round($pWaarde)." is niet gelijk aan €".round($totaleWaarde).". Fondsen dubbel aanwezig in FondsExtraInformatie?";
     
      $query="SELECT
FondsExtraInformatie.fonds,
count(FondsExtraInformatie.id) as aantal
FROM TijdelijkeRapportage
LEFT JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds=FondsExtraInformatie.fonds
LEFT JOIN FondsExtraTrekvelden ON FondsExtraTrekvelden.trekveld='verhandelbaarheid' AND FondsExtraInformatie.Verhandelbaarheid = FondsExtraTrekvelden.waarde
WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'
AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND TijdelijkeRapportage.`type`='fondsen'  ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY FondsExtraInformatie.fonds
HAVING aantal > 1
ORDER BY TijdelijkeRapportage.id";
      $db->SQL($query);
      $db->Query();
      while($data=$db->nextRecord())
      {
listarray($data);
      }
      
      exit;
    }
    foreach($verdeling as $groep=>$waarde)
    {
      $percentage=$waarde/$totaleWaarde;
     // $grafiekData['Omschrijving'][]=$groep." (".$this->formatGetal($percentage*100,1)."%)";
      $grafiekData['Percentage'][$groep]=$percentage*100;
      $grafiekData['Kleur'][]=$mogelijkeKleuren[$i];
      $i++;
    }
  
    $diameter = 50;
    $Xas= 25;
    $yas= 125;
    //$this->pdf->set3dLabels($grafiekData['Omschrijving'],$Xas,$yas+80,$grafiekData['Kleur']);
  

    $this->pdf->setXY($Xas,$yas);
    $legendaStart=$this->correctLegentHeight(count($grafiekData['Percentage']));
    PieChart_L12($this->pdf,$diameter,$diameter,$grafiekData['Percentage'],'%l',$grafiekData['Kleur'],"",$legendaStart);

}
  
  function correctLegentHeight($regels)
  {
    return array($this->pdf->GetX()+60,$this->pdf->GetY()+ 35 -($regels*4)/2);
    
  }


function LineDiagram($x,$y,$w, $h, $data, $title,$color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    //$this->pdf->Rect($x-10,$y-5,$w+15,$h+30);
    $this->pdf->setXY($x,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,$title,'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $legendDatum=array();
    $newData=array();
    $extraIndices=array();
    $fondsOmschrijvingen=array();
    $extraIndicesKleur=array();
    $extraBereik=array('min'=>0,'max'=>0);
    $db=new DB();
   
    foreach($data as $index=>$waarden)
    {
      $legendDatum[$index]=$waarden['jaar'];
      $newData[$index]= $waarden['perfCumu'];
      if(is_array($waarden['extraIndices']))
      {
        $n=0;
        foreach($waarden['extraIndices'] as $fonds=>$rendement)
        {

          $query="SELECT Fondsen.Omschrijving, BeleggingscategoriePerFonds.grafiekKleur FROM Fondsen LEFT JOIN BeleggingscategoriePerFonds ON Fondsen.Fonds=BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' WHERE Fondsen.Fonds='$fonds' ";
          $db->SQL($query);
          $kleurData=$db->lookupRecord();
          $fondsOmschrijvingen[$fonds]=$kleurData['Omschrijving'];
          $extraIndicesKleur[$fonds]=$this->mogelijkeKleuren[$n];

          $extraIndices[$fonds][$index] = $rendement;
          $tmp=unserialize($kleurData['grafiekKleur']);
          if(is_array($tmp))
          {
            $extraIndicesKleur[$fonds] = array($tmp['R']['value'], $tmp['G']['value'], $tmp['B']['value']);
          }
          else
          {
            $extraIndicesKleur[$fonds]=$this->mogelijkeKleuren[$n];
            //$extraIndicesKleur[$fonds] = array(rand(0, 255), rand(0, 255), rand(0, 255));
          }
          if($rendement<$extraBereik['min'])
            $extraBereik['min'] = $rendement;
          if($rendement>$extraBereik['max'])
            $extraBereik['max'] = $rendement;
          $n++;
        }
      }
    }

    $data=$newData;
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

    if($color == null)
      $color=array(0,0,0);
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

    if($extraBereik['max']>$maxVal)
      $maxVal = $extraBereik['max'];
    if($extraBereik['min']<$minVal)
      $minVal = $extraBereik['min'];

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
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

   // $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $this->pdf->Line($XDiag, $YPage, $XDiag ,$YPage+$h,array('dash' => 0,'color'=>array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2])));
    $this->pdf->Line($XDiag, $YPage+$h, $XDiag+$w ,$YPage+$h,array('dash' => 0,'color'=>array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2])));
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
    //  $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 0,'color'=>array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2])));
      $this->pdf->Text($XDiag-8, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
   //   $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 0,'color'=>array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2])));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-8, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }

    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
  
   // $color=array(200,0,0);
    $color=array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2]);
   // $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $aantal=count($data)-1;
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);

    //  $this->pdf->Line($XDiag+($i+1)*$unit+($unit*0.1*-1), $YPage, $XDiag+($i+1)*$unit+($unit*0.1*-1) ,$YPage+$h,array('dash' => 0,'color'=>$color));
  
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
     // $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)// && $aantal==$i)
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'',7);
        $this->pdf->Text($XDiag + ($i + 1) * $unit + $extrax - 4, $yval2 - 2.5, $this->formatGetal($data[$i], 1));
      
      }
      
      $yval = $yval2;
    }
  
   // $this->pdf->Rect($XDiag,$YDiag+$h+11, 1, 1 ,'F','',$color);
    $this->pdf->Text($XDiag+2,$YDiag+$h+12,'Portefeuille');

    $n=0;
  //  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFont($this->pdf->rapport_font,'',7);
    $extraH=0;
   // $indexCounter=0;
    foreach($extraIndices as $index=>$data)
    {
      
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      if(isset($extraIndicesKleur[$index]))
        $color=$extraIndicesKleur[$index];
      else
        $color=array(100,100,100);


      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      $this->pdf->setTextColor($color[0],$color[1],$color[2]);
      $extrax1=0;
     
      for ($i=0; $i<count($data); $i++)
      {
        $extrax=($unit*0.1*-1);
        if($i <> 0)
          $extrax1=($unit*0.1*-1);


        $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
        //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);

        if($data[$i] <> 0 && $aantal==$i)
        {
          $this->pdf->Text($XDiag + ($i + 1) * $unit + $extrax + 1, $yval2 - 2.5, $this->formatGetal($data[$i], 1));
        }
        $yval = $yval2;
      }

      //$this->pdf->Rect($XDiag+($n*20),$YDiag+$h+11, 1, 1 ,'F','',$color);
      $this->pdf->Text($XDiag+20+($n*40),$YDiag+$h+12+$extraH,$fondsOmschrijvingen[$index]);
      if($n==1)
      {
        $extraH+=4;
        $n=0;
      }
      else
      {
        $n++;
      }
   
  //    echo "$n ". ($n%2)."<br>";
  
    
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->setTextColor(0,0,0);
    //exit;
  }


  function VBarDiagram($x,$y,$w, $h, $data,$title='')
  {
    $legendaWidth = 0;
    $this->pdf->setXY($x,$y-$h);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,$title,'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
    $negatieveWaarden=false;
    $minVal = 0;
    $maxPercentage=array();
    $grafiekPunt = array();
    $verwijder=array();
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.01));

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          if($waarde < 0)
          {
            $negatieveWaarden=true;
            //$verwijder[$datum]=$datum;
            $grafiek[$datum][$categorie]=0;
            $grafiekCategorie[$categorie][$datum]=0;
            $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
          }
          else
          {
            $grafiekNegatief[$datum][$categorie]=0;
            $maxPercentage[$datum]+=$waarde;
          }


          if(!isset($colors[$categorie]))
          {
            if($this->attKleuren[$categorie])
              $colors[$categorie]=array($this->attKleuren[$categorie]['R']['value'],$this->attKleuren[$categorie]['G']['value'],$this->attKleuren[$categorie]['B']['value']);
            else
              $colors[$categorie]=array(rand(20,80),rand(20,80),rand(20,250));//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          }
          $n++;
        }
      }

      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($grafiek);
     // $numBars=12;

      $color=array(155,155,155);
      
      $maxVal=100;
      foreach ($this->jaarWaarden['totaal'] as $jaar=>$waarden)
      {
        $maxVal = max($maxVal, $waarden['waarde']);
      }
  
      $maxVal=$maxVal*max($maxPercentage)/100;
      
     // listarray($data);exit;
      $maxVal=round(ceil($maxVal/5000))*5000;
        
     // listarray();
     
     


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda



    // echo "$negatieveWaarden | $minVal $maxVal <br>\n";exit;
    $unit = $hGrafiek / $maxVal * -1;
    if($negatieveWaarden==true)
    {
      
      $minVal=(100-max($maxPercentage))/100*$maxVal;
      $minVal=round(ceil($minVal/5000))*5000;
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
  
      $nulYpos = 0;
    }
      $horDiv = 5;
      $bereik = $hGrafiek/$unit;

    //  $this->pdf->SetFont($this->pdf->rapport_font, '', 6);

      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = (abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;

      $n=0;

    $this->pdf->Line($XstartGrafiek, $YPage, $XstartGrafiek ,$YPage-$hGrafiek,array('dash' => 0,'color'=>array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2])));
    $this->pdf->Line($XstartGrafiek, $nulpunt, $XstartGrafiek+$bGrafiek ,$nulpunt,array('dash' => 0,'color'=>array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2])));
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
       // $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 0,'color'=>array($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2])));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."",0,0,'R');
        $n++;
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

    $this->pdf->setDrawColor($this->pdf->rapport_licht_kleur[0], $this->pdf->rapport_licht_kleur[1], $this->pdf->rapport_licht_kleur[2]);
    /*
    foreach ($grafiek as $datum=>$data)
    {
      $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
      $this->pdf->Line($xval + .5 * $eBaton, $YPage, $xval + .5 * $eBaton, $YPage - $h);
      $i++;
    }
    */
    $this->pdf->setDrawColor(0,0,0);
    $i=0;
    foreach ($grafiek as $datum=>$data)
    {
      $data=array_reverse($data,true);
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
        {
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        }
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit * $this->jaarWaarden['totaal'][$datum]['waarde']/100);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
  

        if( array_sum($colors[$categorie]) > 128*3)
        {
          $this->pdf->SetTextColor(0,0,0);
        }
        else
        {
          $this->pdf->SetTextColor(255,255,255);
        }
        
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
            $this->pdf->Cell($eBaton, 4, number_format($val,0,',','.')."%",0,0,'C');
            $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
         {

           $this->pdf->TextWithRotation($xval + 1, $YstartGrafiek + 6, $legenda[$datum], 25);
         }
         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'F',null,array(128,128,128));
            //if($lastX)
            //  $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            //$lastX = $xval+.5*$eBaton;
           // $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }
  
    $YstartGrafiekLast=array();
    $i=0;
   foreach($grafiekNegatief as $datum=>$data)
   {
     $data=array_reverse($data,true);
     foreach($data as $categorie=>$val)
     {
       if(!isset($YstartGrafiekLast[$datum]))
       {
         $YstartGrafiekLast[$datum] = $YstartGrafiek;
       }
       //Bar
       $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
       $lval = $eBaton;
       $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
       $hval = ($val * $unit * $this->jaarWaarden['totaal'][$datum]['waarde']/100);
    
       $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null,$colors[$categorie]);
       $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
       $this->pdf->SetTextColor(255,255,255);
    
    
       if( array_sum($colors[$categorie]) > 128*3)
       {
         $this->pdf->SetTextColor(0,0,0);
       }
       else
       {
         $this->pdf->SetTextColor(255,255,255);
       }
    
       if(abs($hval) > 3)
       {
         $this->pdf->SetXY($xval, $yval+($hval/2)-2);
         $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
         $this->pdf->Cell($eBaton, 4, number_format($val,0,',','.')."%",0,0,'C');
         $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
       }
       $this->pdf->SetTextColor(0,0,0);
   

     }
     $i++;
   }
   
   $xval=$x+10;
   $yval=$y+16;
   $colors=array_reverse($colors,true);
   foreach ($colors as $cat=>$color)
   {
     $this->pdf->Rect($xval, $yval+1.4, 3, 1, 'F',null,$colors[$cat]);
     $this->pdf->TextWithRotation($xval+5,$yval+2.5,$cat,0);
     $xval=$xval+22;
   }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>