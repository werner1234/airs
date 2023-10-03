<?php
/*
    AE-ICT sourcemodule created 19 apr. 2021
    Author              : Chris van Santen
    Filename            : benchmarkverdelingList.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "benchmarkverdelingEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("Benchmarkverdeling","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("Benchmarkverdeling","benchmark",array("list_width"=>"200","search"=>false));
$list->addFixedField("Benchmarkverdeling","fonds",array("list_width"=>"200","search"=>false));
$list->addFixedField("Benchmarkverdeling","percentage",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields('Benchmarkverdeling');

//	session_start();
if(checkAccess())
{
	$_SESSION["submenu"] = New Submenu();
	$_SESSION["submenu"]->addItem(vt("benchmark berekening"),"benchmarkVerdeling.php");
	$_SESSION["submenu"]->addItem(vt("Herbereken MM-indices"),"benchmarkVerdeling.php?version=MM-indices");
  $_SESSION["submenu"]->addItem($html,"");
}
//	session_write_close();

if(!empty($_GET['benchmark']))
{
  $extraWhere=" benchmark = '".$_GET['benchmark']."' ";
	$list->setWhere($extraWhere);
	$extraWhere="WHERE ".$extraWhere;
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

$DB = new DB();
$DB->SQL("SELECT benchmark FROM benchmarkverdeling GROUP BY benchmark ORDER BY benchmark ASC");
$DB->Query();
while($data = $DB->NextRecord())
	$options .= "<option value=\"".$data['benchmark']."\" ".($_GET['benchmark']==$data['benchmark']?"selected":"").">".$data['benchmark']."</option>\n";

echo template($__appvar["templateContentHeader"],$content);
?>

<form method="GET"  name="controleForm">
  <?=vt("Benchmark")?> :
  <select name="benchmark" onChange="document.controleForm.submit();">
    <option value="">--</option>
    <?=$options?>
  </select>
</form>
<br>
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
<br><br>
<?
$query=" SELECT benchmark, SUM(percentage) as totaal  FROM (benchmarkverdeling) $extraWhere GROUP BY benchmark";
$db=new DB();
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  if(round($data['totaal']) <> 100)
    echo $data['benchmark']." heeft ".$data['totaal']."% <br>";
}

logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
