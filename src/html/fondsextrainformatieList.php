<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 december 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/03/23 17:03:21 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: fondsextrainformatieList.php,v $
    Revision 1.2  2019/03/23 17:03:21  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "fondsextrainformatieEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("FondsExtraInformatie","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsExtraInformatie","fonds",array("list_width"=>"200","search"=>false));
//$list->addFixedField("FondsExtraInformatie","add_date",array("list_width"=>"100","search"=>false));
//$list->addFixedField("FondsExtraInformatie","add_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsExtraInformatie","change_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsExtraInformatie","change_user",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields(array('FondsExtraInformatie','Fonds'),'FondsExtraInformatie');
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

foreach ($list->columns as $colData)
{
  if($colData['objectname'] == 'Fonds')
  {
    $joinFondsen=" LEFT JOIN Fondsen ON FondsExtraInformatie.fonds = Fondsen.Fonds";
  }
}
$list->ownTables=array('FondsExtraInformatie');
$list->setJoin("$joinFondsen ");

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
    while($data = $list->printRow())
    {
      echo $data;
    }
    ?>
  </table>
<?
logAccess();
if($__debug) {
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
