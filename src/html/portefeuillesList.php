<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "portefeuillesEdit.php";

$subHeader     = vt("portefeuilles");
$mainHeader    = vt("overzicht");

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$list = new MysqlList2();
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->categorieVolgorde=array('Gegevens','Beheerfee','Staffels','Recordinfo');

$list->addFixedField("Portefeuilles","Portefeuille",array("width"=>100,"search"=>true));
$list->addFixedField("Portefeuilles","Vermogensbeheerder",array("width"=>100,"search"=>true));
$list->addFixedField("Portefeuilles","Client",array("width"=>100,"search"=>true));
$list->addFixedField("Portefeuilles","Depotbank",array("width"=>100,"search"=>true));
//$list->addFixedField("Portefeuilles","ClientVermogensbeheerder",array("width"=>100,"search"=>true));

$html = $list->getCustomFields('Portefeuilles','PortList');


$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($html,"");

if(checkAccess('portefeuille'))
{
	// superusers appvar
	$allow_add = true;
}
else
{
  // normale user
	$allow_add = false;
	if(checkAccess())// superusers
	  $allow_add = true;

	 if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	 {
	   $beperktToegankelijk = " (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND Portefeuilles.consolidatie=0 ";
	 }
	 else
	 {
	  	$list->setJoin("INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
							    JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ");
    	$beperktToegankelijk = " (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie<2 ";
	 }

}

$list->setWhere($beperktToegankelijk);
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content["javascript"] .= "
function addRecord() {
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
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
