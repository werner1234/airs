<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"] . "/html/rapport/include/layout_97/RapportVKMS_L97.php");
include_once($__appvar["basedir"] . "/html/rapport/RapportVKMS.php");

class RapportVKMS_L101
{
  function RapportVKMS_L101($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    if(is_array($pdf->portefeuilles) && count($pdf->portefeuilles)>1)
    {
      $this->vkms = new RapportVKMS_L97($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
      $this->vkms->returnVkmData=true;
    }
    else
    {
      $this->vkms = new RapportVKMS($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
      $this->vkms->vkm->fiscaliteitTonen=true;
    }
  }
  
  function writeRapport()
  {
    if(isset($this->vkms->vkm->pdf->rapport_type))
      $this->vkms->vkm->pdf->rapport_type = "VKMS";
    if(isset($this->vkms->pdf->rapport_type))
      $this->vkms->pdf->rapport_type = "VKMS";
    $vkmData=$this->vkms->writeRapport();
    if(!empty($vkmData) && count($vkmData)>0)
    {
      $this->toonVKMWaarden($vkmData);
    }
  }
  
  
  
  function toonVKMWaarden($vkmPerPortefeuille)
  {
    
    if(substr($this->vkms->vanafDatum,5,5)==substr($this->vkms->rapportageDatum,5,5))
    {
      $kostenTxt=vertaalTekst('Directe kosten afgelopen 12 maanden',$this->vkms->pdf->rapport_taal);
    }
    else
    {
      $kostenTxt=vertaalTekst('Directe kosten vanaf',$this->vkms->pdf->rapport_taal).' ' . date('d-m-Y', db2jul($this->vkms->vanafDatum));
    }
    $kop=array('');
    $indirecteKosten=array(vertaalTekst('Indirecte (fonds)kosten',$this->vkms->pdf->rapport_taal));
    $indirecteKosten2=array(vertaalTekst('Indirecte (fonds)kosten',$this->vkms->pdf->rapport_taal));
    $indirecteKostenVermogen=array(vertaalTekst('Indirecte (fonds)kosten ten opzichte van onderliggend vermogen',$this->vkms->pdf->rapport_taal));
    $percentageGemIndirect=array(vertaalTekst("Percentage van het gemiddeld indirect vermogen met een kostenfactor",$this->vkms->pdf->rapport_taal));
    $herrekendIndirect=array(vertaalTekst("Herrekende indirecte (fonds)kosten",$this->vkms->pdf->rapport_taal));
    $aandeelIndirect=array(vertaalTekst('Aandeel beleggingsfondsen',$this->vkms->pdf->rapport_taal));
    $gemiddeldVermogen=array(vertaalTekst('Gemiddeld vermogen',$this->vkms->pdf->rapport_taal));
    $indirecteKostenfactor=array(vertaalTekst('Indirecte (fonds)kosten factor van de portefeuille',$this->vkms->pdf->rapport_taal));
    $grootboekKosten=array();
    $totaalDirect=array(vertaalTekst('Totaal directe kosten',$this->vkms->pdf->rapport_taal));
    $vkm=array(vertaalTekst('Vergelijkende kostenmaatstaf',$this->vkms->pdf->rapport_taal));
    $fiscaliteit=array('Fiscaliteit');
    $grootboekKop=array($kostenTxt);
    
    
    //listarray($vkmPerPortefeuille);
    $this->vkms->pdf->ln(-4);
    $alleGrootboeken=array();
    $maxVkm=0;
    foreach($vkmPerPortefeuille as $portefeuille=>$vkmWaarden)
    {
      $grootboekKop[]=$this->vkms->pdf->rapportageValuta;
      $grootboekKop[]='%';//vertaalTekst('Percentage',$this->vkms->pdf->rapport_taal);
      
      foreach($vkmWaarden['grootboekKosten'] as $grootboek=>$bedrag)
      {
        $alleGrootboeken[$grootboek]=0;
        if(!isset($grootboekKosten[$grootboek]))
          $grootboekKosten[$grootboek]=array($vkmWaarden['grootboekOmschrijving'][$grootboek]);
      }
      $maxVkm=max($maxVkm,$vkmWaarden['vkmWaarde']);
    }
    foreach($vkmPerPortefeuille as $portefeuille=>$vkmWaarden)
    {
      //  $gemiddelde=$this->vkms->verdelingTotaal[$portefeuille]['totaal']['gemiddelde'];
      
      $herrekendeKosten=$vkmWaarden['doorlopendeKostenPercentage']/$vkmWaarden['percentageIndirectVermogenMetKostenfactor'];
      $vkmPercentagePortefeuille=$herrekendeKosten*$vkmWaarden['fondsGemiddeldeWaarde']/$vkmWaarden['gemiddeldeWaarde']*100;
      
      
      if($this->vkms->portefeuille==$portefeuille)
      {
        $kop[]='Totaal';
      }
      else
      {
        $DB=new DB();
        $query="SELECT Portefeuilles.Selectieveld1, Depotbanken.Omschrijving
          FROM
          Portefeuilles
          JOIN Depotbanken ON Portefeuilles.Depotbank=Depotbanken.Depotbank
          WHERE Portefeuilles.Portefeuille='$portefeuille' ";
        $DB->SQL($query);
        $CrmClientNaam=$DB->lookupRecord();
        if($CrmClientNaam['Selectieveld1'] <> '')
        {
          $kop[]=$CrmClientNaam['Selectieveld1'];
        }
        elseif($CrmClientNaam['Omschrijving'] <> '')
        {
          $kop[]=$CrmClientNaam['Omschrijving'];
        }
        else
          $kop[]=$portefeuille;
        
      }
      // echo "$portefeuille ".($vkmWaarden['fondsGemiddeldeWaarde']/$vkmWaarden['gemiddeldeWaarde'] * 100)."=".$vkmWaarden['fondsGemiddeldeWaarde']."/".$vkmWaarden['gemiddeldeWaarde']."  * 100<br>\n";
      $indirecteKosten[]=$this->vkms->formatGetal($vkmWaarden['totaalDoorlopendekosten'], 0) . ' '.$this->vkms->pdf->rapportageValuta;
      $indirecteKostenVermogen[]= $this->vkms->formatGetal($vkmWaarden['doorlopendeKostenPercentage'] * 100, 2) . ' %';
      $percentageGemIndirect[]= $this->vkms->formatGetal($vkmWaarden['percentageIndirectVermogenMetKostenfactor'] * 100, 2) . ' %';
      $herrekendIndirect[]= $this->vkms->formatGetal($vkmWaarden['doorlopendeKostenPercentage']/$vkmWaarden['percentageIndirectVermogenMetKostenfactor'] * 100, 2) . ' %';
      $aandeelIndirect[]=$this->vkms->formatGetal($vkmWaarden['fondsGemiddeldeWaarde']/$vkmWaarden['gemiddeldeWaarde'] * 100, 2) . ' %';
      $gemiddeldVermogen[]=$this->vkms->formatGetal($vkmWaarden['gemiddeldeWaarde'], 0) . ' '.$this->vkms->pdf->rapportageValuta;
      $indirecteKostenfactor[]=$this->vkms->formatGetal($vkmPercentagePortefeuille, 2) . ' %';
  
      $indirecteKosten2[]=$this->vkms->formatGetal($vkmWaarden['totaalDoorlopendekosten'], 0);
      $indirecteKosten2[]=$this->vkms->formatGetal($vkmPercentagePortefeuille, 2) . ' %';
      
      //if($this->vkms->portefeuille==$portefeuille)
      $totaalDirect[]=$this->vkms->formatGetal($vkmWaarden['totaalDirecteKosten'],0);
      //else
      //  $totaalDirect[]='';
      $totaalDirect[]=$this->vkms->formatGetal($vkmWaarden['totaalDirecteKosten']/$vkmWaarden['gemiddeldeWaarde']*100,2).' %';
      
      
      foreach($alleGrootboeken as $grootboek=>$null)
      {
        //   $vkmWaarden['grootboekKosten'] as $grootboek=>$bedrag
        $bedrag= $vkmWaarden['grootboekKosten'][$grootboek];
        if($this->vkms->portefeuille==$portefeuille)
        {
          $grootboekKosten[$grootboek][] = $this->vkms->formatGetal($bedrag, 0);
          $grootboekKosten[$grootboek][] = $this->vkms->formatGetal($bedrag / $vkmWaarden['gemiddeldeWaarde'] * 100, 2) . ' %';
        }
        else
        {
          //  $grootboekKosten[$grootboek][]='';
          $grootboekKosten[$grootboek][] = $this->vkms->formatGetal($bedrag, 0);
          $grootboekKosten[$grootboek][] = $this->vkms->formatGetal($bedrag / $vkmWaarden['gemiddeldeWaarde'] * 100, 2) . ' %';
        }
      }
      
      //if($this->vkms->portefeuille==$portefeuille)
      $vkm[]=$this->vkms->formatGetal($vkmWaarden['vkmWaarde']*$vkmWaarden['gemiddeldeWaarde']/100, 0);
      //else
      //  $vkm[]='';
      $vkm[]=$this->vkms->formatGetal($vkmWaarden['vkmWaarde'], 2).' %';
      
      
      $fiscaliteit[]=$this->vkms->formatGetal($vkmWaarden['alleGrootboekWaarden']['DIVBE']+$vkmWaarden['alleGrootboekWaarden']['ROER'], 0);
      $fiscaliteit[]=$this->vkms->formatGetal(($vkmWaarden['alleGrootboekWaarden']['DIVBE']+$vkmWaarden['alleGrootboekWaarden']['ROER'])/$vkmWaarden['gemiddeldeWaarde']*100, 2).' %';
      
    }
    
    
    if($this->vkms->pdfVullen==true)
    {
      
      $this->vkms->pdf->SetTextColor($this->vkms->pdf->rapport_default_fontcolor['r'], $this->vkms->pdf->rapport_default_fontcolor['g'], $this->vkms->pdf->rapport_default_fontcolor['b']);
      $this->vkms->pdf->ln();
      $this->vkms->pdf->excelData[]=array();
      $this->vkms->pdf->excelData[]=array();
      $this->vkms->pdf->setAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
      $colWidth=40;
      $widths=array(90);
      for($i=0;$i<10;$i++)
        $widths[]=$colWidth;
      
      $this->vkms->pdf->setWidths($widths);
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, 'b', $this->vkms->pdf->rapport_fontsize);
      $this->vkms->pdf->row($kop);
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, '', $this->vkms->pdf->rapport_fontsize);
  
      $this->vkms->pdf->CellBorders=array('','UU','UU','UU','UU','UU','UU');
      $this->vkms->pdf->row($gemiddeldVermogen);
      unset($this->vkms->pdf->CellBorders);
      $this->vkms->pdf->ln();
      $this->vkms->pdf->row($aandeelIndirect);//array(, );
      $this->vkms->pdf->ln();
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, 'b', $this->vkms->pdf->rapport_fontsize);
      $indirectKop=$grootboekKop;
      $indirectKop[0]='Indirecte kosten';
      $widths1=array(90);
      for($i=0;$i<20;$i++)
        $widths1[]=$colWidth/2;
      $this->vkms->pdf->setWidths($widths1);
      $this->vkms->pdf->row($indirectKop);
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, '', $this->vkms->pdf->rapport_fontsize);
      $this->vkms->pdf->CellBorders=array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','UU','UU','UU');
      $this->vkms->pdf->row($indirecteKosten2);
      unset($this->vkms->pdf->CellBorders);
      $this->vkms->pdf->ln();
/*
      $this->vkms->pdf->setWidths(array(90, 36, 32, 32, 32, 32, 32, 32, 32, 32, 32));
     // $this->vkms->pdf->row($indirecteKosten);
      $this->vkms->pdf->row($indirecteKostenVermogen);
      $this->vkms->pdf->ln();
      
      
      //  $this->vkms->pdf->row($percentageGemIndirect);
      //  $this->vkms->pdf->row($herrekendIndirect);
      
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, 'B', $this->vkms->pdf->rapport_fontsize);
      $this->vkms->pdf->row($indirecteKostenfactor);
      
      $this->vkms->pdf->ln();
      //$this->vkms->pdf->setWidths(array(60, 20, 20, 40));
      $this->vkms->pdf->setWidths(array(90,18,18,16,16,16,16,16,16,16,16,16,16));
  */
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, 'B', $this->vkms->pdf->rapport_fontsize);
      $this->vkms->pdf->row($grootboekKop);
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, '', $this->vkms->pdf->rapport_fontsize);
      
      foreach($grootboekKosten as $grootboek=>$regel)
        $this->vkms->pdf->row($regel);
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, 'B', $this->vkms->pdf->rapport_fontsize);
      $this->vkms->pdf->CellBorders=array('',array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'),array('UU', 'TS'));
      $this->vkms->pdf->row($totaalDirect);
      unset($this->vkms->pdf->CellBorders);
      $this->vkms->pdf->ln();
      $this->vkms->pdf->CellBorders=array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','UU','UU','UU');
      $this->vkms->pdf->row($vkm);
      unset($this->vkms->pdf->CellBorders);
      if($this->vkms->fiscaliteitTonen==true)
      {
        $this->vkms->pdf->ln();
        $this->vkms->pdf->row($fiscaliteit);
      }
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, '', $this->vkms->pdf->rapport_fontsize);
      $startYGrafiek=$this->vkms->pdf->getY();
      
      $n=0;
      $this->vkms->pdf->setXY($this->vkms->pdf->marge,180);
      $this->vkms->pdf->setWidths($widths);
      $this->vkms->pdf->setAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
     // $this->vkms->pdf->setAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, 'B', $this->vkms->pdf->rapport_fontsize);
      $this->vkms->pdf->row($kop);
      $this->vkms->pdf->SetFont($this->vkms->pdf->rapport_font, '', $this->vkms->pdf->rapport_fontsize);
      foreach($vkmPerPortefeuille as $portefeuille=>$vkmWaarden)
      {
        //arsort($this->vkms->barData[$portefeuille]);
        $this->vkms->pdf->setXY(18+90+$n*$colWidth, 175);
        $this->vkms->VBarVerdeling(28, 50, $this->vkms->barData[$portefeuille],$portefeuille,$maxVkm);
        $this->vkms->pdf->setXY($this->vkms->pdf->marge, $startYGrafiek);
        $n++;
      }
    }
  }
}
