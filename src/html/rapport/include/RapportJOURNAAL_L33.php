<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/08/06 06:17:03 $
File Versie					: $Revision: 1.3 $

$Log: RapportJOURNAAL_L33.php,v $
Revision 1.3  2017/08/06 06:17:03  rvv
*** empty log message ***

Revision 1.2  2017/08/04 05:51:02  rvv
*** empty log message ***

Revision 1.1  2017/08/02 18:23:27  rvv
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

class RapportJournaal_L33
{
	function RapportJournaal_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "JOURNAAL";
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
		$this->pdf->excelData[]=array("Instrument","Verslagperiode","YTD");

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

	  $query2 = "SELECT
Portefeuilles.Vermogensbeheerder,
ModelPortefeuilles.Portefeuille
FROM
ModelPortefeuilles
INNER JOIN Portefeuilles ON ModelPortefeuilles.Portefeuille = Portefeuilles.Portefeuille
WHERE Portefeuilles.Vermogensbeheerder= '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Portefeuilles.einddatum > now() ORDER BY Portefeuille";
		$DB->SQL($query2);
		$DB->Query();
		while($model=$DB->nextRecord())
		{

			$index = new indexHerberekening();
			$indexWaarden = $index->getWaarden($this->rapportageDatumVanaf, $this->rapportageDatum, $model['Portefeuille'], '', 'maanden', $this->pdf->rapportageValuta);
			$modelPortefeuilles[$model['Portefeuille']]['performance'] = $indexWaarden[count($indexWaarden)]['index'] - 100;
			$indexWaarden = $index->getWaarden($this->tweedePerformanceStart, $this->rapportageDatum, $model['Portefeuille'], '', 'maanden', $this->pdf->rapportageValuta);
			$modelPortefeuilles[$model['Portefeuille']]['performanceJaar'] = $indexWaarden[count($indexWaarden)]['index'] - 100;
//
		}


/*
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
    */
    $query=" SELECT Fondsen.Omschrijving, Fondsen.Valuta,benchmarkverdeling.toelichting,benchmarkverdeling.`benchmark`, benchmarkverdeling.`benchmark` as Beursindex
             FROM benchmarkverdeling join Fondsen on  benchmarkverdeling.`benchmark` =  Fondsen.Fonds 
             WHERE (Fondsen.einddatum>now() OR Fondsen.einddatum='0000-00-00') AND benchmarkverdeling.`benchmark` like '%DOO%'  GROUP BY benchmarkverdeling.`benchmark`";//benchmarkverdeling.benchmark='".$SpecifiekeIndex['SpecifiekeIndex']."'";
		$DB->SQL($query); 
		$DB->Query();
    while($index = $DB->nextRecord())
		{
      foreach($index as $key=>$value)
      {
        if($value <> '')
          $indices[$index['benchmark']][$key]=$value;
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

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(60);
  	$this->pdf->SetWidths(array(110,30,30));
  	$this->pdf->SetAligns(array('L','R','R'));
  	$this->pdf->CellBorders = array('U','U','U');
  	//$this->pdf->row(array("\nBenchmarkvergelijking",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	$this->pdf->row(array("\n".vertaalTekst("Modelportefeuilles",$this->pdf->rapport_taal),vertaalTekst("Rendement verslagperiode in %",$this->pdf->rapport_taal),vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' '.vertaalTekst("in %",$this->pdf->rapport_taal)));


  	unset($this->pdf->CellBorders);
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach($modelPortefeuilles as $modelPortefeuille=>$perf)
		{
		//	$this->pdf->row(array(vertaalTekst("Rendement portefeuille", $this->pdf->rapport_taal), $this->formatGetal($portefeuille['performance'], 1), $this->formatGetal($portefeuille['performanceJaar'], 1)));
			$this->pdf->row(array($modelPortefeuille, $this->formatGetal($perf['performance'], 1), $this->formatGetal($perf['performanceJaar'], 1)));
			$this->pdf->excelData[]=array($modelPortefeuille,round($perf['performance'],1),round($perf['performanceJaar'],1));
		}
		$this->pdf->ln();

    $this->pdf->SetWidths(array(60+50,30,30));
  	$this->pdf->SetAligns(array('L','R','R'));
  	$this->pdf->CellBorders = array('U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);


  	$this->pdf->row(array("\n".vertaalTekst("Benchmark",$this->pdf->rapport_taal),
  	vertaalTekst("Rendement verslagperiode in %",$this->pdf->rapport_taal),vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' '.vertaalTekst("in %",$this->pdf->rapport_taal)));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);

  	foreach ($benchmarkCategorie as $benchmark=>$benchmarkFondsen)
  	{
			foreach($benchmarkFondsen as $fonds)
			{
				if($this->pdf->getY() > 180 )
					$this->pdf->addPage();
				$this->pdf->SetWidths(array(60+50,30,30));
				$this->pdf->SetAligns(array('L','R','R'));
				$fondsData = $indexData[$fonds];
				$this->pdf->row(array($fondsData['Omschrijving'], $this->formatGetal($fondsData['performance'], 1), $this->formatGetal($fondsData['performanceJaar'], 1)));
				$this->pdf->excelData[]=array($fondsData['Omschrijving'],round($fondsData['performance'],1),round($fondsData['performanceJaar'],1));
			}
  	}


	}
}
?>