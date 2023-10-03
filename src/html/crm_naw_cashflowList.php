<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 november 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/07/10 15:37:14 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: crm_naw_cashflowList.php,v $
    Revision 1.4  2019/07/10 15:37:14  rvv
    *** empty log message ***

    Revision 1.3  2015/03/04 16:18:51  rvv
    *** empty log message ***

    Revision 1.2  2014/05/29 12:07:22  rvv
    *** empty log message ***

    Revision 1.1  2013/11/17 13:16:20  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "crm_naw_cashflowEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$__appvar['rowsPerPage']=200;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_naw_cashflow","id",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_cashflow","datum",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_cashflow","bedrag",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_cashflow","indexatie",array("description"=>'index %',"list_width"=>"80","search"=>false));
//$list->addColumn("CRM_naw_cashflow","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("CRM_naw_cashflow","add_user",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_cashflow","change_user",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_cashflow","change_date",array("list_width"=>"100","search"=>false));


$rel_id = $_GET['rel_id'];
if ($rel_id > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = '$rel_id'";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";

  $list->setWhere("rel_id = ".$rel_id);

 // $_SESSION['submenu'] = New Submenu();
 // $_SESSION['submenu']->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");
}

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

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new&rel_id=$rel_id';
}
";
echo template($__appvar["templateContentHeader"],$content);

if($_GET['frame']==1)
{
  echo "<a href='crm_naw_cashflowEdit.php?action=new&frame=1&rel_id=".$rel_id."'>
  <img src=\"icon/16/add.png\" class=\"simbisIcon\"> Cashflow toevoegen
  </a>";
  unset($_SESSION['NAV']);
}
?>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
  $data['id']['value']=$data['id']['value']."&frame=1";
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