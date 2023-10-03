<?
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/05/29 06:37:51 $
File Versie					: $Revision: 1.2 $

$Log: bestandsvergoedingFactuurvoorstel.php,v $
Revision 1.2  2011/05/29 06:37:51  rvv
*** empty log message ***

Revision 1.1  2011/05/18 16:51:08  rvv
*** empty log message ***

Revision 1.1  2011/04/17 08:57:25  rvv
*** empty log message ***

Revision 1.2  2011/03/26 16:51:50  rvv
*** empty log message ***

Revision 1.1  2011/03/23 16:59:47  rvv
*** empty log message ***


*/
class bestandsvergoedingFactuurvoorstel
{
	var $selectData;
	var $excelData;

	function bestandsvergoedingFactuurvoorstel( $selectData )
	{
    $this->selectData = $selectData;
  	$this->pdf = new PDFOverzicht('L','mm');
	  $this->pdf->excelData = array();
	}

	function writeRapport()
	{
		global $__appvar;
		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" AND BestandsvergoedingPerPortefeuille.portefeuille IN('".implode("','",$portefeuilleList)."') ";
		$rapportageDatum=date('Y-m-d',$this->selectData['datumTm']);
		$rapportageDatumVan=date('Y-m-d',$this->selectData['datumVan']);

	 if($this->selectData['emittent'] <> '')
		  $extraquery=" AND Bestandsvergoedingen.emittent='".$this->selectData['emittent']."' ";

		$query="SELECT
BestandsvergoedingPerPortefeuille.portefeuille,
BestandsvergoedingPerPortefeuille.bedragBerekend,
round(BestandsvergoedingPerPortefeuille.bedragBerekend * (Bestandsvergoedingen.waardeHerrekend/Bestandsvergoedingen.waardeBerekend) ,2) as vergoeding,
if(Vermogensbeheerders.verrekeningBestandsvergoeding = 0,
  if(Bestandsvergoedingen.datumOntvangen > '2000-01-01',round(BestandsvergoedingPerPortefeuille.bedragBerekend * (Bestandsvergoedingen.waardeHerrekend/Bestandsvergoedingen.waardeBerekend) ,2),0),
if(Vermogensbeheerders.verrekeningBestandsvergoeding = 1,
   if(Bestandsvergoedingen.datumGeaccordeerd > '2000-01-01',round(BestandsvergoedingPerPortefeuille.bedragBerekend * (Bestandsvergoedingen.waardeHerrekend/Bestandsvergoedingen.waardeBerekend) ,2),0),0))
AS vergoeding,
Bestandsvergoedingen.vermogensbeheerder,
Bestandsvergoedingen.emittent,
Bestandsvergoedingen.depotbank,
Bestandsvergoedingen.datumBerekend,
Bestandsvergoedingen.waardeBerekend,
Bestandsvergoedingen.datumHerrekend,
Bestandsvergoedingen.waardeHerrekend,
Bestandsvergoedingen.datumGeaccordeerd,
Bestandsvergoedingen.datumOntvangen,
Bestandsvergoedingen.datumUitbetaald,
Bestandsvergoedingen.periodeVan,
Bestandsvergoedingen.periodeTm
FROM
BestandsvergoedingPerPortefeuille
Inner Join Bestandsvergoedingen ON Bestandsvergoedingen.id = BestandsvergoedingPerPortefeuille.bestandsvergoedingId
Inner Join Vermogensbeheerders ON Bestandsvergoedingen.vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE BestandsvergoedingPerPortefeuille.datumUitbetaald < '2000-01-01' AND (Bestandsvergoedingen.datumGeaccordeerd > '2000-01-01' OR Bestandsvergoedingen.datumOntvangen > '2000-01-01')
$extraquery
";

		$db=new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		{
		  $vergoedingPerPortefeuille[$data['portefeuille']]['berekend'] += $data['bedragBerekend'];
		  $vergoedingPerPortefeuille[$data['portefeuille']]['vergoeding'] += $data['vergoeding'];
		  $totalen['berekend'] += $data['bedragBerekend'];
		  $totalen['vergoeding'] += $data['vergoeding'];
		  $vergoedingPerPortefeuilleDetail[$data['portefeuille']][$data['emittent']][$data['periodeVan'].' tot '.$data['periodeTm']]=array(
		  'bedragBerekend'=>$data['bedragBerekend'],'vergoeding'=>$data['vergoeding'],'datumHerrekend'=>$data['datumHerrekend'],'datumGeaccordeerd'=>$data['datumGeaccordeerd'],'datumOntvangen'=>$data['datumOntvangen']);
		}


		$this->pdf->excelData[] = array('Portefeuille','Client','Naam','Emittent','Periode','Bedrag berekend','Vergoeding','Datum herrekend','Datum geaccordeerd','Datum ontvangen');
		foreach ($vergoedingPerPortefeuilleDetail as $port=>$emittenten)
		  foreach ($emittenten as $emittent=>$periodeData)
		    foreach ($periodeData as $periode=>$vergoedingsData)
	     	  $this->pdf->excelData[] = array($port,$portefeuilles[$port]['Client'],$portefeuilles[$port]['Naam'],$emittent,
	     	                                  $periode,$vergoedingsData['bedragBerekend'],$vergoedingsData['vergoeding'],$vergoedingsData['datumHerrekend'],
	     	                                  $vergoedingsData['datumGeaccordeerd'],$vergoedingsData['datumOntvangen']);

    $this->pdf->excelData[] = array('','','','','Totalen',$totalen['berekend'],$totalen['vergoeding'],'','','');
    $this->pdf->excelData[] = array('');
		//echo $query;
		$this->pdf->excelData[] = array('Portefeuille','Client','Naam','bedragBerekend','Vergoeding');
		foreach ($vergoedingPerPortefeuille as $port=>$waarden)
		  $this->pdf->excelData[] = array($port,$portefeuilles[$port]['Client'],$portefeuilles[$port]['Naam'],$waarden['berekend'],$waarden['vergoeding']);

	}
}
?>