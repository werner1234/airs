<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/07/25 14:34:30 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100725_PREinstall.php,v $
 		Revision 1.1  2010/07/25 14:34:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/28 15:50:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("CRM_naw_dossier","memo",array("Type"=>"tinytext","Null"=>false)); 




?>