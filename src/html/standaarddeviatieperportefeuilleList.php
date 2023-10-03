<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 november 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2016/12/04 13:22:24 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: standaarddeviatieperportefeuilleList.php,v $
    Revision 1.6  2016/12/04 13:22:24  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("standaard deviatie per portefeuille");
$mainHeader    = vt("overzicht");

$editScript = "standaarddeviatieperportefeuilleEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("StandaarddeviatiePerPortefeuille","id",array("list_width"=>"100","search"=>false));
$list->addColumn("StandaarddeviatiePerPortefeuille","Vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addColumn("StandaarddeviatiePerPortefeuille","Portefeuille",array("list_width"=>"150","search"=>true));
$list->addColumn("StandaarddeviatiePerPortefeuille","Minimum",array("list_width"=>"100","search"=>false));
$list->addColumn("StandaarddeviatiePerPortefeuille","Maximum",array("list_width"=>"100","search"=>false));
$list->addColumn("StandaarddeviatiePerPortefeuille","Norm",array("list_width"=>"100","search"=>false));

if($_GET['portefeuille']<>'')
{
  $list->setWhere("StandaarddeviatiePerPortefeuille.Portefeuille='" . $_GET['portefeuille'] . "'");
}

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

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
function addRecordFrame() 
{
	location =  '".$editScript."?action=new&frame=1&Portefeuille=".$_GET['portefeuille']."';
}
function editRecord(url) 
{
	location = url;
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<?
if($_GET['frame']==1 && $list->records() == 0)
{
	if($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
  {
    echo '<a href="#" onclick="addRecordFrame()"><span title="'.vt("record toevoegen").'"><img src="icon/16/add.png" class="simbisIcon"> '.vt("toevoegen").'</span> </a><br><br>';
  }
}
?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$list->customEdit =true;
while($data = $list->getRow())
{
	$data['extraqs']='frame='.$_GET['frame'];
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
