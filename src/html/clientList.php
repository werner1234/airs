<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "clientEdit.php";

$subHeader     = vt("Clienten");
$mainHeader    = vt("overzicht");


$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("Client","id",array("search"=>false));
$list->addFixedField("Client","Client",array("width"=>100,"search"=>true));
$list->addFixedField("Client","Naam",array("search"=>true,"search"=>true));
//$list->addField("Client","Naam1",array("width"=>200,"search"=>false));
//$list->addField("Client","Adres",array("search"=>false));
$list->addFixedField("Client","Woonplaats",array("width"=>200));
//$list->addField("Client","Telefoon",array("search"=>false));
//$list->addField("Client","Fax",array("search"=>false));
//$list->addField("Client","Email",array("search"=>false));

$html = $list->getCustomFields('Client');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

if(checkAccess($type)) 
{
	// superusers
	$allow_add = true;
}
else 
{
	// normale user
	$allow_add = false;
}

// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);



if(!checkAccess('portefeuille'))
{
  
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $rechtenJoin=" JOIN Portefeuilles ON Clienten.Client=Portefeuilles.Client ";
    $beperktToegankelijk = "OR ((Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND Portefeuilles.consolidatie=0) ";
  }
  else
  {
    $rechtenJoin=" LEFT JOIN Portefeuilles ON Clienten.Client=Portefeuilles.Client ";
    $rechtenJoin.=" LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
							     LEFT JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
    $beperktToegankelijk = "OR ( (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie<2 )";
  }

  $list->setJoin($rechtenJoin);
  $list->setWhere("( Portefeuilles.id is NULL $beperktToegankelijk )");
}


$list->setGroupBy('Clienten.Client');

// select page
$list->selectPage($_GET['page']);


session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0" width="5000">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
 $data['filler']['value'] = "&nbsp;";
 $data['filler']['td_style'] = " style='border-bottom:none' ";
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
