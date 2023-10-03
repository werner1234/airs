<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $

    $Log: CRM_nawScenarioEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2014/05/29 12:07:22  rvv
    *** empty log message ***

    Revision 1.1  2012/05/06 11:54:01  rvv
    *** empty log message ***

    Revision 1.3  2006/02/01 10:06:29  cvs
    *** empty log message ***




*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$cfg = new AE_config();

$subHeader    = "";
$mainHeader   = "Scenario instellingen,&nbsp;&nbsp;&nbsp;";

$__funcvar[listurl]  = "CRM_nawScenarioEdit.php";
$__funcvar[location] = "CRM_nawScenarioEdit.php";

$data = $_GET;
$object = new CRM_nawScenario();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div>";

$editObject->template = $editcontent;

$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = false;

//$editObject->formTemplate = "CRM_nawScenarioTemplate.html";
$editObject->controller($action,$data);

if ($object->error)
{
  echo "<h4><font color=\"maroon\">" . vt('Er zijn velden fout ingevuld in dit formulier, na correctie kunt u opnieuw opslaan') . "</font></h4>";
}

if($data['frame']==1)
{
   $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('Opslaan') . '</a>
<input type="hidden" name="frame" value="1">';
  $frame="&frame=1";
  $html=$editObject->getOutput();

  $html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
  echo $html;
  $returnUrl="CRM_nawScenarioEdit.php?frame=1&action=edit&id=".$_GET['id'];
}
else
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