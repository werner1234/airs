<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.3 $

$Log: RapportPERF_L60.php,v $
Revision 1.3  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.2  2017/07/12 08:00:45  rvv
*** empty log message ***

Revision 1.1  2015/03/11 17:13:49  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L60.php");

class RapportPERF_L60
{

	function RapportPERF_L60($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Resultaat en rendementsberekening";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L60($this);
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		$this->pdf->widthB = array(280);
		$this->pdf->alignB = array('L');


		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFPaginas0']=$this->pdf->page;
    $this->pdf->templateVars['PERFPaginas0']=$this->pdf->page;
    $this->pdf->templateVars['PERFPaginas0']=$this->pdf->page;
    

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();
    $this->addResultaat();
    $this->risicoBox();
    //$this->addSectorBar();
    
    $gebruikteCategorie=$this->addZorgBar();
    $this->plotZorgBar2(4,50,$gebruikteCategorie);
	}
  
  function getKleuren()
  {
    $db=new DB();
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }
  }

  function addSectorBar()
  {
    //$att=new ATTberekening_L42($this);
    $this->att->indexPerformance=false;
    $this->waarden['sector']=$this->att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'sector');
    $categorien=array_keys($this->waarden['sector']);
    $min=0;
    $max=1;
    $kleuren=$this->kleuren['OIS'];
    $kleuren['totaal']['R']['value']=150;
    $kleuren['totaal']['G']['value']=150;
    $kleuren['totaal']['B']['value']=150;
    
    foreach($categorien as $categorie)
    { 
      $perc=round($this->waarden['sector'][$categorie]['aandeelOpTotaal']*100,1);
      if($perc <> 0 && $categorie <> 'totaal')
      {
	      $grafiekData[$this->att->categorien[$categorie]]=round($this->waarden['sector'][$categorie]['bijdrage'],1);
        if($this->waarden['sector'][$categorie]['bijdrage'] > $max)
          $max=$this->waarden['sector'][$categorie]['bijdrage'];
        if($this->waarden['sector'][$categorie]['bijdrage'] > $min)
          $min=$this->waarden['sector'][$categorie]['bijdrage'];
	      $grafiekKleurData[$this->att->categorien[$categorie]]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
	    }
    }

	  $this->pdf->setXY(190,45);
	  $this->BarDiagram(100, 60, $grafiekData, '%l (%p)',$grafiekKleurData,$max);
}



function getGrootboeken()
{
  $vertaling=array();
  $db=new DB();
  $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
  }
  return $vertaling;
}

function risicoBox()
{
  $DB=new DB();
  $this->pdf->SetY(150);
  $this->pdf->SetAligns(array('L','L','R'));
  $this->pdf->SetWidths(array(180,85,15));
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
  $this->pdf->Row(array('','RISICOANALYSE'));
	$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
	$query="SELECT verwachtRendement,verwachtMaxVerlies,verwachtMaxWinst,klasseStd FROM Risicoklassen WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Risicoklasse='".$this->pdf->portefeuilledata['Risicoklasse']."'";
	$DB->SQL($query);
	$DB->Query();
	$verwachtRendement = $DB->LookupRecord();
  
  $afm=AFMstd($this->portefeuille,$this->rapportageDatum,false);
  
  if(1==0)
  {
    $index = new indexHerberekening();
    $dagen=(db2jul($this->rapportageDatum)-db2jul($this->pdf->PortefeuilleStartdatum))/86400;
    if($dagen < 550)
    {
      $perioden='geenData';
      $yearCount=0;
    }
    elseif($dagen > (3*365))
    {
      $perioden='maanden';
      $yearCount=12;
    }
    else
    {
      $perioden='halveMaanden';
      $yearCount=24;
    }

    if($yearCount <> 0)
    {
      $indexWaarden = $index->getWaarden($this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,$this->portefeuille,$indexRisicovrij,$perioden);
      $this->pdf->excelData[]=array('Periode','performance','performanceIndex');
      foreach ($indexWaarden as $id=>$waarden)
      {
        $portPerfAvg+=$waarden['performance'];
        $modelPerfAvg+=$waarden['specifiekeIndexPerformance'];
        $overPerf+=($waarden['performance']-$waarden['specifiekeIndexPerformance']);
        $overPerfArray[$waarden['datum']]=($waarden['performance']-$waarden['specifiekeIndexPerformance'])/100;
        $indexArray[$waarden['datum']]=100+$waarden['performance'];
        $indexModelArray[$waarden['datum']]=100+$waarden['specifiekeIndexPerformance'];
        $this->pdf->excelData[]=array($waarden['periode'],100+$waarden['performance'],100+$waarden['specifiekeIndexPerformance']);
      }
      $portPerfAvg=$portPerfAvg/count($indexWaarden);
      $modelPerfAvg=$modelPerfAvg/count($indexWaarden);
      $overPerfAvg=$overPerf/count($indexWaarden);
      $standaarddeviatiePeriode=standard_deviation($indexArray);
      $standaarddeviatie= $standaarddeviatiePeriode*sqrt($yearCount);
    }
  }
   
   
  //$this->pdf->Row(array('','Rendement t/m '.$eindPeriodeTxt,$this->formatGetal($this->waarden['Periode']['totaal']['procent'],2).' %'));
  $this->pdf->Row(array('','Gemiddeld verwacht jaarlijks rendement',$this->formatGetal($verwachtRendement['verwachtRendement'],2).' %'));
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  $this->pdf->Row(array('','Standaarddeviatie van de portefeuille (AFM normen)',$this->formatGetal($afm['std'],2).' %'));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->Row(array('','Maximaal verwacht verlies in enig jaar (AFM normen)',$this->formatGetal($verwachtRendement['verwachtRendement']-2*$afm['std'],2).' %'));
  $this->pdf->Row(array('','Maximaal verwachte winst in enig jaar (AFM normen)',$this->formatGetal($verwachtRendement['verwachtRendement']+2*$afm['std'],2).' %'));
  if($yearCount <> 0)
  { 
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Werkelijke standaarddeviatie van de portefeuille',$this->formatGetal($standaarddeviatie,2).' %'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Maximaal verwacht verlies in enig jaar (werkelijk)',$this->formatGetal($verwachtRendement['verwachtRendement']-2*$standaarddeviatie,2).' %'));
    $this->pdf->Row(array('','Maximaal verwachte winst in enig jaar (werkelijk)',$this->formatGetal($verwachtRendement['verwachtRendement']+2*$standaarddeviatie,2).' %'));
  } 
 // $this->pdf->Row(array('','Standaarddeviatie volgens portefeuilleprofiel',$this->formatGetal($verwachtRendement['klasseStd'],2).' %'));
  
}


  function addZorgBar()
  {
    include_once("rapport/Zorgplichtcontrole.php");
    $zorgplicht = new Zorgplichtcontrole();
	  $pdata=$this->pdf->portefeuilledata;
	  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum);
    $gebruikteCategorien=array();
    foreach($zpwaarde['categorien'] as $categorie=>$data)
    {
      if(!isset($data['fondsGekoppeld']))
      {
        $gebruikteCategorien[$categorie]=$data;
      }
    }
    foreach($zpwaarde['conclusie'] as $data)
    {
      foreach($gebruikteCategorien as $categorie=>$gebruikteCategorie)
      {
        if($data[0]==$gebruikteCategorie['Zorgplicht'])
        {
          $gebruikteCategorien[$categorie]['percentage']=$data[2];
          $gebruikteCategorien[$categorie]['conclusie']=$data[5];
        }
      }
    }   
    return $gebruikteCategorien;
  }

  function plotZorgBar($width,$height,$data)
  {
    $this->pdf->setXY(260,50);
    $this->pdf->Cell(5,5,"Mandaat controle",0,0,'C');
    //$this->pdf->Rect(210,110,70,80);
    $this->pdf->setXY(255,110);
  
    $hProcent=$height/-100;
    $barWidth=$width-5;
    $marge=1;
    $extraX=10;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();   
    
   
       
    $data['percentage']=str_replace(',','.',$data['percentage']);
     
    $this->pdf->setXY($xPage-$marge-4,$yPage-2);
    $this->pdf->cell(4,4,'0',0,0,'R');
    $this->pdf->Rect($xPage, $yPage, $barWidth, $hProcent*100, 'D');
    $this->pdf->Rect($xPage+$extraX, $yPage, $barWidth, $hProcent*100, 'D');
    $this->pdf->setXY($xPage-$marge-4,$yPage+$hProcent*100-2);
    $this->pdf->cell(4,4,'Risicodragend 100',0,0,'R');
    $this->pdf->setXY($xPage-$marge-4,$yPage+$hProcent*$data['Minimum']-2);
    $this->pdf->cell(4,4,"Min. ".$data['Minimum'],0,0,'R');
    $this->pdf->setXY($xPage-$marge-4,$yPage+$hProcent*$data['Maximum']-2);
    $this->pdf->cell(4,4,"Max. ".$data['Maximum'],0,0,'R');
     
    $this->pdf->setXY($xPage-$marge-4,$yPage+$hProcent*$data['Norm']-2);
    $this->pdf->cell(4,4,"Norm ".$data['Norm'],0,0,'R');
    
    $this->pdf->SetFillColor(200,0,0);
    $this->pdf->Rect($xPage, $yPage, $barWidth, $hProcent*$data['Minimum'], 'DF');
    $this->pdf->SetFillColor(0,200,0);
    $this->pdf->Rect($xPage, $yPage+$hProcent*$data['Minimum'],$barWidth, $hProcent*$data['Maximum'],  'DF');
    $this->pdf->SetFillColor(200,0,0);
    $this->pdf->Rect($xPage, $yPage+$hProcent*$data['Maximum'],$barWidth, $hProcent*(100-$data['Maximum']), 'DF');
     
    $this->pdf->Line($xPage, $yPage+$hProcent*$data['Norm'],$xPage+$barWidth, $yPage+$hProcent*$data['Norm']);
    $this->pdf->SetFillColor(0,150,0);
    $extraX=10;
    $this->pdf->Rect($xPage+$extraX, $yPage+$hProcent*($data['percentage']-1), $barWidth, $hProcent*(2), 'DF');
    $this->pdf->setXY($xPage+$barWidth+$marge+$extraX,$yPage+$hProcent*$data['percentage']-2);
    $this->pdf->cell(4,4,$data['percentage'].' werkelijk',0,0,'L');
  }

  function plotZorgBar2($barWidth,$height,$zorgdata)
  {
    $DB=new DB();
    $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  $DB->SQL($query);
	  $DB->Query();
	  while($data = $DB->NextRecord())
    {
      $categorien[$data['Zorgplicht']]=$data['Omschrijving'];
    }
    $this->pdf->setXY(235,45);
    $this->pdf->Cell(5,5,"Mandaat controle",0,0,'C');
    $this->pdf->Rect(200,50,70,95);
    $this->pdf->setXY(210,64);
  
    $hProcent=$height/100;

    $marge=1;
    $extraY=8;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();   
    
   
    
     
     foreach($zorgdata as $categorie=>$data)
     {
       
    $data['percentage']=str_replace(',','.',$data['percentage']);
     
    $this->pdf->setXY($xPage-$marge-4,$yPage-2);
    $this->pdf->Rect($xPage, $yPage, $hProcent*100, $barWidth, 'D');
    $this->pdf->Rect($xPage, $yPage+$extraY,$hProcent*100, $barWidth,  'D');
        $this->pdf->setXY($xPage-2,$yPage-$marge-8);
    $this->pdf->cell(4,4,$categorien[$categorie],0,0,'L');
    $this->pdf->setXY($xPage-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"0",0,0,'R');
    $this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"100",0,0,'R');
    $this->pdf->setXY($xPage+$hProcent*$data['Minimum']-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"".$data['Minimum'],0,0,'R');
    $this->pdf->setXY($xPage+$hProcent*$data['Maximum']-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"".$data['Maximum'],0,0,'R');
     
    //$this->pdf->setXY($xPage+$hProcent*$data['Norm']-2,$yPage+$marge+5);
    //$this->pdf->cell(4,4,"Norm ".$data['Norm'],0,0,'R');
    
    $this->pdf->SetFillColor(239,86,61);
    $this->pdf->Rect($xPage, $yPage, $hProcent*$data['Minimum'], $barWidth,  'DF');
    $this->pdf->SetFillColor(27,159,17);
    $this->pdf->Rect($xPage+$hProcent*$data['Minimum'], $yPage,$hProcent*($data['Maximum']-$data['Minimum']), $barWidth,   'DF');
    $this->pdf->SetFillColor(239,86,61);
    $this->pdf->Rect($xPage+$hProcent*$data['Maximum'], $yPage, $hProcent*(100-$data['Maximum']),$barWidth,  'DF');
     
    //$this->pdf->Line($xPage+$hProcent*$data['Norm'], $yPage,$xPage+$hProcent*$data['Norm'],$yPage+$barWidth);
    if($data['conclusie']=='Voldoet')
      $this->pdf->SetFillColor(27,159,17);
    else
      $this->pdf->SetFillColor(239,86,61);  
    $this->pdf->Rect($xPage,$yPage+$extraY , $hProcent*$data['percentage'], $barWidth,  'DF');
    $this->pdf->setXY($xPage+$hProcent*$data['percentage']-2,$yPage+$barWidth+$marge+$extraY);
    $this->pdf->cell(4,4,$data['percentage'].' werkelijk',0,0,'L');
    $yPage+=30;
    }
  }
  


function addResultaat()
{
  
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
   $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
  
   $vetralingGrootboek=$this->getGrootboeken();
  
    //$att=new ATTberekening_L42($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'categorien');
    $categorien=array_keys($this->waarden['Periode']);
    
    //listarray($this->att->totalen);exit;
//listarray($this->waarden['Periode']);

  $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('U','U');
  $subBoven=array('','');
  $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
  $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
  
  foreach($categorien as $categorie)
  {
    $volOnder[]='U';
    $volOnder[]='U';
    $subOnder[]='U';
    $subOnder[]='';
    $subBoven[]='T';
    $subBoven[]='';    
    $fillArray[]=1;
    $fillArray[]=1;
    $header[]=$this->att->categorien[$categorie];
    $header[]='';
    $samenstelling[]='';
    $samenstelling[]='';
   // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  }

  $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $effectenmutaties=array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
  
  
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fonds resultaten",$this->pdf->rapport_taal)); //
  $ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valuta resultaten",$this->pdf->rapport_taal)); //
  
$gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde fonds resultaten",$this->pdf->rapport_taal)); //
$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valuta resultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal)); //
$rente=array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal));//
$totaalOpbrengst=array("","");//totaalOpbrengst

    $totaalKosten=array("","");   //totaalKosten 
    $totaal=array("","");   //totaalOpbrengst-totaalKosten 


foreach($categorien as $categorie)
{
  unset($this->waarden['Periode'][$categorie]['perfWaarden']);
}

  //listarray($this->waarden['Periode']);exit;
  foreach($categorien as $categorie)
  {
    $perfWaarden=$this->waarden['Periode'][$categorie];
    $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
    $mutwaarde[]='';
    
    if($categorie=='totaal')
    {
      $effectenmutaties[]='';
      $effectenmutaties[]=''; 
     //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
     //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
      $onttrekking[]='';
    }
    else
    {
      $effectenmutaties[]=$this->formatGetal(($perfWaarden['onttrekking']+$perfWaarden['storting'])*-1,0);
      $effectenmutaties[]='';
      $stortingen[]='';
      $stortingen[]='';
      $onttrekking[]='';
      $onttrekking[]='';     
    }
    
    $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
                        $perfWaarden['ongerealiseerdFondsResultaat']+
                        $perfWaarden['ongerealiseerdValutaResultaat']+
                        $perfWaarden['gerealiseerdFondsResultaat']+
                        $perfWaarden['gerealiseerdValutaResultaat']+
                        $perfWaarden['opgelopenrente'];
                        
    $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR+$perfWaarden['kosten']);
    $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
    
    $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $resultaat[]='';

    if($categorie=='Liquiditeiten')
    {
      $rendement[]='';
      $rendement[]='';
    }
    else
    {
      $rendement[]=$this->formatGetal($perfWaarden['procent'],2);
      $rendement[]='%';
    }
    
    if($categorie == 'totaal')
    {
    $ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0);
    $ongerealiseerdFonds[]='';
    $ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
    $ongerealiseerdValuta[]='';
    $gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0);
    $gerealiseerdFonds[]='';
    $gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
    $gerealiseerdValuta[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
    $rente[]='';
    $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0);
    $totaalOpbrengst[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
    $totaalKosten[]='';
    $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $totaal[]='';
    
    
    
    foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
       $opbrengstCategorien[$categorie]=$categorie;
    foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;  
    }
  } 

    $kol=21;
    $fil=2;
  	$this->pdf->widthB = array(0,65,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;
  
  $this->pdf->ln();
//listarray($perfWaarden);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
		$this->pdf->row($header);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->row($perbegin);
	  //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
		$this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
			// subtotaal
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($effectenmutaties);
    $this->pdf->ln();
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->ln();

    $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
		$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
		$this->pdf->row($samenstelling);//,"","","",""));
		//$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row($ongerealiseerdFonds);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		$this->pdf->row($ongerealiseerdValuta);
    $this->pdf->row($gerealiseerdFonds);
    $this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
	//	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
	  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
		$keys=array();
		//foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
		//  $keys[]=$key;

 

    foreach ($opbrengstCategorien as $grootboek)
	  {
		    $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
       // foreach($perfWaarden as $port=>$waarden)
       
        foreach($categorien as $categorie)
        {
          if($categorie == 'totaal')
          {   
          $perfWaarden=$this->waarden['Periode'][$categorie];
          $tmp[]=$this->formatGetal($perfWaarden['grootboekOpbrengsten'][$grootboek],0);
          $tmp[]='';
          }
        }
		  //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			  $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
		}

    $this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
		$this->pdf->ln();
    $this->pdf->CellBorders = array();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
    
		foreach ($kostenCategorien as $grootboek)
		{
      $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
        foreach($categorien as $categorie)
        {
          if($categorie == 'totaal')
          {   
          $perfWaarden=$this->waarden['Periode'][$categorie];
          $tmp[]=$this->formatGetal($perfWaarden['grootboekKosten'][$grootboek],0);
          $tmp[]='';
          }
        }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}
    $this->pdf->CellBorders = $subBoven;
  	$this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = array();
    //$this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
		$actueleWaardePortefeuille = 0;

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

}


    function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $legendWidth=50;
      $YDiag = $YPage + $margin;
      $hDiag = floor($h - $margin * 2);
      $XDiag = $XPage + $margin * 2 + $legendWidth;
      $lDiag = floor($w - $margin * 3 - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }

      $offset=$minVal;
      $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;
      
      $legendaStep=$unit/$nbDiv*$bandBreedte;
      //echo "$bandBreedte / $legendaStep = ".$bandBreedte/$legendaStep." ".$nbDiv;exit;
      //if($bandBreedte/$legendaStep > $nbDiv)

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

      $nullijn=$XDiag - ($offset * $unit) +$margin;

      $i=0;
      $nbDiv=10;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
        }

        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $i=0;

      //$this->pdf->SetXY(0, $YDiag);
      //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
      //$this->pdf->SetXY($nullijn, $YDiag);
      //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, 'Contributie rendement',0,0,'C');
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
          //Bar
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          //Legend
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
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
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $hLegend = 2;
      $radius = min($w - $margin * 4  , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($this->pdf->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage - $radius - 22 ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag - $radius + $hLegend*2;

      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $hLegend;
      }

  }



}
?>