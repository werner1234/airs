<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/05/20 06:43:32 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportEigendomsverhouding.php,v $
 		Revision 1.1  2012/05/20 06:43:32  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/04/25 15:19:32  rvv
 		*** empty log message ***

 		Revision 1.1  2012/04/22 07:50:18  rvv
 		*** empty log message ***

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


class RapportEigendomsverhouding
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportEigendomsverhouding(  $selectData )
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
		$portefeuilleNummers=array_keys($portefeuilles);
	  $db = new DB();
		$query="SELECT Eigenaar, (percentage/100) as percentage,Portefeuille FROM EigendomPerPortefeuille WHERE Portefeuille IN('".implode("','",$portefeuilleNummers)."') ORDER BY Eigenaar";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
		  $eigenaars[$data['Eigenaar']]=$data['Eigenaar'];
		  $percentage[$data['Portefeuille']][$data['Eigenaar']]=$data['percentage'];
    }

//Hierin komt per owner een regel met portefeuille.portefeuille, crm_naam, portefeuille.accountmanager, portefeuille.depotbank, %-tage ownership, totaal vermogen portefeuille, te betalen beheerfee (voor BTW!), "geownde vermogen", "geownde inkomsten".
    $header=array("Portefeuille","CRM naam","Accountmanager","Depotbank",'Totaal vermogen',"Beheerfee");
    foreach ($eigenaars as $eigenaar)
    {
      array_push($header,"$eigenaar % client");
      array_push($header,"$eigenaar deel AUM");
      array_push($header,"$eigenaar deel Profit");
    }
    $this->pdf->excelData[] = $header;



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
    	/*
    	$berekening = new factuurBerekening($pdata['Portefeuille'], $rapportageDatumStart, $rapportageDatumStop,$this->pdf->FactuurDrempelPercentage,false);
    	$berekening->rekenvermogen=$waarden['basisRekenvermogen'];
    	$berekening->gemiddeldeVermogen=$berekening->rekenvermogen;
    	$waarden = $berekening->berekenWaarden(false);
*/

    	$tmp=array($pdata['Portefeuille'],$pdata['crmNaam'],$pdata['Accountmanager'],$pdata['Depotbank'],$waarden['basisRekenvermogen'],$waarden['beheerfeePerPeriode']);
    	foreach ($eigenaars as $eigenaar)
      {
        array_push($tmp,$percentage[$pdata['Portefeuille']][$eigenaar]);
        array_push($tmp,$percentage[$pdata['Portefeuille']][$eigenaar] * $waarden['basisRekenvermogen']);
        array_push($tmp,$percentage[$pdata['Portefeuille']][$eigenaar] * $waarden['beheerfeePerPeriode']);
      }
    	$this->pdf->excelData[] = $tmp;

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