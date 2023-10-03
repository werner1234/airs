<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_105/ATTberekening_L105.php");

class RapportPERF_L105
{
  
  function RapportPERF_L105($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "PERF";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->pdf->rapport_titel = "Bijdrage in het resultaat";
    
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L105($this);
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
    $this->pdf->widthA = array(5,95,30,5,30,5,30,120);
    $this->pdf->alignA = array('L','L','R','L','R');
    
    // voor kopjes
    $this->pdf->widthB = array(0,100,30,5,30,5,30,120);
    $this->pdf->alignB = array('L','L','R','L','R');
    
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    

    $this->addResultaat();

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
      if($data['Grootboekrekening']=='RENTE')
        $data['Omschrijving']="Rente (spaar)rekeningen";
      
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
    
    $vetralingGrootboek=$this->getGrootboeken();
  
    if($this->pdf->portefeuilledata['PerformanceBerekening']==3 && intval(substr($this->rapportageDatum,0,4))>=2020)
    {
      $perioden='maandenTWR';
    }
    else
    {
      $perioden = 'maanden';
    }
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->portefeuilledata['RapportageValuta'],'hoofdcategorie',$perioden);
    $categorien=array_keys($this->waarden['Periode']);//array_merge(array('totaal'),array_keys($this->waarden['Periode']));
    //unset($categorien[count($categorien)-1]);
    
     //listarray($this->att->totalen);exit;
//listarray($this->waarden['Periode']);
    
    //$startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    //  $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    //  $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
    
    //$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    //$this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
   // $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    // listarray($this->pdf->portefeuilles);
    $fillArray=array(0,1);
    $subOnder=array('','');
    $volOnder=array('U','U');
    $subBoven=array('','');
    $header=array("",'');//vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
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
      $header[]=vertaalTekst($this->att->categorien[$categorie],$this->pdf->rapport_taal);
      $header[]='';
      $samenstelling[]='';
      $samenstelling[]='';
      // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    }
    
    $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $stortingen=array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $onttrekking=array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $effectenmutaties=array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
    //$directeOpbrengsten=array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
    
    
    $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    //$resultaatBruto=array("",vertaalTekst("Bruto resultaat voor kosten en belastingen",$this->pdf->rapport_taal));
    $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    //$rendementBruto=array("",vertaalTekst("Bruto rendement voor kosten en belastingen",$this->pdf->rapport_taal));
    //$rendementBenchmark=array("",vertaalTekst("Benchmark-rendement over verslagperiode",$this->pdf->rapport_taal));
    $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde resultaten",$this->pdf->rapport_taal)); //
    //$ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
    //$ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    $gerealiseerd=array("",vertaalTekst("Gerealiseerde resultaten",$this->pdf->rapport_taal)); //
    //$gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
    //$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    $valutaResultaat=array("",vertaalTekst("Koersresultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
    $rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
    $totaalOpbrengst=array("",'');//totaalOpbrengst
    
    //$totaalKostenBoven=array("",vertaalTekst("Ingehouden kosten en belastingen",$this->pdf->rapport_taal));//
    $totaalKosten=array("","");   //totaalKosten
    $totaal=array("",vertaalTekst("Totaal resultaat over verslagperiode",$this->pdf->rapport_taal));  //totaalOpbrengst-totaalKosten
    
    
    foreach($categorien as $categorie)
    {
      unset($this->waarden['Periode'][$categorie]['perfWaarden']);
    }
    
    //listarray($categorien);exit;
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
        $directeOpbrengsten[]='';
        $directeOpbrengsten[]='';
      }
      else
      {
        if($categorie=='H-Liq')
        {
          $effectenmutaties[] = $this->formatGetal(($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'])-$perfWaarden['resultaat'], 0);
        }
        else
        {
          $effectenmutaties[] = $this->formatGetal($perfWaarden['stort'], 0);
        }
        $effectenmutaties[]='';
        $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
        $stortingen[]='';
        $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
        $onttrekking[]='';
        $directeOpbrengsten[]=$this->formatGetal($perfWaarden['opbrengst'],0);
        $directeOpbrengsten[]='';
      }
      
      $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
        $perfWaarden['gerealiseerdResultaat']+
        $perfWaarden['ongerealiseerdResultaat']+
        $perfWaarden['opgelopenrente']+$perfWaarden['kosten'];
      
      $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR);
     // listarray($perfWaarden);
      $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
      
      $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
      $resultaat[]='';
      //$resultaatBruto[]=$this->formatGetal($perfWaarden['resultaat']-$perfWaarden['kosten'],0);
      //$resultaatBruto[]='';
      //echo "$categorie <br>\n";ob_flush();
      if($categorie=='Liquiditeiten'||$categorie=='H-Liq')
      {
       // listarray($perfWaarden);
        $rendement[] = '';
        $rendement[] = '';
      }
      else
      {
        $rendement[] = $this->formatGetal($perfWaarden['procent'], 2);
        $rendement[] = '%';
      }
      //$rendementBruto[]=$this->formatGetal($perfWaarden['procentBruto'],2);
      //$rendementBruto[]='%';
      //$rendementBenchmark[]=$this->formatGetal($this->getBenchmarkRendement($categorie,$this->rapportageDatumVanaf,$this->rapportageDatum)*100,2);
      //$rendementBenchmark[]='%';
      //listarray($perfWaarden);
      $ongerealiseerd[]=$this->formatGetal($perfWaarden['ongerealiseerdResultaat'],0);
      $ongerealiseerd[]='';
      //$ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0);
      //$ongerealiseerdFonds[]='';
      //$ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
      //$ongerealiseerdValuta[]='';
      $gerealiseerd[]=$this->formatGetal($perfWaarden['gerealiseerdResultaat'],0);
      $gerealiseerd[]='';
      //$gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0);
      //$gerealiseerdFonds[]='';
      //$gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
      //$gerealiseerdValuta[]='';
      $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
      $valutaResultaat[]='';
      $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
      $rente[]='';
      $totaalOpbrengst[]=$this->formatGetal($perfWaarden['resultaat']-$perfWaarden['kosten'],0);
      $totaalOpbrengst[]='';
      $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
      $totaalKosten[]='';
      /*
      if($categorie=='totaal'||$categorie=='H-Liq')
        $totaalKostenBoven[]=$this->formatGetal($perfWaarden['kosten'],0);
      else
        $totaalKostenBoven[]='';
      $totaalKostenBoven[]='';
      */
      $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
      $totaal[]='';
      
      
      
      foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $opbrengstCategorien[$categorie]=$categorie;
      foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $kostenCategorien[$categorie]=$categorie;
      
    }
    
    
    $this->pdf->widthB = array(0,85,25,5,25,5,25,5,25,5,25,5,25,5);
    $this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,5,30,5,30,5,30,5,30,5,30,5);
    $this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R');


//listarray($perfWaarden);
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $this->headerTop=$this->pdf->GetY();
    
    
    //$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
   // $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $curY=$this->pdf->getY();
    //$this->pdf->rect($this->pdf->marge,$curY,297-$this->pdf->marge*2,8,'F');
    $this->pdf->row($header);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
   // $this->pdf->setY($curY+12);
    $this->pdf->row($perbegin);
    //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
    // subtotaal
    $this->pdf->ln(2);
    $this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
    
    $this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->row($effectenmutaties);
    //$this->pdf->row($directeOpbrengsten);
    
    
    //$this->pdf->row($totaalKostenBoven);
    //$this->pdf->ln();
    // $this->pdf->CellBorders = $subOnder;
    //
    
    //$this->pdf->row($resultaatBruto);
    //$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($resultaat);
    //$this->pdf->row($rendementBruto);
    //$this->pdf->ln();
    
    //$this->pdf->ln();
    
    // $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    /*
    if($this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
    {
      $this->pdf->ln(2);
      $this->pdf->row($rendementBenchmark);
    }
    */
    $ypos = $this->pdf->GetY();
    
    $this->pdf->SetY($ypos);
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
    $this->pdf->row($samenstelling);//,"","","",""));
    //$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    $this->hoogteBeleggingsresultaat=$this->pdf->getY();
    $this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->SetFillColor(230);
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    //$this->pdf->fillCell=$fillArray;
    $this->pdf->row($ongerealiseerd);
    //$this->pdf->row($ongerealiseerdFonds);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
    $this->pdf->fillCell = array();
    //$this->pdf->row($ongerealiseerdValuta);
    //$this->pdf->fillCell=$fillArray;
    $this->pdf->row($gerealiseerd);
    //$this->pdf->row($gerealiseerdFonds);
    $this->pdf->fillCell = array();
    //$this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
    //	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
    //$this->pdf->fillCell=$fillArray;
    $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
    $this->pdf->fillCell = array();
    $this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
    $keys=array();
    //foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
    //  $keys[]=$key;
    
    
    $i=0;
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
      //if($i%2==0)
     //   $this->pdf->fillCell=$fillArray;
     // else
     //   $this->pdf->fillCell=array();
      //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
      $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
      $i++;
    }
    $this->pdf->fillCell=array();
    $this->pdf->CellBorders = $subBoven;
   // $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    //$this->pdf->ln();
    $this->pdf->CellBorders = array();
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $i=0;
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
      
      //if($i%2==0)
      //  $this->pdf->fillCell=$fillArray;
      //else
      //  $this->pdf->fillCell=array();
      $i++;
    }
    $this->pdf->CellBorders = $subBoven;
    $this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
    $posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = array();
    //$this->pdf->CellBorders = $volOnder;
    $this->pdf->Ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
    $actueleWaardePortefeuille = 0;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    $this->pdf->fillCell=array();
  }
  

  
  
  
}
?>