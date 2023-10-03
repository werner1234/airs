<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 december 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:39 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: attributiepergrootboekrekeningList.php,v $
    Revision 1.2  2011/08/31 14:37:39  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Attributie per grootboekrekening");

$editScript = "attributiepergrootboekrekeningEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("AttributiePerGrootboekrekening","id",array("list_width"=>"100","search"=>false));
$list->addColumn("AttributiePerGrootboekrekening","Vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addColumn("AttributiePerGrootboekrekening","AttributieCategorie",array("list_width"=>"100","search"=>false));
$list->addColumn("AttributiePerGrootboekrekening","Grootboekrekening",array("list_width"=>"100","search"=>false));

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content["javascript"] .= "
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
