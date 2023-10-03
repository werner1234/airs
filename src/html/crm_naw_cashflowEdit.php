<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 november 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: crm_naw_cashflowEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2014/05/29 12:07:22  rvv
    *** empty log message ***

    Revision 1.1  2013/11/17 13:16:20  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "crm_naw_cashflowList.php";
$__funcvar['location'] = "crm_naw_cashflowEdit.php";

$object = new CRM_naw_cashflow();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

if ($action == "new")
{
  $object->setOption("rel_id","value",$_GET['rel_id']);
}

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
  if($data['frame']==1)
  {
   $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('Opslaan') . '</a>
  <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;' . vt('verwijder') . '</a>
<a href="#" onClick="history.back(1);"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;' . vt('terug') . '</a>
<input type="hidden" name="frame" value="1">';
  $frame="&frame=1";
  $html=$editObject->getOutput();

  $html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
  echo $html;
  $returnUrl="crm_naw_cashflowList.php?frame=1&rel_id=".$_GET['rel_id'];
  }
else
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