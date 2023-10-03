<?php
/*
    AE-ICT sourcemodule created 25 sep. 2020
    Author              : Chris van Santen
    Filename            : rekeningmutaties_v2_List.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
$AETemplate = new AE_template();
$AERekeningmutaties = new AE_RekeningMutaties();

$data = array_merge($_POST, $_GET);
$editScript = "rekeningmutaties_v2_Edit.php";

$mutationType = '';
$table = 'Rekeningmutaties';
$object = 'Rekeningafschriften';
if ( isset ($data['type']) && $data['type'] === 'temp')
{
  $table = 'VoorlopigeRekeningmutaties';
  $object = 'VoorlopigeRekeningafschriften';
  $mutationType = 'temp';
}

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
  $afschrift = new $object();
  $afschrift->getById($afschrift_id);
  
  $list->addField($table,"id",array("width"=>100,"search"=>false));
  $list->addField($table,"Volgnummer",array("search"=>false));
  $list->addField($table,"Boekdatum",array("search"=>false));
  $list->addField($table,"Omschrijving",array("search"=>false));
  $list->addField($table,"Grootboekrekening",array("description"=>vt("Grootbk."),"width"=>50,"search"=>false));
  $list->addField($table,"Transactietype",array("description"=>"T","align"=>"right","search"=>false));
  $list->addField($table,"Valuta",array("list_align"=>"right","width"=>50,"search"=>false));
  $list->addField($table,"Valutakoers",array("list_format"=>"", "list_align"=>"right","width"=>100,"search"=>false));
  $list->addField($table,"Fonds",array("width"=>100,"search"=>false));
  $list->addField($table,"Aantal",array("width"=>35,"search"=>false));
  $list->addField($table,"Fondskoers",array("list_format"=>"", "width"=>100,"search"=>false));
  $list->addField($table,"Debet",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addField($table,"Credit",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addField($table,"Bedrag",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addField($table,"bankTransactieId",array("list_align"=>"right","width"=>100,"search"=>false, 'description' => 'Banktransactieid'));
  $list->addField($table,"change_user",array("description"=>vt("Aangepast door"),"list_align"=>"right","width"=>100,"search"=>false));
  $list->addField($table,"add_user",array("description"=>vt("Toegevoegd door"),"list_align"=>"right","width"=>100,"search"=>false));
  
  $list->addField($table,"add_date",array("list_invisible"=>true));

  $list->addField($table,"Rekening",array("list_invisible"=>true));
//  $list->addField("","fondssoort",array('sql_alias'=>'Fondsen.fondssoort',"width"=>"","search"=>true));

  $list->addField("","einddatum",array('sql_alias'=>'Fondsen.EindDatum',"list_invisible"=>true));
  $list->addField("","triggers",array());
  $list->setJoin("LEFT JOIN Fondsen ON $table.Fonds = Fondsen.Fonds");

  $allow_add = true;
	$list->queryWhere = " Rekening = '".$afschrift->get("Rekening")."' AND Afschriftnummer = '".$afschrift->get("Afschriftnummer")."' ";
}
else
{
  $list->addFixedField($table,"id",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addFixedField($table,"Volgnummer",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->addFixedField($table,"Boekdatum",array("list_align"=>"right","width"=>100,"search"=>false));
  $list->categorieVolgorde[$table]=array('Algemeen');
  $list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');
  $html = $list->getCustomFields(array($table,'Portefeuilles'),'rekMut');
  $list->ownTables=array($table);

	if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
    $internDepotToegang="OR Portefeuilles.interndepot=1";

  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	{
	    $list->setJoin("Inner Join Rekeningen ON $table.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0  AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang ) ");
	}
	else
	{
     $list->setJoin("Inner Join Rekeningen ON $table.Rekening = Rekeningen.Rekening
    Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
   Inner Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
   Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
   Inner Join Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker = Gebruikers.Gebruiker");
// $list->queryWhere=" (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Gebruikers.Gebruiker='$USR' ";

     $list->setJoin("
Inner Join Rekeningen ON $table.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 AND Portefeuilles.Vermogensbeheerder IN (
   SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder FROM VermogensbeheerdersPerGebruiker
 	 JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
 	 WHERE (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' AND Gebruikers.Gebruiker='$USR'  )
 	 )");
	}
  $_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem($html,"");
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

/** get editcontent style to content style else it gets broken **/
$content['style'] = $editcontent['style'];
$content['style'] .= $AETemplate->loadCss('colorCodingMutationsList', 'classTemplates/rekeningmutaties/css');


$content['javascript'] .= "
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
<?=
$list->printHeader();
?>
<?php
$template = '
<tr class="list_dataregel {tr_class_value}">
  <td class="list_button">
    <div class="icon">
      <a href="rekeningmutaties_v2_Edit.php?action=edit&id={id_value}&afschrift_id=' . $afschrift_id . '&type=' . $mutationType . '&mutatieId=' . $_GET['mutatieId'] . '" target="content" >
        <img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;
      </a>
    </div>
  </td>
  <td width="25" align="right">{Volgnummer_value} &nbsp;</td>
  <td width="80" >{Boekdatum_value} &nbsp;</td>
  <td nowrap >{Omschrijving_value} &nbsp;</td>
  <td width="50" >{Grootboekrekening_value} &nbsp;</td>
  <td width="50" align="right">{Transactietype_value} &nbsp;</td>
  <td width="50" >{Valuta_value} &nbsp;</td>
  <td width="100" >{Valutakoers_value} &nbsp;</td>
  <td nowrap >{Fonds_value} &nbsp;</td>
  <td width="35" align="right">{Aantal_value} &nbsp;</td>
  <td width="100" align="right">{Fondskoers_value} &nbsp;</td>
  <td width="100" align="right">{Debet_value} &nbsp;</td>
  <td width="100" align="right">{Credit_value} &nbsp;</td>
  <td width="100" align="right">{Bedrag_value} &nbsp;</td>
  <td width="100" align="right">{bankTransactieId_value} &nbsp;</td>
  <td width="100" align="right">{change_user_value} &nbsp;</td>
  <td width="100" align="right">{add_user_value} &nbsp;</td>
  <td> {triggers_value}</td>
</tr>
';

echo '
  <style>
    .list_tabel > tbody > tr:hover {
      background-color: #E8E8E8;
    }
  </style>
';

while($data = $list->getRow()) {
  
//  $data =  $AERekeningmutaties->checkMutationsForErrors($data);
//  
//  echo $list->buildRow($data);
  
  $data =  $AERekeningmutaties->checkMutationsForErrors($data);
  if ( isset($data['tr_class'])) {
    $data['tr_class'] = array(
      'value' => $data['tr_class'],
      'field' => 'tr_class'
    );
  }
  echo $list->buildRow($data, $template);
}

//while($data = $list->printRow($template))
//{ 
//  debug(htmlentities($data));
//	echo $AERekeningmutaties->checkMutationsForErrors($data);
////  
//}
?>
</table>
<?

  echo template($__appvar["templateContentFooter"],$content);
}

include_once("classTemplates/rekeningmutaties/colorCodingLegend.html");

logAccess();
if($__debug)
{
	echo getdebuginfo();
}
