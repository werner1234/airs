<?php
/*
    AE-ICT CODEX source module versie 2.0 (simbis), 09-06-2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $

    $Log: fixordersEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2015/07/19 15:01:39  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once($__appvar["basedir"]."/classes/editObject.php");

$subHeader = "";
$mainHeader    = "FIX orders muteren";

$__funcvar['listurl']  = "fixordersList.php";
$__funcvar['location'] = "fixordersEdit.php";

$object = new FixOrders();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br /><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_GET,$_POST);
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  

$editObject->usetemplate = true;
$editObject->formTemplate = "fixordersEditTemplate.html";

if ($action == 'update')
{
  // na bewerken van FORM data na post voor DB update
}

$editObject->controller($action,$data);


if ($action == 'edit')
{
  $orderLogs = new orderLogs();
  $logData = $orderLogs->getForOrder($object->get('id'),true);
  $editObject->formVars['orderLogs'] = '';
  foreach ( $logData as $log ) 
    $editObject->formVars['orderLogs'] .= date('Y-m-d H:i:s', db2jul($log['change_date'])) . '/' . $log['add_user'] . ' - '.$log['message']. '<br />';
}

echo $editObject->getOutput();
//echo $editObject->getTemplate();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>