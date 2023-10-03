<?

include_once('../indexBerekening.php');
include_once('rapportATTberekening_L12.php');

class RapportINDEX_L12
{

  function RapportINDEX_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->underlinePercentage=0.95;
    $this->pdf->rapport_titel='Portefeuille rendement en indices';
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
    {
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
    }
    //$this->perioden['jan']=$this->tweedePerformanceStart;
    $this->pdf->tweedePerformanceStart= db2jul($this->tweedePerformanceStart );
    
    
    $DB = new DB();
	  $query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, ".
		" Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, ".
		" Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
		" Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
		" AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
		$DB->SQL($query);
		$this->pdata = $DB->lookupRecord();


	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function getFondsPerformance($fonds,$start,$stop,$valuta)
  {
    $beginKoers = globalGetFondsKoers($fonds, $start)/getValutaKoers($valuta,$start);
    $eindKoers = globalGetFondsKoers($fonds, $stop)/getValutaKoers($valuta,$stop);
    $perf = ($eindKoers - $beginKoers) / ($beginKoers / 100);
    return $perf;
  }

  function writeRapport()
	{
    global $__appvar;
	
	  $this->berekening = new rapportATTberekening_L12($this);//rapportATTberekening_L12($this->pdata);
    $this->attWaarden=array();
    $this->attWaarden['periode']=$this->berekening->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'attributie',$this->pdf->rapportageValuta);
    $this->attWaarden['jaar']=$this->berekening->bereken($this->tweedePerformanceStart ,$this->rapportageDatum,'attributie',$this->pdf->rapportageValuta);
    $categorien=$this->berekening->categorien;

    //$categorien
    
    
 //   listarray( $this->tmp);
    unset($this->berekening);
    $header=array('Rendement','');
    foreach($categorien as $categorie)
      if($categorie<>'Totaal')
        $header[]=$categorie;
    $header[]='';
    foreach($categorien as $categorie)
      if($categorie<>'Totaal')
        $header[]=$categorie;
    
    
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[	$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    
    

    $lnhoogte=3;
    $this->pdf->setWidths( $this->pdf->widthsA);
    $this->pdf->setAligns( array('L','L','R','R','R','R','C','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('U','','US','US','US','US','','US','US','US','US');
    $this->pdf->setDrawColor(156,156,156);
//listarray($this->pdf->rapport_totaalLijnenColor);exit;

    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row($header);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  
   
    $this->pdf->CellFontColor=array($this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_fontcolor);
    /*
   $this->pdf->CellFontStyle = array(array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
    */

    unset( $this->pdf->CellBorders);
    $this->pdf->ln($lnhoogte);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   //listarray($this->attWaarden);exit;
    $rowOmschrijving=array('beginwaarde'=>'Beginwaarde','eindwaarde'=>'Eindwaarde','procent'=>'Rendement');
    foreach($rowOmschrijving as $veld=>$omschrijving)
    {
      $row = array($omschrijving,'');
      foreach($this->attWaarden as $periode=>$periodeData)
      {

        foreach ($periodeData as $categorie=>$categoriedata)
        {
          if ($categorie <> 'totaal')
          {
         
            if($veld=='procent')
            {
              if($categorie=='Liquiditeiten')
              {
                $waardeTxt='';
                $extra = '';
              }
              else
              {
                $extra = '%';
                $dec=2;
                $waardeTxt=$this->formatGetal($categoriedata[$veld],$dec);
              }
            }
            else
            {
              $extra = '';
              $dec=0;
              $waardeTxt=$this->formatGetal($categoriedata[$veld],$dec);
            }
            $row[] = $waardeTxt.$extra;
          }
        }
        $row[]='';
      }
      if($omschrijving=='Rendement')
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      else
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        
      $this->pdf->row($row);
      $this->pdf->ln($lnhoogte);
    }
    
    $this->pdf->ln(16);
    $lnhoogte=0;
    
    $query='SELECT
Indices.Beursindex,
BeleggingssectorPerFonds.AttributieCategorie,
Indices.Vermogensbeheerder,
Indices.Afdrukvolgorde,
Fondsen.Omschrijving
FROM
Indices
INNER JOIN BeleggingssectorPerFonds ON Indices.Beursindex = BeleggingssectorPerFonds.Fonds AND Indices.Vermogensbeheerder = BeleggingssectorPerFonds.Vermogensbeheerder
INNER JOIN Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder=\''. $this->pdf->portefeuilledata['Vermogensbeheerder'].'\'
ORDER BY Indices.Afdrukvolgorde';
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $indices=array();
    while($data=$DB->nextRecord())
    {//echo $this->rapportageDatumVanaf." ".$this->tweedePerformanceStart." ".$this->rapportageDatum."<br>\n";
      //$data['periode']=getFondsPerformance($data['Beursindex'],$this->rapportageDatumVanaf ,$this->rapportageDatum);
      //$data['jaar']=getFondsPerformance($data['Beursindex'],$this->tweedePerformanceStart ,$this->rapportageDatum);
      $data['periode']=$this->getFondsPerformance($data['Beursindex'],$this->rapportageDatumVanaf ,$this->rapportageDatum,$this->pdf->rapportageValuta);
      $data['jaar']=$this->getFondsPerformance($data['Beursindex'],$this->tweedePerformanceStart ,$this->rapportageDatum,$this->pdf->rapportageValuta);
      
      $indices[$data['Beursindex']]=$data;
      $indices[$data['Beursindex']][$data['AttributieCategorie']]['jaar']=$data['jaar'];
      $indices[$data['Beursindex']][$data['AttributieCategorie']]['periode']=$data['periode'];
    }

    if($this->pdf->rapportageValuta<>'' && $this->pdf->rapportageValuta<>'EUR')
      $header="Indices (resultaten uitgedrukt in ".$this->pdf->rapportageValuta.")";
    else
      $header='Indices';
    $header=array($header,'');
    $this->pdf->CellBorders = array('U');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($header);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->ln($lnhoogte);
    $perioden=array('periode','jaar');
    foreach($indices as $fonds=>$fondsData)
    {
      $row = array($fondsData['Omschrijving'],'');
      foreach($perioden as $periode)
      {
        
        foreach ($categorien as $categorie=>$catOmschrijving)
        {
          if ($categorie <> 'totaal')
          {
            $extra = '%';
            $dec=2;
            if(isset($fondsData[$categorie][$periode]))
              $row[] = $this->formatGetal($fondsData[$categorie][$periode],$dec).$extra;
            else
              $row[] = '';
          }
        }
        $row[]='';
      }
      $this->pdf->row($row);
      $this->pdf->ln($lnhoogte);
    }
    $this->pdf->CellBorders = array();
    
    
    $this->pdf->ln(6);
    
    $query = "SELECT
TijdelijkeRapportage.valuta,Valutas.Omschrijving,
Valutas.Afdrukvolgorde
FROM
TijdelijkeRapportage
Inner Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' GROUP BY Valuta
ORDER BY Valutas.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    $indexValuta=array();
    while($valuta = $DB->nextRecord())
    {
      $valutas[]=$valuta['Valuta'];
      $indexValuta[$valuta['valuta']]=$valuta;
      foreach ($perioden as $periode=>$datum)
      {
        $indexValuta[$valuta['valuta']]['valutaKoers_'.$periode]=getValutaKoers($valuta['valuta'],$datum);
      }
      $indexValuta[$valuta['valuta']]['Liquiditeiten']['jaar'] = ($indexValuta[$valuta['valuta']]['valutaKoers_eind']/$this->pdf->ValutaKoersEind - $indexValuta[$valuta['valuta']]['valutaKoers_jan']/getValutaKoers($this->pdf->rapportageValuta,$this->tweedePerformanceStart))    / ($indexValuta[$valuta['valuta']]['valutaKoers_jan']/getValutaKoers($this->pdf->rapportageValuta,$this->tweedePerformanceStart)/100 );
      $indexValuta[$valuta['valuta']]['Liquiditeiten']['periode'] =     ($indexValuta[$valuta['valuta']]['valutaKoers_eind']/$this->pdf->ValutaKoersEind - $indexValuta[$valuta['valuta']]['valutaKoers_begin']/$this->pdf->ValutaKoersBegin) / ($indexValuta[$valuta['valuta']]['valutaKoers_begin']/$this->pdf->ValutaKoersBegin/100 );
      if($indexValuta[$valuta['valuta']]['Liquiditeiten']['jaar']==0.00 && $indexValuta[$valuta['valuta']]['Liquiditeiten']['periode']==0.00)
      {
        unset($indexValuta[$valuta['valuta']]);
      }
    }
    //listarray($this->pdf->widths);
    $this->pdf->setWidths(array($this->pdf->widths[0],$this->pdf->widths[1],$this->pdf->widths[2],$this->pdf->widths[3],$this->pdf->widths[4],$this->pdf->widths[5],$this->pdf->widths[6],$this->pdf->widths[7]
      ,$this->pdf->widths[8],$this->pdf->widths[9],$this->pdf->widths[10],$this->pdf->widths[11]));
    //listarray($this->pdf->widths);exit;
    $this->pdf->setAligns( array('L','R','R','R','R','R','R','C','R','R','R','R'));
    $this->pdf->CellFontColor=array($this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_fontcolor);
    $header=array('Valuta\'s','','Koers');
    $this->pdf->CellBorders = array('U','','U');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row($header);
    $this->pdf->CellFontColor=array($this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_fontcolor);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->ln($lnhoogte);
    $perioden=array('periode','jaar');
    //
    //listarray($categorien);
    //listarray($indexValuta);
    unset($categorien['totaal']);
    $categorien=array_values($categorien);
    foreach($indexValuta as $valuta=>$valutaDetails)
    {
      $row = array($valutaDetails['Omschrijving'],'',$this->formatGetal(1/$valutaDetails['valutaKoers_eind']*$this->pdf->ValutaKoersEind,2));
      foreach($perioden as $periode)
      {
    
        foreach ($categorien as $i=>$categorie)
        {
         // echo "$periode $i $categorie <br>\n";
          if($i==1 && $periode=='periode')
            continue;
          if ($categorie <> 'totaal')
          {
            $extra = '%';
            $dec=2;
            if(isset($valutaDetails[$categorie][$periode]))
              $row[] = $this->formatGetal($valutaDetails[$categorie][$periode],$dec).$extra;
            else
              $row[] = '';
          }
        }
        $row[]='';
      }
   //   listarray($row);
      $this->pdf->row($row);
      $this->pdf->ln($lnhoogte);
    }
 //   listarray($indexValuta);exit;
    
 unset($this->pdf->CellFontColor);
}


}
?>
