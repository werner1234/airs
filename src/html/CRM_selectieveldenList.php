<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();


$editScript = "CRM_selectieveldenEdit.php";
$allow_add = true;
$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("CRM_selectievelden","id",array("width"=>100,"search"=>false));
$list->addField("CRM_selectievelden","module",array("width"=>"","search"=>true));
$list->addField("CRM_selectievelden","waarde",array("width"=>"","search"=>true));
$list->addField("CRM_selectievelden","omschrijving",array("width"=>"","search"=>true));

switch ($_GET[module])
{
	case "burgelijke staat":
		$list->setWhere("module = 'burgelijke staat' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", Burgerlijke staat";
		break;
  case "rechtsvorm":
		$list->setWhere("module = 'rechtsvorm' ");
		$list->removeColumn("module");
		$subHeader = ", Rechtsvorm";
		break;
  case "telefoon":
		$list->setWhere("module = 'telefoon' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", Telefoonsoorten";
		break;
  case "risicoprofiel":
		$list->setWhere("module = 'risicoprofiel' ");
    $list->removeColumn("waarde");
		$list->removeColumn("module");
		$subHeader = ", Risicoprofielen";
		break;
	case "beleggingsdoelstelling":
		$list->setWhere("module = 'beleggingsdoelstelling' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", Beleggingsdoelstelling";
		break;
	case "beleggingshorizon":
		$list->setWhere("module = 'beleggingshorizon' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", Beleggingshorizon";
		break;
	case "legitimatie":
		$list->setWhere("module = 'legitimatie' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", Soort legitimatie";
		break;
  case "soort inkomen":
		$list->setWhere("module = 'soort inkomen' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", Soort inkomen";
		break;
  case "verzend freq rapportage":
		$list->setWhere("module = 'verzend freq rapportage' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", Verzend freq rapportage";
		break;
  case "in contact door":
		$list->setWhere("module = 'in contact door' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", In contact door";
		break;
    case "ervaring":
		$list->setWhere("module = 'ervaring' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", ervaring/kennis";
		break;
		case "opleidingsniveau":
		$list->setWhere("module = 'opleidingsniveau' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", opleidingsniveau";
		break;
		case "clientenclassificatie":
		$list->setWhere("module = 'clientenclassificatie' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", clientenclassificatie";
		break;
		case "relatiegeschenken":
		$list->setWhere("module = 'relatiegeschenken' ");
		$list->removeColumn("module");
		$subHeader = ", relatiegeschenken";
		break;
		case "prospect status":
		$list->setWhere("module = 'prospect status' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", prospect status";
		break;
		case "evenementen":
		$list->setWhere("module = 'evenementen' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", evenementen";
		break;
		case "agenda afspraak":
		$list->setWhere("module = 'agenda afspraak' ");
		$list->removeColumn("module");
		$subHeader = ", agenda afspraak soort";
		break;
		case "banken":
		$list->setWhere("module = 'banken' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", banken";
		break;
		case "docCategrien":
		$list->setWhere("module = 'docCategrien' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$list->addField("CRM_selectievelden","waarde",array("description"=>vt("Standaard"),"list_align"=>"center","form_type"=>"checkbox","width"=>"","search"=>true));
		$subHeader = ", document categorien";
		break;
		case "gesprekstypen":
		$list->setWhere("module = 'gesprekstypen' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", gespreksverslag typen";
		break;
 	default:
		$list->setWhere("module = '".$_GET['module']."' ");
		$list->removeColumn("module");
		$list->removeColumn("waarde");
		$subHeader = ", ".$_GET['omschrijving'];
		break;
}

$mainHeader = vt("Overzicht selectievelden");
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
&nbsp;  <b>$mainHeader</b> $subHeader
</div><br>";
$content[javascript] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new&module=".urlencode($_GET[module])."';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->printRow())
{
	 //<td data-field="waarde" class="listTableData"   align="left" >0 &nbsp;</td>
	echo $data;
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>