<?php
/*
    AE-ICT sourcemodule created 28 sep. 2022
    Author              : Chris van Santen
    Filename            : updatequeueList.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "updatequeueEdit.php";
if($__appvar['master'] == false)
{
  exit;
}

$subHeader     = "";
$mainHeader    = vt("Updateserver updates overzicht");


$list = new MysqlList2();
$list->dbId = 2;
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("UpdateHistory","id",array("width"=>100,"search"=>false));
$list->addFixedField("UpdateQueue","Bedrijf",array("list_width"=>100,"search"=>true));
$list->addFixedField("UpdateQueue","exportId",array("list_width"=>100,"search"=>true));
$list->addFixedField("UpdateQueue","filesize",array("list_width"=>100,"search"=>false));
$list->addFixedField("UpdateQueue","add_date",array("list_width"=>120,"search"=>false));
$list->addFixedField("UpdateQueue","change_date",array("list_width"=>120,"search"=>false));
$list->addFixedField("UpdateQueue","terugmelding",array("search"=>false));
$list->addFixedField("UpdateQueue","complete",array("list_width"=>50,"search"=>false));

$html = $list->getCustomFields('UpdateQueue');

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");

if(empty($_GET['sort']))
{
	$_GET['sort'][] = "UpdateQueue.exportId";
	$_GET['direction'][] = "DESC";
}
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page

$list->selectPage($_GET['page']);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

//$_SESSION[submenu] = New Submenu();
//$_SESSION[submenu]->addItem("Updatelog ophalen","updateHistorySync.php");
//session_write_close();

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>

<?=$list->filterHeader();?>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php

while($data = $list->getRow())
{
  // $list->buildRow($data,$template="",$options="");
  $data["updates.terugmelding"]["value"] = rClip($data["updates.terugmelding"]["value"], 50);
  if ($data["updates.complete"]["value"] == 1)
  {
    $data["tr_class"] = "list_dataregel_groen";
  }
  if ($data["updates.complete"]["value"] == 2)
  {
    $data["tr_class"] = "list_dataregel_rood";
  }
  if ($data["updates.complete"]["value"] == 99)
  {
    $data["tr_class"] = "list_dataregel_oranje";
  }
  echo $list->buildRow($data);
}


?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
