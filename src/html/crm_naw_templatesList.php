<?php
/*
    AE-ICT CODEX source module versie 1.6, 6 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/10/22 15:48:06 $
    File Versie         : $Revision: 1.8 $

    $Log: crm_naw_templatesList.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "crm_naw_templatesEdit.php";
$allow_add  = true;

if(!$_POST['sort_0_veldnaam'])
{
    $_POST['sort_0_veldnaam'] = 'CRM_naw_templates.change_date';
    $_POST['sort_0_methode'] = 'DESC';
}


$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->idTable='CRM_naw_templates';
//$list->addFixedField("CRM_naw_templates","id",array("list_width"=>"100","search"=>false));
//$list->addColumn("CRM_naw_templates","tabs",array("list_width"=>"100","search"=>false));
$list->addFixedField("CRM_naw_templates","change_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("CRM_naw_templates","change_date",array("list_width"=>"150","search"=>false));
$list->addFixedField("CRM_naw_templates","add_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("CRM_naw_templates","add_date",array("list_width"=>"150","search"=>false));
$list->addFixedField("CRM_naw_templates","intake",array("list_width"=>"150","search"=>false));
$list->addFixedField("CRM_naw_templates","intakeOmschrijving",array("list_width"=>"150","search"=>false));
$list->addField("","size",array('description'=>'size(kb)',"list_width"=>"50","search"=>false,'sql_alias'=>'round(CHAR_LENGTH(CRM_naw_templates.tabs)/1000)'));

$html = $list->getCustomFields(array('CRM_naw_templates'),'CRM_naw_templatesList');

//$_SESSION['submenu'] = New Submenu();
//$_SESSION['submenu']->addItem($html,"");

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
<br />
<form name="editForm" method="POST">
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
?>