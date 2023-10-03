<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " Referentieportefeuille per beleggingscategorie";

$editScript = "referentieportefeuilleperbeleggingscategorieEdit.php";
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
$list->addFixedField("ReferentieportefeuillePerBeleggingscategorie","Vermogensbeheerder",array("list_width"=>"100","search"=>true));
$list->addFixedField("ReferentieportefeuillePerBeleggingscategorie","Beleggingscategorie",array("list_width"=>"200","search"=>false));
$list->addFixedField("ReferentieportefeuillePerBeleggingscategorie","Referentieportefeuille",array("list_width"=>"200","search"=>false));
$list->addFixedField("ReferentieportefeuillePerBeleggingscategorie","vanaf",array("list_width"=>"200","search"=>false));
$list->addFixedField("ReferentieportefeuillePerBeleggingscategorie","Portefeuille",array("list_width"=>"200","search"=>true));
$list->addFixedField("ReferentieportefeuillePerBeleggingscategorie","Categoriesoort",array("list_width"=>"200","search"=>true));
$list->addFixedField("ReferentieportefeuillePerBeleggingscategorie","Categorie",array("list_width"=>"200","search"=>true));
//$list->addColumn("IndexPerBeleggingscategorie","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("IndexPerBeleggingscategorie","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("IndexPerBeleggingscategorie","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("IndexPerBeleggingscategorie","change_user",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('ReferentieportefeuillePerBeleggingscategorie','ReferentieportefeuillePerBeleggingscategorie');
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

if($_GET['portefeuille']<>'')
  $uitsluitingenWhere=" AND ReferentieportefeuillePerBeleggingscategorie.Portefeuille='".$_GET['portefeuille']."'";
else
  $uitsluitingenWhere='';

if($uitsluitingenWhere <> '')
  $list->setWhere('1 '.$uitsluitingenWhere);
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
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
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
	location = url;
}
";
echo template($__appvar["templateContentHeader"],$content);
if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
  echo '<a href="#" onclick="addRecordFrame()"><span title="record toevoegen"><img src="icon/16/add.png" class="simbisIcon"> ' . vt('toevoegen') . '</span> </a><br><br>';
?>
	<br>
<?=$list->filterHeader();?>
	<table class="list_tabel" cellspacing="0">
		<?=$list->printHeader();?>
		<?php
    $list->customEdit =true;
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
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
?>