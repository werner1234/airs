<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/11/24 11:56:15 $
 		File Versie					: $Revision: 1.7 $

 		$Log: reportBuilder2.php,v $
 		Revision 1.7  2015/11/24 11:56:15  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AE_cls_tableDef.php");
$allow_add=false;
global $USR;
$db=new DB();
$table="reportbuilder_$USR";

$db->SQL("SELECT Rapport FROM $table limit 1");
$rapport=$db->lookupRecord();
$rapport=$rapport['Rapport'];


$tableDef=new tableDef($table,$table);

if(count($tableDef->fields)==0)
{
  echo "<br>Reportbuilder tabel niet gevonden.<br>";
  exit;
}

$db= new DB();

$query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();

$list = new MysqlList2();
$list->idTable=$table;
if($rapport=='Geaggregeerdoverzicht')
{ 
  $vermogensbeheerder=$gebruikPortefeuilleInformatie['Vermogensbeheerder'];
  $html = $list->getCustomFields(array($table,'Fonds','BeleggingscategoriePerFonds','BeleggingssectorPerFonds'),$rapport."_".$USR);
  $list->addFixedField($table,"Fonds",array("list_width"=>200,"search"=>true));
  $joinFondsen=" LEFT JOIN Fondsen ON $table.Fonds = Fondsen.Fonds";
  $joinFondsen.=" LEFT JOIN BeleggingscategoriePerFonds ON $table.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$vermogensbeheerder."'";
  $joinFondsen.=" LEFT JOIN BeleggingssectorPerFonds ON $table.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder='".$vermogensbeheerder."'";
}
else
{
  $list->categorieVolgorde=array(
    $table=>array('Algemeen'),
    'Naw'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo'),
    'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels')
  );

  $list->addFixedField($table,"Portefeuille",array("list_width"=>200,"search"=>true));
  
  if($gebruikPortefeuilleInformatie['CRM_eigenTemplate'] == 1)
  {
    $query = "SELECT veldenPerTab FROM `CRM_naw_templates` WHERE intake=0 order by change_date desc limit 1";
    $db->SQL($query);
    $customFields = $db->lookupRecord();
    $customFields=unserialize($customFields['veldenPerTab']);
    foreach ($customFields as $tab=>$tabdata)
    {
      if($tabdata['naam'] <> '')
     {
       $nieuweVolgorde[$tabdata['object']][]=$tabdata['naam'];
       foreach ($tabdata['velden'] as $key=>$waarden)
        $nieuweVelden[$tabdata['object']][$key]=$waarden;
     }
    }
    foreach ($nieuweVolgorde as $object=>$veldData)
      $list->categorieVolgorde[$object]=$veldData;
      
  }

  $html = $list->getCustomFields(array($table,'Naw','Portefeuilles'),$rapport."_".$USR,$nieuweVelden);    

  $joinPortefeuilles="LEFT JOIN Portefeuilles ON $table.Portefeuille = Portefeuilles.Portefeuille";
  $joinCrm="LEFT JOIN CRM_naw ON $table.Portefeuille = CRM_naw.Portefeuille";
}

  $list->editScript = $editScript;
  $list->perPage = $__appvar['rowsPerPage'];
  $list->storeTableIds=$table;
  // set sort
  $list->setOrder($_GET['sort'],$_GET['direction']);
  // set searchstring
  $list->setSearch($_GET['selectie']);
  $list->ownTables=array($table);
  $list->setJoin("$joinPortefeuilles $joinCrm $joinFondsen");


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

//$content['jsincludes'] .= $editcontent['jsincludes'];
$content["javascript"] .= "
function addRecord() {
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
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
</form>
<?
if($__debug) {
	echo getdebuginfo();
}

echo template($__appvar["templateRefreshFooter"],$content);
