<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L79.php");

class RapportPERFD_L79
{

  function RapportPERFD_L79($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "PERF";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

    if($this->pdf->rapport_PERF_titel)
      $this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
    else
      $this->pdf->rapport_titel = "Resultaat- en rendementsberekening ".date("j",$this->pdf->rapport_datumvanaf)." ".
        vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
        date("Y",$this->pdf->rapport_datumvanaf)." ".
        vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".
        date("j",$this->pdf->rapport_datum)." ".
        vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
        date("Y",$this->pdf->rapport_datum);


    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L79($this);
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
    // voor data
    $this->pdf->widthA = array(5,80,30,5,30,5,30,120);
    $this->pdf->alignA = array('L','L','R','L','R');

    // voor kopjes
    $this->pdf->widthB = array(0,85,30,5,30,5,30,120);
    $this->pdf->alignB = array('L','L','R','L','R');


    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->addResultaat();

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);

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

    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum, null);
    foreach ( $this->waarden['Periode'] as $key => $value ) {
      if ( $value['beginwaarde'] == 0 &&  $value['eindwaarde'] == 0 &&  $value['resultaat'] == 0 ) {
        unset($this->waarden['Periode'][$key]);
      }
    }

    $categorien=array_keys($this->waarden['Periode']);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    // listarray($this->pdf->portefeuilles);
    $fillArray=array(0,1);
    $subOnder=array('','');
    $volOnder=array('U','U');
    $subBoven=array('','');
    $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
    $headerXls = array(vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
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
      $headerXls[] = $this->att->categorien[$categorie];
      $header[]='';
      $samenstelling[]='';
      $samenstelling[]='';
      // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    }

    $this->pdf->excelData[] = $headerXls;

    $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $perbeginXls = array(vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));

    $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $waardeRapdatumXls = array(vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));

    $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $mutwaardeXls = array(vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));

    $stortingen=array("",vertaalTekst("Stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $onttrekking=array("",vertaalTekst("Onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));

    $stortingenOnttrekking=array("",vertaalTekst("Totaal stortingen en onttrekkingen",$this->pdf->rapport_taal));
    $stortingenOnttrekkingXls = array(vertaalTekst("Totaal stortingen en onttrekkingen",$this->pdf->rapport_taal));

    $effectenmutaties=array("",vertaalTekst("Mutaties gedurende verslagperiode",$this->pdf->rapport_taal));
    $effectenmutatiesXls = array(vertaalTekst("Mutaties gedurende verslagperiode",$this->pdf->rapport_taal));


    $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $resultaatXls = array(vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));

    $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $rendementXls = array(vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));

    $ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
    $ongerealiseerdFondsXls = array(vertaalTekst("Ongerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //

    $ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    $ongerealiseerdValutaXls = array(vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //

    $gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
    $gerealiseerdFondsXls = array(vertaalTekst("Gerealiseerde fondsresultaten",$this->pdf->rapport_taal));

    $gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
    $gerealiseerdValutaXls = array(vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal));

    $valutaResultaat=array("",vertaalTekst("Resultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
    $valutaResultaatXls = array(vertaalTekst("Resultaten vreemde valuta rekeningen",$this->pdf->rapport_taal));

    $rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
    $renteXls = array(vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));

    $totaalOpbrengst=array("","");//totaalOpbrengst

    $totaalKosten=array("","");   //totaalKosten
    $totaal=array("","");   //totaalOpbrengst-totaalKosten


    foreach($categorien as $categorie)
    {
      unset($this->waarden['Periode'][$categorie]['perfWaarden']);
    }

    //listarray($this->waarden['Periode']);exit;
    $opbrengstCategorien=array();
    foreach($categorien as $categorie)
    {
      $perfWaarden=$this->waarden['Periode'][$categorie];
      $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
      $perbeginXls[] = round($perfWaarden['beginwaarde'],2);
      $perbegin[]='';
      $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
      $waardeRapdatumXls[]=round($perfWaarden['eindwaarde'],2);

      $waardeRapdatum[]='';
      $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
      $mutwaardeXls[] = round($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],2);
      $mutwaarde[]='';

      if($categorie=='totaal')
      {
        $effectenmutaties[]='';
        $effectenmutatiesXls[]='';
        $effectenmutaties[]='';
        //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
        //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
        $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
        $stortingen[]='';
        $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
        $onttrekking[]='';
        $stortingenOnttrekking[]=$this->formatGetal($perfWaarden['storting']-$perfWaarden['onttrekking'],0);
        $stortingenOnttrekkingXls[] = round($perfWaarden['storting']-$perfWaarden['onttrekking'],2);
        $stortingenOnttrekking[]='';
      }
      else
      {
        $effectenmutaties[]=$this->formatGetal($perfWaarden['stort'],0);
        $effectenmutatiesXls[] = round($perfWaarden['stort'],2);
        $effectenmutaties[]='';
        $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
        $stortingen[]='';
        $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
        $onttrekking[]='';
        $stortingenOnttrekking[]='';
        $stortingenOnttrekkingXls[]='';
        $stortingenOnttrekking[]='';
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
      $resultaatXls[] = round($perfWaarden['resultaat'],2);
      $resultaat[]='';
      if($categorie=='H-Liq')
      {
        $rendement[] = '';
        $rendementXls[] = '';
        $rendement[] = '';
      }
      else
      {
        $rendement[] = $this->formatGetal($perfWaarden['procent'], 2) . ' %';
        $rendementXls[] = round($perfWaarden['procent'],2);
        $rendement[] = '';
      }
      $ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0);
      $ongerealiseerdFondsXls[] = round($perfWaarden['ongerealiseerdFondsResultaat'],2);
      $ongerealiseerdFonds[]='';
      $ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
      $ongerealiseerdValutaXls[] = round($perfWaarden['ongerealiseerdValutaResultaat'],2);
      $ongerealiseerdValuta[]='';
      $gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0);
      $gerealiseerdFondsXls[] = round($perfWaarden['gerealiseerdFondsResultaat'],2);
      $gerealiseerdFonds[]='';
      $gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
      $gerealiseerdValutaXls[] = round($perfWaarden['gerealiseerdValutaResultaat'],2);
      $gerealiseerdValuta[]='';
      $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
      $valutaResultaatXls[] = round($perfWaarden['resultaatValuta'],2);
      $valutaResultaat[]='';
      $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
      $renteXls[] = round($perfWaarden['opgelopenrente'],2);
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



    $cellWidth=27;
    $cellWidthP=2;
    $this->pdf->widthB = array(0,62,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP);
    $this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,5,30,5,30,5,30,5,30,5,30,5);
    $this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');


//listarray($perfWaarden);
    $this->pdf->ln(5);
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $this->headerTop=$this->pdf->GetY();

    $this->pdf->fillCell=array();
    // achtergrond kleur
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row($header);
    $this->pdf->ln(10);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->excelData[] = $perbeginXls;
    $this->pdf->row($perbegin);
    //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->excelData[] = $waardeRapdatumXls;
    $this->pdf->CellBorders = array();
    // subtotaal
    //	$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
    $this->pdf->ln();
    $this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
    $this->pdf->excelData[] = $mutwaardeXls;
    //$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    //$this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->row($stortingenOnttrekking);
    $this->pdf->excelData[] = $stortingenOnttrekkingXls;

    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($effectenmutaties);
    $this->pdf->excelData[] = $effectenmutatiesXls;
    $this->pdf->ln();
    $this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
    $this->pdf->excelData[] = $resultaatXls;
    $this->pdf->ln();

    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $catWidth = 62+ ( count($categorien) * 29)-2;

    $this->pdf->SetFillColor(226,238,241);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), $catWidth , $this->pdf->rowHeight , 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->line($this->pdf->marge,$this->pdf->getY(),$catWidth+8,$this->pdf->getY());

    $this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
//    $this->pdf->excelData[] = $rendementXls;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    $ypos = $this->pdf->GetY();


    $this->pdf->SetY($ypos);
    $this->pdf->ln();
//listarray($this->pdf->widthB);

    $this->pdf->SetWidths(array(0,100));
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    //   $YSamenstelling=$this->pdf->GetY();
    $this->pdf->row($samenstelling);//,"","","",""));
    $this->pdf->SetWidths($this->pdf->widthB);
    //$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    //   $this->hoogteBeleggingsresultaat=$this->pdf->getY();
    $this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->row($ongerealiseerdFonds);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
    $this->pdf->excelData[] = $ongerealiseerdFondsXls;
    $this->pdf->row($ongerealiseerdValuta);
    $this->pdf->excelData[] = $ongerealiseerdValutaXls;
    $this->pdf->row($gerealiseerdFonds);
    $this->pdf->excelData[] = $gerealiseerdFondsXls;
    $this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
    $this->pdf->excelData[] = $gerealiseerdValutaXls;
    //	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
    $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
    $this->pdf->excelData[] = $valutaResultaatXls;
    $this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
    $this->pdf->excelData[] = $renteXls;
    $keys=array();
    //foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
    //  $keys[]=$key;



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

    $this->pdf->CellBorders = $subBoven;
    $this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
    //$this->pdf->ln();
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
      $xlsTmp=array(vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
      foreach($categorien as $categorie)
      {
        $perfWaarden=$this->waarden['Periode'][$categorie];

        $tmp[]=$this->formatGetal($perfWaarden['grootboekKosten'][$grootboek],0);
        $xlsTmp[] = $perfWaarden['grootboekKosten'][$grootboek];
        $tmp[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
      $this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));

      $this->pdf->excelData[] = $xlsTmp;
    }
    $this->pdf->CellBorders = $subBoven;
    $this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
    $posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = array();
    //$this->pdf->CellBorders = $volOnder;
    $this->pdf->Ln();

    $this->pdf->SetFillColor(137,185,198);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

    $this->pdf->line($this->pdf->marge,$this->pdf->getY(),297-$this->pdf->marge,$this->pdf->getY());
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, $this->pdf->rowHeight*2 , 'F');
    $this->pdf->ln(2);
    $this->pdf->SetTextColor(255);


    $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
    $actueleWaardePortefeuille = 0;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

  }





}