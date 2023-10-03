<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 december 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/12/02 19:12:00 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: factuurbeheerfeehistorieList.php,v $
    Revision 1.1  2017/12/02 19:12:00  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("factuur beheerfee historie");
$mainHeader    = vt("overzicht");

$editScript = "factuurbeheerfeehistorieEdit.php";
$allow_add  = false;

$list = new MysqlList2();
$list->idField = "id";
$list->idTable = "FactuurBeheerfeeHistorie";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->categorieVolgorde=array('FactuurBeheerfeeHistorie'=>array('Algemeen'),'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels','Recordinfo'),'Rekeningmutaties'=>array('Algemeen'));

$list->addFixedField("FactuurBeheerfeeHistorie","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurBeheerfeeHistorie","factuurNr",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurBeheerfeeHistorie","periodeDatum",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurBeheerfeeHistorie","grondslag",array("list_width"=>"100","search"=>false));
$list->addFixedField("FactuurBeheerfeeHistorie","beheerfee",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('FactuurBeheerfeeHistorie','Portefeuilles','Rekeningmutaties'),'FacHist');

$joinPortefeuilles='';
$joinRekeningmutaties='';


foreach ($list->columns as $colData)
{
	if($colData['objectname'] == 'Portefeuilles')
	{
		$joinPortefeuilles=" LEFT JOIN Portefeuilles ON FactuurBeheerfeeHistorie.portefeuille = Portefeuilles.portefeuille ";
	}
	if($colData['objectname'] == 'Rekeningmutaties')
	{
		$joinRekeningmutaties=" LEFT JOIN Rekeningmutaties ON FactuurBeheerfeeHistorie.rekeningmutatieId = Rekeningmutaties.id ";
	}
}
if($joinfondsRente<>'')
	createRenteTabel();

$list->ownTables=array('FactuurBeheerfeeHistorie');
$list->setJoin("$joinPortefeuilles $joinRekeningmutaties");




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
