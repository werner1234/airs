<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 maart 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/09/20 06:27:28 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: tijdelijkefondsreconList.php,v $
    Revision 1.1  2017/09/20 06:27:28  cvs
    no message

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "fonds Recon overzicht";

$editScript = "tijdelijkefondsreconEdit.php";
$allow_add  = false;

$list = new MysqlList2();
$list->idField = "id";
$list->idTable='tijdelijkeFondsRecon';
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("TijdelijkeFondsRecon","id",array("search"=>false));
//$list->addColumn("TijdelijkeFondsRecon","datum",array("search"=>false));
$list->addFixedField("tijdelijkeFondsRecon","depot",array("search"=>false));
$list->addFixedField("tijdelijkeFondsRecon","Fonds",array("search"=>true, "list_width"=>200));
//$list->addColumn("TijdelijkeFondsRecon","bank_bankCode",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","bank_ISIN",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","bank_valuta",array("search"=>false));
//$list->addColumn("TijdelijkeFondsRecon","bank_beurs",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","airs_bankCode",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","airs_ISIN",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","airs_valuta",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","airs_beurs",array("search"=>true));

//$list->addColumn("TijdelijkeFondsRecon","airs_ISIN",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","airs_valuta",array("search"=>true));
//$list->addColumn("TijdelijkeFondsRecon","airs_beurs",array("search"=>false));
$list->addFixedField("tijdelijkeFondsRecon","matchcode",array("search"=>false,"list_width"=>200));
//$list->addColumn("TijdelijkeFondsRecon","batch",array("search"=>false));
$list->setJoin("
  LEFT JOIN Fondsen ON tijdelijkeFondsRecon.Fonds = Fondsen.Fonds
  ");
$list->ownTables=array('tijdelijkeFondsRecon');
$list->categorieVolgorde = array(

  'tijdelijkeFondsRecon' =>array('Algemeen'),
  'Fonds' =>array('Algemeen')

);
$html = $list->getCustomFields(array('tijdelijkeFondsRecon','Fonds'));
$list->setWhere(" tijdelijkeFondsRecon.change_user = '$USR' ");

// set default sort
// $_GET['sort'][]      = "tablename.field";
// $_GET['direction'][] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");
session_write_close();

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

  <div id="content">
  <br>
  <form name="editForm" method="POST">
<?=$list->filterHeader();?>
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
  </form>
  </div>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>