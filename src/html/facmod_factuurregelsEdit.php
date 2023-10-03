<?php
/*
    AE-ICT CODEX source module versie 1.2, 26 november 2005
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/07/22 09:11:22 $
    File Versie         : $Revision: 1.1 $

    $Log: facmod_factuurregelsEdit.php,v $
    Revision 1.1  2019/07/22 09:11:22  cvs
    call 7675


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
//aetodo: auth toevoegen
//include_once("facmod_auth.php");  // check level van gebruiker geeft $myLevel terug

$subHeader = "";
$mainHeader    = "factuurregel muteren";

$data = $_GET;
$action = $data["action"];

if ($action == "new")
  $mainHeader    = "factuurregel toevoegen";

if ($data["copyrec"])
  $mainHeader    = "factuurregel kopieren";

$__funcvar["listurl"]  = "facmod_factuurregelsList.php";
$__funcvar["location"] = "facmod_factuurregelsEdit.php";

$koppelObject = array();
$koppelObject[0] = new Koppel("facmod_artikel","editForm");
$koppelObject[0]->addFields("artnr","artnr",true,true);
$koppelObject[0]->addFields("omschrijving","txt",true,true);
$koppelObject[0]->addFields("stuksprijs","stuksprijs",true,true);
$koppelObject[0]->addFields("eenheid","eenheid",false,false);
$koppelObject[0]->addFields("btw","btw",false,false);
$koppelObject[0]->name = "artikel";
$koppelObject[0]->extraQuery = "  ";


$object = new facmod_factuurregels();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent["jsincludes"] .= "<script src=\"javascript/facmod_factuurregelsEdit.js\" type=text/javascript></script>";
$editcontent["jsincludes"] .= "\n<script src=\"javascript/popup.js\" type=text/javascript></script>";
$editcontent["javascript"] = str_replace("//preSubmit//","doMath();",$editcontent["javascript"]);
$editcontent["javascript"] .= "\n".$koppelObject[0]->getJavascript();



$editObject->template = $editcontent;



$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "facmod_factuurregelsEditTemplate.html";
$cfg = new AE_config();

$editObject->formVars['btwH'] = $__facmod["btwH"];
$editObject->formVars['btwL'] = $__facmod["btwL"];


$editObject->controller($action,$data);

$DB = new DB();

if ($action == "new")
{
  $DB->SQL("SELECT * FROM CRM_naw WHERE id =".$_GET[rel_id] );
  $nawRec = $DB->lookupRecord();
  $object->set("rel_id",$_GET["rel_id"]);
  $object->set("btw", $cfg->getData("btwDefault"));

}
else
{
  $DB->SQL("SELECT * FROM CRM_naw WHERE id =".$object->get("rel_id") );
  $nawRec = $DB->lookupRecord();
  $subHeader = ", bij ".$nawRec["naam"];
}

if ($data["copyrec"] == true)
{
  $object->set("id","");
  $object->set("wachtstand",1);
  $object->set("aantal","");
}

//$object->setOption("inkooptotaal_excl","form_extra","style=\"text-align:right;\" onChange=\"calcTotaal();\"");


$object->setOption("btw","form_options",$__facmod["btw"]);


$optionArray = $__facmod["eenheden"];

$object->setOption("eenheid","form_options",$optionArray);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

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