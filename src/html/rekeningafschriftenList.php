<?php
/*
    AE-ICT sourcemodule created 28 sep. 2020
    Author              : Chris van Santen
    Filename            : rekeningafschriftenList.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$subHeader     = vt("rekeningafschriften");
$mainHeader    = vt("overzicht");

$editScript = "rekeningafschriftenEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->idTable = "Rekeningafschriften";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("Rekeningafschriften","id",array("width"=>100,"search"=>false));
$list->addFixedField("Rekeningafschriften","Rekening",array("search"=>true));
$list->addFixedField("Portefeuilles","Client",array("width"=>100,"search"=>true));
$list->addFixedField("Rekeningafschriften","Afschriftnummer",array("width"=>120,"search"=>true));
$list->addFixedField("Rekeningafschriften","Datum",array("width"=>100,"align"=>"right","search"=>false));
$list->addFixedField("Rekeningafschriften","Saldo",array("width"=>100,"align"=>"right","search"=>false));
$list->addFixedField("Rekeningafschriften","NieuwSaldo",array("width"=>100,"align"=>"right","search"=>false));
$list->addFixedField("Rekeningafschriften","Verwerkt",array("align"=>"center","width"=>100,"search"=>false));
$list->addFixedField("Rekeningafschriften","change_user",array("width"=>100,"align"=>"right","search"=>true));
$list->addFixedField("Rekeningen","Memoriaal",array("list_invisible"=>true));


$list->categorieVolgorde=array('Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'),'Rekeningafschriften'=>array('Algemeen'),
                               'Rekeningen'=>array('Algemeen'));

$list->ownTables=array('Rekeningafschriften');

$html = $list->getCustomFields(array('Rekeningafschriften','Portefeuilles','Rekeningen'));
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

$allow_add = checkAccess();

//if(count($_GET) == 2 && $_GET['status']=='verwerkt')
//  $jaarFilter=" AND YEAR(Rekeningafschriften.Datum)='".date('Y')."' " ;

if($memoriaal)
	$memSelect = "1";
else
	$memSelect = "0";

$type='portefeuille';
if(!checkAccess($type))
{
  if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
        $internDepotToegang="OR Portefeuilles.interndepot=1";
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	{
	    $list->setJoin("JOIN  Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening
        JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille ");
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang ) ";
	}
	else
	{
  $list->setJoin("
        JOIN  Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
        JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0
        JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
 				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ");
  $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}
else
{
  $list->setJoin("JOIN  Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
  JOIN  Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 ");
}


if($status == "verwerkt")
	$list->setWhere("Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND Rekeningafschriften.verwerkt = '1' AND Rekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.Memoriaal = '".$memSelect."' $beperktToegankelijk $jaarFilter ");
else
	$list->setWhere("Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND Rekeningafschriften.verwerkt = '0' AND Rekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.Memoriaal = '".$memSelect."' $beperktToegankelijk $jaarFilter ");


// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content["javascript"] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new&memoriaal=".$memoriaal."';
}
";
$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<form action="rekeningafschriftenList.php" method="GET"  name="controleForm">
<input type="hidden" name="memoriaal" value="<?=$memoriaal?>">
  <?=vt("Overzicht")?> :
<select name="status" onChange="document.controleForm.submit()">
<option value="" <?=($status=="verwerkt")?"":"selected"?>><?=vt("Niet verwerkt")?></option>
<option value="verwerkt" <?=($status=="verwerkt")?"selected":""?>><?=vt("Verwerkt")?></option>
</select>
<input type="submit" value="<?=vt("Overzicht")?>">
</form>
<br>
<?=$list->filterHeader();?>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
 $list->setFullEditScript('rekeningmutatiesEdit.php?action=new&afschrift_id={id}');
$DB=new DB();
while($data = $list->getRow())
{
// check of totaal gelijk is aan mutatiebedrag!
$mutatieBedrag = round(($data["Rekeningafschriften.NieuwSaldo"]["value"] - $data["Rekeningafschriften.Saldo"]["value"]),2);
// Haal totaal mutaties op

$DB->SQL("SELECT SUM(Bedrag) AS Totaal FROM Rekeningmutaties WHERE Afschriftnummer = '".$data["Rekeningafschriften.Afschriftnummer"]["value"]."' AND Rekening = '".$data["Rekeningafschriften.Rekening"]["value"]."'");
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
 $data['tr_class']=$class;
 echo $list->buildRow($data);



//sprintf($row[list_format], $printdata)
/*
echo '<tr class="'.$class.'" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\''.$class.'\'">
<td class="list_button"><div class="icon"><a href="rekeningmutatiesEdit.php?action=new&afschrift_id='.$data['id']['value'].'"><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a></div></td>
<td class="listTableData" width="130" >'.$data['Client']['value'].'&nbsp;</td>
<td class="listTableData" width="130" >'.$data['Rekening']['value'].' &nbsp;</td>
<td class="listTableData" width="130" >'.$data['Afschriftnummer']['value'].'&nbsp;</td>
<td class="listTableData" width="100" align="right">'.jul2form(db2jul($data['Datum']['value'])).'&nbsp;</td>
<td class="listTableData" width="100" align="right">'.sprintf($data['Saldo']['list_format'],$data['Saldo']['value']).'&nbsp;</td>
<td class="listTableData" width="100" align="right">'.sprintf($data['NieuwSaldo']['list_format'],$data['NieuwSaldo']['value']).'&nbsp;</td>
<td class="listTableData" width="100" align="center">'.checkboximage($data['Verwerkt']['value']).'&nbsp;</td>
<td class="listTableData" width="130" align="center">'.$data['change_user']['value'].'&nbsp;</td>
</tr>';
*/
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
