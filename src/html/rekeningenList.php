<?php
/*
    AE-ICT sourcemodule created 07 sep. 2020
    Author              : Chris van Santen
    Filename            : rekeningenList.php

    $Log: advent_filemanager.php,v $

*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$subHeader     = vt("rekeningen");
$mainHeader    = vt("overzicht");

$editScript = "rekeningenEdit.php";

if($_GET['frame']==1)
{
  unset($_SESSION['Rekeningen']);
  $__appvar['rowsPerPage']=50;
}
$list = new MysqlList2();
$list->storeTableIds='Rekeningen';
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->idTable='Rekeningen';

if($_GET['consolidatie']==1)
{
  $consolidatieFilter='consolidatie=1';
  $subHeader     = vt("rekeningen in Consolidaties");
}
else
{
  $consolidatieFilter='consolidatie=0';
}
$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

if(!checkAccess('portefeuille'))
{
  // normale user
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $beperktToegankelijk = "AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND Portefeuilles.consolidatie=0 ";
  }
  else
  {
    $rechtenJoin=" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
							    JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
    $beperktToegankelijk = "AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie<2 ";
  }
  
}

//$list->addField("Rekeningen","id",array("width"=>100,"search"=>false));
$list->addFixedField("Rekeningen","Rekening",array("list_width"=>120,"search"=>true));
$list->addFixedField("Rekeningen","Portefeuille",array("width"=>100,"search"=>true));
$list->addFixedField("Rekeningen","Valuta",array("list_width"=>100,"search"=>false));
$list->addFixedField("Rekeningen","Tenaamstelling",array("width"=>100,"search"=>false));
$list->addFixedField("Rekeningen","Memoriaal",array("list_width"=>100,"search"=>false));
$list->addFixedField("Rekeningen","Termijnrekening",array("list_width"=>120,"search"=>false));
$list->addFixedField("Rekeningen","Deposito",array("list_width"=>80,"search"=>false));
$list->addFixedField("Rekeningen","Inactief",array("list_width"=>80,"search"=>false));

$list->categorieVolgorde=array('Rekeningen'=>array('Algemeen'),'DepositoRentepercentages'=>array('Algemeen'),'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'));
$html = $list->getCustomFields(array('Rekeningen','DepositoRentepercentages','Portefeuilles'),"Rekeningen");
$db=new DB();
  foreach ($list->columns as $colData)
  {
    if($colData['objectname'] == 'DepositoRentepercentages' && !$tableCreated)
    {
      $query="CREATE TEMPORARY TABLE laatsteRente SELECT m1.* FROM DepositoRentepercentages m1 LEFT JOIN DepositoRentepercentages m2  ON (m1.Rekening = m2.Rekening AND m1.DatumTot < m2.DatumTot) WHERE m2.id IS NULL ";
      $db->SQL($query);
      $db->Query();
      $query="ALTER TABLE laatsteRente ADD INDEX( Rekening ); ";
      $db->SQL($query);
      $db->Query();
      $joinLaatsteRente="LEFT JOIN laatsteRente as DepositoRentepercentages ON Rekeningen.Rekening = DepositoRentepercentages.Rekening ";
      $tableCreated=true;
    }
  }
//SELECT m1.* FROM DepositoRentepercentages m1 LEFT JOIN DepositoRentepercentages m2  ON (m1.Rekening = m2.Rekening AND m1.DatumTot < m2.DatumTot) WHERE m2.id IS NULL;
$joinLaatsteRente .=" Join Portefeuilles on Rekeningen.Portefeuille=Portefeuilles.Portefeuille AND Portefeuilles.$consolidatieFilter ";
  $list->ownTables=array('Rekeningen');
  $list->setJoin($joinLaatsteRente.$rechtenJoin);



$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
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
if($_GET['portefeuille']<>'')
  $list->setWhere("Rekeningen.Portefeuille='".$_GET['portefeuille']."' AND Rekeningen.$consolidatieFilter $beperktToegankelijk");
else
  $list->setWhere(" Rekeningen.$consolidatieFilter $beperktToegankelijk");
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

function editRecord(url) 
{
	location = url;
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$list->customEdit =true;
while($data = $list->getRow())
{
  $data['extraqs']='frame='.$_GET['frame'];
  echo $list->buildRow($data);
}
?>
</table>
<?
if($tableCreated)
{
  $query="DROP TEMPORARY TABLE laatsteRente ";
  $db->SQL($query);
  $db->Query();
}
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
