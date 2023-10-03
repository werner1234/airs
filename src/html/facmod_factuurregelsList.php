<?php
/*
    AE-ICT CODEX source module versie 1.2, 26 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/25 09:30:52 $
    File Versie         : $Revision: 1.3 $

    $Log: facmod_factuurregelsList.php,v $
    Revision 1.3  2019/11/25 09:30:52  cvs
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

$_SESSION["facmod"]["returnUrl"] = $_SERVER["REQUEST_URI"];
$_SESSION["facmod"]["text"]      = "Terug naar overzicht";

if($switch && $id)
{
	$select = "SELECT wachtstand FROM facmod_factuurregels WHERE  id = '".$id."'";
	$DB = new DB();
	$DB->SQL($select);
	if($DB->Query())
	{
		$regel = $DB->nextRecord();
		switch($switch)
		{
			case "wachtstand" :
				$upvar = " wachtstand = '".(($regel["wachtstand"]==0)?"1":"0")."' ";
			break;
			default :
				$upvar = "";
			break;
		}

		if($upvar)
		{

			$DB->SQL("UPDATE facmod_factuurregels SET ".$upvar." WHERE id = '".$id."'");
			if($DB->Query())
			{
				header("Location: ".$_SESSION["pdfReturnUrl"]);
				exit;
			}
		}
	}
}


session_start();
//aetodo: auth inbouwen
//include_once("facmod_auth.php");  // check level van gebruiker geeft $myLevel terug
$_SESSION["pdfReturnUrl"] = $_SERVER["REQUEST_URI"];

$subHeader     = "";
$mainHeader    = "factuurregel overzicht";
$setWhere      = array();
$editScript = "facmod_factuurregelsEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("facmod_factuurregels","id",array("list_width"=>"100","search"=>false));
$list->addColumn("facmod_factuurregels","facnr",array("list_width"=>"80","search"=>false));
$list->addColumn("facmod_factuurregels","aantal",array("list_width"=>"50","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_factuurregels","eenheid",array("list_width"=>"50","search"=>false,"list_align"=>"center"));
$list->addColumn("facmod_factuurregels","artnr",array("list_width"=>"50","search"=>true));
$list->addColumn("facmod_factuurregels","txt",array("list_width"=>"","search"=>true));
$list->addColumn("facmod_factuurregels","stuksprijs",array("list_width"=>"100","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_factuurregels","btw",array("list_width"=>"30","search"=>false,"list_align"=>"center"));
$list->addColumn("facmod_factuurregels","totaal_excl",array("list_width"=>"100","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_factuurregels","wachtstand",array("description"=>"wacht","list_width"=>"50","search"=>false,"list_align"=>"center"));
$list->addColumn("facmod_factuurregels","volgnr",array("description"=>"prio","list_width"=>"40","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_factuurregels","rubriek",array("list_width"=>"","search"=>false));
$list->addColumn("","copyRec",array("description"=>" ","list_width"=>"40","search"=>false,"list_align"=>"right"));
$list->addColumn("facmod_factuurregels","rel_id",array("description"=>"prio","list_width"=>"40","search"=>false,"list_invisible"=>"false"));
$list->addColumn("","naam",array('sql_alias'=>'CRM_naw.zoekveld',"description"=>"prio","list_width"=>"40","search"=>false,"list_invisible"=>"true"));

$list->setJoin("LEFT JOIN CRM_naw ON CRM_naw.id=facmod_factuurregels.rel_id");

$_SESSION["submenu"] = New Submenu();
if ($_GET["deb_id"] > 0)
{
  $db = new DB();
  $query = "SELECT * FROM CRM_naw WHERE id = ".$_GET["deb_id"];
  $nawRec = $db->lookupRecordByQuery($query);
  $subHeader = " bij <b>".$nawRec["naam"].", ".$nawRec["plaats"]."</b>";

  $setWhere[] = "rel_id = ".$_GET["deb_id"];
  $addExtra = "&rel_id=".$_GET["deb_id"];


  $_SESSION[submenu]->addItem("Terug naar NAW ","CRM_nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");

}
else
{
//  $list->addColumn("","relatie",array("sql_alias"=>"CRM_naw.naam","list_width"=>"","search"=>true,"list_invisible"=>true));
//  $list->setJoin(" join naw ON facmod_factuurregels.rel_id = naw.id ");

}
$suppresCopy = false;
switch ($_GET['do'])
{
	case "notinvoicedList":
    $suppresCopy = true;
	case "notinvoiced":
    $subHeader .= ", Niet gefactureerde factuurregels";
	  $setWhere[] = "facnr < 1";
	  $_GET['sort'][]      = "facmod_factuurregels.rel_id";
    $_GET['direction'][] = "ASC";
    $_GET['sort'][]      = "facmod_factuurregels.volgnr";
    $_GET['direction'][] = "ASC";
    $list->removeColumn("facnr");
//    $list->removeColumn("copyRec");

		break;
  case "old":
    $subHeader .= ", eerder gefactureerde factuurregels";
	  $setWhere[] = "facnr > 0";
	  $_GET['sort'][]      = "facmod_factuurregels.facnr";
    $_GET['direction'][] = "DESC";
    $_GET['sort'][]      = "facmod_factuurregels.volgnr";
    $_GET['direction'][] = "ASC";
		break;
	default:
	  $subHeader .= ", alle factuurregels";
	  $_GET['sort'][]      = "facmod_factuurregels.facnr";
    $_GET['direction'][] = "DESC";
	  $_GET['sort'][]      = "facmod_factuurregels.volgnr";
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
$query = "
SELECT
  SUM(IF(wachtstand = 1  , totaal_excl,0))  AS wachtstand,
  SUM(IF(wachtstand = 1  , 1          ,0))  AS aantalWachtstand,
  SUM(IF(wachtstand <> 1 , totaal_excl,0))  AS factureerbaar,
  SUM(IF(wachtstand <> 1 , 1          ,0))  AS aantalFactureerbaar
FROM
  facmod_factuurregels
$whereStr
  ";
// echo $query;
$db->SQL($query);
$omzetData = $db->lookupRecord();


$prevRelatie = "";
while($data = $list->getRow())
{
// debug($data, $prevRelatie);
  if (key_exists("facnr",$data))
  {
    if ($data["facnr"]["value"] < 1 )
    {
      if ($data["wachtstand"]["value"] == 1)
        $data["facnr"]["value"] = "- wacht -";
      else
        $data["facnr"]["value"] = "";
    }

  }

  if (!$suppresCopy)
  {
    $data["copyRec"]["value"] = "<a href=\"".$editScript."?action=edit&copyrec=true&id=".$data["id"]["value"]."\"><img src=\"images/16/save.gif\" border=\"0\" title=\"klik hier om deze factuurregel te kopieeren\"></a>";
  }

  $data["txt"]["value"] = nl2br($data["txt"]["value"]);
  if ($data["totaal_excl"]["value"] == 0)
  {
    $data["tr_class"] = "list_dataregel_zand";
  }


  if ($data["totaal_excl"]["value"] < 0)
  {
    $data["tr_class"] = "list_dataregel_rood";
  }


  if ($prevRelatie <> $data["naam"]["value"])
  {
    $prevRelatie = $data["naam"]["value"];
    $blankRowTemplate = "
    <tr style='background-color:Silver; height:30px' >
      <td colspan='99'>
        <a href='facmod_factuurMaakFactuur.php?deb_id=".$data["rel_id"]["value"]."'>&nbsp;<img src='images/16/afdrukken.gif' border='0' title='klik hier om een factuur af te drukken'> Maak factuur</a>&nbsp;&nbsp;&nbsp;
        <a href='CRM_nawList.php?page=1&selectie=".urldecode($prevRelatie)."'><b>$prevRelatie</b></a>
    &nbsp;&nbsp;&nbsp;</td>
    </tr>\n";

    echo $list->buildRow(array(),$blankRowTemplate);
  }
	// $list->buildRow($data,$template="",$options="");
	 $data["wachtstand"]["form_type"] = "text";
   $data["wachtstand"]["value"] = "<a href='".$PHP_SELF."?".$str."&switch=wachtstand&id=".$data["id"]["value"]."'>".
                               		imagecheckbox($data["wachtstand"]["value"])."</a>";
	echo $list->buildRow($data);

}
?>
</table>

<br>
<?
if ($list->records() > 0 AND $_GET["do"] <> "old")
{
?>
<table border="0" width="300" cellspacing="2" cellpadding="3" align="center">
<tr>
<td colspan="4" align="center" bgcolor="#CCCCCC"><b>statistiek van selectie</b></td>
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
</tr>
<tr>
  <td ><b>openstaand</b></td>
  <td align="right"><?=$omzetData["aantalFactureerbaar"]?></td>
  <td align="right"><?=fBedrag($omzetData["factureerbaar"])?></td>
</tr>
<tr>
  <td ><b>wachtstand</b></td>
  <td align="right"><?=$omzetData["aantalWachtstand"]?></td>
  <td align="right"><?=fBedrag($omzetData["wachtstand"])?></td>
</tr>
<tr>
  <td colspan="5"><hr></td>
</tr>
<tr>
  <td><b>totaal</b></td>
  <td align="right"><?=$omzetData["aantalWachtstand"]+$omzetData["aantalFactureerbaar"]?></td>
  <td align="right"><?=fBedrag($omzetData["wachtstand"] + $omzetData["factureerbaar"])?></td>

</tr>
</table>
<?
}

logAccess();
if($__debug)
{
	echo getdebuginfo();
}


echo template($__appvar["templateRefreshFooter"],$content);


?>