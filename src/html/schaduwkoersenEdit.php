<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 februari 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: schaduwkoersenEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2009/02/15 11:53:21  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar[listurl]  = "schaduwkoersenList.php";
$__funcvar[location] = "schaduwkoersenEdit.php";

$object = new Schaduwkoersen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$query = "SELECT Fonds FROM Fondsen ORDER BY Fonds";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$object->data['fields']["Fonds"]["form_options"][] ="";
while($clientdata = $DB->NextRecord())
{

	$object->data['fields']["Fonds"]["form_options"][] = $clientdata[Fonds];
}

if($action == "new")
{
	$object->data['fields']['Fonds']['value'] = $Fonds;
}


$editObject->controller($action,$data);

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