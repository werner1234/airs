<?php
/*
    AE-ICT CODEX source module versie 1.6, 4 augustus 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/12/02 16:18:25 $
    File Versie         : $Revision: 1.5 $

    $Log: crm_naw_rtftemplatesList.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "crm_naw_rtftemplatesEdit.php";
$allow_add  = true;


if($_GET['action']=='download' && $_GET['id'] > 0)
{
  $object = new CRM_naw_RtfTemplates();
  $object->getById($_GET['id']);
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header('Content-Disposition: attachment; filename="'.$object->get('naam').'"');
  header("Content-Transfer-Encoding: binary");
  header("Content-Length: ".strlen($object->get('template')));
  echo $object->get('template');
  exit;
}

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_naw_RtfTemplates","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","dl",array('description'=>' ',"list_width"=>"30","search"=>false));
$list->addColumn("CRM_naw_RtfTemplates","standaard",array('description'=>'std',"list_width"=>"50","search"=>false));
$list->addColumn("CRM_naw_RtfTemplates","naam",array("list_width"=>"600","search"=>false));
//$list->addColumn("CRM_naw_RtfTemplates","template",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_naw_RtfTemplates","categorie",array("list_width"=>"300","search"=>false));



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
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);

  
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	$data['dl']['value']='<a href="?action=download&id='.$data['id']['value'].'"><img src="icon/16/disk_blue.png" class="simbisIcon" /></a>';
	echo $list->buildRow($data);
}


$cfg=new AE_config();
if($_GET['rtfDateFormat'])
  $cfg->addItem('rtfDateFormat',addslashes($_GET['rtfDateFormat']));
$data['rtfDateFormat']=$cfg->getData('rtfDateFormat');
if($_GET['rtfGetalFormat'])
  $cfg->addItem('rtfGetalFormat',addslashes($_GET['rtfGetalFormat']));
$data['rtfGetalFormat']=$cfg->getData('rtfGetalFormat');
?>
</table>

<form method="GET" >
  <?= vt('Datum weergave in sjablonen'); ?>:
  <select name="rtfDateFormat">
    <OPTION VALUE="%d-%m-%Y" <?if($data['rtfDateFormat']=='%d-%m-%Y')echo "SELECTED";?>>01-01-2011
    <OPTION VALUE="%d %M %Y" <?if($data['rtfDateFormat']=='%d %M %Y')echo "SELECTED";?>>01 <?= vt('januari'); ?> 2011
  </select>
  <br />
  <?= vt('Getal weergave in sjablonen'); ?>:
  <select name="rtfGetalFormat">
    <OPTION VALUE="1000" <?if($data['rtfGetalFormat']=='1000')echo "SELECTED";?>>1000
    <OPTION VALUE="1000,00" <?if($data['rtfGetalFormat']=='1000,00')echo "SELECTED";?>>1000,00
    <OPTION VALUE="1.000" <?if($data['rtfGetalFormat']=='1.000')echo "SELECTED";?>>1.000
    <OPTION VALUE="1.000,00" <?if($data['rtfGetalFormat']=='1.000,00')echo "SELECTED";?>>1.000,00
  </select>  
<br><br>
  <button type="button" name="standaard" value="Instellen opslaan" onclick="javascript:submit();"><?= vt('Instellen opslaan'); ?></button>
</form>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>