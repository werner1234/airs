<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 november 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: fondskostenEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2017/12/08 14:12:01  rm
    6413 Fondskosten toevoegen autocomplete aan fonds veld

    Revision 1.1  2014/11/19 16:41:12  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader     = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "fondskostenList.php";
$__funcvar['location'] = "fondskostenEdit.php";

$object = new Fondskosten();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);


$autocomplete = new Autocomplete();
$editObject->template['script_voet'] = $autocomplete->getAutoCompleteScript ('Fondskosten', 'fonds', 'fonds');

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
