<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/12/27 15:11:17 $
File Versie					: $Revision: 1.1 $

$Log: RapportINDEX_L81.php,v $
Revision 1.1  2018/12/27 15:11:17  rvv
*** empty log message ***



*/


include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L35.php");

class RapportIndex_L81
{
	function RapportIndex_L81($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(40);
  	$this->pdf->SetWidths(array(10,40,70,25,25,25,25,25));
  	$this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	$this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $tmpRow=array("","\nCategorie","\nIndex");
    foreach($perioden as $periodeText=>$periodeData)
      $tmpRow[]="\n".$periodeText;

	  $this->pdf->row($tmpRow);
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
      foreach($perioden as $periodeText=>$periodeData)
	  	  $indexData[$index['Beursindex']][$periodeText]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop']);
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
      foreach($perioden as $periodeText=>$periodeData)
	  	  $indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop']);
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
      foreach($perioden as $periodeText=>$periodeData)
        $indexData[$index['Beursindex']][$periodeText]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop']);
   	}

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
      foreach($perioden as $periodeText=>$periodeData)
        $indexData[$index['Beursindex']][$periodeText]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop'],true);

 		}   
  
    
  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	  $this->pdf->row(array("",$categorie));
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Ln(-4);
  	  foreach ($fondsen as $fonds)
  	  {
  	    $fondsData=$indexData[$fonds];
        $tmpRow=array("","",$fondsData['Omschrijving'],);
        foreach($perioden as $periodeText=>$periodeData)
          $tmpRow[]=$this->formatGetal($fondsData[$periodeText]['performance'],2);
        $this->pdf->row($tmpRow);
        
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