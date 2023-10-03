<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("geconsolideerde portefeuilles");
$mainHeader    = vt("overzicht");

$editScript = "geconsolideerdeportefeuillesEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("GeconsolideerdePortefeuilles","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","VirtuelePortefeuille",array("list_width"=>"100","search"=>true));
$list->addFixedField("GeconsolideerdePortefeuilles","Portefeuille1",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","Portefeuille2",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","Portefeuille3",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","Portefeuille4",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","Vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","Client",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","Naam",array("list_width"=>"100","search"=>false));
$list->addFixedField("GeconsolideerdePortefeuilles","Naam1",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('GeconsolideerdePortefeuilles');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

//$list->setFilter();
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
