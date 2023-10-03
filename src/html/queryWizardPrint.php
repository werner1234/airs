<?php
/*
    AE-ICT sourcemodule created 14 okt. 2020
    Author              : Chris van Santen
    Filename            : queryWizardPrint.php


*/

include_once("wwwvars.php");
include_once("../classes/AE_cls_listCSV.php");
include_once("../classes/mysqlList.php");

$editScript = "beleggingssectorEdit.php";

switch($_GET['type'])
{
	case "pdf" :
		$list = new ListPDF();
	break;
	case "csv" :
		$list = new ListCSV();
	break;
	default :
		$list = new MysqlList();

	break;
}

session_start();
$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem(vt("Query Wizard"),"queryWizard.php");
$_SESSION["submenu"]->addItem("<br>","");
$_SESSION["submenu"]->addItem(vt("Opslaan als CSV"),"queryWizardPrint.php?type=csv");
session_write_close();

$list->idField = "id";

$list->perPage = $__appvar['rowsPerPage'];

session_start();
$queryWizard = $_SESSION["queryWizard"];
session_write_close();

for($a = 0; $a < count($queryWizard["fields"]); $a++)
{
	//
	if($queryWizard["fields"][$a] <> $queryWizard["groupby"][0]["actionField"])
	{
		$list->addColumn($queryWizard["object"],$queryWizard["fields"][$a]);
	}
	else
	{
		$options["sql_alias"] = $queryWizard["groupby"][0]["actionType"]."(".$queryWizard["groupby"][0]["actionField"].")";
		$options["list_align"] = "right";
		$list->addColumn("",$queryWizard["groupby"][0]["actionType"]."_".$queryWizard[fields][$a],$options);
	}
}
$allow_add = false;

if(count($queryWizard["where"]) > 0)
{
	for($a = 0; $a < count($queryWizard["where"]); $a++)
	{
		$andor = "";
		if((count($queryWizard["where"])) > ($a+1))
		{
			$andor = $queryWizard["where"][$a]["andor"];
		}

		if($queryWizard["where"][$a]["operator"] == "LIKE")
			$where .= $queryWizard["where"][$a]["field"]." ".$queryWizard["where"][$a]["operator"]." '%".$queryWizard["where"][$a]["search"]."%' ".$andor." ";
		else
			$where .= $queryWizard["where"][$a]["field"]." ".$queryWizard["where"][$a]["operator"]." '".$queryWizard["where"][$a]["search"]."' ".$andor." ";
	}

	$list->setWhere("(".$where.")");
}

if(count($queryWizard["groupby"]) > 0)
{
		$list->setGroupBy($queryWizard[groupby][0][field]);
}

if(count($queryWizard["orderby"]) > 0)
{
	for($a = 0; $a < count($queryWizard["orderby"]); $a++)
	{
		$_GET['sort'][] = $queryWizard["orderby"][$a]["field"];
		if(!empty($queryWizard["orderby"][$a]["order"]))
			$_GET['direction'][] = $queryWizard["orderby"][$a]["order"];
	}
}
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);

if($_GET['type'] == "screen")
{

	// select page
	$list->selectPage($_GET['page']);


	session_start();
	$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
	$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
	//$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));


	$content[javascript] .= "
	function addRecord()
	{
		parent.frames['content'].location = '".$editScript."?action=new';
	}
	";
	echo template($__appvar["templateContentHeader"],$content);

	// get session data
	for($a=0;$a<count($_SESSION["queryWizard"]["where"]);$a++)
	{
		$where .= $_SESSION["queryWizard"]["where"][$a]["field"]." ".$_SESSION["queryWizard"]["where"][$a]["operator"]." '".$_SESSION["queryWizard"]["where"][$a]["search"]."' ".$_SESSION["queryWizard"]["where"][$a]["andor"];
	}
	for($a=0;$a<count($_SESSION["queryWizard"]["orderby"]);$a++)
	{
		$order .= $_SESSION["queryWizard"]["orderby"][$a]["field"]." ";
	}
	for($a=0;$a<count($_SESSION["queryWizard"]["groupby"]);$a++)
	{
		$groep .= " op ".$_SESSION["queryWizard"]["groupby"][$a]["field"]." & ".$_SESSION["queryWizard"]["groupby"][$a]["actionType"]." op ".$_SESSION["queryWizard"]["groupby"][$a]["actionField"]." ";
	}
?>
<br>
<table border="0" style="border: 1px solid Gray">
<tr>
	<td colspan="2"><b><?=vt("Query opbouw")?></b></td>
</tr>
<tr>
	<td align="right" width="100"><a href="queryWizard.php?setValue=step&step=0"><u><?=vt("tabel")?></u></a> : </td>
	<td><?=$_SESSION["queryWizard"]["object"]?></td>
</tr>
<tr>
	<td align="right"><a href="queryWizard.php?setValue=step&step=1"><u><?=vt("velden")?></u></a> : </td>
	<td><?=implode(", ",$_SESSION["queryWizard"]["fields"])?></td>
</tr>
<tr>
	<td align="right"><a href="queryWizard.php?setValue=step&step=2"><u><?=vt("selectie")?></u></a> : </td>
	<td><?=$where?></td>
</tr>
<tr>
	<td align="right"><a href="queryWizard.php?setValue=step&step=3"><u><?=vt("sortering")?></u></a> : </td>
	<td><?=$order?></td>
</tr>
<tr>
	<td align="right"><a href="queryWizard.php?setValue=step&step=4"><u><?=vt("groep")?></u></a> : </td>
	<td><?=$groep?></td>
</tr>
</table>
<br>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
	session_write_close();

	while($data = $list->printRow())
	{
		echo $data;
	}
?>
	</table>
<?
	if($__debug)
	{
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}
else if($_GET['type'] == "csv")
{
	$file = date("d-m-Y",mktime())."-queryWizard.csv";
	header('Pragma: public');
	header('Content-type: text/comma-separated-values');
	header('Content-disposition: attachment; filename='.$file);
	echo $list->getCSV();
}

