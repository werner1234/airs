<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/12/09 17:54:25 $
File Versie					: $Revision: 1.3 $

$Log: RapportINDEX_L73.php,v $
Revision 1.3  2017/12/09 17:54:25  rvv
*** empty log message ***

Revision 1.2  2017/11/11 18:23:47  rvv
*** empty log message ***

Revision 1.1  2017/10/04 16:09:09  rvv
*** empty log message ***

Revision 1.8  2017/04/12 08:33:15  rvv
*** empty log message ***

Revision 1.7  2017/03/29 16:23:27  rvv
*** empty log message ***

Revision 1.5  2015/03/14 17:25:18  rvv
*** empty log message ***


*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L73.php");

class RapportIndex_L73
{
	function RapportIndex_L73($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
  
function getPerformance($fonds,$vanaf,$tot,$valuta=false,$indexdata=array())
{
  $att=new ATTberekening_L73($this);
  $maanden=$att->getMaanden(db2jul($vanaf),db2jul($tot));
  $januari=substr($tot,0,4)."-01-01";
  
  $totalPerf=0;
  foreach($maanden as $maand)
  {
		if($indexdata['catOmschrijving']=='Benchmark')
		{
			$totaalIndex=$att->indexPerformance('totaal',$maand['start'],$maand['stop']);
			$totalPerf+=($totaalIndex['perf']*100);
		}
    else
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
		}
   //echo "m $fonds ".($jaarPerf-$voorPerf)." <br>\n";
  }
  //echo "t $fonds $totalPerf  $vanaf,$tot <br>\n";
  return $totalPerf;
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
		$this->pdf->SetY(40);
  	$this->pdf->SetWidths(array(10,40,70,33,33,33,33,33));
  	$this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	$this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	if($perioden['jan']==$perioden['begin'])
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U','U');
  	  $this->pdf->row(array("","\nCategorie","\nIndex","Koers\n".date("d-m-Y",db2jul($perioden['begin'])),"Koers\n".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %'));
  	}
  	else
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
  	  $this->pdf->row(array("","\nCategorie","\nIndex","Koers\n".date("d-m-Y",db2jul($perioden['jan'])),"Koers\n".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	}
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);



  $benchmarkCategorie=array();
	  $query="SELECT specifiekeIndex as Beursindex,
    Fondsen.Omschrijving,
Fondsen.Valuta,
'Benchmark' as catOmschrijving
 FROM Portefeuilles 
 Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds
 WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
 		$DB->SQL($query);
		$DB->Query();


	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
			if($index['catOmschrijving'] <> 'Benchmark')
			{
				foreach ($perioden as $periode => $datum)
				{
					$indexData[$index['Beursindex']]['fondsKoers_' . $periode] = $this->getFondsKoers($index['Beursindex'], $datum);
				}
				$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind'],false,$index);
				$indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind'],false,$index);
			}
			else
			{
			//	$indexData[$index['Beursindex']]['performanceJaar'] =  	getFondsPerformance($index['Beursindex'], $perioden['jan'],$perioden['eind']);
			//	$indexData[$index['Beursindex']]['performance'] =  	getFondsPerformance($index['Beursindex'], $perioden['begin'], $perioden['eind']);

				$indexData[$index['Beursindex']]['performanceJaar'] = getFondsPerformanceGestappeld($index['Beursindex'],$this->portefeuille,$perioden['jan'],$perioden['eind'],'maanden');
				$indexData[$index['Beursindex']]['performance'] = getFondsPerformanceGestappeld($index['Beursindex'],$this->portefeuille,$perioden['begin'], $perioden['eind'],'maanden');
			}

 		}


$query="SELECT
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds as Beursindex,
IndexPerBeleggingscategorie.Vermogensbeheerder,
Fondsen.Omschrijving,
Beleggingscategorien.Omschrijving as catOmschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
(IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille = '".$this->portefeuille."')
ORDER BY Beleggingscategorien.Afdrukvolgorde";
 		$DB->SQL($query);
		$DB->Query();

	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
			foreach ($perioden as $periode => $datum)
			{
				$indexData[$index['Beursindex']]['fondsKoers_' . $periode] = $this->getFondsKoers($index['Beursindex'], $datum);
			}
     	$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);
 		}
  
  
  
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
 		}
  

$query="SELECT
Valutas.Valuta as Beursindex,
Valutas.Omschrijving,
'Valuta' as catOmschrijving
FROM
Valutas
WHERE Valutas.Valuta IN('USD','GBP','JPY') ORDER BY Afdrukvolgorde";
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
 		}   
    
    
  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	  $this->pdf->row(array("",$categorie));
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Ln(-4);

  	  foreach ($fondsen as $fonds)
  	  {
				if($categorie=='Valuta')
					$decimalen=4;
				else
					$decimalen=2;
				if($categorie=='Benchmark')
				{
					$beginKoersTxt = '';
					$eindKoersTxt = '';
					$janKoersTxt = '';
				}
				else
				{
					$beginKoersTxt = $this->formatGetal($indexData[$fonds]['fondsKoers_begin'], $decimalen);
					$eindKoersTxt = $this->formatGetal($indexData[$fonds]['fondsKoers_eind'], $decimalen);
					$janKoersTxt = $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],$decimalen);
				}
  	    $fondsData=$indexData[$fonds];
  	    if($perioden['jan']==$perioden['begin'])
  	    {
  	      $this->pdf->row(array('','',$fondsData['Omschrijving'],
					$beginKoersTxt,
					$eindKoersTxt,
  	      $this->formatGetal($fondsData['performance'],2)));
  	    }
  	    else
  	    {
  	      $this->pdf->row(array('','',$fondsData['Omschrijving'],
  	      $janKoersTxt,
					$beginKoersTxt,
					$eindKoersTxt,
  	      $this->formatGetal($fondsData['performance'],2),$this->formatGetal($fondsData['performanceJaar'],2)));
  	    }
        
  	  }
  
  	}

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