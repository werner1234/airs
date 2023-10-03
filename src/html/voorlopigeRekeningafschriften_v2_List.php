<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/04/12 11:47:16 $
    File Versie         : $Revision: 1.6 $

    $Log: voorlopigeRekeningafschriften_v2_List.php,v $
    Revision 1.6  2020/04/12 11:47:16  rvv
    *** empty log message ***

*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "rekeningafschriften_v2_Edit.php";
$subHeader     = vt("voorlopige Rekeningafschriften V2");
$mainHeader    = vt("overzicht");

//aevertaal: foute var $_post
$data = array_merge($_post, $_GET);

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$__appvar['rowsPerPage']=100;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("VoorlopigeRekeningafschriften","id",array("width"=>100,"search"=>false));
$list->addField("Portefeuilles","Client",array("width"=>100,"search"=>true));
$list->addField("VoorlopigeRekeningafschriften","Rekening",array("search"=>true));
$list->addField("VoorlopigeRekeningafschriften","Afschriftnummer",array("width"=>120,"search"=>true));
$list->addField("VoorlopigeRekeningafschriften","Datum",array("width"=>100,"align"=>"right","search"=>false));
$list->addField("VoorlopigeRekeningafschriften","Saldo",array("width"=>100,"align"=>"right","search"=>false));
$list->addField("VoorlopigeRekeningafschriften","NieuwSaldo",array("width"=>100,"align"=>"right","search"=>false));
$list->addField("VoorlopigeRekeningafschriften","Verwerkt",array("align"=>"center","width"=>100,"search"=>false));
$list->addField("VoorlopigeRekeningafschriften","add_user",array("width"=>100,"align"=>"right","search"=>true));
$list->addField("VoorlopigeRekeningafschriften","add_date",array("width"=>100,"align"=>"right","search"=>true));
$list->addField("Rekeningen","Rekening",array("list_invisible"=>true));
$list->addField("Rekeningen","Memoriaal",array("list_invisible"=>true));

$allow_add = true;

if(empty($_GET['sort']))
{
	$_GET['sort'] = array("VoorlopigeRekeningafschriften.Datum","VoorlopigeRekeningafschriften.Rekening");
	$_GET['direction'] = array("DESC","ASC");
}

$memSelect = '';
if( isset ($data['memoriaal']) && $data['memoriaal'] === 1 )
{
	$memSelect = 1;
}

if($status == "verwerkt")
	$verwerkt = "AND VoorlopigeRekeningafschriften.verwerkt = '1'";
elseif($status == "verzonden")
	$verwerkt = "AND VoorlopigeRekeningafschriften.verwerkt = '2'";
elseif($status == "gedeeltelijk")
	$verwerkt = "AND VoorlopigeRekeningafschriften.verwerkt = '3'";
else
	$verwerkt = "AND VoorlopigeRekeningafschriften.verwerkt = '0'";

$list->setWhere("Portefeuilles.Portefeuille = Rekeningen.Portefeuille $verwerkt AND VoorlopigeRekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.Consolidatie=0");


// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

if ($list->records() > 0 && $status == "" && ($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0 || $__appvar['master'] == 1 ))
{
	$_SESSION['submenu'] = New Submenu();
	if($__appvar['master'] == 1)
	{
	  $_SESSION['submenu']->addItem("Doorvoeren","voorlopigeRekeningmutatiesVerwerk.php?memoriaal=$memSelect");
	}
	else
	{
	 // $_SESSION['submenu']->addItem("Verzenden","voorlopigeRekeningmutatiesVerwerk.php?memoriaal=$memSelect");
		$_SESSION['submenu']->addItem("Verzenden",'javascript:parent.frames[\'content\'].verzenden(\'\');');
	}
}

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new&memoriaal=".$memoriaal."&type=temp';
}

function verzenden()
{
  document.listForm.submit();
}


";
$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<form action="voorlopigeRekeningafschriften_v2_List.php" method="GET"  name="controleForm">
<input type="hidden" name="memoriaal" value="<?=$memoriaal?>">
  <?=vt("Overzicht")?> :
<select name="status" onChange="document.controleForm.submit()">
<?
	if($__appvar['master'] == 1)
	{
	  ?>
<option value="" <?=($status=="verwerkt")?"":"selected"?>><?=vt("Niet verwerkt")?></option>
<option value="verwerkt" <?=($status=="verwerkt")?"selected":""?>><?=vt("Verwerkt")?></option>
	  <?
	}
	else
	{
	  ?>
<option value="" <?=($status=="verwerkt")?"":"selected"?>><?=vt("Niet verzonden")?></option>
<option value="verwerkt" <?=($status=="verwerkt")?"selected":""?>><?=vt("Verwerkt")?></option>
<option value="verzonden" <?=($status=="verzonden")?"selected":""?>><?=vt("Verzonden")?></option>
<option value="gedeeltelijk" <?=($status=="gedeeltelijk")?"selected":""?>><?=vt("Gedeeltelijk verwerkt")?></option>
	  <?
	}
?>

</select>
<input type="submit" value="<?=vt("Overzicht")?>">
</form>
<br>
<form name="listForm" method="POST" action="voorlopigeRekeningmutatiesVerwerk.php?memoriaal=<?=$memSelect?>">
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$DB=new DB();
while($data = $list->getRow($template))
{
// check of totaal gelijk is aan mutatiebedrag!
$mutatieBedrag = round(($data["NieuwSaldo"]["value"] - $data["Saldo"]["value"]),2);
// Haal totaal mutaties op

$DB->SQL("SELECT SUM(Bedrag) AS Totaal FROM VoorlopigeRekeningmutaties WHERE Afschriftnummer = '".$data["Afschriftnummer"]["value"]."' AND Rekening = '".$data["Rekening"]["value"]."'");
$DB->Query();
$totaal = $DB->NextRecord();
// Reken mutatieverschil uit
$mutatieVerschil = $mutatieBedrag - round($totaal['Totaal'],2);
// Zet Fieldset Class voor mutatie veschil
if($mutatieVerschil  <> 0)
{
	$class  = "list_rekeningmutatie_verschil";
}
else
{
	$class  = "list_dataregel";
}

//print_r($data);

//sprintf($row[list_format], $printdata)
	if($__appvar['master'] <> 1 &&  $data['Verwerkt']['value']==0 && $class  <> "list_rekeningmutatie_verschil")
	{
		$checkbox='<input type="checkbox" name="mutatieId_'.$data['id']['value'].'" value="1" checked >';
		$extraStyle=' style="width:50px"';
	}
	else
	{
		$checkbox='';
		$extraStyle='';
	}

echo $template = '<tr class="'.$class.'" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\''.$class.'\'">
<td class="list_button" '.$extraStyle.'>
<a href="rekeningmutaties_v2_Edit.php?action=new&afschrift_id='.$data['id']['value'].'&type=temp"><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a>
'.$checkbox.'
</td>
<td class="listTableData" width="130" >'.$data['Client']['value'].'&nbsp;</td>
<td class="listTableData" width="130" >'.$data['Rekening']['value'].' &nbsp;</td>
<td class="listTableData" width="130" >'.$data['Afschriftnummer']['value'].'&nbsp;</td>
<td class="listTableData" width="100" align="right">'.jul2form(db2jul($data['Datum']['value'])).'&nbsp;</td>
<td class="listTableData" width="100" align="right">'.sprintf($data['Saldo']['list_format'],$data['Saldo']['value']).'&nbsp;</td>
<td class="listTableData" width="100" align="right">'.sprintf($data['NieuwSaldo']['list_format'],$data['NieuwSaldo']['value']).'&nbsp;</td>
<td class="listTableData" width="100" align="center">'.checkboximage($data['Verwerkt']['value']).'&nbsp;</td>
<td class="listTableData" width="130" align="center">'.$data['add_user']['value'].'&nbsp;</td>
<td class="listTableData" width="130" align="center">'.$data['add_date']['value'].'&nbsp;</td>
</tr>';

}
?>
</table>
</form>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);

