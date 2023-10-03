<?php
/*
    AE-ICT CODEX source module versie 1.6, 1 december 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2012/03/11 17:19:04 $
    File Versie         : $Revision: 1.7 $

    $Log: keuzepervermogensbeheerderList.php,v $
    Revision 1.7  2012/03/11 17:19:04  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "keuzepervermogensbeheerderEdit.php";
$allow_add  = true;

$list = new MysqlList2();
//$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("KeuzePerVermogensbeheerder","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("KeuzePerVermogensbeheerder","vermogensbeheerder",array("list_width"=>"150","search"=>false));
$list->addFixedField("KeuzePerVermogensbeheerder","categorie",array("list_width"=>"200","search"=>false));
$list->addFixedField("KeuzePerVermogensbeheerder","waarde",array("list_width"=>"200","search"=>false));
$list->addFixedField("KeuzePerVermogensbeheerder","Afdrukvolgorde",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('KeuzePerVermogensbeheerder','KeuzePerV');

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
