<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/06/18 06:01:58 $
File Versie					: $Revision: 1.4 $

$Log: RapportINDEX_L56.php,v $
Revision 1.4  2015/06/18 06:01:58  rvv
*** empty log message ***

Revision 1.3  2015/05/31 10:15:24  rvv
*** empty log message ***

Revision 1.2  2015/04/01 16:00:45  rvv
*** empty log message ***

Revision 1.1  2015/03/01 14:08:16  rvv
*** empty log message ***



*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L35.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_102/RapportPERFG_L102.php");

class RapportINDEX_L102
{
	function RapportINDEX_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->perfg = new RapportPERFG_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Vergelijkingsmaatstaven";
    $this->pdf->subtitel = "";

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

	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

	function getValutaKoers($valuta,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
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


	function writeRapport()
	{
	  global $__appvar;
	  $this->pdf->addPage();
	  //$this->pdf->templateVars['INDEXPaginas'] = $this->pdf->customPageNo;
    $this->pdf->templateVars['INDEXPaginas']=$this->pdf->page;
    $this->pdf->subtitel='';

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

    $att=new ATTberekening_L35();
    $kwartalen=$att->getKwartalen(db2jul($this->tweedePerformanceStart),db2jul($this->rapportageDatum));
    $perioden=array();
    foreach($kwartalen as $kwartaal)
    {
      $kwartaalTekst='';
      if(substr($kwartaal['start'],5,5)=='01-01' || substr($kwartaal['stop'],5,5)=='03-31')
        $kwartaalTekst='Q1';
      elseif(substr($kwartaal['start'],5,5)=='03-31' || substr($kwartaal['stop'],5,5)=='06-30')
        $kwartaalTekst='Q2'; 
      elseif(substr($kwartaal['start'],5,5)=='06-30' || substr($kwartaal['stop'],5,5)=='09-30')
        $kwartaalTekst='Q3'; 
      elseif(substr($kwartaal['start'],5,5)=='09-30' || substr($kwartaal['stop'],5,5)=='12-31')
        $kwartaalTekst='Q4';                  
      $perioden[$kwartaalTekst]=$kwartaal;
    }  
    $perioden['YTD']=array('start'=>$this->tweedePerformanceStart,'stop'=>$this->rapportageDatum);

	  $DB=new DB();
//	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(40);
    $this->pdf->SetWidths(array(10,250));
    $this->pdf->row(array('','Gebruikte benchmarks in PorteuifelleMonitor

De prestatie van de in uw portefeuille aanwezige beleggingen toetsen wij in ons performance meting systeem van de bijhorende maatstaven, meestal `benchmarks` genoemd.

Tenzij anders overeengekomen met uw beheerder(s), maken wij gebruik van de volgende indices:'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->SetWidths(array(10,40,70,20,20+20+20+20+20));
  	$this->pdf->SetAligns(array('L','L','L','R','C','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();

  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("","","","",vertaalTekst("Rendementen",$this->pdf->rapport_taal)));
    $this->pdf->CellBorders = array('','U','U','U','U','U','U','U','U','U');
    $tmpRow=array("",vertaalTekst("Categorie",$this->pdf->rapport_taal),vertaalTekst("Index",$this->pdf->rapport_taal),vertaalTekst("Weging",$this->pdf->rapport_taal));
    foreach($perioden as $periodeText=>$periodeData)
      $tmpRow[]="". vertaalTekst($periodeText,$this->pdf->rapport_taal);
    $this->pdf->SetWidths(array(10,40,70,20,20,20,20,20,20));
    $this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R'));
	  $this->pdf->row($tmpRow);
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);



    $benchmarkCategorie=array();
    $indexData['Gewogen benchmark']=array('Omschrijving'=>'Gewogen benchmark','catOmschrijving'=>'Benchmark','RegioOmschrijving'=>'');
    
		foreach($perioden as $periodeText=>$tmpPeriode)
    {
     $tmp = $this->perfg->benchmarkBerekening(array(array('start'=>$tmpPeriode['start'],'stop'=>$tmpPeriode['stop'])),true);
  //listarray($tmp);
      $indexData['Gewogen benchmark'][$periodeText]['performance']=$tmp[$tmpPeriode['stop']]['perf'];
  
      $benchmarkCategorie['Benchmark']['']['Gewogen benchmark']='Gewogen benchmark';
    }
   // listarray($indexData);
/*
    $query="SELECT specifiekeIndex as Beursindex,
    Fondsen.Omschrijving,
Fondsen.Valuta,
'Benchmark' as catOmschrijving,
Regios.Omschrijving as RegioOmschrijving
 FROM Portefeuilles
 Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds
 LEFT JOIN BeleggingssectorPerFonds ON Portefeuilles.specifiekeIndex = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
 WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';
      
      $index['RegioOmschrijving']='';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['RegioOmschrijving']][$index['Beursindex']]=$index['Beursindex'];
      $indexData[$index['Beursindex']]=$index;
      foreach($perioden as $periodeText=>$periodeData)
      {
        $indexData[$index['Beursindex']][$periodeText]['performance'] = $this->getPerformance($index['Beursindex'], $periodeData['start'], $periodeData['stop']);
      }
 		}
*/


$query="SELECT
IndexPerBeleggingscategorie.Fonds AS Beursindex,
Beleggingscategorien.Omschrijving as catOmschrijving,
IndexPerBeleggingscategorie.Beleggingscategorie,
Fondsen.Omschrijving,
KeuzePerVermogensbeheerder.Afdrukvolgorde
FROM
IndexPerBeleggingscategorie
LEFT JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
INNER JOIN KeuzePerVermogensbeheerder ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie AND KeuzePerVermogensbeheerder.vermogensbeheerder= '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie='Beleggingscategorien'
WHERE
IndexPerBeleggingscategorie.Vermogensbeheerder =  '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
AND (IndexPerBeleggingscategorie.Portefeuille = ''	OR IndexPerBeleggingscategorie.Portefeuille = '".$this->portefeuille."')
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde, IndexPerBeleggingscategorie.Portefeuille, Fondsen.Omschrijving";
 		$DB->SQL($query);
		$DB->Query();
    $benchmark=array();
	  while($index = $DB->nextRecord())
    {
      $benchmark[$index['Beleggingscategorie']] = $index;
    }
    foreach($benchmark as $index)
    {

      
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';
		  $benchmarkCategorie[$index['catOmschrijving']][$index['RegioOmschrijving']][$index['Beursindex']]=$index['Beursindex'];
      $indexData[$index['Beursindex']]=$index;
      foreach($perioden as $periodeText=>$periodeData)
	  	  $indexData[$index['Beursindex']][$periodeText]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop']);
  
  
      $query="SELECT DatumVanaf FROM NormwegingPerBeleggingscategorie WHERE Portefeuille='" . $this->portefeuille . "' AND DatumVanaf <='". $periodeData['start']."' ORDER BY DatumVanaf desc limit 1 ";
      $DB->SQL($query);
      $DB->Query();
      $datum=$DB->nextRecord();
      $query = "SELECT
NormwegingPerBeleggingscategorie.Portefeuille,
NormwegingPerBeleggingscategorie.Beleggingscategorie as categorie,
NormwegingPerBeleggingscategorie.Normweging
FROM
NormwegingPerBeleggingscategorie
WHERE Beleggingscategorie='".mysql_real_escape_string($index['Beleggingscategorie']) ."' AND Portefeuille='" . $this->portefeuille . "' AND DatumVanaf ='". $datum['DatumVanaf']."' ORDER BY DatumVanaf desc limit 1 ";
      $DB->SQL($query);
      $DB->Query();
      $weging = $DB->nextRecord();
  
      //$query="SELECT Normweging FROM NormwegingPerBeleggingscategorie WHERE Beleggingscategorie='".mysql_real_escape_string($index['Beleggingscategorie']) ."' AND Portefeuille= '".$this->portefeuille."'";
      //$DB->SQL($query);
      //$DB->Query();
      //$weging = $DB->nextRecord();
      $indexData[$index['Beursindex']]['weging']=$weging['Normweging'];
      
      //echo $index['Beursindex']." -> ".$indexData[$index['Beursindex']]['performance']."<br>\n";
 		}
/*
    $benchmarkRendement=array();
 		foreach($indexData as $fonds=>$details)
    {

      if($details['weging'] <> 0)
      {
        foreach($perioden as $periodeText=>$periodeData)
          $benchmarkRendement[$periodeText]+=$details['weging']*$details[$periodeText]['performance']/100;
      }
    }
    foreach($benchmarkRendement as $periodeText=>$rendement)
      $indexData['Gewogen benchmark'][$periodeText]['performance']=$rendement;
*/
  
	  $query="SELECT
Indices.Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting,
BeleggingscategoriePerFonds.Vermogensbeheerder,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as catOmschrijving,
BeleggingssectorPerFonds.Regio,
Regios.Omschrijving as RegioOmschrijving,
Regios.Afdrukvolgorde
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
LEFT Join BeleggingscategoriePerFonds ON Indices.Beursindex = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT JOIN BeleggingssectorPerFonds ON Indices.Beursindex = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";


		$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['RegioOmschrijving']][$index['Beursindex']]=$index['Beursindex'];
      $indexData[$index['Beursindex']]=$index;
      foreach($perioden as $periodeText=>$periodeData)
        $indexData[$index['Beursindex']][$periodeText]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop']);
   	}
/*
$query="SELECT
Valutas.Valuta as Beursindex,
Valutas.Omschrijving,
'Valuta' as catOmschrijving,
'' as RegioOmschrijving
FROM
Valutas
WHERE Valutas.Valuta='USD'";
		$DB->SQL($query);
		$DB->Query();
 	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['RegioOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach($perioden as $periodeText=>$periodeData)
        $indexData[$index['Beursindex']][$periodeText]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop'],true);

 		}   
  */
    $n=0;
  	foreach ($benchmarkCategorie as $categorie=>$regios)
  	{
  	  foreach($regios as $regio =>$fondsen)
      {
        $this->pdf->SetFillColor(220,220,220);
        unset($this->pdf->fillCell);
        $n++;
        foreach ($fondsen as $fonds)
        {
          $fondsData = $indexData[$fonds];
          
          $tmpRow = array("", "", $fondsData['Omschrijving']);
          if($fondsData['weging']<>0)
          {
            $tmpRow[] = $this->formatGetal($fondsData['weging'], 1);
            $this->pdf->fillCell=array(0,1,1,1,1,1,1,1,1);
          }
          else
          {
            $tmpRow[] = '';
            unset($this->pdf->fillCell);
          }
  
          if($categorie=='Benchmark')
          {
            $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0], $this->pdf->rapport_background_fill[1], $this->pdf->rapport_background_fill[2]);
            $this->pdf->fillCell=array(0,1,1,1,1,1,1,1,1);
          }
          foreach ($perioden as $periodeText => $periodeData)
          {
            $tmpRow[] = $this->formatGetal($fondsData[$periodeText]['performance'], 2);
          }
          $this->pdf->row($tmpRow);
        
        }
        $this->pdf->Ln(-4);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->row(array("", $categorie));
        unset($this->pdf->fillCell);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        
        if($categorie=='Benchmark')
          $this->pdf->ln();
      }
  	}
    unset( $this->pdf->fillCell);
    $this->pdf->Ln();
    $this->pdf->SetWidths(array(10,250));
    $this->pdf->row(array('','De beheerder kan op twee manieren een hoger rendement halen dan de markt. Hij kan betere effecten selecteren dan die waaruit de benchmark bestaat. Dit onderdeel van de performance wordt het "Selectie-effect" genoemd.
De beheerder kan (afhankelijk van bandbreedte afspraken) de beleggingen binnen een bepaalde benchmarkcategorie over- of onderwegen ten opzichte van de afgesproken verdeling. Dit onderdeel van de performance wordt het "Allocatie-effect" genoemd.
De som van het "Selectie-effect" en "Allocatie-effect" is altijd gelijk aan de totale onder- of overperformance.
Het Selectie-effect en Allocatie-effect wordt verder in het rapport besproken.'));

   // foreach ($indexData as $fonds=>$fondsData)
  //    $this->pdf->row(array($fondsData['toelichting'],$fondsData['Omschrijving'],$this->formatGetal($fondsData['performance'],1),$this->formatGetal($fondsData['performanceJaar'],1)));
/*
    $this->pdf->ln();
    $this->pdf->SetWidths(array(110,30,30));
  	$this->pdf->SetAligns(array('L','R','R'));
  	$this->pdf->CellBorders = array('U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->row(array("\nValutarendementen",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
  	foreach ($indexValuta as $valuta=>$valutaData)
     $this->pdf->row(array($valutaData['Omschrijving'],$this->formatGetal($valutaData['performance'],1),$this->formatGetal($valutaData['performanceJaar'],1)));
   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
*/
//$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	}
}
?>