<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/04/02 15:54:35 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140402_PREinstall.php,v $
 		Revision 1.1  2014/04/02 15:54:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/03/29 16:25:05  rvv
 		*** empty log message ***
 		
	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Fondsen","koersmemo",array("Type"=>"varchar(255)","Null"=>false));




?>