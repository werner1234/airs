<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/14 14:50:14 $
    File Versie         : $Revision: 1.2 $


 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$fmt = new AE_cls_formatter();
$subHeader     = "";
$mainHeader    = vt("Airs koppelingenoverzicht");

$editScript = "airsKoppelingenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 100;

$list->addColumn("airsKoppelingen","id",array("list_width"=>"100","search"=>false));
$list->addColumn("airsKoppelingen","add_date",array("list_width"=>"160","search"=>false));
$list->addColumn("airsKoppelingen","change_date",array("list_width"=>"160","search"=>false));
$list->addColumn("airsKoppelingen","module",array("list_width"=>"100","search"=>true));
$list->addColumn("airsKoppelingen","airsDescription",array("list_width"=>"250","search"=>true));
$list->addColumn("airsKoppelingen","airsId",array("list_width"=>"40","description"=>"Aid","search"=>false));
$list->addColumn("airsKoppelingen","airsTable",array("list_width"=>"140","search"=>false));
$list->addColumn("airsKoppelingen","externDescription",array("list_width"=>"400","search"=>false));
$list->addColumn("airsKoppelingen","externId",array("list_width"=>"250","search"=>true));


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
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$allow_add));
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
//  debug($data);
  $data['add_date']['form_type'] ='text';
  $data['add_date']['value'] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i} ", $data['add_date']['value']);
  $data['change_date']['form_type'] ='text';
  $data['change_date']['value'] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $data['change_date']['value']);
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