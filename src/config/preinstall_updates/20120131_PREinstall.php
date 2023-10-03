<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2012/03/09 09:23:28 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20120131_PREinstall.php,v $
 		Revision 1.1  2012/03/09 09:23:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
		 
$tst = new SQLman();

$tst->changeField("Gebruikers","verzendrechten",array("Type"=>"tinyint","Null"=>false)); 






?>