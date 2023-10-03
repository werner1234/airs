<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/05 16:42:29 $
File Versie					: $Revision: 1.17 $

$Log: RapportINDEX_L35.php,v $
Revision 1.17  2019/07/05 16:42:29  rvv
*** empty log message ***

Revision 1.16  2019/01/26 19:33:28  rvv
*** empty log message ***

Revision 1.15  2018/09/29 16:19:30  rvv
*** empty log message ***

Revision 1.14  2018/09/27 08:00:22  rvv
*** empty log message ***

Revision 1.13  2018/09/27 06:53:41  rvv
*** empty log message ***

Revision 1.12  2018/09/26 15:53:28  rvv
*** empty log message ***

Revision 1.11  2018/06/24 11:13:16  rvv
*** empty log message ***

Revision 1.10  2018/06/23 14:21:39  rvv
*** empty log message ***

Revision 1.9  2018/01/13 19:10:29  rvv
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
include_once("rapport/include/ATTberekening_L35.php");

class RapportIndex_L35
{
	function RapportIndex_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
  $att=new ATTberekening_L35($this);
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

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(40);
  	$this->pdf->SetWidths(array(5,40,70,33,33,33,33,33));
  	$this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	$this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	if($perioden['jan']==$perioden['begin'])
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U','U');
  	  $this->pdf->row(array("","\n".vertaalTekst("Categorie",$this->pdf->rapport_taal),"\n".vertaalTekst("Index",$this->pdf->rapport_taal),vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['begin'])),
												vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['eind'])),vertaalTekst('Rendement verslagperiode in %',$this->pdf->rapport_taal)));
      $this->pdf->excelData[]=array('Categorie','Index','Koers '.date("d-m-Y",db2jul($perioden['begin'])),'Koers '.date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %');
  	}
  	else
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
  	  $this->pdf->row(array("","\n".vertaalTekst("Categorie",$this->pdf->rapport_taal),"\n".vertaalTekst("Index",$this->pdf->rapport_taal),vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['jan'])),
												vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['begin'])),vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['eind'])),
												vertaalTekst('Rendement verslagperiode in %',$this->pdf->rapport_taal),vertaalTekst('Rendement vanaf',$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($this->tweedePerformanceStart)).' '. vertaalTekst('in %',$this->pdf->rapport_taal)));
      $this->pdf->excelData[]=array('Categorie','Index','Koers '.date("d-m-Y",db2jul($perioden['jan'])),'Koers '.date("d-m-Y",db2jul($perioden['begin'])),'Koers '.date("d-m-Y",db2jul($perioden['eind'])),'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %');
  	}
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);



  $benchmarkCategorie=array();
	  $query="SELECT specifiekeIndex as Beursindex,
    Fondsen.Omschrijving,
Fondsen.Valuta,
'Benchmark' as catOmschrijving,
'Totaal' as Hoofdcategorie
 FROM Portefeuilles 
 Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds
 WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
 		$DB->SQL($query);
		$DB->Query();


	  $index = $DB->nextRecord();
		$fondsen[]=$index;

		$query="SELECT benchmarkverdeling.fonds as Beursindex ,benchmarkverdeling.percentage,Fondsen.Omschrijving , ' ' as catOmschrijving, 'Totaal' as Hoofdcategorie
      FROM benchmarkverdeling 
      JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmark='".$index['Beursindex']."'";
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
			$fondsen[$data['Beursindex']]=$data;

		foreach($fondsen as $index)
		{
			if ($index['catOmschrijving'] == '')
			{
				$index['catOmschrijving'] = 'Overige';
			}
			$benchmarkCategorie['Totaal'][$index['catOmschrijving']][$index['Beursindex']] = $index['Beursindex'];

			$indexData[$index['Beursindex']] = $index;
			if ($index['catOmschrijving'] <> 'Benchmark')
			{
				foreach ($perioden as $periode => $datum)
				{
					$indexData[$index['Beursindex']]['fondsKoers_' . $periode] = $this->getFondsKoers($index['Beursindex'], $datum);
				}
			}
			$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'], $perioden['jan'], $perioden['eind'], false, $index);
			$indexData[$index['Beursindex']]['performance'] = $this->getPerformance($index['Beursindex'], $perioden['begin'], $perioden['eind'], false, $index);
		}


$query="SELECT
IndexPerBeleggingscategorie.Beleggingscategorie,
IndexPerBeleggingscategorie.Fonds as Beursindex,
IndexPerBeleggingscategorie.Vermogensbeheerder,
Fondsen.Omschrijving,
Beleggingscategorien.Omschrijving as catOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HCat.Omschrijving as hcatOmschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN CategorienPerHoofdcategorie ON IndexPerBeleggingscategorie.Vermogensbeheerder = CategorienPerHoofdcategorie.Vermogensbeheerder AND IndexPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
INNER JOIN Beleggingscategorien HCat ON CategorienPerHoofdcategorie.Hoofdcategorie = HCat.Beleggingscategorie  
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
(IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille = '".$this->portefeuille."')
ORDER BY HCat.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";
 		$DB->SQL($query);
		$DB->Query();

	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['hcatOmschrijving']][$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

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
Beleggingscategorien.Omschrijving AS catOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HCat.Omschrijving as hcatOmschrijving
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
INNER Join BeleggingscategoriePerFonds ON Indices.Beursindex = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
INNER Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Vermogensbeheerder = CategorienPerHoofdcategorie.Vermogensbeheerder AND BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
INNER JOIN Beleggingscategorien HCat ON CategorienPerHoofdcategorie.Hoofdcategorie = HCat.Beleggingscategorie  
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY HCat.Afdrukvolgorde, Indices.Afdrukvolgorde";


		$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['hcatOmschrijving']][$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
      }
      
     	$indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind']);
			$indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind']);
 		}

//listarray($indexData);
	
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

       $benchmarkCategorie['Vastrentende waarden'][$index['catOmschrijving']][$index['Beursindex']]=$index['Beursindex'];

        $indexData[$index['Beursindex']]=$index;
       foreach ($perioden as $periode=>$datum)
       {
         $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getValutaKoers($index['Beursindex'],$datum);
       }

        $indexData[$index['Beursindex']]['performanceJaar'] = $this->getPerformance($index['Beursindex'],$perioden['jan'],$perioden['eind'],true);
       $indexData[$index['Beursindex']]['performance'] =    $this->getPerformance($index['Beursindex'],$perioden['begin'],$perioden['eind'],true);
      }
//listarray($benchmarkCategorie);listarray($indexData);

  	foreach ($benchmarkCategorie as $hoofdcategorie=>$categorieData)
  	{
			if($hoofdcategorie=='Vastrentende waarden')
			{
				$this->pdf->addPage();
				$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
				$this->pdf->SetY(40);
				$this->pdf->SetWidths(array(5,40,70,33,33,33,33,33));
				$this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R'));
				$this->pdf->ln();
				$this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
				$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
				if($perioden['jan']==$perioden['begin'])
				{
					$this->pdf->CellBorders = array('','U','U','U','U','U');
					$this->pdf->row(array("","\n".vertaalTekst("Categorie",$this->pdf->rapport_taal),"\n".vertaalTekst("Index",$this->pdf->rapport_taal),vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['begin'])),
														vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['eind'])),vertaalTekst('Rendement verslagperiode in %',$this->pdf->rapport_taal)));
				}
				else
				{
					$this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
					$this->pdf->row(array("","\n".vertaalTekst("Categorie",$this->pdf->rapport_taal),"\n".vertaalTekst("Index",$this->pdf->rapport_taal),vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['jan'])),
														vertaalTekst("Koers",$this->pdf->rapport_taal)."\n".date("d-m-Y",db2jul($perioden['begin'])),vertaalTekst("Koers",$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($perioden['eind'])),
														vertaalTekst('Rendement verslagperiode in %',$this->pdf->rapport_taal),vertaalTekst('Rendement vanaf',$this->pdf->rapport_taal)." ".date("d-m-Y",db2jul($this->tweedePerformanceStart)).' '. vertaalTekst('in %',$this->pdf->rapport_taal)));
				}
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				unset($this->pdf->CellBorders);
				
			}
			if($hoofdcategorie<>'Totaal')
			{
				$this->pdf->ln();
				$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
				$this->pdf->row(array("", vertaalTekst($hoofdcategorie, $this->pdf->rapport_taal)));
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			}
			foreach($categorieData as $categorie=>$fondsen)
			{
				$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
				$this->pdf->row(array("", vertaalTekst($categorie, $this->pdf->rapport_taal)));
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
				$this->pdf->Ln(-4);
				
				if($categorie=='Valuta')
				  $decimalen=4;
				else
			  	$decimalen=2;

				foreach ($fondsen as $fonds)
				{
					if ($categorie == 'Benchmark')
					{
						$beginKoersTxt = '';
						$eindKoersTxt = '';
					}
					else
					{
						$beginKoersTxt = $this->formatGetal($indexData[$fonds]['fondsKoers_begin'], $decimalen);
						$eindKoersTxt = $this->formatGetal($indexData[$fonds]['fondsKoers_eind'], $decimalen);
					}
					$fondsData = $indexData[$fonds];


					if ($perioden['jan'] == $perioden['begin'])
					{
						$this->pdf->row(array('', '', $fondsData['Omschrijving'],
															$beginKoersTxt,
															$eindKoersTxt,
															$this->formatGetal($fondsData['performance'], 2)));
            $this->pdf->excelData[]=array($categorie, $fondsData['Omschrijving'],round($indexData[$fonds]['fondsKoers_begin'],2),round($indexData[$fonds]['fondsKoers_eind'],2),round($fondsData['performance'], 2));
					}
					else
					{
						$this->pdf->row(array('', '', $fondsData['Omschrijving'],
															$this->formatGetal($indexData[$fonds]['fondsKoers_jan'], $decimalen),
															$beginKoersTxt,
															$eindKoersTxt,
															$this->formatGetal($fondsData['performance'], 2), $this->formatGetal($fondsData['performanceJaar'], 2)));
            $this->pdf->excelData[]=array($categorie, $fondsData['Omschrijving'],round($indexData[$fonds]['fondsKoers_jan'],2),round($indexData[$fonds]['fondsKoers_begin'],2),round($indexData[$fonds]['fondsKoers_eind'],2),round($fondsData['performance'], 2),round($fondsData['performanceJaar'], 2));
					}

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