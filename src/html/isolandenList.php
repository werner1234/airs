<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 23 februari 2019
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/02/27 13:48:29 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: isolandenList.php,v $
    Revision 1.2  2019/02/27 13:48:29  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "isolandenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("ISOLanden","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("ISOLanden","landCode",array("list_width"=>"150","search"=>false));
$list->addFixedField("ISOLanden","landCodeKort",array("list_width"=>"150","search"=>false));
$list->addFixedField("ISOLanden","landISOnr",array("list_width"=>"100","search"=>false));
//$list->addColumn("ISOLanden","omschrijvingNL",array("list_width"=>"100","search"=>false));
//$list->addColumn("ISOLanden","omschrijvingEN",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('ISOLanden','ISOLanden');

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

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
  
  <br>
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
if($__debug) {
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
