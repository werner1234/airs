<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 17 juni 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2016/06/20 08:20:20 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: invulinstructiesEdit.php,v $
    Revision 1.1  2016/06/20 08:20:20  cvs
    call 5027 invulinstructies

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Invul instructies muteren");

$__funcvar['listurl']  = "invulinstructiesList.php";
$__funcvar['location'] = "invulinstructiesEdit.php";

$object = new InvulInstructies();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$data = $_GET;
$action = $data['action'];
//debug($data);
if ($action == "new" AND $data["kopie"] > 0)
{
	$object->getById($data["kopie"]);
	$object->data["fields"]["id"]["value"] = 0;
	$object->data["fields"]["memo"]["value"] = " KOPIE d.d. ".date("d-m-Y H:i");
	//$object->save();
	$action = "edit";
}


$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;
$query = '
(SELECT
   "*" as Vermogensbeheerder,
   "*" as Naam
)
UNION
(
	SELECT
    Vermogensbeheerder,
    CONCAT("(",Vermogensbeheerder,") ",Naam) AS Naam
  FROM Vermogensbeheerders
  ORDER BY Vermogensbeheerders.Vermogensbeheerder
)
	';




$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "invulinstructiesEditTemplate.html";


$editObject->controller($action,$data);


echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$_SESSION["invulListUrl"] );
}
else 
{
	echo $_error = $editObject->_error;
}
?>