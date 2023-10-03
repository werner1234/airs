<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/07/18 15:45:00 $
File Versie					: $Revision: 1.6 $

$Log: RapportINDEX_L76.php,v $
Revision 1.6  2018/07/18 15:45:00  rvv
*** empty log message ***

Revision 1.5  2018/07/16 05:24:11  rvv
*** empty log message ***

Revision 1.4  2018/07/15 06:49:34  rvv
*** empty log message ***

Revision 1.3  2018/07/14 14:04:37  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L49.php");


class RapportINDEX_L76
{
	function RapportINDEX_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Weging en ontwikkeling benchmarks";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		$RapStopJaar = date("Y", db2jul($this->rapportageDatum));

		$this->tweedeStart();
		$this->categorieKleuren=array();

	//	$this->rapportageDatumVanaf = "$RapStartJaar-01-01";

		if ($RapStartJaar != $RapStopJaar)
		{
			echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
			exit;
		}
	}

	function tweedeStart()
	{
		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
		{
			$this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
		}
		else
		{
			$this->tweedePerformanceStart = "$RapStartJaar-01-01";
			if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
			{
				$fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
				vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
				$this->extraVulling = true;
			}
		}
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
		if ($start == false)
			$waarde = $waarde / $this->pdf->ValutaKoersEind;
		else
			$waarde = $waarde / $this->pdf->ValutaKoersBegin;

		return number_format($waarde,$dec,",",".");
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}



	function writeRapport()
	{
		global $__appvar;

		//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
		$this->categorieKleuren=$allekleuren['OIB'];
		// $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');

		$q="SELECT beleggingscategorie ,omschrijving FROM Beleggingscategorien";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
			$this->categorieOmschrijving[$data['beleggingscategorie']]=$data['omschrijving'];

//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


		$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();


		$this->pdf->fillCell = array();

		$this->maakIndex();

		$this->pdf->fillCell = array();

	}




	function formatGetalLength ($getal,$decimaal,$gewensteLengte)
	{
		$lengte = strlen(round($getal));
		if($getal < 0)
			$lengte --;
		$mogelijkeDecimalen = $gewensteLengte - $lengte;
		if($lengte >$gewensteLengte)
			$decimaal = 0;
		elseif ($decimaal > $mogelijkeDecimalen)
			$decimaal = $mogelijkeDecimalen;
		return number_format($getal,$decimaal,',','');
	}





	function maakIndex()
	{
		$db=new DB();

		$indexFondSize=$this->pdf->rapport_fontsize;
		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		$this->tweedePerformanceStart = "$RapStartJaar-01-01";
		/*
		if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
			$this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
		elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
			$this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
		else
			$this->tweedePerformanceStart = "$RapStartJaar-01-01";


		if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul(($RapStartJaar-1)."-01-01"))
			$vorigJaar = substr($this->pdf->PortefeuilleStartdatum,0,10);
		else
		*/
			$vorigJaar= ($RapStartJaar-1)."-01-01";



		$DB=new DB();
		$perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum,'vorig'=>$vorigJaar,'pstart'=>substr($this->pdf->PortefeuilleStartdatum,0,10));

		$query="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
		$db->SQL($query);
		$verm=$db->lookupRecord();

		$indices=array();

		$query="SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving,Fondsen.Valuta
    FROM Portefeuilles
    INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
    WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		{
			$data['type']='Benchmark';
			if($data['SpecifiekeIndex'] <> '')
				$indices[$data['SpecifiekeIndex']]=$data;
		}

		$query="SELECT Fondsen.Omschrijving,Fondsen.Omschrijving,Fondsen.Valuta, IndexPerBeleggingscategorie.Fonds ,IndexPerBeleggingscategorie.Beleggingscategorie,
Beleggingscategorien.Omschrijving as `type`
    FROM IndexPerBeleggingscategorie
    INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
    JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
    WHERE 
    IndexPerBeleggingscategorie.Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND ( IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' ) 
    AND IndexPerBeleggingscategorie.vanaf < '".$this->rapportageDatum."'
    ORDER BY IndexPerBeleggingscategorie.Beleggingscategorie ";
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		{
				$indices[$data['Fonds']]=$data;
		}

		//echo $this->pdf->witCell;exit;
/*
		$this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
		$this->pdf->setY(30);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->Cell(150,4,'Weging en ontwikkeling benchmarks', 0, "L");
*/
		$this->pdf->SetWidths(array(75,20,30,30,30,30,30));
		$this->pdf->SetAligns(array('L','R','R','R','R','R','R'));
		$tmp=array_sum($this->pdf->widths);
		$this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
		$this->pdf->Ln(11);


		$indexData=array();
		foreach($indices as $hoofdIndex=>$hoofdIndexData)
		{

			foreach ($perioden as $periode=>$datum)
			{
				$indexData[$hoofdIndex]['fondsKoers_'.$periode]=getFondsKoers($hoofdIndex,$datum);
				//  $indexData[$hoofdIndex]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
			}
			$indexData[$hoofdIndex]['performanceJaar'] = ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_jan'])    / ($indexData[$hoofdIndex]['fondsKoers_jan']/100 );
			$indexData[$hoofdIndex]['performance'] =     ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_begin']) / ($indexData[$hoofdIndex]['fondsKoers_begin']/100 );
			$indexData[$hoofdIndex]['performanceVorig'] =     ($indexData[$hoofdIndex]['fondsKoers_jan'] - $indexData[$hoofdIndex]['fondsKoers_vorig']) / ($indexData[$hoofdIndex]['fondsKoers_vorig']/100 );
			$indexData[$hoofdIndex]['performancePstart'] =     ($indexData[$hoofdIndex]['fondsKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_pstart']) / ($indexData[$hoofdIndex]['fondsKoers_pstart']/100 );
			//$indexData[$hoofdIndex]['performanceEurJaar'] = ($indexData[$hoofdIndex]['fondsKoers_eind']*$indexData[$hoofdIndex]['valutaKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_jan']  *$indexData[$hoofdIndex]['valutaKoers_jan'])/(  $indexData[$hoofdIndex]['fondsKoers_jan']*  $indexData[$hoofdIndex]['valutaKoers_jan']/100 );
			//$indexData[$hoofdIndex]['performanceEur'] =     ($indexData[$hoofdIndex]['fondsKoers_eind']*$indexData[$hoofdIndex]['valutaKoers_eind'] - $indexData[$hoofdIndex]['fondsKoers_begin']*$indexData[$hoofdIndex]['valutaKoers_begin'])/($indexData[$hoofdIndex]['fondsKoers_begin']*$indexData[$hoofdIndex]['valutaKoers_begin']/100 );
			$this->pdf->SetFont($this->pdf->rapport_font,"B",$indexFondSize);
			$this->pdf->row(array($hoofdIndexData['type'],'','Koers per '.date('d-m-Y',db2jul($perioden['eind'])),'Rendement verslagperiode','Rendement ytd','Rendement '.($RapStartJaar-1),'Rendement vanaf '.date('d-m-Y',db2jul($perioden['pstart']))));
			$this->pdf->SetFont($this->pdf->rapport_font,"",$indexFondSize);
			$this->pdf->excelData[]=array(array($hoofdIndexData['type'],'','Koers per '.date('d-m-Y',db2jul($perioden['eind'])),'Rendement verslagperiode','Rendement ytd','Rendement '.($RapStartJaar-1),'Rendement vanaf '.date('d-m-Y',db2jul($perioden['pstart']))));;

			$this->pdf->row(array($hoofdIndexData['Omschrijving'].',','',
												$this->formatGetal($indexData[$hoofdIndex]['fondsKoers_eind'],2),
												$this->formatGetal($indexData[$hoofdIndex]['performance'],1).'%',
												$this->formatGetal($indexData[$hoofdIndex]['performanceJaar'],1).'%',
												$this->formatGetal($indexData[$hoofdIndex]['performanceVorig'],1).'%',
												$this->formatGetal($indexData[$hoofdIndex]['performancePstart'],1).'%'));
			$this->pdf->excelData[]=array($hoofdIndexData['Omschrijving'].',','',
				round($indexData[$hoofdIndex]['fondsKoers_eind'],2),
				round($indexData[$hoofdIndex]['performance'],1),
				round($indexData[$hoofdIndex]['performanceJaar'],1),
				round($indexData[$hoofdIndex]['performanceVorig'],1),
				round($indexData[$hoofdIndex]['performancePstart'],1));
			$this->pdf->row(array('    bestaande uit:','Weging','','','',''));
			//$this->pdf->row(array('','','Weging','','','',''));
			//$n=$this->switchColor($n);
			$query="SELECT benchmarkverdeling.fonds, benchmarkverdeling.percentage, Fondsen.Omschrijving,Fondsen.Valuta
      FROM benchmarkverdeling 
      INNER JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmarkverdeling.benchmark='".$hoofdIndex."'";
			$db->SQL($query);
			$db->Query();
			while($data=$db->nextRecord())
			{
				foreach ($perioden as $periode=>$datum)
				{
					$indexData[$data['fonds']]['fondsKoers_'.$periode]=getFondsKoers($data['fonds'],$datum);
					//$indexData[$data['fonds']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
				}
				$indexData[$data['fonds']]['performanceJaar'] = ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_jan'])    / ($indexData[$data['fonds']]['fondsKoers_jan']/100 );
				$indexData[$data['fonds']]['performance'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_begin']) / ($indexData[$data['fonds']]['fondsKoers_begin']/100 );
				$indexData[$data['fonds']]['performanceVorig'] =     ($indexData[$data['fonds']]['fondsKoers_jan'] - $indexData[$data['fonds']]['fondsKoers_vorig']) / ($indexData[$data['fonds']]['fondsKoers_vorig']/100 );
				$indexData[$data['fonds']]['performancePstart'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_pstart']) / ($indexData[$data['fonds']]['fondsKoers_pstart']/100 );

				//  listarray($data);
				$this->pdf->row(array('    '.$data['Omschrijving'],
													$this->formatGetal($data['percentage'],1),
													$this->formatGetal($indexData[$data['fonds']]['fondsKoers_eind'],2),
													$this->formatGetal($indexData[$data['fonds']]['performance'],1).'%',
													$this->formatGetal($indexData[$data['fonds']]['performanceJaar'],1).'%',
													$this->formatGetal($indexData[$data['fonds']]['performanceVorig'],1).'%',
													$this->formatGetal($indexData[$data['fonds']]['performancePstart'],1).'%'));
				$this->pdf->excelData[]=array('    '.$data['Omschrijving'],
					round($data['percentage'],1),
					round($indexData[$data['fonds']]['fondsKoers_eind'],2),
					round($indexData[$data['fonds']]['performance'],1),
					round($indexData[$data['fonds']]['performanceJaar'],1),
					round($indexData[$data['fonds']]['performanceVorig'],1),
					round($indexData[$data['fonds']]['performancePstart'],1));

			}
		}



		$query="SELECT
IndexPerBeleggingscategorie.Fonds as fonds,
IndexPerBeleggingscategorie.Categoriesoort,
IndexPerBeleggingscategorie.Categorie,
Fondsen.Omschrijving,
Beleggingscategorien.Omschrijving  as categorieOmschrijving
FROM
IndexPerBeleggingscategorie
INNER JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
INNER JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Categorie = Beleggingscategorien.Beleggingscategorie
WHERE 
IndexPerBeleggingscategorie.Vermogensbeheerder='".$verm['Vermogensbeheerder']."' AND IndexPerBeleggingscategorie.Categoriesoort='Beleggingscategorien'
ORDER BY Categorie";
		$db->SQL($query);
		$db->Query();
		$lastCategorie='';

		if($db->records())
		{
			$this->pdf->SetFont($this->pdf->rapport_font, "B", $indexFondSize);
			$this->pdf->row(array($hoofdIndexData['type'], '', 'Koers per ' . date('d-m-Y', db2jul($perioden['eind'])), 'Rendement verslagperiode', 'Rendement ytd', 'Rendement ' . ($RapStartJaar-1), 'Rendement vanaf ' . date('d-m-Y', db2jul($perioden['pstart']))));
			$this->pdf->excelData[]=array($hoofdIndexData['type'],'','Koers per '.date('d-m-Y',db2jul($perioden['eind'])),'Rendement verslagperiode','Rendement ytd','Rendement '.($RapStartJaar-1),'Rendement vanaf '.date('d-m-Y',db2jul($perioden['pstart'])));

			$this->pdf->SetFont($this->pdf->rapport_font, "", $indexFondSize);
		}
		while($data=$db->nextRecord())
		{

			if($data['Categorie'] <> $lastCategorie)
			{
				$this->pdf->SetFont($this->pdf->rapport_font, "B", $indexFondSize);
				$this->pdf->SetFillColor($this->pdf->achtergrondKop[0], $this->pdf->achtergrondKop[1], $this->pdf->achtergrondKop[2]);
				$this->pdf->row(array($data['categorieOmschrijving'], '', '', '', '', ''));
				$this->pdf->excelData[]=array($data['categorieOmschrijving']);
				$this->pdf->SetFont($this->pdf->rapport_font, "BI", $indexFondSize);
				//    $this->pdf->row(array('Benchmark', '', '', '', '% Periode', '', '% YtD', '', 'vorig J', '', 'va start'));
				$this->pdf->SetFont($this->pdf->rapport_font, "", $indexFondSize);
			}

			foreach ($perioden as $periode=>$datum)
			{
				$indexData[$data['fonds']]['fondsKoers_'.$periode]=getFondsKoers($data['fonds'],$datum);
				//echo $indexData[$data['fonds']]['fondsKoers_'.$periode]."=getFondsKoers(".$data['fonds'].",$datum); <br>\n";
			}
			$indexData[$data['fonds']]['performanceJaar'] = ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_jan'])    / ($indexData[$data['fonds']]['fondsKoers_jan']/100 );
			$indexData[$data['fonds']]['performance'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_begin']) / ($indexData[$data['fonds']]['fondsKoers_begin']/100 );
			$indexData[$data['fonds']]['performanceVorig'] =     ($indexData[$data['fonds']]['fondsKoers_jan'] - $indexData[$data['fonds']]['fondsKoers_vorig']) / ($indexData[$data['fonds']]['fondsKoers_vorig']/100 );
			$indexData[$data['fonds']]['performancePstart'] =     ($indexData[$data['fonds']]['fondsKoers_eind'] - $indexData[$data['fonds']]['fondsKoers_pstart']) / ($indexData[$data['fonds']]['fondsKoers_pstart']/100 );

			//  listarray($data);
			$this->pdf->row(array('    '.$data['Omschrijving'],
												'',
												$this->formatGetal($indexData[$data['fonds']]['fondsKoers_eind'],2),
												$this->formatGetal($indexData[$data['fonds']]['performance'],1).'%',
												$this->formatGetal($indexData[$data['fonds']]['performanceJaar'],1).'%',
												$this->formatGetal($indexData[$data['fonds']]['performanceVorig'],1).'%',
												$this->formatGetal($indexData[$data['fonds']]['performancePstart'],1).'%'));
			$this->pdf->excelData[]=array('    '.$data['Omschrijving'],
				'',
				round($indexData[$data['fonds']]['fondsKoers_eind'],2),
				round($indexData[$data['fonds']]['performance'],1),
				round($indexData[$data['fonds']]['performanceJaar'],1),
				round($indexData[$data['fonds']]['performanceVorig'],1),
				round($indexData[$data['fonds']]['performancePstart'],1));
			$lastCategorie=$data['Categorie'];
		}



		$this->pdf->ln();

		$valutas=array();
		/*
		$query="SELECT
Rekeningmutaties.Valuta
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille=".$this->portefeuille." AND Rekeningmutaties.Boekdatum>='".$RapStartJaar."-01-01' AND Rekeningmutaties.Valuta <> 'EUR'
GROUP BY Rekeningmutaties.Valuta";
		*/
		global $__appvar;
					$query = "SELECT Valuta ".
						"FROM TijdelijkeRapportage WHERE ".
						" rapportageDatum ='".$this->rapportageDatum."' AND ".
						" portefeuille = '".$this->portefeuille."' "
						.$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY Valuta";
		debugSpecial($query,__FILE__,__LINE__);
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		{
			$valutas[]=$data['Valuta'];
		}

		if(count($valutas)>0)
		{
			$this->pdf->excelData[]=array();
  		$this->pdf->SetFont($this->pdf->rapport_font, "B", $indexFondSize);
	  	//$this->pdf->row(array('Valuta','','Koers','% Periode','% YtD',substr($perioden['vorig'],0,4),'% LtD'));
		  $this->pdf->row(array('Valuta','','Koers per '.date('d-m-Y',db2jul($perioden['eind'])),'Rendement verslagperiode','Rendement ytd','Rendement '.($RapStartJaar-1),'Rendement vanaf '.date('d-m-Y',db2jul($perioden['pstart']))));
		  $this->pdf->excelData[]=array('Valuta','','Koers per '.date('d-m-Y',db2jul($perioden['eind'])),'Rendement verslagperiode','Rendement ytd','Rendement '.($RapStartJaar-1),'Rendement vanaf '.date('d-m-Y',db2jul($perioden['pstart'])));
		}

		$this->pdf->SetFont($this->pdf->rapport_font, "", $indexFondSize);
		foreach($valutas as $valuta)
		{
			foreach ($perioden as $periode=>$datum)
			{
				$indexData[$valuta]['fondsKoers_'.$periode]=getValutaKoers($valuta,$datum);
			}
			$indexData[$valuta]['performanceJaar'] = ($indexData[$valuta]['fondsKoers_eind'] - $indexData[$valuta]['fondsKoers_jan'])    / ($indexData[$valuta]['fondsKoers_jan']/100 );
			$indexData[$valuta]['performance'] =     ($indexData[$valuta]['fondsKoers_eind'] - $indexData[$valuta]['fondsKoers_begin']) / ($indexData[$valuta]['fondsKoers_begin']/100 );
			$indexData[$valuta]['performanceVorig'] =     ($indexData[$valuta]['fondsKoers_jan'] - $indexData[$valuta]['fondsKoers_vorig']) / ($indexData[$valuta]['fondsKoers_vorig']/100 );
			$indexData[$valuta]['performancePstart'] =     ($indexData[$valuta]['fondsKoers_eind'] - $indexData[$valuta]['fondsKoers_pstart']) / ($indexData[$valuta]['fondsKoers_pstart']/100 );

			//  listarray($data);
			$this->pdf->row(array('    '.$valuta."/EUR",'',
												$this->formatGetal($indexData[$valuta]['fondsKoers_eind'],4),
												$this->formatGetal($indexData[$valuta]['performance'],1).'%',
												$this->formatGetal($indexData[$valuta]['performanceJaar'],1).'%',
												$this->formatGetal($indexData[$valuta]['performanceVorig'],1).'%',
												$this->formatGetal($indexData[$valuta]['performancePstart'],1).'%'));
			$this->pdf->excelData[]=array('    '.$valuta."/EUR",'',
				round($indexData[$valuta]['fondsKoers_eind'],4),
				round($indexData[$valuta]['performance'],1),
				round($indexData[$valuta]['performanceJaar'],1),
				round($indexData[$valuta]['performanceVorig'],1),
				round($indexData[$valuta]['performancePstart'],1));
		}



		if(count($indices)==0)
			return 1;

	}

}
?>