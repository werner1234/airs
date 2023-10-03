<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/09/22 17:10:39 $
 		File Versie					: $Revision: 1.7 $

 		$Log: orderChecksTotaal.php,v $
 		Revision 1.7  2018/09/22 17:10:39  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/05/03 10:41:10  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/05/02 16:33:37  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/04/30 04:12:35  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/04/26 07:43:01  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/04/25 16:51:38  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/04/04 15:45:49  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/04/01 09:34:21  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/03/25 10:15:58  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/03/18 10:54:29  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/03/17 18:47:40  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/02/24 18:32:26  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/02/17 19:17:53  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/02/14 16:52:34  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2017/10/22 11:11:54  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/10/01 14:32:43  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/09/13 15:44:03  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/08/19 18:19:17  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/10/05 15:57:41  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/06/17 13:03:08  rvv
 		*** empty log message ***

 		Revision 1.10  2011/11/05 16:04:41  rvv
 		*** empty log message ***

 		Revision 1.9  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.8  2011/06/18 15:17:55  rvv
 		*** empty log message ***

 		Revision 1.7  2011/06/02 15:04:19  rvv
 		*** empty log message ***

 		Revision 1.6  2011/04/30 16:27:12  rvv
 		*** empty log message ***

 		Revision 1.5  2010/10/06 16:34:31  rvv
 		*** empty log message ***

 		Revision 1.4  2010/08/25 19:02:17  rvv
 		*** empty log message ***

 		Revision 1.3  2010/08/06 16:32:20  rvv
 		*** empty log message ***

 		Revision 1.2  2010/03/24 17:23:03  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/03 09:50:18  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once("rapport/Zorgplichtcontrole.php");


class orderChecksTotaal
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function orderChecksTotaal( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "orderValidatie";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->db=new DB();
		$this->validaties=getActieveControles();

	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function getSaldo($portefeuille,$datum)
	{
		$beginJaar = substr($datum,0,4)."-01-01";

		$DB = new DB();
		$query = "SELECT SUM(Bedrag) as totaal,Rekeningen.Rekening,Rekeningen.Valuta FROM Rekeningmutaties 
JOIN Rekeningen ON Rekeningmutaties.rekening=Rekeningen.rekening
WHERE boekdatum >= '$beginJaar' AND boekdatum <= '$datum' 
AND Rekeningen.Portefeuille='$portefeuille' AND Rekeningen.Deposito=0 AND Rekeningen.Memoriaal=0
GROUP BY Rekeningen.Rekening";
		$DB->SQL($query);
		$DB->Query();
		$rekeningen=array();
		while($data=$DB->NextRecord())
		{
			$rekeningen[]=$data;
		}

		$saldoEur=0;
		foreach($rekeningen as $rekeningData)
		{
			$query = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '" . $rekeningData['Valuta'] . "' AND Datum <= '" . $datum . "' ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($query);
			$DB->Query();
			$actuelevaluta = $DB->NextRecord();
			$saldoEur+=$rekeningData['totaal']*$actuelevaluta['Koers'];
		}
		return round($saldoEur,2);
	}

	function writeRapport()
	{
		global $__appvar,$__ORDERvar;
		$this->pdf->orderHeader=array("OrderId","Soort","Datum","Client","Portefeuille","Fonds");
		$xlsHeader=array("OrderId","Soort","Datum","Portefeuille","Client","Fonds",
			'status orderregel','Risicoklasse','Accountmanager','Tweede Aanspreekpunt','SoortOvereenkomst','Transactiesoort','Order memoveld','Saldo liquiditeiten op tradedate','Saldo liquiditeiten op tradedate+1');
		foreach($this->validaties as $key=>$validatie)
		{
			$xlsHeader[]=$validatie;
			$this->pdf->orderHeader[]=$key;
		}

		$extraExcel=array('gebruiker','datum');
		foreach($extraExcel as $veld)
		{
			foreach ($this->validaties as $key => $validatie)
			{
				$xlsHeader[]=$key." ".$veld;
			}
		}

		if($this->selectData['zorgplicht']==1)
		{
			$xlsHeader[]='Zorgplicht';
		}

		$this->pdf->excelData[]= $xlsHeader;


		$begindatum = jul2sql($this->selectData['datumVan']);
		$einddatum = jul2sql($this->selectData['datumTm']);

		$this->pdf->__appvar = $__appvar;

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		if($records <= 0)		{
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

		// voor kopjes
		$this->pdf->setWidths(array(20,15,35,25,25,30,13,13,13,13,13,13,13,13,13,13,13));
		$this->pdf->setAligns(array('L','L','L','L','L','L','C','C','C','C','C','C','C','C','C','C'));

		$this->pdf->AddPage();

		$portefeuillesKeys=array_keys($portefeuilles);

		$extraFilters=array('OrdersV2.Fonds'=>'FondsenKeyActief','OrderRegelsV2.orderId'=>'Ordernummer',
												'OrdersV2.orderSoort'=>'Ordersoort','OrderRegelsV2.orderregelStatus'=>'Orderstatus','OrderRegelsV2.add_date'=>'datumDb');
		$extraWhere='';
    foreach($extraFilters as $dbKey=>$filerKey)
		{
			if($this->selectData[$filerKey."Van"] <> '' && $this->selectData[$filerKey."Tm"])
			{
				$extraWhere .= "AND ( $dbKey >= '" . $this->selectData[$filerKey . "Van"] . "' ";
				$extraWhere .= "AND  $dbKey <= '" . $this->selectData[$filerKey . "Tm"] . "' ) \n ";
			}
    }

		$query="SELECT
OrderRegelsV2.orderid,
OrderRegelsV2.client,
OrderRegelsV2.portefeuille,
OrdersV2.fonds,
OrderRegelsV2.orderregelStatus,
OrderRegelsV2.controleRegels,
OrderRegelsV2.controleStatus,
OrdersV2.depotbank,
OrdersV2.batchId,
OrderRegelsV2.add_date,
OrderRegelsV2.add_user,
OrdersV2.orderSoort,
OrdersV2.transactieSoort,
OrdersV2.memo
FROM
OrderRegelsV2
INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id WHERE OrderRegelsV2.portefeuille IN('".implode("','",$portefeuillesKeys)."') $extraWhere";
		$this->db->SQL($query);
		$this->db->Query();
		$orderRegels=array();
		while($data=$this->db->nextRecord())
		{
			$name=getCrmNaam($data['portefeuille'],true);
			if($name['naam'] <> '')
		  	$data['client']=$name['naam'];
			$orderRegels[]=$data;
		}

		$pro_multiplier = 100 / count($orderRegels);
		foreach($orderRegels as $data)
		{

			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}

			$pdata=$portefeuilles[$data['portefeuille']];

			$query="SELECT Portefeuilles.tweedeAanspreekpunt FROM Portefeuilles WHERE Portefeuilles.Portefeuille='".$data['portefeuille']."'";
			$this->db->SQL($query);
			$this->db->Query();
			$pdata2 = $this->db->lookupRecord();

			$boekJul=db2jul($data['add_date']);
			/*
			if(date('w',$boekJul)==5)
				$tweedeDatum=date("Y-m-d",$boekJul+86400*3);
			else
				$tweedeDatum=date("Y-m-d",$boekJul+86400);
			*/
			$tweedeDatum=date("Y-m-d",$boekJul+86400*5);
			$saldo1=$this->getSaldo($data['portefeuille'],$data['add_date']);
			$saldo2=$this->getSaldo($data['portefeuille'],$tweedeDatum); //+3 dagen

			if($this->selectData['zorgplicht']==1)
			{
				if(substr($data['add_date'],5,5)=='01-01')
					$min1Dag=true;
				else
					$min1Dag=false;

				$vulDag=substr($data['add_date'],0,10);
				$portefeuilleWaarden=berekenPortefeuilleWaarde($data['portefeuille'],$vulDag,$min1Dag,'EUR',$vulDag);
				vulTijdelijkeTabel($portefeuilleWaarden, $data['portefeuille'], $vulDag);
				$zorg=new Zorgplichtcontrole($this->selectData);
				$tmp=$zorg->zorgplichtMeting($pdata,$vulDag);
				$zorgplichtTxt=$tmp['zorgMeting'];
			}


			$controlleRegels=unserialize($data['controleRegels']);
			$result='';

			$pdfRow=array($data['orderid'], $data['orderSoort'], date('d-m-Y H:i:s', db2jul($data['add_date'])), $data['client'], $data['portefeuille'], $data['fonds']);
			$excelRow=array($data['orderid'], $data['orderSoort'], date('d-m-Y H:i:s', db2jul($data['add_date'])), $data['client'], $data['portefeuille'], $data['fonds'],
				$__ORDERvar["orderStatus"][$data['orderregelStatus']],
				$pdata['Risicoklasse'],
				$pdata['Accountmanager'],
				$pdata2['tweedeAanspreekpunt'], //Tweede Aanspreekpunt
				$pdata['SoortOvereenkomst'],
				$data['transactieSoort'],
				$data['memo'],
				$saldo1,
				$saldo2);
			$extraExcelData=array();
			$checked=false;
			foreach($this->validaties as $check=>$validatie)
			{
				$pdfRow[]=$controlleRegels[$check]['checked'];

				$excelRow[]=$controlleRegels[$check]['checked'];
				if($controlleRegels[$check]['checked']>0)
					$checked=true;

				if($this->selectData['filetype']=='xls')
				{
					$logQueries = array("SELECT add_user,add_date,message,bulkorderRecordId FROM orderLogs WHERE orderRecordId='" . $data['orderid'] . "' AND Message like '%Check $check%' AND Message like '%" . $data['portefeuille'] . "%' ORDER by add_date desc limit 1",
						"SELECT add_user,add_date,message,bulkorderRecordId FROM orderLogs WHERE orderRecordId='" . $data['orderid'] . "' AND Message like '%Check $check%' ORDER by add_date desc limit 1");
					$validateLog=array();
					foreach ($logQueries as $query)
					{
						$this->db->SQL($query);
						$this->db->Query();
						$validateLog = $this->db->lookupRecord();
						if ($this->db->records() > 0)
						{
							break;
						}
					}
					$extraExcelData['add_user'][]=$validateLog['add_user'];
					$extraExcelData['add_date'][]=$validateLog['add_date'];
				}

			}
			if($this->selectData['orderValidatieFilter']=='all' || $checked==true)
			$this->pdf->row($pdfRow);
			foreach($extraExcelData['add_user'] as $value)
				$excelRow[]=$value;
			foreach($extraExcelData['add_date'] as $value)
				$excelRow[]=$value;

			if($this->selectData['zorgplicht']==1)
			{
				$excelRow[]=$zorgplichtTxt;
			}

			$this->pdf->excelData[]=$excelRow;
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}
}
?>