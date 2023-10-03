<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 juni 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:40 $
    File Versie         : $Revision: 1.2 $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Tijdelijke conversie positie overzicht overzicht");

$editScript = "tijdelijkepositielijstEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("TijdelijkePositieLijst","id",array("list_width"=>"100","search"=>false));

$list->addColumn("TijdelijkePositieLijst","portefeuille",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkePositieLijst","datum",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","fondsCode",array("list_width"=>"","search"=>true,"description"=>"F.Code"));
$list->addColumn("TijdelijkePositieLijst","fondsCodeNumeriek",array("list_width"=>"","search"=>true,"description"=>"F.Code.Num"));
$list->addColumn("TijdelijkePositieLijst","fondsSoort",array("list_width"=>"","search"=>true,"description"=>"F.Soort"));
$list->addColumn("TijdelijkePositieLijst","fondsOmschrijving",array("list_width"=>"400","search"=>true,"description"=>"Omschrijving"));
$list->addColumn("TijdelijkePositieLijst","fondsValuta",array("list_width"=>"","search"=>false,"description"=>"F.Valuta"));
$list->addColumn("TijdelijkePositieLijst","ISIN",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","aantal",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","optieSoort",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","soort",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","waardeInEUR",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","waardeInValuta",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","koers",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","kostprijs",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","valutakoers",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","batchid",array("list_width"=>"","search"=>false));
$list->addColumn("TijdelijkePositieLijst","vermogensbeheerder",array("list_width"=>"","search"=>false, "description"=>"beh"));
$list->addColumn("TijdelijkePositieLijst","depotbank",array("list_width"=>"","search"=>false,"description"=>"dep"));
$list->addColumn("TijdelijkePositieLijst","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("TijdelijkePositieLijst","add_date",array("list_width"=>"100","search"=>false));

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 

$list->setWhere(" add_user = '".$USR."' ");
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));



if ($list->records() <> 0)
{
	$_SESSION['submenu'] = New Submenu();
	$_SESSION['submenu']->addItem(vt("Verwerken"),"tijdelijkePositieVerwerk.php");
	$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Lijst leegmaken"),"tijdelijkePositieDrop.php");
  $_SESSION['submenu']->addItem("<br>","");
}

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