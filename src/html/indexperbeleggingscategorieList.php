<?php
/*
    AE-ICT CODEX source module versie 1.6, 6 mei 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/07/01 17:20:46 $
    File Versie         : $Revision: 1.7 $

    $Log: indexperbeleggingscategorieList.php,v $
    Revision 1.7  2017/07/01 17:20:46  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Index per Beleggingscategorie");

$editScript = "indexperbeleggingscategorieEdit.php";
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

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("IndexPerBeleggingscategorie","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("IndexPerBeleggingscategorie","Vermogensbeheerder",array("list_width"=>"100","search"=>true));
$list->addFixedField("IndexPerBeleggingscategorie","Beleggingscategorie",array("list_width"=>"200","search"=>false));
$list->addFixedField("IndexPerBeleggingscategorie","Fonds",array("list_width"=>"200","search"=>false));
$list->addFixedField("IndexPerBeleggingscategorie","vanaf",array("list_width"=>"200","search"=>false));
$list->addFixedField("IndexPerBeleggingscategorie","Portefeuille",array("list_width"=>"200","search"=>true));
$list->addFixedField("IndexPerBeleggingscategorie","Categoriesoort",array("list_width"=>"200","search"=>true));
$list->addFixedField("IndexPerBeleggingscategorie","Categorie",array("list_width"=>"200","search"=>true));
//$list->addColumn("IndexPerBeleggingscategorie","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("IndexPerBeleggingscategorie","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("IndexPerBeleggingscategorie","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("IndexPerBeleggingscategorie","change_user",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('IndexPerBeleggingscategorie','IndexPerBeleggingscategorie');
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

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
