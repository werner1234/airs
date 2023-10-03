<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 9 juli 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/04/07 15:23:45 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: portefeuilleclustersList.php,v $
    Revision 1.3  2018/04/07 15:23:45  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

if($_GET['frame']==1)
{
  unset($_SESSION['PortefeuilleClusters']);
}

$subHeader     = vt("portefeuille clusters");
$mainHeader    = vt("overzicht");

$editScript = "portefeuilleclustersEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("PortefeuilleClusters","vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortefeuilleClusters","cluster",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortefeuilleClusters","clusterOmschrijving",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortefeuilleClusters","portefeuille1",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortefeuilleClusters","portefeuille2",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortefeuilleClusters","portefeuille3",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortefeuilleClusters","portefeuille4",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields('PortefeuilleClusters');

if($_GET['portefeuille']<>'')
{
	$where='';
	for($i=1;$i<31;$i++)
	{
		if($where<>'')
			$where.=" OR ";
		$where.=" portefeuille$i='".mysql_real_escape_string($_GET['portefeuille'])."'";
	}
	$list->setWhere("( $where )");
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

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$nrOfRecords=$list->records();
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $nrOfRecords, $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "

function addRecordFrame() 
{
	location =  '".$editScript."?action=new&frame=1&portefeuille=".$_GET['portefeuille']."';
}

function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}


function editRecord(url) 
{
	location = url+'&portefeuille=".$_GET['portefeuille']."';
}
";
echo template($__appvar["templateContentHeader"],$content);

if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 && $nrOfRecords==0)
	echo '<a href="#" onclick="addRecordFrame()"><span title="record toevoegen"><img src="icon/16/add.png" class="simbisIcon"> ' . vt('toevoegen') . '</span> </a><br><br>';

?>
	<form name="editForm" method="POST">
<?=$list->filterHeader();?>
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
	</form>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
