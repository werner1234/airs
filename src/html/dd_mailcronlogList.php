<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 30 juni 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/12/13 13:42:30 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: dd_mailcronlogList.php,v $
    Revision 1.1  2017/12/13 13:42:30  cvs
    call 5911

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "log automatisch ingelezen mails overzicht";

$editScript = "dd_mailcronlogEdit.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Dd_mailCronLog","id",array("search"=>false));
$list->addColumn("Dd_mailCronLog","stamp",array("search"=>false));

$list->addColumn("Dd_mailCronLog","CRM_naam",array("search"=>true));
$list->addColumn("Dd_mailCronLog","CRM_id",array("list_invisible"=>true));
$list->addColumn("Dd_mailCronLog","route",array("search"=>true));

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
  <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
  <link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">
  <button class="btn-new btn-default"><a href="dd_inlees_email.php"><i class="fa fa-angle-double-left" aria-hidden="true"></i> terug naar inleesmenu </a></button><br/><br/>
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