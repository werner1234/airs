<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/02/04 15:47:34 $
File Versie					: $Revision: 1.6 $

$Log: RapportPERFG_L102.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFG_L102
{
	function RapportPERFG_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Historisch rendement - lange termijn";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
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

	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];


    
    $q="SELECT Fonds,Omschrijving FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $this->specifiekeIndex = $DB->LookupRecord();
   

$DB = new DB();
$query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
$DB->SQL($query);
$DB->Query();
$datum = $DB->nextRecord();


if($datum['id'] > 0 && $this->pdf->lastPOST['perfPstart'] == 1)
{
  if($datum['month'] <10)
    $datum['month'] = "0".$datum['month'];
  $start = $datum['year'].'-'.$datum['month'].'-01';
}
else
  $start = substr($this->pdf->PortefeuilleStartdatum,0,10);
$eind = $this->rapportageDatum;

$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$index = new indexHerberekening();
$maanden=$index->getMaanden(db2jul($start),db2jul($eind));
$indexData = $index->getWaarden($start,$eind,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'maanden',$this->pdf->rapportageValuta);

$eindMaandDag=substr($eind,5,5);
$aantalWaarden=count($indexData);
$startIndex=$aantalWaarden-36;
    $kwartaalP=0;
    $kwartaalPBruto=0;
    $kwartaalB=0;
    $kwartaalB2=0;
    $drieP=0;
    $drieB=0;
    $drieB2=0;
    $driePBruto=0;
    $benchmark=$this->benchmarkBerekening($maanden);

    
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $data['performanceBruto']=($data['resultaatVerslagperiode']-$data['kosten'])/$data['gemiddelde']*100;
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
    $b2Perf=$benchmark[$data['datum']]['perf'];
  
    $kwartaalP=((1+$data['performance']/100)*(1+$kwartaalP/100)-1)*100;
    $kwartaalPBruto=((1+$data['performanceBruto']/100)*(1+$kwartaalPBruto/100)-1)*100;
    $kwartaalB=((1+$data['specifiekeIndexPerformance']/100)*(1+$kwartaalB/100)-1)*100;
    $kwartaalB2=((1+$b2Perf/100)*(1+$kwartaalB2/100)-1)*100;
    $maandDag=substr($data['datum'],5,5);
    if($maandDag=='03-31'||$maandDag=='06-30'||$maandDag=='09-30'||$maandDag=='12-31'||$maandDag==$eindMaandDag)
    {
      $eersteGrafiek[db2jul($data['datum'])]['portefeuille']=$kwartaalP;
      $eersteGrafiek[db2jul($data['datum'])]['benchmark']=$kwartaalB;
  
      $eersteGrafiek2[db2jul($data['datum'])]['portefeuille']=$kwartaalPBruto;
      $eersteGrafiek2[db2jul($data['datum'])]['portefeuille2']=$kwartaalP;
      $eersteGrafiek2[db2jul($data['datum'])]['benchmark']=$kwartaalB2;
    }
    if($index>$startIndex)
    {
      
      $drieP=((1+$data['performance']/100)*(1+$drieP/100)-1)*100;
      $driePBruto=((1+$data['performanceBruto']/100)*(1+$driePBruto/100)-1)*100;
      $drieB=((1+$data['specifiekeIndexPerformance']/100)*(1+$drieB/100)-1)*100;
      $drieB2=((1+$b2Perf/100)*(1+$drieB2/100)-1)*100;
      
      $driejarenGrafiek[db2jul($data['datum'])]['portefeuille']=$drieP;
      $driejarenGrafiek[db2jul($data['datum'])]['benchmark']=$drieB;
  
      $driejarenGrafiek2[db2jul($data['datum'])]['portefeuille']=$driePBruto;
      $driejarenGrafiek2[db2jul($data['datum'])]['portefeuille2']=$drieP;
      $driejarenGrafiek2[db2jul($data['datum'])]['benchmark']=$drieB2;
    }
    
    
  }
}
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
   //$this->pdf->subtitel=date('d-m-Y',db2jul($start))." t/m ".date('d-m-Y',$this->pdf->rapport_datum);
    $this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date("j",db2jul($start))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($start))],$this->pdf->rapport_taal)." ".date("Y",db2jul($start))." ".vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    
    $grafiekX=260;
    $this->pdf->setXY(20,25);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->MultiCell($grafiekX,4,vertaalTekst('Huidig rendement',$this->pdf->rapport_taal).' (%)',0,'C');
    $this->pdf->setXY(20,100);
    $this->VBarDiagram2($grafiekX,55,$eersteGrafiek,true);
    
    $this->pdf->setXY(20,125);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->MultiCell($grafiekX,4,vertaalTekst('Huidig rendement',$this->pdf->rapport_taal).' (%)',0,'C');
    $this->pdf->setXY(20,175);
    $this->VBarDiagram2($grafiekX,55,$eersteGrafiek2,true,true);
    
    $this->pdf->rapport_titel = "Historische rendement - kortere termijn";
    $this->pdf->addPage();
    
    $grafiekX=260;
    $this->pdf->setXY(20,25);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->MultiCell($grafiekX,4,vertaalTekst('Huidig rendement',$this->pdf->rapport_taal).' (%)',0,'C');
    $this->pdf->setXY(20,100);
    $this->VBarDiagram2($grafiekX,55,$driejarenGrafiek,true);
    
    $this->pdf->setXY(20,125);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->MultiCell($grafiekX,4,vertaalTekst('Huidig rendement',$this->pdf->rapport_taal).' (%)',0,'C');
    $this->pdf->setXY(20,175);
    $this->VBarDiagram2($grafiekX,55,$driejarenGrafiek2,true,true);


	   $this->pdf->fillCell = array();


		if($this->extraVulling)
		{
	   // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
		}

	}
  
  function benchmarkBerekening($perioden=array(),$maandwaarden=true)
  {
    
    $db=new DB();
    if(is_array($this->pdf->portefeuilles) && count($this->pdf->portefeuilles)>0 && $this->viaKERNZ==false)
      $portefeuilles=$this->pdf->portefeuilles;
    else
      $portefeuilles=array($this->portefeuille);
    
    if(count($perioden)==0)
    {
      $vanaf=$this->pdf->rapport_datumvanaf;
      $tot=$this->pdf->rapport_datum;
      $index=new indexHerberekening();
      $maanden=$index->getMaanden($vanaf,$tot);
      $lossePeriode=false;
      
    }
    else
    {
      $maanden=$perioden;
      foreach($maanden as $periode)
      {
        if(!isset($vanaf))
          $vanaf=db2jul($periode['start']);
        $tot=db2jul($periode['stop']);
      }
      $lossePeriode=true;
    }
    
    
    
    $begin=date('Y-m-d',$vanaf);
    $eind=date('Y-m-d',$tot);
    $perfReeksTotaal=array();
    $maandperfReeksTotaal=array();
    $perfCategorie=array();
    $perfReeksTotaalEnkel=array();
    
    if(count($portefeuilles)<2 && $this->crmData['SpecifiekeIndex']<>'')//$this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
    {
      foreach ($maanden as $periode)
      {
        $fonds = getSpecifiekeIndex($this->portefeuille, $periode['stop']);
        $verdeling = getFondsverdeling($fonds);
        $perf = getFondsPerformance($verdeling, $begin, $periode['stop']);
        $perfReeksTotaalEnkel[$periode['stop']] = $perf;
      }
    }
    
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];

      $query = "SELECT
NormwegingPerBeleggingscategorie.Portefeuille,
NormwegingPerBeleggingscategorie.Beleggingscategorie as categorie,
NormwegingPerBeleggingscategorie.Normweging,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Beleggingscategorien.Omschrijving as categorieOmschrijving
FROM
NormwegingPerBeleggingscategorie
INNER JOIN KeuzePerVermogensbeheerder ON NormwegingPerBeleggingscategorie.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND
KeuzePerVermogensbeheerder.vermogensbeheerder = '$beheerder' AND KeuzePerVermogensbeheerder.categorie = 'Beleggingscategorien'
INNER JOIN Beleggingscategorien ON NormwegingPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE Portefeuille='" . $this->portefeuille . "' ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
    
    $db->SQL($query);
    $db->Query();
    while($cat = $db->nextRecord())
    {
      $normWeging[$cat['categorie']]=0;//$cat['Normweging']/100;
    }
    
    $query="SELECT id FROM IndexPerBeleggingscategorie WHERE IndexPerBeleggingscategorie.Portefeuille IN('".$this->portefeuille."')";
    $records=$db->QRecords($query);
    if($records>0)
    {
      $gebruiktePortefeuilles=array($this->portefeuille);
    }
    else
    {
      $gebruiktePortefeuilles=$portefeuilles;
      $gebruiktePortefeuilles[]=$this->portefeuille;
    }
    //listarray($query);listarray($gebruiktePortefeuilles);exit;
    //listarray($query);listarray($normWeging);ob_flush();
    $totaleWaarde=array();
    $waardePerPortefeuilleCategorie=array();
    if(count($portefeuilles)>0)
    {
      
      foreach($normWeging as $categorie=>$norm)
      {
        $perf=0;
        
        foreach ($maanden as $periode)
        {
          $normWeging[$categorie]=0;
          $query="SELECT DatumVanaf FROM NormwegingPerBeleggingscategorie WHERE Portefeuille='" . $this->portefeuille . "' AND DatumVanaf <='". $periode['start']."' ORDER BY DatumVanaf desc limit 1 ";
          $db->SQL($query);
          $db->Query();
          $datum=$db->nextRecord();
          $query = "SELECT
NormwegingPerBeleggingscategorie.Portefeuille,
NormwegingPerBeleggingscategorie.Beleggingscategorie as categorie,
NormwegingPerBeleggingscategorie.Normweging
FROM
NormwegingPerBeleggingscategorie
WHERE Beleggingscategorie='" . mysql_real_escape_string($categorie) . "' AND Portefeuille='" . $this->portefeuille . "' AND DatumVanaf ='". $datum['DatumVanaf']."' ORDER BY DatumVanaf desc limit 1 ";
          $db->SQL($query);
          $db->Query();
          while($cat = $db->nextRecord())
          {
            $normWeging[$cat['categorie']]=$cat['Normweging']/100;
          }
          
          
          $datum=$periode['stop'];
          $query = "SELECT
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds,
IndexPerBeleggingscategorie.vanaf,
IndexPerBeleggingscategorie.Portefeuille
FROM IndexPerBeleggingscategorie
WHERE Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND
(IndexPerBeleggingscategorie.Portefeuille IN('".implode("','",$gebruiktePortefeuilles)."') OR IndexPerBeleggingscategorie.Portefeuille='') AND
(IndexPerBeleggingscategorie.vanaf <='$datum')
AND IndexPerBeleggingscategorie.Beleggingscategorie='" . mysql_real_escape_string($categorie) . "'
ORDER BY IndexPerBeleggingscategorie.Portefeuille desc, IndexPerBeleggingscategorie.vanaf desc limit 1";
          $db->SQL($query);
          $db->Query();
          $fondsData = $db->nextRecord();
          //echo "$query <br>\n"; listarray($fondsData);
          $verdeling = getFondsverdeling($fondsData['Fonds']);
          //    $perf = getFondsPerformance($verdeling, $begin, $periode['stop']);
          
          $tmp=getFondsPerformance($verdeling, $periode['start'], $periode['stop']);
          $perf=((1+$perf)*(1+$tmp/100))-1;
          
          $perfCategorie[$categorie]=$perf*100;
          
          $perfReeksTotaal[$datum]+=$perf * $normWeging[$categorie] *100 ;
          if($lossePeriode==true)
          {
            //echo "$categorie $tmp * ".$normWeging[$categorie]."<br>\n";
            $maandperfReeksTotaal[$datum]['perf'] += $tmp * $normWeging[$categorie];
            $maandperfReeksTotaal[$datum]['start'] = $periode['start'];
            $maandperfReeksTotaal[$datum]['datum'] = $periode['stop'];
          }
          
        }
      }
      
      //  listarray($perfCategorie);exit;
      // listarray($perfReeksTotaal);
      // listarray($waardePerPortefeuilleCategorie);
      if($lossePeriode==true)
      {
        $this->langePeriodeBenchmark=$perfReeksTotaal;
        if($maandwaarden==true)
        {
          return $maandperfReeksTotaal;
        }
        if(count($perfReeksTotaal)>0)
          return $perfReeksTotaal;
        elseif(count($perfReeksTotaalEnkel)>0)
          return $perfReeksTotaalEnkel;
        
        
      }
      $this->benchmarkCategoriePerf=$perfCategorie;
      if(count($perfReeksTotaalEnkel)>0)
        $this->perfReeksTotaal=$perfReeksTotaalEnkel;
      else
        $this->perfReeksTotaal=$perfReeksTotaal;
    }
    
    
    
    
  }
  
  
  function VBarDiagram2($w, $h, $data,$metLijn=false,$bruto=false,$portefeuilles=false)
  {
    global $__appvar;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1);
    
    //$this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.1);
    
    $maxVal=0;
    $minVal=0;
    $maanden=array();
    $aantalStaven=0;
  
    foreach($data as $maand=>$maandData)
    {
      if($aantalStaven==0)
        $aantalStaven=count($maandData);
      $maanden[$maand]=$maand;
      foreach($maandData as $type=>$waarde)
      {
        if($type=='portefeuille2')
          continue;
        if($waarde > $maxVal)
          $maxVal = $waarde;
        if($waarde < $minVal)
          $minVal = $waarde;
      }
    }
    if($metLijn==true)
      $aantalStaven=$aantalStaven/2;
    if($maxVal > 1)
      $maxVal=ceil($maxVal);
    if($minVal < -1)
      $minVal=floor($minVal);
    $minVal = $minVal * 1.1;
    $maxVal = $maxVal * 1.1;
    if ($maxVal <0)
      $maxVal=0;
    
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
    
    $horDiv = 10;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
  
    if($portefeuilles==false)
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
  
    $stapgrootte = ceil(ceil(ceil(abs($bereik)/10)*10)/$horDiv*10)/10;

    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XstartGrafiek-9, $i, -1*$n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XstartGrafiek-9, $i, $n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }
    
    $numBars=count($data);
   
    
    $this->pdf->SetFillColor();
  
    if($bruto==true)
    {
      $colors = array('portefeuille' => array(238, 55, 21),
                      'benchmark'    => array(150,150,150));//,'totaalEffect'=>array(0, 52, 121)); //
    }
    else
    {
      $colors = array('portefeuille' => array(0, 98, 143),
                      'benchmark'    => array(150, 150, 150));//,'totaalEffect'=>array(0, 52, 121)); //
    }
    
    
    $vBar = ($bGrafiek /$numBars/2); //4
    $bGrafiek = $vBar * ($numBars*2);
    $eBaton = ($vBar * 80 / 100);
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
  
    if($portefeuilles==false)
     $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $lastXY=array();
    foreach($data as $periode=>$maandData)
    {
      
      foreach($maandData as $type=>$val)
      {
        $color=$colors[$type];
        $legenda[$type]=$color;
        if($metLijn==true)
        {
          //listarray($type);
          if($type=='portefeuille' || $type=='portefeuille2')
          {
            continue;
          }
          
        }
        
        
        
        //Bar
        $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiek + $nulYpos;
        $hval = ($val * $unit);
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
        
       // $this->pdf->SetTextColor(180,180,180);
        if(abs($hval) > 3 && $eBaton > 4 || 1)
        {
          if($val <= 0)
            $extraY=0;
          else
            $extraY=-4;
          $this->pdf->SetXY($xval, $yval+$hval+$extraY);//+($hval/2)
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        /*   */
        $i++;
      }
      $i++;
      if(strlen($periode)==4)
        $xLegenda=$periode;
      else
        $xLegenda=date('M-Y',$periode);
      if($portefeuilles==false)
        $this->pdf->TextWithRotation($XstartGrafiek + ($i-1) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,$xLegenda,20);
      
    }
    
    $xPos=$XstartGrafiek;
    $vorigeWaarde=array();
    foreach($data as $periode=>$maandData)
    {
      
      foreach($maandData as $type=>$val)
      {

        if($metLijn==true)
        {
          //listarray($type);
          if($type=='portefeuille')
          {
            //echo "$type $periode $val <br>\n";
            
            
            $color=$colors[$type];
            if(!isset($lastXY[$type]))
              $lastXY[$type]=array($XstartGrafiek,$YstartGrafiek + $nulYpos);
            
            $newXY=array($xPos + $vBar ,$YstartGrafiek + $nulYpos+($val * $unit));
            
            //if($type=='benchmarkCumu')
            //  $this->pdf->Line($lastXY[$type][0], $lastXY[$type][1] , $newXY[0],$newXY[1] ,array('dash' => "1,2",'color'=>array(0,0,0)));
            //else
              $this->pdf->Line($lastXY[$type][0], $lastXY[$type][1] , $newXY[0],$newXY[1] ,array('dash' => 0,'color'=>$color,'width'=>0.5));
            $this->pdf->setDash(0);
            $this->pdf->setDrawColor(0);
            $this->pdf->Rect($newXY[0]-0.5, $newXY[1]-0.5 , 1, 1, 'F',null,$color);
  
            $this->pdf->SetXY($newXY[0]-5,$newXY[1]);//+($hval/2)
            $this->pdf->Cell(10, 4, number_format($val,1,',','.')."%",0,0,'C');
  
            $lastXY[$type]=array( $newXY[0],$newXY[1]);
            $xPos+=$vBar;
          }
          
          $this->pdf->SetLineWidth(0.1);
          if(isset($maandData['portefeuille2']))
          {
          
            if($type=='benchmark')
              $extraY=8;
            elseif ($type=='portefeuille2')
            {
              $extraY = 4;
              $legendaExtraY = 4;
            }
            else
              $extraY=0;
              
            $this->pdf->setXY($xPos - $vBar, $YstartGrafiek + 4 + $extraY);
          }
          else
          {
            $this->pdf->setXY($xPos - $vBar, $YstartGrafiek + 4 + ($type == 'benchmark'?4:0));
          }
          $herrekendeWaarde=((1+$val/100)/(1+$vorigeWaarde[$type]/100)-1)*100;
          $this->pdf->Cell($vBar*2,$h=4,$this->formatGetal($herrekendeWaarde,1),$border=1,$ln=0,$align='C',$fill=0,$link='');
          //echo "$type $val ".$herrekendeWaarde."<br>\n";
          $vorigeWaarde[$type]=$val;
        }
        
      }
      $xPos+=$vBar;
    }
    
    $n=0;
    if($bruto==true)
      $omschrijvingen=array('portefeuille'=>'Bruto rendement portefeuille','benchmark'=>'Gekoppelde benchmark');//.($benchmarkOmschrijving<>''?': '.$benchmarkOmschrijving:''));
    else
      $omschrijvingen=array('portefeuille'=>'Netto rendement portefeuille','benchmark'=>'Absolute doelstelling '.($this->specifiekeIndex['Omschrijving']<>''?': '.$this->specifiekeIndex['Omschrijving'].' na kosten en fiscaliteit':''));
    
    if($portefeuilles==false)
    {
      $this->pdf->setDash(0);
      $this->pdf->SetLineWidth(0.1);
      if(isset($legendaExtraY))
      {
        $legendaExtraY+=15;
        $benchmarkExtraY=4;
      }
      else
      {
        $legendaExtraY=15;
        $benchmarkExtraY=0;
      }
      foreach ($legenda as $type => $kleur)
      {
        if ($type == 'benchmark')
        {
          $this->pdf->Rect($XstartGrafiek + 6 + ($n * 40), $YstartGrafiek + $legendaExtraY , 2, 2, 'DF',null, $kleur);
          $this->pdf->Rect($XstartGrafiek - 4, $YstartGrafiek + $benchmarkExtraY + 9, 2, 2, 'DF',null, $kleur);
        }
        else if ($type == 'portefeuille')
        {
          $this->pdf->Line($XstartGrafiek + 6 + ($n * 40), $YstartGrafiek + $legendaExtraY + 1, $XstartGrafiek + 8, $YstartGrafiek + $legendaExtraY + ($n * 4) + 1, array('dash' => 0, 'color' => array(0, 0, 0)));
          $this->pdf->Line($XstartGrafiek - 4, $YstartGrafiek + 6, $XstartGrafiek - 2, $YstartGrafiek + 6, array('dash' => 0, 'color' => array(0, 0, 0)));
        }
        $this->pdf->Text($XstartGrafiek + 10 + ($n * 40), $YstartGrafiek + $legendaExtraY + 1.5, vertaalTekst($omschrijvingen[$type], $this->pdf->rapport_taal));
        $n++;
      }
    }
    else
    {
      $i=0;
      foreach($data as $portefeuille=>$grafiekData)
      {
        //$this->pdf->line($XstartGrafiek+$vBar+ ($i*$vBar*2)-$eBaton/2,50,$XstartGrafiek+$vBar + ($i*$vBar*2),150);
        $this->pdf->TextWithRotation($XstartGrafiek+$vBar+ ($i*$vBar*2)-$eBaton/2+2, $YstartGrafiek + 50, $portefeuille, 80);
        $i++;
      }
    }
    
    
    // $color=array(155,155,155);
    // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
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
    $margin = 2;
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
      $color=array(0,38,84);
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
    $unit = $lDiag / count($data);

    if($jaar && count($data) < 12)
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

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
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
    $lineStyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
   
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
        
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
      $yval = $yval2;
    }

    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        
        $this->pdf->Text($XDiag+($i+1)*$unit-0.5,$yval2-4.5,$data1[$i]);
         
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


  function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 00;
      $grafiekPunt = array();
      $verwijder=array();

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
          $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
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
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);

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