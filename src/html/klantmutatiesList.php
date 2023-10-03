<?php
/*
    AE-ICT CODEX source module versie 1.6, 29 december 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/03/11 13:22:16 $
    File Versie         : $Revision: 1.39 $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/klantMutatiesVerwerken.php");
include_once("../classes/AIRS_consolidatie.php");
session_start();

$subHeader     = "";
$mainHeader    = vt('overzicht');
$__appvar["rowsPerPage"] = 1000;

$editScript = "klantmutatiesEdit.php";
if($__appvar['master'])
  $allow_add  = true;
else
  $allow_add  = false;


$DB=new DB();


function logKlantExport($txt)
{
  global $exportStart,$exportLogLaatste;
  if($exportStart==0)
  {
    $exportStart = time();
    $exportLogLaatste = $exportStart;
  }
  $nu=time();
  logIt("Klantmutaties | ".($nu-$exportLogLaatste)."s | ".($nu-$exportStart)."s | $txt ",1);
  $exportLogLaatste =$nu;
}

function checkVermogensbeheerder($vermogensbeheerder)
{
  global $vermogensbeheerderVerwerkRechten,$DB;
  if(!isset($vermogensbeheerderVerwerkRechten[$vermogensbeheerder]))
  {
    $query="SELECT Vermogensbeheerders.CrmTerugRapportage FROM Vermogensbeheerders WHERE vermogensbeheerder='$vermogensbeheerder'";
    $DB->SQL($query);
    $data=$DB->lookupRecord();
    if($data['CrmTerugRapportage']=='2'||$data['CrmTerugRapportage']=='8')
      $vermogensbeheerderVerwerkRechten[$vermogensbeheerder]=1;
    else
      $vermogensbeheerderVerwerkRechten[$vermogensbeheerder]=0;
  }
  return $vermogensbeheerderVerwerkRechten[$vermogensbeheerder];
}

$verwerk = new klantMutatiesVerwerken();

if($__appvar['master'])
{
  if($_GET['verwerk']=='1')
  {
    logKlantExport('start verwerking');
    // $verwerk->getKeyFields();
    //$verwerk->keyFields=$keyFields;
    //$verwerk->extraChecks=$verwerk->getExtraChecks();
    //$verwerk->tableObject=$tableObject;

    foreach ($_GET as $key=>$value)
    {
      if(substr($key,0,3)=='id_')
      {
        $ids[]=substr($key,3);
      }
    }
    foreach ($ids as $id)
    {
      $verwerk->verwerk($id);
    }
    logKlantExport('ids verwerkt');
    $verwerk->createNewRecords();
    logKlantExport('createNewRecords');
    $regelInfo = $verwerk->counter." " . vt('regels verwerkt') . ". <br>\n";
    $con=new AIRS_consolidatie();
    logKlantExport('AIRS_consolidatie');
    $con->bijwerkenConsolidaties();
    logKlantExport('bijwerkenConsolidaties');
    $verwerk->createQueueUpdates();
    logKlantExport('createQueueUpdates');
    $verwerk->sendEmail();
    logKlantExport('sendEmail');
  }
  elseif($_GET['fetch']=='1')
  {
    $cfg=new AE_config();
    $lastSync=$cfg->getData('LastKlantSync');
    if($lastSync > time()-60)
    {
      $regelInfo=vt('Het is minder dan één minuut geleden dat er gegevens zijn opgehaald. Ophalen afgebroken.');
    }
    else
    {
      $cfg->addItem('LastKlantSync',time());
      include_once("../classes/AE_tableSync.php");
      $sync = new AE_tableSync('klantMutaties');
      $sync->copyRecords();
      $regelInfo = $sync->getRecordNr()." opgehaald. <br>\n";
      
    }
  }
}
else
{
  if(!isset($_SESSION['klantMutatiesCleaned']) || ($_SESSION['klantMutatiesCleaned']+3600 < time()) )
  {
    $query="UPDATE klantMutaties SET verwerkt=10 WHERE verwerkt=0 AND change_date < now() - interval 5 day";
    $DB->SQL($query);
    $DB->Query();
    $_SESSION['klantMutatiesCleaned']=time();
  }
}

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("KlantMutaties","id",array("list_width"=>"100","search"=>false));
$list->addColumn("KlantMutaties","verwerkt",array("list_width"=>"50","search"=>false));
$list->addColumn("KlantMutaties","tabel",array("list_width"=>"100","search"=>true,"list_order"=>true));
$list->addColumn("","Sleutelvelden",array("list_width"=>"200","search"=>false));
$list->addColumn("KlantMutaties","recordId",array("list_width"=>"100","search"=>false,"list_order"=>true));
$list->addColumn("KlantMutaties","veld",array("list_width"=>"100","search"=>false,"list_order"=>true));
$list->addColumn("KlantMutaties","oudeWaarde",array("list_width"=>"100","search"=>false,"list_order"=>true));
$list->addColumn("KlantMutaties","nieuweWaarde",array("list_width"=>"100","search"=>false,"list_order"=>true));
$list->addColumn("KlantMutaties","Vermogensbeheerder",array("list_width"=>"100","search"=>false,"list_order"=>true));
$list->addColumn("KlantMutaties","add_date",array("list_width"=>"150","search"=>false,"list_order"=>true));
$list->addColumn("KlantMutaties","add_user",array("list_width"=>"100","search"=>false,"list_order"=>true));
//$list->addFixedField("KlantMutaties","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("KlantMutaties","change_user",array("list_width"=>"100","search"=>false,"order"=>false));
$list->idTable='klantMutaties';
$list->ownTables=array('klantMutaties');


if($_GET['resetFilter'])
{
  for($i=0;$i<10;$i++)
   $_POST['filter_'.$i.'_verwijder'] = 1;
}
$html = $list->getCustomFields('KlantMutaties');

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
if(isset($_GET['portefeuille']))
{
  $list->setWhere(" klantMutaties.tabel='Portefeuilles'");
  $list->setJoin("JOIN Portefeuilles ON Portefeuilles.id=klantMutaties.recordId AND Portefeuilles.Portefeuille='".$_GET['portefeuille']."' AND Portefeuilles.consolidatie=0 ");
 
  if(!isset($list->sortOptions[0]))
    $list->sortOptions[0]=Array('veldnaam'=>'klantMutaties.add_date','methode'=>'DESC');
}
else
{
  if($_GET['status']=='verwerkt')
    $list->setWhere("verwerkt > 0 AND verwerkt < 10");
  elseif($_GET['status']=='verwijderd')
    $list->setWhere("verwerkt = 10");
  else
    $list->setWhere("verwerkt = 0");


}

if(empty($_GET['sort']))
  $list->setOrder(array("verwerkt",'add_date'),array('ASC','DESC'));
else
  $list->setOrder($_GET['sort'],$_GET['direction']);   

// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


if($__appvar['master'])
{
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Records ophalen."),"klantmutatiesList.php?fetch=1");
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem(vt("Verwerk selectie"),"javascript:parent.frames['content'].editForm2.submit();");
$_SESSION['submenu']->addItem($html,"");

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><b>$regelInfo</b> <br>";

if($_POST['toXls'] <> 'scherm')
{
  echo template($__appvar["templateContentHeader"],$content);
?>

<br>
<form  method="GET"  name="controleForm">
<input type="hidden" name="memoriaal" value="<?=$memoriaal?>">
<?= vt('Overzicht'); ?> :
<select name="status" onChange="document.controleForm.submit()">
<option value="" <?=($_GET['status']=="verwerkt")?"":"selected"?>><?= vt('Niet verwerkt'); ?></option>
<option value="verwerkt" <?=($_GET['status']=="verwerkt")?"selected":""?>><?= vt('Verwerkt'); ?></option>
</select>
<input type="submit" value="Overzicht">
</form>
<br>

<?=$list->filterHeader();?>
<form method="GET" name="editForm2">
<input type="hidden" name="verwerk" value="1">

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>

<div id="wrapper" style="overflow:hidden;"> 
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> <?= vt('Alles selecteren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> <?= vt('Niets selecteren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /><?= vt('Selectie omkeren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="document.editForm.toXls.value='scherm';document.editForm.submit();" >&nbsp;&nbsp;<img src='icon/16/xls.png' class='simbisIcon' /><?= vt('Scherm naar XLS'); ?></div>

</div>
<script language="JavaScript" TYPE="text/javascript">
function checkAll(optie)
{
  var theForm = document.editForm2.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,3) == 'id_')
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
</script>
<br /><br />
<?
}
$xlsdata[]=array('verwerkt','tabel','Sleutelvelden','recordId','veld','oudeWaarde','nieuweWaarde','Vermogensbeheerder','add_date','add_user');
while($data = $list->getRow())
{
 // listarray($data);
	// $list->buildRow($data,$template="",$options="");
	$data['klantMutaties.verwerkt']['list_nobreak']=true;
	$data['.Sleutelvelden']['value']=$verwerk->getKeyValues($data['klantMutaties.tabel']['value'],$data['klantMutaties.recordId']['value']);
  $verwerkt=$data['klantMutaties.verwerkt']['value'];
	if($data['klantMutaties.verwerkt']['value'] == '0')
	{

   if(checkVermogensbeheerder($data['klantMutaties.Vermogensbeheerder']['value'])==0)
   {
     $data['klantMutaties.verwerkt']['value'] = "<input type=\"checkbox\" name=\"id_" . $data['id']['value'] . "\" value=\"1\">";
     $data['tr_class']='list_dataregel_rood';
   }
   else
   {
     //$data['klantMutaties.verwerkt']['value'] = "<img src=\"images/16/save_gray.gif\" alt=\"Automatisch verwerken.\" border=\"0\">";
     $data['klantMutaties.verwerkt']['value'] = "<input type=\"checkbox\" name=\"id_" . $data['id']['value'] . "\" value=\"1\">";
     $data['tr_class']='list_dataregel_groen';
   }

	}
	elseif($data['klantMutaties.verwerkt']['value'] == '1')
  	$data['klantMutaties.verwerkt']['value']="<img src=\"images/16/check.gif\" alt=\"Record verwerkt en verzonden.\" border=\"0\">";
 	elseif($data['klantMutaties.verwerkt']['value'] == '8')
  	$data['klantMutaties.verwerkt']['value']="<img src=\"images/16/record_next.gif\" alt=\"Queue update wordt gemaakt. (Of is mislukt)\" border=\"0\">";
	elseif($data['klantMutaties.verwerkt']['value'] == '9')
  	$data['klantMutaties.verwerkt']['value']="<img src=\"images/16/check_leeg.gif\" alt=\"Record lokaal verwerkt.\" border=\"0\">";
    
  $xlsdata[]=array($verwerkt,
                   $data['klantMutaties.tabel']['value'],
                   $data['.Sleutelvelden']['value'],
                   $data['klantMutaties.recordId']['value'],
                   $data['klantMutaties.veld']['value'],
                   $data['klantMutaties.oudeWaarde']['value'],
                   $data['klantMutaties.nieuweWaarde']['value'],
                   $data['klantMutaties.Vermogensbeheerder']['value'],
                   $data['klantMutaties.add_date']['value'],
                   $data['klantMutaties.add_user']['value']);
  if($_POST['toXls'] <> 'scherm')
  	echo $list->buildRow($data);
}
//listarray($xlsdata);

if($_POST['toXls'] == 'scherm')
{
  $xls = new AE_xls();
  $xls->setData($xlsdata);
  $xls->OutputXls('klantmutatues.xls',false);
}
else
{
  echo '</table>
</form>
';
}

}
else
{
if($_POST['toXls'] <> 'scherm')
{
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<?
echo $list->filterHeader();
if($_GET['portefeuille'] == '')
{
?>
<form  method="GET"  name="controleForm">
<input type="hidden" name="memoriaal" value="<?=$memoriaal?>">
<?= vt('Overzicht'); ?> :
<select name="status" onChange="document.controleForm.submit()">
<option value="" <?=($_GET['status']=="verwerkt")?"":"selected"?>><?= vt('Niet verwerkt'); ?></option>
<option value="verwerkt" <?=($_GET['status']=="verwerkt")?"selected":""?>><?= vt('Verwerkt'); ?></option>
<option value="verwijderd" <?=($_GET['status']=="verwijderd")?"selected":""?>><?= vt('Verwijderd'); ?></option>
</select>
<input type="submit" value="Overzicht">
</form>

<div id="wrapper" style="overflow:hidden;"> 
<div class="buttonDiv" style="width:150px;float:left;" onclick="document.editForm.toXls.value='scherm';document.editForm.submit();" >&nbsp;&nbsp;<img src='icon/16/xls.png' class='simbisIcon' />Scherm naar XLS</div>
</div>
<br>
<?
}
?>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader(true);?>
<?php
}
$xlsdata[]=array('verwerkt','tabel','Sleutelvelden','recordId','veld','oudeWaarde','nieuweWaarde','Vermogensbeheerder','add_date','add_user');
while($data = $list->getRow())
{
  $data['disableEdit']=true;
  $verwerkt=$data['klantMutaties.verwerkt']['value'];
  $data['klantMutaties.verwerkt']['list_nobreak']=true;
  $data['.Sleutelvelden']['value']=$verwerk->getKeyValues($data['klantMutaties.tabel']['value'],$data['klantMutaties.recordId']['value']);
	if($data['klantMutaties.verwerkt']['value'] == '0')
	{
	  $data['tr_class']='list_dataregel_rood';
	  $data['klantMutaties.verwerkt']['value']="<img src=\"images/16/check_leeg.gif\" alt=\"Nog niet verwerkt.\" border=\"0\">";
	}
	elseif($data['klantMutaties.verwerkt']['value'] == '8')
  	$data['klantMutaties.verwerkt']['value']="<img src=\"images/16/check.gif\" alt=\"Verwerkt.\" border=\"0\">";
 	elseif($data['klantMutaties.verwerkt']['value'] == '10')
  	$data['klantMutaties.verwerkt']['value']="<img src=\"images/16/delete.gif\" alt=\"Verwijderd.\" border=\"0\">";
  
  $xlsdata[]=array($verwerkt,
                   $data['klantMutaties.tabel']['value'],
                   $data['.Sleutelvelden']['value'],
                   $data['klantMutaties.recordId']['value'],
                   $data['klantMutaties.veld']['value'],
                   $data['klantMutaties.oudeWaarde']['value'],
                   $data['klantMutaties.nieuweWaarde']['value'],
                   $data['klantMutaties.Vermogensbeheerder']['value'],
                   $data['klantMutaties.add_date']['value'],
                   $data['klantMutaties.add_user']['value']);
  if($_POST['toXls'] <> 'scherm')
  	echo $list->buildRow($data);
}

if($_POST['toXls'] == 'scherm')
{
  $xls = new AE_xls();
  $xls->setData($xlsdata);
  $xls->OutputXls('klantmutatues.xls',false);
}
else
  echo "</table>";
}
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>