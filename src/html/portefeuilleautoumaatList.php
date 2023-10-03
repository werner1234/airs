<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 februari 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2012/03/09 09:08:56 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: portefeuilleautoumaatList.php,v $
    Revision 1.1  2012/03/09 09:08:56  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Bulkcontrole overzicht");

$editScript = "portefeuilleautoumaatEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 1000;

$list->addColumn("PortefeuilleAutoumaat","id",array("list_width"=>"100","search"=>false));
$list->addColumn("PortefeuilleAutoumaat","add_date",array("list_width"=>"70","description"=>"datum","search"=>false));
$list->addColumn("PortefeuilleAutoumaat","bank",array("list_width"=>"20","search"=>true));
$list->addColumn("PortefeuilleAutoumaat","portefeuille",array("search"=>true));

$list->addColumn("PortefeuilleAutoumaat","fonds",array("list_width"=>"200","search"=>true));
$list->addColumn("PortefeuilleAutoumaat","aantal_airs",array("list_align"=>"right","search"=>true));
$list->addColumn("PortefeuilleAutoumaat","aantal_bank",array("list_align"=>"right","search"=>true));
$list->addColumn("PortefeuilleAutoumaat","file",array("list_width"=>"200","search"=>true));
//$list->addColumn("PortefeuilleAutoumaat","tag",array("list_align"=>"center","list_width"=>"30","search"=>false));


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
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$allow_add));
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
echo template($__appvar["templateContentHeader"],$content);
?>


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
?>