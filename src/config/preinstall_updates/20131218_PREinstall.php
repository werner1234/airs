<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/01/04 17:10:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20131218_PREinstall.php,v $
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
$tst->changeField("Fondsen","identifierVWD",array("Type"=>"varchar(20)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Fondsen","identifierFactSet",array("Type"=>"varchar(20)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Fondsen","koersmethodiek",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));

$tst->changeField("CRM_naw","startdatum",array("Type"=>"varchar(4)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw","doeldatum",array("Type"=>"varchar(4)","Null"=>false,'Default'=>'default \'\''));

//$tst->changeField("CRM_naw","startdatum",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
//$tst->changeField("CRM_naw","doeldatum",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
?>