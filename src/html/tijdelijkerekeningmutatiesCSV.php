<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/21 17:48:19 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: tijdelijkerekeningmutatiesCSV.php,v $
 		Revision 1.3  2018/12/21 17:48:19  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/05/16 08:04:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/01/16 12:29:19  jwellner
 		PDF Export
 		
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AE_cls_listCSV.php");

$list = new ListCSV();
$list->idField = "id";

$allow_add = false;

//$list->addField("TijdelijkeRekeningmutaties","id",array("search"=>false));
$list->addField("Portefeuilles","Client",array());
$list->addField("TijdelijkeRekeningmutaties","Rekening",array("list_tdcode"=>"nowrap","search"=>true));
$list->addField("TijdelijkeRekeningmutaties","Omschrijving",array("list_tdcode"=>"nowrap","search"=>true));
$list->addField("TijdelijkeRekeningmutaties","Boekdatum",array("list_tdcode"=>"nowrap","search"=>false));
$list->addField("TijdelijkeRekeningmutaties","Grootboekrekening",array("list_tdcode"=>"nowrap","description"=>"GB","search"=>true));
$list->addField("TijdelijkeRekeningmutaties","Valuta",array("list_tdcode"=>"nowrap","search"=>false));
$list->addField("TijdelijkeRekeningmutaties","Valutakoers",array("list_tdcode"=>"nowrap","search"=>false));
$list->addField("TijdelijkeRekeningmutaties","Aantal",array("list_tdcode"=>"nowrap","search"=>false));
$list->addField("TijdelijkeRekeningmutaties","Fondskoers",array("list_tdcode"=>"nowrap","search"=>true));
$list->addField("TijdelijkeRekeningmutaties","Debet",array("list_tdcode"=>"nowrap","search"=>false));
$list->addField("TijdelijkeRekeningmutaties","Credit",array("list_tdcode"=>"nowrap","search"=>false));
$list->addField("TijdelijkeRekeningmutaties","Bedrag",array("list_tdcode"=>"nowrap","search"=>false));
$list->addField("TijdelijkeRekeningmutaties","Transactietype",array("list_tdcode"=>"nowrap","search"=>false));

$list->setJoin(" JOIN Rekeningen ON Rekeningen.Rekening = TijdelijkeRekeningmutaties.Rekening AND Rekeningen.consolidatie=0 ");
$list->setWhere("  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 AND TijdelijkeRekeningmutaties.change_user = '$USR' ");

if(empty($_GET['sort'])) 
{
	$_GET['sort'] = array("Rekening");
	$_GET['direction'] = array("ASC");
}
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$file = date("d-m-Y",mktime())."-rekeningmutaties.csv";
header('Pragma: public');
header('Content-type: text/comma-separated-values');
header('Content-disposition: attachment; filename='.$file);
echo $list->getCSV();
?>