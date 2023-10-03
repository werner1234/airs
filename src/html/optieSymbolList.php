<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 18 november 2005
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2015/01/30 15:17:42 $
    File Versie         : $Revision: 1.2 $
*/

include_once("wwwvars.php");


include_once("../classes/mysqlList.php");
$data = array_merge($_GET, $_POST);
$list = new MysqlList2();

if( ! isset ($data['sort']) ) {
  $data['sort'][]      = "fondsOptieSymbolen.key";
  $data['direction'][] = "DESC";
}


$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "optieSymbolEdit.php";

$allow_add = false;
if(checkAccess($type)) {$allow_add = true;}// superusers

$list->idField = "id";
$list->editScript = $editScript;
$__appvar['rowsPerPage']=50;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("fondsOptieSymbolen","key",array("list_width"=>"100","search"=>true));
$list->addFixedField("fondsOptieSymbolen","Fonds",array("list_width"=>"100","search"=>true));
$list->addFixedField("fondsOptieSymbolen","aantal",array("list_width"=>"100","search"=>false));
$list->addFixedField("fondsOptieSymbolen","optieBeurs",array("list_width"=>"100","search"=>false));



$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($list->getCustomFields('fondsOptieSymbolen'),"");



// set sort 
$list->setOrder($data['sort'],$data['direction']);
// set searchstring
$list->setSearch($data['selectie']);
// select page
$list->selectPage($data['page']);



session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content['javascript'] .= "
function addRecord() {
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
 echo $list->buildRow($data);
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>