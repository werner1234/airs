<?php
/*
    AE-ICT CODEX source module versie 1.6, 21 april 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2012/05/20 06:42:23 $
    File Versie         : $Revision: 1.1 $

    $Log: EigendomPerPortefeuilleList.php,v $
    Revision 1.1  2012/05/20 06:42:23  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("Eigendom Per Portefeuille");
$mainHeader    = vt("overzicht");

$editScript = "EigendomPerPortefeuilleEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$list->addFixedField("EigendomPerPortefeuille","Portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("EigendomPerPortefeuille","Owner",array("list_width"=>"100","search"=>false));
$list->addFixedField("EigendomPerPortefeuille","percentage",array("list_width"=>"100","search"=>false));
$list->addFixedField("EigendomPerPortefeuille","add_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("EigendomPerPortefeuille","add_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("EigendomPerPortefeuille","change_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("EigendomPerPortefeuille","change_user",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields(array('EigendomPerPortefeuille'));
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
