<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/02/24 17:41:18 $
 		File Versie					: $Revision: 1.6 $

 		$Log: Remisiervergoeding.php,v $
 		Revision 1.6  2011/02/24 17:41:18  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2010/05/20 17:56:16  rvv
 		*** empty log message ***

 		Revision 1.4  2010/05/19 16:23:11  rvv
 		*** empty log message ***

 		Revision 1.3  2009/07/12 09:31:17  rvv
 		*** empty log message ***

 		Revision 1.2  2009/06/24 14:43:11  rvv
 		*** empty log message ***

 		Revision 1.1  2008/10/06 12:25:54  rvv
 		*** empty log message ***


*/
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/factuur/PDFFactuur.php");

	include_once("rapport/factuur/FactuurRekenClass.php");
	include_once('../../classes/excel/Writer.php');


class Remisiervergoeding
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Remisiervergoeding(  $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');

		$this->pdf->rapport_type = "remisiervergoeding";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];

 		$this->orderby  = " Portefeuilles.Remisier ";
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	global $__appvar;
	$this->pdf->__appvar = $this->__appvar;

  $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
  $records = $selectie->getRecords();


  $portefeuilles = $selectie->getSelectie();

	if($records <= 0)
	{
		echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
		$this->progressbar->hide();
		exit;
	}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		$rapportageDatumStart= jul2sql($this->selectData[datumVan]);
		$rapportageDatumStop = jul2sql($this->selectData[datumTm]);
		// vul eerst de tijdelijketabel
		$this->pdf->AddPage();

		$db=new DB();
		$query="SELECT Vermogensbeheerders.Logo,Vermogensbeheerders.Layout FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker ='".$_SESSION['USR']."' ";
		$db->SQL($query);
		$data = $db->lookupRecord();
		$layout=$data['Layout'];

		if($layout == 2)
		  $this->pdf->excelData[] = array("Remisier","Startdatum","Portefeuille","Client","Gemiddelde waarde","Te betalen fee",'Percentage',"Remisier- vergoeding","Bodem- vermogen",'Netto',"BTW","Netto incl");
		else
		  $this->pdf->excelData[] = array("Remisier","Portefeuille","Client","Gemiddelde waarde","Te betalen fee",'Percentage',"Remisier- vergoeding","Bodem- vermogen",'Netto',"BTW","Netto incl");

		$methodes['1'] = '%fee';
		$methodes['2'] = '%vermogen';
		$db = new DB();
		foreach($portefeuilles as $pdata)
		{
		  if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				flush();
			}

		  $query = "SELECT * FROM Remisiers WHERE Remisier = '".$pdata['Remisier']."'";
		  $db->SQL($query);
		  $remisier = $db->lookupRecord();

		  $berekening = new factuurBerekening($pdata['Portefeuille'], $rapportageDatumStart, $rapportageDatumStop,$this->pdf->FactuurDrempelPercentage,true);
    	$waarden = $berekening->berekenWaarden();
    	$berekening = new factuurBerekening($pdata['Portefeuille'], $rapportageDatumStart, $rapportageDatumStop,$this->pdf->FactuurDrempelPercentage,false);
    	$berekening->rekenvermogen=$waarden['basisRekenvermogen'];
    	$berekening->gemiddeldeVermogen=$berekening->rekenvermogen;
    	$waarden = $berekening->berekenWaarden(false);

		  if($remisier['methode'] == 1) //%fee
		  {
		    $remisiervergoeding = $waarden['beheerfeeBetalen'] * ($remisier['percentage']/100);
		  }
		  elseif ($remisier['methode'] == 2) //%vermogen
		  {
		    $remisiervergoeding = $waarden['gemiddeldeVermogen'] * ($remisier['percentage']/100);
		  }
		  else
		   $remisiervergoeding=0;

		  if($layout == 2)
		  {
		    $startdatum=(dbdate2form($pdata['Startdatum']));
			  $regel = array($pdata['Remisier'],$startdatum,$pdata['Portefeuille'],$pdata['Client'],$this->formatGetal($waarden['gemiddeldeVermogen'],2),$this->formatGetal($waarden['beheerfeeBetalen'],2),$this->formatGetal($remisier['percentage'],2)." %",$this->formatGetal($remisiervergoeding,2));
			  $regel1 = array($pdata['Remisier'],$startdatum,$pdata['Portefeuille'],$pdata['Client'],round($waarden['gemiddeldeVermogen'],2),round($waarden['beheerfeeBetalen'],2),round($remisier['percentage'],2),round($remisiervergoeding,2));
		  }
		  else
		  {
			  $regel = array($pdata['Remisier'],$pdata['Portefeuille'],$pdata['Client'],$this->formatGetal($waarden['gemiddeldeVermogen'],2),$this->formatGetal($waarden['beheerfeeBetalen'],2),$this->formatGetal($remisier['percentage'],2)." %",$this->formatGetal($remisiervergoeding,2));
			  $regel1 = array($pdata['Remisier'],$pdata['Portefeuille'],$pdata['Client'],round($waarden['gemiddeldeVermogen'],2),round($waarden['beheerfeeBetalen'],2),round($remisier['percentage'],2),round($remisiervergoeding,2));
		  }

			$this->pdf->row($regel);
			$this->pdf->excelData[] = $regel1;

			$totalen[$pdata['Remisier']]['gemiddeldeVermogen']+=$waarden['gemiddeldeVermogen'];
			$totalen[$pdata['Remisier']]['beheerfeeBetalen']+=$waarden['beheerfeeBetalen'];
			$totalen[$pdata['Remisier']]['percentage']=$remisier['percentage'];
			$totalen[$pdata['Remisier']]['btw']=$remisier['btw'];
			$totalen[$pdata['Remisier']]['remisiervergoeding']+=$remisiervergoeding;
			$totalen[$pdata['Remisier']]['bodemVermogen'] =$remisier['bodemVermogen'];
			$totalen[$pdata['Remisier']]['methode'] = $remisier['methode'];

      $lastRemisier = $pdata['Remisier'];
      $lastPortefeuille = $pdata['Portefeuille'];
		}
		$this->pdf->ln(8);
		$this->pdf->excelData[] = array();
		$this->pdf->excelData[] = array('Totalen');
		$this->pdf->row(array('Totalen'));

		foreach ($totalen as $remisier=>$data)
		{
		  if($data['bodemVermogen'] > 0)
		  {
		    //$netto = $data['remisiervergoeding']-($data['bodemVermogen']*($data['percentage']/100));
		    $korting=(($data['beheerfeeBetalen']/$data['gemiddeldeVermogen'])*$data['bodemVermogen']);
		    $netto=($data['beheerfeeBetalen']-$korting)*($data['percentage']/100);
		  }
		  else
		    $netto = $data['remisiervergoeding'];

		  if($data['gemiddeldeVermogen']-$data['bodemVermogen'] < 0)
		    $netto=0;

		  $vergoedingIncl =   $netto * (1+$data['btw']/100);

		  if(round($data['bodemVermogen'],2) != 0.00)
		  {
    		$bodemVermogen=  $this->formatGetal($data['bodemVermogen'],2);
    		$bodemVermogen1=  round($data['bodemVermogen'],2);
		  }
		  else
		  {
		    $bodemVermogen ='';
		    $bodemVermogen1 = '';
		  }

		  if($layout == 2)
		  {
		    $regel = array($remisier,'',$methodes[$data['methode']],'',$this->formatGetal($data['gemiddeldeVermogen'],2),$this->formatGetal($data['beheerfeeBetalen'],2),$this->formatGetal($data['percentage'],2)." %",
		                $this->formatGetal($data['remisiervergoeding'],2),$bodemVermogen,$this->formatGetal($netto,2),$this->formatGetal($data['btw'],2)." %",$this->formatGetal($vergoedingIncl,2));
		    $regel1 = array($remisier,'',$methodes[$data['methode']],'',round($data['gemiddeldeVermogen'],2),round($data['beheerfeeBetalen'],2),round($data['percentage'],2),
		                round($data['remisiervergoeding'],2),$bodemVermogen1,round($netto,2),round($data['btw'],2),round($vergoedingIncl,2));
		  }
		  else
		  {
		    $regel = array($remisier,$methodes[$data['methode']],'',$this->formatGetal($data['gemiddeldeVermogen'],2),$this->formatGetal($data['beheerfeeBetalen'],2),$this->formatGetal($data['percentage'],2)." %",
		                $this->formatGetal($data['remisiervergoeding'],2),$bodemVermogen,$this->formatGetal($netto,2),$this->formatGetal($data['btw'],2)." %",$this->formatGetal($vergoedingIncl,2));
		    $regel1 = array($remisier,$methodes[$data['methode']],'',round($data['gemiddeldeVermogen'],2),round($data['beheerfeeBetalen'],2),round($data['percentage'],2),
		                round($data['remisiervergoeding'],2),$bodemVermogen1,round($netto,2),round($data['btw'],2),round($vergoedingIncl,2));
		  }
		  $this->pdf->row($regel);
		  $this->pdf->excelData[] = $regel1;

		}



		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$excelData = generateCSV($this->pdf->excelData);
			fwrite($fp,$excelData);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}
}
?>