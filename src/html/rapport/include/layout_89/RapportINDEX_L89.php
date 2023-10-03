<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/13 15:37:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: RapportINDEX_L89.php,v $
 		Revision 1.2  2020/05/13 15:37:13  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/04/08 15:45:20  rvv
 		*** empty log message ***
 		

 		
 	
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");

//ini_set('max_execution_time',60);
class RapportINDEX_L89
{
	function RapportINDEX_L89($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_RISK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_RISK_titel;
		else
			$this->pdf->rapport_titel = "\n \n \nRendement & Risicokenmerken";

		$this->pdf->rapport_titel2='';

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$this->perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->addPage();
		$this->pdf->templateVars[$this->pdf->rapport_type .'Paginas']=$this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=trim($this->pdf->rapport_titel);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		$benchmarkData=getBenchmarkvergelijking($this);
    $this->printBenchmarkvergelijking($benchmarkData);
 	}



  function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

  function printBenchmarkvergelijking($benchmarkData)
  {
		$regelData=$benchmarkData['opbouw'];
		$totalen=$benchmarkData['totaal'];
		$this->pdf->SetWidths(array(60,25,25,25,25,25,25,20,25));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('Benchmark-vergelijking','ZP','begin','eind','Perf in %','weging bm','weging p','factor','weging new','aandeel Perf'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach ($regelData as $regel)
		{
			$this->pdf->row(array($regel['fonds'],
												    $regel['zorgplicht'],
	  	  										$this->formatGetal($regel['fondsKoers_begin'],2),
														$this->formatGetal($regel['fondsKoers_eind'],2),
														$this->formatGetal($regel['performance'],2),
														$this->formatGetal($regel['samenstellingBenchmark'],2),
														$this->formatGetal($regel['wegingPortefeuille'],2),
														$this->formatGetal($regel['factor'],2),
														$this->formatGetal($regel['wegingNew'],2),
														$this->formatGetal($regel['aandeelPerf'],2)
													));
		}
    $this->pdf->ln(2);
    $this->pdf->row(array('','','','','',
													$this->formatGetal($totalen['samenstellingBm']*100,2),
													'','',
													$this->formatGetal($totalen['weging']*100,2),
													$this->formatGetal($totalen['rendement'],2)
												));

  }
 
  



}
?>
