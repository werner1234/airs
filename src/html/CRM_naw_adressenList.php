<?php
/*
    AE-ICT CODEX source module versie 1.6, 3 februari 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2013/02/13 17:04:56 $
    File Versie         : $Revision: 1.7 $

    $Log: CRM_naw_adressenList.php,v $
    Revision 1.7  2013/02/13 17:04:56  rvv
    *** empty log message ***

    Revision 1.6  2011/11/05 16:03:45  rvv
    *** empty log message ***

    Revision 1.5  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.4  2010/10/24 10:28:48  rvv
    *** empty log message ***

    Revision 1.3  2010/10/21 16:14:04  rvv
    *** empty log message ***

    Revision 1.2  2010/09/15 09:37:22  rvv
    *** empty log message ***

    Revision 1.1  2010/02/03 17:04:59  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt('overzicht');

$editScript = "CRM_naw_adressenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_naw_adressen","id",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_adressen","naam",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_adressen","naam1",array("list_width"=>"100","search"=>false));
$list->addColumn("","sjabloon",array("list_width"=>"60",'description'=>"sjabloon ",'list_nobreak'=>true));
$list->addColumn("CRM_naw_adressen","adres",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_adressen","pc",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_adressen","plaats",array("list_width"=>"100","search"=>false));

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
  $_SESSION['submenu']->addItem(vt("Kopieer adressen"),"CRM_naw_dossierCopy.php?tabel=CRM_naw_adressen&relid=$deb_id");
  
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
	$data['sjabloon']['value'] = "<a href=\"CRM_naw_rtfMergeList.php?deb_id=$deb_id&adres=".$data['id']['value']."\" ><img src=\"images/16/template.gif\" border=\"0\" align=\"bottom\"></a>";
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