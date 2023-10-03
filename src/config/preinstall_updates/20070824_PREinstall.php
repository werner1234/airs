<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2007/08/24 11:26:49 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20070824_PREinstall.php,v $
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");




$tst = new SQLman();

$tst->changeField("Orders","fondsCode",array("Type"=>"varchar(26)","Null"=>false)); 

$tst->changeField("Fondsen","Garantiepercentage",array("Type"=>"double","Null"=>false)); 
$tst->changeField("Fondsen","binckCode",array("Type"=>"varchar(26)","Null"=>false));


?>