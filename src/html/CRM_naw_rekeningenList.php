<?php
/*
    AE-ICT CODEX source module versie 1.6, 3 februari 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/08/27 15:44:57 $
    File Versie         : $Revision: 1.8 $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt('overzicht');

$editScript = "CRM_naw_rekeningenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_naw_rekeningen","id",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_rekeningen","rekening",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_rekeningen","IBAN",array("list_width"=>"200","search"=>false));
$list->addColumn("CRM_naw_rekeningen","bank",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_rekeningen","omschrijving",array("list_width"=>"300","search"=>false));

$deb_id = $_GET['deb_id'];
if ($deb_id > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " " . vt('bij') . " <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";

  $list->setWhere("rel_id = ".$deb_id);

 // $_SESSION['submenu'] = New Submenu();
 // $_SESSION['submenu']->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");
}

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
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
  $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Kopieer rekeningen"),"CRM_naw_dossierCopy.php?tabel=CRM_naw_rekeningen&relid=$deb_id");
  
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