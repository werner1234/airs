<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/29 10:58:16 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: appvertalingList.php,v $

branche vertaling_updateMaster
 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$_SESSION["returnUrl"]["appVertaling"] = $_SERVER["REQUEST_URI"];

$subHeader     = "";
$mainHeader    = vt("Applicatie vertaling");

$editScript = "appvertalingEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 1000;

//$list->addColumn("AppVertaling","id");
$list->addFixedField("AppVertaling","veld",array("search"=>'true'));
$list->addFixedField("AppVertaling","nl",array("search"=>'true'));
$list->addFixedField("AppVertaling","en",array("search"=>'true'));
$list->addFixedField("AppVertaling","orgin",array("search"=>false));
//$list->addColumn("AppVertaling","du",array("search"=>false));
$html = $list->getCustomFields('AppVertaling');
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

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
</div><br>";

if($__appvar['bedrijf']=='HOME') {
  $content['pageHeader'] .= '<a style="    -webkit-user-drag: none;
      box-sizing: border-box;
      cursor: pointer;
      display: inline-block;
      line-height: normal;
      user-select: none;
      vertical-align: middle;
      background-color: #e6e6e6;
      border: transparent;
      border-radius: 2px;
      color: rgba(0,0,0,.8);
      font-size: 100%;
      padding: 0.5em 1em;
      text-decoration: none;" class="btn btn-active" href="appvertalingLoader.php?type=records">Records inlezen</a><br><br>';
}
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