<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "beleggingscategorieperfondsEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("BeleggingscategoriePerFonds","id",array("search"=>false));
$list->addFixedField("BeleggingscategoriePerFonds","Vermogensbeheerder",array("list_width"=>100,"search"=>false));
$list->addFixedField("BeleggingscategoriePerFonds","Fonds",array("search"=>true));
$list->addFixedField("BeleggingscategoriePerFonds","Beleggingscategorie",array("search"=>false));
$list->addFixedField("BeleggingscategoriePerFonds","afmCategorie",array("search"=>false));
$list->addFixedField("BeleggingscategoriePerFonds","Vanaf",array("search"=>false));
$list->addFixedField("BeleggingscategoriePerFonds","duurzaamheid",array("search"=>false));

$html = $list->getCustomFields(array('BeleggingscategoriePerFonds','Fonds'),'BeleggingscategoriePerFonds');
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

foreach ($list->columns as $colData)
{
  if($colData['objectname'] == 'Fonds')
  {
    $joinFondsen=" LEFT JOIN Fondsen ON BeleggingscategoriePerFonds.Fonds = Fondsen.Fonds";
  }
}
  $list->ownTables=array('BeleggingscategoriePerFonds');
  $list->setJoin("$joinFondsen ");


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