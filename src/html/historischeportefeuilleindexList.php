<?php
/*
    AE-ICT CODEX source module versie 1.6, 21 november 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/01/27 18:06:36 $
    File Versie         : $Revision: 1.3 $

    $Log: historischeportefeuilleindexList.php,v $
    Revision 1.3  2018/01/27 18:06:36  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("historische portefeuille index");
$mainHeader    = vt("overzicht");

$editScript = "historischeportefeuilleindexEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("HistorischePortefeuilleIndex","Portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("HistorischePortefeuilleIndex","Datum",array("list_width"=>"100","search"=>false));
$list->addFixedField("HistorischePortefeuilleIndex","IndexWaarde",array("list_width"=>"100","search"=>false));

$list->categorieVolgorde=array('HistorischePortefeuilleIndex'=>array('Algemeen'),'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'));

$html = $list->getCustomFields(array('HistorischePortefeuilleIndex','Portefeuilles'),'HistorischePortefeuilleIndex');


if(!checkAccess('portefeuille'))
{
  //$_SESSION['usersession']['gebruiker']['Accountmanager']='ALGDOO';
  //$_SESSION['usersession']['gebruiker']['overigePortefeuilles'] =0;
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $rechtenJoin=" JOIN Portefeuilles ON HistorischePortefeuilleIndex.Portefeuille=Portefeuilles.Portefeuille ";
    $beperktToegankelijk = "OR ((Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND Portefeuilles.consolidatie=0) ";
  }
  else
  {
    $rechtenJoin=" LEFT JOIN Portefeuilles ON HistorischePortefeuilleIndex.Portefeuille=Portefeuilles.Portefeuille ";
    $rechtenJoin.=" LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
							     LEFT JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
    $beperktToegankelijk = "OR ( (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie<2 )";
  }
  
  $list->setJoin($rechtenJoin);
  $list->setWhere("( Portefeuilles.id is NULL $beperktToegankelijk )");
}
else
{
  $joinPortefeuille .=" Join Portefeuilles on HistorischePortefeuilleIndex.Portefeuille=Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 ";
  $list->setJoin($joinPortefeuille);
}


$list->ownTables=array('HistorischePortefeuilleIndex');

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");

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
</form>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
