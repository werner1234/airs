<?php
/*
    AE-ICT sourcemodule created 25 sep. 2020
    Author              : Chris van Santen
    Filename            : voorlopigeRekeningmutatiesList.php


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "voorlopigeRekeningmutatiesEdit.php";

$subHeader     = vt("voorlopige Rekeningmutaties");
$mainHeader    = vt("overzicht");

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript ;
$list->perPage = 1000;


$list->addField("VoorlopigeRekeningmutaties","id",array("width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Volgnummer",array("description"=>"#","list_align"=>"right","width"=>25,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Boekdatum",array("width"=>80,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Omschrijving",array("search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Grootboekrekening",array("description"=>"Grootbk.","width"=>50,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Transactietype",array("description"=>"T","align"=>"right","search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Valuta",array("list_align"=>"right","width"=>50,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Valutakoers",array("list_align"=>"right","width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Fonds",array("width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Aantal",array("width"=>35,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Fondskoers",array("width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Debet",array("list_align"=>"right","width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Credit",array("list_align"=>"right","width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","Bedrag",array("list_align"=>"right","width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningmutaties","change_user",array("description"=>"Aangepast door","list_align"=>"right","width"=>100,"search"=>false));

$allow_add = true;


if(!$afschrift_id)
  $afschrift_id=$_GET['afschrift_id'];
// get afschriftgegevens.
$afschrift = new VoorlopigeRekeningafschriften();
if($afschrift_id && $afschrift->getById($afschrift_id))
{
	$list->queryWhere = " Rekening = '".$afschrift->get("Rekening")."' AND Afschriftnummer = '".$afschrift->get("Afschriftnummer")."' ";
}

if(empty($_GET['sort'])) {
	$_GET['sort'] = array("Volgnummer");
	$_GET['direction'] = array("ASC");
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

if($list->records() == 0)
{
  $_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
  $_SESSION['NAV']->addItem(new NavEdit("editForm", true,true,true));
}

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<table class="list_tabel" cellspacing="0">
<?=
$list->printHeader();
?>
<?php
$template = '<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'">
<td class="list_button"><div class="icon"><a href="voorlopigeRekeningmutatiesEdit.php?action=edit&id={id_value}&afschrift_id='.$afschrift_id.'" target="content" ><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a></div></td>
<td class="listTableData" width="25" align="right">{Volgnummer_value} &nbsp;</td>
<td class="listTableData" width="80" >{Boekdatum_value} &nbsp;</td>
<td class="listTableData" nowrap >{Omschrijving_value} &nbsp;</td>
<td class="listTableData" width="50" >{Grootboekrekening_value} &nbsp;</td>
<td class="listTableData" width="50" align="right">{Transactietype_value} &nbsp;</td>
<td class="listTableData" width="50" >{Valuta_value} &nbsp;</td>
<td class="listTableData" width="100" >{Valutakoers_value} &nbsp;</td>
<td class="listTableData" nowrap >{Fonds_value} &nbsp;</td>
<td class="listTableData" width="35" align="right">{Aantal_value} &nbsp;</td>
<td class="listTableData" width="100" align="right">{Fondskoers_value} &nbsp;</td>
<td class="listTableData" width="100" align="right">{Debet_value} &nbsp;</td>
<td class="listTableData" width="100" align="right">{Credit_value} &nbsp;</td>
<td class="listTableData" width="100" align="right">{Bedrag_value} &nbsp;</td>
<td class="listTableData" width="100" align="right">{change_user_value} &nbsp;</td>
<td class="listTableData"> &nbsp;</td>
</tr>';

while($data = $list->printRow($template))
{
	echo $data;
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateContentFooter"],$content);
?>