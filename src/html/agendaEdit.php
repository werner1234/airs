<?php
/*
    AE-ICT CODEX source module versie 1.3, 31 oktober 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.5 $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
session_start();

if($_POST)
  $data = $_POST;
else
  $data = $_GET;

$action = $data['action'];
$subHeader = "";
$mainHeader    = vt('agenda muteren');

$__funcvar['listurl']  = "agendaList.php";
$__funcvar['location'] = "agendaEdit.php";

$object = new Agenda();
$object->data['fields']['klant']['form_extra'] = " readonly onclick=\"javascript:select_naw(document.editForm.klant.value,600,400);\" ";

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

if ($data['day'])
  $object->set("plandate",jul2db($data['day']));
if($data['plantime'])
  $object->set("plantime",$data['plantime'].":00");
else
  $object->set("plantime",$_GET['hour'].":".$_GET['minute'].":00");



$editObject->includeHeaderInOutput = true;
$editObject->usetemplate = true;
$editObject->formTemplate = "agendaEditTemplate.html";
$editObject->controller($action,$data);

$startMin=(substr($object->get('plantime'),0,2)*60) + (substr($object->get('plantime'),3,2));
$stopMin=$startMin+(substr($object->get('duur'),0,2)*60) + (substr($object->get('duur'),3,2));

if($_GET['deb_id'])
{
  $object->set('rel_id',$_GET['deb_id']);
  $db=new DB();
  $query="SELECT naam FROM CRM_naw WHERE id='".$_GET['deb_id']."'";
  $db->SQL($query);
  $naam=$db->lookupRecord();
  $object->set('klant',$naam['naam']);
  
  

}



$koppelObject = array();
$koppelObject[0] = new Koppel("CRM_naw","editForm");
$koppelObject[0]->addFields("id","rel_id",false,false);
$koppelObject[0]->addFields("naam","klant",true,true);
$koppelObject[0]->addFields("plaats","",true,false);
$koppelObject[0]->name = "naw";
$koppelObject[0]->extraQuery = " AND aktief = 1 ";

$DB = new DB();
$usrs = array();
if($object->get('id') > 0)
{
  $editObject->formVars['naarCRM']='<a href="CRM_nawEdit.php?action=edit&id='.$object->get('rel_id').'">Naar CRM record.</a>';
  $DB->SQL("SELECT * FROM agenda_gebruiker WHERE agenda_id = '".$object->get('id')."'");
	$DB->Query();
  while ($urow = $DB->nextRecord())
    $usrs[] = $urow['user_id'];
}
else
{
  $object->set("gebruiker",$USR);
}

if($data['selectedFields'])
	$data['gebruikers'] = $data['selectedFields'];

if(!is_array($data['gebruikers']))
  $data['gebruikers'] = array();
$DB->SQL("SELECT Gebruiker,Naam FROM Gebruikers ORDER BY naam");
$DB->Query();
while ($urow = $DB->nextRecord())
{
  if($urow['Naam']=='')
    $urow['Naam']=$urow['Gebruiker'];
  if (($action == "new" && strtoupper($urow['Gebruiker']) == strtoupper($USR)) || in_array(($urow['Gebruiker']),$usrs) || in_array($urow['Gebruiker'],$data['gebruikers']))
    $selected .= "<option value=\"".$urow['Gebruiker']."\" $option>".$urow['Naam']."</option>\n";
  else
    $notSelected .= "<option value=\"".$urow['Gebruiker']."\" $option>".$urow['Naam']."</option>\n";
}
$editObject->formVars['gebruikerOptions'] = $options;
$editObject->formVars['sliderplantime'] = '
' . vt('Afdpraak duur') . ': <input type="text" id="amount" size=30  style="border:0;  font-weight:bold;"/>
<div id="slider-range"></div>
';
$userJava='
function moveItem(from,to){var tmp_text = new Array();var tmp_value = new Array();for(var i=0; i < from.options.length; i++){if(from.options[i].selected){var blnInList = false;
for(j=0; j < to.options.length; j++){if(to.options[j].value == from.options[i].value){blnInList = true;break;}}if(!blnInList){to.options.length++;to.options[to.options.length-1].text = from.options[i].text;
to.options[to.options.length-1].value = from.options[i].value;}}else{tmp_text.length++;tmp_value.length++;tmp_text[tmp_text.length-1] = from.options[i].text;tmp_value[tmp_text.length-1] = from.options[i].value;
}}from.options.length = 0;for(var i=0; i < tmp_text.length; i++){from.options.length++;from.options[from.options.length-1].text = tmp_text[i];from.options[from.options.length-1].value = tmp_value[i];
}from.selectedIndex = -1;}';
$editObject->formVars['notSelected']=$notSelected;
$editObject->formVars['selected']=$selected;
$editcontent[jsincludes] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>
	<script type=\"text/javascript\" src=\"javascript/jquery-min.js\"></script>
	<script type=\"text/javascript\" src=\"javascript/jquery-ui-min.js\"></script>";
$editcontent[javascript] .= "\n".$koppelObject[0]->getJavascript();
$editcontent[javascript] .= $userJava;
$editcontent[javascript] .= '
function minToTime(min)
{
  hours=Math.floor(min/60);
  minutes=min-(hours*60);
  if(hours < 10) {hours="0"+hours;}
  if(minutes < 10) {minutes="0"+minutes;}
  return(hours + ":" + minutes);
}

	$(function() {
		$("#slider-range").slider({
			range: true,
			min: 360,
			max: 1320,
			step: 15,
			values: ['.$startMin.', '.$stopMin.'],
			slide: function(event, ui)
			{
        plantime=minToTime(ui.values[0]);
        duur=minToTime(ui.values[1]-ui.values[0]);
				$("#amount").val("Van " + plantime + " tot " + minToTime(ui.values[1]) + "  ( Duur: " + duur + " )");
				$("#plantime").val(plantime);
				$("#duur").val(duur);
			}
		});
		plantime=minToTime($("#slider-range").slider("values", 0));
		duur=minToTime($("#slider-range").slider("values", 1)-$("#slider-range").slider("values", 0));
		$("#plantime").val(plantime);
		$("#duur").val(duur);
		$("#amount").val("Van " + plantime + " tot " + minToTime($("#slider-range").slider("values", 1))  +  " ( Duur: " + duur + " )");
	});
';

$preSubmit ="
	if(document.editForm['inFields[]'])
	{
		var inFields  			= document.editForm['inFields[]'];
		var selectedFields 	= document.editForm['selectedFields[]'];
		for(j=0; j < selectedFields.options.length; j++)
		{
 			selectedFields.options[j].selected = true;
		}
	}
";
$editcontent[javascript] = str_replace("//check values ?",$preSubmit,$editcontent['javascript']);

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'>  <b>$mainHeader</b> $subHeader</div><br><br>";
$editObject->template = $editcontent;

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $gebruikers = $data['selectedFields'];
  $ids = implode("','",($gebruikers));
  if (!empty($data['id']))
  {
    $qg = "DELETE FROM agenda_gebruiker WHERE agenda_id = ".$data['id']." AND user_id NOT IN('$ids')";
    $DB->SQL($qg);
    $DB->Query();
    $agenda_id = $id;
  }
  else
    $agenda_id = $object->get("id");

  for ($a=0; $a < count($gebruikers);$a++)
  {
  	if(!empty($gebruikers[$a]))
  	{
  	  $query = "SELECT id FROM agenda_gebruiker WHERE agenda_id=".$agenda_id." AND user_id='".$gebruikers[$a]."'";
  	  if($DB->QRecords($query) == 0)
  	  {
    	  $qg = "INSERT INTO agenda_gebruiker SET agenda_id=".$agenda_id.", user_id='".$gebruikers[$a]."'";
    	  $DB->SQL($qg);
    	  $DB->Query();
  	  }
  	}
  }
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>