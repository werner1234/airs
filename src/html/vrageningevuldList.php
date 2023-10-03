<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 3 augustus 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2014/11/23 14:11:47 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: vrageningevuldList.php,v $
    Revision 1.4  2014/11/23 14:11:47  rvv
    *** empty log message ***

    Revision 1.3  2014/11/19 16:41:12  rvv
    *** empty log message ***

    Revision 1.2  2014/08/17 12:17:40  rvv
    *** empty log message ***

    Revision 1.1  2014/08/03 13:14:10  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "vrageningevuldEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$list->addColumn("VragenIngevuld","id",array("list_width"=>"20","search"=>false));
$list->addColumn("","print",array("description"=>' ',"list_width"=>"50","search"=>false));
$list->addColumn("VragenIngevuld","vragenlijstId",array("list_width"=>"100","search"=>false));
$list->addColumn("VragenIngevuld","add_date",array("list_width"=>"100","search"=>false));
$list->addColumn("VragenIngevuld","add_user",array("list_width"=>"100","search"=>false));

$list->setWhere("relatieId='".$_GET['rel_id']."'");

$list->setGroupBy("vragenlijstId, date(add_date)");
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
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new&relatieId=".$_GET['rel_id']."';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$db=new DB();
$query="SELECT id,omschrijving FROM VragenVragenlijsten";
$db->SQL($query);
$db->Query();
while($data= $db->nextRecord())
 $vragenlijstLookup[$data['id']]=$data['omschrijving'];
 

while($data = $list->getRow())
{
  $data['print']['value']='<a class="icon" href="vragenantwoordenPrint.php?id='.$data['id']['value'].'&score=0"> '.maakKnop('pdf.png',array('size'=>16,'tooltip'=>'Print zonder cijfers')).'</a>';
  $data['print']['value'].='&nbsp;&nbsp; <a class="icon" href="vragenantwoordenPrint.php?id='.$data['id']['value'].'&score=1"> '.maakKnop('pdf.png',array('size'=>16,'tooltip'=>'Print met cijfers')).'</a>';
	// $list->buildRow($data,$template="",$options="");
  $data['vragenlijstId']['value']=$vragenlijstLookup[$data['vragenlijstId']['value']];
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