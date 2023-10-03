<?php
/*
    AE-ICT CODEX source module versie 1.2, 26 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/12/06 14:17:45 $
    File Versie         : $Revision: 1.3 $

    $Log: facmod_abonnementList.php,v $
    Revision 1.3  2019/12/06 14:17:45  cvs
    call 7675

    Revision 1.2  2019/11/13 15:13:48  cvs
    call 7675

    Revision 1.1  2019/07/22 09:11:22  cvs
    call 7675


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

if (!facmodAccess())
{
  echo "geen toegang";
  return false;
}
$switch = $_GET["actief"];
$id     = $_GET["id"];

session_start();
$_SESSION["pdfReturnUrl"] = $_SERVER["REQUEST_URI"];

$subHeader     = "";
$mainHeader    = vt("abonnementen overzicht");
$setWhere      = array();
$editScript = "facmod_abonnementEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("facmod_abonnement","id",array("list_width"=>"100","search"=>false));
$_SESSION["submenu"] = New Submenu();
if ($_GET["deb_id"] > 0)
{
  $db = new DB();
  $query = "SELECT * FROM CRM_naw WHERE id = ".$_GET["deb_id"];

  $nawRec = $db->lookupRecordByQuery($query);
  $subHeader = " bij <b>".$nawRec["naam"].", ".$nawRec["plaats"]."</b>";

  $setWhere[] = "rel_id = ".$_GET["deb_id"];
  $addExtra = "&rel_id=".$_GET["deb_id"];


  $_SESSION["submenu"]->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");
  $list->addColumn("facmod_abonnement","actief",array("description"=>"actief","list_width"=>"50","search"=>false,"list_align"=>"center"));
}
else
{
  $list->addColumn("","relatie",array("sql_alias"=>"CRM_naw.naam","list_width"=>"","search"=>true,"list_invisible"=>false));
  $list->setJoin(" join CRM_naw ON facmod_abonnement.rel_id = CRM_naw.id ");
  $_SESSION["submenu"]->addItem("genereer factuurregels","facmod_abonnementGenereer.php");
}


$list->addColumn("facmod_abonnement","aantal",array("list_width"=>"50","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_abonnement","eenheid",array("list_width"=>"50","search"=>false,"list_align"=>"center"));
$list->addColumn("facmod_abonnement","txt",array("list_width"=>"","search"=>true));
$list->addColumn("facmod_abonnement","stuksprijs",array("list_width"=>"75","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_abonnement","totaal_excl",array("list_width"=>"75","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_abonnement","btw",array("list_width"=>"30","search"=>false,"list_align"=>"center"));

$list->addColumn("facmod_abonnement","periode",array("description"=>"periode","list_width"=>"40","search"=>false,"list_align"=>"center"));
$list->addColumn("facmod_abonnement","achteraf",array("list_width"=>"40","search"=>false,"list_align"=>"center"));
$list->addColumn("facmod_abonnement","volgnr",array("description"=>"volgnr","list_width"=>"40","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_abonnement","vorigeVerwerkdatum",array("description"=>"vorige verw.","list_width"=>"120","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_abonnement","rubriek",array("list_width"=>"","search"=>false));
$list->addColumn("facmod_abonnement","rel_id",array("description"=>"prio","list_width"=>"40","search"=>false,"list_invisible"=>"false"));



switch ($_GET['do'])
{
	case "deb":
    $subHeader .= ", abonnementen";
	  $setWhere[] = "rel_id = ".$_GET["deb_id"];
    $_GET['sort'][]      = "facmod_abonnement.volgnr";
    $_GET['direction'][] = "ASC";


		break;
  case "all":
    $subHeader .= ", alle actieve abonnementen";
	  $setWhere[] = "actief != 0";
	  $_GET['sort'][]      = "facmod_abonnement.rel_id";
    $_GET['direction'][] = "DESC";
    $_GET['sort'][]      = "facmod_abonnement.volgnr";
    $_GET['direction'][] = "ASC";
		break;
	default:
	  $subHeader .= ", alle factuurregels";
	  $_GET['sort'][]      = "facmod_abonnement.facnr";
    $_GET['direction'][] = "DESC";
	  $_GET['sort'][]      = "facmod_abonnement.volgnr";
    $_GET['direction'][] = "asc";

		break;
}


// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort

if (count($setWhere) > 0)
{
  reset($setWhere);
  while (list($k,$v) = each($setWhere))
  {
    $out .= " AND ".$v;
  }
  $list->setWhere(substr($out,5));
}
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

if ($list->records() > 0 AND $_GET["do"] == "notinvoiced" AND $_GET["deb_id"] > 0)
  $_SESSION["submenu"]->addItem("Maak factuur","facmod_factuurMaakFactuur.php?deb_id=$deb_id");


$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content["javascript"] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new".$addExtra."';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$db  = new DB();
if ($list->queryWhere <> "")
  $whereStr = " WHERE rel_id > 0 AND ".$list->queryWhere;
else
  $whereStr = "";


$prevRelatie = "";

while($data = $list->getRow())
{
//    echo $list->buildRow(array(),$blankRowTemplate);

	// $list->buildRow($data,$template="",$options="");
//	 $data["actief"]["form_type"] = "text";
//   $data["actief"]["value"] = "<a href='".$PHP_SELF."?".$str."&switch=actief&id=".$data["id"]["value"]."'>".
//                              		imagecheckbox($data["actief"]["value"])."</a>";
  $data["extraqs"]="rel_id=".$_GET["deb_id"];
	echo $list->buildRow($data);

}
?>
</table>

<br>
<?


logAccess();
if($__debug)
{
	echo getdebuginfo();
}


echo template($__appvar["templateRefreshFooter"],$content);


?>