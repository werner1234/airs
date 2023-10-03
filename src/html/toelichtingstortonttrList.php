<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 22 februari 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/02/22 18:43:08 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: toelichtingstortonttrList.php,v $
    Revision 1.1  2020/02/22 18:43:08  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "toelichtingstortonttrEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("ToelichtingStortOnttr","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("ToelichtingStortOnttr","toelichting",array("list_width"=>"400","search"=>false));
$list->addFixedField("ToelichtingStortOnttr","change_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("ToelichtingStortOnttr","change_date",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('ToelichtingStortOnttr');

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");


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
  echo $list->printHeader();
  while($data = $list->getRow())
  {
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
