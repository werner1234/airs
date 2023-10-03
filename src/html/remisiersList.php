<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 juli 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:40 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: remisiersList.php,v $
    Revision 1.3  2011/08/31 14:37:40  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "remisiersEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Remisiers","id",array("list_width"=>"100","search"=>false));
$list->addColumn("Remisiers","Remisier",array("list_width"=>"100","search"=>false));
$list->addColumn("Remisiers","Naam",array("list_width"=>"100","search"=>false));
$list->addColumn("Remisiers","Vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addColumn("Remisiers","percentage",array("list_width"=>"100","search"=>false));
$list->addColumn("Remisiers","methode",array("list_width"=>"150","search"=>false));
$list->addColumn("Remisiers","bodemVermogen",array("list_width"=>"100","search"=>false));


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

$data['methode']['value'] = $data['methode']['form_options'][$data['methode']['value']];
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
