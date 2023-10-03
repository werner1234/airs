<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 8 juni 2011
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2012/03/09 09:23:28 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: importgrootboektoewijzingList.php,v $
    Revision 1.1  2012/03/09 09:23:28  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "Transactie import Grootboek toewijzing overzicht";

$editScript = "importgrootboektoewijzingEdit.php";
$allow_add  = true;

$list = new MysqlList2();
//$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("ImportGrootboekToewijzing","id",array("list_width"=>"","search"=>false, "list_invisible"=>true));
$list->addFixedField("ImportGrootboekToewijzing","depotbank",array("list_width"=>"70","search"=>false, "description"=>"depot"));
$list->addFixedField("ImportGrootboekToewijzing","grootboek",array("list_width"=>"90","search"=>false));
$list->addFixedField("ImportGrootboekToewijzing","tekst",array("list_width"=>"300","search"=>false));

$html = $list->getCustomFields('ImportGrootboekToewijzing');

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
<br>
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