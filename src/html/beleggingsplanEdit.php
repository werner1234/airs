<?php
/*
    AE-ICT CODEX source module versie 1.6, 17 december 2008
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/07/03 14:22:26 $
    File Versie         : $Revision: 1.3 $

    $Log: beleggingsplanEdit.php,v $
    Revision 1.3  2020/07/03 14:22:26  rm
    8696

    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2008/12/17 13:34:56  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
session_start();
$subHeader     = vt("beleggingsplan");
$mainHeader    = vt("muteren");

$__funcvar["listurl"]  = "beleggingsplanList.php";
$__funcvar["location"] = "beleggingsplanEdit.php";

$object = new Beleggingsplan();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b> ".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('Beleggingsplan', 'Portefeuille', 'Portefeuille');

$editObject->formTemplate = "beleggingsplanEditTemplate.html";
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

$DB = new DB();

if($data['frame']==1)
{
  if($_GET['Portefeuille'])
  {
    $object->set('Portefeuille', $_GET['Portefeuille']);
    $q="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$_GET['Portefeuille']."'";
    $DB->SQL($q);
    $vermogensbeheerder=$DB->lookupRecord();
    $object->set('Vermogensbeheerder', $vermogensbeheerder['Vermogensbeheerder']);
  }
  $object->setOption('Portefeuille', 'form_type', 'text');
  $object->setOption('Portefeuille', 'form_extra', 'READONLY');
  $object->setOption('Vermogensbeheerder', 'form_type', 'text');
  $object->setOption('Vermogensbeheerder', 'form_extra', 'READONLY');

  if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 )
  {
    $editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <input type="hidden" name="frame" value="1">';
  }
  else
    $editObject->formVars["verzendKnop"] ='Geen rechten om te verzenden.';
  echo $editObject->getOutput();

}
else {
  echo $editObject->getOutput();
}

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
