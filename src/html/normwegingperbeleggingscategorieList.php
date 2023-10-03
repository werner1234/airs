<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("norm weging per beleggingscategorie");
$mainHeader    = vt("overzicht");

$editScript = "normwegingperbeleggingscategorieEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("NormwegingPerBeleggingscategorie","Portefeuille",array("list_width"=>"200","search"=>true));
$list->addFixedField("NormwegingPerBeleggingscategorie","Beleggingscategorie",array("list_width"=>"200","search"=>false));
$list->addFixedField("NormwegingPerBeleggingscategorie","Normweging",array("list_width"=>"200","search"=>false));

$list->categorieVolgorde=array('NormwegingPerBeleggingscategorie'=>array('Algemeen'),'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'));
$html = $list->getCustomFields(array('NormwegingPerBeleggingscategorie','Portefeuilles'),"NormwegingPerBeleggingscategorie");
$joinPortefeuilles='';
foreach ($list->columns as $colData)
{
  if($colData['objectname'] == 'Portefeuilles' && !isset($tableCreated))
  {
    $joinPortefeuilles=" Join Portefeuilles on NormwegingPerBeleggingscategorie.Portefeuille=Portefeuilles.Portefeuille";
    $tableCreated=true;
  }
}
//SELECT m1.* FROM DepositoRentepercentages m1 LEFT JOIN DepositoRentepercentages m2  ON (m1.Rekening = m2.Rekening AND m1.DatumTot < m2.DatumTot) WHERE m2.id IS NULL;

$list->ownTables=array('NormwegingPerBeleggingscategorie');
$list->setJoin($joinPortefeuilles);

if($_GET['portefeuille']<>'')
  $uitsluitingenWhere=" AND NormwegingPerBeleggingscategorie.Portefeuille='".$_GET['portefeuille']."'";
else
  $uitsluitingenWhere='';

if($uitsluitingenWhere <> '')
  $list->setWhere('1 '.$uitsluitingenWhere);


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

function addRecordFrame()
{
	location =  '".$editScript."?action=new&frame=1&portefeuille=".$_GET['portefeuille']."';
}

function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
function editRecord(url)
{
	location = url;
}
";
echo template($__appvar["templateContentHeader"],$content);
if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
  echo '<a href="#" onclick="addRecordFrame()"><span title="record toevoegen"><img src="icon/16/add.png" class="simbisIcon"> ' . vt('toevoegen') . '</span> </a><br><br>';

echo template($__appvar["templateContentHeader"],$content);

$totalen=array();
$db= new DB();
$query="SELECT DatumVanaf,Portefeuille,SUM(Normweging) as totaal FROM NormwegingPerBeleggingscategorie GROUP BY Portefeuille,DatumVanaf HAVING totaal <> 100 ORDER BY Portefeuille,DatumVanaf ";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  $totalen[]=$data;
}

?>
  
 
  <br>
<?=$list->filterHeader();?>
  <table class="list_tabel" cellspacing="0">
    <?=$list->printHeader();?>
    <?php
   // $list->customEdit =true;
    while($data = $list->getRow())
    {
	// $list->buildRow($data,$template="",$options="");
    	echo $list->buildRow($data);
    }
?>
</table>
<?
if(count($totalen) > 0)
{
  echo "<br>\n";
  foreach ($totalen as $data)
    echo "Geen 100% normweging voor portefeuille: ".$data['Portefeuille']." op ".$data['DatumVanaf']." (".$data['totaal']." %)<br>\n";

}
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
