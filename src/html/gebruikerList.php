<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "gebruikerEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("Gebruiker","id",array("width"=>100,"search"=>false));
$list->addFixedField("Gebruiker","Gebruiker",array("width"=>100,"search"=>true));
$list->addFixedField("Gebruiker","Naam",array("search"=>false));

$html = $list->getCustomFields('Gebruiker');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

if(checkAccess($type) || $_SESSION['usersession']['gebruiker']['Gebruikersbeheer'] == 1) 
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
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>