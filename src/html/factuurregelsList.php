<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 8 april 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/04/08 18:20:37 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: factuurregelsList.php,v $
    Revision 1.1  2017/04/08 18:20:37  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("factuurregels");
$mainHeader    = vt("overzicht");

$editScript = "factuurregelsEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("Factuurregels","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("Factuurregels","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("Factuurregels","datum",array("list_width"=>"100","search"=>false));
$list->addFixedField("Factuurregels","omschrijving",array("list_width"=>"100","search"=>false));
$list->addFixedField("Factuurregels","bedrag",array("list_width"=>"100","search"=>false));
$list->addFixedField("Factuurregels","btw",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('Factuurregels');

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>

<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?php
echo $list->printHeader();;
while( $data = $list->getRow() ) {
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
