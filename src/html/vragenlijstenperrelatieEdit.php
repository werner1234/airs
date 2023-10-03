<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 22 november 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/01 13:02:18 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: vragenlijstenperrelatieEdit.php,v $
    Revision 1.2  2018/02/01 13:02:18  cvs
    update naar airsV2


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Vragenlijsten per relatie muteren");

$__funcvar['listurl']  = "vragenlijstenperrelatieList.php";
$__funcvar['location'] = "vragenlijstenperrelatieEdit.php";

$object = new VragenLijstenPerRelatie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar  = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_POST, $_GET);
$action = $data['action'];
//debug($data);
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "vragenlijstenperrelatieEditTemplate.html";
global $USR;
if ($action == "update")
{
  $trackAndTraceFields = array(
    'nawId',
    'vragenLijstId',
    'datum',
    'zichtbaarInPortaal',
    'portaalStatus',
    'portaalDatumIngevuld',
    'omschrijving',
    'memo',
  );

  $db = new DB();
  $query = "SELECT * FROM `VragenLijstenPerRelatie` WHERE id = ".(int)$_REQUEST["id"];
  $oldRec = $db->lookupRecordByQuery($query);

  foreach($trackAndTraceFields as $testField)
  {
    if ($oldRec[$testField] != $_REQUEST[$testField])
    {
      addTrackAndTrace("VragenLijstenPerRelatie", (int)$_REQUEST["id"], $testField, $oldRec[$testField], $_REQUEST[$testField], $USR);
    }
  }

}

$editObject->controller($action,$data);

if ($action == "new")
{
  $object->set("datum", date("Y-m-d"));
  $object->set("nawId", $data["rel_id"]);
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
