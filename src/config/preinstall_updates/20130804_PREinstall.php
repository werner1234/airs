<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/08/04 10:46:09 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20130804_PREinstall.php,v $
 		Revision 1.1  2013/08/04 10:46:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/31 15:54:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/22 06:35:57  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","autoPortaalVulling",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));


?>