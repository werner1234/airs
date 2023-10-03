<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 22 februari 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/09/14 17:07:33 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: depositorentepercentagesEdit.php,v $
    Revision 1.3  2019/09/14 17:07:33  rvv
    *** empty log message ***

    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2007/03/22 07:34:23  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Deposito rente muteren");

$__funcvar['listurl']  = "depositorentepercentagesList.php";
$__funcvar['location'] = "depositorentepercentagesEdit.php";


$object = new DepositoRentepercentages();
$object->set('Rekening',$_GET['rekening']);

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editObject->formTemplate = "depositorentepercentagesTemplate.html";
$editObject->usetemplate = true;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = false;  // geen templateheaders in $editObject->output toevoegen 

$editObject->formVars["submit"]=
'
<a href="#" onClick="editForm.submit();">
<img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('opslaan') . '</a> 
';
if(checkAccess() && $data['id']>0)
{
  $editObject->formVars["submit"] .= '
<a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();"> <img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a> ';
}
$editObject->controller($action,$data);

$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();	

if ($action != 'update' && $action != 'delete')
  echo template($__appvar["templateContentHeader"],$editcontent);

echo $editObject->getOutput();


if ($result = $editObject->result)
{
  header("Location: depositorentepercentagesList.php?rekening=".$object->get('Rekening'));
}
else 
{
	echo $_error = $editObject->_error;
}
?>