<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2010/07/13 09:02:08 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100706_PREinstall.php,v $
 		Revision 1.1  2010/07/13 09:02:08  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2010/06/09 08:48:25  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2010/05/16 11:05:48  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Fondsen","aabbeCode",array("Type"=>"varchar(30)","Null"=>false)); 


?>