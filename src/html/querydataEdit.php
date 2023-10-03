<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.4 $

 		$Log: querydataEdit.php,v $
 		Revision 1.4  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.3  2006/07/07 15:03:34  cvs
 		einde dag 7-7-2006
 		

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "querydataList.php";
$__funcvar[location] = "querydataEdit.php";

$object = new Querydata();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

//$editObject->usetemplate = true;
$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>