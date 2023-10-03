<?php
/*
    AE-ICT CODEX source module versie 1.2, 25 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/18 09:11:36 $
    File Versie         : $Revision: 1.2 $

    $Log: facmod_factuurbeheerList.php,v $
    Revision 1.2  2019/11/18 09:11:36  cvs
    call 7675

    Revision 1.1  2019/07/22 09:11:22  cvs
    call 7675



*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

if (!facmodAccess())
{
  return false;
}


$subHeader     = "";
$mainHeader    = "Verkoop facturen overzicht";

$editScript = "facmod_factuurbeheerEdit.php";
$allow_add  = true;

$deb_id = $_REQUEST["deb_id"];

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("facmod_factuurbeheer","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","copy",array("list_width"=>"30"));
$list->addColumn("facmod_factuurbeheer","facnr",array("list_width"=>"80","search"=>true));
$list->addColumn("facmod_factuurbeheer","datum",array("list_width"=>"90","search"=>false));
//$list->addColumn("facmod_factuurbeheer","vervaldatum",array("list_width"=>"90","search"=>false));

if ($deb_id < 1)
{
  $list->addColumn("facmod_factuurbeheer","firmanaam",array("list_width"=>"","search"=>true));
}

$list->addColumn("facmod_factuurbeheer","status",array("list_width"=>"100","search"=>false));

$list->addColumn("facmod_factuurbeheer","bedrag_incl",array("description"=>"bedrag","list_width"=>"90","search"=>true));
$list->addColumn("facmod_factuurbeheer","bedrag_voldaan",array("list_width"=>"100","search"=>false,"list_invisible"=>"true"));
$list->addColumn("facmod_factuurbeheer","betaal_dagen",array("list_width"=>"100","search"=>false,"list_invisible"=>"true"));
$list->addColumn("","openstaand",array("list_width"=>"90","list_align"=>"right","search"=>false));
$list->addColumn("","dagenOpen",array("list_width"=>"50","list_align"=>"right","search"=>false,"description"=>"D open"));
$list->addColumn("facmod_factuurbeheer","email_datum",array("list_width"=>"100","list_align"=>"right","search"=>false,"description"=>"E-mail datum"));

//$list->addColumn("","spacer",array("description"=>" "));

  $_SESSION["submenu"] = New Submenu();

if ($deb_id > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec["naam"].", ".$nawRec["plaats"]."</b>";

  $list->setWhere("facmod_factuurbeheer.rel_id = ".$deb_id);
  $addExtra = "&rel_id=".$_GET['deb_id'];


  $_SESSION["submenu"]->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");

}

switch ($_GET['do'])
{
	case "open":
    $subHeader .= ", Niet betaalde verkoopfacturen";
	  $list->setWhere("facmod_factuurbeheer.status = 'G' OR facmod_factuurbeheer.status= 'D' ");
	  $_GET['sort'][]      = "facmod_factuurbeheer.datum";
    $_GET['direction'][] = "ASC";
//    $_SESSION["submenu"]->addItem("Genereer xls","facmod_factuurbeheerPrintOpenstaand.php");
		break;
  case "partial":
		$subHeader .= ", Deels betaalde verkoopfacturen";
	  $list->setWhere("facmod_factuurbeheer.status = 'D'");
	  $_GET['sort'][]      = "facmod_factuurbeheer.datum";
    $_GET['direction'][] = "ASC";
		break;
	default:
	  $subHeader .= ", alle verkoop facturen";
	  $_GET['sort'][]      = "facmod_factuurbeheer.facnr";
    $_GET['direction'][] = "DESC";

		break;
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);


$db  = new DB();
$ditJaar = date("Y");
$vorigJaar = $ditJaar - 1;
if ($list->queryWhere <> "")
  $whereStr = " WHERE ".$list->queryWhere;
else
  $whereStr = "";

$query = "
SELECT
  SUM(IF(status = 'G' OR status = 'D', (bedrag_incl - bedrag_voldaan ),0)) AS in_openstaand,
  SUM(IF(status = 'G' OR status = 'D', 1,0)) AS aantal_openstaand,
  SUM(IF(YEAR(datum) = '$ditJaar',   (bedrag_incl),0)) AS in_ditjaar,
  SUM(IF(YEAR(datum) = '$ditJaar',   (bedrag_ex_h + bedrag_ex_l + bedrag_0 + bedrag_vh + bedrag_vl ),0)) AS ex_ditjaar,
  SUM(IF(YEAR(datum) = '$ditJaar',   1,0)) AS aantal_ditjaar,
  SUM(IF(YEAR(datum) = '$vorigJaar', (bedrag_incl),0)) AS in_vorigjaar,
  SUM(IF(YEAR(datum) = '$vorigJaar', (bedrag_ex_h + bedrag_ex_l + bedrag_0 + bedrag_vh + bedrag_vl ),0)) AS ex_vorigjaar,
  SUM(IF(YEAR(datum) = '$vorigJaar', 1,0)) AS aantal_vorigjaar,
  SUM(IF(YEAR(datum) < '$vorigJaar', (bedrag_incl),0)) AS in_oudejaren,
  SUM(IF(YEAR(datum) < '$vorigJaar', (bedrag_ex_h + bedrag_ex_l + bedrag_0 + bedrag_vh + bedrag_vl ),0)) AS ex_oudejaren,
  SUM(IF(YEAR(datum) < '$vorigJaar', 1,0)) AS aantal_oudejaren,
  SUM(IF(status = 'N', (bedrag_incl - bedrag_voldaan ),0)) AS in_nietinbaar,
  SUM(IF(status = 'N', (bedrag_ex_h + bedrag_ex_l + bedrag_0 + bedrag_vh + bedrag_vl ),0)) AS ex_nietinbaar,
  SUM(IF(status = 'N', 1,0)) AS aantal_nietinbaar

FROM
  facmod_factuurbeheer
$whereStr
  ";
$db->SQL($query); //echo $query;
$omzetData = $db->lookupRecord();




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
if ($_GET["copyDone"] == 1)
  $referer = urlencode($_SESSION["NAV"]->currentScript."?".$_SESSION["NAV"]->currentQueryString);
?>


<table class="list_tabel" cellspacing="0" border="0">
<?=$list->printHeader();?>
<?php
$cfg = new AE_config();
$betaalTermijn = $cfg->getData("betalingstermijn");
while($data = $list->getRow())
{


	if ($data["status"]["value"] == "G" OR $data["status"]["value"] == "D" )
	{

	  $data["dagenOpen"]["value"] = julDag(mktime()- db2jul($data["datum"]["value"])) ;
	  if ($betaalTermijn < $data["dagenOpen"]["value"])
	  {
	    $data["tr_class"] = "list_dataregel_rood";
	  }
	  else
	  {
	    $data["tr_class"] = "list_dataregel_zand";
	  }
	  $data["openstaand"]["value"]   = number_format($data["bedrag_incl"]["value"]-$data["bedrag_voldaan"]["value"],2,".","");
	  $data["dagenBetaald"]["value"] = "";
	}
	else
	{
	  $data["dagenBetaald"]["value"] = $data["betaal_dagen"]["value"];
	  $data["dagenOpen"]["value"]    = "";
	  $data["openstaand"]["value"]   = "voldaan";
	}

	if ($deb_id < 1)
  {
    $data["firmanaam"]["value"]  = rclip($data["firmanaam"]["value"],35);
  }

	$data["status"]["value"]     = $__facmod["debiteurStatus"][$data["status"]["value"]];

	if ($data["bedrag_incl"]["value"] < 0)
  {
    $data["bedrag_incl"]["td_style"] = " style='color:Red; 	font-weight: bold;' ";
  }
	else
  {
    $data["bedrag_incl"]["td_style"] = " style='color:Black;  font-weight: bold;' ";
  }


	$data["copy"]["value"] = "<a href='facmod_factuurAfdrukkenPDF.php?invoiceNr=".$data["facnr"]["value"]."&referer=".$referer."' target='pdfOutput'><img src='images/16/afdrukken.gif' border='0' title='klik hier om een kopie factuur af te drukken'></a>";
	// $list->buildRow($data,$template="",$options="");
	echo $list->buildRow($data);
}
?>
</table>
<br>


<table border="0" width="300" cellspacing="2" cellpadding="3" align="center">
<tr>
<td colspan="4" align="center" bgcolor="#CCCCCC"><b>omzet historie van selectie</b></td>
</tr>
<tr>
  <td bgcolor="#EEEEEE">
    &nbsp;
  </td>
  <td bgcolor="#EEEEEE">
    aantal
  </td>
  <td bgcolor="#EEEEEE" align="right">
    excl. BTW
  </td>
  <td bgcolor="#EEEEEE" align="right">
    incl. BTW
  </td>
</tr>
<tr>
  <td ><b>openstaand</b></td>
  <td align="center"><?=$omzetData["aantal_openstaand"]?></td>
  <td align="right"><?//=fBedrag($omzetData[ex_openstaand])?></td>
  <td align="right"><?=fBedrag($omzetData["in_openstaand"])?></td>
</tr>
<tr>
  <td ><b><?=$ditJaar?></b></td>
  <td align="center"><?=$omzetData["aantal_ditjaar"]?></td>
  <td align="right"><?=fBedrag($omzetData["ex_ditjaar"])?></td>
  <td align="right"><?=fBedrag($omzetData["in_ditjaar"])?></td>
</tr>
<tr>
  <td><b><?=$vorigJaar?></b></td>
  <td align="center"><?=$omzetData["aantal_vorigjaar"]?></td>
  <td align="right"><?=fBedrag($omzetData["ex_vorigjaar"])?></td>
  <td align="right"><?=fBedrag($omzetData["in_vorigjaar"])?></td>
</tr>
<tr>
  <td><b>ouder dan <?=$vorigJaar?></b></td>
  <td align="center"><?=$omzetData["aantal_oudejaren"]?></td>
  <td align="right"><?=fBedrag($omzetData["ex_oudejaren"])?></td>
  <td align="right"><?=fBedrag($omzetData["in_oudejaren"])?></td>
</tr>
<tr>
  <td><b>niet inbaar</b></td>
  <td align="center"><?=$omzetData["aantal_nietinbaar"]?></td>
  <td align="right"><?=fBedrag($omzetData["ex_nietinbaar"])?></td>
  <td align="right"><?=fBedrag($omzetData["in_nietinbaar"])?></td>
</tr>
</table>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>