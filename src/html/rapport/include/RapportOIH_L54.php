<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/01/12 14:01:32 $
File Versie					: $Revision: 1.9 $

$Log: RapportOIH_L54.php,v $
Revision 1.9  2020/01/12 14:01:32  rvv
*** empty log message ***

Revision 1.8  2019/12/04 15:56:35  rvv
*** empty log message ***

Revision 1.7  2019/12/01 07:51:04  rvv
*** empty log message ***

Revision 1.6  2019/10/02 15:12:58  rvv
*** empty log message ***

Revision 1.5  2017/09/24 10:02:35  rvv
*** empty log message ***

Revision 1.4  2017/09/23 17:42:26  rvv
*** empty log message ***

Revision 1.3  2017/09/16 18:07:12  rvv
*** empty log message ***

Revision 1.2  2017/09/13 10:01:56  rvv
*** empty log message ***

Revision 1.1  2017/09/10 14:31:29  rvv
*** empty log message ***

Revision 1.7  2016/07/31 10:40:44  rvv
*** empty log message ***

Revision 1.6  2016/07/13 16:06:39  rvv
*** empty log message ***

Revision 1.5  2016/06/02 07:10:25  rvv
*** empty log message ***

Revision 1.4  2016/06/01 19:48:58  rvv
*** empty log message ***

Revision 1.3  2016/05/04 16:01:30  rvv
*** empty log message ***

Revision 1.2  2016/04/23 15:33:07  rvv
*** empty log message ***

Revision 1.1  2016/03/20 14:32:23  rvv
*** empty log message ***

Revision 1.5  2015/03/14 17:25:18  rvv
*** empty log message ***


*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L35.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportOIH_L54
{
	function RapportOIH_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	function getFondsKoers($fonds,$datum,$datumTerug=false)
	{
	  $db=new DB();
	  $query="SELECT Koers,date(Datum) as Datum FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
    if($datumTerug==true)
      return $koers;
    else
	    return $koers['Koers'];
	}

	function getValutaKoers($valuta,$datum,$datumTerug=false)
	{
	  $db=new DB();
	  $query="SELECT Koers,date(Datum) as Datum FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
    if($datumTerug==true)
      return $koers;
    else
      return $koers['Koers'];
	}
  
function getPerformance($fonds,$vanaf,$tot,$valuta=false)
{
  $att=new ATTberekening_L35();
  $maanden=$att->getMaanden(db2jul($vanaf),db2jul($tot));
  $januari=substr($tot,0,4)."-01-01";
  
  $totalPerf=0;
  foreach($maanden as $maand)
  {
    if($valuta==true)
      $indexData=array('fondsKoers_eind'=>$this->getValutaKoers($fonds,$maand['stop']),
                    'fondsKoers_begin'=>$this->getValutaKoers($fonds,$maand['start']),
                    'fondsKoers_jan'=>$this->getValutaKoers($fonds,$januari));   
    else
     $indexData=array('fondsKoers_eind'=>$this->getFondsKoers($fonds,$maand['stop']),
                    'fondsKoers_begin'=>$this->getFondsKoers($fonds,$maand['start']),
                    'fondsKoers_jan'=>$this->getFondsKoers($fonds,$januari));
                    
   $jaarPerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );                 
   $voorPerf=($indexData['fondsKoers_begin'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );
   $totalPerf+=($jaarPerf-$voorPerf);
   //echo "m $fonds ".($jaarPerf-$voorPerf)." <br>\n";
  }
  //echo "t $fonds $totalPerf  $vanaf,$tot <br>\n";
  return $totalPerf;
}

  function fondsStandaarddeviatie($Fonds,$rapportageDatum)
  {
    $perioden = array('3-jaars' => 'date(\'' . $rapportageDatum . '\')-interval 3 year');
    $db=new DB();

    foreach ($perioden as $periode => $wherePeriode)
    {
      $query = "select Datum FROM Fondskoersen WHERE Fonds='$Fonds' AND Datum < $wherePeriode limit 1";
      $db->SQL($query);
      $beschikbaar = $db->lookupRecord();
      $query = "SELECT koers FROM Fondskoersen WHERE Fonds='$Fonds' AND Datum > $wherePeriode order by Datum";
      $db->SQL($query);
      $db->Query();
      unset($laatsteKoers);
      $koersRendementen = array();
  
      while ($data = $db->nextRecord())
      {
        if (isset($laatsteKoers))
          $koersRendementen[] = ($data['koers'] / $laatsteKoers) * 100;
        $laatsteKoers = $data['koers'];
      }
      $sdtev = standard_deviation($koersRendementen);
      if ($beschikbaar['Datum'] == '')
        $stddeviatieJaar = 'na';
      else
      {
        $jaren = substr($periode, 0, 1);
        $stddeviatieJaar = round($sdtev * pow((count($koersRendementen) / $jaren), 0.5), 2);
        //echo "$stddeviatieJaar=round($sdtev*pow((".count($koersRendementen)."/$jaren),0.5),2);<br>\n";
      }
      return $stddeviatieJaar;
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



    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(32);
  	$this->pdf->SetWidths(array(5,35,70,24,24,32,32,32,25));
  	$this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	$this->pdf->CellBorders = array('','U','U','U','U','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $sterGetoond=false;
  	if($perioden['jan']==$perioden['begin'])
  	{
      $this->pdf->SetWidths(array(10,35,70,24,24,32,32,32,25));
  	  $this->pdf->CellBorders = array('','U','U','U','U','U','U');
  	  $this->pdf->row(array("","\nCategorie","\nIndex","Koers\n".date("d-m-Y",db2jul($perioden['begin'])),"Koers\n".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %',"Standaard-\ndeviatie"));
  	}
  	else
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U','U','U','U','U');
  	  $this->pdf->row(array("","\nCategorie","\nIndex","Koers\n".date("d-m-Y",db2jul($perioden['jan'])),"Koers\n".date("d-m-Y",db2jul($perioden['begin'])),
                        "Koers\n".date("d-m-Y",db2jul($perioden['eind'])),
                        'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %',"Standaard-\ndeviatie"));
  	}
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);


/*
  $benchmarkCategorie=array();
	  $query="SELECT specifiekeIndex as Beursindex,
    Fondsen.Omschrijving,
Fondsen.Valuta,
'Benchmark' as catOmschrijving
 FROM Portefeuilles 
 Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds
 WHERE Portefeuilles.Portefeuille='".$this->portefeuille."' ORDER BY Fondsen.Fonds";
 		$DB->SQL($query);
		$DB->Query();

	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
      }
      
     	$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);

      $tmp=$this->getFondsKoers($index['Beursindex'],$perioden['eind'],true);
      if($tmp['Datum'] <> $perioden['eind'])
      {
        $indexData[$index['Beursindex']]['ster']=true;
      }
 		}
*/
//listarray($this->pdf->portefeuilledata);
$verm=strtoupper(substr($this->pdf->portefeuilledata['VermogensbeheerderNaam'],0,3));
    $query="SELECT
benchmarkverdeling.benchmark as Beursindex,
    Fondsen.Omschrijving,
Fondsen.Valuta,
'Benchmark' as catOmschrijving
FROM
benchmarkverdeling
 Inner Join Fondsen ON benchmarkverdeling.benchmark = Fondsen.Fonds
WHERE benchmarkverdeling.benchmark like '".$verm."%'
GROUP BY benchmarkverdeling.benchmark ORDER BY Fondsen.Fonds";
   
 		$DB->SQL($query);
		$DB->Query();

	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
      }
      
     	$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);
      $tmp=$this->getFondsKoers($index['Beursindex'],$perioden['eind'],true);
      if($tmp['Datum'] <> $perioden['eind'])
      {
        $indexData[$index['Beursindex']]['ster']=true;
      }
 		}

    $fondsen=array_keys($indexData);

  /*
	  $query="SELECT
Indices.Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting,
BeleggingscategoriePerFonds.Vermogensbeheerder,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as catOmschrijving
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
LEFT Join BeleggingscategoriePerFonds ON Indices.Beursindex = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
*/
$query="SELECT
benchmarkverdeling.benchmark,
benchmarkverdeling.fonds as Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta
FROM
benchmarkverdeling
Inner Join Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
WHERE benchmark IN('".implode("','",$fondsen)."') GROUP BY Beursindex ORDER BY Fondsen.Omschrijving ";



		$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
      }
      
     	$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);

      $tmp=$this->getFondsKoers($index['Beursindex'],$perioden['eind'],true);
      if($tmp['Datum'] <> $perioden['eind'])
      {
         $indexData[$index['Beursindex']]['ster']=true;
      }
 		}

    /*

  $query="SELECT
  Valutas.Valuta as Beursindex,
  Valutas.Omschrijving,
  'Valuta' as catOmschrijving
  FROM
  Valutas
  WHERE Valutas.Valuta='USD'";
      $DB->SQL($query);
      $DB->Query();
       while($index = $DB->nextRecord())
      {
        if($index['catOmschrijving'] == '')
          $index['catOmschrijving']='Overige';

        $benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

         $indexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getValutaKoers($index['Beursindex'],$datum);
        }

         $indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind'],true);
        $indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind'],true);
        $tmp=$this->getValutaKoers($index['Beursindex'],$perioden['eind'],true);
        if($tmp['Datum'] <> $perioden['eind'])
        {
          $indexData[$index['Beursindex']]['ster']=true;
        }
       }
      */

  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	  $this->pdf->row(array("",$categorie));
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Ln(-4);

  	  foreach ($fondsen as $fonds)
  	  {
        $stdev=$this->fondsStandaarddeviatie($fonds,$this->rapportageDatum);
  	    $fondsData=$indexData[$fonds];
        if($fondsData['ster']==true)
        {
          $ster = '*';
          $sterGetoond=true;
        }
        else
          $ster='';
  	    if($perioden['jan']==$perioden['begin'])
  	    {
  	      $this->pdf->row(array('','',$fondsData['Omschrijving'].$ster,
     	    $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
  	      $this->formatGetal($fondsData['performance'],2),
          $this->formatGetal($stdev,2)));
  	    }
  	    else
  	    {
  	      $this->pdf->row(array('','',$fondsData['Omschrijving'].$ster,
  	                        $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],2),
  	                        $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
  	                        $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
  	                        $this->formatGetal($fondsData['performance'],2),
                            $this->formatGetal($fondsData['performanceJaar'],2),
                            $this->formatGetal($stdev,2)));
  	    }
        
  	  }
  
  	}
  	
  	/*
    if($sterGetoond)
    {
      $this->pdf->ln();
      $this->pdf->SetWidths(array(10,100));
      //$this->pdf->row(array('* De koers van dit instrument is ouder dan '.date("d-m-Y",db2jul($perioden['eind']))));
      $this->pdf->row(array('',"* De koers van dit instrument is vertraagd."));
    }
  	*/
    $this->pdf->ln();

    $query2 = "SELECT
Portefeuilles.Vermogensbeheerder,ModelPortefeuilles.Portefeuille,ModelPortefeuilles.Omschrijving
FROM ModelPortefeuilles
INNER JOIN Portefeuilles ON ModelPortefeuilles.Portefeuille = Portefeuilles.Portefeuille
WHERE Portefeuilles.Vermogensbeheerder= '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Portefeuilles.einddatum > now() AND ModelPortefeuilles.fixed=0
ORDER BY Portefeuille";
    $DB->SQL($query2);
    $DB->Query();
    $modelPortefeuilles=array();
    while($model=$DB->nextRecord())
    {
      $modelPortefeuilles[$model['Portefeuille']]=$model;
      $index = new indexHerberekening();
      $indexWaarden = $index->getWaarden($this->rapportageDatumVanaf, $this->rapportageDatum, $model['Portefeuille'], '', 'maanden', $this->pdf->rapportageValuta);
      $modelPortefeuilles[$model['Portefeuille']]['performance'] = $indexWaarden[count($indexWaarden)]['index'] - 100;
      $indexWaarden = $index->getWaarden($this->tweedePerformanceStart, $this->rapportageDatum, $model['Portefeuille'], '', 'maanden', $this->pdf->rapportageValuta);
      $modelPortefeuilles[$model['Portefeuille']]['performanceJaar'] = $indexWaarden[count($indexWaarden)]['index'] - 100;
  
      $regels=berekenPortefeuilleWaarde($model['Portefeuille'],$this->rapportageDatum,false,'EUR',$this->rapportageDatum,2);
      vulTijdelijkeTabel($regels,$model['Portefeuille'],$this->rapportageDatum);
      $afm=AFMstd($model['Portefeuille'],$this->rapportageDatum);
      $modelPortefeuilles[$model['Portefeuille']]['AFMstd']=$afm['std'];
    }
//listarray($modelPortefeuilles);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(10,60,30,30,30,30));
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->CellBorders = array('','U','U','U','U','U');
    //$this->pdf->row(array("\nBenchmarkvergelijking",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
    $this->pdf->row(array('',"\n".vertaalTekst("Modelportefeuilles",$this->pdf->rapport_taal),vertaalTekst("Rendement verslagperiode in %",$this->pdf->rapport_taal),
                      vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' '.vertaalTekst("in %",$this->pdf->rapport_taal),
                      vertaalTekst('Standaarddeviatie',$this->pdf->rapport_taal),vertaalTekst('AFM Standaarddeviatie',$this->pdf->rapport_taal)));

    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($modelPortefeuilles as $modelPortefeuille=>$perf)
    {

      $stdev=new rapportSDberekening($modelPortefeuille,$this->rapportageDatum);
      $stdev->addReeks('totaal');
      $stdev->berekenWaarden();
      $standaardeviatie=$stdev->getUitvoer();


      //	$this->pdf->row(array(vertaalTekst("Rendement portefeuille", $this->pdf->rapport_taal), $this->formatGetal($portefeuille['performance'], 1), $this->formatGetal($portefeuille['performanceJaar'], 1)));
      $this->pdf->row(array('',$perf['Omschrijving'], $this->formatGetal($perf['performance'], 2), $this->formatGetal($perf['performanceJaar'], 2)
                      , $this->formatGetal($standaardeviatie['totaal'], 2), $this->formatGetal($perf['AFMstd'], 2)));
      $this->pdf->excelData[]=array($perf['Omschrijving'],round($perf['performance'],2),round($perf['performanceJaar'],2),round($standaardeviatie['totaal'],2),round($perf['AFMstd'],2));
    }


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
	}
  
    function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4, $startZero=false)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    

    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }

    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

     

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > ($top+$margin); $i-= $absUnit*$stapgrootte)
    {
      //echo round($i)." >= ($top+$margin) | $hDiag<br>\n";
     // if($h)
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      
      if ($i>0 || $startZero==true)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }

      $yval = $yval2;
    }
    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        
        if ($i>0 || $startZero==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
    $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
    $this->pdf->Cell(0,3,$item);
    $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
}
?>