<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: doorkijk_looptijdEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2017/12/04 14:48:17  cvs
    call 6349

    Revision 1.1  2017/12/04 10:40:51  cvs
    Update van Ben ingelezen dd 4-12-2017

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "doorkijk_looptijdList.php";
$__funcvar['location'] = "doorkijk_looptijdEdit.php";

$object = new Doorkijk_Looptijd();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];
  /*
$db=new DB();
if($data['id'] > 0)
{
	$query="SELECT id,Code FROM Doorkijk_Looptijd WHERE id='".$data['id']."'";
	$db->SQL($query);
	$dbRecord=$db->lookupRecord();
}
     */
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
//$editObject->formTemplate = "doorkijk_looptijdEditTemplate.html";
$editObject->formTemplate = "";

$editObject->controller($action,$data);
echo $editObject->getOutput();

if ($result = $editObject->result)
{   // en hier wordt het vaag...
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>