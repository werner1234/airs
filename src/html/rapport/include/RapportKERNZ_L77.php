<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/02 15:57:50 $
File Versie					: $Revision: 1.2 $

$Log: RapportKERNZ_L77.php,v $
Revision 1.2  2020/05/02 15:57:50  rvv
*** empty log message ***

Revision 1.1  2020/04/29 15:58:17  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportKERNZ_L77
{
  
  function RapportKERNZ_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    global $__appvar;
    $this->__appvar = $__appvar;
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "KERNZ";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->underlinePercentage=1;
    
    if($this->pdf->rapport_PERF_titel)
      $this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
    else
      $this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;

    
    if(strval($pdf->rapport_ATT_decimaal) != '')
      $this->bedragDecimalen=$pdf->rapport_ATT_decimaal;
    else
      $this->bedragDecimalen=0;
    
   
    $this->db = new DB();
    $this->perfWaarden=array();


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
  

  
  
  function createRows()
  {
    $row['waardeVanaf'] = array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $row['waardeTot'] = array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $row['mutatiewaarde'] = array("",vertaalTekst("Mutatie waarde vermogen",$this->pdf->rapport_taal));
    $row['totaalStortingen'] = array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['totaalOnttrekkingen'] = array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['directeOpbrengsten'] = array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
    $row['toegerekendeKosten'] = array("",vertaalTekst("Toegerekende kosten",$this->pdf->rapport_taal));
    $row['resultaatVerslagperiode'] = array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcent'] = array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcentJaar'] = array("",vertaalTekst("Rendement over lopende jaar",$this->pdf->rapport_taal));
    $row['gerealiseerdKoersresultaat'] = array("",vertaalTekst("gerealiseerdKoersresultaat",$this->pdf->rapport_taal));
    $row['ongerealiseerdeKoersResultaaten'] = array("",vertaalTekst("ongerealiseerdeKoersResultaaten",$this->pdf->rapport_taal));
    $row['opgelopenRentes'] = array("",vertaalTekst("opgelopenRentes",$this->pdf->rapport_taal));
    $row['totaal'] = array("",vertaalTekst("Totaal Performance",$this->pdf->rapport_taal));
    
    
    foreach ($this->categorien as $categorie)
    {
      $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode']['mutatie'][$categorie] - $this->waarden['rapportagePeriode']['stortingen'][$categorie] + $this->waarden['rapportagePeriode']['onttrekkingen'][$categorie] + $this->waarden['rapportagePeriode']['opbrengsten'][$categorie] - $this->waarden['rapportagePeriode']['kosten'][$categorie];
      if ($categorie == 'Totaal')
      {
        $resultaatCorrectie = $resultaatVerslagperiode['Totaal'] - $this->waarde['opgelopenRentes'][$categorie] - $this->waarde['ongerealiseerdeKoersResultaaten'][$categorie] -
          ($this->waardenPerGrootboek['totaalOpbrengst'] - $this->waardenPerGrootboek['totaalKosten']);
        if(round($resultaatCorrectie,1) != round($this->waarde['gerealiseerdKoersresultaat'][$categorie],1))//correctie vreemde valuta
        {
          $this->waarde['gerealiseerdKoersresultaat'][$categorie]  = $resultaatCorrectie ;
        }
      }
      
      array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode']['beginWaarde'][$categorie],$this->bedragDecimalen,true));
      array_push($row['waardeVanaf'],"");
      
      array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode']['eindWaarde'][$categorie],$this->bedragDecimalen));
      array_push($row['waardeTot'],"");
      
      array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode']['mutatie'][$categorie],$this->bedragDecimalen));
      array_push($row['mutatiewaarde'],"");
      
      if ($categorie == 'Liquiditeiten')
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcentJaar'],' ');
        array_push($row['rendementProcentJaar'],' ');
      }
      else
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
        array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode']['performance'][$categorie],2));
        array_push($row['rendementProcent'],'%');
        array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar']['performance'][$categorie],2));
        array_push($row['rendementProcentJaar'],'%');
      }
      
      array_push($row['totaalStortingen'],"");
      array_push($row['totaalOnttrekkingen'],"");
      
      if ($categorie == 'Totaal')
      {
        array_push($row['directeOpbrengsten'],'0');
        array_push($row['toegerekendeKosten'],'0');
      }
      else
      {
        array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode']['opbrengsten'][$categorie],$this->bedragDecimalen));
        array_push($row['toegerekendeKosten'],$this->formatGetal($this->waarden['rapportagePeriode']['kosten'][$categorie],$this->bedragDecimalen));
      }
      array_push($row['directeOpbrengsten'],"");
      array_push($row['toegerekendeKosten'],"");
      
      array_push($row['resultaatVerslagperiode'],$this->formatGetal($resultaatVerslagperiode[$categorie],$this->bedragDecimalen));
      array_push($row['resultaatVerslagperiode'],"");
    }
    return $row;
  }
  
  
  function waardenPerGrootboek()
  {
    
    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening,
		Grootboekrekeningen.Kosten ,Grootboekrekeningen.Opbrengst,".
      "SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) -  ".
      "SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery )AS waarde ".
      "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
      "WHERE ".
      "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
      "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
      "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
      "  (Grootboekrekeningen.Kosten = '1' || Grootboekrekeningen.Opbrengst ='1') ".
      "GROUP BY Rekeningmutaties.Grootboekrekening ".
      "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $waardenPerGrootboek = array();
    while($grootboek = $DB->nextRecord())
    {
      if($grootboek['Opbrengst']=='1')
      {
        $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
        $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['bedrag'] += $grootboek['waarde'];
        $waardenPerGrootboek['totaalOpbrengst'] += $grootboek['waarde'];
      }
      else
      {
        if($grootboek[Grootboekrekening] == "KNBA")
        {
          $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = "Bankkosten en provisie";
          $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
        }
        else if($grootboek[Grootboekrekening] == "KOBU")
        {
          $waardenPerGrootboek['kosten']['KOST']['bedrag'] -= $grootboek['waarde'];
          $waardenPerGrootboek['kosten']['KOST']['omschrijving'] = "Transactiekosten";
        }
        else
        {
          $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
          $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
        }
        $waardenPerGrootboek['totaalKosten'] -= $grootboek['waarde'];
      }
    }
    
    return $waardenPerGrootboek;
  }
  
  
  function writeRapport()
  {
  
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    

      $x=23;
    
    $this->pdf->widthA = array(0,95,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3);
    $this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->widthB = array(0,95,30,10,30,116);
    $this->pdf->alignB = array('L','L','R','R','R','R','R','R');
    
    
    
    if(is_array($this->pdf->__appvar['consolidatie']))
    {
      $this->pdf->templateVars['KERNZPaginas']=$this->pdf->page+1;
      $this->pdf->templateVarsOmschrijving['KERNZPaginas']=$this->pdf->rapport_titel;
      
      $fillPortefeuilles=$this->pdf->portefeuilles;
      $fillPortefeuilles[]=$this->portefeuille;
      
      foreach($fillPortefeuilles as $portefeuille)
      {
        if(!isset($this->perfWaarden[$portefeuille]))
          $this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
      }
      
      
      $fillPortefeuilles=array();
      $portefeuillesVerwijderen=array();
      foreach( $this->perfWaarden as $portefeuille=>$pdata)
      {
        if($pdata['waardeBegin']<>0 || $pdata['waardeEind']<>0 || $pdata['resultaatVerslagperiode']<>0)
        {
          if($portefeuille<>$this->portefeuille)
            $fillPortefeuilles[]=$portefeuille;
        }
        else
        {
          $portefeuillesVerwijderen[]=$portefeuille;
        }
      }
      foreach($portefeuillesVerwijderen as $port)
      {
        unset($this->perfWaarden[$port]);
      }
      
      
      
      
      
      
      
      $backup=$this->pdf->portefeuilles;
      $this->pdf->portefeuilles=$fillPortefeuilles;
      
      $aantalPortefeuilles=count($this->pdf->portefeuilles);
      if($aantalPortefeuilles>5)
      {
        $n=1;
        $p=0;
        $verdeling=array();
        $tmp=array();
        foreach($this->pdf->portefeuilles as $index=>$portefeuille)
        {
          //echo "$n $p $aantalPortefeuilles $portefeuille <br>\n";
          $tmp[]=$portefeuille;
          if($n%5==0 || $n == $aantalPortefeuilles)
          {
            $verdeling[$p]=$tmp;
            $tmp=array();
            $p++;
            // $n=0;
          }
          
          $n++;
        }
        //listarray($verdeling);exit;
        foreach($verdeling as $pagina=>$portefeuilles)
        {
          $this->pdf->portefeuilles=$portefeuilles;
          $this->addconsolidatie();
        }
        
      }
      else
        $this->addconsolidatie();
      if(is_array($backup))
        $this->pdf->portefeuilles=$backup;
      
    }
    
    if($this->pdf->debug)
    {
      // listarray($this->berekening->performance);flush();
      // exit;
    }
  }
  
  
  
  
  function addconsolidatie()
  {
    
    if(!isset($this->pdf->__appvar['consolidatie']))
    {
      $this->pdf->__appvar['consolidatie']=1;
      $this->pdf->portefeuilles=array($this->portefeuille);
    }
    $this->pdf->doubleHeader=true;
    $this->pdf->addPage();
    
    $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));
    
    //	$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    //  $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    //  $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    // listarray($this->pdf->portefeuilles);
    $fillArray=array(0,1);
    $subOnder=array('','');
    $volOnder=array('U','U');
    $subBoven=array('','');
    $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
    $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
    
    $db=new DB();
    
    if(count($this->pdf->portefeuilles)<6)
      $portefeuilles[]=$this->portefeuille;
    else
      $portefeuilles=array();
    foreach($this->pdf->portefeuilles as $portefeuille)
      $portefeuilles[]=$portefeuille;
    $longName=false;
    
    $perfWaarden=array();
    foreach($portefeuilles as $portefeuille)
    {
      if(strlen($portefeuille)>15)
        $longName=true;
      $query="SELECT Depotbanken.omschrijving,Portefeuilles.ClientVermogensbeheerder FROM Depotbanken JOIN Portefeuilles ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE Portefeuilles.Portefeuille='".$portefeuille."'";
      $db->SQL($query);
      $depotbank=$db->lookupRecord();
      $volOnder[]='U';
      $volOnder[]='U';
      $subOnder[]='U';
      $subOnder[]='';
      $subBoven[]='T';
      $subBoven[]='';
      $fillArray[]=1;
      $fillArray[]=1;
      if($portefeuille==$this->portefeuille)
        $header[]=vertaalTekst("Totaal",$this->pdf->rapport_taal);
      else
      {
        if($depotbank['ClientVermogensbeheerder']<>'')
          $header[] = $portefeuille;// . "\n" . $depotbank['ClientVermogensbeheerder'];
        else
          $header[] = $portefeuille;// . "\n" . $depotbank['omschrijving'];
      }
      $header[]='';
      $samenstelling[]='';
      $samenstelling[]='';
      if(!isset($this->perfWaarden[$portefeuille]))
        $this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
      
      $perfWaarden[$portefeuille]=$this->perfWaarden[$portefeuille];
    }
    
    foreach($perfWaarden as $port=>$waarden)
    {
      foreach($waarden['opbrengstenPerGrootboek'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $opbrengstCategorien[$categorie]=$categorie;
      foreach($waarden['kostenPerGrootboek'] as $categorie=>$waarde)
        if(round($waarde,2)!=0.00)
          $kostenCategorien[$categorie]=$categorie;
    }
    
    $perbegin=array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $waardeRapdatum=array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $mutwaarde=array("",vertaalTekst("Mutatie waarde vermogen",$this->pdf->rapport_taal));
    $stortingen=array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $onttrekking=array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
    $gerealiseerd=array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
    $valutaResultaat=array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal)); //
    $rente=array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal));//
    $totaalOpbrengst=array("","");//totaalOpbrengst
    $aandeel=array("",vertaalTekst("Percentage v/h vermogen",$this->pdf->rapport_taal));//
    
    $totaalKosten=array("","");   //totaalKosten
    $totaal=array("",vertaalTekst("Resultaat lopende jaar",$this->pdf->rapport_taal));   //totaalOpbrengst-totaalKosten
    
    foreach($perfWaarden as $portefeuille=>$waarden)
    {
      $perbegin[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeBegin'],0,true);
      $perbegin[]='';
      $waardeRapdatum[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind'],0,true);
      $waardeRapdatum[]='';
      $mutwaarde[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeMutatie'],0,true);
      $mutwaarde[]='';
      $stortingen[]=$this->formatGetal($perfWaarden[$portefeuille]['stortingen'],0);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden[$portefeuille]['onttrekkingen'],0);
      $onttrekking[]='';
      $resultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['resultaatVerslagperiode'],0);
      $resultaat[]='';
      $rendement[]=$this->formatGetal($perfWaarden[$portefeuille]['rendementProcent'],2);
      $rendement[]='%';
      $ongerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat'],0);
      $ongerealiseerd[]='';
      $gerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat'],0);
      $gerealiseerd[]='';
      $valutaResultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['koersResulaatValutas'],0);
      $valutaResultaat[]='';
      $rente[]=$this->formatGetal($perfWaarden[$portefeuille]['opgelopenRente'],0);
      $rente[]='';
      $totaalOpbrengst[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst'],0);
      $totaalOpbrengst[]='';
      $totaalKosten[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalKosten'],0);
      $totaalKosten[]='';
      $totaal[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst']-$perfWaarden[$portefeuille]['totaalKosten'],0);
      $totaal[]='';
      $aandeel[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind']/$this->perfWaarden[$this->portefeuille]['waardeEind']*100,1);
      $aandeel[]='%';
      
    }
    
    // if($longName==true && count($portefeuilles) < 8)
    $cols=7;
    //else
    //  $cols=9;
    
    $w=(297-2*8-50-(9*3))/$cols;
    $w2=4.5;
    $this->pdf->widthB = array(0,60,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2);
    $this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;
    $this->pdf->alignA = $this->pdf->alignB;
    
    $this->pdf->ln();
//listarray($perfWaarden);
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
   // $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_kop_fontsize);
    //$this->pdf->fillCell=$fillArray;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  
  
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY()-.5, 297-$this->pdf->marge*2, 5+4, 'F');
    $this->pdf->row($header);
    $this->pdf->ln(5);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->fillCell=array();
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->row($perbegin);
    //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
    // subtotaal
   // $this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
    $this->pdf->ln();
    $this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
    $this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->ln();
    $this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
    $this->pdf->ln();
    
    $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    $this->pdf->ln(3);
    $this->pdf->row($aandeel);
    $this->pdf->ln(3);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    //	$ypos = $this->pdf->GetY()-5;
    //	$this->pdf->SetY($ypos);
    
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    //$this->pdf->fillCell=$fillArray;
    // $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
    //$this->pdf->row($samenstelling);//,"","","",""));
    //$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->row($ongerealiseerd);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
    $this->pdf->row($gerealiseerd);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
    //	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
    $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
    $this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
    
    $keys=array();
    foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
      $keys[]=$key;
    
    foreach ($opbrengstCategorien as $categorie)
    {
      $tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
      foreach($perfWaarden as $port=>$waarden)
      {
        $tmp[]=$this->formatGetal($waarden['opbrengstenPerGrootboek'][$categorie],0);
        $tmp[]='';
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
    foreach ($kostenCategorien as $categorie)
    {
      
      $tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
      foreach($perfWaarden as $port=>$waarden)
      {
        $tmp[]=$this->formatGetal($waarden['kostenPerGrootboek'][$categorie],0);
        $tmp[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
      $this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
    }
    $this->pdf->CellBorders = $subBoven;
    $this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
    $posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
    $actueleWaardePortefeuille = 0;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    
  }
  
  function getWaarden($portefeuille,$vanafDatum,$totDatum)
  {
    global $__appvar;
    // ***************************** ophalen data voor afdruk ************************ //
    
    $waarden=array();
    if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
    {
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
      $totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$vanafDatum);
      $vanRapKoers=getValutaKoers($this->pdf->rapportageValuta,$totDatum);
    }
    else
    {
      $koersQuery = "";
      $totRapKoers=1;
      $vanRapKoers=1;
    }
    
    if(substr($vanafDatum,5,5)=='01-01')
      $beginJaar=true;
    else
      $beginJaar=false;
    
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$vanafDatum,$beginJaar,$this->pdf->rapportageValuta,$vanafDatum);
    $totaal=array();
    $totaalWaardeVanaf['totaal']=0;
    foreach($fondsen as $id=>$regel)
    {
      $totaalWaardeVanaf['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      if($regel['type']=='rente')
      {
        $totaalB['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      }
    }
    
    $totaalWaarde['totaal']=0;
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$totDatum,false,$this->pdf->rapportageValuta,$vanafDatum);
    $totaal=array();
    foreach($fondsen as $id=>$regel)
    {
      $totaalWaarde['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      if($regel['type']=='rente')
      {
        $totaalA['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      }
      if($regel['type']=='fondsen')
      {
        $totaal['totaalB']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
        $totaal['totaalA']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
      }
    }
    
    $ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA'];
    $waarden['ongerealiseerdeKoersResultaat']=$ongerealiseerdeKoersResultaat;
    
    
    $DB=new DB();
    
    $waardeEind				  = $totaalWaarde['totaal'];
    $waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
    $waardeMutatie 	   	= $waardeEind - $waardeBegin;
    $stortingen 			 	= getStortingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
    $onttrekkingen 		 	= getOnttrekkingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    $rendementProcent  	=  performanceMeting($portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
//echo $this->pdf->portefeuilledata['PerformanceBerekening'];exit;
    //$rendementProcent = $this->berekening->performanceMeting($portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    $waarden['waardeEind']=$waardeEind;
    $waarden['waardeBegin']=$waardeBegin;
    $waarden['waardeMutatie']=$waardeMutatie;
    $waarden['stortingen']=$stortingen;
    $waarden['onttrekkingen']=$onttrekkingen;
    $waarden['resultaatVerslagperiode']=$resultaatVerslagperiode;
    $waarden['rendementProcent']=$rendementProcent;
    
    $RapJaar = date("Y", db2jul($totDatum));
    $RapStartJaar = date("Y", db2jul($vanafDatum));
    $totaalOpbrengst += $ongerealiseerdeKoersResultaat;
    $gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($portefeuille, $vanafDatum, $totDatum,$this->pdf->rapportageValuta,true);
    $totaalOpbrengst += $gerealiseerdeKoersResultaat;
    $waarden['gerealiseerdeKoersResultaat']=$gerealiseerdeKoersResultaat;
    
    $opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $totRapKoers;
    $totaalOpbrengst += $opgelopenRente;
    $waarden['opgelopenRente']=$opgelopenRente;
    
    if($this->pdf->GrootboekPerVermogensbeheerder)
      $query = "SELECT DISTINCT(GrootboekPerVermogensbeheerder.Grootboekrekening), GrootboekPerVermogensbeheerder.Omschrijving FROM GrootboekPerVermogensbeheerder
                WHERE GrootboekPerVermogensbeheerder.Opbrengst = '1' AND GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
                ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde";
    else
      $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
    
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    while($gb = $DB->nextRecord())
    {
      $query = "SELECT  ".
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
        "WHERE ".
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
        "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";
      
      $DB2 = new DB();
      $DB2->SQL($query);
      $DB2->Query();
      
      while($opbrengst = $DB2->nextRecord())
      {
        $opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
        $totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
      }
    }
    $waarden['opbrengstenPerGrootboek']=$opbrengstenPerGrootboek;
    $waarden['totaalOpbrengst']=$totaalOpbrengst;
    
    // loopje over Grootboekrekeningen Kosten = 1
    if($this->pdf->GrootboekPerVermogensbeheerder)
    {
      $query = "SELECT GrootboekPerVermogensbeheerder.Omschrijving,GrootboekPerVermogensbeheerder.Grootboekrekening, ".
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles, GrootboekPerVermogensbeheerder ".
        "WHERE ".
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
        "GrootboekPerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ".
        "Rekeningmutaties.Grootboekrekening = GrootboekPerVermogensbeheerder.GrootboekRekening AND ".
        "GrootboekPerVermogensbeheerder.Kosten = '1' ".
        "GROUP BY Rekeningmutaties.Grootboekrekening ".
        "ORDER BY GrootboekPerVermogensbeheerder.Afdrukvolgorde ";
    }
    else
    {
      $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
        "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
        "WHERE ".
        "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
        "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
        "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
        "Grootboekrekeningen.Kosten = '1' ".
        "GROUP BY Rekeningmutaties.Grootboekrekening ".
        "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
    }
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    
    $kostenPerGrootboek = array();
    
    while($kosten = $DB->nextRecord())
    {
      if($kosten['Grootboekrekening'] == "KNBA")
      {
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      }
      elseif($kosten['Grootboekrekening'] == "KOBU")
      {
        $kostenPerGrootboek['KOST']['Omschrijving'] = "Transactiekosten";
        $kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      }
      else
      {
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
        $kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      }
      
      
      $totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
    }
    foreach ($kostenPerGrootboek as $data)
    {
      $tmp[$data['Omschrijving']]=$data['Bedrag'];
    }
    
    $waarden['kostenPerGrootboek']=$tmp;
    $waarden['totaalKosten']=$totaalKosten;
    
    $kostenProcent = ($totaalKosten / $waardeEind) * 100;
    $koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
    $totaalOpbrengst += $koersResulaatValutas;
    $waarden['kostenProcent']=$kostenProcent;
    $waarden['koersResulaatValutas']=$koersResulaatValutas;
    $waarden['totaalOpbrengst']=$totaalOpbrengst;
    
    return $waarden;
  }
  
  
}

?>