<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 23 mei 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/06/20 12:11:48 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: fondsenfundinformatieList.php,v $
    Revision 1.2  2020/06/20 12:11:48  rvv
    *** empty log message ***

    Revision 1.1  2020/05/23 16:36:21  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "fondsenfundinformatieEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("FondsenFundInformatie","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsenFundInformatie","fonds",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsenFundInformatie","datumVanaf",array("list_width"=>"100","search"=>false));
/*
$list->addColumn("FondsenFundInformatie","MSFondswaarde",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","MSAantalIntr",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","MSManFeeFonds",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","YieldtoMaturity",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","AverageCreditQuality",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","AverageEffDuration",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","AverageEffMaturity",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","AverageCoupon",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","change_user",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","change_date",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","add_user",array("list_width"=>"100","search"=>false));
$list->addColumn("FondsenFundInformatie","add_date",array("list_width"=>"100","search"=>false));
*/

$html = $list->getCustomFields('FondsenFundInformatie');


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
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

  <br>
<?=$list->filterHeader();?>
  <table class="list_tabel" cellspacing="0">
 <?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
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
