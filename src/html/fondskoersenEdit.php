<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "fondskoersenList.php";
$__funcvar['location'] = "fondskoersenEdit.php";

$object = new Fondskoersen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

if($action == "new")
{

  if($_GET['base64']==1)
      $Fonds=base64_decode($Fonds);

  if($Fonds=='')
  {
    if($_GET['base64']==1)
      $Fonds=base64_decode($_GET['Fonds']);
    else
      $Fonds=$_GET['Fonds'];  
  } 
	$object->data['fields']['Fonds']['value'] = $Fonds;
       
}

$editObject->formTemplate = "fondskoersenEditTemplate.html";
$editObject->usetemplate = true;



$editObject->controller($action,$data);


/** als request type = ajax return json voor jquery bij update of verwijderen **/
if( requestType('ajax') && ($action == 'update' || $action == 'delete') ) {
  $AEJson = new AE_Json();
  if ($editObject->object->error == false) {
    echo $AEJson->json_encode(array(
      'success' => true, 
      'saved'   => true,
    )); //let ajax know the request ended in success
    exit();
  } else {
    echo $AEJson->json_encode(array(
      'success'               => true,
      'saved'                 => false,
      'message'               => $editObject->_error,
      'errors'                => $object->getErrors()
    )); //let ajax know the request ended in failure
  }
  exit();
}

$editObject->formVars['formName'] = 'editForm';
if( requestType('ajax') ) {
  $editObject->includeHeaderInOutput = false;
  $editObject->formVars['formName'] = 'fondsKoersen';
}

$fonds=$object->get('Fonds');
$db=new DB();
$query="SELECT id FROM benchmarkverdeling WHERE fonds='".$fonds."'";
$db->SQL($query);
$db->Query();
if($db->records() > 0)
{
  $editObject->formVars["benchmark"]='
  <div class="formblock">
<div class="formlinks"></div>
<div class="formrechts">
Fonds wordt gebruikt in benchmarkverdeling.
</div>
</div>
';
}
if($_GET['frame']==1)
{
  $Fonds=$object->get('Fonds');

  if(isset($_GET['base64']) && $_GET['base64']==1)
      $_GET['Fonds']=base64_decode($_GET['Fonds']);
  
  if(isset($_GET['Fonds']))
      $object->set('Fonds',$_GET['Fonds']);
  $object->setOption('Fonds','form_type','text');
  $object->setOption('Fonds','form_extra','READONLY');
  
  //<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
  if( ! requestType('ajax') ) {
    if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
        $editObject->formVars["submit"]='
    <a href="javascript:history.go(-1);" onClick="editForm.action.value=\'delete\';editForm.submit();"><a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;' . vt('terug') . '</a><input type="hidden" name="frame" value="1">';
    elseif(checkAccess())
        $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('Opslaan') . '</a>
    <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;' . vt('verwijder') . '</a>
  <a href="javascript:history.go(-1);" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;' . vt('terug') . '</a>
  <input type="hidden" name="frame" value="1">';
    $frame="&frame=1";

    $html=$editObject->getOutput();
    $html=str_replace("</div>
  </form>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
    echo $html;
  } else {
    $html = $editObject->getOutput();
  }
  $returnUrl="blankFondsKoppeling.php";
  
  $db=new DB();
  $query="SELECT id FROM Fondsen WHERE Fonds='".$object->get('Fonds')."'";
  $db->SQL($query);
  $Fonds=$db->lookupRecord();
  $fondsId=$Fonds['id'];
  $returnUrl="fondsFondskoersen.php?id=".$fondsId;
  
  
}
else
  echo $editObject->getOutput();

if ($result = $editObject->result)
{ 
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}


/** ajax functionaliteit voor jquery modal **/
if( requestType('ajax') ) {
  $AETemplate = new AE_template();
  echo template('templates/ajax_head.inc');
  
  $actions = '';
  if($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0)
  {
    //<a href="#" id="fondskoersenSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    $actions .= '  
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;' . vt('terug') . '</a>
      <input type="hidden" name="frame" value="1">
    ';
  }
  elseif(checkAccess())
  {
    $actions .= '<a href="#" id="fondskoersenSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('Opslaan') . '</a>';
    if ( $action !== 'new' )
    {
      $actions .= '<a href="#" id="fondskoersenRemoveData"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;' . vt('verwijder') . '</a>';
    }
    $actions .= '
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;' . vt('terug') . '</a>
      <input type="hidden" name="frame" value="1">
    ';
  }
  
  
  echo $AETemplate->parseFile('jqueryDialog/fondskoersenDialogData.html', array(
    'html'          => $html,
    'actions'       => $actions,
  ));

  echo template('templates/ajax_voet.inc', array());
}
