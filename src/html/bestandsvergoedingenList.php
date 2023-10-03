<?php
/*
    AE-ICT CODEX source module versie 1.6, 16 april 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/09/28 18:44:42 $
    File Versie         : $Revision: 1.5 $

    $Log: bestandsvergoedingenList.php,v $
    Revision 1.5  2011/09/28 18:44:42  rvv
    *** empty log message ***

    Revision 1.4  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.3  2011/05/29 06:35:44  rvv
    *** empty log message ***

    Revision 1.2  2011/05/18 16:50:14  rvv
    *** empty log message ***

    Revision 1.1  2011/04/17 08:56:16  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "bestandsvergoedingenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->idTable = "Bestandsvergoedingen";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("Bestandsvergoedingen","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","regels",array("list_width"=>"30","description"=>" ",'list_nobreak'=>true,'list_order'=>false));
$list->addColumn("Bestandsvergoedingen","vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","emittent",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","depotbank",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","datumBerekend",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","waardeBerekend",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","datumHerrekend",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","waardeHerrekend",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","datumGeaccordeerd",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","datumOntvangen",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","datumUitbetaald",array("list_width"=>"100","search"=>false));
//$list->addColumn("Bestandsvergoedingen","status",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("Bestandsvergoedingen","add_user",array("list_width"=>"100","search"=>false));
$list->addColumn("Bestandsvergoedingen","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("Bestandsvergoedingen","change_user",array("list_width"=>"100","search"=>false));


// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

$list->setFilter();
// select page
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>


<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{

  $data[".regels"]["value"] = "<a href=bestandsvergoedingperportefeuilleList.php?bestandsvergoedingId=".$data["id"]["value"].">".drawButton("orderregels","","")."</a>";
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