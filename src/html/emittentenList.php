<?php
/*
    AE-ICT CODEX source module versie 1.6, 26 februari 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/10/13 17:16:37 $
    File Versie         : $Revision: 1.4 $

    $Log: emittentenList.php,v $
    Revision 1.4  2018/10/13 17:16:37  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "emittentenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("Emittenten","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","emittent",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","naam",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","adres",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","woonplaats",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","telefoon",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","fax",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","email",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","contactpersoon",array("list_width"=>"100","search"=>false));
$list->addFixedField("Emittenten","rating",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('Emittenten','Emittenten');

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
<?
logAccess();
if($__debug) {
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
