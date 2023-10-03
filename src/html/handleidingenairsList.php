<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 4 januari 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/01/04 16:19:18 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: handleidingenairsList.php,v $
    Revision 1.1  2017/01/04 16:19:18  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "handleidingenairsEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->idTable ='handleidingenAIRS';
$list->ownTables=array('handleidingenAIRS');
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("HandleidingenAIRS","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","regels",array("list_width"=>"25","description"=>" ",'list_nobreak'=>true,'list_order'=>false));
$list->addFixedField("handleidingenAIRS","titel",array("list_width"=>"400","search"=>false));
$list->addFixedField("handleidingenAIRS","categorie",array("list_width"=>"200","search"=>false));
$list->addFixedField("handleidingenAIRS","publiceer",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('handleidingenAIRS'),'handleidingenAIRS');


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

function downloadHandleiding(url)
{
   window.open(url);
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

	$realId=$data["id"]["value"];
	$data[".regels"]["value"] .= "<a href=\"#\" onclick=\"javascript:downloadHandleiding('handleidingenairsEdit.php?action=download&id=".$realId."');\">".drawButton("save","","Download")."</a>";

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