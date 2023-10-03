<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/01/04 13:14:14 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20150103_PREinstall.php,v $
 		Revision 1.1  2015/01/04 13:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/13 12:34:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Fondsen","KoersVBH",array("Type"=>"varchar(10)","Null"=>false));



?>