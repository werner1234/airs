<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$editScript = "aabtransaktiecodesEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("AABTransaktieCodes","id",array("width"=>"100","search"=>false));
$list->addField("AABTransaktieCodes","code",array("width"=>"100","search"=>true));
$list->addField("AABTransaktieCodes","actie",array("width"=>"","search"=>true));
$list->addField("AABTransaktieCodes","toelichting",array("width"=>"","search"=>false));
$list->addField("AABTransaktieCodes","change_date",array("width"=>"100","search"=>false, "description"=>"mutatiedatum"));


// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);
$allow_add = true;
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

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
while($data = $list->getRow())
{
   
  $actie = $data["actie"]["value"];
  
  $data["actie"]["value"] = $__appvar["AABTransakties"][$actie];
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
?>