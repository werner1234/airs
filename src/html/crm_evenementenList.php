<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " " . vt('overzicht') . "";

$editScript = "crm_evenementenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_evenementen","id",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_evenementen","evenement",array("list_width"=>"300","search"=>false));
$list->addColumn("CRM_evenementen","add_date",array("list_width"=>"300","search"=>false));

$deb_id = $_GET['deb_id'];
if ($deb_id > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " " . vt('bij') . " <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";

  $list->setWhere("rel_id = ".$deb_id);

  //$_SESSION[submenu] = New Submenu();
  //$_SESSION[submenu]->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");
}
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
if($_GET['sort']=='')
{
  $_GET['sort'][]='add_date';
  $_GET['direction'][]='DESC';
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

if($_GET['action'] == 'xls')
{
  $list->setXLS();
  $list->getXLS();
}
else
{
  if(!is_a($_SESSION['submenu'],'Submenu'))
    $_SESSION['submenu']=new Submenu();
  $_SESSION['submenu']->addItem(vt("XLS-lijst"),"$PHP_SELF?action=xls&".$_SERVER['QUERY_STRING']);
//  $_SESSION['submenu']->addItem("<br>","");
//  $_SESSION['submenu']->addItem(vt("Kopieer evenementen"),"CRM_naw_dossierCopy.php?tabel=CRM_evenementen&relid=$deb_id");
  
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new&rel_id=$deb_id';
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
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);
}
?>