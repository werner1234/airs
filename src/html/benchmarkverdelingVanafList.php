<?php
/*
    AE-ICT CODEX source module versie 1.6, 4 december 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/08/09 16:09:48 $
    File Versie         : $Revision: 1.2 $

    $Log: benchmarkverdelingVanafList.php,v $
    Revision 1.2  2017/08/09 16:09:48  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "benchmarkverdelingVanafEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("BenchmarkverdelingVanaf","id",array("list_width"=>"100","search"=>false));
$list->addColumn("BenchmarkverdelingVanaf","benchmark",array("list_width"=>"200","search"=>false));
$list->addColumn("BenchmarkverdelingVanaf","fonds",array("list_width"=>"200","search"=>false));
$list->addColumn("BenchmarkverdelingVanaf","percentage",array("list_width"=>"100","search"=>false));
$list->addColumn("BenchmarkverdelingVanaf","vanaf",array("list_width"=>"100","search"=>false));

//	session_start();
if(checkAccess())
{
	$_SESSION['submenu'] = New Submenu();
	$_SESSION['submenu']->addItem("benchmark berekening","benchmarkVerdeling.php?version=2");
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

$DB = new DB();
$DB->SQL("SELECT benchmark FROM benchmarkverdelingVanaf GROUP BY benchmark ORDER BY benchmark ASC");
$DB->Query();
while($data = $DB->NextRecord())
	$options .= "<option value=\"".$data['benchmark']."\" ".($_GET['benchmark']==$data['benchmark']?"selected":"").">".$data['benchmark']."</option>\n";

echo template($__appvar["templateContentHeader"],$content);
?>

<form method="GET"  name="controleForm">
Benchmark :
<select name="benchmark" onChange="document.controleForm.submit();">
<option value="">--</option>
<?=$options?>
</select>
<br>
<br>

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
$query=" SELECT benchmark,vanaf, SUM(percentage) as totaal  FROM (benchmarkverdelingVanaf) $extraWhere GROUP BY vanaf,benchmark";
$db=new DB();
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  if(round($data['totaal']) <> 100)
    echo $data['benchmark']." op ".date('d-m-Y',db2jul($data['vanaf']))." heeft ".$data['totaal']."% <br>";
}

logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
