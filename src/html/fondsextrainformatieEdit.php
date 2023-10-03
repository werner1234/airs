<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 20 december 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/07/13 17:47:04 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: fondsextrainformatieEdit.php,v $
    Revision 1.6  2019/07/13 17:47:04  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader     = "";
$mainHeader    = "";

$__funcvar["listurl"]  = "fondsextrainformatieList.php";
$__funcvar["location"] = "fondsextrainformatieEdit.php";


if($_GET['frame']==1)
{
  $navBackup=$_SESSION['NAV'];
}
$object = new FondsExtraInformatie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formMethod='FILE';

if( requestType('ajax') ) {
	$object->formId = 'fondsextrainformatieForm';
	$object->formName = 'fondsextrainformatie';
}

$AETemplate = new AE_template();
$editcontent['jsincludes'] .= $AETemplate->loadJs('jsrsClient');
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_GET,$_POST);
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);


/** als request type = ajax return json voor jquery bij update of verwijderen **/
if( requestType('ajax') && ($action == 'update' || $action == 'delete') ) {
	if ($editObject->object->error == false) {
		echo json_encode(array(
											 'success' => true,
											 'saved'   => true,
											 'fonds' => $data['fonds'],
										 )); //let ajax know the request ended in success
		exit();
	} else {
		$currentErrors = $object->getErrors();
		$jsonReplaces = array(array("\\\\", "/", "\n", "\t", "\r", "\b", "\f"), array('\\', '/', '', '', '', '', ''));
		foreach ( $currentErrors as $currentErrorKey => $currentError ) {
			$currentErrors[$currentErrorKey]['message'] = str_replace($jsonReplaces[0], $jsonReplaces[1], $currentError['message']);
		}
		echo json_encode(array(
											 'success'               => true,
											 'saved'                 => false,
											 'fonds'                 => $data['fonds'],
											 'message'               => $editObject->_error,
											 'errors'                => $currentErrors
										 )); //let ajax know the request ended in failure
	}
	exit();
}

/** if ajax disable header and footer **/
if( requestType('ajax') ) {
	$editObject->includeHeaderInOutput = false;
}


//listarray($editObject);
$autocomplete = new Autocomplete();
//$template = "factuurregelsTemplate.html";
//$editObject->formTemplate = $template;
//$editObject->usetemplate = true;

$editObject->template['script_voet'] = $autocomplete->getAutoCompleteScript('FondsExtraInformatie','fonds','fonds');

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if( isset($data['frame']) && $data['frame'] == 1)
{
	$object->setOption('fonds','form_type','text');
	//$object->setOption('fonds','form_extra','READONLY');
	if(isset($data['fonds']))
		$object->set('fonds',$data['fonds']);

//	if($__appvar['master']==false)
		$editObject->object->data['fields']['fonds']['form_visible']=false;

	if( ! requestType('ajax') )
	{
		if($object->checkAccess())// $_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
			$editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;' . vt('Opslaan') . '</a> <input type="hidden" name="frame" value="1">';
	//	elseif(checkAccess())
//			$editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan</a>
 //   <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>
//  <!--<a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>-->
 // <input type="hidden" name="frame" value="1">';


		$html=$editObject->getOutput();
		$html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);

		echo $html;
	}
	else
	{
		$html = $editObject->getOutput();
	}
	//listarray($_SERVER);
	$returnUrl='fondsextrainformatieEdit.php?action=edit&id='.$object->get('id').'&frame=1';
	//echo $returnUrl;
	//$returnUrl='blankFondsKoppeling.php';
}
else
  echo $editObject->getOutput();

if($_GET['frame']==1)
{
  $_SESSION['NAV']=$navBackup;
}

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
