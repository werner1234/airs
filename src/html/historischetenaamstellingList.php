<?php
/*
    AE-ICT CODEX source module versie 1.6, 11 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2013/03/09 16:20:10 $
    File Versie         : $Revision: 1.3 $

    $Log: historischetenaamstellingList.php,v $
    Revision 1.3  2013/03/09 16:20:10  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("historische tenaamstelling");
$mainHeader    = vt("overzicht");

$editScript = "historischetenaamstellingEdit.php";
if(checkAccess($type)) 
{
	// superusers
	$allow_add = true;
}
elseif(GetCRMAccess(2))
{ // CRM beheerder
  $allow_add = true;
}
else 
{
	// normale user
	$allow_add = false;
}

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("HistorischeTenaamstelling","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","ClientenNaam",array('sql_alias'=>'Clienten.Naam',"list_width"=>"250","search"=>false,'list_order'=>true));
$list->addColumn("","CrmNaam",array('sql_alias'=>'CRM_naw.Naam',"list_width"=>"250","search"=>false,'list_order'=>true));
$list->addColumn("HistorischeTenaamstelling","Naam",array("list_width"=>"100","search"=>false));
$list->addColumn("HistorischeTenaamstelling","Naam1",array("list_width"=>"100","search"=>false));
$list->addColumn("HistorischeTenaamstelling","geldigTot",array("list_width"=>"100","search"=>false));

$list->setJoin("Left Join Clienten ON  historischeTenaamstelling.clientId = Clienten.id
Left Join CRM_naw ON historischeTenaamstelling.crmId = CRM_naw.id");

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
