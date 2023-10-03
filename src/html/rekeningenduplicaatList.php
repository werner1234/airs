<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 februari 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/07/22 18:20:50 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: rekeningenduplicaatList.php,v $
    Revision 1.3  2017/07/22 18:20:50  rvv
    *** empty log message ***


 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("rekeningen duplicaten");
$mainHeader    = vt("overzicht");

$editScript = "rekeningenduplicaatEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$list->addFixedField("RekeningenDuplicaat","Rekening",array("list_width"=>"100","search"=>false));
$list->addFixedField("RekeningenDuplicaat","RekeningDuplicaat",array("list_width"=>"100","search"=>false));
$list->addFixedField("RekeningenDuplicaat","Memo",array("list_width"=>"100","search"=>false));
$list->addFixedField("RekeningenDuplicaat","actief",array("list_width"=>"100","search"=>false));

$list->categorieVolgorde=array('RekeningenDuplicaat'=>array('Algemeen'),
'Rekeningen'=>array('Algemeen'),
'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels','Recordinfo'));
$html = $list->getCustomFields(array('RekeningenDuplicaat','Rekeningen','Portefeuilles'),'RekeningenDupList');

$joinRekeningen='';
$joinPortefeuilles='';
foreach ($list->columns as $colData)
{
  if($joinRekeningen=='' && ($colData['objectname'] == 'Rekeningen' || $colData['objectname'] == 'Portefeuilles'))
  {
    $joinRekeningen=" LEFT JOIN Rekeningen ON RekeningenDuplicaat.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0";
  }
  if($joinPortefeuilles=='' && $colData['objectname'] == 'Portefeuilles')
  {
    $joinPortefeuilles=" LEFT JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0";
  }

}
  $list->ownTables=array('RekeningenDuplicaat');
  $list->setJoin("$joinRekeningen $joinPortefeuilles ");


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>

<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
</form>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
