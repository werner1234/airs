<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/08 05:43:48 $
File Versie					: $Revision: 1.20 $

$Log: RapportATT_L77.php,v $
Revision 1.20  2020/06/08 05:43:48  rvv
*** empty log message ***

Revision 1.19  2020/05/02 15:57:50  rvv
*** empty log message ***

Revision 1.18  2019/01/05 09:16:36  rvv
*** empty log message ***

Revision 1.17  2019/01/02 16:18:56  rvv
*** empty log message ***

Revision 1.16  2018/11/16 10:18:07  rvv
*** empty log message ***

Revision 1.15  2018/11/07 17:08:06  rvv
*** empty log message ***

Revision 1.14  2018/11/03 18:45:31  rvv
*** empty log message ***

Revision 1.13  2018/10/27 16:49:57  rvv
*** empty log message ***

Revision 1.12  2018/10/24 16:00:59  rvv
*** empty log message ***

Revision 1.11  2018/10/23 08:57:57  rvv
*** empty log message ***

Revision 1.10  2018/10/22 10:45:06  rvv
*** empty log message ***

Revision 1.9  2018/10/21 09:42:37  rvv
*** empty log message ***

Revision 1.8  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.7  2018/10/07 13:39:46  rvv
*** empty log message ***

Revision 1.6  2018/10/07 10:19:56  rvv
*** empty log message ***

Revision 1.5  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.4  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.3  2018/09/15 17:45:24  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L32.php");

class RapportATT_L32
{
  
  function RapportATT_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "ATT";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->pdf->rapport_titel = "Bijdrage in het resultaat";
    
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L32($this);
    $this->pdf->underlinePercentage=0.90;
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
    
    $this->getKleuren();
    $this->addResultaat();
    
    
    // $gebruikteCategorien=$this->addZorgBar();
    //  $this->plotZorgBar4(80,40,$gebruikteCategorien);
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
    
    //$att=new ATTberekening_L77($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'','attributie');
    
    $categorien=array_keys($this->waarden['Periode']);//array_merge(array('totaal'),array_keys($this->waarden['Periode']));
    //unset($categorien[count($categorien)-1]);
    
    //listarray($this->att->totalen);exit;
//listarray($this->waarden['Periode']);
    
    //$startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    //  $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    //  $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    // listarray($this->pdf->portefeuilles);
    $fillArray=array(0,1);
    $subOnder=array('','');
    $volOnder=array('US','US');
    $subBoven=array('','');
    $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
    $samenstelling=array("",vertaalTekst("Samenstelling resultaat",$this->pdf->rapport_taal));
    
    foreach($categorien as $categorie)
    {
      $volOnder[]='US';
//      $volOnder[]='U';
      $subOnder[]='US';
//      $subOnder[]='';
      $subBoven[]='TS';
//      $subBoven[]='';
      $fillArray[]=1;
//      $fillArray[]=1;
      $header[]=vertaalTekst($this->att->categorien[$categorie],$this->pdf->rapport_taal);
//      $header[]='';
      $samenstelling[]='';
//      $samenstelling[]='';
      // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    }
    
    $perbegin=array("",vertaalTekst("Waarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $waardeRapdatum=array("",vertaalTekst("Waarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $stortingen=array("",vertaalTekst("Totaal stortingen",$this->pdf->rapport_taal));
    $onttrekking=array("",vertaalTekst("Totaal onttrekkingen",$this->pdf->rapport_taal));
    $effectenmutaties=array("",vertaalTekst("Effectenmutaties",$this->pdf->rapport_taal));
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
    $valutaResultaat=array("",vertaalTekst("Koersresultaat VV-rekeningen",$this->pdf->rapport_taal)); //
    $rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
    $totaalOpbrengst=array("",'');//totaalOpbrengst
    
    //$totaalKostenBoven=array("",vertaalTekst("Ingehouden kosten en belastingen",$this->pdf->rapport_taal));//
    $totaalKosten=array("","");   //totaalKosten
    $totaal=array("",vertaalTekst("Totaal resultaat",$this->pdf->rapport_taal));  //totaalOpbrengst-totaalKosten
    
    
    foreach($categorien as $categorie)
    {
      unset($this->waarden['Periode'][$categorie]['perfWaarden']);
    }
    
    //listarray($categorien);exit;
    foreach($categorien as $categorie)
    {
      $perfWaarden=$this->waarden['Periode'][$categorie];
      $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
//      $perbegin[]='';
      $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
//      $waardeRapdatum[]='';
      $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
//      $mutwaarde[]='';
      
      if($categorie=='totaal')
      {
        $effectenmutaties[]='';
//        $effectenmutaties[]='';
        //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
        //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
        $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
//        $stortingen[]='';
        $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
//        $onttrekking[]='';
        $directeOpbrengsten[]='';
//        $directeOpbrengsten[]='';
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
//        $effectenmutaties[]='';
        $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
//        $stortingen[]='';
        $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
//        $onttrekking[]='';
        $directeOpbrengsten[]=$this->formatGetal($perfWaarden['opbrengst'],0);
//        $directeOpbrengsten[]='';
      }
      
      $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
        $perfWaarden['gerealiseerdResultaat']+
        $perfWaarden['ongerealiseerdResultaat']+
        $perfWaarden['opgelopenrente']+$perfWaarden['kosten'];
      
      $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR);
      // listarray($perfWaarden);
      $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
      
      $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
//      $resultaat[]='';
      //$resultaatBruto[]=$this->formatGetal($perfWaarden['resultaat']-$perfWaarden['kosten'],0);
      //$resultaatBruto[]='';
      if($categorie=='H-Liq')
      {
        // listarray($perfWaarden);
        $rendement[] = '';
//        $rendement[] = '';
      }
      else
      {
        $rendement[] = $this->formatGetal($perfWaarden['procent'], 2).'%';
//        $rendement[] = '%';
      }
      //$rendementBruto[]=$this->formatGetal($perfWaarden['procentBruto'],2);
      //$rendementBruto[]='%';
      //$rendementBenchmark[]=$this->formatGetal($this->getBenchmarkRendement($categorie,$this->rapportageDatumVanaf,$this->rapportageDatum)*100,2);
      //$rendementBenchmark[]='%';
      //listarray($perfWaarden);
      $ongerealiseerd[]=$this->formatGetal($perfWaarden['ongerealiseerdResultaat'],0);
//      $ongerealiseerd[]='';
      //$ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0);
      //$ongerealiseerdFonds[]='';
      //$ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
      //$ongerealiseerdValuta[]='';
      $gerealiseerd[]=$this->formatGetal($perfWaarden['gerealiseerdResultaat'],0);
//      $gerealiseerd[]='';
      //$gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0);
      //$gerealiseerdFonds[]='';
      //$gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
      //$gerealiseerdValuta[]='';
      $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
//      $valutaResultaat[]='';
      $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
//      $rente[]='';
      $totaalOpbrengst[]=$this->formatGetal($perfWaarden['resultaat']-$perfWaarden['kosten'],0);
//      $totaalOpbrengst[]='';
      $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
//      $totaalKosten[]='';
      /*
      if($categorie=='totaal'||$categorie=='H-Liq')
        $totaalKostenBoven[]=$this->formatGetal($perfWaarden['kosten'],0);
      else
        $totaalKostenBoven[]='';
      $totaalKostenBoven[]='';
      */
      $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
//      $totaal[]='';
      
      
      
      foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $opbrengstCategorien[$categorie]=$categorie;
      foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $kostenCategorien[$categorie]=$categorie;
      
    }
    
    $w=($this->pdf->w-(2*$this->pdf->marge)-55)/8;
    $this->pdf->widthB = array(0,55,$w,$w,$w,$w,$w,$w,$w,$w);
    $this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,5,30,5,30,5,30,5,30,5,30,5);
    $this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R');


//listarray($perfWaarden);
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $this->headerTop=$this->pdf->GetY();
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $curY=$this->pdf->getY();
    $this->pdf->rect($this->pdf->marge,$curY,297-$this->pdf->marge*2,8,'F');
    $this->pdf->row($header);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->setY($curY+12);
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
       // $tmp[]='';
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
      //  $tmp[]='';
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
  
  
  
  function SetLegends($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.2f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
  
  function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
  {
    
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
    $this->SetLegends($data,$format);
    
    
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
    if($minVal >0)
      $minVal=0;
    
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    $hBar = ($hDiag / ($this->pdf->NbVal + 1));
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
    
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
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
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $i=0;
    
    //$this->pdf->SetXY(0, $YDiag);
    //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
    //$this->pdf->SetXY($nullijn, $YDiag);
    //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
    $this->pdf->SetXY($XDiag, $YDiag);
    $this->pdf->Cell($lDiag, $hval-4, 'Contributie rendement',0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
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
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
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