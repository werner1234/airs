<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "grootboekrekeningEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("Grootboekrekening","id");
$list->addField("Grootboekrekening","Grootboekrekening",array("width"=>150,"search"=>true));
$list->addField("Grootboekrekening","Omschrijving",array("search"=>true));
$list->addField("Grootboekrekening","FondsAanVerkoop",array("width"=>130,"align"=>"center","search"=>false));
$list->addField("Grootboekrekening","Storting",array("width"=>130,"align"=>"center","search"=>false));
$list->addField("Grootboekrekening","Onttrekking",array("width"=>130,"align"=>"center","search"=>false));
$list->addField("Grootboekrekening","Kosten",array("width"=>130,"align"=>"center"));
$list->addField("Grootboekrekening","Opbrengst",array("width"=>130,"align"=>"center"));
$list->addField("Grootboekrekening","Beginboeking",array("width"=>130,"align"=>"center"));
$list->addField("Grootboekrekening","Kruispost",array("width"=>130,"align"=>"center"));
$list->addField("Grootboekrekening","FondsGebruik",array("width"=>130,"align"=>"center"));

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
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
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
?>