<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/04/07 15:23:45 $
    File Versie         : $Revision: 1.16 $

    $Log: zorgplichtperportefeuilleList.php,v $
    Revision 1.16  2018/04/07 15:23:45  rvv
    *** empty log message ***



*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$subHeader     = vt("Zorgplicht Per Portefeuille");
$mainHeader    = vt("overzicht");
$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$editScript = "zorgplichtperportefeuilleEdit.php";

if($_GET['frame']==1)
{
  unset($_SESSION['ZorgplichtPerPortefeuille']);
}

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("ZorgplichtPerPortefeuille","id",array("width"=>100,"search"=>false));
$list->addFixedField("ZorgplichtPerPortefeuille","Portefeuille",array("width"=>100,"search"=>true));
$list->addFixedField("ZorgplichtPerPortefeuille","Vermogensbeheerder",array("width"=>100,"search"=>true));
$list->addFixedField("ZorgplichtPerPortefeuille","Zorgplicht",array("width"=>100,"search"=>true));
$list->addFixedField("ZorgplichtPerPortefeuille","Minimum",array("width"=>100,"search"=>false));
$list->addFixedField("ZorgplichtPerPortefeuille","Maximum",array("width"=>100,"search"=>false));
$list->addFixedField("ZorgplichtPerPortefeuille","norm",array("width"=>100,"search"=>false));
$list->addFixedField("ZorgplichtPerPortefeuille","Vanaf",array("width"=>100,"search"=>false));
$list->addFixedField("ZorgplichtPerPortefeuille","extra",array("width"=>100,"search"=>false));

$list->categorieVolgorde=array('ZorgplichtPerPortefeuille'=>array('Algemeen'),'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'));
$html = $list->getCustomFields(array('ZorgplichtPerPortefeuille','Portefeuilles'),'ZorgPpPlist');

$joinPortefeuilles='';
foreach ($list->columns as $colData)
{
	if($colData['objectname'] == 'Portefeuilles')
	{
		$joinPortefeuilles=" LEFT JOIN Portefeuilles ON ZorgplichtPerPortefeuille.Portefeuille = Portefeuilles.Portefeuille ";
	}
}

$list->ownTables=array('ZorgplichtPerPortefeuille');
$list->setJoin("$joinPortefeuilles");

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");


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

if($_GET['portefeuille']<>'')
	$list->setWhere("ZorgplichtPerPortefeuille.portefeuille='".$_GET['portefeuille']."'");
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page

$list->selectPage($_GET['page']);


session_start();
$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content["javascript"] .= "
function addRecord() {
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
<br>
<?
if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
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
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
