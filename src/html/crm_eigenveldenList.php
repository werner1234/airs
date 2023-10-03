<?php
/*
    AE-ICT CODEX source module versie 1.6, 28 april 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/10/07 10:19:21 $
    File Versie         : $Revision: 1.4 $

    $Log: crm_eigenveldenList.php,v $
    Revision 1.4  2018/10/07 10:19:21  rvv
    *** empty log message ***

    Revision 1.3  2018/10/06 17:19:09  rvv
    *** empty log message ***

    Revision 1.2  2014/08/09 15:05:41  rvv
    *** empty log message ***

    Revision 1.1  2012/04/28 15:55:51  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "crm_eigenveldenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("CRM_eigenVelden","id",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_eigenVelden","veldnaam",array("list_width"=>"200","search"=>true));
$list->addColumn("CRM_eigenVelden","omschrijving",array("list_width"=>"200","search"=>true));
$list->addColumn("CRM_eigenVelden","veldtype",array("list_width"=>"200","search"=>true));

$html = $list->getCustomFields(array('CRM_eigenVelden'),"CRM_eigenVelden");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
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
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>