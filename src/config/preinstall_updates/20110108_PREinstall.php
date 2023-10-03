<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/01/26 17:18:16 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20110108_PREinstall.php,v $
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

if(file_exists("../html/rapport/include/RapportHSE_L29.php"))
  unlink("../html/rapport/include/RapportHSE_L29.php");




?>