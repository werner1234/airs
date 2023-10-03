<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/01/04 17:10:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131214_PREinstall.php,v $
 		Revision 1.1  2014/01/04 17:10:04  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/11/27 16:26:19  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/11/17 11:19:40  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/11/13 15:54:00  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/01 13:29:55  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/21 15:32:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw","begeleidendeBrief",array("Type"=>"varchar(100)","Null"=>false,'Default'=>'default \'\''));
?>