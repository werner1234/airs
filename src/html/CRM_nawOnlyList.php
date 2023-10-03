<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$mainHeader = vt("relatie overzicht");

$editScript = "CRM_nawOnlyEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->storeTableIds='CRM_naw';
$list->idTable='CRM_naw';
$list->ownTables=array('CRM_naw');


if($_GET['sql'])
  $_SESSION['CRM_naw_listFilter']=$_GET['sql'];
else
  $_GET['sql']=$_SESSION['CRM_naw_listFilter'];

if($_SESSION['CRM_naw_listFilter']=='all')
{
  unset($_SESSION['CRM_naw_listFilter']);
  unset($_GET['sql']);
}

$_GET['sql']=$_SESSION['CRM_naw_listFilter'];



switch ($_GET['sql'])
{
  case "actief":
    $where="CRM_naw.aktief = 1 $filter";
    $subHeader = ", actieve relaties";
    break;
  case "inactief":
    $where="CRM_naw.aktief <> 1 $filter";
    $subHeader = ", inactieve relaties";
    $filter=getRelatieSoortenFilter(true);
    break;
  default:
    if($_GET['sql'] <> '')
      $where="aktief = 1 AND `".$_GET['sql']."`=1 ";
    else
    {
      $where = '';
      $subHeader = ", alle relaties";
      $filter = getRelatieSoortenFilter();
      $where = "aktief = 1 $filter";
    }
    break;
}

$db=new DB();
$query = "SELECT CRMeigenRecords FROM Gebruikers WHERE Gebruiker='$USR'";
$db->SQL($query);
$gebruikersData = $db->lookupRecord();
if ($gebruikersData['CRMeigenRecords'] > 0)
{
  $eigenaarFilter = " AND (CRM_naw.prospectEigenaar='$USR' OR CRM_naw.accountEigenaar='$USR') ";
}



if(checkAccess($type))
  $beperktToegankelijk = "";
else
{
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $beperktToegankelijk = " WHERE (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') ";
  }
  else
  {
    $join = "LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND
    ( VermogensbeheerdersPerGebruiker.Gebruiker='".$USR."' OR Portefeuilles.Portefeuille='')
    JOIN Gebruikers as GebruikersRechten ON GebruikersRechten.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
    JOIN Vermogensbeheerders ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
    $beperktToegankelijk = " WHERE (Portefeuilles.beperktToegankelijk = '0' OR  GebruikersRechten.beperkingOpheffen = '1' ) AND Vermogensbeheerders.CrmPortefeuilleInformatie = '1' ";
  }
  
}
$query = "SELECT count(Portefeuilles.id) as aantal
          FROM Portefeuilles
          $join
          $beperktToegankelijk ";

$db->SQL($query);
$dbData = $db->lookupRecord();

if($dbData['aantal'] > 0 || checkAccess($type) || ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0))
{
  
  $joinPortefeuilles="LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille = Portefeuilles.Portefeuille
  LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'  ";
  $portefeuillesAdded=true;
  if ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $where .= " AND ( (Portefeuilles.Portefeuille IS NOT NULL AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' AND (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR
                Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "')) OR (Portefeuilles.Portefeuille IS NULL  $eigenaarFilter ) ) ";
  }
  else
  {
    $where .= " AND ( (Portefeuilles.Portefeuille IS NOT NULL AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' AND Portefeuilles.consolidatie<2) OR ( Portefeuilles.Portefeuille IS NULL   $eigenaarFilter )  ) ";
  }
}



$list->setWhere($where);


//$list->addColumn("Naw","id",array("list_width"=>"100","search"=>false));
$list->addFixedField("Naw","naam",array("list_width"=>"","search"=>true));
$list->addFixedField("Naw","naam1",array("list_width"=>"","search"=>true));
$list->addFixedField("Naw","portefeuille",array("list_width"=>"","search"=>true));

$nieuweVelden=array();
$extraTabellen=array();
$customFields=array('naam','naam1','portefeuille','tel1','verzendAanhef','adres','pc','plaats','land','email','wachtwoord','profielOverigeBeperkingen','aktief');
$naw=new Naw();
$list->categorieVolgorde['Naw']=array('Algemeen');
foreach($customFields as $veld)
{
  if($veld=='profielOverigeBeperkingen')
    $naw->data['fields'][$veld]['categorie']='Algemeen';
  $nieuweVelden['Naw'][$veld]=$naw->data['fields'][$veld];
  $categorie=$naw->data['fields'][$veld]['categorie'];
  if(!in_array($categorie,$list->categorieVolgorde['Naw']))
    $list->categorieVolgorde['Naw'][]=$categorie;
}

$extraTabellen[]='Portefeuilles';
$extraTabellen[]='crmLaatsteFondsWaarden';
$extraTabellen[]='laatstePortefeuilleWaarde';
if(defined('DBportaal'))
  $extraTabellen[]='CRM_portaalClienten';

if(GetModuleAccess('NAW_inclDocumenten'))
{
  $extraTabellen[]='Dd_reference';
}

foreach($extraTabellen as $tabel)
  $list->categorieVolgorde[$tabel]=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');

$html = $list->getCustomFields(array_merge(array('Naw'),$extraTabellen),"crm_nawOnly",$nieuweVelden);//,$categorie

foreach ($list->columns as $colData)
{
  if (in_array($colData['objectname'], $extraTabelJoinAdded))
  {
    continue;
  }
  elseif($colData['objectname'] == 'crmLaatsteFondsWaarden' && !isset($crmLaatsteFondsWaardenAdded))
  {
    $crmLaatsteFondsWaardenAdded=true;
    $joinExtra.=" LEFT JOIN crmLaatsteFondsWaarden ON CRM_naw.portefeuille=crmLaatsteFondsWaarden.Portefeuille ";
    $extraTabelJoinAdded[]=$colData['objectname'];
  }
  elseif($colData['objectname'] == 'Portefeuilles' && !isset($portefeuillesAdded))
  {
    $portefeuillesAdded=true;
    $joinExtra.=" LEFT JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.Portefeuille ";
    $extraTabelJoinAdded[]=$colData['objectname'];
  }
  elseif($colData['objectname'] == 'Dd_reference')
  {
    $joinExtra.=" LEFT JOIN dd_reference ON CRM_naw.id = dd_reference.module_id AND dd_reference.module='CRM_naw'";
    $extraTabelJoinAdded[]=$colData['objectname'];
    $meerdereKoppelingen=true;
  }
  elseif($colData['objectname'] == 'CRM_portaalClienten' && !isset($CRM_portaalClienten))
  {
    $velden=array('id','change_user','change_date','add_user','add_date','portefeuille','name','name1','email','password','passwordChange','passwordTimes','loginTimes','loginLast','clientWW','verzendAanhef','accountmanagerNaam','accountmanagerGebruikerNaam','accountmanagerEmail','geblokkeerd','rel_id','depotbank','overRide2factor','accountmanagerTelefoon');
    $CRM_portaalClienten=true;
    $doel="CREATE TEMPORARY TABLE `CRM_portaalClienten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `change_user` varchar(10)  DEFAULT NULL,
  `change_date` datetime DEFAULT NULL,
  `add_user` varchar(10)  DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `portefeuille` varchar(20)  NOT NULL,
  `name` varchar(60)  NOT NULL,
  `name1` varchar(60)  NOT NULL,
  `email` varchar(60)  NOT NULL,
  `password` varchar(20)  NOT NULL,
  `passwordChange` datetime NOT NULL,
  `passwordTimes` int(11) NOT NULL,
  `loginTimes` int(11) NOT NULL,
  `loginLast` datetime NOT NULL,
  `clientWW` varchar(150)  NOT NULL,
  `verzendAanhef` varchar(255)  NOT NULL,
  `accountmanagerNaam` varchar(75)  NOT NULL,
  `accountmanagerGebruikerNaam` varchar(50)  NOT NULL,
  `accountmanagerEmail` varchar(50)  NOT NULL,
  `geblokkeerd` tinyint(4) NOT NULL,
  `rel_id` bigint(20) NOT NULL,
  `depotbank` varchar(10)  NOT NULL,
  `overRide2factor` tinyint(255) NOT NULL,
  `accountmanagerTelefoon` varchar(25)  NOT NULL,
  PRIMARY KEY (`id`),  KEY `portefeuille` (`portefeuille`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ";
    $DB=new DB(DBportaal);
    $query='SELECT '.implode(",",$velden)." FROM clienten";
    $DB->SQL($query);
    $DB->query();
    while($dbData=$DB->nextRecord())
      $clientData[]=$dbData;
  
    $DB=new DB();
    $DB->SQL($doel);
    $DB->query();
    foreach($clientData as $clientRecord)
    {
      foreach ($clientRecord as $key=> $item)
      {
        $clientRecord[$key]=mysql_real_escape_string($item);
      }
      $insert="INSERT INTO CRM_portaalClienten values('".implode("','",$clientRecord)."')";
      $DB->SQL($insert);
      $DB->query();
    }
    $joinExtra.=" LEFT JOIN CRM_portaalClienten  ON CRM_naw.portefeuille = CRM_portaalClienten.Portefeuille ";
    $extraTabelJoinAdded[]=$colData['objectname'];
  }
}
$joinWaarde="LEFT JOIN laatstePortefeuilleWaarde as laatstePortefeuilleWaarde ON CRM_naw.portefeuille = laatstePortefeuilleWaarde.portefeuille ";


// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

$list->setJoin("$joinPortefeuilles  $joinWaarde  $joinExtra");
//$list->queryWhere.=$where;

// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

if(isset($_SESSION['verzendStatus']))
{
  $verzendStatus=$_SESSION['verzendStatus'];
  unset($_SESSION['verzendStatus']);
}

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("Rapportage instellingen", "CRM_rapportageInstelling.php", array('target' => '_blank'));
if(GetCRMAccess(1))
{
  $_SESSION['submenu']->addItem("Rapportage selectie", "CRM_rapportageSelectie.php");
}
$_SESSION['submenu']->addItem($html,"");
$_SESSION['lastListQuery']=$list->sqlQuery;

$htmlRapportageEnabled = getVermogensbeheerderField("HTMLRapportage");

if($meerdereKoppelingen==true)
  $meerdereKoppelingenTxt="<br>\n<div  class='edit_actionTxt' style='font-size: 12px; color:red'>&nbsp;  " . vt('Let op; er kunnen meerdere regels per relatie worden getoond') . "</div>";
else
  $meerdereKoppelingenTxt='';

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
&nbsp;  <b>$mainHeader</b> $subHeader $meerdereKoppelingenTxt
</div><br>$verzendStatus";

echo template($__appvar["templateContentHeader"],$content);



?>
<?=$list->filterHeader();?>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  if ( isset ($data['CRM_naw.naam']) ) {$data['CRM_naw.naam']['noClick'] = false;}
  
  if ( $htmlRapportageEnabled )
  {
    if (isset ($data['CRM_naw.zoekveld']))
    {
      $data['CRM_naw.zoekveld']['noClick'] = false;
    }
    if (isset ($data['CRM_naw.portefeuille']))
    {
      $data['CRM_naw.portefeuille']['noClick'] = false;
    }
    
    if (isset ($data['laatstePortefeuilleWaarde.laatsteWaarde']))
    {
      $data['laatstePortefeuilleWaarde.laatsteWaarde']['url'] = 'rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=VOLK&Portefeuille=' . $data['CRM_naw.portefeuille']['value'];
    }
    
    if (isset ($data['laatstePortefeuilleWaarde.rendement']))
    {
      $data['laatstePortefeuilleWaarde.rendement']['url'] = 'rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=ATT&Portefeuille=' . $data['CRM_naw.portefeuille']['value'];
    }
    
    if (isset ($data['CRM_naw.portefeuille']) && ! empty ($data['CRM_naw.portefeuille']['value']) )
    {
      $data['CRM_naw.portefeuille']['url'] = $__appvar['baseurl'].'/HTMLrapport/dashboard.php?port=' . $data['CRM_naw.portefeuille']['value'];
    }
  }
  
  if($data['Portefeuilles.consolidatie']['value']==1){$data["tr_class"]= "list_dataregel_geel";};
  if($db->QRecords("SELECT id FROM CRM_naw WHERE PortGec=1 AND id='".$data['id']['value']."'"))
    $data["tr_class"] = "list_dataregel_geel";
  
	echo $list->buildRow($data);
}
?>
</table>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>
