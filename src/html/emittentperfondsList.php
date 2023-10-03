<?php
/*
    AE-ICT CODEX source module versie 1.6, 26 februari 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/10/04 06:01:34 $
    File Versie         : $Revision: 1.7 $

    $Log: emittentperfondsList.php,v $
    Revision 1.7  2018/10/04 06:01:34  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "emittentperfondsEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("EmittentPerFonds","emittent",array("list_width"=>"100","search"=>false));
$list->addColumn("EmittentPerFonds","fonds",array("list_width"=>"200","search"=>false));
$list->addColumn("EmittentPerFonds","vermogensbeheerder",array("list_width"=>"100","search"=>false));
//$list->addColumn("EmittentPerFonds","depotbank",array("list_width"=>"100","search"=>false));
//$list->addColumn("EmittentPerFonds","rekenmethode",array("list_width"=>"200","search"=>false));
//$list->addColumn("EmittentPerFonds","percentage",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields(array('EmittentPerFonds','Fonds','Emittenten'),'EmittentPerFonds');

$list->ownTables=array('emittentPerFonds');

$join =" LEFT Join Fondsen on emittentPerFonds.fonds=Fondsen.Fonds LEFT Join emittenten on emittentPerFonds.emittent=emittenten.emittent";
$list->setJoin($join);

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");


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


<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  //listarray($data);
	// $list->buildRow($data,$template="",$options="");
	$data['emittentPerFonds.rekenmethode']['value']=$data['emittentPerFonds.rekenmethode']['form_options'][$data['emittentPerFonds.rekenmethode']['value']];
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
