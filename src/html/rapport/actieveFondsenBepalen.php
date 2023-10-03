<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/08/02 18:22:25 $
 		File Versie					: $Revision: 1.1 $

 		$Log: actieveFondsenBepalen.php,v $
 		Revision 1.1  2017/08/02 18:22:25  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/07/30 10:19:17  rvv
 		*** empty log message ***
 		


*/
include_once("rapportRekenClass.php");

class actieveFondsenBepalen
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function actieveFondsenBepalen( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "koersControle";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->aantalfondsen =0;
		$this->valutas =0;

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;
		$this->labelPerRegel=array();
		$this->onbekendekoers=array();
		$this->fondsen = array();
		$this->koersControleCheck='';
		$this->orderby = " Client ";
		$this->wherePortefeuilles='';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function bepaalActieveFondsen($portefeuilleList)
	{
		include_once("../classes/bepaalActieveFondsenClass.php");
		$actieveFondsen = new bepaalActieveFondsen();
		$actieveFondsen->verbose=true;
		$actieveFondsen->createTable();
		$actieveFondsen->fillTable($portefeuilleList);
	  $xlsdata=$actieveFondsen->createXls(true);
		foreach($xlsdata as $row)
			$this->pdf->excelData[]=$row;
	}



	function writeRapport()
	{
		global $__appvar;

		if($this->selectData['gebruikPortefeuilleSelectie']==1)
		{
			$selectie = new portefeuilleSelectie($this->selectData, $this->orderby);
			$portefeuilles = $selectie->getSelectie();
			$portefeuilleList = array_keys($portefeuilles);
		}
		else
			$portefeuilleList=array();

	  $this->bepaalActieveFondsen($portefeuilleList);


 		if($this->progressbar)
			$this->progressbar->hide();
	}


}
?>