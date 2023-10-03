<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportINDEX_L105
{
  function RapportINDEX_L105($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    //$this->pdf->portefeuilledata['Vermogensbeheerder']='ibe';
    $query="SELECT Indices.Beursindex,Fondsen.Omschrijving,Fondsen.Valuta,Indices.toelichting
FROM Indices Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
    
    $query="SELECT
IndexPerBeleggingscategorie.Vermogensbeheerder,
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds as Beursindex,
Beleggingscategorien.Omschrijving as CatOmschrijving,
Fondsen.Omschrijving as Omschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN KeuzePerVermogensbeheerder ON IndexPerBeleggingscategorie.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
WHERE
IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde,Fondsen.Omschrijving";
    
    
    $DB->SQL($query);
    $DB->Query();
    $benchmarkCategorie=array();
    $indexData=array();
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
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(10,55,60,30,30,30,30,30));
    $this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    if($perioden['jan']==$perioden['begin'])
    {
      $this->pdf->CellBorders = array('','U','U','U','U','U');
      $this->pdf->row(array("","\nCategorie","\nIndex","Koers ".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %'));
    }
    else
    {
      $this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
      $this->pdf->row(array("","\nCategorie","\nIndex","Koers ".date("d-m-Y",db2jul($perioden['jan'])),"Koers ".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    
    foreach ($benchmarkCategorie as $categorie=>$fondsen)
    {
      //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      //$this->pdf->row(array("",$categorie));
      //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach ($fondsen as $fonds)
      {
        $fondsData=$indexData[$fonds];
        if($perioden['jan']==$perioden['begin'])
        {
          $this->pdf->row(array('',$fondsData['CatOmschrijving'],$fondsData['Omschrijving'],
                            $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
                            $this->formatGetal($fondsData['performance'],2)));
        }
        else
        {
          $this->pdf->row(array('',$fondsData['CatOmschrijving'],$fondsData['Omschrijving'],
                            $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],2),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
                            $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
                            $this->formatGetal($fondsData['performance'],2),$this->formatGetal($fondsData['performanceJaar'],2)));
        }
      }
    }
  }
}
?>