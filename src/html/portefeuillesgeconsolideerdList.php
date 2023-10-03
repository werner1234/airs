<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

if($_GET['frame']==1)
{
  unset($_SESSION['Rekeningen']);
  $__appvar['rowsPerPage']=250;
}

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "portefeuillesgeconsolideerdEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->ownTables=array('PortefeuillesGeconsolideerd');

//$list->addFixedField("PortefeuillesGeconsolideerd","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortefeuillesGeconsolideerd","VirtuelePortefeuille",array("list_width"=>"200","search"=>true));
$list->addFixedField("PortefeuillesGeconsolideerd","Portefeuille",array("list_width"=>"200","search"=>true));
//$list->addColumn("PortefeuillesGeconsolideerd","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("PortefeuillesGeconsolideerd","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("PortefeuillesGeconsolideerd","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("PortefeuillesGeconsolideerd","change_user",array("list_width"=>"100","search"=>false));

$list->categorieVolgorde=array();
$list->categorieVolgorde['PortefeuillesGeconsolideerd']=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels','Recordinfo');

$html = $list->getCustomFields(array('PortefeuillesGeconsolideerd','Portefeuilles'),'PortefeuillesGeconsolideerd');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");


if($_GET['portefeuille']<>'')
{
  $_GET['VirtuelePortefeuille']=$_GET['portefeuille'];
}

$vermogensbeheerderJoin='';
$gebruikerWhere='';

    if (checkAccess('portefeuille'))
    {
      // superusers appvar
      $allow_add = true;
    }
    else
    {
      // normale user
      $allow_add = false;
      if (checkAccess())// superusers
      {
        $allow_add = true;
      }
      
      if ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
      {
        $beperktToegankelijk = " (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "') AND Portefeuilles.consolidatie=0 ";
      }
      else
      {
        $vermogensbeheerderJoin="LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "'
							    LEFT JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
        $beperktToegankelijk = " (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Portefeuilles.consolidatie=0 ";
        $gebruikerWhere='AND (VermogensbeheerdersPerGebruiker.id IS NOT NULL OR PortefeuillesGeconsolideerd.portefeuille=\'\')';
      }
      
    }


$portefeuilleJoin="LEFT JOIN Portefeuilles ON PortefeuillesGeconsolideerd.VirtuelePortefeuille=Portefeuilles.Portefeuille";
$list->setJoin("$portefeuilleJoin $vermogensbeheerderJoin");

if($_GET['VirtuelePortefeuille']<>'')
  $uitsluitingenWhere=" AND PortefeuillesGeconsolideerd.VirtuelePortefeuille='".$_GET['VirtuelePortefeuille']."'";
else
  $uitsluitingenWhere='';

if($gebruikerWhere<>'' || $uitsluitingenWhere <> '')
  $list->setWhere('1 '.$gebruikerWhere.$uitsluitingenWhere);

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "

function addRecordFrame()
{
	location =  '".$editScript."?action=new&frame=1&portefeuille=".$_GET['portefeuille']."';
}

function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
function editRecord(url)
{
	location = url;
}
";

echo template($__appvar["templateContentHeader"],$content);
if($_GET['frame']==1 && $_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
  echo '<a href="#" onclick="addRecordFrame()"><span title="record toevoegen"><img src="icon/16/add.png" class="simbisIcon"> ' . vt('toevoegen') . '</span> </a><br><br>';

$list->customEdit =true;

?>


<form name="editForm" method="POST">
  <?=$list->filterHeader();?>
  <table class="list_tabel" cellspacing="0">
    <?=$list->printHeader();?>
    <?php
    while($data = $list->getRow())
    {
      $data['extraqs']='frame='.$_GET['frame'];
      echo $list->buildRow($data);
    }
    ?>
  </table>
</form>
<?
logAccess();
if($__debug)
{
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>