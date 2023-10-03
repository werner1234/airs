<?php
/*
WORDT OOK GEBRUIKT VOOR L92,93,94,96,98,101
*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportINDEX_L98
{
  function RapportINDEX_L98($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "INDEX";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    
    if($this->pdf->rapport_FRONT_titel)
      $this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
    else
      $this->pdf->rapport_titel = "Indices";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
    $this->rapportageDatum = $rapportageDatum;
    $this->rapportageDatumJul=db2jul($this->rapportageDatum);
    $this->pdf->extraPage =0;
    $this->tweedePerformanceStart='';
    $this->DB = new DB();
    $this->rapportJaar 		= date("Y",$this->rapportageDatumJul);
    $this->pdf->brief_font = $this->pdf->rapport_font;
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  
  function fondsPerformance($fonds,$vanaf,$tot)
  {
    $perf=getFondsPerformance($fonds,$vanaf,$tot);
    return $perf;
  }
  
  function writeRapport()
  {
    //WORDT OOK GEBRUIKT VOOR L92,93,94,96,98,101
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    else
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
    
    
    $DB=new DB();
    $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    $benchmarkCategorie=array();
    $indexData=array();
  
    $benchmarkCategorie['Benchmark'][]=$this->pdf->portefeuilledata['SpecifiekeIndex'];
    $query="SELECT fonds,Omschrijving FROM Fondsen WHERE Fonds='".mysql_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."'";
    $DB->SQL($query);
    $data=$DB->lookupRecord();
    //$data['Omschrijving'].=' bestaande uit:';
    $indexData[$data['fonds']]=$data;
    foreach ($perioden as $periode=>$datum)
    {
      $indexData[$data['fonds']]['fondsKoers_'.$periode]=$this->getFondsKoers($data['fonds'],$datum);
      //$indexData[$data['fonds']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
    }
    $indexData[$data['fonds']]['performanceJaar'] = ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_jan'])    / ($indexData[$data['fonds']]['fondsKoers_jan']/100 );
    $indexData[$data['fonds']]['performance'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_begin']) / ($indexData[$data['fonds']]['fondsKoers_begin']/100 );
  
    $query="SELECT benchmarkverdeling.fonds, benchmarkverdeling.percentage, Fondsen.Omschrijving,Fondsen.Valuta
      FROM benchmarkverdeling
      INNER JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmarkverdeling.benchmark='".$this->pdf->portefeuilledata['SpecifiekeIndex']."'";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $data['Omschrijving']='    '.$this->formatGetal($data['percentage'])."% ".$data['Omschrijving'];
      $indexData[$data['fonds']]=$data;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$data['fonds']]['fondsKoers_'.$periode]=$this->getFondsKoers($data['fonds'],$datum);
        //$indexData[$data['fonds']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
      $indexData[$data['fonds']]['performanceJaar'] = ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_jan'])    / ($indexData[$data['fonds']]['fondsKoers_jan']/100 );
      $indexData[$data['fonds']]['performance'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_begin']) / ($indexData[$data['fonds']]['fondsKoers_begin']/100 );
  
      $benchmarkCategorie['Benchmark'][]=$data['fonds'];
    
    }
    
    $query="SELECT Indices.Beursindex,Fondsen.Omschrijving,Fondsen.Valuta,Indices.toelichting
FROM Indices Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
    
    
    $DB->SQL($query);
    $DB->Query();
    while($index = $DB->nextRecord())
    {
      if($index['toelichting'] == '')
        $index['toelichting']='Overige';
      
      $benchmarkCategorie[$index['toelichting']][]=$index['Beursindex'];
      
      $indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
      }
      
      $indexData[$index['Beursindex']]['performanceJaar'] = $this->fondsPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
      $indexData[$index['Beursindex']]['performance'] =    $this->fondsPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);
    }
  
    $query="SELECT
Valutas.Valuta as valuta,
Valutas.Valuta as Beursindex,
Valutas.Omschrijving,
'Valuta' as catOmschrijving
FROM
Valutas
WHERE Valutas.Valuta IN('USD','JPY') ORDER BY Valutas.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    while($valuta = $DB->nextRecord())
    {
      if ($valuta['catOmschrijving'] == '')
      {
        $valuta['catOmschrijving'] = 'Overige';
      }
      $valuta['Omschrijving'] = 'EUR-' . $valuta['valuta'];
      $valutas[]=$valuta;
      if($valuta['valuta']=='USD')
      {
        $valuta['Omschrijving'] = $valuta['valuta'].'-EUR';
        $valutas[]=$valuta;
      }
    }
    foreach($valutas as $valuta)
    {
      $benchmarkCategorie[$valuta['catOmschrijving']][]=$valuta['Omschrijving'];
  
      $indexData[$valuta['Omschrijving']]=$valuta;
      
        foreach ($perioden as $periode=>$datum)
        {
          if(substr($valuta['Omschrijving'],0,3)=='EUR')
            $indexData[$valuta['Omschrijving']]['fondsKoers_'.$periode]=1/globalGetValutaKoers($valuta['valuta'],$datum);
          else
            $indexData[$valuta['Omschrijving']]['fondsKoers_'.$periode]=globalGetValutaKoers($valuta['valuta'],$datum);
        }
      $indexData[$valuta['Omschrijving']]['performanceJaar'] = ($indexData[$valuta['Omschrijving']]['fondsKoers_eind'] - $indexData[$valuta['Omschrijving']]['fondsKoers_jan'])    / ($indexData[$valuta['Omschrijving']]['fondsKoers_jan']/100 );
      $indexData[$valuta['Omschrijving']]['performance'] =     ($indexData[$valuta['Omschrijving']]['fondsKoers_eind'] - $indexData[$valuta['Omschrijving']]['fondsKoers_begin']) / ($indexData[$valuta['Omschrijving']]['fondsKoers_begin']/100 );
  
      
    }
    //listarray($benchmarkCategorie);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(10,60,33,33,33,33,33));
    $this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    if($perioden['jan']==$perioden['begin'])
    {
   //   $this->pdf->CellBorders = array('','U','U','U','U');
      $this->pdf->row(array("","\nIndex","Koers ".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %'));
    }
    else
    {
  //    $this->pdf->CellBorders = array('','U','U','U','U','U','U');
      $this->pdf->row(array("","\nIndex","Koers ".date("d-m-Y",db2jul($perioden['jan'])),"Koers ".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  
  //  listarray($benchmarkCategorie);
  //  listarray($indexData);
    
    foreach ($benchmarkCategorie as $categorie=>$fondsen)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array("",$categorie));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      if($categorie=='Valuta')
        $dec=4;
      else
        $dec=2;
      foreach ($fondsen as $i=>$fonds)
      {
        $fondsData=$indexData[$fonds];
        if($perioden['jan']==$perioden['begin'])
        {
          $this->pdf->row(array('',$fondsData['Omschrijving'],
                            $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],$dec),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],$dec),
                            $this->formatGetal($fondsData['performance'],2)));
        }
        else
        {
          $this->pdf->row(array('',$fondsData['Omschrijving'],
                            $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],$dec),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],$dec),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],$dec),
                            $this->formatGetal($fondsData['performance'],2),$this->formatGetal($fondsData['performanceJaar'],2)));
        }
        if($categorie=='Benchmark' && $i==0)
        {
          $this->pdf->row(array('','    Bestaande uit:'));
        }
      }
      if($categorie=='Benchmark')
        $this->pdf->ln();
    }
  }
}
?>