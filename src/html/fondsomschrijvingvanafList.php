<?php
/*
    AE-ICT CODEX source module versie 1.6, 13 juni 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/06/13 15:19:26 $
    File Versie         : $Revision: 1.3 $

    $Log: fondsomschrijvingvanafList.php,v $
    Revision 1.3  2018/06/13 15:19:26  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "fondsomschrijvingvanafEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("FondsOmschrijvingVanaf","Fonds",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsOmschrijvingVanaf","Omschrijving",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsOmschrijvingVanaf","Vanaf",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('FondsOmschrijvingVanaf','FondsOmschVanafList');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

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
