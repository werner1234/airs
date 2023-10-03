<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 29 december 2018
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/01/06 15:45:30 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: fondsenoptiestatistiekenList.php,v $
    Revision 1.2  2019/01/06 15:45:30  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "fondsenoptiestatistiekenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("FondsenOptiestatistieken","fonds",array("list_width"=>"200","search"=>false));
$list->addFixedField("FondsenOptiestatistieken","datum",array("list_width"=>"100","search"=>false));
/*
$list->addFixedField("FondsenOptiestatistieken","delta",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsenOptiestatistieken","omega",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsenOptiestatistieken","theta",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsenOptiestatistieken","vega",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsenOptiestatistieken","gamma",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsenOptiestatistieken","rho",array("list_width"=>"100","search"=>false));
*/

$html = $list->getCustomFields(array('FondsenOptiestatistieken','Fonds'),'FondsenOptiestatistieken');
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

foreach ($list->columns as $colData)
{
  if($colData['objectname'] == 'Fonds')
  {
    $joinFondsen=" LEFT JOIN Fondsen ON fondsenOptiestatistieken.fonds = Fondsen.Fonds";
  }
}
$list->ownTables=array('fondsenOptiestatistieken');
$list->setJoin("$joinFondsen ");



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

$content["javascript"] .= "
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
