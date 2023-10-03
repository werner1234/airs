<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/01 13:05:43 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: CRM_mutatieQueueList.php,v $
    Revision 1.1  2018/02/01 13:05:43  cvs
    update naar airsV2

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";


$editScript = "CRM_mutatieQueueEdit.php";
$allow_add  = false;
$frm = new AE_cls_formatter();
$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_mutatieQueue","id");
$list->addColumn("CRM_mutatieQueue","portefeuille",array("search"=>'true'));
$list->addColumn("CRM_mutatieQueue","veld",array("search"=>'true'));
$list->addColumn("CRM_mutatieQueue","wasWaarde",array("search"=>'true'));
$list->addColumn("CRM_mutatieQueue","wordtWaarde",array("search"=>'true'));
$list->addColumn("CRM_mutatieQueue","add_date",array("description"=>"ingediend","search"=>'false'));
$list->addColumn("CRM_mutatieQueue","afgewerkt",array("search"=>'false'));
$list->addColumn("CRM_mutatieQueue","verwerkt",array("search"=>'false'));
$list->addColumn("CRM_mutatieQueue","verwerktDoor",array("search"=>'false'));


$list->addColumn("","mutatie",array("search"=>'false', "decsription"=>"verwerkt d.d."));
$list->addColumn("CRM_mutatieQueue","verwerktDatum",array("list_invisible"=>'true'));
$list->addColumn("CRM_mutatieQueue","ip",array("search"=>'false'));



$list->setWhere("afgewerkt = 1");

// set default sort
$_GET['sort'][]      = "verwerktDatum";
$_GET['direction'][] = "DESC";
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
  //$data["disableEdit"] = true;
  $data["add_date"]["value"] = $frm->format("@D {d}-{m}-{Y} om {H}:{i}", $data["add_date"]["value"]);
//  $data["add_date"]["value"]
  $data["mutatie"]["value"] = $frm->format("@D {d}-{m}-{Y} om {H}:{i}", $data["verwerktDatum"]["value"]);

  if ($data["verwerkt"]["value"] != 1)
  {
    $data["tr_class"] = "list_dataregel_oranje";
  }
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