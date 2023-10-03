<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 juli 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/03/18 17:51:09 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: fixdepotbankenpervermogensbeheerderList.php,v $
    Revision 1.6  2020/03/18 17:51:09  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "fixdepotbankenpervermogensbeheerderEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("FixDepotbankenPerVermogensbeheerder","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","depotbank",array("list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","rekeningNrTonen",array("description"=>"Rek.nr.Tonen","list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","meervoudigViaFix",array("description"=>"Fix M.V.","list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","meervNominaalFIX",array("description"=>"Fix m.v.n","list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","nominaalViaFix",array("description"=>"Fix nominaal","list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","fixDefaultAan",array("description"=>"standaard Fix","list_width"=>"100","search"=>false));
$list->addFixedField("FixDepotbankenPerVermogensbeheerder","careOrderVerplicht",array("description"=>"Care","list_width"=>"100","search"=>false));

$html = $list->getCustomFields('FixDepotbankenPerVermogensbeheerder','FixDepPerVerm');


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
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
  
  <br>
  <form name="editForm" method="POST">
<?=$list->filterHeader();?>
  <table class="list_tabel" cellspacing="0">
    <?=$list->printHeader();?>
    <?php
    while($data = $list->printRow())
    {
      echo $data;
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
