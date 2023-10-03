<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 november 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/03/04 19:20:02 $
    File Versie         : $Revision: 1.5 $
 		
    $Log: fondskostenList.php,v $
    Revision 1.5  2017/03/04 19:20:02  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "fondskostenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("Fondskosten","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("Fondskosten","fonds",array("list_width"=>"180","search"=>false));
$list->addFixedField("Fondskosten","datum",array("list_width"=>"100","search"=>false));
$list->addFixedField("Fondskosten","percentage",array("list_width"=>"100","search"=>false));
//$list->addColumn("Fondskosten","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("Fondskosten","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("Fondskosten","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("Fondskosten","change_user",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('Fondskosten','Fonds'));

$list->ownTables=array('fondskosten');
$list->setJoin("JOIN Fondsen ON fondskosten.fonds=Fondsen.Fonds");

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


	<form name="editForm" method="POST">
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
	</form>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
