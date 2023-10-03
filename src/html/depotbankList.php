<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "depotbankEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$list->addField("Depotbank","Depotbank",array("list_width"=>200,"search"=>true));
$list->addField("Depotbank","Omschrijving",array("search"=>true));


if(checkAccess($type))
{
	// superusers
	$allow_add = true;
}
else
{
	// selecteer alleen de depotbanken bij vemogensbeheerder
	//$list->setJoin("RIGHT JOIN Portefeuilles ON Depotbanken.Depotbank = Portefeuilles.Depotbank ");
	$allow_add = false;
}


// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);


$list->setFilter();

// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

//$content['jsincludes'] .= $editcontent['jsincludes'];
$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}



";
echo template($__appvar["templateContentHeader"],$content);
?>

<br>
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