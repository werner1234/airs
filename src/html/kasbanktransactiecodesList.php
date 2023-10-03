<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2016/10/21 14:02:57 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: kasbanktransactiecodesList.php,v $
    Revision 1.2  2016/10/21 14:02:57  cvs
    call 5346

    Revision 1.1  2014/11/06 09:26:59  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Kasbank transactiecodes overzicht");

$editScript = "kasbanktransactiecodesEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("KasBankTransactieCodes","id",array("list_width"=>"100","search"=>false));
$list->addColumn("KasBankTransactieCodes","kasbankCode",array("list_width"=>"100","search"=>true));
$list->addColumn("KasBankTransactieCodes","omschrijving",array("list_width"=>"300","search"=>true));
$list->addColumn("KasBankTransactieCodes","doActie",array("list_width"=>"100","search"=>false));
$list->addColumn("KasBankTransactieCodes","actieAlternatief",array("list_width"=>"100","search"=>false, "description" => "ALT. actie"));
$list->addColumn("KasBankTransactieCodes","portefeuillesAltActies",array("list_width"=>"100","search"=>false, "list_invisible" => true));
$list->addColumn("","info",array("list_width"=>"100","list_align"=>"center", "description" => "ALT. port."));



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


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	$tel = 0;
	$p = explode(";", $data["portefeuillesAltActies"]["value"]);
	foreach ($p as $t)
	{
		if (trim($t) <> "")
		{
			$tel++;
		}
	}
	$data["info"]["value"] = ($tel > 0)?$tel:"-";

	//debug($data);
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
?>