<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/11/25 09:17:09 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20091120_PREinstall.php,v $
 		Revision 1.1  2009/11/25 09:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/11/08 14:19:08  rvv
 		*** empty log message ***
 		

 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","ddInleesLocatie",array("Type"=>"varchar(200)","Null"=>false)); 




?>