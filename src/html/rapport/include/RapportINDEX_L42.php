<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2014/12/06 18:13:44 $
File Versie					: $Revision: 1.2 $

$Log: RapportINDEX_L42.php,v $
Revision 1.2  2014/12/06 18:13:44  rvv
*** empty log message ***

Revision 1.1  2014/05/25 14:38:33  rvv
*** empty log message ***


*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L35.php");

class RapportIndex_L42
{
	function RapportIndex_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

function fondsPerformance($fonds,$vanaf,$tot)
{
  $att=new ATTberekening_L35();
  $maanden=$att->getMaanden(db2jul($vanaf),db2jul($tot));
  $januari=substr($tot,0,4)."-01-01";
  
  $totalPerf=0;
  foreach($maanden as $maand)
  {
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
	  $this->pdf->templateVars['INDEXPaginas'] = $this->pdf->page;

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";


	  $DB=new DB();
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

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
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
      }
      
     	$indexData[$index['Beursindex']]['performanceJaar'] = $this->fondsPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Beursindex']]['performance'] =    $this->fondsPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);
 		}



    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	
  	$this->pdf->SetWidths(array(10,60,33,33,33,33,33));
  	$this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	$this->pdf->CellBorders = array('','U','U','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	if($perioden['jan']==$perioden['begin'])
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U');
  	  $this->pdf->row(array("","\nIndex","Koers ".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %'));
  	}
  	else
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U','U','U');
  	  $this->pdf->row(array("","\nIndex","Koers ".date("d-m-Y",db2jul($perioden['jan'])),"Koers ".date("d-m-Y",db2jul($perioden['begin'])),"Koers ".date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	}
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);


  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	  $this->pdf->row(array("",$categorie));
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	  foreach ($fondsen as $fonds)
  	  {
  	    $fondsData=$indexData[$fonds];
  	    if($perioden['jan']==$perioden['begin'])
  	    {
  	      $this->pdf->row(array('',$fondsData['Omschrijving'],
     	    $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
  	      $this->formatGetal($fondsData['performance'],2)));
  	    }
  	    else
  	    {
  	      $this->pdf->row(array('',$fondsData['Omschrijving'],
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],2),
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
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