<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/06/30 17:43:55 $
 		File Versie					: $Revision: 1.23 $

 		$Log: RapportATT_L49.php,v $
 		Revision 1.23  2018/06/30 17:43:55  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2017/07/23 13:36:28  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2017/06/25 14:49:37  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2017/05/17 15:57:50  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2017/05/13 16:27:34  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2017/04/05 15:39:45  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2015/04/15 18:25:11  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/12/29 13:55:30  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/12/28 14:29:08  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2014/12/13 19:24:44  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2014/08/16 15:31:50  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2014/05/07 15:40:17  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/04/26 16:43:08  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/04/23 19:02:09  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/04/23 16:18:44  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/04/05 15:33:48  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/04/02 15:53:15  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/03/27 14:59:18  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/03/22 15:47:14  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/03/19 16:39:09  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/12/18 17:10:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/12/14 17:16:30  rvv
 		*** empty log message ***
 		
*

*/

include_once('../indexBerekening.php');


class RapportATT_L49
{

  function RapportATT_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);



		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function writeRapport()
	{

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    
    $this->pdf->AddPage();
    checkPage($this->pdf);
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->setY($this->pdf->rapportYstart+2); 
    $this->pdf->Cell(100,4,'Beleggingsresultaat');
    
    
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    $this->pdf->Line($this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3,297-$this->pdf->marge,$this->pdf->rapportYstart+$this->pdf->rowHeight+3);
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    if(is_array($this->pdf->portefeuilles))
      $portefeuilles="Portefeuille IN ('".implode("','",$this->pdf->portefeuilles)."') AND";
    else
      $portefeuilles="Portefeuille = '".$this->portefeuille."' AND";


    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE $portefeuilles  Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();

//$this->pdf->lastPOST['perfPstart']=1;
if($this->pdf->lastPOST['perfPstart'] == 1 || 1)
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
$indexWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles),$this->pdf->portefeuilledata['SpecifiekeIndex'],'kwartaal');

$stopJaar=date("Y",$datumStop);
$startJaar=date("Y",$datumStart);
if(($stopJaar-$startJaar) > 3)
{
 $filterJaar=$stopJaar-3;
}
else
{
  $filterJaar=$startJaar;
}


//new

$query="SELECT Fonds FROM IndexPerBeleggingscategorie WHERE Portefeuille='".$this->portefeuille."' AND Portefeuille <> ''";
$DB->SQL($query);
$DB->Query();
$index = $DB->nextRecord();
$extraIndex=$index['Fonds'];  
//echo $extraIndex." ".$this->pdf->portefeuilledata['SpecifiekeIndex'],"<br>\n";

/*
if(isset($this->pdf->portefeuilles) && is_array($this->pdf->portefeuilles))
{
  // $extraIndex='';
}
*/

$kwartaalPeriode=mktime(0,0,0,1,1,date("Y",$datumStop));//-1

$n=0;
$minVal = 99;
$maxVal = 101;
$aantalWaarden=count($indexWaarden);
foreach ($indexWaarden as $id=>$data)
{
  if($data['performance'] == -100)
    $data['performance']=0;
    $pstart=substr($data['periode'],0,10);
    $pstop=substr($data['periode'],12,10);

  $indexData['fondsKoers_start']=$this->getFondsKoers($extraIndex,$pstart);
  $indexData['fondsKoers_stop']=$this->getFondsKoers($extraIndex,$pstop);
  $indexData['benchmarkBeheerder'] = ($indexData['fondsKoers_stop'] - $indexData['fondsKoers_start']) / ($indexData['fondsKoers_start']/100 )+100;    
  $indexData['extraIndex']=$extraIndex;
  
  $indexData['fondsKoers_start']=$this->getFondsKoers($this->pdf->portefeuilledata['SpecifiekeIndex'],$pstart);
  $indexData['fondsKoers_stop']=$this->getFondsKoers($this->pdf->portefeuilledata['SpecifiekeIndex'],$pstop);
  $indexData['specifiekeIndex'] = ($indexData['fondsKoers_stop'] - $indexData['fondsKoers_start']) / ($indexData['fondsKoers_start']/100 )+100; 
  $indexData['specifiekeIndexNaam']=$this->pdf->portefeuilledata['SpecifiekeIndex']; 
  if(empty($cumulatief['specifiekeIndexCumulatief']))
    $cumulatief['specifiekeIndexCumulatief']=100;
  if(empty($cumulatief['benchmarkBeheerderCumulatief']))
    $cumulatief['benchmarkBeheerderCumulatief']=100;

  if(empty($cumulatiefJaar['specifiekeIndexCumulatief']))
    $cumulatiefJaar['specifiekeIndexCumulatief']=100;
  if(empty($cumulatiefJaar['benchmarkBeheerderCumulatief']))
    $cumulatiefJaar['benchmarkBeheerderCumulatief']=100;    
    
  $cumulatief['specifiekeIndexCumulatief'] =($cumulatief['specifiekeIndexCumulatief']*($indexData['specifiekeIndex'])/100);
  $cumulatief['benchmarkBeheerderCumulatief'] =($cumulatief['benchmarkBeheerderCumulatief']*($indexData['benchmarkBeheerder'])/100);

  $cumulatiefJaar['specifiekeIndexCumulatief'] =($cumulatiefJaar['specifiekeIndexCumulatief']*($indexData['specifiekeIndex'])/100);
  $cumulatiefJaar['benchmarkBeheerderCumulatief'] =($cumulatiefJaar['benchmarkBeheerderCumulatief']*($indexData['benchmarkBeheerder'])/100);    
    
  $jaar=substr($data['datum'],0,4);
  $juldate=db2jul($data['datum']);
  if($juldate < $kwartaalPeriode)
  {
    if(empty($jaarPerf[$jaar]))
    {
      $jaarPerf[$jaar]['performance']=100;
      $jaarPerf[$jaar]['benchmarkBeheerderCumulatief']=100;
      $jaarPerf[$jaar]['specifiekeIndexCumulatief']=100;
      
    }  
    $jaarPerf[$jaar]['performance'] =($jaarPerf[$jaar]['performance']*(100+$data['performance'])/100);
    $jaarPerf[$jaar]['specifiekeIndexPerformance'] =($jaarPerf[$jaar]['specifiekeIndexPerformance']*(100+$indexData['specifiekeIndex'])/100);
    $jaarPerf[$jaar]['benchmarkBeheerderCumulatief'] =($jaarPerf[$jaar]['benchmarkBeheerderCumulatief']+100*($indexData['benchmarkBeheerder'])/100);
  
    if(!isset($jaarWaarden[$jaar]['waardeBegin']))
      $jaarWaarden[$jaar]['waardeBegin']=$data['waardeBegin'];

    $jaarWaarden[$jaar]['stortingen']+=$data['stortingen'];
    $jaarWaarden[$jaar]['onttrekkingen']+=$data['onttrekkingen'];
    $jaarWaarden[$jaar]['kosten']+=$data['kosten'];

    if(substr($data['datum'],5,5)=='12-31')
    {
       $grafiekData['jaren']['portefeuille'][]=$jaarPerf[$jaar]['performance']-100;
       $grafiekData['jaren']['benchmarkBeheerder'][]=$jaarPerf[$jaar]['benchmarkBeheerder']-100;


       $grafiekData['jaren']['datum'][]= $jaar;
       $tmp=array_merge($data,$jaarWaarden[$jaar]);
       $grafiekData['jaren']['waarde'][]=$tmp;

    }
  }
  else
  {
    if(empty($kwartalenCumu['perf']))
      $kwartalenCumu['perf']=100;
   
    $kwartalenCumu['stortingen']+=$data['stortingen'];
    $kwartalenCumu['onttrekkingen']+=$data['onttrekkingen'];
    $kwartalenCumu['kosten']+=$data['kosten'];
    $kwartalenCumu['resultaat']+=($data['waardeHuidige']-$data['waardeBegin']+$data['onttrekkingen']-$data['stortingen']-$data['kosten']);
    $kwartalenCumu['perf'] =($kwartalenCumu['perf']*(100+$data['performance'])/100);

    
    //$data['specifiekeIndexCumulatief']=$data['specifiekeIndex'];
   // listarray($data);
    $data['benchmarkBeheerder']=$indexData['benchmarkBeheerder'];
    $data['benchmarkBeheerderCumulatief']=$cumulatief['benchmarkBeheerderCumulatief'];
    $data['specifiekeIndex']=$indexData['specifiekeIndex'];
    $data['specifiekeIndexCumulatief']=$cumulatief['specifiekeIndexCumulatief'];
    $grafiekData['kwartalen']['portefeuille'][]=$data['performance'];
    $grafiekData['kwartalen']['datum'][]= "Q".(ceil(date("m",$juldate)/3))."-".date("Y",$juldate);
    $grafiekData2['portefeuille'][]=$data['waardeHuidige'];
    $grafiekData['kwartalen']['waarde'][]=$data;
    // $grafiekData['kwartalen']['portefeuille'][]['cumulatief'] = $data['index'];
  }

  if(empty($indexTabel['cumulatief']['portefeuille']['jaren']))
    $indexTabel['cumulatief']['portefeuille']['jaren']=100;
  $indexTabel['cumulatief']['portefeuille']['jaren'] = ($indexTabel['cumulatief']['portefeuille']['jaren']*(100+$data['performance'])/100);
  $indexTabel[$jaar]['portefeuille']['jaar'] = $indexTabel['cumulatief']['portefeuille']['jaren'];
  if(substr($data['datum'],5,5) == '12-31' || $aantalWaarden == $id)
  { //listarray($data);
    $indexTabel['cumulatief']['portefeuille']['jaren'] = 100;
//    $indexTabel[$jaar]['portefeuille']['specifiekeIndex'] = $data['specifiekeIndexPerformance'];
 //   $indexTabel[$jaar]['portefeuille']['specifiekeIndexCumulatief'] = $data['specifiekeIndex'];
 
    $indexTabel[$jaar]['portefeuille']['index'] = $data['index'];
    
    $indexTabel[$jaar]['portefeuille']['benchmarkBeheerder']=$cumulatiefJaar['benchmarkBeheerderCumulatief'];//$indexData['benchmarkBeheerder'] ;
    $indexTabel[$jaar]['portefeuille']['benchmarkBeheerderCumulatief']=$cumulatief['benchmarkBeheerderCumulatief'];
    
    //$indexTabel[$jaar]['portefeuille']['specifiekeIndex']=$indexData['specifiekeIndex'] ;
    $indexTabel[$jaar]['portefeuille']['specifiekeIndex']=$cumulatiefJaar['specifiekeIndexCumulatief'];
    $indexTabel[$jaar]['portefeuille']['specifiekeIndexCumulatief']=$cumulatief['specifiekeIndexCumulatief'];    
    

    $cumulatiefJaar['specifiekeIndexCumulatief']=100;
    $cumulatiefJaar['benchmarkBeheerderCumulatief']=100;
  }
}
//listarray($cumulatiefJaar);ob_flush();

//listarray($grafiekData['kwartalen']);//specifiekeIndexPerformance


$this->pdf->CellBorders=array();
$this->pdf->setY($this->pdf->rapportYstart+10); 


/*
$YendIndex = $this->pdf->GetY();
$this->pdf->setXY(190,80);
$this->VBarDiagram(90,40,$grafiekData['kwartalen'],'',$color);
*/
//$this->pdf->setY(85);
$color=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
$this->pdf->ln();
$w=(297-2*$this->pdf->marge)/7;
$witCell=$this->pdf->witCell;
//$this->pdf->setWidths(array($w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell));
$correctie=5;
$kopwidths=array($w-$witCell-$correctie,$witCell,$w-$witCell-$correctie,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell+$correctie,$witCell,$w-$witCell,$witCell,$w+$correctie);
$this->pdf->setWidths($kopwidths);
$this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1);
$this->pdf->setAligns(array('L','','R','','R','','R','','R','','R','','R','','R'));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
//if(count($grafiekData['jaren']['datum'])>0)
$this->pdf->Row(array("\nPeriode",'', "\nBeginvermogen",'', "\nStortingen",'',"\nOnttrekkingen",'', "\nResultaat",'',"\nKosten",'',"\nEindvermogen"));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
foreach($grafiekData['jaren']['datum'] as $i=>$datum)
{
  if($datum>=$filterJaar)
  {
    $n = $this->switchColor($n);
    $this->pdf->Row(array($datum, '',
                      $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeBegin'], 0), '',
                      $this->formatGetal($grafiekData['jaren']['waarde'][$i]['stortingen'], 0), '',
                      $this->formatGetal($grafiekData['jaren']['waarde'][$i]['onttrekkingen'], 0), '',
                      $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige'] - $grafiekData['jaren']['waarde'][$i]['waardeBegin'] + $grafiekData['jaren']['waarde'][$i]['onttrekkingen'] - $grafiekData['jaren']['waarde'][$i]['stortingen'] - $grafiekData['jaren']['waarde'][$i]['kosten'], 0), '',
                      $this->formatGetal($grafiekData['jaren']['waarde'][$i]['kosten'], 0), '',
                      $this->formatGetal($grafiekData['jaren']['waarde'][$i]['waardeHuidige'], 0)
                    ));
  }
}

$this->pdf->ln();
$this->pdf->setWidths($kopwidths);
$this->pdf->setAligns(array('L','','R','','R','','R','','R','','R','','R','','R'));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
//$this->pdf->Row(array('Periode','', 'Beginvermogen','', 'Stortingen','', 'Onttrekkingen','', 'Resultaat','','Kosten','', 'Eindvermogen'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$aantal=count($grafiekData['kwartalen']['datum']);
foreach($grafiekData['kwartalen']['datum'] as $i=>$datum)
{
  $n=$this->switchColor($n);
   $this->pdf->Row(array($datum,'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeBegin'],0),'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['stortingen'],0),'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['onttrekkingen'],0),'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige']-$grafiekData['kwartalen']['waarde'][$i]['waardeBegin']+$grafiekData['kwartalen']['waarde'][$i]['onttrekkingen']-$grafiekData['kwartalen']['waarde'][$i]['stortingen']-$grafiekData['kwartalen']['waarde'][$i]['kosten'],0),'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['kosten'],0),'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'],0)));
}
    $n=$this->switchColor($n);
    $this->pdf->Ln();
    $this->pdf->SetFillColor($this->pdf->achtergrondTotaal[0],$this->pdf->achtergrondTotaal[1],$this->pdf->achtergrondTotaal[2]);
    $this->pdf->Row(array(substr($datum,3,4).' Cumulatief','',
      $this->formatGetal($grafiekData['kwartalen']['waarde'][0]['waardeBegin'],0),'',
      $this->formatGetal($kwartalenCumu['stortingen'],0),'',
      $this->formatGetal($kwartalenCumu['onttrekkingen'],0),'',
      $this->formatGetal($kwartalenCumu['resultaat'],0),'',
      $this->formatGetal($kwartalenCumu['kosten'],0),'',
      $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['waardeHuidige'],0)));

$this->pdf->CellBorders = array();
unset($this->pdf->fillCell);
////

//$this->pdf->setY($this->pdf->rapportYstart); 
 // $this->pdf->AddPage();
 //    $this->pdf->SetY($this->pdf->rapportYstart);
 $this->pdf->Ln();

 $tweedeY=$this->pdf->getY();
   $this->pdf->Ln(2);
     $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(100,4,'Rendementsanalyse procentueel');
    
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    $this->pdf->Line($this->pdf->marge,$tweedeY+$this->pdf->rowHeight+3,297-$this->pdf->marge-79,$tweedeY+$this->pdf->rowHeight+3);
 $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize+2);



$color=array($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
$this->pdf->ln();
$this->pdf->ln(8);
$w=(297-2*$this->pdf->marge)/7;
$this->pdf->setWidths($kopwidths);
$this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1);
$this->pdf->setAligns(array('L','','R','','R','','R','','R','','R','','R','','R'));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
//if(count($grafiekData['jaren']['datum'])>0)


$header=array("\nPeriode",'', "\nPeriode",'', "Vanaf start cumulatief",'',"Benchmark FCT\nperiode",'',"Benchmark FCT\nVanaf start cumulatief");
$perfGrafiek=array();
if($this->pdf->portefeuilledata['SpecifiekeIndex']=='')
{
  $header[6]="\n ";
  $header[8]="\n ";
}
$ystart=$this->pdf->getY();
$this->pdf->Row($header);
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
foreach($grafiekData['jaren']['datum'] as $i=>$datum)
{
  if($datum>=$filterJaar)
  {
    $line = array($datum, '',
      $this->formatGetal($indexTabel[$datum]['portefeuille']['jaar'] - 100, 1) . "%", '',
      $this->formatGetal($indexTabel[$datum]['portefeuille']['index'] - 100, 1) . "%", '',
      $this->formatGetal($indexTabel[$datum]['portefeuille']['specifiekeIndex'] - 100, 1) . "%", '',
      $this->formatGetal($indexTabel[$datum]['portefeuille']['specifiekeIndexCumulatief'] - 100, 1) . "%");

    $perfGrafiek[$datum]['portefeuille'] = $indexTabel[$datum]['portefeuille']['jaar'] - 100;
    $perfGrafiek[$datum]['benchmark'] = $indexTabel[$datum]['portefeuille']['specifiekeIndex'] - 100;
    if ($this->pdf->portefeuilledata['SpecifiekeIndex'] == '')
    {
      $line[6] = "";
      $line[8] = "";
    }
    $n = $this->switchColor($n);
    $this->pdf->Row($line);
  }
}

$this->pdf->ln();

//$this->pdf->setWidths(array($w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w-$witCell,$witCell,$w));
$this->pdf->setAligns(array('L','','R','','R','','R','','R','','R','','R','','R'));
$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
$this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
//$this->pdf->Row(array("Periode\n ",'', "Periode\n ",'', "Cumulatief\n ",'', "Benchmark periode\n ",'', 'Benchmark Cumulatief','','Benchmark FTC periode','','Benchmark FTC Cumulatief'));
$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
$aantal=count($grafiekData['kwartalen']['datum']);

$jaarPerf=array('performance'=>0,'benchmarkBeheerderCumulatief'=>0,'specifiekeIndexCumulatief'=>0);
foreach($grafiekData['kwartalen']['datum'] as $i=>$datum)
{
  $line=array($datum,'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['performance'],1)."%",'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['index']-100,1)."%",'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['specifiekeIndex']-100,1)."%",'',
   $this->formatGetal($grafiekData['kwartalen']['waarde'][$i]['specifiekeIndexCumulatief']-100,1)."%");


  $perfGrafiek[$datum]['portefeuille']=$grafiekData['kwartalen']['waarde'][$i]['performance'];
  $perfGrafiek[$datum]['benchmark']=$grafiekData['kwartalen']['waarde'][$i]['specifiekeIndex']-100;
  if($this->pdf->portefeuilledata['SpecifiekeIndex']=='')
  {
    $line[6]="";
    $line[8]="";
  }   
  $n=$this->switchColor($n);
   $this->pdf->Row($line);

   $jaarPerf['performance']=((1+$jaarPerf['performance']/100)*(1+$grafiekData['kwartalen']['waarde'][$i]['performance']/100)-1)*100;
   $jaarPerf['benchmarkBeheerderCumulatief']=((1+$jaarPerf['benchmarkBeheerderCumulatief']/100)*(1+($grafiekData['kwartalen']['waarde'][$i]['benchmarkBeheerder']-100)/100)-1)*100;
   $jaarPerf['specifiekeIndexCumulatief']=((1+$jaarPerf['specifiekeIndexCumulatief']/100)*(1+($grafiekData['kwartalen']['waarde'][$i]['specifiekeIndex']-100)/100)-1)*100;
}




    $n=$this->switchColor($n);

    $this->pdf->Ln();
    $this->pdf->SetFillColor($this->pdf->achtergrondTotaal[0],$this->pdf->achtergrondTotaal[1],$this->pdf->achtergrondTotaal[2]);
    $line=array(substr($datum,3,4).' Cumulatief','',
   $this->formatGetal($jaarPerf['performance'],1)."%",
   '','','',
   $this->formatGetal($jaarPerf['specifiekeIndexCumulatief'],1)."%",
   '','');
   if($this->pdf->portefeuilledata['SpecifiekeIndex']=='')
   {
     $line[6]="";
     $line[8]="";
   }      
   $this->pdf->Row($line);
$this->pdf->CellBorders = array();
unset($this->pdf->fillCell);
    $this->categorieOmschrijving=array('portefeuille'=>'Portefeuille','benchmark'=>'Benchmark');
    $this->pdf->SetXY(200,$ystart-14);
    $this->VBarDiagram2(77,50,$perfGrafiek,'','Portefeuille vs benchmark');

paginaVoet($this->pdf);

	}
  
    function switchColor($n)
  {
     $col1=$this->pdf->achtergrondLicht;
     $col2=$this->pdf->achtergrondDonker;

    if($n%2==0)
      $this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
    else
      $this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);
      
      $n++;
      return $n;
  }

	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
    //echo $query." ".$koers['Koers']."<br>\n";
	  return $koers['Koers'];
	}


  function VBarDiagram2($w, $h, $data,$datalijn,$titel,$procent=true,$legendaLocatie='U')
  {
    global $__appvar;
    if($legendaLocatie=='R')
      $legendaWidth = 45;
    elseif($legendaLocatie=='U')
      $legendaWidth = 0;
    else
      $legendaHeight = 30;
    $grafiekPunt = array();
    $verwijder=array();

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();

    $h=$h-$legendaHeight;

    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setXY($XPage,$YPage+2);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell($w,4,$titel,0,1,'L');
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    $this->pdf->line($XPage,$YPage+$this->pdf->rowHeight+3,$XPage+$w,$YPage+$this->pdf->rowHeight+3);

    $YPage=$YPage+$h+15;

    $colors=array('stortingen'=>array(120,170,0),
                  'onttrekkingen'=>array(220,57,18),
                  'waardeHuidige'=>array(255,153,0),
                  'doelvermogen'=>array(51,102,204),
                  'performance'=>array(51,102,204),
                  'portefeuille'=>array(51,102,204),
                  'benchmarkBeheerder'=>array(220,57,18),
                  'specifiekeIndexPerformance'=>array(255,153,0),
                  'benchmark'=>array(255,153,0),
                  'verwachtRendementY'=>array(120,170,0),
                  'verwachtRendementQ'=>array(120,170,0));
    //    listarray($data);
    //    listarray($datalijn);

    $geldigeCategorien=array();
    foreach ($data as $datum=>$waarden)
    {
      $legenda[$datum] = $datum;
      $n=0;
      $minVal=-1;
      $maxVal=1;
      foreach ($waarden as $categorie=>$waarde)
      {
        if($categorie=='LIQ')
          $categorie='Liquiditeiten';
        if($waarde <> 0)
          $geldigeCategorien[$categorie]=true;
        $grafiek[$datum][$categorie]=$waarde;
        $grafiekCategorie[$categorie][$datum]=$waarde;
        $categorien[$categorie] = $categorie;
        $categorieId[$n]=$categorie ;


        if(!isset($colors[$categorie]))
          $colors[$categorie]=array(rand(0,255),rand(0,255),rand(0,255));

//            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        $n++;
      }
    }

    foreach ($datalijn as $datum=>$waarden)
    {
      $legenda[$datum] = $datum;

      $n=0;
      foreach ($waarden as $categorie=>$waarde)
      {
        $categorien[$categorie] = $categorie;
        if($waarde > $maxVal)
          $maxVal=ceil($waarde);
        if($waarde < $minVal)
          $minVal=ceil($waarde);

        if($waarde <> 0)
          $geldigeCategorien[$categorie]=true;

        if(!isset($colors[$categorie]))
          $colors[$categorie]=array(rand(0,255),rand(0,255),rand(0,255));

//            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        $n++;
      }
    }

    foreach($grafiek as $datum=>$datumData)
    {

      foreach($datumData as $categorie=>$waarde)
      {

        if($waarde > $maxVal)
          $maxVal=ceil($waarde);
        if($waarde < $minVal)
          $minVal=floor($waarde);
      }
    }

    if($procent==false)
      $maxVal=ceil($maxVal/pow(10,strlen($maxVal)-1))*pow(10,strlen($maxVal)-1);
    else
      $maxVal=ceil($maxVal/5)*5;
//echo $max;exit;
//echo "$minVal <br>\n";
    $minVal=floor($minVal/.5)*.5;
//
//echo "$minVal <br>\n<br>\n";
    $numBars = count($legenda);

    if($color == null)
    {
      $color=array(155,155,155);
    }


    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//      $XPage = $this->pdf->GetX();
//      $YPage = $this->pdf->GetY()+$h+15;
    $margin = 0;
    $margeLinks=10;
    $XPage+=$margeLinks;
    $w-=$margeLinks;

    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

    $n=0;
    if($legendaLocatie=='U')
    {
      $xcorrectie=$w;
      $ycorrectie=$h+10;
    }

    foreach($colors as $categorie=>$kleur)
    {
      if(isset($geldigeCategorien[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$bGrafiek+3-$xcorrectie , $YstartGrafiek-$hGrafiek+$n*7+2+$ycorrectie, 2, 2, 'F',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6-$xcorrectie ,$YstartGrafiek-$hGrafiek+$n*7+1.5+$ycorrectie );
        $this->pdf->MultiCell(40, 4,$this->categorieOmschrijving[$categorie],0,'L');
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


    $horDiv = 4;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);

    $stapgrootte = round(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);

    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;

    if($procent==true)
      $legendaEnd=' %';
    else
      $legendaEnd='';

    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1).$legendaEnd,0,0,'R');
      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; round($i) >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte).$legendaEnd,0,0,'R');
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

    if($minVal < 0)
      $extraY=abs($minVal * $unit);
    else
      $extraY=0;



    foreach ($grafiek as $datum=>$data)
    {
      $aantalCategorien=count($data);
      $catCount=0;

      foreach($colors as $categorie=>$kleur)
      {
        if(isset($data[$categorie]) && isset($geldigeCategorien[$categorie]))
        {

          $val=$data[$categorie];
          $lval = $eBaton/$aantalCategorien;
          $xval = $XstartGrafiek + (1 + $i ) * $vBar + ($catCount * $lval) - $eBaton / 2;
          $yval = $YstartGrafiek + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);

          $this->pdf->SetTextColor(0,0,0);

          if($legendaPrinted[$datum] != 1)
          {
            $part=explode("-",$legenda[$datum]);
            $this->pdf->SetXY($xval,$yval+$extraY+2);
            $this->pdf->Cell($eBaton,0,$part[0],0,0,'C');
          }

          if($grafiekPunt[$categorie][$datum])
          {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
          }
          $legendaPrinted[$datum] = 1;
          $catCount++;
        }
      }
      $i++;

    }

    $i=0;
    $YstartGrafiekLast=array();
    foreach ($grafiekNegatief as $datum=>$data)
    {

      foreach($data as $categorie=>$val)
      {
        if(isset($data[$categorie]) && isset($geldigeCategorien[$categorie]))
        {
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $lval = $eBaton/$aantalCategorien;
          $xval = $XstartGrafiek + (1 + $i ) * $vBar + ($catCount * $lval) - $eBaton / 2;
          $yval = $YstartGrafiek + $nulYpos ;
          $hval = $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;

          if($grafiekPunt[$categorie][$datum])
          {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
          }
        }
      }
      $i++;
    }


    $i=0;


    $YDiag=$YstartGrafiek;
    $XDiag=$XstartGrafiek;
    foreach($datalijn as $datum=>$data)
    {
      foreach($data as $categorie=>$val)
      {
        if(isset($geldigeCategorien[$categorie]))
        {
          $extrax=($unit*0.5*-1);
          if($i <> 0)
            $extrax1=($unit*0.5*-1);

          $xval1[$categorie]  = $XstartGrafiek + (0 + $i ) * $vBar ;
          $xval2[$categorie] = $XstartGrafiek + (1 + $i ) * $vBar ;
          $yval2[$categorie] = $YstartGrafiek + ($unit*$val) + $nulYpos;


          if($i <>0 && isset($yval1[$categorie]))
            $this->pdf->line($xval1[$categorie], $yval1[$categorie], $xval2[$categorie], $yval2[$categorie],array('color'=>$colors[$categorie] ));
          $this->pdf->Rect($xval2[$categorie], $yval2[$categorie]-0.5, 1, 1 ,'F','',$colors[$categorie]);

          $yval1[$categorie] = $yval2[$categorie];
        }
      }
      $i++;
    }




    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }

  
}
?>
