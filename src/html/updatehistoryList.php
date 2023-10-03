<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "updatehistoryEdit.php";
if($__appvar['master'] == false)
  exit;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("UpdateHistory","id",array("width"=>100,"search"=>false));
$list->addFixedField("UpdateHistory","Bedrijf",array("list_width"=>100,"search"=>true));
$list->addFixedField("UpdateHistory","exportId",array("list_width"=>100,"search"=>true));
$list->addFixedField("UpdateHistory","filesize",array("list_width"=>100,"search"=>false));
$list->addFixedField("UpdateHistory","add_date",array("list_width"=>120,"search"=>false));
$list->addFixedField("UpdateHistory","change_date",array("list_width"=>120,"search"=>false));
$list->addFixedField("UpdateHistory","terugmelding",array("search"=>false));
$list->addFixedField("UpdateHistory","complete",array("list_width"=>50,"search"=>false));

$html = $list->getCustomFields('UpdateHistory');

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");

if(empty($_GET['sort']))
{
	$_GET['sort'][] = "UpdateHistory.exportId";
	$_GET['direction'][] = "DESC";
}
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

//$_SESSION[submenu] = New Submenu();
//$_SESSION[submenu]->addItem("Updatelog ophalen","updateHistorySync.php");
//session_write_close();


$content['javascript'] .= "
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