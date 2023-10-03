<?php

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "fondskoersaanvragenList.php";
$__funcvar['location'] = "fondskoersaanvragenEdit.php";

$object = new FondskoersAanvragen();


if($__appvar["bedrijf"] != "HOME")
{
  $object->dbId=2;
}

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$subHeader=urldecode($_GET['message']);
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b><br>\n".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
//listarray($data);exit;
$action = $data['action'];

$AETemplate = new AE_template();

if($__appvar["bedrijf"] != "HOME")
{
  $editObject->extraNavSettings['save']['text']='versturen';
  $editObject->extraNavSettings['save']['tip']='versturen naar AIRS';
  $editObject->extraNavSettings['opslaanNietVerlaten']['hidden']=true;

}
else
{
   $db=new DB();
   $query="SELECT verwerkt FROM fondskoersAanvragen WHERE id='".$data['id']."'";
   $db->SQL($query);
   $db->query();
   $laatsteStatus=$db->nextRecord();
}


$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->formTemplate = "fondskoersaanvragenEditTemplate.html";
$editObject->usetemplate = false;
if($action=='delete')
{
  $data['verwerkt']=-1;
  $action='update';
}

$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Fonds');

$object->data['fields']['Fonds']['form_type'] = 'autocomplete';
$object->data['fields']['Fonds']['form_size'] = 25;
$editObject->formVars['Fonds_inputfield'] = $autocomplete->addVirtuelField('Fonds', array(
  'autocomplete' => array(
    'table'        => 'Fondsen',
    'label'        => array(
      'Fondsen.Fonds',
      'Fondsen.ISINCode',
      'Fondsen.Omschrijving',
      'Fondsen.FondsImportCode'
    ),
    'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving', 'Fondsen.FondsImportCode'),
    'field_value'  => array('Fondsen.Fonds'),
    'value'        => 'Fonds',
    'actions'      => array(
      'select' => '
        event.preventDefault();
        $("#Fonds").val(ui.item.value);
      ',
      'change' => '
        if ( ui.item == null ) {
          $("#Fonds").val("");
        }     
      '
    ),
    'conditions'   => array(
      'AND' => array(
        'Fondsen.KoersVBH = "'. $__appvar['bedrijf'] . '"',
        'Fondsen.koersmethodiek=5',
        '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00")'
      )
    ),
    'order' => 'Fonds'
  )
));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
//echo $editObject->getTemplate();exit;
echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($__appvar['bedrijf']<>'HOME')
  {
    echo "Fondskoersaanvraag voor ".$_GET['Fonds']." verzonden.<br>";
    exit;
    //header("Location: fondskoersaanvragenEdit.php?action=new&message=".urldecode("Fondskoersaanvraag voor ".$_GET['Fonds']." verzonden.<br>"));
  }
  else
  {
    if($__appvar['bedrijf']=='HOME' && $object->get('id') > 0 && $action=='update')
    { 
      if($object->get('verwerkt') <> $laatsteStatus['verwerkt'])
      {
        $object->sendFondskoersaanvraagEmail();
      }
    }   
  	header("Location: ".$returnUrl);
  }
}
else 
{
	echo $_error = $editObject->_error;
}
?>