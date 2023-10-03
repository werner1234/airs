<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "sectorperhoofdsectorEdit.php";

$list = new MysqlList2();

$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("SectorPerHoofdsector","Vermogensbeheerder",array("width"=>150,"search"=>false));
$list->addFixedField("SectorPerHoofdsector","Hoofdsector",array("search"=>true));
$list->addFixedField("SectorPerHoofdsector","Beleggingssector",array("width"=>150,"search"=>false));

$html = $list->getCustomFields('SectorPerHoofdsector','SPerHs');

if(checkAccess($type))
{
	// superusers
	$allow_add = true;
}
else
{
	// normale user
	$allow_add = false;
}

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content[javascript] .= "
function addRecord() {
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
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>