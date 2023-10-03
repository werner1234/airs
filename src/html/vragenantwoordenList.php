<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 juli 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/07/20 13:06:52 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: vragenantwoordenList.php,v $
    Revision 1.1  2014/07/20 13:06:52  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "vragenantwoordenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("VragenAntwoorden","id",array("list_width"=>"100","search"=>false));
$list->addColumn("VragenAntwoorden","vraagId",array("list_width"=>"300","search"=>false));
$list->addColumn("VragenAntwoorden","omschrijving",array("list_width"=>"200","search"=>false));
$list->addColumn("VragenAntwoorden","punten",array("list_width"=>"100","search"=>false));



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

$db=new DB();
$query="SELECT VragenVragen.id, concat(VragenVragenlijsten.omschrijving,' - ',VragenVragen.omschrijving) as omschrijving
FROM VragenVragen 
LEFT JOIN VragenVragenlijsten ON VragenVragen.vragenlijstId = VragenVragenlijsten.id
ORDER BY VragenVragen.omschrijving";
$db->SQL($query);
$db->Query();
$vragenLookup=array();
while($lookup=$db->nextRecord())
  $vragenLookup[$lookup['id']]=$lookup['omschrijving'];
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
 // listarray($data);
  $data['vraagId']['value']=$vragenLookup[$data['vraagId']['value']];
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