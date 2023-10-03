<?php
/*
    AE-ICT CODEX source module versie 1.6, 14 december 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/06/14 13:59:35 $
    File Versie         : $Revision: 1.3 $

    $Log: afmcategorienList.php,v $
    Revision 1.3  2015/06/14 13:59:35  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "afmcategorienEdit.php";
$allow_add  = checkAccess();

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("AfmCategorien","id",array("list_width"=>"100","search"=>false));
$list->addColumn("AfmCategorien","afmCategorie",array("list_width"=>"100","search"=>false));
$list->addColumn("AfmCategorien","omschrijving",array("list_width"=>"100","search"=>false));
$list->addColumn("AfmCategorien","standaarddeviatie",array("list_width"=>"100","search"=>false));

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
<style>
  .td45{
    font-size: 10px;
  }
</style>

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

$db=new DB();
$query="SELECT * FROM afmCategorien ORDER BY afmCategorie";
$db->SQL($query);
$db->Query();

while($rec=$db->nextRecord())
{
  $values=unserialize($rec['correlatie']);
  $matrix[$rec['id']]=$values;
  $ids[$rec['id']]=$rec['afmCategorie'];
}
$html="<table><tr class='list_kopregel'><td class='list_kopregel_data' >&nbsp;</td>";
foreach ($ids as $header)
{
  $html.="<td class='list_kopregel_data td45' width='50'> $header </td>";
}

$html.="</tr>";

foreach ($matrix as $row=>$cols)
{
  $html.="<tr><td class='list_kopregel_data'>".$ids[$row]."</td>";
  foreach ($ids as $id=>$header)
  {
    $html.="<td class='listTableData' align=right> ".$cols[$id]."</td>";
  }
  $html.="</tr>";
}
$html.="</table><br/><br/><br/><br/>";

echo $html;

echo template($__appvar["templateRefreshFooter"],$content);
