<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "parameterspervermogensbeheerderEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("ParametersPerVermogensbeheerder","id",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","Vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","Categoriesoort",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","Categorie",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","Veld",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","Datum",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","Waarde",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","add_date",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","add_user",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","change_date",array("list_width"=>"100","search"=>false));
$list->addColumn("ParametersPerVermogensbeheerder","change_user",array("list_width"=>"100","search"=>false));


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
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>