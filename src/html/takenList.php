<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 18 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/10/11 17:33:58 $
    File Versie         : $Revision: 1.24 $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$addExtra      = "";                             // extra parameters voor insertrecord
$subHeader     = "";
$mainHeader    = vt("Taaklijst overzicht");
$__appvar['rowsPerPage']=50;

$editScript = "takenEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

if(isset($_POST))
{
  $ids=array();
  foreach($_POST as $key=>$value)
  { 
    if(substr($key,0,6)=='check_')
    { 
      $ids[]=substr($key,6);
    }
  }
  if($_POST['actie']=='klaar' && count($ids) > 0)
  {
    $query="UPDATE taken SET afgewerkt='1',change_date=now(),change_user='".$USR."' WHERE id IN('".implode("','",$ids)."')";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
  }
  if($_POST['actie']=='verwijderen' && count($ids) > 0)
  {
    $query="DELETE FROM taken WHERE id IN('".implode("','",$ids)."')";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
  }
}


//$list->addColumn("","check",array("description"=>' ',"list_width"=>"20","search"=>false));
$list->addColumn("","check",array("description"=>' ',"list_width"=>"40","search"=>false,'list_nobreak'=>true));
$list->addFixedField("Taken","add_date",array("list_width"=>"100","search"=>false));
$list->addFixedField("Taken","relatie",array("list_width"=>"150","search"=>true));
$list->addFixedField("Taken","rel_id",array("list_width"=>"","search"=>true,"list_invisible"=>true));
$list->addFixedField("Taken","kop",array("list_width"=>"","search"=>true));
$list->addFixedField("Taken","soort",array("list_width"=>"100","search"=>true,"list_align"=>"center"));
$list->addFixedField("Taken","spoed",array("list_width"=>"50","search"=>false,"list_align"=>"center"));
$list->addFixedField("Taken","zichtbaar",array("description"=>"zichtbaar na","list_width"=>"120","search"=>false,"list_align"=>"center"));
$list->addFixedField("Taken","gebruiker",array("list_width"=>"60","search"=>true,"list_align"=>"center"));


  
if(!isset($list->sortOptions[0]))
  $list->sortOptions[0]=Array('veldnaam'=>'taken.zichtbaar','methode'=>'ASC');

$list->categorieVolgorde=array('Taken'=>array('Algemeen'),
                               'Naw'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo'));

$db= new DB();
$query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();
if($gebruikPortefeuilleInformatie['CRM_eigenTemplate'] == 1)
{
  $query = "SELECT veldenPerTab FROM `CRM_naw_templates` WHERE intake=0 order by change_date desc limit 1";
  $db->SQL($query);
  $customFields = $db->lookupRecord();
  $customFields=unserialize($customFields['veldenPerTab']);
  $naw=new Naw();
  $nieuweVolgorde=array();
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

$list->ownTables=array('taken');
$list->idTable="taken";
$list->setJoin("JOIN CRM_naw ON taken.rel_id = CRM_naw.id");
  
$html = $list->getCustomFields(array('Taken','Naw'),'Takenlist');
//$_SESSION['submenu'] = New Submenu();

if(!isset($_GET['filter']))
  $filter='openstaandEnToekomstig';
else
  $filter=$_GET['filter'];
  
$whereArray = array();
switch ($_GET['do'])
{
	case "alles":
		$subHeader = ", " . vt('alle taken') . "";
		$list->addColumn("Taken","afgewerkt",array("description"=>"klaar","list_width"=>"80","search"=>false,"list_align"=>"center"));

		break;
  default:

    if($filter=='openstaandEnToekomstig')
    {
      $subHeader = ", " . vt('openstaande en toekomstige taken') . "";
      $whereArray[] = "afgewerkt = 0";
    }
    elseif($filter=='openstaand')
    {
      $whereArray[] = "afgewerkt = 0 AND zichtbaar < NOW()";
      $subHeader = ", " . vt('openstaande taken') . "";
    }
    elseif($filter=='afgehandeld')
    {
      $whereArray[] = "afgewerkt = 1 AND zichtbaar < NOW()";
      $subHeader = ", " . vt('afgehandelde taken') . "";
    }
    elseif($filter=='toekomstig')
		{  
		  $whereArray[] = "zichtbaar > NOW()";
      $subHeader = ", " . vt('toekomstige taken') . "";
		}
		break;
}




$addExtra = "";
$deb_id = $_GET['deb_id'];
if($deb_id<1 && isset($_GET['rel_id']))
  $deb_id=$_GET['rel_id'];
  
if ($deb_id > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader .= " bij <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";

  $whereArray[] = " rel_id = ".$deb_id;
  $addExtra = "&rel_id=".$_GET['deb_id'];

 // $_SESSION[submenu] = New Submenu();
 // $_SESSION[submenu]->addItem("Terug naar NAW ","nawEdit.php?action=edit&id=$deb_id&useSavedUrl=1");

}

if (count($whereArray) > 0)
{
  $operator = "";
  for ($wCount=0; $wCount < count($whereArray); $wCount++)
  {
    $whereString .= $operator." ".$whereArray[$wCount]." ";
    $operator = " AND ";
  }
  $list->setWhere($whereString);

}

// set default sort
$_GET['sort'][]      = "taken.spoed";
$_GET['direction'][] = "DESC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);



// select page
$list->selectPage($_GET['page']);

if($_GET['action'] == 'xls')
{
  $list->setXLS();
  $list->getXLS();
}
else
{
  if(!is_array($_SESSION['submenu'],'Submenu'))
    $_SESSION['submenu']=new Submenu();
  $_SESSION['submenu']->addItem("XLS-lijst","$PHP_SELF?action=xls&".$_SERVER['QUERY_STRING']);
  $_SESSION['submenu']->addItem($html,"");
  
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new".$addExtra."';
}

function checkAll(optie)
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;  
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
}
";

echo template($__appvar["templateContentHeader"],$content);


echo str_replace('</form>','',$list->filterHeader());
  echo "<input type='hidden' value='' name='actie' id='actie' >\n";
echo '<table class="list_tabel" cellspacing="0" >';

$DB = new DB();
$DB->SQL("SELECT gebruiker,bgkleur FROM Gebruikers");
$DB->Query();
while ($usrData = $DB->nextRecord())
{
  $usrColors[$usrData['gebruiker']] = $usrData['bgkleur'];
}


if($_GET['deb_id'] > 0)
  $extraId="&deb_id=".$_GET['deb_id'];
$filters=array(vt('openstaand'),vt('afgehandeld'),vt('toekomstig'),vt('alle'));
foreach($filters as $filter)
{
  echo "<a style=\"display: inline-block;background-color: #FFDEAD;border: 1px solid;text-align:center;width:120px;\" href=\"?filter=$filter".$extraId."\"> " . $filter . " </a>";
}
echo "<br><br>";

echo $list->printHeader();
while($data = $list->getRow())
{
  if ($data['spoed']['value'] <> 0)
  {
    $data['tr_class'] = "list_dataregel_rood";
  }
  $data['gebruiker']['td_style'] = " style=\"background-color:#".$usrColors[$data['gebruiker']['value']].";\" ";
  //listarray($data);

if($_GET['do']=="alles")
  $afgewerkt='<td class="listTableData" width="50"  align="center" >{spoed_value}&nbsp;</td>';
else
	$afgewerkt='';

$crmEditLink='';
if(!isset($_GET['deb_id']) || $_GET['deb_id'] < 1)
{
  $crmEditWidth=20;
  $crmEditLink='<a href="CRM_nawEdit.php?action=edit&id='.$data['taken.rel_id']['value'].'&taakId='.$data['id']['value'].'&frame=1"><img src="images/relaties.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a>';

//frameSet.php?page=".base64_encode("takenList.php?deb_id=$deb_id")
}
else
  $crmEditLink='<a href="#">&nbsp;</a>';

$crmEditLink.="<input type=\"checkbox\" name=\"check_".$data['id']['value']."\" value=\"1\"> ";

	$maxChar = $data['taken.relatie']['list_width'] /	$list->pixelsPerChar;

$data['.check']['value']=$crmEditLink;
  foreach($data as $col=>$colData)
  {
    if($colData['db_type']=='varchar' && strlen($data[$col]['value']) > $maxChar)
	    $data[$col]['value'] = substr($data[$col]['value'],0,$maxChar)."...";
	}
	echo $list->buildRow($data,$template);

	//echo $list->buildRow($data);
}
?>
</table>
</form>


<div id="wrapper" style="overflow:hidden;"> 
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> <?= vt('Alles selecteren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> <?= vt('Niets selecteren'); ?></div>
<div class="buttonDiv" style="width:160px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> <?= vt('Selectie omkeren'); ?></div>
<div class="buttonDiv" style="width:200px;float:left;" onclick="document.editForm.actie.value='klaar';document.editForm.submit();document.editForm.actie.value='';">&nbsp;&nbsp;<img src='icon/16/disk_blue.png' class='simbisIcon' /> <?= vt('Selectie klaarmelden'); ?></div>
<div class="buttonDiv" style="width:200px;float:left;" onclick="document.editForm.actie.value='verwijderen';document.editForm.submit();document.editForm.actie.value='';">&nbsp;&nbsp;<img src='icon/16/delete.png' class='simbisIcon' /> <?= vt('Selectie verwijderen'); ?></div>
</div>
<!--
-->
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);
}
?>