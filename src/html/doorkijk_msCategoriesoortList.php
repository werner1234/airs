<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/01/03 14:19:00 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: doorkijk_msCategoriesoortList.php,v $
    Revision 1.3  2018/01/03 14:19:00  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("doorkijk ms Categorie soort");
$mainHeader    = vt("overzicht");

$editScript = "doorkijk_msCategoriesoortEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("doorkijk_msCategoriesoort","id",array("search"=>false));
$list->addFixedField("doorkijk_msCategoriesoort","msCategoriesoort",array("search"=>false,'list_width'=>200));
$list->addFixedField("doorkijk_msCategoriesoort","msCategorie",array("search"=>false,'list_width'=>200));
$list->addFixedField("doorkijk_msCategoriesoort","omschrijving",array("search"=>false,'list_width'=>200));

$html = $list->getCustomFields('doorkijk_msCategoriesoort','doorkijk_msCategoriesoort');

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");

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

<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
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
