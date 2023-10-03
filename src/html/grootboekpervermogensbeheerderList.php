<?php
/*
    AE-ICT CODEX source module versie 1.6, 3 september 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:39 $
    File Versie         : $Revision: 1.3 $

    $Log: grootboekpervermogensbeheerderList.php,v $
    Revision 1.3  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.2  2010/07/11 15:58:35  rvv
    *** empty log message ***

    Revision 1.1  2008/05/16 07:55:46  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "grootboekpervermogensbeheerderEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];



$list->addColumn("GrootboekPerVermogensbeheerder","id");
$list->addColumn("GrootboekPerVermogensbeheerder","Vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addColumn("GrootboekPerVermogensbeheerder","StartDatum",array("list_width"=>"100","search"=>false));
$list->addColumn("GrootboekPerVermogensbeheerder","Grootboekrekening",array("width"=>150,"search"=>true));
$list->addColumn("GrootboekPerVermogensbeheerder","Omschrijving",array("search"=>true));
$list->addColumn("GrootboekPerVermogensbeheerder","FondsAanVerkoop",array("width"=>130,"align"=>"center","search"=>false));
$list->addColumn("GrootboekPerVermogensbeheerder","Storting",array("width"=>130,"align"=>"center","search"=>false));
$list->addColumn("GrootboekPerVermogensbeheerder","Onttrekking",array("width"=>130,"align"=>"center","search"=>false));
$list->addColumn("GrootboekPerVermogensbeheerder","Kosten",array("width"=>130,"align"=>"center"));
$list->addColumn("GrootboekPerVermogensbeheerder","Opbrengst",array("width"=>130,"align"=>"center"));
$list->addColumn("GrootboekPerVermogensbeheerder","Beginboeking",array("width"=>130,"align"=>"center"));
$list->addColumn("GrootboekPerVermogensbeheerder","Kruispost",array("width"=>130,"align"=>"center"));

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));


$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem('Import Grootboek','grootboekpervermogensbeheerderImport.php');

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