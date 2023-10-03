<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$editScript = "fondsparticipatieverloopEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("FondsParticipatieVerloop","id",array("width"=>100,"search"=>false));
$list->addField("FondsParticipatieVerloop","Fonds",array("width"=>100,"search"=>true));
$list->addField("FondsParticipatieVerloop","Datum",array("width"=>100,"search"=>false));
$list->addField("FondsParticipatieVerloop","Transactietype",array("width"=>100,"search"=>false));
$list->addField("FondsParticipatieVerloop","Aantal",array("width"=>100,"search"=>false));

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

if(!empty($Fonds))
{
	$list->setWhere(" Fonds = '".$Fonds."' ");
}

$DB = new DB();
$DB->SQL("SELECT Fonds FROM Fondsen WHERE Huisfonds > 0 ORDER BY Fonds ASC");
$DB->Query();
while($data = $DB->NextRecord())
{
	$options .= "<option value=\"".$data['Fonds']."\" ".($Fonds==$data['Fonds']?"selected":"").">".$data['Fonds']."</option>\n";
}

if(empty($_GET['sort'])) {
	$_GET['sort'] = array("Datum");
	$_GET['direction'] = array("DESC");
}

// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

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
<form action="fondsparticipatieverloopList.php" method="GET"  name="controleForm">
Fonds :
<select name="Fonds" onChange="document.controleForm.submit();">
<option value="">--</option>
<?=$options?>
</select>
<input type="submit" value="Overzicht">
</form>
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