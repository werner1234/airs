<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/03/26 14:29:35 $
File Versie					: $Revision: 1.8 $

$Log: RapportINDEX_L51.php,v $
Revision 1.8  2018/03/26 14:29:35  rvv
*** empty log message ***

Revision 1.7  2018/03/26 11:06:54  rvv
*** empty log message ***

Revision 1.6  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.5  2015/12/30 19:01:23  rvv
*** empty log message ***

Revision 1.4  2015/12/20 16:46:36  rvv
*** empty log message ***

Revision 1.3  2014/09/06 15:24:17  rvv
*** empty log message ***

Revision 1.2  2014/09/03 15:56:32  rvv
*** empty log message ***

Revision 1.1  2014/05/04 10:55:50  rvv
*** empty log message ***


*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L35.php");

class RapportIndex_L51
{
	function RapportIndex_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

/*
$query="(SELECT
IndexPerAttributieCategorie.AttributieCategorie,
IndexPerAttributieCategorie.Fonds,
AttributieCategorien.Omschrijving as catOmschrijving,
Fondsen.Omschrijving,
1 as tweedeVolgorde,
IFNULL(AttributieCategorien.Afdrukvolgorde,127) as hoofdVolgorde
FROM
IndexPerAttributieCategorie 
JOIN Fondsen ON IndexPerAttributieCategorie.Fonds=Fondsen.Fonds
JOIN AttributieCategorien ON IndexPerAttributieCategorie.AttributieCategorie=AttributieCategorien.AttributieCategorie
WHERE
 IndexPerAttributieCategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' and 1=2
ORDER BY AttributieCategorien.Afdrukvolgorde
)
UNION
(
SELECT
BeleggingssectorPerFonds.AttributieCategorie,
Indices.Beursindex as Fonds,
AttributieCategorien.Omschrijving as catOmschrijving,
Fondsen.Omschrijving,
2 as tweedeVolgorde,
IFNULL(AttributieCategorien.Afdrukvolgorde,127) as hoofdVolgorde
FROM
Indices
JOIN Fondsen ON Indices.Beursindex=Fondsen.Fonds
INNER JOIN BeleggingssectorPerFonds ON Indices.Vermogensbeheerder = BeleggingssectorPerFonds.Vermogensbeheerder AND Indices.Beursindex = BeleggingssectorPerFonds.Fonds
LEFT JOIN AttributieCategorien ON BeleggingssectorPerFonds.AttributieCategorie=AttributieCategorien.AttributieCategorie
WHERE
Indices.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY AttributieCategorien.Afdrukvolgorde,Indices.Afdrukvolgorde
)
ORDER BY hoofdVolgorde,tweedeVolgorde";
*/
$query="SELECT
BeleggingssectorPerFonds.AttributieCategorie,
Indices.Beursindex as Fonds,
AttributieCategorien.Omschrijving as catOmschrijving,
Fondsen.Omschrijving,
2 as tweedeVolgorde,
IFNULL(AttributieCategorien.Afdrukvolgorde,127) as hoofdVolgorde
FROM
Indices
JOIN Fondsen ON Indices.Beursindex=Fondsen.Fonds
INNER JOIN BeleggingssectorPerFonds ON Indices.Vermogensbeheerder = BeleggingssectorPerFonds.Vermogensbeheerder AND Indices.Beursindex = BeleggingssectorPerFonds.Fonds
LEFT JOIN AttributieCategorien ON BeleggingssectorPerFonds.AttributieCategorie=AttributieCategorien.AttributieCategorie
WHERE
Indices.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY hoofdVolgorde,Indices.Afdrukvolgorde,Fonds";
		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][]=$index['Fonds'];

		 	$indexData[$index['Fonds']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Fonds']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Fonds'],$datum);
      }
      
     	$indexData[$index['Fonds']]['performanceJaar'] = $this->fondsPerformance($index['Fonds'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Fonds']]['performance'] =    $this->fondsPerformance($index['Fonds'],$perioden['begin'],$perioden['eind']);
 		}


		if(count($benchmarkCategorie) >0)
		{

			$query="SELECT
	IndexPerBeleggingscategorie.Categorie AS AttributieCategorie
FROM
	IndexPerBeleggingscategorie
WHERE
IndexPerBeleggingscategorie.Categoriesoort='AttributieCategorien' AND 
IndexPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND IndexPerBeleggingscategorie.Portefeuille='" . $this->portefeuille . "'
 AND IndexPerBeleggingscategorie.vanaf < '".$this->rapportageDatum."'";
			$DB->SQL($query); //echo $query;exit;
			$DB->Query();
			$attributieCategorieen=array();
			while ($index = $DB->nextRecord())
			{
				$attributieCategorieen[]=$index['AttributieCategorie'];
			}

			foreach($attributieCategorieen as $attributieCategorie)
			{
		  	$query = "SELECT
IndexPerBeleggingscategorie.Categorie as AttributieCategorie,
IndexPerBeleggingscategorie.Fonds,
AttributieCategorien.Omschrijving as catOmschrijving,
Fondsen.Omschrijving,
3 as tweedeVolgorde,
IFNULL(AttributieCategorien.Afdrukvolgorde,127) as hoofdVolgorde
FROM 
IndexPerBeleggingscategorie
JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds
LEFT JOIN AttributieCategorien ON IndexPerBeleggingscategorie.Categorie=AttributieCategorien.AttributieCategorie
WHERE 
IndexPerBeleggingscategorie.Categoriesoort='AttributieCategorien' AND IndexPerBeleggingscategorie.Categorie='".mysql_real_escape_string($attributieCategorie)."' AND 
IndexPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND IndexPerBeleggingscategorie.Portefeuille='" . $this->portefeuille . "'
 AND IndexPerBeleggingscategorie.vanaf < '".$this->rapportageDatum."' ORDER BY vanaf desc limit 1 ";
		  	$DB->SQL($query); //echo $query;exit;
			  $DB->Query();
		  	while ($index = $DB->nextRecord())
		  	{
			  	if ($index['catOmschrijving'] == '')
					  $index['catOmschrijving'] = 'Overige';

          if(!in_array($index['Fonds'],$benchmarkCategorie[$index['catOmschrijving']]))
				    $benchmarkCategorie[$index['catOmschrijving']][] = $index['Fonds'];

			  	$indexData[$index['Fonds']] = $index;
			  	foreach ($perioden as $periode => $datum)
				  	$indexData[$index['Fonds']]['fondsKoers_' . $periode] = $this->getFondsKoers($index['Fonds'], $datum);

			  	$indexData[$index['Fonds']]['performanceJaar'] = $this->fondsPerformance($index['Fonds'], $perioden['jan'], $perioden['eind']);
			  	$indexData[$index['Fonds']]['performance'] = $this->fondsPerformance($index['Fonds'], $perioden['begin'], $perioden['eind']);
		  	}
			}
		}

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(40);
  	$this->pdf->SetWidths(array(10,60,33,33,33,33,33,46));
  	$this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	//$this->pdf->CellBorders = array('','U','U','U','U','U','U');
  	//$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths),8,'F',null,array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']));
    $this->pdf->SetDrawColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
	  $this->pdf->Line($this->pdf->marge,$this->pdf->GetY()+8,297-$this->pdf->marge,$this->pdf->GetY()+8);
    $this->pdf->SetDrawColor(0);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    if($perioden['jan']==$perioden['begin'])
  	{
  	 
  	  //$this->pdf->CellBorders = array('','U','U','U','U');
  	  $this->pdf->row(array("","\n".vertaalTekst("Index",$this->pdf->rapport_taal),
                                 vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['begin'])),
                                 vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['eind'])),
                                 vertaalTekst("Rendement verslagperiode in %",$this->pdf->rapport_taal)));
  	}
  	else
  	{
  	  //$this->pdf->CellBorders = array('','U','U','U','U','U','U');
  	  $this->pdf->row(array("","\n".vertaalTekst("Index",$this->pdf->rapport_taal),
                                 vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['jan'])),
                                 vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['begin'])),
                                 vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['eind'])),
                                 vertaalTekst("Rendement verslagperiode in %",$this->pdf->rapport_taal),
                                 vertaalTekst("Rendement vanaf",$this->pdf->rapport_taal).' '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' '.vertaalTekst("in %",$this->pdf->rapport_taal)));
  	}
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);


  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->underline=1;
  	  $this->pdf->row(array("",vertaalTekst($categorie,$this->pdf->rapport_taal)));
      $this->pdf->underline=0;
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
      $this->pdf->Ln();
  	}
	}
}
?>