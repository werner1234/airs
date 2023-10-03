<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 18 augustus 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/03/01 08:57:20 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: API_queueExternEdit.php,v $
    Revision 1.1  2019/03/01 08:57:20  cvs
    call 7364

    Revision 1.1  2017/08/18 14:42:58  cvs
    call 5815

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$subHeader = "";
$mainHeader    = "API extern bericht bekijken";

$fmt = new AE_cls_formatter();

$__funcvar['listurl']  = "API_queueExternList.php";
$__funcvar['location'] = "API_queueExternEdit.php";

$object = new API_queueExtern();

$outP = new editObject($object);
$outP->__funcvar = $__funcvar;
$outP->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$outP->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$outP->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$outP->usetemplate = true;
$outP->formTemplate = "API_queueExternEditTemplate.html";

$outP->controller($action, $data);

$outP->formVars["addStamp"] = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $object->get("add_date"));

$df = (array)json_decode($object->get("dataFields"));
$out = "
<table class='dataFieldsTable'>
<tr>
  <td class='tHead'>veldnaam</td>
  <td class='tHead'>waarde</td>
</tr>
";
foreach($df as $k=>$v)
{
  $out .= "<tr><td class='keyColumn'>$k</td><td class='valueColumn'>$v</td></tr>";
}

$out .= "</table>";
$outP->formVars["dataFields"] = $out;

echo $outP->getOutput();

if ($result = $outP->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $outP->_error;
}
?>