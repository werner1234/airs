<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$editScript = "accountmanagerEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("Accountmanager","id",array("width"=>100,"search"=>false));
$list->addField("Accountmanager","Accountmanager",array("width"=>100,"search"=>true));
$list->addField("Accountmanager","Naam",array("search"=>true));
$list->addField("Accountmanager","Vermogensbeheerder",array("width"=>200,"search"=>false));

if(checkAccess($type)) 
{
	// superusers
	$allow_add = true;
}
else 
{
	// normale users mogen alleen hun eigen vermogensbeheerders zien
	$list->setJoin("INNER JOIN VermogensbeheerdersPerGebruiker ON Accountmanagers.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' ");
	$allow_add = false;
}
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

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
<?php
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>