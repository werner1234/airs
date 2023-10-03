<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/04 18:22:49 $
File Versie					: $Revision: 1.7 $

$Log: RapportPERF_L99.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_99/ATTberekening_L99.php");

class RapportPERF_L99
{

	function RapportPERF_L99($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting over de categorieën";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L99($this);
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

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();
    $this->addResultaat();


    $this->indexVergelijking();



  }


  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  function indexVergelijking()
  {
    $DB=new DB();
  
   
  
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $RapJaar = date("Y", db2jul($this->rapportageDatum));
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
      $jan = $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapJaar-01-01"))
      $jan = $this->pdf->PortefeuilleStartdatum;
    else
      $jan = "$RapJaar-01-01";
  
    $kwartaal=ceil(date('m',db2jul($this->rapportageDatum))/3);
    $beginMaand=($kwartaal-1)*3+1;

    $kwart=date('Y-m-d',mktime(0,0,0,$beginMaand,0,$RapJaar));
  
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($kwart))
      $kwart= $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul("$RapJaar-01-01")> db2jul($kwart))
      $kwart= "$RapJaar-01-01";

    
    $perioden=array('begin'=>$this->rapportageDatumVanaf,'jan'=>$jan,'kwart'=>$kwart,'eind'=>$this->rapportageDatum);

    $query="SELECT
Indices.Beursindex,
Indices.specialeIndex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $indices=array();
    while($index = $DB->nextRecord())
      $indices[]=$index;


  
    $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$this->rapportageDatum);
    $query2 = "SELECT Fondsen.Fonds as SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Fondsen
	             WHERE Fondsen.Fonds  = '". mysql_real_escape_string($SpecifiekeIndexFonds)."' ";
    $DB->SQL($query2);
    $DB->Query();
    $SpecifiekeIndex=$DB->lookupRecord();
    $indices[]=$SpecifiekeIndexFonds;
    $fondsOmschrijvingen=array($SpecifiekeIndexFonds=>$SpecifiekeIndex['Omschrijving']);
  
    foreach ($perioden as $periode=>$datum)
    {
      $indexData[$SpecifiekeIndexFonds]['fondsKoers_'.$periode]=$this->getFondsKoers($SpecifiekeIndexFonds,$datum);
    }
  
    $indexBerekening = new indexHerberekening();
    $fondsWissel='';
    foreach($indices as $index)
    {
      if($index['specialeIndex']==1)
      {
        $specialeBenchmarks[]=$index['Beursindex'];
        $specialeIndexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
          $specialeIndexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
        $specialeIndexData[$index['Beursindex']]['performance'] =     ($specialeIndexData[$index['Beursindex']]['fondsKoers_eind'] - $specialeIndexData[$index['Beursindex']]['fondsKoers_begin']) / ($specialeIndexData[$index['Beursindex']]['fondsKoers_begin']/100 );
        
      }
      else
      {
        $maanden=$indexBerekening->getMaanden(db2jul($perioden['jan']),db2jul($perioden['eind']));
        foreach($maanden as $maand)
        {
          $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$maand['stop']);
          $maandPerf=getFondsPerformance($SpecifiekeIndexFonds,$maand['start'],$maand['stop']);
          $SpecifiekeIndex['performanceJaar']=((1+$SpecifiekeIndex['performanceJaar']/100)*(1+$maandPerf/100)-1)*100;
          if($SpecifiekeIndexFonds<>$SpecifiekeIndex['SpecifiekeIndex'])
          {
            if(!isset($fondsOmschrijvingen[$SpecifiekeIndexFonds]))
            {
              $query2 = "SELECT Fondsen.Fonds as SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Fondsen
	             WHERE Fondsen.Fonds  = '" . mysql_real_escape_string($SpecifiekeIndexFonds) . "' ";
              $DB->SQL($query2);
              $DB->Query();
              $omschrijving = $DB->lookupRecord();
              $fondsOmschrijvingen[$SpecifiekeIndexFonds]=$omschrijving['Omschrijving'];
            }
            $fondsWissel = vertaalTekst("Benchmark is op",$this->pdf->rapport_taal).' '. date('d-m-Y',db2jul($maand['stop'])).' '.vertaalTekst("gewijzigd van",$this->pdf->rapport_taal).' '. $fondsOmschrijvingen[$SpecifiekeIndexFonds].' '.vertaalTekst("naar",$this->pdf->rapport_taal).' '.$SpecifiekeIndex['Omschrijving'];
          }
        }
        $maanden=$indexBerekening->getMaanden(db2jul($perioden['begin']),db2jul($perioden['eind']));
        foreach($maanden as $maand)
        {
          $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$maand['stop']);
          $maandPerf=getFondsPerformance($SpecifiekeIndexFonds,$maand['start'],$maand['stop']);
          $SpecifiekeIndex['performance']=((1+$SpecifiekeIndex['performance']/100)*(1+$maandPerf/100)-1)*100;
  
          if($RapStartJaar <> $RapJaar && $SpecifiekeIndexFonds<>$SpecifiekeIndex['SpecifiekeIndex'])
          {
            if(!isset($fondsOmschrijvingen[$SpecifiekeIndexFonds]))
            {
              $query2 = "SELECT Fondsen.Fonds as SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Fondsen
	             WHERE Fondsen.Fonds  = '" . mysql_real_escape_string($SpecifiekeIndexFonds) . "' ";
              $DB->SQL($query2);
              $DB->Query();
              $omschrijving = $DB->lookupRecord();
              $fondsOmschrijvingen[$SpecifiekeIndexFonds]=$omschrijving['Omschrijving'];
            }
            $fondsWissel = vertaalTekst("Benchmark is op",$this->pdf->rapport_taal).' '. date('d-m-Y',db2jul($maand['stop'])).' '.vertaalTekst("gewijzigd van",$this->pdf->rapport_taal).' '. $fondsOmschrijvingen[$SpecifiekeIndexFonds].' '.vertaalTekst("naar",$this->pdf->rapport_taal).' '.$SpecifiekeIndex['Omschrijving'];
          }
          
        }
        $maanden=$indexBerekening->getMaanden(db2jul($perioden['kwart']),db2jul($perioden['eind']));
        foreach($maanden as $maand)
        {
          $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$maand['stop']);
          $maandPerf=getFondsPerformance($SpecifiekeIndexFonds,$maand['start'],$maand['stop']);
          $SpecifiekeIndex['performanceKwart']=((1+$SpecifiekeIndex['performanceKwart']/100)*(1+$maandPerf/100)-1)*100;
        }

        $indexData[$SpecifiekeIndexFonds]['Omschrijving']=$fondsOmschrijvingen[$SpecifiekeIndexFonds];
        $indexData[$SpecifiekeIndexFonds]['performanceEur'] =  $SpecifiekeIndex['performance'];
        $indexData[$SpecifiekeIndexFonds]['performanceKwartEur'] =  $SpecifiekeIndex['performanceKwart'];
        $indexData[$SpecifiekeIndexFonds]['performanceJanEur'] =  $SpecifiekeIndex['performanceJaar'];
        $benchmarks[]=$SpecifiekeIndexFonds;
  


        /*
        $benchmarks[]=$index['Beursindex'];
        $indexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
          $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
        $indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
   
       $indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
      $indexData[$index['Beursindex']]['performanceJanEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']*$indexData[$index['Beursindex']]['valutaKoers_jan'])/($indexData[$index['Beursindex']]['fondsKoers_jan']*$indexData[$index['Beursindex']]['valutaKoers_jan']/100 );

        */
      }
    }


    $this->pdf->SetY(120);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(128,50,20,20,30,30));
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
   
    if($RapStartJaar == $RapJaar)
    {
      $this->pdf->Rect($this->pdf->marge+128,120,150,count($benchmarks)*4+4+($fondsWissel==''?0:4));
      $this->pdf->row(array("", "Vergelijkingsmaatstaven", "" . date("d-m-Y", db2jul($perioden['kwart'])), "" . date("d-m-Y", db2jul($perioden['eind'])), "Rendement YTD", "Rendement QTD"));
    }
    else
    {
      $this->pdf->SetWidths(array(128,40,36.66,36.66,36.66));
      $this->pdf->SetAligns(array('L','L','R','R','R','R'));
      $this->pdf->Rect($this->pdf->marge+128,120,150,count($benchmarks)*4+12+($fondsWissel==''?0:4));
      
      $periodeTxt=date("d-m-Y",db2jul($perioden['begin'])).' - '.date("d-m-Y",db2jul($perioden['eind']));
      $kwartaalTxt=date("d-m-Y",db2jul($perioden['kwart'])).' - '.date("d-m-Y",db2jul($perioden['eind']));
      $jaarTxt=date("d-m-Y",db2jul($perioden['jan'])).' - '.date("d-m-Y",db2jul($perioden['eind']));
      $this->pdf->row(array("", "Vergelijkingsmaatstaven", "Rendement\n".$periodeTxt, "Rendement\n".$jaarTxt, "Rendement\n".$kwartaalTxt));
    }
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    foreach ($benchmarks as $fonds)
    {
      $fondsData=$indexData[$fonds];
      if($fondsData['Omschrijving']=='')
        $this->pdf->row(array(''));
      else
      {
        if($RapStartJaar == $RapJaar)
        {
          $this->pdf->row(array('', $fondsData['Omschrijving'],
                            $this->formatGetal($fondsData['fondsKoers_kwart'], 2),
                            $this->formatGetal($fondsData['fondsKoers_eind'], 2),
                            $this->formatGetal($fondsData['performanceJanEur'], 2) . "%",
                            $this->formatGetal($fondsData['performanceKwartEur'], 2) . "%"));
        }
        else
        {
          $this->pdf->row(array('', $fondsData['Omschrijving'],
                            $this->formatGetal($fondsData['performanceEur'], 2). "%",
                            $this->formatGetal($fondsData['performanceJanEur'], 2) . "%",
                            $this->formatGetal($fondsData['performanceKwartEur'], 2) . "%"));
        }
      }
    }
    if($fondsWissel<>'')
    {
      $this->pdf->SetWidths(array(128,50+20+20+30+30));
      $this->pdf->row(array('', $fondsWissel));
      $this->pdf->SetWidths(array(128,50,20,20,30,30));
    }


    if(count($specialeBenchmarks) > 0)
    {
      $this->pdf->SetY(150);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->SetWidths(array(150,60,20,20,20));
      $this->pdf->SetAligns(array('L','L','R','R','R'));
      $this->pdf->Rect($this->pdf->marge+150,150,120,count($specialeBenchmarks)*4+4);
      $this->pdf->row(array("","Overige marktindices ter informatie","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
      unset($this->pdf->CellBorders);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      foreach ($specialeBenchmarks as $fonds)
      {
        $fondsData=$specialeIndexData[$fonds];
        if($fondsData['Omschrijving']=='')
          $this->pdf->row(array(''));
        else
          $this->pdf->row(array('',$fondsData['Omschrijving'],
                            $this->formatGetal($fondsData['fondsKoers_begin'],2),
                            $this->formatGetal($fondsData['fondsKoers_eind'],2),
                            $this->formatGetal($fondsData['performance'],2)."%"));
      }
    }

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

function getGrootboeken()
{
  $vertaling=array();
  $db=new DB();
  $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    if($data['Grootboekrekening']=='BEW')
      $data['Omschrijving']="Administratiekosten bank";      
    if($data['Grootboekrekening']=='KOST')
      $data['Omschrijving']="Transactiekosten bank";      
      
    $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
  }
  return $vertaling;
}




function addResultaat()
{
  
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
   $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
  $rapParts=explode("-",$this->rapportageDatum);
  
  $kwartaal = ceil(date("n",db2jul($this->rapportageDatum))/3);
  if($kwartaal==1)
    $beginKwartaal=$rapParts[0]."-01-01";
  elseif($kwartaal==2)
    $beginKwartaal=$rapParts[0]."-03-31";
  elseif($kwartaal==3)
    $beginKwartaal=$rapParts[0]."-06-30";
  elseif($kwartaal==4)
    $beginKwartaal=$rapParts[0]."-09-30";
  if(db2jul($beginKwartaal)<db2jul($this->pdf->PortefeuilleStartdatum))
    $beginKwartaal=$this->pdf->PortefeuilleStartdatum;
  
  $vetralingGrootboek=$this->getGrootboeken();
  
    $att=new ATTberekening_L99($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Categorien');
    $this->waarden['Kwartaal']=$this->att->bereken($beginKwartaal,$this->rapportageDatum,'Categorien');
    
   // $categorien=array_keys($this->waarden['Periode']);
    $categorien=array();
    foreach(array_keys($this->att->categorien) as $categorie)
    {
      if($this->waarden['Periode'][$categorie]['procent'] <> 0 || $this->waarden['Periode'][$categorie]['beginwaarde'] <> 0 || $this->waarden['Periode'][$categorie]['eindwaarde'] <> 0)
      {
        $categorien[]=$categorie;
      }
    }

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
  $header=array("","");
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
  $stortingen=array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $effectenmutaties=array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
  
  
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $rendementKwartaal=array("",vertaalTekst("Rendement lopend kwartaal",$this->pdf->rapport_taal));
  
  $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde resultaten",$this->pdf->rapport_taal)); //
  //$ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
  
$gerealiseerd=array("",vertaalTekst("Gerealiseerde resultaten",$this->pdf->rapport_taal)); //
//$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Koersresultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
$rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
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
      $effectenmutaties[]=$this->formatGetal($perfWaarden['stort'],0);
      $effectenmutaties[]='';
      $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
      $stortingen[]='';
      $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
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
      $rendementKwartaal[]='';
      $rendementKwartaal[]='';
    }
    else
    {
    $rendement[]=$this->formatGetal($perfWaarden['procent'],2);
    $rendement[]='%';
    $rendementKwartaal[]=$this->formatGetal($this->waarden['Kwartaal'][$categorie]['procent'],2);
    $rendementKwartaal[]='%';
    }
    if($categorie=='totaal')
    {
    $ongerealiseerd[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat']+$perfWaarden['ongerealiseerdValutaResultaat'],0);
    $ongerealiseerd[]='';
    //$ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
    //$ongerealiseerdValuta[]='';
    $gerealiseerd[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat']+$perfWaarden['gerealiseerdValutaResultaat'],0);
    $gerealiseerd[]='';
    //$gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
    //$gerealiseerdValuta[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
    $rente[]='';
    $totaalOpbrengst[]='';
    $totaalOpbrengst[]='';
    $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0);
    $totaalOpbrengst[]='';
    $totaalKosten[]='';
    $totaalKosten[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
    $totaalKosten[]='';
    $totaal[]='';
    $totaal[]='';
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


  	$this->pdf->widthB = array(0,70,24,6,24,6,24,6,24,6,24,6,24,6,24,6,24,6);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,6,30,6,30,6,30,6,30,6,30,6);
		$this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
  

//listarray($perfWaarden);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  	
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,245,245);
    $this->headerTop=$this->pdf->GetY();

//    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
//    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
//		$this->pdf->Rect($this->pdf->marge+70, $this->pdf->getY(), (count($header)-2)*15, 8 , 'F');
	   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row($header);
  //  unset($this->pdf->fillCell);
//    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

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
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->ln();

    $this->pdf->CellBorders = array();
    //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		//$this->pdf->CellBorders = $volOnder;
    $this->pdf->row($rendementKwartaal);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
    $this->pdf->ln();
    $this->hoogteBeleggingsresultaat=$this->pdf->getY();
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row($ongerealiseerd);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		//$this->pdf->row($ongerealiseerdValuta);
    $this->pdf->row($gerealiseerd);
    //$this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
	//	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
	  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
		$keys=array();
		//foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
		//  $keys[]=$key;

    $categorien=array('totaal');
    foreach ($opbrengstCategorien as $grootboek)
	  {
		    $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
       // foreach($perfWaarden as $port=>$waarden)
       
        foreach($categorien as $categorie)
        {
          $perfWaarden=$this->waarden['Periode'][$categorie];
          $tmp[]=$this->formatGetal($perfWaarden['grootboekOpbrengsten'][$grootboek],0);
          $tmp[]='';
        }
		  //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			  $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
		}
    $subBovenTotalen=array('','','TS');
    $this->pdf->CellBorders = $subBovenTotalen;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));

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
          $perfWaarden=$this->waarden['Periode'][$categorie];
       
        $tmp[]=$this->formatGetal($perfWaarden['grootboekKosten'][$grootboek],0);
        $tmp[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}
    $this->pdf->CellBorders = $subBovenTotalen;
  	$this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = array();
    //$this->pdf->CellBorders = $volOnder;
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

}




}
?>