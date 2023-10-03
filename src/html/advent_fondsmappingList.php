<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 november 2013
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2013/11/15 10:22:21 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: advent_fondsmappingList.php,v $
    Revision 1.1  2013/11/15 10:22:21  cvs
    aanpassing tbv Adventexport

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "Advent fondsmapping overzicht";

$editScript = "advent_fondsmappingEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Advent_FondsMapping","id",array("list_width"=>"100","search"=>false));

$list->addColumn("Advent_FondsMapping","Fonds",array("list_width"=>"100","search"=>true));
$list->addColumn("Advent_FondsMapping","Omschrijving",array("search"=>true));
$list->addColumn("Advent_FondsMapping","adventCode",array("search"=>true));
$list->addColumn("Advent_FondsMapping","adventSecCode",array("search"=>true));
$list->addColumn("Advent_FondsMapping","change_date",array("search"=>false));

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
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