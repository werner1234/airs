<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 oktober 2011
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2012/06/06 10:05:12 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: CRM_uur_activiteitenList.php,v $
    Revision 1.2  2012/06/06 10:05:12  cvs
    factuurregels uit CRM_uren

    Revision 1.1  2011/10/22 06:45:09  cvs
    Urenregistratie voor TRA

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "ActiviteitenCodes overzicht";

$editScript = "CRM_uur_activiteitenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_uur_activiteiten","id",array("list_width"=>"100","search"=>false));
//$list->addColumn("CRM_uur_activiteiten","change_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("CRM_uur_activiteiten","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("CRM_uur_activiteiten","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("CRM_uur_activiteiten","add_date",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_uur_activiteiten","code",array("list_width"=>"65","search"=>true));
$list->addColumn("CRM_uur_activiteiten","omschrijving",array("list_width"=>"500","search"=>true));
$list->addColumn("CRM_uur_activiteiten","uurtarief",array("list_width"=>"100","search"=>true));


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