<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/11/13 15:53:45 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131112_PREinstall.php,v $
 		Revision 1.1  2013/11/13 15:53:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

if(file_exists("../html/rapport/include/RapportTemplate_L5.php "))
  unlink("../html/rapport/include/RapportTemplate_L5.php ");

?>