<?php
/* 	
    AE-ICT CODEX source module versie 1.2, 25 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/07/22 09:11:22 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: facmod_factuurbeheerEdit.php,v $
    Revision 1.1  2019/07/22 09:11:22  cvs
    call 7675

   
 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
if (!facmodAccess())
{
  return false;
}

$subHeader = "";
$mainHeader    = "Factuurstatus aanpassen";

$__funcvar["listurl"]  = "facmod_factuurbeheerList.php";
$__funcvar["location"] = "facmod_factuurbeheerEdit.php";

$object = new facmod_factuurbeheer();

$editObject = new editObject(&$object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent["body"]        = " onload='initScript();'";


$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$object->getById($data["id"]);
$editObject->formVars['inclHoog']        = fBedrag($object->get("bedrag_ex_h") + $object->get("btw_h"));
$editObject->formVars['inclLaag']        = fBedrag($object->get("bedrag_ex_l") + $object->get("btw_l"));
$editObject->formVars['incl0']           = fBedrag($object->get("bedrag_0"));
$editObject->formVars['inclVerlegdHoog'] = fBedrag($object->get("bedrag_vh"));
$editObject->formVars['inclVerlegdLaag'] = fBedrag($object->get("bedrag_vl"));
$editObject->formVars['inclTotaal']      = fBedrag($object->get("bedrag_incl"));
$editObject->formVars['factuurdatum']    = kdbdatum($object->get("datum"));
$editObject->formVars['datum']           = str_replace(".","-",dbdatum($object->get("datum")));





$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "facmod_factuurbeheerEditTemplate.html";

$editObject->controller($action,$data);
$object->setOption("status","form_options",$__facmod["debiteurStatus"]);
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
$object->setOption("status","form_extra","onBlur='StatusAction()' OnChange='StatusAction();'");
$object->setOption("deelbetaling_1","form_extra","style='text-align:right;' onBlur='this.value=formatNumericField(this);sumdeels()' ");
$object->setOption("deelbetaling_2","form_extra","style='text-align:right;' onBlur='this.value=formatNumericField(this);sumdeels()' ");
$object->setOption("deelbetaling_3","form_extra","style='text-align:right;' onBlur='this.value=formatNumericField(this);sumdeels()' ");
$object->setOption("bedrag_voldaan","form_extra","style='text-align:right;' onBlur='this.value=formatNumericField(this);sumdeels()' ");
//$object->setOption("deelbetaling_1_datum","form_extra"," ");
//$object->setOption("deelbetaling_2_datum","form_extra","class='AIRSdatepicker ' ");
//$object->setOption("deelbetaling_3_datum","form_extra","class='AIRSdatepicker '  ");
$object->setOption("betaal_datum","form_extra","class='AIRSdatepicker '  onChange='date_complete(this);betaaldagen();' ");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
  echo $_error = $editObject->_error;
//	echo "<br>FOUT melding: ".$_error = $editObject->_error;
//	echo var_export($editObject->data,true);
}

?>