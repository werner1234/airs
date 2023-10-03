<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 30 maart 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2016/12/04 13:22:24 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: orderkostenList.php,v $
    Revision 1.7  2016/12/04 13:22:24  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "orderkostenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$list->addFixedField("Orderkosten","vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orderkosten","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orderkosten","fondssoort",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orderkosten","valuta",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orderkosten","beurs",array("list_width"=>"100","search"=>false));

if($_GET['portefeuille']<>'')
  $list->setWhere("orderkosten.portefeuille='".$_GET['portefeuille']."'");


$html = $list->getCustomFields('Orderkosten');

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

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
function addRecordFrame() 
{
	location =  '".$editScript."?action=new&frame=1&portefeuille=".$_GET['portefeuille']."';
}
function editRecord(url) 
{
	location = url;
}
";
echo template($__appvar["templateContentHeader"],$content);
if($_GET['frame']==1)
{
  if($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 )
    echo '<a href="#" onclick="addRecordFrame()"><span title="record toevoegen"><img src="icon/16/add.png" class="simbisIcon"> '.vt("toevoegen").'</span> </a><br><br>';
}
?>
<br>
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
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
