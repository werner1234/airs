<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 november 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: standaarddeviatieperportefeuilleEdit.php,v $
    Revision 1.6  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = vt("standaard deviatie per portefeuille");
$mainHeader   = vt("muteren");

$__funcvar['listurl']  = "standaarddeviatieperportefeuilleList.php";
$__funcvar['location'] = "standaarddeviatieperportefeuilleEdit.php";

$object = new StandaarddeviatiePerPortefeuille();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate="standaarddeviatieperportefeuilleEditTemplate.html";

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if($data['frame']==1)
{
	if($_GET['Portefeuille'])
	{
		$DB=new DB();
		$object->set('Portefeuille', $_GET['Portefeuille']);
		$q="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$_GET['Portefeuille']."'";
		$DB->SQL($q);
		$vermogensbeheerder=$DB->lookupRecord();
		$object->set('Vermogensbeheerder', $vermogensbeheerder['Vermogensbeheerder']);
	}
	$object->setOption('Vermogensbeheerder', 'form_type', 'text');
	$object->setOption('Vermogensbeheerder', 'form_extra', 'READONLY');
	$object->setOption('Portefeuille', 'form_type', 'text');
	$object->setOption('Portefeuille', 'form_extra', 'READONLY');
//listarray($_SESSION['usersession']['gebruiker']);
	if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
	{
		$editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>
    <input type="hidden" name="frame" value="1">';
	}
	else
  {
    $editObject->formVars["verzendKnop"] = vt('Geen rechten om te verzenden.');
  }
	echo $editObject->getOutput();
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
