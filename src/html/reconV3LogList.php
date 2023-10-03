<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 juni 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/01 12:17:26 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: reconV3LogList.php,v $
    Revision 1.1  2020/07/01 12:17:26  cvs
    call 7937


 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$fmt = new AE_cls_formatter();
$subHeader     = "Recon V3 ";
$mainHeader    = vt("overzicht");

$editScript = "reconV3LogEdit.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 250;

$list->addColumn("reconV3Log","id",array("list_width"=>"100","search"=>false));
$list->addColumn("reconV3Log","stamp",array("list_width"=>"100","search"=>false));
$list->addColumn("","duur",array("list_width"=>"60","search"=>false, "list_align"=>"right"));
$list->addColumn("reconV3Log","location",array("list_width"=>"200","search"=>true));
$list->addColumn("reconV3Log","omschrijving",array("list_width"=>"300","search"=>true));
$list->addColumn("reconV3Log","batch",array("list_width"=>"160","search"=>true));
$list->addColumn("reconV3Log","add_user",array("list_width"=>"70"));


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

$prevBatch = "";
$prevTime = 0;
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");

  if ($prevBatch != $data["batch"]["value"])
  {
     $prevtime =  db2jul($data["stamp"]["value"]);
     $prevBatch = $data["batch"]["value"];
  }
  $data["duur"]["value"]  = (db2jul($data["stamp"]["value"]) - $prevtime) ." sec" ;
  $prevtime = db2jul($data["stamp"]["value"]);
//  debug(array($prevtime, $data["duur"]["value"]));
  $data["stamp"]["value"] = $fmt->format("@D {d}-{m} {H}:{i}:{s}", $data["stamp"]["value"]);
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