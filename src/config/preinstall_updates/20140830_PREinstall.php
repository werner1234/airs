<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/30 16:27:26 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140830_PREinstall.php,v $
 		Revision 1.1  2014/08/30 16:27:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("CRM_naw_templates","intake",array("Type"=>"tinyint(3)","Null"=>false));
$tst->changeField("veldopmaak","formExtra",array("Type"=>"text","Null"=>false));



?>