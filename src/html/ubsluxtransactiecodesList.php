<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $

    $Log: ubsluxtransactiecodesList.php,v $
    Revision 1.2  2020/04/10 11:26:05  cvs
    call 8413

    Revision 1.1  2019/12/11 10:57:16  cvs
    call 7606

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader  = "";
$mainHeader = "UBSLUX transactiecodes overzicht";

$editScript = "ubsluxtransactiecodesEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("UbsluxTransactieCodes","id",array("list_width"=>"100","search"=>false));
$list->addColumn("UbsluxTransactieCodes","bankCode",array("list_width"=>"100","search"=>true));
$list->addColumn("UbsluxTransactieCodes","omschrijving",array("list_width"=>"100","search"=>true));
$list->addColumn("UbsluxTransactieCodes","doActie",array("list_width"=>"100","search"=>false));

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

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
