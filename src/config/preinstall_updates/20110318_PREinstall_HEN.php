<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/05/04 16:25:11 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20110318_PREinstall_HEN.php,v $
 		Revision 1.1  2011/05/04 16:25:11  rvv
 		*** empty log message ***
 		
		
 	
*/
include("wwwvars.php");
$_GET["forceCreateDB"] = "1";
include_once("../html/HEN_dbDump.php");

?>