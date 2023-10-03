<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "valutaEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("Valuta","id",array("width"=>100,"search"=>false));
$list->addField("Valuta","Valuta",array("list_width"=>100,"search"=>true));
$list->addField("Valuta","Omschrijving",array("search"=>true));
$list->addField("Valuta","ValutaImportCode",array("list_width"=>100,"search"=>false));
$list->addField("Valuta","Valutateken",array("list_width"=>100,"search"=>false));
//$list->addField("Valuta","Koerseenheid",array("list_width"=>100,"search"=>false));
//$list->addField("Valuta","Afdrukvolgorde",array("list_width"=>100,"search"=>false));
//$list->addField("Valuta","AABgrens",array("list_width"=>100,"search"=>false));
//$list->addField("Valuta","AABcorrectie",array("list_width"=>100,"search"=>false));

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
<br>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>