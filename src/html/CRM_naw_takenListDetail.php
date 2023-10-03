<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 18 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/10/10 04:20:38 $
    File Versie         : $Revision: 1.4 $

    $Log: CRM_naw_takenListDetail.php,v $
    Revision 1.4  2019/10/10 04:20:38  rvv
    *** empty log message ***

    Revision 1.3  2017/07/22 18:20:50  rvv
    *** empty log message ***

    Revision 1.2  2014/02/22 18:42:25  rvv
    *** empty log message ***

    Revision 1.1  2013/08/10 15:47:17  rvv
    *** empty log message ***

    Revision 1.7  2012/04/28 15:55:51  rvv
    *** empty log message ***



*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$addExtra      = "";                             // extra parameters voor insertrecord
$subHeader     = "";
$mainHeader    = vt("Taaklijst overzicht");

$editScript = "takenEdit.php";
$allow_add  = true;
$__appvar['rowsPerPage']=50;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$db= new DB();
$query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();

$query="SELECT CRMeigenRecords FROM Gebruikers WHERE Gebruiker='$USR'";
$db->SQL($query);
$gebruikersData = $db->lookupRecord();
if($gebruikersData['CRMeigenRecords']>0)
 $extraWhere=" AND CRM_naw.prospectEigenaar='$USR' ";
 

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
	case "debiteur":
		$where="debiteur = 1 AND aktief=1";
		$subHeader = ", " . vt('Clienten') . "";
		break;
  case "crediteur":
		$where="crediteur = 1 AND aktief=1";
		$subHeader = ", Leveranciers";
		break;
  case "prospect":
		$where="prospect = 1 AND aktief=1";
		$subHeader = ", " . vt('Prospects') . "";
		break;
  case "overige":
		$where="overige = 1 AND aktief=1";
		$subHeader = ", " . vt('Overige') . "";
		break;
	case "inaktief":
		$where="aktief <> 1";
		$subHeader = ", " . vt('inaktieve relaties') . "";
		break;
	default:
    if($_GET['sql'] <> '')
      $where="aktief = 1 AND `".$_GET['sql']."`=1 ";
    else
    {
      $query="SELECT CRM_relatieSoorten FROM Gebruikers WHERE Gebruiker='$USR'";
      $db->SQL($query);
      $CRM_relatieSoorten=$db->lookupRecord();
      $CRM_relatieSoorten=unserialize($CRM_relatieSoorten['CRM_relatieSoorten']);
      $filter='';
      if(is_array($CRM_relatieSoorten))
      {
        $query="DESC CRM_naw";
        $db->SQL($query);
        $db->Query();
        $crmVelden=array();
        while($data=$db->nextRecord('num'))
         $crmVelden[]=$data[0];
        
        $allArray=array();
        foreach($CRM_relatieSoorten as $key=>$value)
        {
          if($value<>'all' && $value<>'inaktief' && $value<>'aktief' && in_array($value,$crmVelden))
            $allArray[]=$value;
        }
        $filter="AND ((".implode('=1 OR ',$allArray)."=1) or ( ".implode('=0 AND ',$allArray)."=0)      )";
       
      }
	    $where="aktief = 1 $filter";
    }
		$subHeader = ", " . vt('alle aktieve relaties') . "";
		break;
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
    $join = "LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND ( VermogensbeheerdersPerGebruiker.Gebruiker='".$USR."' OR Portefeuilles.Portefeuille='')
    JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker
    JOIN Vermogensbeheerders ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
    $beperktToegankelijk = " WHERE (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Vermogensbeheerders.CrmPortefeuilleInformatie = '1' ";
	}

}
$query = "SELECT count(Portefeuilles.id) as aantal
          FROM Portefeuilles
          $join
          $beperktToegankelijk ";

$db->SQL($query);
$dbData = $db->lookupRecord();
//$list->categorieVolgorde['laatstePortefeuilleWaarde']=array('Algemeen');
//$list->addFixedField("laatstePortefeuilleWaarde","laatsteWaarde",array("list_width"=>"150","search"=>true));
$list->categorieVolgorde=array('Naw'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo'),
                               'CRM_evenementen'=>array('Algemeen'),
                               'Taken'=>array('Algemeen') );
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');



if($gebruikPortefeuilleInformatie['CRM_eigenTemplate'] == 1)
{
  $query = "SELECT veldenPerTab FROM `CRM_naw_templates` WHERE intake=0 order by change_date desc limit 1";
  $db->SQL($query);
  $customFields = $db->lookupRecord();
  $customFields=unserialize($customFields['veldenPerTab']);
  $naw=new Naw();
  foreach ($customFields as $tab=>$tabdata)
  {
    if($tabdata['naam'] <> '')
    {
      $nieuweVolgorde[$tabdata['object']][]=$tabdata['naam'];
      foreach ($tabdata['velden'] as $key=>$waarden)
        $nieuweVelden[$tabdata['object']][$key]=$waarden;
    }
  }
  if(isset($nieuweVelden['Naw']))
    $nieuweVelden['Naw']['PortGec']=$naw->data['fields']['PortGec'];
  
  $nieuweVelden['Naw']['clientGesproken']=$naw->data['fields']['clientGesproken'];
  foreach ($nieuweVolgorde as $object=>$veldData)
    $list->categorieVolgorde[$object]=$veldData;
}



$list->storeTableIds='taken';
$list->idTable='taken';
$list->noGroup=false;

$list->addFixedField("Taken","add_date",array("list_width"=>"100","search"=>false,"list_align"=>"left","form_type"=>"calendar"));
$list->addFixedField("Taken","kop",array("list_width"=>"","search"=>true));
$list->addFixedField("Naw","zoekveld",array("list_width"=>"150","search"=>true));
$list->addFixedField("Naw","portefeuille",array("list_width"=>"150","search"=>true));
$html = $list->getCustomFields(array('Naw','Portefeuilles','Taken'),"taken");

$list->ownTables=array('CRM_naw');
$list->setJoin("LEFT JOIN taken ON CRM_naw.id=taken.rel_id
LEFT JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0"); // $joinEvenementen $joinDossier $joinWaarde
$list->queryWhere.=$where;
$list->skipCloseForm=true;
$list->extraButtons="";
  
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$list->setWhere($where.$extraWhere);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new&rel_id=$deb_id';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>

<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">

<?
echo $list->printHeader();


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

while($data = $list->getRow())
{
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
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);
?>