<?php
/*
    AE-ICT CODEX source module versie 1.6, 6 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/11/02 15:17:19 $
    File Versie         : $Revision: 1.2 $

    $Log: crm_naw_dossier_templatesList.php,v $
    Revision 1.2  2019/11/02 15:17:19  rvv
    *** empty log message ***

    Revision 1.1  2016/11/20 10:19:03  rvv
    *** empty log message ***

    Revision 1.8  2014/10/22 15:48:06  rvv
    *** empty log message ***

    Revision 1.7  2014/10/09 11:13:29  rvv
    *** empty log message ***

    Revision 1.6  2014/09/27 16:03:59  rvv
    *** empty log message ***

    Revision 1.5  2014/09/20 17:23:59  rvv
    *** empty log message ***

    Revision 1.4  2014/08/30 16:28:19  rvv
    *** empty log message ***

    Revision 1.3  2011/09/25 16:27:06  rvv
    *** empty log message ***

    Revision 1.2  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.1  2011/08/07 09:19:02  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "crm_naw_dossier_templatesEdit.php";
$allow_add  = true;

if(!$_POST['sort_0_veldnaam'])
{
    $_POST['sort_0_veldnaam'] = 'CRM_naw_dossier_templates.change_date';
    $_POST['sort_0_methode'] = 'DESC';
}


$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->idTable='CRM_naw_dossier_templates';
$list->addFixedField("CRM_naw_dossier_templates","omschrijving",array("list_width"=>"200","search"=>false));
$list->addFixedField("CRM_naw_dossier_templates","change_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("CRM_naw_dossier_templates","change_date",array("list_width"=>"150","search"=>false));
$list->addFixedField("CRM_naw_dossier_templates","add_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("CRM_naw_dossier_templates","add_date",array("list_width"=>"150","search"=>false));
$list->addFixedField("CRM_naw_dossier_templates","intake",array("list_width"=>"150","search"=>false));
$list->addField("","size",array('description'=>'size(kb)',"list_width"=>"50","search"=>false,'sql_alias'=>'ceil(CHAR_LENGTH(CRM_naw_dossier_templates.template)/1000)'));

$html = $list->getCustomFields(array('CRM_naw_dossier_templates'),'CRM_naw_dossier_templates');

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