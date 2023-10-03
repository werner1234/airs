<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 april 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/09/27 11:36:44 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: importafwijkingenList.php,v $
    Revision 1.2  2017/09/27 11:36:44  cvs
    no message

    Revision 1.1  2017/03/24 09:35:57  cvs
    call 5731

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("Transactie import uitzonderingen");
$mainHeader    = vt('overzicht');

$editScript = "importafwijkingenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("ImportAfwijkingen","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("ImportAfwijkingen","actief",array("list_width"=>"50","description"=>"ACT", "list_align"=>"center"));
$list->addFixedField("ImportAfwijkingen","depotbank",array("description"=>"Depot", "search"=>true));
$list->addFixedField("ImportAfwijkingen","vermogensBeheerder",array("description"=>"VB","search"=>true));
$list->addFixedField("ImportAfwijkingen","functie",array("search"=>true));
$list->addFixedField("ImportAfwijkingen","subInFunctie",array("search"=>true));

$html = $list->getCustomFields(array('importAfwijkingen'));
$list->ownTables=array('importAfwijkingen');



$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

// set default sort
$_GET['sort'][]      = "importAfwijkingen.prio";
$_GET['direction'][] = "ASC";
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
      while($data = $list->getRow())
      {
        echo $list->buildRow($data);
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