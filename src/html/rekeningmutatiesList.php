<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "rekeningmutatiesEdit.php";


if($afschrift_id)
{
  $list = new MysqlList();
  $list->idField = "id";
}
else
{
  $list = new MysqlList2();
}

$list->editScript = $editScript ;
$list->perPage = 1500;

// get afschriftgegevens.

if($afschrift_id)
{
  $afschrift = new Rekeningafschriften();
  $afschrift->getById($afschrift_id);
  $list->addField("Rekeningmutaties","id",array("width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","Volgnummer",array("search"=>false));
  $list->addField("Rekeningmutaties","Boekdatum",array("search"=>false));
  $list->addField("Rekeningmutaties","Omschrijving",array("search"=>false));
  $list->addField("Rekeningmutaties","Grootboekrekening",array("description"=>"Grootbk.","width"=>50,"search"=>false));
  $list->addField("Rekeningmutaties","Transactietype",array("description"=>"T","align"=>"right","search"=>false));
  $list->addField("Rekeningmutaties","Valuta",array("list_align"=>"right","width"=>50,"search"=>false));
  $list->addField("Rekeningmutaties","Valutakoers",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","Fonds",array("width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","Aantal",array("width"=>35,"search"=>false));
  $list->addField("Rekeningmutaties","Fondskoers",array("width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","Debet",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","Credit",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","Bedrag",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","change_user",array("description"=>"Aangepast door","list_align"=>"right","width"=>100,"search"=>false));
  $list->addField("Rekeningmutaties","add_user",array("description"=>"Toegevoegd door","list_align"=>"right","width"=>100,"search"=>false));
  $allow_add = true;
	$list->queryWhere = " Rekening = '".$afschrift->get("Rekening")."' AND Afschriftnummer = '".$afschrift->get("Afschriftnummer")."' ";
}
else
{
  $list->addFixedField("Rekeningmutaties","id",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addFixedField("Rekeningmutaties","Volgnummer",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addFixedField("Rekeningmutaties","Boekdatum",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->categorieVolgorde['Rekeningmutaties']=array('Algemeen');
  $list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');
  $list->categorieVolgorde['Rekeningen']=array('Algemeen');
  $list->categorieVolgorde['Fonds']=array('Algemeen');
  $html = $list->getCustomFields(array('Rekeningmutaties','Portefeuilles','Rekeningen','Fonds'),'rekMut');
  $list->ownTables=array('Rekeningmutaties');

	if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
    $internDepotToegang="OR Portefeuilles.interndepot=1";

  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	{
	    $list->setJoin("Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0 
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang ) AND Portefeuilles.consolidatie=0 ");
	}
	else
	{
	 /*
     $list->setJoin("Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
    Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
   Inner Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
   Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
   Inner Join Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker = Gebruikers.Gebruiker");
*/
// $list->queryWhere=" (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Gebruikers.Gebruiker='$USR' ";

 foreach ($list->columns as $colData)
 {
    if($colData['objectname'] == 'Fonds')
    {
      $joinFondsen=" LEFT JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds ";
    }
 }
     $list->setJoin("
     $joinFondsen
Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille  AND Portefeuilles.consolidatie=0 AND Portefeuilles.Vermogensbeheerder IN (
   SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder FROM VermogensbeheerdersPerGebruiker
 	 JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
 	 WHERE (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' AND Gebruikers.Gebruiker='$USR'  )
 	 )");
	}
  $_SESSION['submenu'] = New Submenu();
	if (count($__rekmutGlobe) > 0)
  {
    $_SESSION['submenu']->addItem("Export Exact Globe","rekeningmutatieExport.php");
  }
  $_SESSION['submenu']->addItem("Export RM","rekeningmutatieExport.php?type=RM");
  $_SESSION['submenu']->addItem($html,"");
}

$list->idTable='Rekeningmutaties';

if(empty($_GET['sort'])) {
	$_GET['sort'] = array("Volgnummer");
	$_GET['direction'] = array("ASC");
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);
//debug($list->getSQL());
$_SESSION["rekmutQuery"] = $list->getSQL();


$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);

if(!$afschrift_id)
{
  session_start();
  $_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
  $_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$allow_add));
  $_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
  session_write_close();

  echo '<br>';
  echo $list->filterHeader();
  echo '<table class="list_tabel" cellspacing="0">';
  echo $list->printHeader();//true
  while($data = $list->getRow())
  {
    //$data['disableEdit']=true;
    //$data['id']['value']=0;
    $list->fullEditScript="rekeningmutaties_v2_Edit.php?action=edit&mutatieId=".$data['id']['value'];
    echo $list->buildRow($data);
  }
  echo '</table>';
  if($__debug)
	  echo getdebuginfo();
  echo template($__appvar["templateRefreshFooter"],$content);
}
else
{
?>
<br>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();



?>
<?php

$template = '<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'">
<td class="list_button"><div class="icon"><a href="rekeningmutatiesEdit.php?action=edit&id={id_value}&afschrift_id='.$afschrift_id.'" target="content" ><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a></div></td>
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
<td class="listTableData" width="100" align="right">{add_user_value} &nbsp;</td>
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
}
?>