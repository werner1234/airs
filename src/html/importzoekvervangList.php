<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 december 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/03/24 09:35:57 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: importzoekvervangList.php,v $
    Revision 1.1  2017/03/24 09:35:57  cvs
    call 5731

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("zoek vervang bij import overzicht");

$editScript = "importzoekvervangEdit.php";
$allow_add  = true;

$object = new ImportZoekVervang();

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("ImportZoekVervang","id",array("search"=>false));
$list->addColumn("ImportZoekVervang","actief",array("search"=>false));
$list->addColumn("ImportZoekVervang","depotbank",array("search"=>true));
$list->addColumn("ImportZoekVervang","vermogensBeheerder",array("search"=>true));

$list->addColumn("ImportZoekVervang","zoek",array("search"=>true));
$list->addColumn("ImportZoekVervang","vervang",array("search"=>true));
$list->addColumn("ImportZoekVervang","typeVervang",array("search"=>false));



// set default sort
// $_GET['sort'][]      = "tablename.field";
// $_GET['direction'][] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
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


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	$data["typeVervang"]["value"] = $object->typeVervangArray[ $data["typeVervang"]["value"] ];
	echo $list->buildRow($data);
}
?>
</table>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>