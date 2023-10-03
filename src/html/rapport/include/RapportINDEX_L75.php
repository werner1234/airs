<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/16 15:57:02 $
File Versie					: $Revision: 1.5 $

$Log: RapportINDEX_L75.php,v $
Revision 1.5  2020/05/16 15:57:02  rvv
*** empty log message ***

Revision 1.4  2019/09/23 04:20:54  rvv
*** empty log message ***

Revision 1.3  2019/09/21 16:31:25  rvv
*** empty log message ***

Revision 1.2  2019/09/07 16:07:48  rvv
*** empty log message ***

Revision 1.1  2019/07/20 16:28:44  rvv
*** empty log message ***

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

class RapportINDEX_L75
{
	function RapportINDEX_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_FRONT_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Benchmark en indices";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->tweedePerformanceStart = $rapportageDatumVanaf;
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
  
function getPerformance($fonds,$vanaf,$tot,$valuta=false,$totaal=false)
{
 
  if($valuta==false)
  {
  //  echo "$fonds <br>\n";
    if($totaal==true)
      return getFondsPerformanceGestappeld2($fonds,$this->portefeuille,$vanaf, $tot,'maanden',false,true,false,'benchmarkTot');
    else
      return getFondsPerformanceGestappeld($fonds,$this->portefeuille,$vanaf, $tot,'maanden',true);
  }
  else
  {
    $beginkoers=$this->getValutaKoers($fonds,$vanaf);
    $eindkoers=$this->getValutaKoers($fonds,$tot);
    return ($eindkoers - $beginkoers) / ($beginkoers/100 );
  }
  /*  */
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
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

    $perioden=array();
    
    $huidigeKwartaal=ceil(date('n',$this->rapportageDatumJul)/3);
    if($huidigeKwartaal==2)
      $vorigeDag=1;
    else
      $vorigeDag=0;
    $vorigeKwartaal=$huidigeKwartaal-1;
    if($vorigeKwartaal==0)
      $vorigeKwartaal=4;
    $vorigeKwartaalPeriode = array('start'=>date('Y-m-d',mktime(0,0,0,($huidigeKwartaal-2)*3+1,$vorigeDag,substr($this->rapportageDatum,0,4))),
                                   'stop'=>date('Y-m-d',mktime(0,0,0,($huidigeKwartaal-2)*3+4,0,substr($this->rapportageDatum,0,4))));
    
    $perioden['Q'.$vorigeKwartaal]=$vorigeKwartaalPeriode;
    $perioden['YTD']=array('start'=>$this->tweedePerformanceStart,'stop'=>$this->rapportageDatum);
    $perioden['1 '.vertaalTekst('jaar',$this->pdf->rapport_taal)]=array('start'=>(substr($this->rapportageDatum,0,4)-1).substr($this->rapportageDatum,4,6),'stop'=>$this->rapportageDatum);
    $perioden['3 '.vertaalTekst('jaar',$this->pdf->rapport_taal)]=array('start'=>(substr($this->rapportageDatum,0,4)-3).substr($this->rapportageDatum,4,6),'stop'=>$this->rapportageDatum);
    $perioden['5 '.vertaalTekst('jaar',$this->pdf->rapport_taal)]=array('start'=>(substr($this->rapportageDatum,0,4)-5).substr($this->rapportageDatum,4,6),'stop'=>$this->rapportageDatum);

	  $DB=new DB();
//	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    

		$this->pdf->SetY(20);
  	$this->pdf->SetWidths(array(10,80,25,25,25,25,25,25));
  	$this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R'));
    
    $this->pdf->row(array('',vertaalTekst('Risico-profiel',$this->pdf->rapport_taal).': '.vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $indexData=array();
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
        $index['catOmschrijving']=vertaalTekst('Overige',$this->pdf->rapport_taal);
      
      $indexData[$index['Beursindex']]=$index;
      foreach($perioden as $periodeText=>$periodeData)
        $indexData[$index['Beursindex']][$periodeText]['performance'] = $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop'],false,true);
    }
    
  
    
   // $this->pdf->row(array('','Gehanteerde benchmarks met rendementen'));
 	 // $this->pdf->ln();
  	$this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $tmpRow=array("",vertaalTekst($indexData[$this->pdf->portefeuilledata['SpecifiekeIndex']]['Omschrijving'],$this->pdf->rapport_taal),vertaalTekst("Weging",$this->pdf->rapport_taal));
    foreach($perioden as $periodeText=>$periodeData)
      $tmpRow[]="".$periodeText;

	  $this->pdf->row($tmpRow);
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);

  	$query="SELECT
benchmarkverdeling.benchmark,
benchmarkverdeling.fonds,
benchmarkverdeling.percentage,
BeleggingscategoriePerFonds.Vermogensbeheerder,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as BeleggingscategorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
Fondsen.Omschrijving
FROM
benchmarkverdeling
INNER JOIN BeleggingscategoriePerFonds ON benchmarkverdeling.fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
INNER JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
WHERE
benchmarkverdeling.benchmark='".mysql_real_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."'
ORDER BY Beleggingscategorien.Afdrukvolgorde,benchmarkverdeling.fonds";
    $DB->SQL($query);
    $DB->Query();
    
    $specifiekeIndexVerdeling=array();
    $categoriePercentages=array();
    while($index = $DB->nextRecord())
    {
      $specifiekeIndexVerdeling[]=$index;
      $categoriePercentages[$index['BeleggingscategorieOmschrijving']]+=$index['percentage'];
    }
    
    $lastCategorie='';
    foreach($specifiekeIndexVerdeling as $index)
    {
      if($index['BeleggingscategorieOmschrijving']<>$lastCategorie)
      {
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->row(array("", vertaalTekst($index['BeleggingscategorieOmschrijving'] ,$this->pdf->rapport_taal),$this->formatGetal($categoriePercentages[$index['BeleggingscategorieOmschrijving']],2).'%'));
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      }
      $lastCategorie=$index['BeleggingscategorieOmschrijving'];
  
      $tmpRow=array("",$index['Omschrijving'],$this->formatGetal($index['percentage'],2).'%');
      foreach($perioden as $periodeText=>$periodeData)
        $tmpRow[]=$this->formatGetal($this->getPerformance($index['fonds'],$periodeData['start'],$periodeData['stop'],false),2);
      $this->pdf->row($tmpRow);
    }
    
    $specifiekeIndexData=$indexData[$this->pdf->portefeuilledata['SpecifiekeIndex']];
    $tmpRow=array("",vertaalTekst($specifiekeIndexData['Omschrijving'],$this->pdf->rapport_taal),$this->formatGetal(100,2).'%');
    foreach($perioden as $periodeText=>$periodeData)
    {
      $tmpRow[] = $this->formatGetal($specifiekeIndexData[$periodeText]['performance'], 2);
    }
    $this->pdf->ln();
    $this->pdf->CellFontStyle[2]=array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellFontStyle[3]=array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row($tmpRow);
    unset($this->pdf->CellFontStyle);
    
    
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
    
    $tmpRow=array("",vertaalTekst("Overige indices en rendementen",$this->pdf->rapport_taal),"");
    foreach($perioden as $periodeText=>$periodeData)
      $tmpRow[]="".$periodeText;
    
    $this->pdf->row($tmpRow);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
  $benchmarkCategorie=array();


/*
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
        $index['catOmschrijving']=vertaalTekst('Overige',$this->pdf->rapport_taal);
		  $benchmarkCategorie[$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];
      $indexData[$index['Beursindex']]=$index;
      foreach($perioden as $periodeText=>$periodeData)
	  	  $indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$periodeData['start'],$periodeData['stop']);
 		}
*/
  
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
        $index['catOmschrijving']=vertaalTekst('Overige',$this->pdf->rapport_taal);

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
  	  $this->pdf->row(array("",vertaalTekst($categorie ,$this->pdf->rapport_taal)));
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      //$this->pdf->Ln(-4);
  	  foreach ($fondsen as $fonds)
  	  {
  	    $fondsData=$indexData[$fonds];
        $tmpRow=array("",vertaalTekst($fondsData['Omschrijving'],$this->pdf->rapport_taal),'',);
        foreach($perioden as $periodeText=>$periodeData)
          $tmpRow[]=$this->formatGetal($fondsData[$periodeText]['performance'],2);
        $this->pdf->row($tmpRow);
        
  	  }
  
  	}

	}
}
?>