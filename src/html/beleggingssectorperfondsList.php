<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/03/07 17:27:08 $
 		File Versie					: $Revision: 1.11 $

 		$Log: beleggingssectorperfondsList.php,v $
 		Revision 1.11  2015/03/07 17:27:08  rvv
 		*** empty log message ***
 		


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$subHeader     = "";
$mainHeader    = vt("Beleggingssectoren per fonds");

$editScript = "beleggingssectorperfondsEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("BeleggingssectorPerFonds","id",array("width"=>100,"search"=>false));
$list->addFixedField("BeleggingssectorPerFonds","Vermogensbeheerder",array("list_width"=>100,"search"=>false));
$list->addFixedField("BeleggingssectorPerFonds","Fonds",array("list_width"=>200, "search"=>true));
$list->addFixedField("BeleggingssectorPerFonds","Beleggingssector",array("list_width"=>110,"search"=>true));
$list->addFixedField("BeleggingssectorPerFonds","Regio",array("list_width"=>110,"search"=>true));
$list->addFixedField("BeleggingssectorPerFonds","AttributieCategorie",array("list_width"=>110,"search"=>true));
$list->addFixedField("BeleggingssectorPerFonds","Vanaf",array("list_width"=>110,"search"=>false));

$html = $list->getCustomFields(array('BeleggingssectorPerFonds','Fonds'),'BeleggingssectorPerFonds');
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

foreach ($list->columns as $colData)
{
  if($colData['objectname'] == 'Fonds')
  {
    $joinFondsen=" LEFT JOIN Fondsen ON BeleggingssectorPerFonds.Fonds = Fondsen.Fonds";
  }
}
  $list->ownTables=array('BeleggingssectorPerFonds');
  $list->setJoin("$joinFondsen ");

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

if(checkAccess($type))
{
	// superusers
	$allow_add = true;
}
else
{
	// normale user
	$allow_add = false;
}

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content["javascript"] .= "
function addRecord() {
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
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
