<?php
/*
    AE-ICT CODEX source module versie 1.6, 18 januari 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/11/12 16:40:11 $
    File Versie         : $Revision: 1.3 $

    $Log: updateinformatieList.php,v $
    Revision 1.3  2014/11/12 16:40:11  rvv
    *** empty log message ***

    Revision 1.2  2013/01/20 13:26:08  rvv
    *** empty log message ***

    Revision 1.1  2012/01/18 18:53:40  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "updateinformatieEdit.php";
$allow_add  = $__appvar['master']  ;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("UpdateInformatie","id",array("list_width"=>"100","search"=>false));
$list->addField("UpdateInformatie","versie",array("list_width"=>"100","search"=>true));
$list->addField("UpdateInformatie","informatie",array("list_width"=>"500","search"=>true));

if(!isset($list->sortOptions[0]))
  $list->sortOptions[0]=Array('veldnaam'=>'updateInformatie.versie','methode'=>'DESC');
  


// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

$list->setFilter();
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
<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  if(strlen($data['informatie']['value']) > 80)
    $data['informatie']['value']=substr($data['informatie']['value'],0,80)."...";
	// $list->buildRow($data,$template="",$options="");
	echo $list->buildRow($data);
}
?>
</table>
</form>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>