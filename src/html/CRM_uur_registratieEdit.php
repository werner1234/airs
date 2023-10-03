<?php
/*
    AE-ICT CODEX source module versie 1.6, 20 oktober 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $

    $Log: CRM_uur_registratieEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2016/04/22 12:23:12  rm
    4855

    Revision 1.1  2011/10/22 06:45:09  cvs
    Urenregistratie voor TRA


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Uur registratie muteren");

$__funcvar[listurl]  = "CRM_uur_registratieList.php";
$__funcvar[location] = "CRM_uur_registratieEdit.php";


$object = new CRM_uur_registratie();
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

/**
 * het formulier valideren voordat het gesubmit wordt
 */
if ( requestType('ajax'))
{
  $AEJson = new AE_Json();
  $data = array_merge($_POST, $_GET);

  $validationJson = array(
    'fieldErrors' => array(),
    'result'      => array(),
    'error'       => array()
  );

  $editObject->data = $data;
  $editObject->setFields();
  $editObject->object->validate();

  if (is_array($object->getErrors()) || $object->error == true)
  {
    $validationJson['fieldErrors'] = array_merge($validationJson['fieldErrors'], $object->getErrors());
    $validationJson['saved'][] = false;
  }
  else
  {
    $editObject->controller('update', $data);
    $validationJson['saved'][] = true;
  }

  echo $AEJson->json_encode(
    array(
      'success'     => true,
      'saved'       => in_array(false, $validationJson['saved'])?false:true,
      'message'     => $editObject->_error,
      'error'       => $validationJson['fieldErrors'],
      'CheckResult' => (isset($validateEditObject->object->data['orderData']['orderCheckHtml'])?$validateEditObject->object->data['orderData']['orderCheckHtml']:'')
    )
  );
  exit();
}


$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->formTemplate = "CRM_uur_registratieEditTemplate.html";
$editObject->usetemplate = true;

$editObject->controller($action,$data);

if ($action == "new")
{
  $object->set("wn_code",$_SESSION["USR"]);
  $object->set("datum",date("Y-m-d"));
}
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

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