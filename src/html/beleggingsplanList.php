<?php
/*
    AE-ICT CODEX source module versie 1.6, 17 december 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/03/21 17:02:37 $
    File Versie         : $Revision: 1.5 $

    $Log: beleggingsplanList.php,v $
    Revision 1.5  2018/03/21 17:02:37  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("beleggingsplan");
$mainHeader    = vt("overzicht");

$editScript = "beleggingsplanEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("Beleggingsplan","id",array("list_width"=>"100","search"=>false));
$list->addColumn("Beleggingsplan","Portefeuille",array("list_width"=>"100","search"=>true));
$list->addColumn("Beleggingsplan","Datum",array("list_width"=>"100","search"=>false));
$list->addColumn("Beleggingsplan","Waarde",array("list_width"=>"100","search"=>false));
$list->addColumn("Beleggingsplan","ProcentRisicoMijdend",array("list_width"=>"","search"=>false));
$list->addColumn("Beleggingsplan","ProcentRisicoDragend",array("list_width"=>"","search"=>false));

$list->idTable='Beleggingsplan';
$list->ownTables=array('Beleggingsplan');
$list->setJoin("JOIN Portefeuilles ON Beleggingsplan.Portefeuille=Portefeuilles.Portefeuille");
$list->categorieVolgorde['Beleggingsplan']=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');

if( isset ($_GET['portefeuille']) && ! empty ($_GET['portefeuille']) ) {
  $list->setWhere("Beleggingsplan.portefeuille='".$_GET['portefeuille']."'");
}
$html = $list->getCustomFields(array('Beleggingsplan','Portefeuilles'));

$_SESSION['submenu'] = New Submenu();
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

if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
  echo '<a href="#" onclick="addRecordFrame()"><span title="record toevoegen"><img src="icon/16/add.png" class="simbisIcon"> ' . vt('toevoegen') . '</span> </a><br><br>';


$list->customEdit =true;
?>
	<br>
<?=$list->filterHeader();?>
	<table class="list_tabel" cellspacing="0">
		<?=$list->printHeader();?>
		<?php
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
