<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");
session_start();
$__funcvar['listurl']  = "CRM_selectieveldenList.php";
$__funcvar['location'] = "CRM_selectieveldenEdit.php";


$subHeader    = "";
$object = new CRM_selectievelden();

if ($_GET['action'] == "new" || $_GET['action']=='update')
  $module = $_GET["module"];
else
{
  $object->getById($_GET['id']);
  $module = $object->get("module");
}
$module=trim($module);

$mainHeader   = vt("Selectieveld muteren bij ").$module;

$db=new DB();
$query="SELECT Max(Vermogensbeheerders.check_module_VRAGEN) AS module_VRAGEN FROM Vermogensbeheerders";
$db->SQL($query);
$vragen=$db->lookupRecord();


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editObject->template = $editcontent;
$data = $_GET;
$action = $data['action'];
$editObject->usetemplate = true;

// extra  db  actions
if($module == "docCategrien" and in_array($_REQUEST['action'], array('update', 'updateStay')))
{
  if($_REQUEST["waarde"] and !empty($_REQUEST["omschrijving"]))
  {
    $query = " UPDATE CRM_selectievelden SET waarde = 0 WHERE module = 'docCategrien' 
               AND NOT omschrijving = '".mysql_escape_string($_REQUEST["omschrijving"])."' ";
    $object->setOption("waarde","waarde", 1);
    $db->executeQuery($query);
  }

}

if($module == "risicoprofiel" || $_GET['punten'] <> '')
{
  $extraData=unserialize();
  $extraData=serialize(array('min'=>$_GET['min'],'max'=>$_GET['max'],'punten'=>$_GET['punten']));
 // $object->set('extra',$extraData);
  $data['extra']=$extraData;
}

$editObject->formTemplate='
<form name="editForm" action="{updateScript}">
<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="updateScript" value="{updateScript}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
{id_inputfield}{module_inputfield}

<div class="formblock">
<div class="formlinks"><label for="waarde_waarde">{waarde_description}</label> </div>
<div class="formrechts">
{waarde_inputfield} {waarde_error}
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="omschrijving">{omschrijving_description}</label> </div>
<div class="formrechts">
{omschrijving_inputfield} {omschrijving_error}
</div>
</div>

{extra}

<div class="formblock">
<div class="formlinks">&nbsp;</div>
<div class="formrechts">
{change_user_value} {change_date_value}</div>
</div>

</form></div>';


if($module=='burgelijke staat')
  $object->setOption("omschrijving","db_size",120);
elseif($module=='rechtsvorm')
  $object->setOption("waarde","db_size",120);
elseif($module=='opleidingsniveau')
  $object->setOption("omschrijving","db_size",120);
elseif($module=='clientenclassificatie')
  $object->setOption("omschrijving","db_size",120);
elseif($module=='prospect status')
  $object->setOption("omschrijving","db_size",120);
elseif($module=='evenementen')
  $object->setOption("omschrijving","db_size",50);
elseif($module=='agenda afspraak')
  $object->setOption("waarde","db_size",20);
elseif($module=='banken')
  $object->setOption("omschrijving","db_size",50);
elseif($module=='docCategrien')
  $object->setOption("omschrijving","db_size",50);
elseif($module=='gesprekstypen')
  $object->setOption("omschrijving","db_size",50);
elseif($module=='beleggingsdoelstelling')
  $object->setOption("omschrijving","db_size",120);  
else
  $object->setOption("omschrijving","db_size",200);

//$editObject->usetemplate = true;
$editObject->controller($action,$data);
$toonWaarde = array('rechtsvorm','agenda afspraak','gesprekstypen','docCategrien');

if ($action == "new")
{
  if ($_GET['module'])
  {
    $object->setOption("module","value",$_GET['module']);
  }
  else
  {
    $object->setOption("module","form_visible",true);
    $object->setOption("module","form_type","select");
    $object->setOption("module","form_select_option_notempty",true);
    $object->setOption("module","form_options",$__CRMvars["selectieTypen"]);
  }
}


   

$editObject->formVars['extra']='';
$extraData=unserialize($object->get('extra'));
if($module == "risicoprofiel")
{
   $editObject->formVars['extra'].='<div class="formblock">
<div class="formlinks"><label for="minmax">' . vt('min/max score') . '</label> </div>
<div class="formrechts">
<input type="text" size="3" value="'.$extraData['min'].'" name="min" >
<input type="text" size="3" value="'.$extraData['max'].'" name="max" >
</div>
</div>';
}

if($vragen['module_VRAGEN']==1)
{
$editObject->formVars['extra'].='<div class="formblock">
<div class="formlinks"><label for="punten">' . vt('Vragenlijst punten') . '</label> </div>
<div class="formrechts">
<input type="text" size="3" value="'.$extraData['punten'].'" name="punten" >
</div>
</div>';
}
else
{
  $editObject->formVars['extra'].='<input type="hidden" value="'.$extraData['punten'].'" name="punten" >';
}

if($module == "relatiegeschenken")
{
  $object->setOption("waarde","form_type","select");
  $object->setOption("waarde","form_select_option_notempty",true);
  for($i=1;$i<16;$i++)
   $relatieOpties[]="relatie".$i;
  $object->setOption("waarde","form_options",$relatieOpties);
 // $object->setOption("omschrijving","key_field",true);
}
elseif (!in_array($module,$toonWaarde))
{
  $object->setOption("waarde","form_visible",false);
  
}

if($module=='gesprekstypen')
{
  $object->setOption("waarde","form_type","checkbox");
  $object->setOption("waarde","description","meenemen als contact");
}
//else
//  $object->setOption("waarde","key_field",true);

if($module=='docCategrien')
{
  $standaardVeld = null;
  $isStamdaard   = false;
  $query         = "SELECT * FROM CRM_selectievelden  WHERE module = 'docCategrien' AND waarde = 1";
  $db->SQL($query);

  if($record=$db->lookupRecord())
  {
    $isStandaard   = $record["waarde"];
    $standaardVeld = $record["omschrijving"];
  }

  $object->setOption("waarde","form_type","checkbox");
  $object->setOption("waarde","description", vt("instellen als standaard"));
  $object->setOption("waarde","error", trim(($isStandaard ? vt("als standaard ingesteld") : vt("niet als standaard ingesteld")) . ". " . ( $standaardVeld != null ? vt("Huidige veld:") . " " . $standaardVeld : "")));
}

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>