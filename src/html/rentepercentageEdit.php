<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "rentepercentageList.php";
$__funcvar['location'] = "rentepercentageEdit.php";

$object = new Rentepercentage();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

if(isset($_GET['Fonds']))
  $_GET['Fonds']=urldecode($_GET['Fonds']);
$data = $_GET;
$action = $data['action'];


// Vermogensbeheerder ophalen
$DB = new DB();
$DB->SQL("SELECT Fonds FROM Fondsen ORDER BY Fonds");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Fonds"]["form_options"][] = $gb['Fonds'];
}

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

if( requestType('ajax') ) {
  $editObject->includeHeaderInOutput = false;
  $object->formName = 'rentepercentageEdit';
  $object->formId = 'rentepercentageEditForm';
  
  
  /** set airs datepicker on ajax form fields **/
  $object->addClass('Datum', 'AIRSdatepicker');
  $object->data['fields']['Datum']['form_extra'] = ' onchange="date_complete(this);"';

  $object->addClass('GeldigVanaf', 'AIRSdatepicker');
  if ( isset ($object->data['fields']['GeldigVanaf']['form_extra']) )
  {
    $object->data['fields']['GeldigVanaf']['form_extra'] = $object->data['fields']['GeldigVanaf']['form_extra'] . ' onchange=\"date_complete(this);\"';
  } 
  else 
  {
    $object->data['fields']['GeldigVanaf']['form_extra'] = ' onchange="date_complete(this);"';
  }
}


if( isset ($data['Fonds']) )
  $object->set('Fonds',$data['Fonds']);

  if($data['frame']==1)
  {
    $object->setOption('Fonds','form_type','text');
    $object->setOption('Fonds','form_extra','READONLY');

    if( ! requestType('ajax') ) {

      $editObject->formVars["submit"]='<a href="#" onClick="submitForm();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;opslaan</a>
    <a href="#" onClick="editForm.action.value=\'delete\';submitForm();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>
    <a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>
    <input type="hidden" name="frame" value="1">';
    }
    $frame="&frame=1";
  } 
  
  
  $html=$editObject->getOutput();
  if( requestType('ajax') ) {
    $html .= '
      <script>
        function blockDate () {
          try
          {
            boekdatum = $("#rentepercentageEditForm #Datum").val();
            var datumParts=boekdatum.split(\'-\')
            var boekdate=new Date(datumParts[2],(datumParts[1]-1),datumParts[0]);
            blockdate = new Date('.date("Y").',('.date("m").'-1),1);
            if (blockdate > boekdate)
            {
              ret=confirm("De rentedatum ligt niet in de huidige maand, wilt u doorgaan?");
              if(ret<1){return ret;}
            } else {
              ret = true;
              return ret;
            }
          }
          catch(err) { } 
        }
      </script>
      
    ';
  } else {
    $vastzetParts=explode("-",date("Y-m-01"));
    $editObject->formVars['blockdate']='
      try
      {
        var boekdatum=document.editForm.Datum.value;
        var datumParts=boekdatum.split(\'-\')
        var boekdate=new Date(datumParts[2],(datumParts[1]-1),datumParts[0]);
        var blockdate=new Date('.date("Y").',('.date("m").'-1),1);
        if (blockdate > boekdate)
        {
          ret=confirm("De rentedatum ligt niet in de huidige maand, wilt u doorgaan?");
          if(ret<1){return ret;}
        }
      }
      catch(err) { }  
      ';  
  }

$html=str_replace('//check values ?',$editObject->formVars['blockdate'],$html);


if( ! requestType('ajax') ) {
  $html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
  echo $html;
}

if ($result = $editObject->result)
{
	header("Location: ".$__funcvar['listurl']."?Fonds=".$object->get('Fonds').$frame);
}
else {
	echo $_error = $editObject->_error;
}



/** ajax functionaliteit voor jquery modal **/

if( requestType('ajax') ) {
  $AETemplate = new AE_template();
  echo template('templates/ajax_head.inc', array());
  $actions = '';
  
  $actions .= '<a href="#" id="rentepercentageSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan</a>';
  if ( $action !== 'new' ) {
    $actions .= '<a href="#" id="rentepercentageRemoveData"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>';
  }
  $actions .= '
    <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>
    <input type="hidden" name="frame" value="1">
  ';
  
  echo $AETemplate->parseFile('jqueryDialog/rentepercentageEditDialogData.html', array(
    'html'          => $html,
    'actions'       => $actions,
  ));

  echo template('templates/ajax_voet.inc', $editObject->template);
}
