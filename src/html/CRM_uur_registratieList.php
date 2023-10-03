<?php
/*
    AE-ICT CODEX source module versie 1.6, 20 oktober 2011
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2016/04/22 12:23:12 $
    File Versie         : $Revision: 1.10 $

    $Log: CRM_uur_registratieList.php,v $
 */
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$__appvar['rowsPerPage']=1000;

$subHeader     = "";
$mainHeader    = vt("Uur registratie ");

$editScript = "CRM_uur_registratieEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$copyIds=array();
foreach ($_POST as $key=>$value)
{
  if(substr($key,0,5)=='copy_')
  {
    $copyIds[]=substr($key,5);
  }
}
$db=new DB();
foreach ($copyIds as $id)
{
  $query="SELECT * FROM CRM_uur_registratie WHERE id='$id'";
  $db->SQL($query);
  $db->Query();
  $data=$db->lookupRecord();
  $unsetVar=array('add_date','change_date','add_user','add_date','datum','datum','verwerkt','change_user','id');
  foreach ($unsetVar as $var)
    unset($data[$var]);
  $query="INSERT INTO CRM_uur_registratie SET add_date=now(),change_date=now(),add_user='$USR',change_user='$USR',datum=now(),verwerkt=0 ";
  foreach ($data as $key=>$value)
    $query.=", $key='".addslashes($value)."'";
  $db->SQL($query);
  $db->Query();
}

if(!$_POST['sort_0_veldnaam'])
{
    $_POST['sort_0_veldnaam'] = 'CRM_uur_registratie.datum';
    $_POST['sort_0_methode'] = 'DESC';
}

//$list->addColumn("CRM_uur_registratie","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","copy",array("list_width"=>"35","list_align"=>"right","search"=>false,'list_nobreak'=>true));
$list->addColumn("CRM_uur_registratie","datum",array("list_width"=>"70","list_align"=>"right","search"=>false));
$list->addColumn("CRM_uur_registratie","tijd",array("list_width"=>"60","list_align"=>"right","search"=>false,"description"=>"tijd"));
$list->addColumn("","debiteur",array("sql_alias"=>"concat(CRM_naw.debiteurnr,': ',CRM_naw.naam) ","list_width"=>"250","list_search"=>true,"search"=>true));
$list->addColumn("","actitviteit",array("sql_alias"=>"concat(CRM_uur_activiteiten.code,': ',CRM_uur_activiteiten.omschrijving) ","list_width"=>"300","list_search"=>true,"search"=>true));
$list->addColumn("CRM_uur_registratie","wn_code",array("list_width"=>"70","list_align"=>"center","search"=>true, "description"=>"WN"));
$list->addColumn("CRM_uur_registratie","verwerkt",array("list_width"=>"60","list_align"=>"center","search"=>false,"description"=>"VW"));
$list->addColumn("CRM_uur_registratie","memo",array("list_width"=>"160","list_align"=>"left","search"=>false,"description"=>"bijzonderheden"));

$list->idTable='CRM_uur_registratie';
//$list->ownTables='CRM_uur_registratie';

$list->setJoin("LEFT JOIN CRM_naw ON  CRM_uur_registratie.deb_id = CRM_naw.id
                LEFT JOIN CRM_uur_activiteiten ON  CRM_uur_registratie.act_id = CRM_uur_activiteiten.id");

$list->getCustomFields('CRM_uur_registratie');


if ($_GET["q"] == "perUser")
{
  $subHeader = " voor werknemer ".$_SESSION["USR"];
  $list->setWhere("wn_code = '".$_SESSION["USR"]."'");
}
else
{
  $query = "SELECT id, debiteurnr, naam FROM CRM_naw WHERE debiteur = 1 AND aktief =1 ORDER BY debiteurnr ";
  $dbn = new DB();
  $dbn->executeQuery($query);
  while ($debRec = $dbn->nextRecord())
  {
    $debArray[$debRec["id"]] = $debRec["debiteurnr"]." - ".$debRec["naam"];
  }  
  $uurDebSelect = '<select name="deb_id">'."\n";
  reset($debArray);
  while (list($key, $value) = each($debArray)) 
  {
    $uurDebSelect .= "  <option value='$key' >$value</option> \n";
  }
  $uurDebSelect .= "</select>  <input type='submit' value=' verzamel uren ' />";
  
}

$whereFilter="AND wn_code = '".$_SESSION["USR"]."'";

$dayOfWeek=date('w');
$beginMaand=date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
$beginVorigeMaand=date('Y-m-d',mktime(0,0,0,date('m')-1,1,date('Y')));

$queries['thisMonth']="SELECT SUM(tijd) as uren FROM CRM_uur_registratie WHERE datum >='$beginMaand'  AND date(datum)<=date(now())  $whereFilter";
$queries['lastMonth']="SELECT SUM(tijd) as uren FROM CRM_uur_registratie WHERE datum >='$beginVorigeMaand' AND date(datum)<'$beginMaand'  $whereFilter";

$db=new DB();
foreach ($queries as $var=>$query)
{
  $db->SQL($query);
  $db->Query();
  $data=$db->lookupRecord();
  $uren[$var]=$data['uren'];
}

$vorigeWeek=array();
$huidigeWeek=array();
$maandag=time()-($dayOfWeek*86400)+86400;
$vorigeMaandag=$maandag-(7*86400);
$VolgendeMaandag=$maandag+(7*86400);
for($dag=$vorigeMaandag;$dag<$VolgendeMaandag;$dag=$dag+86400)
{
  $datum=date('Y-m-d',$dag);
  $query="SELECT SUM(tijd) as uren FROM CRM_uur_registratie WHERE date(datum)='".$datum."' $whereFilter";
  $db->SQL($query);
  $db->Query();
  $data=$db->lookupRecord();
  $uren[$datum]=$data['uren'];
  if($dag < $maandag)
  {
    $vorigeWeek[]=$datum;
    $uren['lastWeek'] +=$data['uren'];
  }
  else
  {
    $huidigeWeek[]=$datum;
    $uren['thisWeek'] +=$data['uren'];
  }
}


$dagVertaling=array('maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag');

$font='style="font-family: Arial, Helvetica, sans-serif; font-size:9pt;"';
$alignRight='style="font-family: Arial, Helvetica, sans-serif; font-size:9pt; text-align:right"';
$html='
<div >
<table>
<tr><td '.$font.'>Uren overzicht</td><td></td></tr>
<tr><td '.$font.'>Deze week:</td><td '.$alignRight.'>'.$uren['thisWeek'].'</td></tr>';
foreach ($huidigeWeek as $index=>$datum)
  $html.='<tr><td '.$font.'>'.$dagVertaling[$index].'</td><td '.$alignRight.'>'.$uren[$datum].'</td></tr>';
$html.='
<tr><td '.$font.'>&nbsp;</td><td></td></tr>
<tr><td '.$font.'>Vorige week:</td><td '.$alignRight.'>'.$uren['lastWeek'].'</td></tr>';
foreach ($vorigeWeek as $index=>$datum)
  $html.='<tr><td '.$font.'>'.$dagVertaling[$index].'</td><td '.$alignRight.'>'.$uren[$datum].'</td></tr>';

$html.='
<tr><td '.$font.'>&nbsp;</td><td></td></tr>
<tr><td '.$font.'>deze maand:</td><td '.$alignRight.'>'.$uren['thisMonth'].'</td></tr>
<tr><td '.$font.'>vorige maand:</td><td '.$alignRight.'>'.$uren['lastMonth'].'</td></tr>
</table>
<div>
';

$_SESSION['submenu'] = New Submenu();
$_SESSION[submenu]->addItem($html,"");

// set default sort
$_GET['sort'][]      = "CRM_uur_registratie.datum";
$_GET['direction'][] = "DESC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);


$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$content = $editcontent;


$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";


/**
 * Snelle invoer
 */
$crmUurRegistratieObj = new CRM_uur_registratie();

$crmUurRegistratieObj->data['fields']['datum']['form_size'] = '7';
$crmUurRegistratieObj->data['fields']['datum']['form_type'] = 'calendar';
$crmUurRegistratieObj->data['fields']['datum']['form_class'] = 'AIRSdatepicker';
$crmUurRegistratieObj->data['fields']['datum']['form_extra'] = 'onchange="date_complete(this);"';
$crmUurRegistratieObj->data['fields']['deb_id']['form_type'] = 'hidden';
$crmUurRegistratieObj->data['fields']['act_id']['form_type'] = 'hidden';

$quickFormData['returnProp'] = $_GET["q"];
/** set autocomplete velden **/
//SELECT id, concat(debiteurnr,': ',naam) as naam FROM CRM_naw WHERE debiteurnr <> '' AND debiteur = 1 AND aktief = 1 ORDER BY debiteurnr

$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('deb_id_field');
$quickFormData['deb_id_field'] = $autocomplete->addVirtuelField('deb_id_field', array(
  'autocomplete' => array(
    'table' => 'CRM_naw',
//    'order' => 'Fondskoersen.Datum DESC',
    'label' => array('debiteurnr','naam'),
    'searchable' => array('debiteurnr','naam'),
    'field_value' => array('debiteurnr', 'naam'),
    'extra_fields' => array('id'),
    'value' => 'id',
    'actions' => array(
      'select' => '
      event.preventDefault();
        console.log(ui.item);
        $("#deb_id_field").val(ui.item.label);
        $("#deb_id").val(ui.item.value);
      '
    ),
    'conditions' => array('AND' => 'debiteurnr <> "" AND debiteur = 1 AND aktief = 1'),
    'order' => 'debiteurnr'
  ),
  'form_size' => '35',
));
$content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('deb_id_field');

//SELECT id, concat(code,': ',omschrijving) FROM (CRM_uur_activiteiten) ORDER BY code
$autocomplete->resetVirtualField('act_id_field');
$quickFormData['act_id_field'] = $autocomplete->addVirtuelField('act_id_field', array(
  'autocomplete' => array(
    'table' => 'CRM_uur_activiteiten',
//    'order' => 'Fondskoersen.Datum DESC',
    'label' => array('code','omschrijving'),
    'searchable' => array('code','omschrijving'),
    'field_value' => array('code', 'omschrijving'),
    'extra_fields' => array('id'),
    'value' => 'id',
    'actions' => array(
      'select' => '
      event.preventDefault();
        console.log(ui.item);
        $("#act_id_field").val(ui.item.label);
        $("#act_id").val(ui.item.value);
      '
    ),
    'order' => 'code'
  ),
  'form_size' => '30',
));
$content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('act_id_field');

$crmUurRegistratieObj->set("wn_code",$_SESSION["USR"]);
$crmUurRegistratieObj->set("datum",date("Y-m-d"));

$AETemplate = new AE_template();
echo template($__appvar["templateContentHeader"],$content);



echo $AETemplate->parseBlockFromFileWithForm('classTemplates/crmUurRegistratie/quickEditTemplate.html', $quickFormData, $crmUurRegistratieObj);
/**
 * Einde snelle invoer
 */

if ($uurDebSelect <> "")
{
?>
<fieldset>
  <legend> facuratie </legend>
  <form action="CRM_uur_verzamel.php" method="POST" >
  <?=$uurDebSelect;?>
  </form>
</fieldset>
<br>
<?
}
?>
<form name="editForm" method="POST">
<?=$list->filterHeader();?>

<form method="POST">
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  $data['.copy']['value']='<input type="checkbox" name="copy_'.$data['id']['value'].'" value="1">';
	echo $list->buildRow($data);//,$template,""
}
?>
</table>
<br><br>

  <button type="submit" value="kopieer naar vandaag"><?= vt('kopieer naar vandaag'); ?></button>
</form>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>