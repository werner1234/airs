<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 juni 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/01/04 13:24:11 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: tgc_blacklistList.php,v $
    Revision 1.1  2017/01/04 13:24:11  cvs
    call 5542, uitrol WWB en TGC

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("blacklist (geblokkeerde IP nummers)");
$mainHeader    = vt("overzicht");

$editScript = "tgc_blacklistEdit.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Tgc_blacklist","id",array("list_width"=>"100","search"=>false));
$list->addColumn("Tgc_blacklist","add_date",array("list_width"=>"100","search"=>false));
$list->addColumn("Tgc_blacklist","ip",array("list_width"=>"100","search"=>true));

$list->addColumn("Tgc_blacklist","onList",array("list_width"=>"100","search"=>false));
$list->addColumn("Tgc_blacklist","offList",array("list_width"=>"100","search"=>false));
$list->addColumn("Tgc_blacklist","memo",array("list_width"=>"600","search"=>false));


// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
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