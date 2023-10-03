<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/10/19 08:50:05 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20141018_PREinstall.php,v $
 		Revision 1.1  2014/10/19 08:50:05  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/10/18 11:22:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/30 16:27:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();


$tst->changeField("Vermogensbeheerders","frontofficeClientExcel",array("Type"=>"tinyint(4)","Null"=>false));

?>