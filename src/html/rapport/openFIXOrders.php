<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/10/14 17:25:20 $
 		File Versie					: $Revision: 1.1 $

 		$Log: openFIXOrders.php,v $
 		Revision 1.1  2017/10/14 17:25:20  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/09/18 17:26:38  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/08/04 05:51:18  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/08/02 18:22:25  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/07/30 10:19:17  rvv
 		*** empty log message ***
 		


*/
include_once("rapportRekenClass.php");

class openFIXOrders
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function openFIXOrders( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "openFIXOrders";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$x=280/18;
		$this->pdf->setWidths(array($x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x,$x));

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	function writeRapport()
	{

		$db=new DB();

		$query="SELECT
fx.orderid,
fx.add_user,
fx.add_date,
fx.portefeuille,
fx.client,
fx.vermogensBeheerder,
fx.aantal,
fx.fondsCode,
fx.fonds,
fx.transactieSoort,
fx.laatsteStatus,
fx.Depotbank,
fx.AIRS_bedrijf,
od.ISINCode,
od.orderStatus,
od.orderSubStatus,
od.fixVerzenddatum,
od.fixAnnuleerdatum
from fixOrders fx
left join OrdersV2 od on fx.AIRSorderReference = od.Id
where fx.laatsteStatus in ('cp','0','1')";

		$db->SQL($query);
	  $db->query();

		$this->pdf->excelData[]=array('orderid','add_user','add_date','portefeuille','client','vermogensBeheerder','aantal','fondsCode','fonds','transactieSoort',
			'laatsteStatus','Depotbank','AIRS_bedrijf','ISINCode','orderStatus','orderSubStatus','fixVerzenddatum','fixAnnuleerdatum');
		$this->pdf->Row(array('orderid','add_user','add_date','portefeuille','client','vermogensBeheerder','aantal','fondsCode','fonds','transactieSoort',
											'laatsteStatus','Depotbank','AIRS_bedrijf','ISINCode','orderStatus','orderSubStatus','fixVerzenddatum','fixAnnuleerdatum'));

		while($data=$db->nextRecord('num'))
		{
			$this->pdf->excelData[]=$data;
			$this->pdf->Row($data);
		}

 		if($this->progressbar)
			$this->progressbar->hide();
	}


}
?>