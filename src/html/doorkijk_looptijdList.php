<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/12/04 14:48:17 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: doorkijk_looptijdList.php,v $
    Revision 1.2  2017/12/04 14:48:17  cvs
    call 6349

    Revision 1.1  2017/12/04 10:40:51  cvs
    Update van Ben ingelezen dd 4-12-2017

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Looptijden overzicht");

$editScript = "doorkijk_looptijdEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("doorkijk_looptijd","id",array("search"=>false));
$list->addFixedField("doorkijk_looptijd","code",array("search"=>false));
$list->addFixedField("doorkijk_looptijd","van",array("search"=>false));
$list->addFixedField("doorkijk_looptijd","tot",array("search"=>false));

$html = $list->getCustomFields('doorkijk_looptijd','doorkijk_looptijd');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

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
	// $list->buildRow($data,$template="",$options="");
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
?>