<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 18 augustus 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/23 17:14:23 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: api_loggingList.php,v $
    Revision 1.2  2018/09/23 17:14:23  cvs
    call 7175

    Revision 1.1  2017/08/18 14:42:58  cvs
    call 5815

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$__appvar['rowsPerPage'] = 100;
$subHeader     = "";
$mainHeader    = "API logs overzicht";

$editScript = "api_loggingEdit.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("API_logging","id",array("search"=>false));
//$list->addColumn("API_logging","add_user",array("search"=>false));
$list->addColumn("API_logging","add_date",array("search"=>false));
$list->addColumn("","tijd",array("list_width"=>60));
$list->addColumn("API_logging","ip",array("search"=>true));
//$list->addColumn("API_logging","referer",array("search"=>false));
$list->addColumn("API_logging","request",array("search"=>true));
$list->addColumn("API_logging","errors",array("search"=>true));
$list->addColumn("API_logging","results",array("search"=>true));


// set default sort
$_GET['sort'][]      = "id";
$_GET['direction'][] = "DESC";
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
//  debug($data);
  $data["tijd"]["value"] = substr($data["add_date"]["value"],11,8);
	// $list->buildRow($data,$template="",$options="");
  $r = json_decode($data["request"]["value"],true);
  $data["request"]["value"] = "(".strlen($data["request"]["value"]).") ".$r["action"];
  $data["errors"]["value"] = rclip($data["errors"]["value"],25);
  $data["results"]["value"] = rclip($data["results"]["value"],80);
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