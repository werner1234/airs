<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 september 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/11/22 14:29:16 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: biccodesList.php,v $
    Revision 1.2  2015/11/22 14:29:16  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "biccodesEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("BICcodes","vermogensbeheerder",array("list_width"=>"150","search"=>false));
$list->addColumn("BICcodes","code",array("list_width"=>"100","search"=>true));
$list->addColumn("BICcodes","naam",array("list_width"=>"100","search"=>false));
$list->addColumn("BICcodes","BICcode",array("list_width"=>"100","search"=>false));
$list->addColumn("BICcodes","PSET",array("list_width"=>"100","search"=>false));
$list->addColumn("BICcodes","PSAF",array("list_width"=>"100","search"=>false));
$list->addColumn("BICcodes","change_user",array("list_width"=>"100","search"=>false));
$list->addColumn("BICcodes","change_date",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('BICcodes'),'BICcodes');

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

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

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

<br>
<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
</form>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
