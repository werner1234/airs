<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/09/05 08:36:32 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070805_REinstall.php,v $
 		Revision 1.1  2007/09/05 08:36:32  rvv
 		*** empty log message ***
 		

 		
 
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Rekeningen","Deposito",array("Type"=>"tinyint(4)","Null"=>false)); 



?>