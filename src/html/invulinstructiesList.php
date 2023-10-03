<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 17 juni 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2016/06/20 08:20:20 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: invulinstructiesList.php,v $
    Revision 1.1  2016/06/20 08:20:20  cvs
    call 5027 invulinstructies

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();


$_SESSION["invulListUrl"] = $_SERVER["REQUEST_URI"];
$subHeader     = "";
$mainHeader    = vt("Invul instructies overzicht");

$editScript = "invulinstructiesEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("InvulInstructies","id",array("search"=>false));
$list->addColumn("InvulInstructies","vermogensBeheerder",array("description"=>"VB","search"=>true));
$list->addColumn("InvulInstructies","script",array("search"=>true,"list_width"=>200));
$list->addColumn("InvulInstructies","field",array("description"=>"veld","search"=>true));
$list->addColumn("InvulInstructies","value",array("description"=>"waarde","search"=>false));
$list->addColumn("InvulInstructies","active",array("description"=>"AC","search"=>false));
$list->addColumn("InvulInstructies","text",array("search"=>false));
$list->addColumn("InvulInstructies","header",array("search"=>false));
$list->addColumn("InvulInstructies","class",array("search"=>false));
//$list->addColumn("","copy",array("search"=>false, "description" =>"kopie"));



// set default sort
// $_GET['sort'][]      = "tablename.field";
// $_GET['direction'][] = "ASC";
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


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	$data['copy']['value']="<a href='$editScript?action=new&kopie=".$data['id']['value']."'><button>kopieer</button></a>";
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