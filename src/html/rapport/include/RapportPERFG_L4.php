<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/01/16 11:02:07 $
 		File Versie					: $Revision: 1.6 $

 		$Log: RapportPERFG_L4.php,v $
 		Revision 1.6  2019/01/16 11:02:07  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/09/05 16:48:04  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/10/26 15:42:47  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/09/28 14:43:25  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/09/25 15:54:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/06 16:00:55  rvv
 		*** empty log message ***

*/

include_once('../indexBerekening.php');


class RapportPERFG_L4
{

  function RapportPERFG_L4($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_PERFGRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERFG_titel;
		else
			$this->pdf->rapport_titel = "Historisch rendement";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  
  
  function maandenNaarKwartalen($maandDataIn)
  {
//listarray($maandData);
    $tmp=array();
    $somVelden=array('stortingen','onttrekkingen','resultaatVerslagperiode','kosten','opbrengsten','ongerealiseerd','rente','gerealiseerd');
    $stapelItems=array('performance');
    $gemiddeldeVelden=array('gemiddelde');
    // listarray($maandDataIn);
    $eersteDag=array();
    $laatsteKwartaal='';
    $lastKwartaal='';
    $laasteJulDatum=0;
    foreach($maandDataIn as $totaalData)
    {
      // $beginJul=db2jul();
      $julDatum=db2jul($totaalData['datum']);
      $kwartaal=ceil(date("m",$julDatum) / 3)." ".date("Y",$julDatum);
      
      //echo $kwartaal." ".$totaalData['periode']."<br>\n";
      if(!isset($eersteDag[$kwartaal]))
        $eersteDag[$kwartaal]=substr($totaalData['periode'],0,10);
      
      if($kwartaal<>$laatsteKwartaal)
      {
        $laatsteDag[$laatsteKwartaal]=date("-m-d",$laasteJulDatum);
      }
      
      $laasteJulDatum=$julDatum;
      $laatsteKwartaal=$kwartaal;
    }
    $laatsteDag[$laatsteKwartaal]=date("-m-d",$laasteJulDatum);
    
    $aantalWaarden=0;
    foreach($maandDataIn as $totaalData)
    {
      $julDatum=db2jul($totaalData['datum']);
      $kwartaal=ceil(date("m",$julDatum) / 3)." ".date("Y",$julDatum);
      $dateBegin=$eersteDag[$kwartaal];
      if($kwartaal <> '')
      {
        
        if($kwartaal <> $lastKwartaal)
        {
          $lastKwartaal='';
          if($lastKwartaal <> '')
          {
            foreach ($gemiddeldeVelden as $item)
              $tmp['perfWaarden'][$kwartaal][$item]=$tmp['perfWaarden'][$kwartaal][$item] /($aantalWaarden+1);
          }
          $aantalWaarden=0;
          
        }
        
        if(!isset($tmp['perfWaarden'][$kwartaal]['waardeBegin']))
          $tmp['perfWaarden'][$kwartaal]['waardeBegin']=$totaalData['waardeBegin'];
        $tmp['perfWaarden'][$kwartaal]['waardeHuidige']=$totaalData['waardeHuidige'];
        $tmp['perfWaarden'][$kwartaal]['index']=$totaalData['index'];
        $tmp['perfWaarden'][$kwartaal]['beginDatum']=$dateBegin;
        $tmp['perfWaarden'][$kwartaal]['eindDatum']=date('Y-m-d',$julDatum);
        
        
        
        
        foreach($somVelden as $veld)
          $tmp['perfWaarden'][$kwartaal][$veld]+=$totaalData[$veld];
        
        foreach ($stapelItems as $item)
          $tmp['perfWaarden'][$kwartaal][$item] = ((($tmp['perfWaarden'][$kwartaal][$item]/100+1)  * ($totaalData[$item]/100+1))-1)*100;
        
        foreach ($gemiddeldeVelden as $item)
          $tmp['perfWaarden'][$kwartaal][$item] += $totaalData[$item];
        
        $lastKwartaal=$kwartaal;
        $aantalWaarden++;
      }
    }
    foreach ($gemiddeldeVelden as $item)
      $tmp['perfWaarden'][$kwartaal][$item] =$tmp['perfWaarden'][$kwartaal][$item]/($aantalWaarden+1);
    //foreach ($stapelItems as $item)
    //   $tmp['perfWaarden'][$kwartaal.$dateEnd][$item] =$tmp['perfWaarden'][$kwartaal.$dateEnd][$item]-1;
    
    
    //listarray($tmp);
    return $tmp;
  }


  function writeRapport()
	{

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    $this->pdf->AddPage();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));

    if(is_array($this->pdf->portefeuilles))
      $portefeuilles="Portefeuille IN ('".implode("','",$this->pdf->portefeuilles)."') AND";
    else
      $portefeuilles="Portefeuille = '".$this->portefeuille."' AND";

    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE $portefeuilles  Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();

if($this->pdf->lastPOST['perfPstart'] == 1)
{
  if($datum['id'] > 0)
  {
    if($datum['month'] <10)
      $datum['month'] = "0".$datum['month'];
    $start = $datum['year'].'-'.$datum['month'].'-01';
  }
  else
    $start=$this->pdf->PortefeuilleStartdatum;
}
else
  $start = $this->rapportageDatumVanaf;

$eind = $this->rapportageDatum;
$datumStart = db2jul($start);
$datumStop  = db2jul($eind);

$index = new indexHerberekening();
//$index->geenCacheGebruik=true;
//$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille,'','kwartaal');
   // echo "-- $start -- <br>\n";
//$indexWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles),'','maanden',$this->pdf->rapportageValuta);
    $indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);
//listarray($indexWaarden);exit;

$kwartaalPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1
$indexWaardenKwartaal = $index->getWaarden(date('Y-m-d',$kwartaalPeriode),$eind,array($this->portefeuille,$this->pdf->portefeuilles),'','maanden',$this->pdf->rapportageValuta);
$tmp=$this->maandenNaarKwartalen($indexWaardenKwartaal);
$indexWaardenKwartaal=$tmp['perfWaarden'];

//listarray($tmp);
//listarray($indexWaardenKwartaal);

if($this->pdf->portefeuilledata['SpecifiekeIndex'] != '')
{
  $lookupDB = new DB();
  $lookupQuery = "SELECT Fondsen.Omschrijving FROM Fondsen WHERE Fondsen.Fonds = '".$this->pdf->portefeuilledata['SpecifiekeIndex']."'";
  $lookupDB->SQL($lookupQuery);
  $lookupRec = $lookupDB->lookupRecord();
  $indexFondsen[]=$this->pdf->portefeuilledata['SpecifiekeIndex'];
  $indexNaam[$this->pdf->portefeuilledata['SpecifiekeIndex']] = $lookupRec['Omschrijving'];
}

$query = "SELECT Indices.Beursindex ,Indices.grafiekKleur
          FROM Indices
          WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ORDER BY Indices.Afdrukvolgorde  ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
	$indexFondsen[] = $data['Beursindex'];
	$indexKleuren[$data['Beursindex']] = unserialize($data['grafiekKleur']);
}

$query = "SELECT BeleggingscategoriePerFonds.grafiekKleur, BeleggingscategoriePerFonds.Fonds
          FROM  BeleggingscategoriePerFonds
          WHERE BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND BeleggingscategoriePerFonds.Fonds IN('".implode("','",$indexFondsen)."') ";
$DB->SQL($query);
$DB->Query();
while ($data = $DB->nextRecord())
{
  if($data['grafiekKleur'] !='')
	  $indexKleuren[$data['Fonds']] = unserialize($data['grafiekKleur']);
}


//listarray($indexKleuren);exit;
//listarray($indexFondsen);

$aantalWaarden = count($indexWaarden);
foreach ($indexWaarden as $id=>$waarden)
{
  $start = jul2sql(form2jul(substr($waarden['periodeForm'],0,10)));
  $eind = jul2sql(form2jul(substr($waarden['periodeForm'],13)));
  foreach ($indexFondsen as $fonds)
  {
 	  $q0 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$eind."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1" ;
 	  $q1 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$start."' AND Fonds = '$fonds'  ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($q0);
	  $DB->Query();
	  $koersEind = $DB->LookupRecord();
	  $DB->SQL($q1);
	  $DB->Query();
	  $koersStart = $DB->LookupRecord();
	  $perf = $koersEind['Koers'] /$koersStart['Koers']  ;
	  if($perf==0)
      $perf =1;
    $indexWaarden[$id]['fondsPerf'][$fonds] = $perf  ;

  //  echo "$eind $fonds $perf <br>";

    if(empty($indexWaarden[$id-1]['fondsIndex'][$fonds]))
	    $indexWaarden[$id]['fondsIndex'][$fonds] = $indexWaarden[$id]['fondsPerf'][$fonds];
	  else
  	  $indexWaarden[$id]['fondsIndex'][$fonds]  =($indexWaarden[$id]['fondsPerf'][$fonds]*$indexWaarden[$id-1]['fondsIndex'][$fonds]);

    $jaar=substr($eind,0,4);

   	if(empty($indexTabel['cumulatief'][$fonds]['jaren']))
   	  $indexTabel['cumulatief'][$fonds]['jaren']=100;

   	if(empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
   	   $indexTabel['cumulatief'][$fonds]['cumulatief']=100;

    $indexTabel['cumulatief'][$fonds]['jaren']      = ($indexTabel['cumulatief'][$fonds]['jaren']*($perf*100))/100;
    $indexTabel['cumulatief'][$fonds]['cumulatief'] = ($indexTabel['cumulatief'][$fonds]['cumulatief']*($perf*100))/100;
    $indexTabel[$jaar][$fonds]['jaar'] = $indexTabel['cumulatief'][$fonds]['jaren'];

    if(substr($eind,5,5) == '12-31' || $aantalWaarden == $id)
    {
      $indexTabel['cumulatief'][$fonds]['jaren'] = 100;
      $indexTabel[$jaar][$fonds]['cumulatief'] = $indexTabel['cumulatief'][$fonds]['cumulatief'];
    }
  }
}

$kwartaalPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1
$n=0;
$minVal = 99;
$maxVal = 101;
$aantalWaarden=count($indexWaarden);
foreach ($indexWaarden as $id=>$data)
{ //echo "". $data['datum']." ".$data['performance']."<br>\n";
  if($data['performance'] == -100)
    $data['performance']=0;

  $jaar=substr($data['datum'],0,4);
  $juldate=db2jul($data['datum']);
  if($juldate < $kwartaalPeriode)
  {
    if(empty($jaarPerf[$jaar]))
      $jaarPerf[$jaar]=100;
    $jaarPerf[$jaar] =($jaarPerf[$jaar]*(100+$data['performance'])/100);

    if(!isset($jaarWaarden[$jaar]['waardeBegin']))
      $jaarWaarden[$jaar]['waardeBegin']=$data['waardeBegin'];

    $jaarWaarden[$jaar]['stortingen']+=$data['stortingen'];
    $jaarWaarden[$jaar]['onttrekkingen']+=$data['onttrekkingen'];

    if(substr($data['datum'],5,5)=='12-31')
    {
       $grafiekData['jaren']['portefeuille'][]=$jaarPerf[$jaar]-100;
       $grafiekData['jaren']['datum'][]= $jaar;
       $tmp=array_merge($data,$jaarWaarden[$jaar]);
       $grafiekData['jaren']['waarde'][]=$tmp;
    }
  }
  else
  {
    //$grafiekData['kwartalen']['portefeuille'][]=$data['performance'];
   // $grafiekData['kwartalen']['datum'][]= "Q".(floor(date("m",$juldate)/4)+1)."-".date("Y",$juldate);
   // $grafiekData2['portefeuille'][]=$data['waardeHuidige'];
   // $grafiekData['kwartalen']['waarde'][]=$data;
  }

  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] = ($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$jaar]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];
  if(substr($data['datum'],5,5) == '12-31' || $aantalWaarden == $id)
  {
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;
    $indexTabel[$jaar]['portefeuille']['cumulatief'] = $data['index'];
  }
}

foreach ($indexWaardenKwartaal as $id=>$data)
{
  if($data['performance'] == -100)
    $data['performance']=0;

  $jaar=substr($data['eindDatum'],0,4);
  $juldate=db2jul($data['eindDatum']);
  $grafiekData['kwartalen']['portefeuille'][]=$data['performance'];
  //$grafiekData['kwartalen']['datum'][]= "Q".(floor(date("m",$juldate)/4)+1)."-".date("Y",$juldate);
  $grafiekData['kwartalen']['datum'][]= "Q".(ceil(date("m",$juldate)/3))."-".date("Y",$juldate);
  $grafiekData2['portefeuille'][]=$data['waardeHuidige'];
  $grafiekData['kwartalen']['waarde'][]=$data;
}





$this->pdf->CellBorders=array();
$this->pdf->setY(40);
$this->pdf->ln();
$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array(26,30,28,28,28,28));
$this->pdf->setAligns(array('L','R','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->Row(array('periode', 'beginvermogen', 'stortingen', 'onttrekkingen', 'resultaat', 'eindvermogen'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
foreach($grafiekData['kwartalen']['datum'] as $i=>$datum)
{
   $this->pdf->Row(array($datum,
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeBegin'],2),
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['stortingen'],2),
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['onttrekkingen'],2),
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige']-$grafiekData['kwartalen']['waarde'][$i]['waardeBegin']+$grafiekData['kwartalen']['waarde'][$i]['onttrekkingen']-$grafiekData['kwartalen']['waarde'][$i]['stortingen'],2),
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'],2)));
}
$this->pdf->CellBorders = array();

$YendIndex = $this->pdf->GetY();
$this->pdf->setXY(190,80);
$color=array(244,90,74);
$color=array(200,30,12);
$color=array(204,51,5);
$this->VBarDiagram(90,40,$grafiekData['kwartalen'],'',$color);

$this->pdf->setY(85);
$this->pdf->ln();
$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array(26,30,28,28,28,28));
$this->pdf->setAligns(array('L','R','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->Row(array('periode', 'beginvermogen', 'stortingen', 'onttrekkingen', 'resultaat', 'eindvermogen'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
foreach($grafiekData['jaren']['datum'] as $i=>$datum)
{
   $this->pdf->Row(array($datum,
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeBegin'],2),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['stortingen'],2),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['onttrekkingen'],2),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige']-$grafiekData['jaren']['waarde'][$i]['waardeBegin']+$grafiekData['jaren']['waarde'][$i]['onttrekkingen']-$grafiekData['jaren']['waarde'][$i]['stortingen'],2),
   $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige'],2)));
}
$this->pdf->CellBorders = array();
////
$this->pdf->setXY(190,140);
$this->VBarDiagram(90,50,$grafiekData['jaren'],'',$color);
$this->pdf->ln();

if($this->pdf->portefeuilledata['SpecifiekeIndex'] <> '')
  $indexTabelFondsen = array('Portefeuille'=>'portefeuille',$this->pdf->portefeuilledata['SpecifiekeIndex']=>$this->pdf->portefeuilledata['SpecifiekeIndex']);
else
  $indexTabelFondsen = array('Portefeuille'=>'portefeuille');


$tmpArray0 = array('','');
$tmpArray1 = array('','Jaar');
foreach ($indexTabelFondsen as $fondsOmschrijving=>$fonds)
{
  array_push($tmpArray0,($indexNaam[$fonds] <> ""?"benchmark":$fonds));
//  array_push($tmpArray0,($indexNaam[$fonds] <> ""?$indexNaam[$fonds]:$fonds));
  array_push($tmpArray1,"per jaar");
  array_push($tmpArray1,"cumulatief");
}

$this->pdf->setXY(5,135);
$this->pdf->Row(array(''));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->setAligns(array('L','L','C','C'));
$this->pdf->CellBorders = array('',array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
$this->pdf->setWidths(array(0,20,40,40));
$this->pdf->Row($tmpArray0);
$this->pdf->CellBorders = array('',array('U','L','R'),'U',array('U','R'),'U',array('U','R'));
$this->pdf->setWidths(array(0,20,20,20,20,20));
$this->pdf->setAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$this->pdf->Row($tmpArray1);

foreach ($indexTabel as $datum=>$fondsen)
{
  if(is_numeric($datum))
  {
    $tmpArray = array('');
    array_push($tmpArray,$datum);
    foreach ($indexTabelFondsen as $fonds)
    {
      $waarden = $indexTabel[$datum][$fonds];
      if(in_array($fonds,$indexTabelFondsen))
      {
        if(!empty($waarden['jaar']))
          array_push($tmpArray,$this->formatGetal(($waarden['jaar']-100),1)."%");
        else
          array_push($tmpArray,"0,0%");

        if(!empty($waarden['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($waarden['cumulatief']-100),1)."%");
        elseif(!empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
          array_push($tmpArray,$this->formatGetal(($indexTabel['cumulatief'][$fonds]['cumulatief']-100),1)."%");
        else
          array_push($tmpArray,"");
      }
    }
    $this->pdf->Row($tmpArray);
  }
}
$this->pdf->CellBorders=array();




	}



  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      $data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0)
        $maxVal = ceil(max($data));
      $minVal = floor(min($data));

      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.2;

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

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
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
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
        $n++;
        if($n >20)
          break;
      }

      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

      $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
      $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $val)
      {
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
      }

      //datum onder grafiek
      foreach ($legendDatum as $i=>$datum)
      {
       $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
       $this->pdf->SetXY($xval,$YstartGrafiek);
       $this->pdf->Cell($eBaton, 4,$datum,0,0,'C');
      }
  }
}
?>
