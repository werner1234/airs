<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/11/12 16:35:50 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20141112_PREinstall.php,v $
 		Revision 1.1  2014/11/12 16:35:50  rvv
 		*** empty log message ***
 		
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Gebruikers","updateInfoAan",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("updateInformatie","publiceer",array("Type"=>"tinyint(4)","Null"=>false));


?>