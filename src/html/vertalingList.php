<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "vertalingEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("Vertaling","Taal",array("width"=>100,"search"=>true));
$list->addFixedField("Vertaling","Term",array("search"=>true));
$list->addFixedField("Vertaling","Vertaling",array("width"=>100,"search"=>true));

if(checkAccess($type)) 
{
	// superusers
	$allow_add = true;
}
elseif(GetCRMAccess(2))
{ // CRM beheerder
  $allow_add = true;
}
else 
{
	// normale user
	$allow_add = false;
}

$html = $list->getCustomFields(array('Vertaling')); 
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

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
