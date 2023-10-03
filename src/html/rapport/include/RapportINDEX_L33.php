<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/03/31 12:20:15 $
File Versie					: $Revision: 1.28 $

$Log: RapportINDEX_L33.php,v $
Revision 1.28  2019/03/31 12:20:15  rvv
*** empty log message ***

Revision 1.27  2019/03/06 16:13:44  rvv
*** empty log message ***

Revision 1.26  2018/12/15 17:49:14  rvv
*** empty log message ***

Revision 1.25  2018/09/08 17:43:29  rvv
*** empty log message ***

Revision 1.24  2018/09/05 15:53:27  rvv
*** empty log message ***

Revision 1.23  2018/04/14 17:23:49  rvv
*** empty log message ***

Revision 1.22  2018/04/12 09:36:16  rvv
*** empty log message ***

Revision 1.21  2018/04/11 09:14:19  rvv
*** empty log message ***

Revision 1.20  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.19  2017/04/05 15:39:45  rvv
*** empty log message ***

Revision 1.18  2017/03/31 15:39:22  rvv
*** empty log message ***

Revision 1.17  2017/03/11 20:27:43  rvv
*** empty log message ***

Revision 1.15  2017/02/04 19:11:39  rvv
*** empty log message ***

Revision 1.14  2017/02/01 16:44:57  rvv
*** empty log message ***

Revision 1.13  2014/11/01 22:05:57  rvv
*** empty log message ***

Revision 1.12  2013/05/01 15:53:08  rvv
*** empty log message ***

Revision 1.11  2013/03/06 16:59:51  rvv
*** empty log message ***

Revision 1.10  2012/10/31 16:59:18  rvv
*** empty log message ***

Revision 1.9  2012/09/30 11:18:17  rvv
*** empty log message ***

Revision 1.8  2012/08/22 15:46:00  rvv
*** empty log message ***

Revision 1.7  2012/06/13 14:35:39  rvv
*** empty log message ***

Revision 1.6  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.5  2011/12/24 16:35:21  rvv
*** empty log message ***

Revision 1.4  2011/10/10 16:44:51  rvv
*** empty log message ***

Revision 1.3  2011/10/05 18:00:14  rvv
*** empty log message ***

Revision 1.2  2011/10/02 08:37:20  rvv
*** empty log message ***

Revision 1.1  2011/09/28 18:46:41  rvv
*** empty log message ***

Revision 1.6  2011/09/25 16:23:28  rvv
*** empty log message ***

Revision 1.5  2011/04/12 09:05:54  cvs
telefoonnr en BTW nr aanpassen

Revision 1.4  2011/01/11 08:23:38  cvs
*** empty log message ***

Revision 1.3  2011/01/08 14:27:56  rvv
*** empty log message ***

Revision 1.2  2011/01/05 18:53:09  rvv
*** empty log message ***

Revision 1.1  2010/12/05 09:54:08  rvv
*** empty log message ***

Revision 1.4  2010/07/04 15:24:39  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportIndex_L33
{
	function RapportIndex_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkingsmaatstaven";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->extraPage =0;
		$this->DB = new DB();


		$this->rapportJaar 		= date("Y",$this->rapportageDatumJul);

		$this->pdf->brief_font = $this->pdf->rapport_font;

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	function kopEnVoet()
	{
	  if(is_file($this->pdf->rapport_factuurHeader))
		{
			$this->pdf->Image($this->pdf->rapport_factuurHeader, 0, 10, 210, 34);
		}
		if(is_file($this->pdf->rapport_factuurFooter))
		{
			$this->pdf->Image($this->pdf->rapport_factuurFooter, 5, 255, 200, 37);
		}
	}


	function writeRapport()
	{
	  global $__appvar;
	  $this->pdf->addPage();
	  $this->pdf->templateVars['INDEXPaginas'] = $this->pdf->customPageNo;

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";


	  $DB=new DB();
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    
    $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$this->rapportageDatum);
    $query2 = "SELECT Fondsen.Fonds as SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Fondsen
	             WHERE Fondsen.Fonds  = '". mysql_real_escape_string($SpecifiekeIndexFonds)."' ";
		$DB->SQL($query2);
		$DB->Query();
    $SpecifiekeIndex=$DB->lookupRecord();
    $fondsOmschrijvingen=array();

    $index = new indexHerberekening();
    $indexWaarden = $index->getWaarden($this->rapportageDatumVanaf,$this->rapportageDatum,array($this->portefeuille,$this->pdf->portefeuilles),'','maanden',$this->pdf->rapportageValuta);
    $portefeuille['performance']=$indexWaarden[count($indexWaarden)]['index']-100;
    $indexWaarden = $index->getWaarden($this->tweedePerformanceStart,$this->rapportageDatum,array($this->portefeuille,$this->pdf->portefeuilles),'','maanden',$this->pdf->rapportageValuta);
    $portefeuille['performanceJaar']=$indexWaarden[count($indexWaarden)]['index']-100;
    $fondsWissel='';
    if($SpecifiekeIndex['SpecifiekeIndex'] <> '')
    {
      /*
      foreach ($perioden as $periode=>$datum)
      {
        $SpecifiekeIndex['fondsKoers_'.$periode]=getFondsKoers($SpecifiekeIndex['SpecifiekeIndex'],$datum);
        $SpecifiekeIndex['valutaKoers_'.$periode]=getValutaKoers($SpecifiekeIndex['SpecifiekeIndex'],$datum);
      }
  */
      $SpecifiekeIndex['performanceJaar']=0;
      $SpecifiekeIndex['performance']=0;
      $maanden=$index->getMaanden(db2jul($perioden['jan']),db2jul($perioden['eind']));
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
      $maanden=$index->getMaanden(db2jul($perioden['begin']),db2jul($perioden['eind']));
      foreach($maanden as $maand)
      {
        $SpecifiekeIndexFonds=getSpecifiekeIndex($this->portefeuille,$maand['stop']);
        $maandPerf=getFondsPerformance($SpecifiekeIndexFonds,$maand['start'],$maand['stop']);
        $SpecifiekeIndex['performance']=((1+$SpecifiekeIndex['performance']/100)*(1+$maandPerf/100)-1)*100;
      }
     }



	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta,Indices.toelichting
	  FROM Indices
	  JOIN Fondsen ON Indices.Beursindex = Fondsen.Fonds
	  WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
		$DB->SQL($query); 
		$DB->Query();
		
	  while($index = $DB->nextRecord())
		{
		  if($index['benchmark'] == '')
		    $index['benchmark']='rest';
      $indices[$index['Beursindex']]=$index;
    }
    
    $query=" SELECT Fondsen.Omschrijving, Fondsen.Valuta,benchmarkverdeling.toelichting,benchmarkverdeling.`benchmark`, benchmarkverdeling.fonds as Beursindex
             FROM benchmarkverdeling join Fondsen on  benchmarkverdeling.fonds =  Fondsen.Fonds 
             WHERE benchmarkverdeling.benchmark='".$SpecifiekeIndex['SpecifiekeIndex']."'";
		$DB->SQL($query); 
		$DB->Query();
    while($index = $DB->nextRecord())
		{
      foreach($index as $key=>$value)
      {
        if($value <> '')
          $indices[$index['Beursindex']][$key]=$value;
      }
    }

    
    $benchmarkCategorie=array();
    foreach($indices as $index) 
    {
		  $benchmarkCategorie[$index['benchmark']][]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=getFondsKoers($index['Beursindex'],$datum);
        $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Beursindex']]['performanceJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan'])    / ($indexData[$index['Beursindex']]['fondsKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		$indexData[$index['Beursindex']]['performanceEurJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']  *$indexData[$index['Beursindex']]['valutaKoers_jan'])/(  $indexData[$index['Beursindex']]['fondsKoers_jan']*  $indexData[$index['Beursindex']]['valutaKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}

		$query = "SELECT
TijdelijkeRapportage.valuta,Valutas.Omschrijving,
Valutas.Afdrukvolgorde
FROM
TijdelijkeRapportage
Inner Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' GROUP BY Valuta
ORDER BY Valutas.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($valuta = $DB->nextRecord())
		{
		  $valutas[]=$valuta['Valuta'];
		  $indexValuta[$valuta['valuta']]=$valuta;
		  foreach ($perioden as $periode=>$datum)
      {
        $indexValuta[$valuta['valuta']]['valutaKoers_'.$periode]=getValutaKoers($valuta['valuta'],$datum);
      }
      $indexValuta[$valuta['valuta']]['performanceJaar'] = ($indexValuta[$valuta['valuta']]['valutaKoers_eind']/$this->pdf->ValutaKoersEind - $indexValuta[$valuta['valuta']]['valutaKoers_jan']/$this->pdf->ValutaKoersStart)    / ($indexValuta[$valuta['valuta']]['valutaKoers_jan']/$this->pdf->ValutaKoersStart/100 );
			$indexValuta[$valuta['valuta']]['performance'] =     ($indexValuta[$valuta['valuta']]['valutaKoers_eind']/$this->pdf->ValutaKoersEind - $indexValuta[$valuta['valuta']]['valutaKoers_begin']/$this->pdf->ValutaKoersBegin) / ($indexValuta[$valuta['valuta']]['valutaKoers_begin']/$this->pdf->ValutaKoersBegin/100 );
      if($indexValuta[$valuta['valuta']]['performanceJaar']==0.00 && $indexValuta[$valuta['valuta']]['performance']==0.00)
			{
				unset($indexValuta[$valuta['valuta']]);
			}
		}

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(60);
  	$this->pdf->SetWidths(array(110,30,30));
  	$this->pdf->SetAligns(array('L','R','R'));
  	$this->pdf->CellBorders = array('U','U','U');
  	//$this->pdf->row(array("\nBenchmarkvergelijking",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	$this->pdf->row(array("\n".vertaalTekst("Benchmarkvergelijking",$this->pdf->rapport_taal),vertaalTekst("Rendement verslagperiode in %",$this->pdf->rapport_taal),vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' '.vertaalTekst("in %",$this->pdf->rapport_taal)));


  	unset($this->pdf->CellBorders);
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array(vertaalTekst("Rendement portefeuille",$this->pdf->rapport_taal),$this->formatGetal($portefeuille['performance'],1),$this->formatGetal($portefeuille['performanceJaar'],1)));
  	$this->pdf->row(array(vertaalTekst("Rendement benchmark",$this->pdf->rapport_taal).' ('.$SpecifiekeIndex['Omschrijving'].")",$this->formatGetal($SpecifiekeIndex['performance'],1),$this->formatGetal($SpecifiekeIndex['performanceJaar'],1)));
   
  	if($fondsWissel<>'')
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'I',$this->pdf->rapport_fontsize);
      $this->pdf->row(array($fondsWissel));
    }
    // $this->pdf->ln();

    $this->pdf->SetWidths(array(60,50,30,30));
  	$this->pdf->SetAligns(array('L','L','R','R'));
  	$this->pdf->CellBorders = array('U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);


		if(count($benchmarkCategorie[$SpecifiekeIndex['SpecifiekeIndex']])>0)
		{
			$this->pdf->row(array("\n" . vertaalTekst("Samenstelling benchmark", $this->pdf->rapport_taal), "\n" . vertaalTekst("Index", $this->pdf->rapport_taal), '', ''));
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			unset($this->pdf->CellBorders);

			foreach ($benchmarkCategorie[$SpecifiekeIndex['SpecifiekeIndex']] as $fonds)
			{
				$fondsData = $indexData[$fonds];
				$this->pdf->row(array(vertaalTekst($fondsData['toelichting'], $this->pdf->rapport_taal), $fondsData['Omschrijving'], $this->formatGetal($fondsData['performance'], 1), $this->formatGetal($fondsData['performanceJaar'], 1)));
			}
		}
  	// $this->pdf->ln();


  if(count($indexValuta)>0)
	{
		$this->pdf->SetWidths(array(110, 30, 30));
		$this->pdf->SetAligns(array('L', 'R', 'R'));
		$this->pdf->CellBorders = array('U', 'U', 'U');
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
		$this->pdf->row(array("\n" . vertaalTekst("Valutarendementen", $this->pdf->rapport_taal), '', ''));
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

		foreach ($indexValuta as $valuta => $valutaData)
		{
			$this->pdf->row(array(vertaalTekst($valutaData['Omschrijving'], $this->pdf->rapport_taal), $this->formatGetal($valutaData['performance'], 1), $this->formatGetal($valutaData['performanceJaar'], 1)));
		}
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
	}

		$this->pdf->ln();
		if( $this->pdf->getY()+(count($benchmarkCategorie['rest'])+2)*$this->pdf->rowHeight > $this->pdf->PageBreakTrigger)
		{
			$this->pdf->addPage();
			$this->pdf->ln();
		}
		$this->pdf->SetWidths(array(60,50,30,30));
		$this->pdf->SetAligns(array('L','L','R','R'));
		$this->pdf->CellBorders = array('U','U','U','U');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	//$this->pdf->row(array("\nOverige indices","\nIndex (in €)",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	$this->pdf->row(array("\n\n".vertaalTekst("Overige indices",$this->pdf->rapport_taal),"\n\n".vertaalTekst("Index",$this->pdf->rapport_taal),
  	vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart))." ".vertaalTekst("in",$this->pdf->rapport_taal)."\n".vertaalTekst("euro in %",$this->pdf->rapport_taal),
		vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart))." ".vertaalTekst("in",$this->pdf->rapport_taal)."\n".vertaalTekst("lokale valuta in %",$this->pdf->rapport_taal)));

  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);

  	foreach ($benchmarkCategorie['rest'] as $fonds)
  	{
  	  $fondsData=$indexData[$fonds];
  	  $this->pdf->row(array(vertaalTekst($fondsData['toelichting'],$this->pdf->rapport_taal),$fondsData['Omschrijving'],$this->formatGetal($fondsData['performanceEurJaar'],1),$this->formatGetal($fondsData['performanceJaar'],1),$fondsData['Valuta']));
  	}

   // foreach ($indexData as $fonds=>$fondsData)
  //    $this->pdf->row(array($fondsData['toelichting'],$fondsData['Omschrijving'],$this->formatGetal($fondsData['performance'],1),$this->formatGetal($fondsData['performanceJaar'],1)));
$this->nadereToelichting();






	}

	function nadereToelichting()
	{
    
    
    $gebruikteCrmVelden=array(
      'HeaderToelBM1',
      'HeaderToelBM2',
      'ToelBM1',
      'ToelBM2');
    $query = "DESC CRM_naw";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $crmVelden=array();
    while($data=$DB->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
    
    $nawSelect='';
    $nietgevonden=array();
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $nawSelect.=",CRM_naw.$veld ";
      }
      else
      {
        $nietgevonden[]=$veld;
      }
    }

//        listarray($gebruikteCrmVelden)."<br>\n"; listarray($nietgevonden)."<br>\n";
    /*
    if(count($nietgevonden) > 0)
    {
      $this->pdf->MultiCell($this->colWidth, 5, 'Niet gevonden crm velden: ');
      for ($x=0; $x < count($nietgevonden);$x++)
      {
        $this->pdf->MultiCell($this->colWidth, 5, '-> '.$nietgevonden[$x]);
      }

    }
    */
    $query="
      SELECT CRM_naw.id
        $nawSelect
      FROM
        CRM_naw
      WHERE
        CRM_naw.portefeuille='".$this->portefeuille."'";
    
    $DB->SQL($query);
    $crmData=$DB->lookupRecord();
    
    
    if($crmData['HeaderToelBM1']<>'')
    {
      $header1=vertaalTekst($crmData['HeaderToelBM1'],$this->pdf->rapport_taal);
      $header2=vertaalTekst($crmData['HeaderToelBM2'],$this->pdf->rapport_taal);
      $text1=$crmData['ToelBM1'];
      $text2=$crmData['ToelBM2'];
    }
    else
    {
      $header1=vertaalTekst('Risicodragende beleggingen',$this->pdf->rapport_taal);
      $header2=vertaalTekst('Risicomijdende beleggingen',$this->pdf->rapport_taal);
      if ($this->pdf->rapport_taal == 1)
      {
        $text1 = "For growth assets, MSCI World is used as the benchmark as of 2017. This index is calculated on a total return basis, so including dividends, in euro's. Previously the benchmark was a composite of 50% MSCI Europe and 50% MSCI World (also on a total return basis). These indices are published on www.mscibarra.com.";
        $text2 = "For fixed income and comparable investments we use the JP Morgan Government Bond Index Netherlands as the benchmark. This index represents the weighted average return of all Dutch government bonds in euro’s with a remaining maturity of at least 13 months.
  
To put the performance of the portfolio in a broader context, we show, apart from the composite benchmark, also a number of other index returns.";
      }
      else
      {
        $text1 = "Voor risicodragende beleggingen (aandelen en alternatieven) wordt de MSCI World (wereldwijde aandelen, ontwikkelde markten) als benchmark gebruikt. Deze index wordt op total return-basis, ofwel inclusief dividenden, in euro’s weergegeven. Deze indices zijn op de website van www.mscibarra.com te volgen.";
        $text2 = "Voor risicomijdende beleggingen (obligaties en liquiditeiten) hanteren wij de vergelijkingsmaatstaf JP Morgan Government Bond Index Netherlands. Deze index vertegenwoordigt het gewogen gemiddelde rendement van alle Nederlandse staatsobligaties in euro’s, met een looptijd langer dan 13 maanden.

Om de resultaten van de portefeuille in een breder perspectief te plaatsen, geven wij u naast de samengestelde benchmark, ook een aantal andere vergelijkingsmaatstaven.";
      }
    }
    
    
    $ybackup=$this->pdf->getY();
		$this->pdf->SetY(60);
		$this->pdf->ln();
		$this->pdf->SetWidths(array(182,103));
		$this->pdf->SetAligns(array('L','L'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',vertaalTekst("Nadere toelichting",$this->pdf->rapport_taal)));
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$header1));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$text1));
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);  ///
		$this->pdf->row(array('',$header2));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$text2));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetY($ybackup);

	}
}
?>