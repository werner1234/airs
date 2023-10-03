<?php
/* 	
    AE-ICT CODEX source module versie 1.3, 5 december 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/08/26 17:39:33 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: modelportefeuillesList.php,v $
    Revision 1.4  2017/08/26 17:39:33  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("./rapport/rapportRekenClass.php");
session_start();

$subHeader     = vt("Modelportefeuilles");
$mainHeader    = vt("overzicht");

$editScript = "modelportefeuillesEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("ModelPortefeuilles","id");
$list->addFixedField("ModelPortefeuilles","Portefeuille",array("list_width"=>"100","search"=>true));
$list->addFixedField("ModelPortefeuilles","Omschrijving",array("list_width"=>"200","search"=>true));
$list->addFixedField("ModelPortefeuilles","Fixed",array("list_width"=>"100","search"=>true));
$list->addFixedField("ModelPortefeuilles","FixedDatum",array("list_width"=>"100","search"=>true));

$html = $list->getCustomFields(array("ModelPortefeuilles"));



$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Import fixed modelportefeuilles"),'modelportefeuillesImport.php');

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

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
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
		while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	if($data['ModelPortefeuilles.Fixed'])
	{
		if($data['ModelPortefeuilles.Fixed']['value']==1)
		{
			$fixedregels=berekenFixedModelPortefeuille($data['ModelPortefeuilles.Portefeuille']['value'],$data['ModelPortefeuilles.FixedDatum']['value']);
			vulTijdelijkeTabel($fixedregels,$data['ModelPortefeuilles.Portefeuille']['value'],$data['ModelPortefeuilles.FixedDatum']['value']);
			$afm=AFMstd($data['ModelPortefeuilles.Portefeuille']['value'],$data['ModelPortefeuilles.FixedDatum']['value']);
			$data['ModelPortefeuilles.Fixed']['value']=$data['ModelPortefeuilles.Fixed']['form_options'][$data['ModelPortefeuilles.Fixed']['value']]." (".round($afm['std'],1).")";
		}
		else
			$data['ModelPortefeuilles.Fixed']['value']=$data['ModelPortefeuilles.Fixed']['form_options'][$data['ModelPortefeuilles.Fixed']['value']];

	}
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